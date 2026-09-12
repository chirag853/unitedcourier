<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Admin;
use App\Models\BillTo;
use App\Models\ConsigneeInfo;
use App\Models\CourierRate;
use App\Models\CourierService;
use App\Models\CreateShipment;
use App\Models\CsbInformation;
use App\Models\Customer;
use App\Models\Destination;
use App\Models\Manifest;
use App\Models\PackageDimension;
use App\Models\ShipmentInvoice;
use App\Models\ShipmentInvoiceItem;
use App\Models\ShipmentLog;
use App\Models\ShipmentRemark;
use App\Models\ShipmentTracking;
use App\Models\ShipperInfo;
use App\Models\SurCharge;
use App\Models\Tracking;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Zone;
use App\Services\AdomantraApiClient;
use App\Services\PrimusShipmentService;
use Barryvdh\DomPDF\Facade\Pdf;

class PrepaidController extends Controller
{
    /**
     * Show the "Create Prepaid Order" form.
     *
     * Mirrors the customer create-shipment page exactly (same fields and
     * validation), but with no logged-in customer: default rates only,
     * no saved exporters and no CSB pre-fill.
     */
    public function prepaidCreateOrder()
    {
        $customer = null;
        $csbForm = null;
        // All enabled services are offered (same as customer create-shipment).
        $courierServices = CourierService::where('status', 1)->get();
        // The same zone (name + code) is stored once per service, so the raw
        // query returns duplicates. Deduplicate here so the initial consignee
        // state dropdown does not show the same state/zipcode multiple times.
        $zones = Zone::orderBy('zone_name')
            ->get()
            ->unique(function ($zone) {
                return strtolower(trim((string) $zone->zone_code)) . '|' . strtolower(trim((string) $zone->zone_name));
            })
            ->values();
        $destinations = Destination::where('is_active', true)->orderBy('name')->get();
        $canCreateShipment = true;
        // Customers with an approved KYC and an active status can be selected as
        // the Prepaid order shipper. Only customers recharged in cash
        // (wallet_transactions.recharge_type = 'cash') are listed. Each entry
        // shows whether the customer is CSB 4 (csb_status = 1) or CSB 5 (csb_status = 2).
        $prepaidCustomers = Customer::query()
            ->where('status', 1)
            ->whereHas('kycDetail', function ($query) {
                $query->where('kyc_status', 'approved');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('wallet_transactions')
                    ->whereColumn('wallet_transactions.customer_id', 'customers.id')
                    ->whereRaw('LOWER(wallet_transactions.recharge_type) = ?', ['cash']);
            })
            ->with(['kycDetail', 'csbForm'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.prepaid-create-order', compact(
            'customer',
            'csbForm',
            'courierServices',
            'zones',
            'destinations',
            'canCreateShipment',
            'prepaidCustomers'
        ));
    }

    /**
     * Store a new Prepaid order (shipment_type = 5).
     *
     * Mirrors the customer create-shipment flow (storeShipment) exactly:
     * same validation, same per-box re-pricing, same tracking/logging and
     * the same Adomantra submission. Differences for the admin flow:
     *   - no authenticated customer (default rates only, customer_id = 0)
     *   - shipper customer_id is null / shipment_type is forced to 2
     *   - Prepaid AWB numbers are generated (generatePrepaidAwbNumber)
     *   - the shipment invoice keeps status "draft" + delivery_type "DDU"
     *     so prepaidAllOrders() lists it correctly
     *   - tracking/shipment-log entries are performed by "admin"
     *   - the JSON success payload includes a top-level "tracking_number"
     *     (the generated AWB) which the admin page displays after submit
     */
    public function prepaidStoreOrder(Request $request, AdomantraApiClient $adomantra)
    {
        $transactionStarted = false;
        $carrierErrorRaw = null;

        try {
            $admin = auth()->guard('admin')->user();

            // Keep shipper state as an uppercase two-letter code regardless of client-side behavior.
            if ($request->filled('shipper_state')) {
                $request->merge([
                    'shipper_state' => strtoupper(
                        preg_replace('/[^A-Za-z]/', '', trim((string) $request->shipper_state))
                    ),
                ]);
            }

            // Validate the request data (identical rules to the customer create-shipment page)
            $validatedData = $request->validate([
                // Shipper Info
                'delivery_destination' => 'required',
                'origin_type' => 'required|string|max:50',
                'selected_exporter_customer_id' => 'nullable|integer',
                'shipping_method' => 'nullable|string|max:100',
                'service_rate_id' => 'nullable|integer',
                'shipper_same_as_customer' => 'boolean',
                'shipper_company_names' => 'required|string|max:150',
                'shipper_contact_person' => 'required|string|max:100',
                'shipper_address_line1' => 'required|string|max:255',
                'shipper_address_line2' => 'nullable|string|max:255',
                'shipper_address_line3' => 'nullable|string|max:255',
                'shipper_pincode' => 'required|string|max:20',
                'shipper_city' => 'required|string|max:100',
                'shipper_state' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
                'shipper_phone_number' => 'required|string|max:30',
                'shipper_emails' => 'required|email|max:150',
                'shipper_email_opt_out' => 'boolean',
                'shipper_kyc_type' => 'nullable|string|max:50',
                'shipper_kyc_number' => 'nullable|string|max:100',

                // Bill To Info
                'biller_name' => 'nullable|string|max:255',

                // Consignee Info
                'consignee_name' => 'required|string|max:150',
                'consignee_contact_person' => 'required|string|max:100',
                'consignee_address_line1' => 'required|string|max:255',
                'consignee_address_line2' => 'nullable|string|max:255',
                'consignee_address_line3' => 'nullable|string|max:255',
                'consignee_zip_code' => 'required|string|max:20',
                'consignee_city' => 'required|string|max:100',
                'consignee_state' => 'nullable|string|max:100',
                'consignee_phone_number' => 'required|string|max:30',
                'consignee_email' => 'required|email|max:150',
                'consignee_email_opt_out' => 'boolean',

                // Package Dimension
                'number_of_boxes' => 'nullable|integer|min:1|max:50',
                'package_shipping_method' => 'nullable|string|max:100',
                'packages' => 'nullable|array',
                'packages.*.actual_weight_kg' => 'nullable|numeric|min:0',
                'packages.*.length_cm' => 'nullable|numeric|min:0',
                'packages.*.width_cm' => 'nullable|numeric|min:0',
                'packages.*.height_cm' => 'nullable|numeric|min:0',
                'packages.*.volumetric_weight' => 'nullable|numeric|min:0',
                'packages.*.chargeable_weight' => 'nullable|numeric|min:0',
                'oversize_charge' => 'nullable|numeric|min:0',
                'handling_charge' => 'nullable|numeric|min:0',

                // CSB Information
                'ecommerce' => 'required_if:origin_type,CSB V|nullable|in:Yes,No',
                'scheme' => 'required_if:origin_type,CSB V|nullable|in:Yes,No',
                'csb_tax_type' => 'nullable|in:gst,lut',
                'bond_ut_igst' => 'nullable|in:Bond UT,IGST',
                'lut_number' => 'nullable|string|max:100',
                'iec_code' => 'nullable|string|max:50',
                'gst_number' => 'nullable|string|max:50',
                'ad_code' => 'nullable|string|max:100',
                'bank_account_number' => 'nullable|string|max:50',
                'bank_ifsc_code' => 'nullable|string|max:20',

                // Invoice Information
                'invoice_number' => 'required|string|max:100',
                'invoice_date' => 'required|date|after_or_equal:today',
                'invoice_amount' => 'required|numeric|min:0',
                'incoterms' => 'required|string|max:50',
                'invoice_currency' => 'required|string|max:20',
                'reference_number' => 'nullable|string|max:100',

                // Remark
                'entry_remark' => 'required|string|max:1000',
                'finance_remark' => 'nullable|string|max:1000',

                // invoice items
                'items.*.box_no' => 'nullable|integer',
                'items.*.description' => 'nullable|string|max:500',
                'items.*.hs_code' => 'nullable|string|max:50',
                'items.*.hts_code' => 'nullable|string|max:50',
                'items.*.unit_type' => 'nullable|string|max:50',
                'items.*.qty' => 'nullable|numeric|min:0',
                'items.*.unit_rate' => 'nullable|numeric|min:0',
                'items.*.igst_percentage' => 'nullable|numeric|min:0|max:100',
                'items.*.igst_amount' => 'nullable|numeric|min:0',
                'items.*.amount' => 'nullable|numeric|min:0',
            ]);

            // ------------------------------------------------------------------
            // Every box declared in the package dimensions must have at least
            // one invoice item row mapped to it (Box No. 1..N). Otherwise the
            // shipment invoice would be incomplete, so creation must be blocked.
            // ------------------------------------------------------------------
            $numberOfBoxes = (int) ($validatedData['number_of_boxes'] ?? 0);
            if ($numberOfBoxes < 1) {
                $numberOfBoxes = count(array_filter(
                    $validatedData['packages'] ?? [],
                    function ($package) {
                        if (! is_array($package)) {
                            return false;
                        }

                        return collect($package)->filter(function ($value) {
                            return $value !== null && $value !== '';
                        })->isNotEmpty();
                    }
                ));
            }
            $numberOfBoxes = max(1, $numberOfBoxes);

            $itemBoxNos = collect($validatedData['items'] ?? [])
                ->pluck('box_no')
                ->filter(function ($boxNo) {
                    return $boxNo !== null && $boxNo !== '';
                })
                ->map(function ($boxNo) {
                    return (int) $boxNo;
                })
                ->unique()
                ->values()
                ->all();

            $missingBoxNos = [];
            for ($boxNo = 1; $boxNo <= $numberOfBoxes; $boxNo++) {
                if (! in_array($boxNo, $itemBoxNos, true)) {
                    $missingBoxNos[] = $boxNo;
                }
            }

            if (! empty($missingBoxNos)) {
                $boxesMessage = 'Invoice item details are missing for Box No. '.
                    implode(', ', $missingBoxNos).
                    ' in the Shipment Invoice Items table. Please add an item row for each of these boxes before creating the order.';
                if (! $request->expectsJson()) {
                    return back()
                        ->withErrors(['items' => $boxesMessage])
                        ->withInput()
                        ->with('error', $boxesMessage);
                }

                return response()->json([
                    'success' => false,
                    'message' => $boxesMessage,
                    'errors' => ['items' => [$boxesMessage]],
                ], 422);
            }

            // ------------------------------------------------------------------
            // KYC Number format validation based on the selected KYC Type.
            // Patterns:
            //   GST (Normal)       -> ^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$
            //   Aadhar Card        -> ^[2-9]{1}[0-9]{11}$  (12 digits, first digit 2-9)
            //   PAN Card           -> ^[A-Z]{5}[0-9]{4}[A-Z]{1}$
            //   Passport Number    -> ^[A-Z][0-9]{7}$
            // ------------------------------------------------------------------
            $kycType = $validatedData['shipper_kyc_type'] ?? null;
            $kycNumber = $validatedData['shipper_kyc_number'] ?? null;

            if ($kycType && $kycNumber !== null && $kycNumber !== '') {
                $kycPatterns = [
                    'GST (Normal)' => '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                    'Aadhar Card' => '/^[2-9]{1}[0-9]{11}$/',
                    'PAN Card' => '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                    'Passport Number' => '/^[A-Z][0-9]{7}$/',
                ];

                $kycHints = [
                    'GST (Normal)' => 'GSTIN must be 15 characters in the format 2 digits, 5 letters, 4 digits, 1 letter, 1 alphanumeric, Z, 1 alphanumeric (e.g. 27ABCDE1234F1Z5).',
                    'Aadhar Card' => 'Aadhaar number must be 12 digits and the first digit must be between 2 and 9.',
                    'PAN Card' => 'PAN must be 10 characters: 5 letters, 4 digits, 1 letter (e.g. ABCDE1234F).',
                    'Passport Number' => 'Passport number must be 1 letter followed by 7 digits (e.g. A1234567).',
                ];

                if (isset($kycPatterns[$kycType]) && ! preg_match($kycPatterns[$kycType], $kycNumber)) {
                    $kycMessage = 'The KYC Number entered is not valid for the selected KYC Type ('.$kycType.'). '.($kycHints[$kycType] ?? '');
                    if (! $request->expectsJson()) {
                        return back()
                            ->withErrors(['shipper_kyc_number' => $kycMessage])
                            ->withInput()
                            ->with('error', $kycMessage);
                    }

                    return response()->json([
                        'success' => false,
                        'message' => $kycMessage,
                        'errors' => [
                            'shipper_kyc_number' => [$kycMessage],
                        ],
                    ], 422);
                }
            }

            // ------------------------------------------------------------------
            // Resolve delivery_destination: the dropdown now sends the numeric
            // destination_id. Convert it back to the destination NAME so all
            // downstream logic (storage, getCountryCodeFromDestination(),
            // resolveDestinationCountry(), rate calculation, APIs) keeps
            // working with the human-readable name as before.
            // ------------------------------------------------------------------
            $destInput = $validatedData['delivery_destination'] ?? null;
            $destName = is_numeric($destInput)
                ? optional(Destination::find((int) $destInput))->name
                : $destInput;

            if (! $destName) {
                if (! $request->expectsJson()) {
                    return back()
                        ->withErrors(['delivery_destination' => 'The selected destination is invalid.'])
                        ->withInput()
                        ->with('error', 'The selected destination is invalid.');
                }

                return response()->json([
                    'success' => false,
                    'message' => 'The selected destination is invalid.',
                    'errors' => ['delivery_destination' => ['The selected destination is invalid.']],
                ], 422);
            }
            $validatedData['delivery_destination'] = $destName;

            // Server-side validation: enforce max invoice total based on origin_type.
            // CSB IV -> max 25,000 | CSB V -> max 10,00,000 (1,000,000)
            $items = $request->input('items', []);
            $calculatedTotal = 0;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $calculatedTotal += (float) ($item['amount'] ?? 0);
                }
            }
            $maxAllowedTotal = ($validatedData['origin_type'] === 'CSB V') ? 1000000 : 25000;
            if ($calculatedTotal > $maxAllowedTotal) {
                $originLabel = ($validatedData['origin_type'] === 'CSB V') ? 'CSB V' : 'CSB IV';
                $message = 'The total invoice amount is ₹'.number_format($calculatedTotal, 2).
                    ', which exceeds the maximum allowed limit of ₹'.number_format($maxAllowedTotal, 2).
                    ' for '.$originLabel.'. Please reduce the invoice total and try again.';
                if (! $request->expectsJson()) {
                    return back()
                        ->withErrors(['invoice_amount' => $message])
                        ->withInput()
                        ->with('error', $message);
                }

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => [
                        'invoice_amount' => [$message],
                    ],
                ], 422);
            }

            // Server-side validation: enforce max total package weight (68 kg) for CSB IV only.
            // CSB V has no weight limit.
            if ($validatedData['origin_type'] !== 'CSB V') {
                $packages = $request->input('packages', []);
                $totalWeight = 0;
                if (is_array($packages)) {
                    foreach ($packages as $pkg) {
                        $totalWeight += (float) ($pkg['chargeable_weight'] ?? 0);
                    }
                }
                $maxWeight = 68;
                if ($totalWeight > $maxWeight) {
                    $weightMessage = 'The total package weight is '.number_format($totalWeight, 2).
                        ' kg, which exceeds the maximum allowed limit of '.$maxWeight.
                        ' kg for CSB IV. Please reduce the weight and try again.';
                    if (! $request->expectsJson()) {
                        return back()
                            ->withErrors(['packages' => $weightMessage])
                            ->withInput()
                            ->with('error', $weightMessage);
                    }

                    return response()->json([
                        'success' => false,
                        'message' => $weightMessage,
                        'errors' => [
                            'packages' => [$weightMessage],
                        ],
                    ], 422);
                }
            }

            // ============================================================
            // SERVER-SIDE PER-PACKAGE WEIGHT & DIMENSION VALIDATION
            // Max Actual Weight: 30 kg | Max Length: 120 cm | Max Width: 76 cm
            // Max Volumetric Weight: 68 kg (hard block above this)
            // Oversize: vol wt 40.001–68 kg → ₹21,000 charge (allowed)
            // ============================================================
            $pkgMaxActualWeight = 30;
            $pkgMaxLength = 120;
            $pkgMaxWidth = 76;
            $pkgMaxVolumetricWeight = 68;
            $pkgOversizeMin = 40.001;
            $oversizeChargeAmount = 21000;

            $packagesForValidation = $request->input('packages', []);
            $hasOversizePackage = false;
            if (is_array($packagesForValidation)) {
                foreach ($packagesForValidation as $idx => $pkg) {
                    $actualWt = (float) ($pkg['actual_weight_kg'] ?? 0);
                    $length = (float) ($pkg['length_cm'] ?? 0);
                    $width = (float) ($pkg['width_cm'] ?? 0);
                    $volWt = (float) ($pkg['volumetric_weight'] ?? 0);

                    $pkgErrors = [];
                    if ($actualWt > $pkgMaxActualWeight) {
                        $pkgErrors[] = 'Actual weight '.number_format($actualWt, 2).
                            ' kg exceeds max '.$pkgMaxActualWeight.' kg.';
                    }
                    if ($length > $pkgMaxLength) {
                        $pkgErrors[] = 'Length '.number_format($length, 2).
                            ' cm exceeds max '.$pkgMaxLength.' cm.';
                    }
                    if ($width > $pkgMaxWidth) {
                        $pkgErrors[] = 'Width '.number_format($width, 2).
                            ' cm exceeds max '.$pkgMaxWidth.' cm.';
                    }
                    if ($volWt > $pkgMaxVolumetricWeight) {
                        $pkgErrors[] = 'Volumetric weight '.number_format($volWt, 2).
                            ' kg exceeds the maximum allowed '.$pkgMaxVolumetricWeight.' kg.';
                    }

                    if (! empty($pkgErrors)) {
                        $boxNum = $idx + 1;
                        $pkgMessage = 'Box #'.$boxNum.': '.implode(' ', $pkgErrors).
                            ' Please correct the values and try again.';
                        if (! $request->expectsJson()) {
                            return back()
                                ->withErrors(['packages' => $pkgMessage])
                                ->withInput()
                                ->with('error', $pkgMessage);
                        }

                        return response()->json([
                            'success' => false,
                            'message' => $pkgMessage,
                            'errors' => [
                                'packages' => [$pkgMessage],
                            ],
                        ], 422);
                    }

                    // Track whether any package is in the oversize range (40.001–68 kg)
                    if ($volWt > $pkgOversizeMin && $volWt <= $pkgMaxVolumetricWeight) {
                        $hasOversizePackage = true;
                    }
                }
            }

            // Determine the final oversize charge:
            // Use the frontend-confirmed value if present, otherwise compute from packages.
            $oversizeCharge = (float) ($validatedData['oversize_charge'] ?? 0);
            if ($hasOversizePackage && $oversizeCharge <= 0) {
                $oversizeCharge = $oversizeChargeAmount;
            }

            // Resolve shipping_method from service_id when the shipping_method
            // <select> dropdown is empty but a DDP/DDU radio button was selected.
            // The JS sends 'service_id' alongside FormData.
            $serviceId = $request->input('service_id');
            // SELF is a static frontend-only option (no courier_services row,
            // no carrier API call, zero price). Detect it before any
            // service/rate lookup so the order is saved with
            // shipper_info.shipping_method = 'SELF'.
            $isSelfService = is_string($serviceId)
                ? strtoupper(trim($serviceId)) === 'SELF'
                : false;
            if (! $isSelfService && strtoupper(trim((string) ($validatedData['shipping_method'] ?? ''))) === 'SELF') {
                $isSelfService = true;
            }
            if ($isSelfService) {
                $validatedData['shipping_method'] = 'SELF';
                $validatedData['service_rate_id'] = null;
                $serviceId = null;
            }
            $courierService = null;
            if (! $isSelfService && empty($validatedData['shipping_method']) && $serviceId) {
                $courierService = CourierService::find($serviceId);
                if ($courierService) {
                    $validatedData['shipping_method'] = $courierService->method;
                    \Log::info('prepaidStoreOrder: Resolved shipping_method from service_id #'.$serviceId.' → "'.$courierService->method.'"');
                }
            }

            // Ensure we have a service_id (courier_services.id) to persist on the
            // shipper_info row. Prefer the value sent by the frontend; otherwise
            // resolve it from the (possibly just-resolved) shipping_method.
            // Skipped for SELF (no courier_services row).
            if (! $isSelfService && ! $serviceId && ! empty($validatedData['shipping_method'])) {
                $resolvedService = CourierService::whereRaw('LOWER(method) = ?', [strtolower($validatedData['shipping_method'])])->first();
                if ($resolvedService) {
                    $serviceId = $resolvedService->id;
                }
            }

            // Resolve the selected rate from server-owned records. Like the
            // customer create-shipment page, the exporter's own rates are
            // accepted first, then the shared default rates (customer_id = 0).
            // Skipped for SELF (zero price, no courier rate row).
            $exporterCustomerId = (int) ($validatedData['selected_exporter_customer_id'] ?? 0);
            $rateOwnerIds = $exporterCustomerId > 0 ? [$exporterCustomerId, 0] : [0];
            $courierRate = null;
            if (! $isSelfService && ! empty($validatedData['service_rate_id'])) {
                $courierRate = CourierRate::whereKey((int) $validatedData['service_rate_id'])
                    ->whereIn('customer_id', $rateOwnerIds)
                    ->with('service')
                    ->first();

                if (! $courierRate) {
                    throw ValidationException::withMessages([
                        'service_rate_id' => 'The selected courier rate is invalid or unavailable.',
                    ]);
                }

                if ($serviceId && (int) $courierRate->service_id !== (int) $serviceId) {
                    throw ValidationException::withMessages([
                        'service_rate_id' => 'The selected courier rate does not belong to the selected courier service.',
                    ]);
                }

                $serviceId = $courierRate->service_id;
                $courierService = $courierRate->service;
                if ($courierService && empty($validatedData['shipping_method'])) {
                    $validatedData['shipping_method'] = $courierService->method;
                }
            }

            // Any enabled courier service may be selected (same as the
            // customer create-shipment page). SELF is allowed (static
            // frontend-only option, no service row).

            // ------------------------------------------------------------
            // SHIPPER STATE — MAX 2 WORDS VALIDATION
            // The shipper state field must not contain more than 2 words
            // (e.g. "Gujarat", "New South Wales" are valid, but
            // "Some Very Long State Name" is not). Block order creation
            // here so the user is informed BEFORE the order is saved.
            // ------------------------------------------------------------
            $shipperStateForWordCount = trim((string) ($validatedData['shipper_state'] ?? ''));
            if ($shipperStateForWordCount !== '' && str_word_count($shipperStateForWordCount) > 2) {
                $wordCountMessage = 'Shipper state must not exceed 2 words. The provided state "'.$shipperStateForWordCount.'" contains '.str_word_count($shipperStateForWordCount).' words. Please enter a shorter state name and try again.';
                if (! $request->expectsJson()) {
                    return back()
                        ->withErrors(['shipper_state' => $wordCountMessage])
                        ->withInput()
                        ->with('error', $wordCountMessage);
                }

                return response()->json([
                    'success' => false,
                    'message' => $wordCountMessage,
                    'errors' => [
                        'shipper_state' => [$wordCountMessage],
                    ],
                ], 422);
            }

            // ============================================================
            // OVERSEAS LOGISTIC — SHIPPER STATE VALIDATION
            // The Overseas Logistic API (used for UNITED CANADA DDP /
            // E-COMMERCE and ARAMEX GPX / Australia) requires the shipper
            // state to be a 2-letter code (e.g. "GJ", "MH"). If the shipper
            // state field contains more than 2 characters, block order
            // creation here so the user is informed BEFORE the order is
            // saved (rather than failing later at manifest time).
            // ============================================================
            $resolvedShippingMethod = $validatedData['shipping_method'] ?? '';
            if ($this->isOverseasLogisticMethod($resolvedShippingMethod)) {
                $shipperStateInput = trim((string) ($validatedData['shipper_state'] ?? ''));
                if (strlen($shipperStateInput) > 2) {
                    $stateMessage = 'Shipper state must be a 2-letter code (e.g. "GJ", "MH") for Overseas shipments. The provided state "'.$shipperStateInput.'" is too long. Please enter a 2-letter state code and try again.';
                    if (! $request->expectsJson()) {
                        return back()
                            ->withErrors(['shipper_state' => $stateMessage])
                            ->withInput()
                            ->with('error', $stateMessage);
                    }

                    return response()->json([
                        'success' => false,
                        'message' => $stateMessage,
                        'errors' => [
                            'shipper_state' => [$stateMessage],
                        ],
                    ], 422);
                }
            }

            // ============================================================
            // USA – UNITED GROUND PREMIUM HANDLING CHARGE
            // If the resolved shipping_method is "United Ground Premium" and
            // any package's actual weight exceeds 22 kg, apply a ₹5,000
            // handling charge. Use the frontend-confirmed value if present,
            // otherwise compute from packages (defense in depth).
            // ============================================================
            $handlingChargeAmount = 5000;
            $handlingCharge = (float) ($validatedData['handling_charge'] ?? 0);
            if (strcasecmp(($validatedData['shipping_method'] ?? ''), 'UNITED GROUND PREMIUM') === 0 && $handlingCharge <= 0) {
                $hasHandlingPackage = false;
                if (! empty($validatedData['packages']) && is_array($validatedData['packages'])) {
                    foreach ($validatedData['packages'] as $pkg) {
                        $actualWt = (float) ($pkg['actual_weight_kg'] ?? 0);
                        if ($actualWt > 22) {
                            $hasHandlingPackage = true;
                            break;
                        }
                    }
                }
                if ($hasHandlingPackage) {
                    $handlingCharge = $handlingChargeAmount;
                }
            }

            // Keep local records uncommitted until the vendor accepts the order.
            // This permits rollback on vendor failure, but it is not a distributed
            // transaction: a later database commit failure cannot undo vendor state.
            DB::beginTransaction();
            $transactionStarted = true;

            // Store Shipper Info
            $awbNumber = $this->generatePrepaidAwbNumber();

            // Re-price every box from server-owned courier rates. The selected
            // rate supplies the service and zone; each box is matched by its
            // own chargeable weight. Surcharges remain shipment-level.
            $packageRowsForPricing = array_values(array_filter(
                $validatedData['packages'] ?? [],
                fn ($package) => is_array($package)
            ));
            $packageRowsForPricing = ! empty($packageRowsForPricing)
                ? $packageRowsForPricing
                : [[]];
            $basePrice = 0.0;
            $fuelPrice = 0.0;
            $surchargeTotal = 0.0;
            $gstAmt = 0.0;
            $surchargeList = [];

            $parseSurchargeIds = function ($value): array {
                return $this->normalizeSurchargeIds($value);
            };

            $findBoxRate = function (int $customerId, float $weight) use ($courierRate) {
                if (! $courierRate) {
                    return null;
                }

                return CourierRate::where('customer_id', $customerId)
                    ->where('service_id', $courierRate->service_id)
                    ->where(function ($query) use ($courierRate) {
                        $query->where('zone_no', $courierRate->zone_no)
                            ->orWhereNull('zone_no')
                            ->orWhere('zone_no', 0);
                    })
                    ->where('wt_range_start', '<=', $weight)
                    ->where('wt_range_end', '>=', $weight)
                    ->orderByRaw(
                        'CASE WHEN zone_no = ? THEN 0 ELSE 1 END',
                        [(int) ($courierRate->zone_no ?? 0)]
                    )
                    ->orderBy('wt_range_start')
                    ->first();
            };

            foreach ($packageRowsForPricing as $packageData) {
                $chargeableWeight = max(
                    (float) ($packageData['actual_weight_kg'] ?? 0),
                    (float) ($packageData['volumetric_weight'] ?? 0),
                    (float) ($packageData['chargeable_weight'] ?? 0)
                );
                // Exporter's own rate band first (same as create-shipment),
                // then the shared default band.
                $boxRate = ($exporterCustomerId > 0 ? $findBoxRate($exporterCustomerId, $chargeableWeight) : null)
                    ?: $findBoxRate(0, $chargeableWeight)
                    ?: $courierRate;

                if (! $boxRate) {
                    continue;
                }

                $boxBase = (float) $boxRate->price;
                $boxFuel = (float) $boxRate->fuel_charge > 0
                    ? (float) $boxRate->fuel_charge
                    : ($boxBase * (float) $boxRate->fuel_percentage / 100);

                // Each box carries its own surcharges from its own matched rate.
                $boxSurchargeIds = $parseSurchargeIds($boxRate->surcharge_id);
                $boxSurcharge = (float) SurCharge::whereIn('id', $boxSurchargeIds)->sum('price');

                $basePrice += $boxBase;
                $fuelPrice += $boxFuel;
                $surchargeTotal += $boxSurcharge;

                if (! empty($boxSurchargeIds)) {
                    foreach (SurCharge::whereIn('id', $boxSurchargeIds)->get() as $s) {
                        $surchargeList[$s->id] = [
                            'id' => $s->id,
                            'name' => $s->name,
                            'code' => $s->code,
                            'price' => (float) $s->price,
                        ];
                    }
                }
            }

            $surchargeData = array_values($surchargeList);
            $surchargeTotal = round($surchargeTotal, 2);
            $gstPct = $courierRate ? (float) $courierRate->gst_percentage : 0;
            // GST is applied once on the combined total
            // (total base + total fuel + total surcharge).
            $gstAmt = $courierRate && (float) $courierRate->gst_amount > 0
                ? (float) $courierRate->gst_amount
                : round(($basePrice + $fuelPrice + $surchargeTotal) * $gstPct / 100, 2);
            $basePrice = round($basePrice, 2);
            $fuelPrice = round($fuelPrice, 2);
            $totalPrice = round($basePrice + $fuelPrice + $gstAmt + $surchargeTotal, 2);

            $shipper = ShipperInfo::create([
                'customer_id' => $validatedData['selected_exporter_customer_id'] ?? null,
                'awb_number' => $awbNumber,
                'shipping_method' => $validatedData['shipping_method'] ?? null,
                'shipper_same_as_customer' => $validatedData['shipper_same_as_customer'] ?? false,
                'company_name' => $validatedData['shipper_company_names'],
                'contact_person' => $validatedData['shipper_contact_person'],
                'address_line1' => $validatedData['shipper_address_line1'],
                'address_line2' => $validatedData['shipper_address_line2'] ?? null,
                'address_line3' => $validatedData['shipper_address_line3'] ?? null,
                'pincode' => $validatedData['shipper_pincode'],
                'city' => $validatedData['shipper_city'],
                'state' => $validatedData['shipper_state'],
                'phone_number' => $validatedData['shipper_phone_number'],
                'email' => $validatedData['shipper_emails'],
                'email_opt_out' => $validatedData['shipper_email_opt_out'] ?? false,
                'kyc_type' => $validatedData['shipper_kyc_type'] ?? null,
                'kyc_number' => $validatedData['shipper_kyc_number'] ?? null,
                'service_rate_id' => $validatedData['service_rate_id'] ?? null,
                'service_id' => $serviceId ?? null,
                'base_price' => $basePrice,
                'fuel_price' => $fuelPrice,
                'gst_percentage' => $gstPct,
                'gst_amount' => $gstAmt,
                'surcharge' => ! empty($surchargeData) ? $surchargeData : null,
                'surcharge_total' => $surchargeTotal,
                'total_base_price' => $basePrice,
                'total_fuel_price' => $fuelPrice,
                'total_surcharge' => $surchargeTotal,
                'total_price' => $totalPrice,
                'status' => 'received',
                'shipment_type' => 4,
            ]);

            $shipperId = $shipper->id;

            // Store the shipment remark (entry remark captured at creation time,
            // finance remark can be filled later by the finance team).
            ShipmentRemark::create([
                'customer_id' => $validatedData['selected_exporter_customer_id'] ?? 0,
                'shipper_id' => $shipperId,
                'entry_remark' => $validatedData['entry_remark'] ?? null,
                'finance_remark' => $validatedData['finance_remark'] ?? null,
            ]);

            // Store Bill To info
            BillTo::create([
                'shipper_id' => $shipperId,
                'customer_id' => $validatedData['selected_exporter_customer_id'] ?? null,
                'biller_name' => $validatedData['biller_name']
                    ?? $validatedData['shipper_contact_person']
                    ?? null,
            ]);

            // Store Consignee Info
            $consignee = ConsigneeInfo::create([
                'shipper_id' => $shipperId,
                'delivery_destination' => $validatedData['delivery_destination'],
                'origin_type' => $validatedData['origin_type'],
                'consignee_name' => $validatedData['consignee_name'],
                'contact_person' => $validatedData['consignee_contact_person'],
                'address_line1' => $validatedData['consignee_address_line1'],
                'address_line2' => $validatedData['consignee_address_line2'] ?? null,
                'address_line3' => $validatedData['consignee_address_line3'] ?? null,
                'zip_code' => $validatedData['consignee_zip_code'],
                'city' => $validatedData['consignee_city'],
                'state' => $validatedData['consignee_state'] ?? null,
                'phone_number' => $validatedData['consignee_phone_number'],
                'email' => $validatedData['consignee_email'],
                'email_opt_out' => $validatedData['consignee_email_opt_out'] ?? false,
            ]);

            // Store Package Dimensions
            $packageIds = [];
            $packageShippingMethod = $validatedData['package_shipping_method'] ?? null;
            $packageRows = $validatedData['packages'] ?? [[]];

            foreach ($packageRows as $packageData) {
                $hasPackageValue = collect($packageData)->filter(function ($value) {
                    return $value !== null && $value !== '';
                })->isNotEmpty() || ! empty($packageShippingMethod);

                if (! $hasPackageValue) {
                    continue;
                }

                $package = PackageDimension::create([
                    'shipper_id' => $shipperId,
                    'shipping_method' => $packageShippingMethod,
                    'actual_weight_kg' => $packageData['actual_weight_kg'] ?? null,
                    'length_cm' => $packageData['length_cm'] ?? null,
                    'width_cm' => $packageData['width_cm'] ?? null,
                    'height_cm' => $packageData['height_cm'] ?? null,
                    'volumetric_weight' => $packageData['volumetric_weight'] ?? null,
                    'chargeable_weight' => $packageData['chargeable_weight'] ?? null,
                ]);

                $packageIds[] = $package->id;
            }

            // Store CSB Information
            $csb = CsbInformation::create([
                'shipper_id' => $shipperId,
                'ecommerce' => $validatedData['ecommerce'] ?? 'No',
                'scheme' => $validatedData['scheme'] ?? 'No',
                'bond_ut_igst' => $validatedData['bond_ut_igst'] ?? null,
                'lut_number' => $validatedData['lut_number'] ?? null,
                'iec_code' => $validatedData['iec_code'] ?? null,
                'gst_number' => $validatedData['gst_number'] ?? null,
                'ad_code' => $validatedData['ad_code'] ?? null,
                'bank_account_number' => $validatedData['bank_account_number'] ?? null,
                'bank_ifsc_code' => $validatedData['bank_ifsc_code'] ?? null,
            ]);

            // Store Shipment Invoice
            $invoiceNumber = $this->generatePrepaidInvoiceNumber();
            $invoice = ShipmentInvoice::create([
                'shipper_id' => $shipperId,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $validatedData['invoice_date'],
                'invoice_amount' => $validatedData['invoice_amount'],
                'incoterms' => $validatedData['incoterms'],
                'invoice_currency' => $validatedData['invoice_currency'],
                'reference_number' => $validatedData['reference_number'] ?? null,
                'status' => 'draft',
                'delivery_type' => 'DDU',
            ]);

            // Store Invoice Items
            \Log::info('Prepaid order items data received:', $validatedData['items'] ?? []);
            if (isset($validatedData['items']) && is_array($validatedData['items'])) {
                foreach ($validatedData['items'] as $item) {
                    // Map box_no to package_dimension_id (box_no 1 = packageIds[0], box_no 2 = packageIds[1], etc.)
                    $boxNo = $item['box_no'] ?? null;
                    $packageDimensionId = null;
                    if ($boxNo !== null && isset($packageIds[$boxNo - 1])) {
                        $packageDimensionId = $packageIds[$boxNo - 1];
                    }
                    ShipmentInvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'package_dimension_id' => $packageDimensionId,
                        'box_no' => $boxNo,
                        'description' => $item['description'] ?? null,
                        'hs_code' => $item['hs_code'] ?? null,
                        'hts_code' => $item['hts_code'] ?? null,
                        'unit_type' => $item['unit_type'] ?? null,
                        'qty' => $item['qty'] ?? null,
                        'unit_rate' => $item['unit_rate'] ?? null,
                        'igst_percentage' => $item['igst_percentage'] ?? null,
                        'igst_amount' => $item['igst_amount'] ?? null,
                        'amount' => $item['amount'] ?? null,
                    ]);
                }
            } else {
                \Log::info('No Prepaid order items data received');
            }

            // Store into create_shipment table.
            // create_shipment.customer_id is NOT NULL (no default) — the admin Prepaid
            // flow has no authenticated customer, so store the selected exporter
            // customer id when one was chosen, otherwise 0 (the admin "default
            // rates" sentinel already used throughout this flow).
            $createShipment = CreateShipment::create([
                'customer_id' => $validatedData['selected_exporter_customer_id'] ?? 0,
                'shipper_id' => $shipperId,
                'awb_number' => $awbNumber,
                'delivery_destination' => $validatedData['delivery_destination'],
                'origin_type' => $validatedData['origin_type'],
                'shipping_method' => $validatedData['shipping_method'] ?? null,
                'shipper_same_as_customer' => $validatedData['shipper_same_as_customer'] ?? false,
                'shipper_company_name' => $validatedData['shipper_company_names'],
                'shipper_contact_person' => $validatedData['shipper_contact_person'],
                'shipper_address_line1' => $validatedData['shipper_address_line1'],
                'shipper_address_line2' => $validatedData['shipper_address_line2'] ?? null,
                'shipper_address_line3' => $validatedData['shipper_address_line3'] ?? null,
                'shipper_pincode' => $validatedData['shipper_pincode'],
                'shipper_city' => $validatedData['shipper_city'],
                'shipper_state' => $validatedData['shipper_state'],
                'shipper_phone_number' => $validatedData['shipper_phone_number'],
                'shipper_email' => $validatedData['shipper_emails'],
                'shipper_email_opt_out' => $validatedData['shipper_email_opt_out'] ?? false,
                'shipper_kyc_type' => $validatedData['shipper_kyc_type'] ?? null,
                'shipper_kyc_number' => $validatedData['shipper_kyc_number'] ?? null,
                'consignee_name' => $validatedData['consignee_name'],
                'consignee_contact_person' => $validatedData['consignee_contact_person'],
                'consignee_address_line1' => $validatedData['consignee_address_line1'],
                'consignee_address_line2' => $validatedData['consignee_address_line2'] ?? null,
                'consignee_address_line3' => $validatedData['consignee_address_line3'] ?? null,
                'consignee_zip_code' => $validatedData['consignee_zip_code'],
                'consignee_city' => $validatedData['consignee_city'],
                'consignee_state' => $validatedData['consignee_state'] ?? null,
                'consignee_phone_number' => $validatedData['consignee_phone_number'],
                'consignee_email' => $validatedData['consignee_email'],
                'consignee_email_opt_out' => $validatedData['consignee_email_opt_out'] ?? false,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $validatedData['invoice_date'],
                'invoice_amount' => $validatedData['invoice_amount'],
                'incoterms' => $validatedData['incoterms'],
                'invoice_currency' => $validatedData['invoice_currency'],
                'reference_number' => $validatedData['reference_number'] ?? null,
                'ecommerce' => $validatedData['ecommerce'] ?? 'No',
                'scheme' => $validatedData['scheme'] ?? 'No',
                'bond_ut_igst' => $validatedData['bond_ut_igst'] ?? null,
                'lut_number' => $validatedData['lut_number'] ?? null,
                'iec_code' => $validatedData['iec_code'] ?? null,
                'gst_number' => $validatedData['gst_number'] ?? null,
                'ad_code' => $validatedData['ad_code'] ?? null,
                'bank_account_number' => $validatedData['bank_account_number'] ?? null,
                'bank_ifsc_code' => $validatedData['bank_ifsc_code'] ?? null,
                'status' => 'manifested',
                'oversize_charge' => $oversizeCharge,
                'handling_charge' => $handlingCharge,
            ]);

            // Create initial tracking record for the shipment.
            // A Prepaid order is auto-manifested on creation, so the first tracking
            // entry reflects the manifested status rather than a draft.
            Tracking::create([
                'awb_number' => $awbNumber,
                'shipper_id' => $shipper->id,
                'shipping_id' => $createShipment->id,
                'uwc_id' => $awbNumber,
                'title' => Tracking::getTitleForStatus('manifested'),
                'status' => 'manifested',
            ]);

            // Log the manifested status change
            ShipmentLog::logStatus(
                $shipper->id,
                $awbNumber,
                'manifested',
                null,
                'Prepaid order created (manifested)',
                $validatedData['selected_exporter_customer_id'] ?? null,
                'admin'
            );

            // Create the manifest record so the Prepaid order shows up under the
            // Manifested tab with a manifest number, exactly like the customer
            // manifest flow does for regular shipments.
            Manifest::createForShipper(
                $shipper->id,
                (int) ($validatedData['selected_exporter_customer_id'] ?? 0)
            );

            // ============================================================
            // Carrier API call at creation time (Create Now button).
            // The SELECTED service's own carrier API is booked — same
            // per-service routing as manifest-time in the customer flow
            // (shipuniversal / primus / overseas / postshipping /
            // flyingtigers / shipglobal / UPS default) — while the DB
            // transaction is still open. The shipment is only committed if
            // the carrier accepts it; otherwise everything rolls back and
            // the carrier error is surfaced to the admin.
            // Skipped entirely for SELF (static option): no carrier accepts
            // it, the order is saved with zero price and the generated AWB.
            // ============================================================
            $carrierTrackingNumber = null;
            $shipmentResponse = null;
            $adomantraResponse = null;
            if (! $isSelfService) {
                $carrierResult = $this->bookPrepaidCarrierAtCreation($shipper, (int) $admin->id);

                if (empty($carrierResult['success'])) {
                    $carrierErrorRaw = $carrierResult['rawResponse'] ?? null;

                    throw new \RuntimeException(
                        $carrierResult['message'] ?? 'Carrier booking failed.'
                    );
                }

                $carrierTrackingNumber = $carrierResult['tracking_number'] ?? null;
                $shipmentResponse = $carrierResult['shipment_response'] ?? null;
            }

            if (! $isSelfService) {
            $adomantraPayload = $this->buildAdomantraOrderPayload(
                $validatedData,
                $admin,
                $courierService ?? null,
                $courierRate,
                $awbNumber,
                $oversizeCharge,
                $handlingCharge
            );

            // Debug the exact payload generated when Create Now is submitted.
            // This is intentionally server-side so the vendor contract is not
            // exposed through browser-side code or a public debug response.
            Log::info('Adomantra Prepaid order payload generated.', [
                'admin_id' => $admin->id,
                'awb_number' => $awbNumber,
                'payload' => $adomantraPayload,
            ]);

            $adomantraResponse = $adomantra->createOrder($adomantraPayload);

            $this->logPrepaidApiCall('adomantra', $adomantraPayload, $adomantraResponse, [
                'stage' => 'response',
                'admin_id' => $admin->id,
                'awb_number' => $awbNumber,
            ]);
            Log::info('Adomantra Prepaid order created.', [
                'admin_id' => $admin->id,
                'awb_number' => $awbNumber,
            ]);
            } else {
                Log::info('SELF Prepaid order created (no carrier API).', [
                    'admin_id' => $admin->id,
                    'awb_number' => $awbNumber,
                ]);
            }

            // Every prepaid order keeps a shipment_tracking row (UPS bookings
            // fill in the carrier response via updateOrCreate above; SELF and
            // non-UPS orders keep this base row) so tracking/label lookups
            // always find one. customer_id is NOT NULL + FK constrained, so a
            // row is only possible when an exporter customer was selected.
            $trackingCustomerId = $validatedData['selected_exporter_customer_id'] ?? $shipper->customer_id ?? null;
            if ($trackingCustomerId) {
                ShipmentTracking::firstOrCreate(
                    ['shipper_id' => $shipper->id],
                    [
                        'customer_id' => $trackingCustomerId,
                        'create_shipment_id' => $createShipment->id,
                        'status' => 'created',
                    ]
                );
            }

            DB::commit();
            $transactionStarted = false;

            if (! $request->expectsJson()) {
                return back()->with('success', 'Prepaid order created successfully!');
            }

            return response()->json([
                'success' => true,
                'message' => 'Prepaid order created successfully!',
                'tracking_number' => $carrierTrackingNumber ?? $awbNumber,
                'is_self' => $isSelfService,
                'data' => [
                    'create_shipment_id' => $createShipment->id,
                    'shipper_id' => $shipper->id,
                    'consignee_id' => $consignee->id,
                    'package_id' => $packageIds[0] ?? null,
                    'package_ids' => $packageIds,
                    'csb_id' => $csb->id,
                    'invoice_id' => $invoice->id,
                    'oversize_charge' => (float) $oversizeCharge,
                    'handling_charge' => (float) $handlingCharge,
                    'carrier_tracking_number' => $carrierTrackingNumber,
                    'ups_response' => $shipmentResponse,
                    'adomantra' => $adomantraResponse,
                ],
            ], 200);
        } catch (ValidationException $e) {
            if ($transactionStarted) {
                DB::rollBack();
                $transactionStarted = false;
            }

            if (! $request->expectsJson()) {
                return back()
                    ->withErrors($e->validator)
                    ->withInput()
                    ->with('error', 'Please correct the highlighted shipment details.');
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            // Local database failure (e.g. NOT NULL / constraint violation).
            // This is NOT a carrier submission problem, so it must not be
            // reported as one. Roll back and show a database-specific error.
            if ($transactionStarted) {
                DB::rollBack();
                $transactionStarted = false;
            }

            Log::error('Prepaid order database failure.', [
                'awb_number' => $awbNumber ?? null,
                'exception' => $e->getMessage(),
            ]);

            $message = 'Unable to save the Prepaid order because of a database error. No shipment was saved. Please try again.';

            if (! $request->expectsJson()) {
                return back()
                    ->withInput()
                    ->with('error', $message);
            }

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 500);
        } catch (\RuntimeException $e) {
            if ($transactionStarted) {
                DB::rollBack();
                $transactionStarted = false;
            }

            Log::error('Prepaid order carrier submission failed.', [
                'awb_number' => $awbNumber ?? null,
                'exception' => $e->getMessage(),
                'raw_response' => $carrierErrorRaw,
            ]);

            $message = 'The Prepaid order could not be submitted to the carrier. No shipment was saved. '.$e->getMessage();

            if (! $request->expectsJson()) {
                return back()
                    ->withInput()
                    ->with('error', $message);
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'rawResponse' => $carrierErrorRaw,
            ], 502);
        } catch (\Exception $e) {
            if ($transactionStarted) {
                DB::rollBack();
                $transactionStarted = false;
            }

            Log::error('Prepaid order creation failed.', [
                'awb_number' => $awbNumber ?? null,
                'exception' => $e->getMessage(),
            ]);

            $message = 'Failed to create Prepaid order. Please try again.';

            if (! $request->expectsJson()) {
                return back()
                    ->withInput()
                    ->with('error', $message);
            }

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 500);
        }
    }

    /**
     * List all Prepaid orders (shipment_type = 5).
     */
    public function prepaidAllOrders(Request $request)
    {
        $type = $request->query('type', $request->query('status', 'all'));

        $isClosedView = in_array($type, ['prepaid_close', 'delivered'], true);

        $withRelations = [
            'shipperInfo.consigneeInfo',
            'shipperInfo.packageDimensions',
            'shipperInfo.manifest',
            'shipperInfo.shipmentRemark',
            'shipperInfo.shipmentTracking',
        ];

        // Main list query - always scoped to Prepaid (shipment_type = 5).
        $query = ShipmentInvoice::with($withRelations);

        if ($isClosedView) {
            // Prepaid Close tile: prepaid orders closed via Close button (status = prepaid_close).
            // 'delivered' ko BC ke liye rakha hai + purane close orders (jo delivered me save the) bhi dikhen.
            $query->whereHas('shipperInfo', function ($shipper) {
                $shipper->where('shipment_type', 4)->whereIn('status', ['prepaid_close', 'delivered']);
            });
        } else {
            $query->whereHas('shipperInfo', function ($shipper) {
                $shipper->where('shipment_type', 4);
            });

            // 'all' aur 'prepaid' me koi extra filter nahi
            if (in_array($type, ['draft', 'manifested'], true)) {
                $query->whereHas('shipperInfo', function ($shipper) use ($type) {
                    $shipper->where('status', $type);
                });
            }
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        $allPrepaid = ShipmentInvoice::whereHas('shipperInfo', function ($shipper) {
            $shipper->where('shipment_type', 4);
        })->count();

        $draftCount = ShipmentInvoice::whereHas('shipperInfo', function ($shipper) {
            $shipper->where('shipment_type', 4)->where('status', 'draft');
        })->count();

        $manifestedCount = ShipmentInvoice::whereHas('shipperInfo', function ($shipper) {
            $shipper->where('shipment_type', 4)->where('status', 'manifested');
        })->count();

        $deliveredCount = ShipmentInvoice::whereHas('shipperInfo', function ($shipper) {
            $shipper->where('shipment_type', 4)->whereIn('status', ['prepaid_close', 'delivered']);
        })->count();

        $counts = [
            'all' => $allPrepaid,
            'draft' => $draftCount,
            'manifested' => $manifestedCount,
            'prepaid' => $allPrepaid,
            'prepaid_close' => $deliveredCount,
            'delivered' => $deliveredCount,
        ];

        // Customer names
        $customerIds = $invoices->getCollection()
            ->map(function ($invoice) {
                return $invoice->shipperInfo?->customer_id;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $customerNames = [];
        if (! empty($customerIds)) {
            $customers = Customer::whereIn('id', $customerIds)->get(['id', 'first_name', 'last_name']);
            foreach ($customers as $customer) {
                $customerNames[$customer->id] = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
            }
        }

        return view('admin.prepaid-all-orders', [
            'invoices' => $invoices,
            'counts' => $counts,
            'status' => $type,
            'type' => $type,
            'customerNames' => $customerNames,
        ]);
    }

    /**
     * Full shipment detail for the All Orders "View" modal (JSON).
     *
     * Same data shape as CustomerController::viewManifestDetail()'s
     * $shipmentDetails entries so the admin modal renders the identical
     * "Shipment Details" layout as the manifest-detail page.
     */
    public function prepaidShipmentDetail($shipperId)
    {
        $shipper = ShipperInfo::with([
            'consigneeInfo',
            'shipmentTracking',
            'packageDimensions',
            'invoices.invoiceItems',
            'serviceRate.service',
            'manifest.customer' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'phone_number', 'email');
            },
        ])->find($shipperId);

        if (! $shipper) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        $consignee = $shipper->consigneeInfo;
        $tracking = $shipper->shipmentTracking;
        $invoice = $shipper->invoices->sortByDesc('id')->first();
        $items = $invoice ? $invoice->invoiceItems : collect([]);
        $packages = $shipper->packageDimensions;

        // Customer: manifest owner first, else the exporter's own customer record.
        $customerModel = $shipper->manifest?->customer;
        if (! $customerModel && $shipper->customer_id) {
            $customerModel = Customer::select('id', 'first_name', 'last_name', 'phone_number', 'email')
                ->find($shipper->customer_id);
        }

        $displayAmount = $shipper->total_price !== null && (float) $shipper->total_price > 0
            ? (float) $shipper->total_price
            : round((float) $items->sum('amount'), 2);

        $data = [
            'shipper_id' => (int) $shipper->id,
            'awb_number' => $shipper->awb_number ?? null,
            'manifest_number' => $shipper->manifest?->manifest_number,
            'tracking_number' => $tracking ? ($tracking->shipment_identification_number ?? null) : null,
            'invoice_number' => $invoice ? ($invoice->invoice_number ?? null) : null,
            'invoice_date' => $invoice && $invoice->invoice_date ? $invoice->invoice_date->format('d-m-Y') : null,
            'invoice_amount' => $invoice ? number_format((float) $items->sum('amount'), 2) : null,
            'invoice_currency' => $invoice ? ($invoice->invoice_currency ?? null) : null,
            'incoterms' => $invoice ? ($invoice->incoterms ?? null) : null,
            'reference_number' => $invoice ? ($invoice->reference_number ?? null) : null,
            'status' => $shipper->status ?: 'draft',
            'order_date' => $shipper->created_at
                ? $shipper->created_at->format('d-m-Y h:i A')
                : null,
            'customer' => $customerModel ? [
                'name' => trim(($customerModel->first_name ?? '') . ' ' . ($customerModel->last_name ?? '')),
                'phone' => $customerModel->phone_number ?? null,
                'email' => $customerModel->email ?? null,
            ] : null,
            'ship_from' => trim(($shipper->city ?? '') . ', ' . ($shipper->state ?? '') . ' - ' . ($shipper->pincode ?? '') . ', India'),
            'ship_to' => $consignee
                ? trim(($consignee->city ?? '') . ', ' . ($consignee->state ?? '') . ' - ' . ($consignee->zip_code ?? '') . ', ' . ($consignee->delivery_destination ?? ''))
                : null,
            'shipper' => [
                'company' => $shipper->company_name,
                'contact' => $shipper->contact_person,
                'phone' => $shipper->phone_number,
                'email' => $shipper->email,
                'address' => trim(($shipper->address_line1 ?? '') . ' ' . ($shipper->address_line2 ?? '') . ' ' . ($shipper->address_line3 ?? '')),
                'address_line1' => $shipper->address_line1,
                'address_line2' => $shipper->address_line2,
                'address_line3' => $shipper->address_line3,
                'kyc_number' => $shipper->kyc_number,
                'city_state_pin' => trim(($shipper->city ?? '') . ', ' . ($shipper->state ?? '') . ' - ' . ($shipper->pincode ?? '')),
            ],
            'consignee' => $consignee ? [
                'name' => $consignee->consignee_name,
                'contact' => $consignee->contact_person,
                'phone' => $consignee->phone_number,
                'email' => $consignee->email,
                'address' => trim(($consignee->address_line1 ?? '') . ' ' . ($consignee->address_line2 ?? '') . ' ' . ($consignee->address_line3 ?? '')),
                'address_line1' => $consignee->address_line1,
                'address_line2' => $consignee->address_line2,
                'address_line3' => $consignee->address_line3,
                'city_state_zip' => trim(($consignee->city ?? '') . ', ' . ($consignee->state ?? '') . ' - ' . ($consignee->zip_code ?? '')),
            ] : null,
            'destination' => $consignee ? $consignee->delivery_destination : null,
            'origin_type' => $consignee ? $consignee->origin_type : null,
            'shipping_method' => $shipper->shipping_method,
            'service' => $this->resolvePrepaidShipmentService($shipper),
            'packages' => $packages->map(function ($pkg, $idx) {
                return [
                    'index' => $idx + 1,
                    'weight' => $pkg->actual_weight_kg,
                    'length' => $pkg->length_cm,
                    'width' => $pkg->width_cm,
                    'height' => $pkg->height_cm,
                    'volumetric' => $pkg->volumetric_weight,
                    'chargeable' => $pkg->chargeable_weight,
                ];
            })->values()->toArray(),
            'items' => $items->map(function ($item) {
                $qty = $item->qty ?? 0;
                $rate = $item->unit_rate ?? 0;
                $igstAmt = $item->igst_amount ?? 0;
                $baseAmount = $qty * $rate;
                $amount = $item->amount ?? ($baseAmount + $igstAmt);

                return [
                    'box_no' => $item->box_no,
                    'description' => $item->description,
                    'hs_code' => $item->hs_code,
                    'hts_code' => $item->hts_code,
                    'unit_type' => $item->unit_type,
                    'qty' => $qty,
                    'unit_rate' => $rate,
                    'igst_percentage' => $item->igst_percentage ?? 0,
                    'igst_amount' => number_format($igstAmt, 2),
                    'amount' => number_format($amount, 2),
                ];
            })->values()->toArray(),
            'items_total' => number_format($displayAmount, 2),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Resolve the courier service a COD shipment was created with.
     * Prefers the stored rate chain (service_rate_id -> service), then the
     * stored service_id, then a method-name match. Returns null when unknown.
     */
    private function resolvePrepaidShipmentService($shipper): ?array
    {
        $service = $shipper->serviceRate?->service;

        if (! $service && $shipper->service_id) {
            $service = CourierService::find($shipper->service_id);
        }

        if (! $service && $shipper->shipping_method) {
            $service = CourierService::whereRaw('LOWER(method) = ?', [strtolower($shipper->shipping_method)])->first();
        }

        if (! $service) {
            return null;
        }

        return [
            'method' => $service->method,
            'network' => $service->network,
            'tat' => $service->tat,
            'api_provider' => $service->api_provider,
            'service_code' => $service->service_code,
        ];
    }

    /**
     * Close Order modal se Prepaid order close karo.
     * shipment_type = 4 rehta hai, status = prepaid_close. Remark finance_remark me save hota hai.
     */
    public function prepaidCloseOrder(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|integer|exists:shipment_invoice,id',
            'shipper_id' => 'nullable|integer|exists:shipper_info,id',
            'remark' => 'nullable|string|max:1000',
        ]);

        $invoice = ShipmentInvoice::findOrFail($validated['invoice_id']);
        $shipperId = $validated['shipper_id'] ?? $invoice->shipper_id;

        $shipper = ShipperInfo::findOrFail($shipperId);

        $shipper->shipment_type = 4;
        $shipper->status = 'prepaid_close';
        $shipper->save();

        $remark = trim((string) ($validated['remark'] ?? ''));

        $shipmentRemark = ShipmentRemark::firstOrNew(['shipper_id' => $shipper->id]);
        if ($shipmentRemark->exists === false) {
            $shipmentRemark->customer_id = $shipper->customer_id ?? 0;
        }
        // Close Order modal se bhara remark hamesha finance_remark me jayega
        if ($remark !== '') {
            $shipmentRemark->finance_remark = $remark;
        }
        $shipmentRemark->save();

        return response()->json([
            'success' => true,
            'message' => 'Order closed as PREPAID_CLOSE',
            'shipment_type' => $shipper->shipment_type,
        ]);
    }

    /**
     * Generate a unique AWB number for Prepaid orders.
     * Same format as the customer create-shipment page:
     * UWC + YYMMDD + 5-digit serial (resets daily).
     * Example: UWC26060200001
     */
    private function generatePrepaidAwbNumber()
    {
        $prefix = 'UWC';
        $datePart = now()->format('ymd');

        $todayPrefix = $prefix.$datePart;
        $lastAwb = ShipperInfo::where('awb_number', 'LIKE', $todayPrefix.'%')
            ->orderBy('awb_number', 'desc')
            ->value('awb_number');

        if ($lastAwb) {
            $lastSerial = (int) substr($lastAwb, -5);
            $newSerial = $lastSerial + 1;
        } else {
            $newSerial = 1;
        }

        return $todayPrefix.str_pad($newSerial, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique invoice number for Prepaid orders.
     */
    private function generatePrepaidInvoiceNumber()
    {
        return 'INV-PREPAID-'.now()->format('ymdHis').'-'.random_int(1000, 9999);
    }

    // ============================================================
    // COD ORDER HELPERS (mirrors CustomerController so the admin COD
    // create-order page behaves identically to the customer
    // create-shipment page)
    // ============================================================

    /**
     * Build the Adomantra order payload for a Prepaid order.
     *
     * Mirrors CustomerController::buildAdomantraOrderPayload() exactly, but
     * accepts an Admin instead of a Customer: the sender name and account
     * code come from the logged-in admin (the Admin model has a single
     * `name` field and no customer_code).
     */
    private function buildAdomantraOrderPayload(
        array $data,
        Admin $admin,
        ?CourierService $service,
        ?CourierRate $rate,
        string $awbNumber,
        float $oversizeCharge,
        float $handlingCharge
    ): array {
        $items = is_array($data['items'] ?? null) ? $data['items'] : [];
        $packages = is_array($data['packages'] ?? null) ? $data['packages'] : [];
        $baseAmount = (float) ($rate?->price ?? 0);
        $fuelPercentage = (float) ($rate?->fuel_percentage ?? 0);
        $fuel = (float) ($rate?->fuel_charge ?? 0);
        if ($fuel <= 0) {
            $fuel = $baseAmount * $fuelPercentage / 100;
        }
        $gstPercentage = (float) ($rate?->gst_percentage ?? 0);
        $gst = (float) ($rate?->gst_amount ?? 0);
        $surcharge = (float) ($rate?->surcharge_amount ?? 0);
        if ($gst <= 0) {
            $gst = ($baseAmount + $fuel + $surcharge) * $gstPercentage / 100;
        }
        $miscellaneous = $oversizeCharge + $handlingCharge;
        $invoiceAmount = (float) ($data['invoice_amount'] ?? 0);
        $destination = (string) ($data['delivery_destination'] ?? '');
        $destinationRecord = Destination::where('name', $destination)->first();
        $countryCode = strtoupper((string) ($destinationRecord?->country_code ?? ''));
        $csbType = strtoupper((string) ($data['origin_type'] ?? 'CSB IV')) === 'CSB V' ? 'CSB 5' : 'CSB 4';
        $customerName = trim((string) ($admin->name ?? 'Admin'));
        $accountCode = 'UWC'.str_pad((string) $admin->id, 6, '0', STR_PAD_LEFT);

        $productDetails = array_map(function (array $item): array {
            return [
                'BoxNo' => (string) ($item['box_no'] ?? ''),
                'Description' => (string) ($item['description'] ?? ''),
                'HSNCode' => (string) ($item['hs_code'] ?? ''),
                'HTSCode' => (string) ($item['hts_code'] ?? ''),
                'UnitType' => (string) ($item['unit_type'] ?? 'PCS'),
                'Qty' => (float) ($item['qty'] ?? 0),
                'UnitRate' => (float) ($item['unit_rate'] ?? 0),
                'ShipPieceIGST' => (float) ($item['igst_amount'] ?? 0),
                'PieceWt' => 0,
            ];
        }, array_values(array_filter($items, 'is_array')));

        $packageDetails = array_map(function (array $package): array {
            return [
                'Length' => (float) ($package['length_cm'] ?? 0),
                'Width' => (float) ($package['width_cm'] ?? 0),
                'Height' => (float) ($package['height_cm'] ?? 0),
                'ActualWeight' => (float) ($package['actual_weight_kg'] ?? 0),
            ];
        }, array_values(array_filter($packages, 'is_array')));

        return [
            'Awbno' => $awbNumber,
            'AccountCode' => $accountCode,
            'AccountName' => $customerName,
            'Origin' => 'DEL',
            'PaymentType' => 'Credit',
            'ShipDate' => now()->format('Y-m-d\\TH:i:s'),
            'Sender' => [
                'SenderName' => (string) ($data['shipper_company_names'] ?? $customerName),
                'SenderContactPerson' => (string) ($data['shipper_contact_person'] ?? $customerName),
                'SenderAddressLine1' => (string) ($data['shipper_address_line1'] ?? ''),
                'SenderAddressLine2' => (string) ($data['shipper_address_line2'] ?? ''),
                'SenderAddressLine3' => (string) ($data['shipper_address_line3'] ?? ''),
                'SenderPincode' => (string) ($data['shipper_pincode'] ?? ''),
                'SenderCity' => (string) ($data['shipper_city'] ?? ''),
                'SenderState' => (string) ($data['shipper_state'] ?? ''),
                'SenderTelephone' => (string) ($data['shipper_phone_number'] ?? ''),
                'SenderEmailId' => (string) ($data['shipper_emails'] ?? ''),
                'KYCType' => (string) ($data['shipper_kyc_type'] ?? ''),
                'KYCNo' => (string) ($data['shipper_kyc_number'] ?? ''),
            ],
            'Receiver' => [
                'ReceiverName' => (string) ($data['consignee_name'] ?? ''),
                'ReceiverContactPerson' => (string) ($data['consignee_contact_person'] ?? ''),
                'ReceiverAddressLine1' => (string) ($data['consignee_address_line1'] ?? ''),
                'ReceiverAddressLine2' => (string) ($data['consignee_address_line2'] ?? ''),
                'ReceiverAddressLine3' => (string) ($data['consignee_address_line3'] ?? ''),
                'ReceiverZipcode' => (string) ($data['consignee_zip_code'] ?? ''),
                'ReceiverCity' => (string) ($data['consignee_city'] ?? ''),
                'ReceiverState' => (string) ($data['consignee_state'] ?? ''),
                'ReceiverCountry' => $countryCode !== '' ? $countryCode : $destination,
                'ReceiverTelephone' => (string) ($data['consignee_phone_number'] ?? ''),
                'ReceiverEmailid' => (string) ($data['consignee_email'] ?? ''),
                'VatId' => '',
            ],
            'ServiceDetails' => [
                'ServiceCode' => (string) ($service?->service_code ?? $service?->scode ?? ''),
                'ServiceName' => (string) ($service?->method ?? $data['shipping_method'] ?? ''),
                'Forwarder' => (string) ($service?->shipper_code ?? $service?->network ?? ''),
                'NetworkCode' => (string) ($service?->network ?? ''),
                'NetworkName' => (string) ($service?->description ?? $service?->network ?? ''),
                'NetworkNo' => (string) ($service?->method_code ?? ''),
                'GoodsType' => 'NDOX',
                'PackageType' => 'PACKAGE',
            ],
            'PackageDetails' => ['PackageDetail' => $packageDetails],
            'AdditionalDetails' => [
                'IsThirdParty' => false,
                'ProductDetails' => $productDetails,
                'InvoiceCurrency' => (string) ($data['invoice_currency'] ?? ''),
                'InvoiceNo' => (string) ($data['invoice_number'] ?? ''),
                'InvoiceDate' => date('Y-m-d\\T00:00:00', strtotime((string) ($data['invoice_date'] ?? 'now'))),
                'TermsOfSale' => (string) ($data['incoterms'] ?? ''),
                'ReasonForExport' => 'Sale',
                'FreightCharge' => round($baseAmount, 2),
                'InsuranceCharge' => 0,
                'CSB_Type' => $csbType,
                'CustomerRefNo' => (string) ($data['reference_number'] ?? ''),
                'DeliveryConfirmation' => '',
                'DutyTax' => '',
                'DutiesAccountNo' => '',
                'TransactionId' => $awbNumber,
                'IECNo' => (string) ($data['iec_code'] ?? ''),
                'ADCode' => (string) ($data['ad_code'] ?? ''),
                'BankType' => '',
                'NFEI' => false,
                'Ecom' => strtoupper((string) ($data['ecommerce'] ?? 'No')) === 'YES',
                'MEIS' => false,
                'BankAccount' => (string) ($data['bank_account_number'] ?? ''),
                'ProductType' => 'Commercial',
                'BoundUT' => (string) ($data['bond_ut_igst'] ?? ''),
                'IGSTAmount' => round(array_sum(array_map(fn (array $item): float => (float) ($item['igst_amount'] ?? 0), array_filter($items, 'is_array'))), 2),
                'IGSTPaid' => strtoupper((string) ($data['bond_ut_igst'] ?? '')) === 'IGST' ? 'Yes' : 'No',
                'ShipperImage' => '',
                'ShipperKYC' => '',
                'FileName' => '',
            ],
            'FreightDetails' => [
                'BasicAmount' => round($baseAmount, 2),
                'FuelPercentage' => round($fuelPercentage, 2),
                'Fuel' => round($fuel, 2),
                'MisFuel' => 0,
                'Misc' => round($miscellaneous, 2),
                'Demand' => 0,
                'GreenSuch' => 0,
                'Taxable' => round($baseAmount + $fuel + $surcharge + $miscellaneous, 2),
                'SGST' => 0,
                'CGST' => 0,
                'IGST' => round($gst, 2),
                'NTaxable' => 0,
                'NetTotal' => round($baseAmount + $fuel + $gst + $surcharge + $miscellaneous, 2),
            ],
            'MiscDetailsTable' => array_values(array_filter([
                $oversizeCharge > 0 ? ['MiscCode' => 'OVERSIZE', 'MiscName' => 'Oversize Charge', 'MisAmt' => round($oversizeCharge, 2), 'MisNTax' => 0, 'MisFuel' => 0] : null,
                $handlingCharge > 0 ? ['MiscCode' => 'HANDLING', 'MiscName' => 'Handling Charge', 'MisAmt' => round($handlingCharge, 2), 'MisNTax' => 0, 'MisFuel' => 0] : null,
            ])),
        ];
    }

    /**
     * Get UPS OAuth access token and cache it.
     * Expects UPS_CLIENT_ID and UPS_CLIENT_SECRET in environment.
     */
    private function getUpsAccessToken()
    {
        $cacheKey = 'ups_access_token';
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $clientId = env('UPS_CLIENT_ID');
        $clientSecret = env('UPS_CLIENT_SECRET');
        $tokenUrl = 'https://onlinetools.ups.com/security/v1/oauth/token';

        if (! $clientId || ! $clientSecret) {
            throw new \Exception('UPS client credentials not configured. Set UPS_CLIENT_ID and UPS_CLIENT_SECRET in .env');
        }

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post($tokenUrl, ['grant_type' => 'client_credentials']);

        if (! $response->successful()) {
            \Log::error('UPS token error: '.$response->body());
            throw new \Exception('Unable to retrieve UPS access token');
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            \Log::error('UPS token missing access_token: '.$response->body());
            throw new \Exception('UPS access token not found in response');
        }

        $expiresIn = isset($data['expires_in']) ? (int) $data['expires_in'] : 3600;
        $ttl = max(60, $expiresIn - 60);
        Cache::put($cacheKey, $data['access_token'], $ttl);

        return $data['access_token'];
    }

    // new ups rate made by "Anil Sir"
    private function normalizeSurchargeIds($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('intval', $value)));
        }
        if (is_string($value)) {
            $value = trim($value);
            if ($value === '' || $value === 'null' || $value === '[]') {
                return [];
            }
            // JSON array string like "[1,2]" (DB stores it as a JSON string).
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map('intval', $decoded)));
            }

            // Plain comma-separated string like "1,2".
            return array_values(array_filter(array_map('intval', explode(',', $value))));
        }

        return [];
    }

    /**
     * Resolve the two-letter destination country code for the COD rate
     * calculation. Mirrors CustomerController::resolveDestinationCountry()
     * exactly: fast-paths for USA/UK/CA/AUS/UAE/NZ/SG/MY/DE/BD plus a
     * dynamic lookup against the destinations table.
     */
    public function resolveDestinationCountry($destination)
    {
        $destinationValue = trim((string) ($destination ?? ''));

        // Resolve numeric dropdown/API values through the destination record so
        // country selection never depends on a display-name fallback.
        if (ctype_digit($destinationValue)) {
            $destinationRecord = Destination::find((int) $destinationValue);
            if ($destinationRecord) {
                $destinationValue = $destinationRecord->country_code
                    ?: $destinationRecord->code
                    ?: $destinationRecord->name;
            }
        }

        $destUpper = strtoupper($destinationValue);

        // USA detection — covers "USA", "US", "United States",
        // "United States of America", "US - United States", etc.
        $isUSA = (
            $destUpper === 'USA'
            || $destUpper === 'US'
            || str_contains($destUpper, 'UNITED STATES')
            || str_starts_with($destUpper, 'US -')
        );

        if ($isUSA) {
            return 'USA';
        }

        // UK detection — covers "UK", "GB", "United Kingdom", "UK - United Kingdom",
        // "Great Britain", and any string starting with "UK -".
        $isUk = (
            $destUpper === 'UK'
            || $destUpper === 'GB'
            || str_contains($destUpper, 'UNITED KINGDOM')
            || str_starts_with($destUpper, 'UK -')
            || str_contains($destUpper, 'GREAT BRITAIN')
        );
        if ($isUk) {
            return 'UK';
        }

        // Canada detection — covers "Canada", "CA", and any string containing "Canada".
        $isCanada = (
            $destUpper === 'CANADA'
            || $destUpper === 'CA'
            || str_contains($destUpper, 'CANADA')
        );
        if ($isCanada) {
            return 'CA';
        }

        // Australia detection — covers "Australia", "AU", "AUS",
        // and any string containing "Australia". Returns "AUS" to match the
        // destinations.country_code value used for Australia.
        $isAustralia = (
            $destUpper === 'AUSTRALIA'
            || $destUpper === 'AU'
            || $destUpper === 'AUS'
            || str_contains($destUpper, 'AUSTRALIA')
        );
        if ($isAustralia) {
            return 'AUS';
        }

        // UAE detection also covers the destination display name "Dubai".
        $isUae = (
            $destUpper === 'UNITED ARAB EMIRATES'
            || $destUpper === 'UAE'
            || $destUpper === 'AE'
            || $destUpper === 'ARE'
            || str_contains($destUpper, 'DUBAI')
            || str_contains($destUpper, 'UNITED ARAB EMIRATES')
        );

        if ($isUae) {
            return 'UAE';
        }

        // New Zealand detection — covers "New Zealand", "NZ", "NZL",
        // and any string containing "New Zealand". Returns "NZ" to match the
        // destinations.country_code value used for New Zealand.
        $isNewZealand = (
            $destUpper === 'NEW ZEALAND'
            || $destUpper === 'NZ'
            || $destUpper === 'NZL'
            || str_contains($destUpper, 'NEW ZEALAND')
        );
        if ($isNewZealand) {
            return 'NZ';
        }

        // Singapore detection — covers "Singapore", "SG", and "SGP".
        $isSingapore = (
            $destUpper === 'SINGAPORE'
            || $destUpper === 'SG'
            || $destUpper === 'SGP'
            || str_contains($destUpper, 'SINGAPORE')
        );
        if ($isSingapore) {
            return 'SG';
        }

        // Malaysia detection — covers "Malaysia", "MY", and "MYS".
        $isMalaysia = (
            $destUpper === 'MALAYSIA'
            || $destUpper === 'MY'
            || $destUpper === 'MYS'
            || str_contains($destUpper, 'MALAYSIA')
        );
        if ($isMalaysia) {
            return 'MY';
        }

        // Germany detection — covers "Germany", "DE", and "DEU".
        $isGermany = (
            $destUpper === 'GERMANY'
            || $destUpper === 'DE'
            || $destUpper === 'DEU'
            || str_contains($destUpper, 'GERMANY')
        );
        if ($isGermany) {
            return 'DE';
        }

        // Bangladesh detection — covers "Bangladesh", "BD", and "BGD".
        $isBangladesh = (
            $destUpper === 'BANGLADESH'
            || $destUpper === 'BD'
            || $destUpper === 'BGD'
            || str_contains($destUpper, 'BANGLADESH')
        );
        if ($isBangladesh) {
            return 'BD';
        }

        // ------------------------------------------------------------------
        // DYNAMIC LOOKUP — resolve any remaining destination against the
        // destinations table by country_code, code, or name (case-insensitive,
        // including a substring match on the display name). This keeps newly
        // added destinations (e.g. Zimbabwe) working without having to edit
        // this method every time a country is added.
        // ------------------------------------------------------------------
        $matchedDestination = Destination::where(function ($query) use ($destUpper) {
            $query->whereRaw('UPPER(country_code) = ?', [$destUpper])
                ->orWhereRaw('UPPER(code) = ?', [$destUpper])
                ->orWhereRaw('UPPER(name) = ?', [$destUpper])
                ->orWhereRaw('UPPER(name) LIKE ?', ['%'.$destUpper.'%']);
        })->first();

        if ($matchedDestination) {
            return $matchedDestination->country_code
                ?: $matchedDestination->code
                ?: $matchedDestination->name;
        }

        // Everything else (US, USA, United States, etc.) → US.
        // return 'US';
    }

    /**
     * Country code => known name variants (ISO codes, display names, aliases).
     *
     * courier_services.country stores a MIX of both (e.g. 'DE' and 'Germany',
     * 'US' and 'USA'), so country-wise matching must accept every variant of
     * the selected destination — otherwise that country's services never show.
     */
    private function prepaidCountryVariantMap(): array
    {
        return [
            'US' => ['USA', 'UNITED STATES', 'UNITED STATES OF AMERICA', 'US- UNITED STATE OF AMERICA'],
            'UK' => ['GB', 'UNITED KINGDOM', 'GREAT BRITAIN', 'UK - UNITED KINGDOM'],
            'CA' => ['CANADA'],
            'AUS' => ['AU', 'AUSTRALIA'],
            'UAE' => ['AE', 'ARE', 'UNITED ARAB EMIRATES', 'DUBAI'],
            'NZ' => ['NZL', 'NEW ZEALAND'],
            'SG' => ['SGP', 'SINGAPORE'],
            'MY' => ['MYS', 'MALAYSIA'],
            'DE' => ['DEU', 'GERMANY'],
            'BD' => ['BGD', 'BANGLADESH'],
            'ZW' => ['ZWE', 'ZIMBABWE'],
        ];
    }

    /**
     * Canonical country code for any known variant (case-insensitive).
     * Unknown values pass through unchanged.
     */
    private function canonicalPrepaidCountry($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $upper = strtoupper(trim((string) $value));
        if ($upper === '') {
            return null;
        }

        foreach ($this->prepaidCountryVariantMap() as $code => $variants) {
            if ($upper === $code) {
                return $code;
            }
            foreach ($variants as $variant) {
                if ($upper === $variant) {
                    return $code;
                }
            }
        }

        return trim((string) $value);
    }

    /**
     * Every courier_services.country value that belongs to the selected
     * destination (canonical code + destination record fields + variants),
     * for country-wise service/rate matching.
     */
    private function prepaidCountryAliases($destinationCountry, $destination = null): array
    {
        $aliases = [];

        if ($destination) {
            foreach (['country_code', 'code', 'name'] as $attr) {
                $value = trim((string) ($destination->{$attr} ?? ''));
                if ($value !== '') {
                    $aliases[] = $value;
                }
            }
        }

        if ($destinationCountry !== null && trim((string) $destinationCountry) !== '') {
            $aliases[] = trim((string) $destinationCountry);
        }

        $map = $this->prepaidCountryVariantMap();
        $expanded = $aliases;
        foreach ($aliases as $alias) {
            $canonical = $this->canonicalPrepaidCountry($alias);
            if ($canonical !== null) {
                $expanded[] = $canonical;
                foreach ($map[$canonical] ?? [] as $variant) {
                    $expanded[] = $variant;
                }
            }
        }

        // Unique case-insensitively (MySQL matching is case-insensitive anyway).
        $unique = [];
        foreach ($expanded as $value) {
            $key = strtolower(trim((string) $value));
            if ($key !== '' && ! isset($unique[$key])) {
                $unique[$key] = trim((string) $value);
            }
        }

        return array_values($unique);
    }

    /**
     * Determine if a shipping method should be routed to the Flying Tigers API.
     * Triggered for UNITED ECO POST shipments.
     *
     * @param  string|null  $shippingMethod
     * @return bool
     */
    private function isFlyingTigersMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return false;
        }

        $methodUpper = strtoupper(trim($shippingMethod));

        // Flying Tigers API is called for UNITED ECO POST shipments.
        return str_contains($methodUpper, 'UNITED ECO POST');
    }

    /**
     * Determine if a shipping method should be routed to the Overseas Logistic API.
     * Triggered for Canada shipments created with the methods:
     *   - UNITED CANADA DDP
     *   - UNITED CANADA E-COMMERCE
     *
     * Both variants use the same Overseas Logistic endpoint and payload; the
     * Service field inside ServiceDetails differentiates the exact service.
     *
     * @param  string|null  $shippingMethod
     * @return bool
     */
    private function isCanadaOverseasMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return false;
        }

        $methodUpper = strtoupper(trim($shippingMethod));

        // Match "UNITED CANADA" with either "DDP" or "E-COMMERCE"/"ECOMMERCE".
        $isCanada = str_contains($methodUpper, 'UNITED CANADA');
        $isDdpOrEcom = str_contains($methodUpper, 'DDP')
            || str_contains($methodUpper, 'E-COMMERCE')
            || str_contains($methodUpper, 'ECOMMERCE')
            || str_contains($methodUpper, 'E COMMERCE');

        return $isCanada && $isDdpOrEcom;
    }

    /**
     * Determine if a shipping method should be routed to the Overseas
     * Logistic API for Australia shipments.
     *
     * Triggered for the "ARAMEX GPX ALL IN" service (Australia), whose
     * courier_services.service_code is "AUSTRALIA-ARAMEX-GPX", and for the
     * "DPEX_AU_EXPRESS" service (Australia), whose courier_services.service_code
     * is "DPEX_AU_EXPRESS". The same Overseas Logistic endpoint/payload is
     * reused; the Service field inside ServiceDetails (resolved from
     * courier_services.service_code) differentiates the exact service, and
     * ReceiverCountry is set to "AU" via getOverseasCountryCode().
     *
     * @param  string|null  $shippingMethod
     * @return bool
     */
    private function isAustraliaOverseasMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return false;
        }

        $methodUpper = strtoupper(trim($shippingMethod));

        // Match "ARAMEX GPX" (the Australia service method is
        // "ARAMEX GPX ALL IN"). Matching on "ARAMEX GPX" is intentionally
        // tolerant of the "ALL IN" suffix so future variants still route
        // correctly.
        //
        // Also match "DPEX_AU_EXPRESS" / "DPEX AU EXPRESS" — the Australia
        // DPEX express service. Matching is tolerant of underscores vs
        // spaces so both the service_code and human-readable variants route
        // through the Overseas Logistic API.
        return str_contains($methodUpper, 'ARAMEX GPX')
            || str_contains($methodUpper, 'DPEX_AU_EXPRESS')
            || str_contains($methodUpper, 'DPEX AU EXPRESS');
    }

    /**
     * Determine if a shipping method should be routed to the Overseas
     * Logistic API at all (Canada OR Australia variants).
     *
     * Centralises the Overseas-routing decision so both manifestShipment()
     * and bulkManifestShipments() stay in sync.
     *
     * @param  string|null  $shippingMethod
     * @return bool
     */
    private function isOverseasLogisticMethod($shippingMethod)
    {
        return $this->isCanadaOverseasMethod($shippingMethod)
            || $this->isAustraliaOverseasMethod($shippingMethod);
    }

    /**
     * Find the courier rate row for one box: exact weight-band match first,
     * nearest configured band as fallback (by distance to the band edges).
     *
     * Returns [rateRow|null, isFallback]. Only when the service has no rates
     * at all for the zone scope does it return [null, false] — so services
     * are never hidden merely because of the entered weight.
     */
    private function findPrepaidBoxRate(int $serviceId, $destinationCountry, $zoneNumber, float $weight): array
    {
        $exact = \DB::select(
            'SELECT cr.*, cs.country, cs.service_code, cs.method
                FROM courier_rates cr
                INNER JOIN courier_services cs ON cr.service_id = cs.id
                WHERE cr.customer_id = 0
                AND cr.service_id = ?
                AND cs.country = ?
                AND (cr.zone_no = ? OR (cr.zone_no IS NULL OR cr.zone_no = 0))
                AND ? BETWEEN cr.wt_range_start AND cr.wt_range_end
                ORDER BY cr.zone_no DESC, cr.wt_range_start
                LIMIT 1',
            [$serviceId, $destinationCountry, $zoneNumber, $weight]
        );

        if (! empty($exact)) {
            return [$exact[0], false];
        }

        $nearest = \DB::select(
            'SELECT cr.*, cs.country, cs.service_code, cs.method
                FROM courier_rates cr
                INNER JOIN courier_services cs ON cr.service_id = cs.id
                WHERE cr.customer_id = 0
                AND cr.service_id = ?
                AND cs.country = ?
                AND (cr.zone_no = ? OR (cr.zone_no IS NULL OR cr.zone_no = 0))
                ORDER BY LEAST(ABS(? - cr.wt_range_start), ABS(? - cr.wt_range_end)), cr.wt_range_start
                LIMIT 1',
            [$serviceId, $destinationCountry, $zoneNumber, $weight, $weight]
        );

        if (! empty($nearest)) {
            return [$nearest[0], true];
        }

        return [null, false];
    }

    /**
     * In-memory box matcher (same rules as create-shipment's getUpsRate()):
     * exact weight-band match, exporter's own rates first, then the shared
     * default rates (customer_id = 0). Same zone scope + ordering as the
     * customer SQL (zone_no DESC, wt_range_start).
     *
     * Lets prepaidUpsRate() serve every service x every box from a single bulk
     * query instead of 2 SQL queries per box per service.
     *
     * @param  array  $serviceRates  Raw courier_rates rows (stdClass) for one service.
     * @return array  [rateRow|null, isFallback(always false, kept for shape)]
     */
    private function matchPrepaidBoxRate(array $serviceRates, $zoneNumber, float $weight, int $rateCustomerId = 0): array
    {
        $exactCustomer = [];
        $exactDefault = [];

        $sortExact = function ($a, $b) {
            // Same as SQL: ORDER BY cr.zone_no DESC, cr.wt_range_start
            // (NULL sorts last in DESC, matching MySQL behaviour).
            // Trailing id tie-break keeps the pick deterministic where the
            // old SQL had no defined order (fully tied rows).
            $zoneA = ($a->zone_no ?? null) === null ? -1 : (int) $a->zone_no;
            $zoneB = ($b->zone_no ?? null) === null ? -1 : (int) $b->zone_no;
            if ($zoneA !== $zoneB) {
                return $zoneB <=> $zoneA;
            }
            $startCmp = (float) ($a->wt_range_start ?? 0) <=> (float) ($b->wt_range_start ?? 0);
            if ($startCmp !== 0) {
                return $startCmp;
            }

            return (int) ($a->id ?? 0) <=> (int) ($b->id ?? 0);
        };

        foreach ($serviceRates as $row) {
            $zoneNo = $row->zone_no ?? null;
            $zoneMatch = ($zoneNo === null
                || (int) $zoneNo === 0
                || ($zoneNumber !== null && (int) $zoneNo === (int) $zoneNumber));

            if (! $zoneMatch) {
                continue;
            }

            $start = (float) ($row->wt_range_start ?? 0);
            $end = (float) ($row->wt_range_end ?? 0);
            if ($weight < $start || $weight > $end) {
                continue;
            }

            if ($rateCustomerId > 0 && (int) ($row->customer_id ?? 0) === $rateCustomerId) {
                $exactCustomer[] = $row;
            } elseif ((int) ($row->customer_id ?? 0) === 0) {
                $exactDefault[] = $row;
            }
        }

        if (! empty($exactCustomer)) {
            usort($exactCustomer, $sortExact);

            return [$exactCustomer[0], false];
        }

        if (! empty($exactDefault)) {
            usort($exactDefault, $sortExact);

            return [$exactDefault[0], false];
        }

        return [null, false];
    }

    /**
     * Proxy UPS Rate API call for the admin Prepaid create-order page.
     *
     * Mirrors CustomerController::getUpsRate() exactly (same box-wise
     * calculation, zone resolution and response shape) with these admin
     * differences:
     *   - rates use the selected exporter customer's own rates first
     *     (like create-shipment), falling back to default rates
     *   - the response key is `zone` (not `selected_zone`) because the admin
     *     blade reads data.zone
     */
    public function prepaidUpsRate(Request $request)
    {
        // 1. Get logged-in admin
        $admin = auth()->guard('admin')->user();
        $adminName = trim((string) ($admin->name ?? 'Admin'));

        // 2. Get all inputs
        $totalWeight = floatval($request->total_weight ?? 0);
        $consigneeState = $request->consignee_state;
        $consigneeZipCode = $request->consignee_zip_code;
        $deliveryDestination = $request->delivery_destination;
        $packageWeights = $request->package_weights;
        // Selected exporter customer: their own rates apply first (same as
        // create-shipment), shared default rates (customer_id = 0) otherwise.
        $rateCustomerId = (int) ($request->input('selected_exporter_customer_id') ?? 0);
        $rateCustomerIds = $rateCustomerId > 0 ? [$rateCustomerId, 0] : [0];
        $rateCustomer = $rateCustomerId > 0
            ? Customer::select('id', 'first_name', 'last_name')->find($rateCustomerId)
            : null;
        $rateCustomerName = $rateCustomer
            ? trim(($rateCustomer->first_name ?? '') . ' ' . ($rateCustomer->last_name ?? ''))
            : $adminName;

        // 3. Weight validation
        if ($totalWeight <= 0) {
            return response()->json([
                'success' => true,
                'customer_exists' => false,
                'customer_name' => $rateCustomerName,
                'all_rates' => [],
                'message' => 'Please enter Actual Weight greater than 0 to view rates.',
            ]);
        }

        // 4. Resolve destination and its zone.
        $destinationCountry = $this->resolveDestinationCountry($deliveryDestination);
        $destination = null;
        if (ctype_digit(trim((string) $deliveryDestination))) {
            $destination = Destination::find((int) $deliveryDestination);
        } elseif (! empty($deliveryDestination)) {
            $destinationValue = trim((string) $deliveryDestination);
            $destination = Destination::where(function ($query) use ($destinationValue) {
                $query->whereRaw('UPPER(name) = ?', [strtoupper($destinationValue)])
                    ->orWhereRaw('UPPER(code) = ?', [strtoupper($destinationValue)]);
            })->first();
        }

        // Country-wise matching: courier_services.country stores a mix of ISO
        // codes and full names (e.g. 'DE' and 'Germany'), so match every
        // variant of the selected destination — only that country's services
        // may appear. $rateCountry is the canonical code for the rate logic.
        $countryAliases = $this->prepaidCountryAliases($destinationCountry, $destination);
        $rateCountry = $this->canonicalPrepaidCountry(
            ($destination ? ($destination->country_code ?: $destination->code) : null) ?: $destinationCountry
        );

        $zone = null;
        if (! empty($consigneeState)) {
            $stateValue = trim((string) $consigneeState);
            $stateAliases = [$stateValue];
            $uaeEmirateCodes = [
                'ABU DHABI' => 'AZ',
                'AJMAN' => 'AJ',
                'DUBAI' => 'DU',
                'FUJAIRAH' => 'FU',
                'RAS AL KHAIMAH' => 'RK',
                'SHARJAH' => 'SH',
                'UMM AL QUWAIN' => 'UQ',
            ];
            if ($rateCountry === 'UAE' && isset($uaeEmirateCodes[strtoupper($stateValue)])) {
                $stateAliases[] = $uaeEmirateCodes[strtoupper($stateValue)];
            }

            $zoneQuery = Zone::query();
            if ($destination) {
                $zoneQuery->where('destination_id', $destination->id);
            }
            $zone = $zoneQuery->where(function ($query) use ($stateAliases) {
                foreach ($stateAliases as $alias) {
                    $query->orWhereRaw('UPPER(zone_code) = ?', [strtoupper($alias)])
                        ->orWhereRaw('UPPER(zone_name) = ?', [strtoupper($alias)]);
                }
            })->first();
        }

        if (empty($zone) && ! empty($consigneeZipCode)) {
            $zipNorm = strtoupper(preg_replace('/\s+/', '', trim($consigneeZipCode)));
            if ($zipNorm !== '') {
                $exactZipQuery = Zone::query();
                if ($destination) {
                    $exactZipQuery->where('destination_id', $destination->id);
                }
                $zone = $exactZipQuery
                    ->whereRaw("UPPER(REPLACE(zone_code, ' ', '')) = ?", [$zipNorm])
                    ->first();

                if (empty($zone)) {
                    $prefixZipQuery = Zone::where('zone_category', 'zipcode');
                    if ($destination) {
                        $prefixZipQuery->where('destination_id', $destination->id);
                    }
                    $zone = $prefixZipQuery
                        ->whereRaw("? LIKE CONCAT(UPPER(REPLACE(zone_code, ' ', '')), '%')", [$zipNorm])
                        ->orderByRaw("LENGTH(REPLACE(zone_code, ' ', '')) DESC")
                        ->first();
                }
            }
        }

        $zoneNumber = $zone?->zone_number_testing;
        $zoneName = $zone?->zone_name;
        $zoneCode = $zone?->zone_code;

        // 5. Get services
        // 5. Get services - ALL enabled services of the selected country
        // (same as the customer create-shipment page).
        $services = CourierService::whereIn('country', $countryAliases)
            ->where('status', 1)
            ->get();

        if (empty($services)) {
            return response()->json([
                'success' => false,
                'message' => 'Service not available.',
            ], 404);
        }

        // 6. Process rates - SINGLE LOOP (exporter rates first, default fallback)
        $allRates = [];
        $isMultiPackageCount = is_array($packageWeights) ? count($packageWeights) : 1;

        // Non-array package weights (missing input) behave like zero boxes,
        // same as before, but without foreach() warnings in the logs.
        if (! is_array($packageWeights)) {
            $packageWeights = [];
        }

        // PERF: bulk-fetch every candidate rate row for all services in ONE
        // query — the exporter's own rates plus the shared default rates
        // (same customer-first, default-fallback order as create-shipment) —
        // then match each box in memory.
        $serviceIds = $services->pluck('id')->map(fn ($id) => (int) $id)->all();
        $preloadedRates = empty($serviceIds)
            ? collect()
            : \DB::table('courier_rates as cr')
                ->join('courier_services as cs', 'cr.service_id', '=', 'cs.id')
                ->whereIn('cr.customer_id', $rateCustomerIds)
                ->whereIn('cr.service_id', $serviceIds)
                ->whereIn('cs.country', $countryAliases)
                ->where(function ($query) use ($zoneNumber) {
                    $query->where('cr.zone_no', $zoneNumber)
                        ->orWhereNull('cr.zone_no')
                        ->orWhere('cr.zone_no', 0);
                })
                ->select('cr.*')
                ->get();

        $ratesByService = [];
        $allSurchargeIds = [];
        foreach ($preloadedRates as $rateRow) {
            $ratesByService[(int) $rateRow->service_id][] = $rateRow;
            foreach ($this->normalizeSurchargeIds($rateRow->surcharge_id ?? null) as $surchargeId) {
                $allSurchargeIds[$surchargeId] = true;
            }
        }

        // PERF: load every surcharge referenced by the candidate rates ONCE
        // instead of sum()+get() per box per service.
        $surchargeMap = empty($allSurchargeIds)
            ? []
            : SurCharge::whereIn('id', array_keys($allSurchargeIds))->get()->keyBy('id')->all();

        foreach ($services as $key => $service) {
            // Preloaded rows for this service (bulk-fetched above — the
            // per-box matching below runs fully in memory, no SQL).
            $serviceRates = $ratesByService[(int) $service->id] ?? [];

            // ========== US: box-wise rate ==========
            // Same as create-shipment: multi-package US orders only price
            // United Ground Premium per box; other US services are priced
            // box-wise only for single-package orders.
            if ($rateCountry === 'US' && $isMultiPackageCount > 1 && strtolower($service->method ?? '') !== 'united ground premium') {
                continue;
            }
            if ($rateCountry === 'US') {
                $boxBreakdown = [];
                $combinedBase = 0;
                $combinedFuel = 0;
                $combinedGst = 0;
                $combinedSurcharge = 0;
                $surchargeList = [];
                $firstMatchedRate = null;
                $allBoxesMatched = true;
                $usedFallback = false;

                foreach ($packageWeights as $index => $pkgWt) {
                    $pkgWt = floatval($pkgWt);

                    // Exact weight-band match first, nearest configured band as
                    // fallback — so a service is never hidden just because the
                    // weight falls outside its configured bands.
                    [$boxRate, $boxFallback] = $this->matchPrepaidBoxRate($serviceRates, $zoneNumber, $pkgWt, $rateCustomerId);
                    if ($boxFallback) {
                        $usedFallback = true;
                    }

                    if (! empty($boxRate)) {
                        if ($firstMatchedRate === null) {
                            $firstMatchedRate = $boxRate;
                        }

                        $base = floatval($boxRate->price);
                        $fuel = floatval($boxRate->fuel_charge) > 0
                            ? floatval($boxRate->fuel_charge)
                            : ($base * floatval($boxRate->fuel_percentage) / 100);

                        $boxSurchargeIds = $this->normalizeSurchargeIds($boxRate->surcharge_id ?? null);
                        $boxSurcharge = 0.0;
                        foreach ($boxSurchargeIds as $surchargeId) {
                            if (isset($surchargeMap[$surchargeId])) {
                                $surcharge = $surchargeMap[$surchargeId];
                                $boxSurcharge += (float) $surcharge->price;
                                $surchargeList[$surcharge->id] = [
                                    'name' => $surcharge->name,
                                    'code' => $surcharge->code,
                                    'price' => (float) $surcharge->price,
                                ];
                            }
                        }

                        $boxBreakdown[] = [
                            'box' => $index + 1,
                            'weight' => $pkgWt,
                            'base' => $base,
                            'fuel' => $fuel,
                            'surcharge' => $boxSurcharge,
                            'total' => $base + $fuel + $boxSurcharge,
                        ];

                        $combinedBase += $base;
                        $combinedFuel += $fuel;
                        $combinedSurcharge += $boxSurcharge;
                    }
                }

                $gstPctForTotal = $firstMatchedRate ? (float) $firstMatchedRate->gst_percentage : 0;
                $fixedGst = $firstMatchedRate ? (float) $firstMatchedRate->gst_amount : 0;
                $combinedGst = $fixedGst > 0
                    ? $fixedGst
                    : (($combinedBase + $combinedFuel + $combinedSurcharge) * $gstPctForTotal / 100);

                $allRates[] = [
                    'rate_id' => $firstMatchedRate ? $firstMatchedRate->id : null,
                    'service_id' => $service->id,
                    'method' => $service->method,
                    'method_display' => $service->method.' '.$service->tat,
                    'network' => $service->network,
                    'method_code' => $service->method_code,
                    'tat' => $service->tat,
                    'delivery_days' => $service->tat,
                    'scode' => $service->scode,
                    'consigneeState' => $consigneeState,
                    'zone_no' => $zoneNumber,
                    'zone_name' => $zoneName ?? null,
                    'pkg_wt' => $pkgWt,
                    'price' => $combinedBase,
                    'fuel_charge' => $combinedFuel,
                    'fuel_percentage' => 0,
                    'gst_percentage' => $gstPctForTotal,
                    'gst_amount' => $combinedGst,
                    'surcharge_total' => $combinedSurcharge,
                    'surcharges' => array_values($surchargeList),
                    'total_base_price' => $combinedBase,
                    'total_fuel_price' => $combinedFuel,
                    'total_surcharge' => $combinedSurcharge,
                    'is_multi_package' => true,
                    'is_fallback' => $usedFallback ?? false,
                    'box_breakdown' => $boxBreakdown,
                ];
            }

            if ($rateCountry === 'UK') {
                $boxBreakdown = [];
                $combinedBase = 0;
                $combinedFuel = 0;
                $combinedGst = 0;
                $combinedSurcharge = 0;
                $surchargeList = [];
                $firstMatchedRate = null;
                $allBoxesMatched = true;
                $usedFallback = false;

                foreach ($packageWeights as $index => $pkgWt) {
                    $pkgWt = floatval($pkgWt);

                    // Exact weight-band match first, nearest configured band as
                    // fallback — so a service is never hidden just because the
                    // weight falls outside its configured bands.
                    [$boxRate, $boxFallback] = $this->matchPrepaidBoxRate($serviceRates, $zoneNumber, $pkgWt, $rateCustomerId);
                    if ($boxFallback) {
                        $usedFallback = true;
                    }

                    if (! empty($boxRate)) {
                        if ($firstMatchedRate === null) {
                            $firstMatchedRate = $boxRate;
                        }

                        $base = floatval($boxRate->price);
                        $fuel = floatval($boxRate->fuel_charge) > 0
                            ? floatval($boxRate->fuel_charge)
                            : ($base * floatval($boxRate->fuel_percentage) / 100);

                        $boxSurchargeIds = $this->normalizeSurchargeIds($boxRate->surcharge_id ?? null);
                        $boxSurcharge = 0.0;
                        foreach ($boxSurchargeIds as $surchargeId) {
                            if (isset($surchargeMap[$surchargeId])) {
                                $surcharge = $surchargeMap[$surchargeId];
                                $boxSurcharge += (float) $surcharge->price;
                                $surchargeList[$surcharge->id] = [
                                    'name' => $surcharge->name,
                                    'code' => $surcharge->code,
                                    'price' => (float) $surcharge->price,
                                ];
                            }
                        }

                        $boxBreakdown[] = [
                            'box' => $index + 1,
                            'weight' => $pkgWt,
                            'base' => $base,
                            'fuel' => $fuel,
                            'surcharge' => $boxSurcharge,
                            'total' => $base + $fuel + $boxSurcharge,
                        ];

                        $combinedBase += $base;
                        $combinedFuel += $fuel;
                        $combinedSurcharge += $boxSurcharge;
                    }
                }

                $gstPctForTotal = $firstMatchedRate ? (float) $firstMatchedRate->gst_percentage : 0;
                $fixedGst = $firstMatchedRate ? (float) $firstMatchedRate->gst_amount : 0;
                $combinedGst = $fixedGst > 0
                    ? $fixedGst
                    : (($combinedBase + $combinedFuel + $combinedSurcharge) * $gstPctForTotal / 100);

                $allRates[] = [
                    'rate_id' => $firstMatchedRate ? $firstMatchedRate->id : null,
                    'service_id' => $service->id,
                    'method' => $service->method,
                    'method_display' => $service->method.' '.$service->tat,
                    'network' => $service->network,
                    'method_code' => $service->method_code,
                    'tat' => $service->tat,
                    'delivery_days' => $service->tat,
                    'scode' => $service->scode,
                    'consigneeState' => $consigneeState,
                    'zone_no' => $zoneNumber,
                    'pkg_wt' => $pkgWt,
                    'price' => $combinedBase,
                    'fuel_charge' => $combinedFuel,
                    'fuel_percentage' => 0,
                    'gst_percentage' => $gstPctForTotal,
                    'gst_amount' => $combinedGst,
                    'surcharge_total' => $combinedSurcharge,
                    'surcharges' => array_values($surchargeList),
                    'total_base_price' => $combinedBase,
                    'total_fuel_price' => $combinedFuel,
                    'total_surcharge' => $combinedSurcharge,
                    'is_multi_package' => true,
                    'is_fallback' => $usedFallback ?? false,
                    'box_breakdown' => $boxBreakdown,
                ];
            }

            // Australia uses the same box-wise rate calculation as Canada
            // (both are zipcode-category destinations with zone_no-based
            // rates). The query below is fully parameterized by
            // $destinationCountry, so adding 'AUS' here makes the
            // ARAMEX GPX ALL IN service rates resolve correctly.
            if ($rateCountry === 'CA' || $rateCountry === 'AUS' || $rateCountry === 'NZ' || $rateCountry === 'UAE' || $rateCountry === 'SG' || $rateCountry === 'MY' || $rateCountry === 'DE' || $rateCountry === 'BD' || $rateCountry === 'ZW') {
                $boxBreakdown = [];
                $combinedBase = 0;
                $combinedFuel = 0;
                $combinedGst = 0;
                $combinedSurcharge = 0;
                $surchargeList = [];
                $firstMatchedRate = null;
                $allBoxesMatched = true;
                $usedFallback = false;

                foreach ($packageWeights as $index => $pkgWt) {
                    $pkgWt = floatval($pkgWt);

                    // Exact weight-band match first, nearest configured band as
                    // fallback — so a service is never hidden just because the
                    // weight falls outside its configured bands.
                    [$boxRate, $boxFallback] = $this->matchPrepaidBoxRate($serviceRates, $zoneNumber, $pkgWt, $rateCustomerId);
                    if ($boxFallback) {
                        $usedFallback = true;
                    }

                    if (! empty($boxRate)) {
                        if ($firstMatchedRate === null) {
                            $firstMatchedRate = $boxRate;
                        }

                        $base = floatval($boxRate->price);
                        $fuel = floatval($boxRate->fuel_charge) > 0
                            ? floatval($boxRate->fuel_charge)
                            : ($base * floatval($boxRate->fuel_percentage) / 100);

                        $boxSurchargeIds = $this->normalizeSurchargeIds($boxRate->surcharge_id ?? null);
                        $boxSurcharge = 0.0;
                        foreach ($boxSurchargeIds as $surchargeId) {
                            if (isset($surchargeMap[$surchargeId])) {
                                $surcharge = $surchargeMap[$surchargeId];
                                $boxSurcharge += (float) $surcharge->price;
                                $surchargeList[$surcharge->id] = [
                                    'name' => $surcharge->name,
                                    'code' => $surcharge->code,
                                    'price' => (float) $surcharge->price,
                                ];
                            }
                        }

                        $boxBreakdown[] = [
                            'box' => $index + 1,
                            'weight' => $pkgWt,
                            'base' => $base,
                            'fuel' => $fuel,
                            'surcharge' => $boxSurcharge,
                            'total' => $base + $fuel + $boxSurcharge,
                        ];

                        $combinedBase += $base;
                        $combinedFuel += $fuel;
                        $combinedSurcharge += $boxSurcharge;
                    }
                }

                $gstPctForTotal = $firstMatchedRate ? (float) $firstMatchedRate->gst_percentage : 0;
                $fixedGst = $firstMatchedRate ? (float) $firstMatchedRate->gst_amount : 0;
                $combinedGst = $fixedGst > 0
                    ? $fixedGst
                    : (($combinedBase + $combinedFuel + $combinedSurcharge) * $gstPctForTotal / 100);

                $allRates[] = [
                    'rate_id' => $firstMatchedRate ? $firstMatchedRate->id : null,
                    'service_id' => $service->id,
                    'method' => $service->method,
                    'method_display' => $service->method.' '.$service->tat,
                    'network' => $service->network,
                    'method_code' => $service->method_code,
                    'tat' => $service->tat,
                    'delivery_days' => $service->tat,
                    'scode' => $service->scode,
                    'consigneeState' => $consigneeState,
                    'zone_no' => $zoneNumber,
                    'pkg_wt' => $pkgWt,
                    'price' => $combinedBase,
                    'fuel_charge' => $combinedFuel,
                    'fuel_percentage' => 0,
                    'gst_percentage' => $gstPctForTotal,
                    'gst_amount' => $combinedGst,
                    'surcharge_total' => $combinedSurcharge,
                    'surcharges' => array_values($surchargeList),
                    'total_base_price' => $combinedBase,
                    'total_fuel_price' => $combinedFuel,
                    'total_surcharge' => $combinedSurcharge,
                    'is_multi_package' => true,
                    'is_fallback' => $usedFallback ?? false,
                    'box_breakdown' => $boxBreakdown,
                ];
            }

        }// end foreach

        // Filter out rate cards whose total price (base + fuel + gst) is 0.
        // When no rate row matches the weight/zone the combined amounts stay
        // 0 and the card would otherwise show "₹0.00" — hidden from the rate
        // list, exactly like the customer create-shipment page.
        $allRates = array_values(array_filter($allRates, function ($r) {
            $base = floatval($r['price'] ?? 0);
            $fuel = floatval($r['fuel_charge'] ?? 0);
            $gst = floatval($r['gst_amount'] ?? 0);

            return ($base + $fuel + $gst) > 0;
        }));

        // Whether any priced card used the exporter's own (custom) rates.
        $rateCustomerById = [];
        foreach ($preloadedRates as $rateRow) {
            $rateCustomerById[(int) $rateRow->id] = (int) ($rateRow->customer_id ?? 0);
        }
        $usedCustomRates = false;
        foreach ($allRates as $rated) {
            if (! empty($rated['rate_id']) && ($rateCustomerById[(int) $rated['rate_id']] ?? 0) !== 0) {
                $usedCustomRates = true;
                break;
            }
        }

        // Attach consistent selected-zone metadata to every card.
        $allRates = array_map(function ($rate) use ($zoneNumber, $zoneName, $zoneCode) {
            $rate['zone_no'] = $rate['zone_no'] ?? $zoneNumber;
            $rate['zone_name'] = $zoneName;
            $rate['zone_code'] = $zoneCode;

            return $rate;
        }, $allRates);

        // Attach surcharge breakdown to every rate card.
        // PERF: rate models are preloaded in ONE query (no CourierRate::find
        // per card) and surcharges come from the preloaded $surchargeMap
        // (no per-card queries). Math mirrors CourierRate::surcharge_amount
        // / surchargeModels() exactly.
        $neededRateIds = [];
        foreach ($allRates as $ratedCard) {
            if (! empty($ratedCard['rate_id'])) {
                $neededRateIds[(int) $ratedCard['rate_id']] = true;
            }
        }
        $rateModelsById = empty($neededRateIds)
            ? []
            : CourierRate::whereIn('id', array_keys($neededRateIds))->get()->keyBy('id')->all();

        $allRates = array_map(function ($rate) use ($rateModelsById, $surchargeMap) {
            $surcharges = $rate['surcharges'] ?? collect();
            $surchargeTotal = (float) ($rate['surcharge_total'] ?? 0);
            $cr = ! empty($rate['rate_id']) ? ($rateModelsById[(int) $rate['rate_id']] ?? null) : null;
            if ($cr && $surchargeTotal <= 0) {
                // Same ids the surcharge_amount accessor / surchargeModels()
                // would resolve (Eloquent 'array' cast aware).
                $castIds = $cr->surcharge_id;
                $modelSurchargeIds = is_array($castIds)
                    ? array_values(array_filter(array_map('intval', $castIds)))
                    : [];
                $surchargeTotal = 0.0;
                $surcharges = [];
                foreach ($modelSurchargeIds as $modelSurchargeId) {
                    if (isset($surchargeMap[$modelSurchargeId])) {
                        $surchargeModel = $surchargeMap[$modelSurchargeId];
                        $surchargeTotal += (float) $surchargeModel->price;
                        $surcharges[] = [
                            'name' => $surchargeModel->name,
                            'code' => $surchargeModel->code,
                            'price' => (float) $surchargeModel->price,
                        ];
                    }
                }
                $surchargeTotal = round($surchargeTotal, 2);
                if ($surchargeTotal > 0 && (float) $cr->gst_amount <= 0 && (float) $cr->gst_percentage > 0) {
                    $rate['gst_amount'] = ((float) ($rate['gst_amount'] ?? 0))
                        + ($surchargeTotal * (float) $cr->gst_percentage / 100);
                }
            }
            $rate['surcharges'] = $surcharges;
            $rate['surcharge_total'] = round($surchargeTotal, 2);

            return $rate;
        }, $allRates);

        // 7. Build response (admin blade reads the top-level `zone` key).
        $response = [
            'success' => true,

            'customer_exists' => $usedCustomRates,
            'customer_name' => $rateCustomerName,
            'zone' => $zone ? [
                'zone_id' => $zone->id,
                'zone_number' => $zone->zone_number_testing,
                'zone_name' => $zone->zone_name,
                'zone_code' => $zone->zone_code,
                'state' => $consigneeState,
            ] : [
                'state' => $consigneeState,
                'message' => 'No zone found for the selected state',
            ],
            'all_rates' => $allRates,
        ];

        return response()->json($response);
    }

    /**
     * Return the zones for a destination for the admin Prepaid create-order page.
     *
     * Verbatim copy of CustomerController::getZonesByDestination() minus the
     * customer guard (the admin route is already protected by the admin
     * middleware).
     */
    public function prepaidZonesByDestination(Request $request)
    {
        $destinationId = $request->query('destination_id');

        if (! $destinationId || ! ctype_digit((string) $destinationId)) {
            return response()->json([
                'exists' => false,
                'category' => null,
                'destination' => null,
                'zones' => [],
            ], 200);
        }

        $destination = Destination::find((int) $destinationId);

        if (! $destination) {
            return response()->json([
                'exists' => false,
                'category' => null,
                'destination' => null,
                'zones' => [],
            ], 200);
        }

        // The same zone (name + code) is stored once per service, so the raw
        // query returns duplicates. Deduplicate here so the state dropdown /
        // zipcode suggestions do not show the same value twice.
        $zones = Zone::where('destination_id', $destination->id)
            ->orderBy('zone_name')
            ->get()
            ->unique(function ($zone) {
                return strtolower(trim((string) $zone->zone_code)) . '|' . strtolower(trim((string) $zone->zone_name));
            })
            ->values();

        if ($zones->isEmpty()) {
            return response()->json([
                'exists' => false,
                'category' => null,
                'destination' => [
                    'id' => $destination->id,
                    'name' => $destination->name,
                    'code' => $destination->code,
                    'country_code' => $destination->country_code,
                ],
                'zones' => [],
            ], 200);
        }

        // Determine the category from the first zone's zone_category value.
        // All zones for a destination are expected to share the same category.
        $category = $zones->first()->zone_category ?: 'state';

        return response()->json([
            'exists' => true,
            'category' => $category,
            'destination' => [
                'id' => $destination->id,
                'name' => $destination->name,
                'code' => $destination->code,
                'country_code' => $destination->country_code,
            ],
            'zones' => $zones->map(function ($z) {
                return [
                    'zone_code' => $z->zone_code,
                    'zone_name' => $z->zone_name,
                ];
            })->values(),
        ], 200);
    }

    /**
     * Return the country-wise courier services for a destination for the
     * admin Prepaid create-order page — WITHOUT any weight filtering.
     *
     * The frontend calls this as soon as the Delivery Destination is
     * selected, so the admin sees which services exist for that country
     * (e.g. US shows only the UPS services) before entering weights or
     * clicking Calculate Rate. Same UPS-only rule as prepaidUpsRate().
     */
    public function prepaidServicesByDestination(Request $request)
    {
        $destinationId = $request->query('destination_id', $request->query('delivery_destination'));

        $destination = null;
        if ($destinationId && ctype_digit((string) $destinationId)) {
            $destination = Destination::find((int) $destinationId);
        } elseif ($destinationId) {
            $destinationValue = trim((string) $destinationId);
            $destination = Destination::where(function ($query) use ($destinationValue) {
                $query->whereRaw('UPPER(name) = ?', [strtoupper($destinationValue)])
                    ->orWhereRaw('UPPER(code) = ?', [strtoupper($destinationValue)]);
            })->first();
        }

        if (! $destination) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a valid delivery destination.',
                'destination' => null,
                'destination_country' => null,
                'services' => [],
            ], 200);
        }

        // Resolve via the human-readable destination NAME first (same path
        // prepaidUpsRate() uses), so the country matches courier_services.country
        // (e.g. 'US', not 'USA'). Numeric-id lookup is only a fallback.
        $destinationCountry = $this->resolveDestinationCountry($destination->name)
            ?? $this->resolveDestinationCountry($destination->id);

        // Country-wise matching (codes + names, e.g. 'DE' and 'Germany'):
        // all enabled services of this destination are listed (same as
        // the customer create-shipment page).
        $countryAliases = $this->prepaidCountryAliases($destinationCountry, $destination);

        $services = CourierService::whereIn('country', $countryAliases)
            ->where('status', 1)
            ->orderBy('method')
            ->get(['id', 'country', 'method', 'network', 'tat', 'method_code', 'service_code']);

        return response()->json([
            'success' => true,
            'destination' => [
                'id' => $destination->id,
                'name' => $destination->name,
                'code' => $destination->code,
                'country_code' => $destination->country_code,
            ],
            'destination_country' => $destinationCountry,
            'services' => $services->map(function ($s) {
                return [
                    'service_id' => $s->id,
                    'method' => $s->method,
                    'network' => $s->network,
                    'tat' => $s->tat,
                    'method_code' => $s->method_code,
                    'service_code' => $s->service_code,
                ];
            })->values(),
        ], 200);
    }

    // ====================================================================
    // UPS Ship API helpers (ported from CustomerController so the COD flow
    // can call the selected service's carrier API at creation time).
    // ====================================================================

    /**
     * Obtain a cached UPS OAuth token for the Ship API.
     */
    private function getUpsShipAccessToken()
    {
        $cacheKey = 'ups_ship_access_token';
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $clientId = 'GSTEuQdy5XHnWalxGQECH4yhSqJAiydVNjho6AkPGn1ZwMYX';
        $clientSecret = 'fVuQ8CMYIzxpABWkZFcOM3AyW0x4i1zo7mwiZk7gyLjpD1IWawoCXa3OXWNfVjao';
        $tokenUrl = 'https://onlinetools.ups.com/security/v1/oauth/token';

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post($tokenUrl, ['grant_type' => 'client_credentials']);

        if (! $response->successful()) {
            Log::error('UPS Ship token error: '.$response->body());

            throw new \Exception('Unable to retrieve UPS Ship access token');
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            Log::error('UPS Ship token missing access_token: '.$response->body());

            throw new \Exception('UPS Ship access token not found in response');
        }

        $expiresIn = isset($data['expires_in']) ? (int) $data['expires_in'] : 3600;
        $ttl = max(60, $expiresIn - 60);
        Cache::put($cacheKey, $data['access_token'], $ttl);

        return $data['access_token'];
    }

    /**
     * Build the UPS ShipRequest payload from DB records for a COD shipper.
     * Reads ShipperInfo, ConsigneeInfo and PackageDimension from the DB
     * (already created inside the transaction) instead of the form data.
     */
    private function buildUpsShipPayloadFromDb($shipper)
    {
        $consignee = $shipper->consigneeInfo;
        if (! $consignee) {
            Log::warning('buildUpsShipPayloadFromDb: No consignee found for shipper #'.$shipper->id);

            return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
        }

        // Resolve the shipping method and the courier service.
        $shippingMethod = $shipper->shipping_method;
        if (! $shippingMethod) {
            $createShipmentMethod = CreateShipment::where('shipper_id', $shipper->id)->value('shipping_method');
            if ($createShipmentMethod) {
                $shippingMethod = $createShipmentMethod;
            }
        }

        if (! $shippingMethod) {
            $pkgMethod = PackageDimension::where('shipper_id', $shipper->id)
                ->whereNotNull('shipping_method')
                ->value('shipping_method');
            if ($pkgMethod) {
                $shippingMethod = $pkgMethod;
            }
        }

        if (! $shippingMethod) {
            $defaultService = CourierService::orderBy('id', 'asc')->first();
            if ($defaultService) {
                $shippingMethod = $defaultService->method;
                Log::warning('buildUpsShipPayloadFromDb: ALL sources null for shipper #'.$shipper->id.' — defaulting to first CourierService: "'.$shippingMethod.'"');
            }
        }

        if (! $shippingMethod) {
            return ['success' => false, 'message' => 'Shipping method is not set for this shipment. Please edit the shipment and select a shipping method.'];
        }

        if (! $shipper->shipping_method) {
            $shipper->shipping_method = $shippingMethod;
            $shipper->save();
            Log::info('buildUpsShipPayloadFromDb: Persisted shipping_method to shipper_info #'.$shipper->id.' → "'.$shippingMethod.'"');
        }

        $service = $this->findCourierService($shippingMethod, $shipper->id);

        if (! $service) {
            return ['success' => false, 'message' => 'No matching courier service found for shipping method: "'.$shippingMethod.'".'];
        }

        $packagesData = [];
        foreach ($shipper->packageDimensions as $pkg) {
            $packagesData[] = [
                'actual_weight_kg' => $pkg->actual_weight_kg,
                'length_cm' => $pkg->length_cm,
                'width_cm' => $pkg->width_cm,
                'height_cm' => $pkg->height_cm,
            ];
        }

        $validatedData = [
            'consignee_name' => $consignee->consignee_name,
            'consignee_phone_number' => $consignee->phone_number,
            'consignee_address_line1' => $consignee->address_line1,
            'consignee_address_line2' => $consignee->address_line2,
            'consignee_address_line3' => $consignee->address_line3,
            'consignee_city' => $consignee->city,
            'consignee_state' => $consignee->state ?? '',
            'consignee_zip_code' => $consignee->zip_code,
            'delivery_destination' => $consignee->delivery_destination,
            'packages' => $packagesData,
        ];

        $upsPayload = $this->buildUpsShipPayload($validatedData, $service);

        return ['success' => true, 'payload' => $upsPayload];
    }

    /**
     * Build UPS Ship API payload from validated data and service info.
     */
    private function buildUpsShipPayload($validatedData, $service)
    {
        $shipperName = 'SANDEEP KAPUR';
        $shipperAttentionName = 'United';
        $shipperCompanyDisplayableName = 'UWC';
        $shipperPhone = '6466741258';

        $serviceWeight = $service->weight ?? 'LBS';
        $shipperNumber = 'X19700';

        $shipperAddressLine = '218 WEST 37 STREET 6TH FLOOR';
        $shipperCity = 'NEW YORK';
        $shipperState = 'NY';
        $shipperPostal = '10018';
        $shipperCountry = 'US';

        $consigneeName = $validatedData['consignee_name'];
        $consigneePhone = $validatedData['consignee_phone_number'];
        $consigneeAddressLines = [];
        if (! empty($validatedData['consignee_address_line1'])) {
            $consigneeAddressLines[] = $validatedData['consignee_address_line1'];
        }
        if (! empty($validatedData['consignee_address_line2'])) {
            $consigneeAddressLines[] = $validatedData['consignee_address_line2'];
        }
        if (! empty($validatedData['consignee_address_line3'])) {
            $consigneeAddressLines[] = $validatedData['consignee_address_line3'];
        }
        $consigneeCity = $validatedData['consignee_city'];
        $consigneeState = $validatedData['consignee_state'] ?? '';
        $consigneePostal = $validatedData['consignee_zip_code'];
        $destCountry = $this->getCountryCodeFromDestination($validatedData['delivery_destination']);

        $serviceCode = $service->scode;
        $serviceDescription = $this->getServiceDescriptionFromMethod($service->method);

        $weightUnit = 'LBS';

        if ($serviceWeight === 'OZS') {
            $serviceCode = $service->scode;
            $weightUnit = 'OZS';
        } elseif ($serviceWeight === 'OZS/LBS') {
            $maxWeightKg = 0;
            $preScanRows = $validatedData['packages'] ?? [];
            foreach ($preScanRows as $pkgData) {
                $w = floatval($pkgData['actual_weight_kg'] ?? 0);

                if ($w > $maxWeightKg) {
                    $maxWeightKg = $w;
                }
            }
            $maxWeightLbs = $maxWeightKg * 2.205;
            if ($maxWeightLbs > 0 && $maxWeightLbs < 1) {
                $serviceCode = '92';
                $weightUnit = 'OZS';
                $serviceDescription = 'Ground Saver Less than 1 lb';
            } else {
                $serviceCode = '93';
                $weightUnit = 'LBS';
                $serviceDescription = 'Ground Saver 1 lbs or grater';
            }
        }

        $packages = [];
        $packageRows = $validatedData['packages'] ?? [];
        foreach ($packageRows as $pkgData) {
            $weightKg = $pkgData['actual_weight_kg'] ?? null;
            if (! $weightKg || $weightKg <= 0) {
                continue;
            }

            $convertedWeight = $weightUnit === 'OZS'
                ? round($weightKg * 35.274, 2)
                : round($weightKg * 2.20462, 2);

            $pkg = [
                'Description' => 'Documents',
                'Packaging' => ['Code' => '02'],
                'ReferenceNumber' => [
                    [
                        'Code' => '9S',
                        'Value' => 'ORDER12345',
                    ],
                ],
                'PackageWeight' => [
                    'UnitOfMeasurement' => ['Code' => $weightUnit],
                    'Weight' => (string) $convertedWeight,
                ],
            ];

            $lengthCm = $pkgData['length_cm'] ?? null;
            $widthCm = $pkgData['width_cm'] ?? null;
            $heightCm = $pkgData['height_cm'] ?? null;

            if ($lengthCm && $widthCm && $heightCm) {
                $pkg['Dimensions'] = [
                    'UnitOfMeasurement' => ['Code' => 'IN'],
                    'Length' => (string) $lengthCm,
                    'Width' => (string) $widthCm,
                    'Height' => (string) $heightCm,
                ];
            } else {
                $pkg['Dimensions'] = [
                    'UnitOfMeasurement' => ['Code' => 'IN'],
                    'Length' => '10',
                    'Width' => '8',
                    'Height' => '4',
                ];
            }

            $packages[] = $pkg;
        }

        if (empty($packages)) {
            $fallbackWeight = $weightUnit === 'OZS' ? '176.37' : '11.02';
            $packages[] = [
                'Description' => 'Documents',
                'ReferenceNumber' => [
                    [
                        'Code' => '9S',
                        'Value' => 'ORDER12345',
                    ],
                ],
                'Packaging' => ['Code' => '02'],
                'PackageWeight' => [
                    'UnitOfMeasurement' => ['Code' => $weightUnit],
                    'Weight' => $fallbackWeight,
                ],
                'Dimensions' => [
                    'UnitOfMeasurement' => ['Code' => 'IN'],
                    'Length' => '10',
                    'Width' => '8',
                    'Height' => '4',
                ],
            ];
        }

        $payload = [
            'ShipmentRequest' => [
                'Request' => [
                    'RequestOption' => 'validate',
                    'TransactionReference' => [
                        'CustomerContext' => 'ORDER-12345',
                    ],
                ],
                'Shipment' => [
                    'Shipper' => [
                        'Name' => $shipperName,
                        'AttentionName' => $shipperAttentionName,
                        'CompanyDisplayableName' => $shipperCompanyDisplayableName,
                        'Phone' => ['Number' => $shipperPhone],
                        'ShipperNumber' => $shipperNumber,
                        'Address' => [
                            'AddressLine' => $shipperAddressLine,
                            'City' => $shipperCity,
                            'StateProvinceCode' => $shipperState,
                            'PostalCode' => $shipperPostal,
                            'CountryCode' => $shipperCountry,
                        ],
                    ],
                    'ShipFrom' => [
                        'Name' => $shipperName,
                        'AttentionName' => $shipperAttentionName,
                        'Phone' => ['Number' => $shipperPhone],
                        'Address' => [
                            'AddressLine' => [$shipperAddressLine],
                            'City' => $shipperCity,
                            'StateProvinceCode' => $shipperState,
                            'PostalCode' => $shipperPostal,
                            'CountryCode' => $shipperCountry,
                        ],
                    ],
                    'ShipTo' => [
                        'Name' => $consigneeName,
                        'AttentionName' => $consigneeName,
                        'Phone' => ['Number' => $consigneePhone],
                        'Address' => [
                            'AddressLine' => ! empty($consigneeAddressLines) ? $consigneeAddressLines : ['Receiver Address'],
                            'City' => $consigneeCity,
                            'StateProvinceCode' => $consigneeState,
                            'PostalCode' => $consigneePostal,
                            'CountryCode' => $destCountry,
                        ],
                    ],
                    'PaymentInformation' => [
                        'ShipmentCharge' => [
                            'Type' => '01',
                            'BillShipper' => [
                                'AccountNumber' => $shipperNumber,
                            ],
                        ],
                    ],
                    'Service' => [
                        'Code' => $serviceCode,
                        'Description' => $serviceDescription,
                    ],
                    'Package' => $packages,
                ],
                'LabelSpecification' => [
                    'LabelImageFormat' => ['Code' => 'PDF'],
                ],
            ],
        ];

        return $payload;
    }

    /**
     * Log one side (request or response) of an external vendor API call made
     * for a prepaid order. Base64 label blobs (GraphicImage / pdf_base64 /
     * LabelImage) are redacted to their byte size and oversized payloads are
     * truncated, so the log stays readable. Find entries in
     * storage/logs/laravel.log by searching for "PREPAID-API".
     */
    private function logPrepaidApiCall(string $api, $request, $response, array $meta = []): void
    {
        $redact = function ($value) use (&$redact) {
            if (is_array($value)) {
                $out = [];
                foreach ($value as $key => $item) {
                    if (is_string($key) && preg_match('/graphicimage|pdf_base64|labelimage/i', (string) $key) && is_string($item)) {
                        $out[$key] = '[BASE64 len=' . strlen($item) . ']';
                    } else {
                        $out[$key] = $redact($item);
                    }
                }

                return $out;
            }
            if (is_string($value) && strlen($value) > 2000) {
                return substr($value, 0, 2000) . '...[TRUNCATED total=' . strlen($value) . ']';
            }

            return $value;
        };

        $context = $meta;
        if ($request !== null) {
            $context['request'] = $redact($request);
        }
        if ($response !== null) {
            $context['response'] = $redact($response);
        }

        $json = json_encode($context);
        if ($json !== false && strlen($json) > 20000) {
            $context['response'] = '[TRUNCATED total=' . strlen($json) . ']';
        }

        Log::info('PREPAID-API ' . $api, $context);
    }

    /**
     * Call the UPS Ship API directly (internal method).
     * Returns ['success' => bool, 'shipmentResponse' => array] or
     * ['success' => false, 'message' => string, 'rawResponse' => mixed].
     */
    private function callUpsShipApiInternal($payload)
    {
        try {
            $this->logPrepaidApiCall('ups-ship', $payload, null, ['stage' => 'request']);

            try {
                $token = $this->getUpsShipAccessToken();
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Failed to obtain UPS Ship access token: '.$e->getMessage(),
                ];
            }

            $ch = curl_init('https://onlinetools.ups.com/api/shipments/v2403/ship');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Bearer '.$token,
                    'transId: '.uniqid('ship_', true),
                    'transactionSrc: unitedcourier',
                ],
                CURLOPT_TIMEOUT => 60,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                Log::error('UPS Ship cURL error (internal): '.$curlError);

                return [
                    'success' => false,
                    'message' => 'UPS Ship API connection error: '.$curlError,
                ];
            }

            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('UPS Ship returned non-JSON response. HTTP: '.$httpCode.' Body: '.$response);

                return [
                    'success' => false,
                    'message' => 'UPS Ship returned non-JSON response',
                    'rawResponse' => $response,
                ];
            }

            Log::info('UPS Ship response HTTP: '.$httpCode.' Body: '.substr($response, 0, 2000));
            $this->logPrepaidApiCall('ups-ship', null, $decoded ?? $response, ['stage' => 'response', 'http_code' => $httpCode]);

            if ($httpCode >= 200 && $httpCode < 300 && isset($decoded['ShipmentResponse'])) {
                return [
                    'success' => true,
                    'shipmentResponse' => $decoded['ShipmentResponse'],
                ];
            }

            $errorMessage = 'Failed to create UPS shipment';
            if (isset($decoded['response']['errors'][0]['message'])) {
                $errorMessage = $decoded['response']['errors'][0]['message'];
            } elseif (isset($decoded['ShipmentResponse']['Response']['Error'][0]['ErrorDescription'])) {
                $errorMessage = $decoded['ShipmentResponse']['Response']['Error'][0]['ErrorDescription'];
            } elseif (isset($decoded['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['ErrorDescription'])) {
                $errorMessage = $decoded['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['ErrorDescription'];
            }

            return [
                'success' => false,
                'message' => $errorMessage,
                'rawResponse' => $decoded,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Server error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Map delivery_destination text to ISO country code.
     */
    private function getCountryCodeFromDestination($dest)
    {
        $map = [
            'US- United State of America' => 'US',
            'India' => 'IN',
            'UK - United Kingdom' => 'GB',
            'China' => 'CN',
            'Russia' => 'RU',
            'Srilanka' => 'LK',
        ];

        return $map[$dest] ?? 'US';
    }

    /**
     * Map courier service method name to UPS service description.
     */
    private function getServiceDescriptionFromMethod($method)
    {
        $methodUpper = strtoupper($method);
        $descMap = [
            'UNITED MY DELIVERY' => 'Ground',
            'UNITED AIR PREMIUM' => 'Next Day Air',
            'UNITED GRD PREMIUM' => '2nd Day Air',
            'UNITED AIR EXPRESS' => 'Worldwide Express',
            'UNITED PRIOR POST' => 'Standard',
            'UNITED ECO POST' => 'Saver',
            'UNITED MY PICKUP' => 'Ground',
            'DDP AIREXPRESS' => 'Worldwide Express',
            'DDU AIREXPRESS' => 'Worldwide Express',
        ];

        foreach ($descMap as $key => $desc) {
            if (str_contains($methodUpper, $key)) {
                return $desc;
            }
        }

        return 'Ground';
    }

    /**
     * Multi-tier CourierService lookup from a shipping method string.
     */
    private function findCourierService($shippingMethod, $shipperId)
    {
        // Tier 1: Exact match
        $service = CourierService::where('method', $shippingMethod)->first();
        if ($service) {
            return $service;
        }

        // Tier 2: Case-insensitive exact match
        $service = CourierService::whereRaw('LOWER(method) = ?', [strtolower($shippingMethod)])->first();
        if ($service) {
            return $service;
        }

        $methodUpper = strtoupper($shippingMethod);
        $allServices = CourierService::all();

        // Tier 3: str_contains partial match (both directions)
        foreach ($allServices as $svc) {
            $svcUpper = strtoupper($svc->method);
            if (str_contains($svcUpper, $methodUpper) || str_contains($methodUpper, $svcUpper)) {
                return $svc;
            }
        }

        // Tier 4: Word-by-word normalized match with abbreviation detection.
        $formWords = preg_split('/\s+/', preg_replace('/[^A-Za-z0-9\s]/', '', $methodUpper));
        $formWords = array_values(array_filter($formWords, function ($w) {
            return strlen($w) > 0;
        }));

        foreach ($allServices as $svc) {
            $svcUpper = strtoupper($svc->method);
            $svcWords = preg_split('/\s+/', preg_replace('/[^A-Za-z0-9\s]/', '', $svcUpper));
            $svcWords = array_values(array_filter($svcWords, function ($w) {
                return strlen($w) > 0;
            }));

            if (empty($formWords) || empty($svcWords)) {
                continue;
            }

            $matchedCount = 0;
            $unmatchedLongWords = 0;
            foreach ($formWords as $fw) {
                $found = false;
                foreach ($svcWords as $sw) {
                    if ($fw === $sw || str_contains($fw, $sw) || str_contains($sw, $fw)) {
                        $found = true;
                        break;
                    }
                    $shorter = strlen($fw) <= strlen($sw) ? $fw : $sw;
                    $longer = strlen($fw) > strlen($sw) ? $fw : $sw;
                    if (strlen($shorter) >= 2 && strlen($shorter) <= 3 && strlen($longer) >= 4) {
                        if ($this->isAbbreviationOf($shorter, $longer)) {
                            $found = true;
                            break;
                        }
                    }
                }
                if ($found) {
                    $matchedCount++;
                } elseif (strlen($fw) >= 4) {
                    $unmatchedLongWords++;
                }
            }

            $totalWords = count($formWords);
            if ($unmatchedLongWords === 0 && $matchedCount > 0 && ($matchedCount / $totalWords) >= 0.5) {
                return $svc;
            }
        }

        // Tier 5: Collapsed-string match (remove all spaces and non-alphanumeric)
        $collapsedForm = preg_replace('/[^A-Za-z0-9]/', '', $methodUpper);
        foreach ($allServices as $svc) {
            $svcUpper = strtoupper($svc->method);
            $collapsedSvc = preg_replace('/[^A-Za-z0-9]/', '', $svcUpper);
            if (str_contains($collapsedForm, $collapsedSvc) || str_contains($collapsedSvc, $collapsedForm)) {
                return $svc;
            }
        }

        $availableMethods = CourierService::pluck('method')->toArray();
        Log::warning('findCourierService: No match for "'.$shippingMethod.'" (shipper #'.$shipperId.'). Available methods: '.implode(', ', $availableMethods));

        return null;
    }

    /**
     * Check if a short string (2-3 chars) is an abbreviation of a longer string.
     */
    private function isAbbreviationOf($short, $long)
    {
        $shortLen = strlen($short);
        $longLen = strlen($long);
        if ($shortLen > $longLen) {
            return false;
        }

        $si = 0;
        for ($li = 0; $li < $longLen && $si < $shortLen; $li++) {
            if ($short[$si] === $long[$li]) {
                $si++;
            }
        }

        return $si === $shortLen;
    }

    // ====================================================================
    // Prepaid manifest APIs (mirrors the customer manifest APIs in
    // CustomerController so admin prepaid orders support the same pack /
    // manifest / label / document / close / pickup flows).
    // Differences: admin guard (no customer login), prepaid shippers only
    // (shipment_type = 5), audit entries performed by "admin", and wallet
    // operations use the shipper's exporter customer when one exists.
    // ====================================================================

    public function prepaidMarkPacked(Request $request)
    {
        if (! auth()->guard('admin')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $adminId = (int) auth()->guard('admin')->id();

        $validator = Validator::make($request->all(), [
            'shipper_id' => ['required', 'integer'],
            'custom_label' => ['required', 'string', 'max:1000000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        if (! ShipperInfo::whereKey($validated['shipper_id'])->where('shipment_type', 5)->exists()) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $labelPath = null;
        $labelUrl = null;
        $packedShipper = null;
        $failureStage = 'database_transaction';

        try {
            $packedShipper = DB::transaction(function () use (
                $validated,
                &$labelPath,
                &$labelUrl,
                &$failureStage
            ) {
                $shipper = ShipperInfo::whereKey($validated['shipper_id'])
                    ->where('shipment_type', 5)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($shipper->status !== 'ready') {
                    throw new \DomainException('Shipment is not in Ready status.');
                }

                $failureStage = 'pdf_storage';
                [$labelPath, $labelUrl] = $this->storeCustomLabelFile(
                    $shipper,
                    $validated['custom_label']
                );

                $failureStage = 'shipment_update';
                $shipper->custom_label = $labelUrl;
                $shipper->status = 'packed';
                $shipper->save();

                $failureStage = 'complete';

                return $shipper;
            });
        } catch (\DomainException $e) {
            $this->deleteCustomLabelFile($labelPath);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            $this->deleteCustomLabelFile($labelPath);
            $errorReference = (string) Str::uuid();

            Log::error('Prepaid custom label persistence failed.', [
                'error_reference' => $errorReference,
                'failure_stage' => $failureStage,
                'shipper_id' => $validated['shipper_id'],
                'admin_id' => $adminId,
                'public_file_path' => $labelPath,
                'public_directory' => public_path('uploads/custom_labels'),
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to save the custom label. The shipment remains ready. Reference: '.$errorReference,
                'error_reference' => $errorReference,
            ], 500);
        }

        $this->recordPrepaidPackedShipmentAudit($packedShipper, $adminId);

        return response()->json([
            'success' => true,
            'message' => 'Custom label PDF stored and status updated to Packed.',
            'custom_label_url' => $labelUrl,
        ]);
    }

    /** @return array{0: string, 1: string} */
    private function storeCustomLabelFile(ShipperInfo $shipper, string $labelHtml): array
    {
        $name = Str::slug((string) $shipper->awb_number) ?: 'shipment-'.$shipper->id;
        $timestamp = now('Asia/Kolkata')->format('Ymd-His');
        $filename = $name.'-'.$timestamp.'.pdf';
        $document = $this->buildCustomLabelDocument($shipper, $labelHtml);
        $publicDirectory = public_path('uploads/custom_labels');
        $publicPath = $publicDirectory.DIRECTORY_SEPARATOR.$filename;

        if (! is_dir($publicDirectory)
            && ! mkdir($publicDirectory, 0775, true)
            && ! is_dir($publicDirectory)) {
            throw new \RuntimeException('Unable to create the public custom label directory.');
        }

        if (! is_writable($publicDirectory)) {
            throw new \RuntimeException('The public custom label directory is not writable.');
        }

        $destination = null;
        $publicFileCreated = false;
        $stored = false;

        try {
            $pdfBytes = Pdf::loadHTML($document)->output();

            if (! is_string($pdfBytes) || $pdfBytes === '') {
                throw new \RuntimeException('The custom label PDF was not generated correctly.');
            }

            $expectedBytes = strlen($pdfBytes);
            $destination = fopen($publicPath, 'xb');
            if ($destination === false) {
                throw new \RuntimeException('Unable to create the custom label PDF in the public directory.');
            }

            $publicFileCreated = true;
            $bytesWritten = 0;
            while ($bytesWritten < $expectedBytes) {
                $written = fwrite($destination, substr($pdfBytes, $bytesWritten));

                if ($written === false || $written === 0) {
                    throw new \RuntimeException('Unable to write the custom label PDF to the public directory.');
                }

                $bytesWritten += $written;
            }

            if (! fflush($destination)) {
                throw new \RuntimeException('Unable to flush the custom label PDF to the public directory.');
            }

            fclose($destination);
            $destination = null;
            clearstatcache(true, $publicPath);
            $storedBytes = is_file($publicPath) ? filesize($publicPath) : false;

            if (! is_readable($publicPath) || $storedBytes !== $expectedBytes) {
                throw new \RuntimeException('The custom label PDF was not stored correctly in the public directory.');
            }

            $stored = true;

            return [$publicPath, asset('uploads/custom_labels/'.$filename)];
        } finally {
            if (is_resource($destination)) {
                fclose($destination);
            }

            if ($publicFileCreated && ! $stored && is_file($publicPath)) {
                @unlink($publicPath);
            }
        }
    }

    private function buildCustomLabelDocument(ShipperInfo $shipper, string $labelHtml): string
    {
        $awbNumber = htmlspecialchars((string) ($shipper->awb_number ?: $shipper->id), ENT_QUOTES, 'UTF-8');

        return '<!DOCTYPE html>'.PHP_EOL
            .'<html lang="en"><head><meta charset="UTF-8">'
            .'<meta name="viewport" content="width=device-width, initial-scale=1">'
            .'<title>Shipping Label '.$awbNumber.'</title>'
            .'<style>html,body{margin:0;padding:0;background:#fff;color:#000}'
            .'body{font-family:Arial,sans-serif}.custom-label-document{box-sizing:border-box;width:100%}'
            .'@media print{@page{margin:0}body{-webkit-print-color-adjust:exact;print-color-adjust:exact}}</style>'
            .'</head><body><main class="custom-label-document">'
            .$labelHtml
            .'</main></body></html>';
    }

    private function deleteCustomLabelFile(?string $path): void
    {
        if ($path === null) {
            return;
        }

        $directory = realpath(public_path('uploads/custom_labels'));
        $file = realpath($path);

        if ($directory === false || $file === false || ! is_file($file)) {
            return;
        }

        $directoryPrefix = rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (! str_starts_with($file, $directoryPrefix)) {
            Log::warning('Refused to remove a file outside the public custom label directory.', [
                'public_file_path' => $path,
            ]);

            return;
        }

        if (! @unlink($file)) {
            Log::warning('Unable to remove a custom label file.', [
                'public_file_path' => $file,
            ]);
        }
    }

    private function recordPrepaidPackedShipmentAudit(ShipperInfo $shipper, int $adminId): void
    {
        $customerId = (int) ($shipper->customer_id ?? 0);

        try {
            $shippingId = CreateShipment::where('shipper_id', $shipper->id)->value('id');

            Tracking::firstOrCreate(
                ['shipper_id' => $shipper->id, 'status' => 'packed'],
                [
                    'awb_number' => $shipper->awb_number,
                    'shipping_id' => $shippingId,
                    'uwc_id' => $shipper->awb_number,
                    'title' => Tracking::getTitleForStatus('packed'),
                ]
            );
        } catch (\Throwable $e) {
            $this->logPrepaidPackedShipmentAuditFailure('tracking_insert', $shipper, $adminId, $e);
        }

        try {
            ShipmentLog::firstOrCreate(
                ['shipper_id' => $shipper->id, 'status' => 'packed'],
                [
                    'customer_id' => $customerId,
                    'awb_number' => $shipper->awb_number,
                    'previous_status' => 'ready',
                    'title' => Tracking::getTitleForStatus('packed'),
                    'description' => 'Prepaid shipment marked as packed and custom label URL stored.',
                    'performed_by' => 'admin',
                    'created_at' => now(),
                ]
            );
        } catch (\Throwable $e) {
            $this->logPrepaidPackedShipmentAuditFailure('shipment_log_insert', $shipper, $adminId, $e);
        }
    }

    private function logPrepaidPackedShipmentAuditFailure(
        string $stage,
        ShipperInfo $shipper,
        int $adminId,
        \Throwable $exception
    ): void {
        Log::error('Prepaid packed shipment audit record failed after the shipment was saved.', [
            'failure_stage' => $stage,
            'shipper_id' => $shipper->id,
            'admin_id' => $adminId,
            'exception_class' => $exception::class,
            'exception_message' => $exception->getMessage(),
            'exception_file' => $exception->getFile(),
            'exception_line' => $exception->getLine(),
        ]);
    }

    /**
     * Fetch the shipping label (base64 graphic image) for a prepaid shipment
     * on demand. Restricted to prepaid shippers (shipment_type = 5).
     */
    public function prepaidGetShipmentLabel($invoiceId)
    {
        if (! auth()->guard('admin')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $invoice = ShipmentInvoice::where('id', $invoiceId)
            ->whereHas('shipperInfo', function ($q) {
                $q->where('shipment_type', 5);
            })
            ->with('shipperInfo.shipmentTracking')
            ->first();

        if (! $invoice || ! $invoice->shipperInfo || ! $invoice->shipperInfo->shipmentTracking) {
            return response()->json(['success' => false, 'message' => 'Label not available for this shipment.']);
        }

        $tracking = $invoice->shipperInfo->shipmentTracking;
        $pkgResults = $tracking->package_results;
        $firstPkg = is_array($pkgResults) && isset($pkgResults[0]) ? $pkgResults[0] : $pkgResults;

        $labelFormat = null;
        $graphicImage = null;
        if (isset($firstPkg['ShippingLabel'])) {
            $labelFormat = $firstPkg['ShippingLabel']['ImageFormat']['Code'] ?? 'GIF';
            $graphicImage = $firstPkg['ShippingLabel']['GraphicImage'] ?? null;
        } elseif (isset($firstPkg['LabelImage'])) {
            // Fallback for older/different UPS response format
            $labelFormat = $firstPkg['LabelImage']['LabelImageFormat']['Code'] ?? 'PDF';
            $graphicImage = $firstPkg['LabelImage']['GraphicImage'] ?? null;
        }

        if (! $graphicImage) {
            return response()->json(['success' => false, 'message' => 'Label not available for this shipment.']);
        }

        return response()->json([
            'success' => true,
            'awb_number' => $invoice->shipperInfo->awb_number,
            'label_format' => $labelFormat,
            'graphic_image' => $graphicImage,
        ]);
    }

    /**
     * Remove a single prepaid shipment from a manifest.
     *
     * Deletes the Manifest row for the given shipper and moves the shipment
     * back to 'packed' so it can be re-manifested later. Admin may act on any
     * prepaid (shipment_type = 5) manifest.
     */
    public function prepaidRemoveFromManifest(Request $request)
    {
        if (! auth()->guard('admin')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Your session has expired. Please login again.',
            ], 401);
        }

        $adminId = (int) auth()->guard('admin')->id();
        $shipperId = (int) $request->input('shipper_id');
        $manifestNumber = trim((string) $request->input('manifest_number'));

        if (! $shipperId || $manifestNumber === '') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request. Please try again.',
            ], 422);
        }

        $manifest = Manifest::query()
            ->where('shipper_id', $shipperId)
            ->where('manifest_number', $manifestNumber)
            ->whereHas('shipper', function ($q) {
                $q->where('shipment_type', 5);
            })
            ->first();

        if (! $manifest) {
            return response()->json([
                'success' => false,
                'message' => 'Manifest record not found for this shipment.',
            ], 404);
        }

        $shipper = ShipperInfo::find($shipperId);

        if (! $shipper) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        $awbNumber = $shipper->awb_number;
        $manifestNumberRemoved = $manifest->manifest_number;

        $manifest->delete();

        $shipper->status = 'packed';
        $shipper->save();

        ShipmentLog::logStatus(
            $shipperId,
            $awbNumber,
            'packed',
            'manifested',
            'Prepaid shipment removed from manifest '.$manifestNumberRemoved.' and moved back to Packed.',
            $shipper->customer_id ?? 0,
            'admin'
        );

        \Log::info('Prepaid shipment #'.$shipperId.' (AWB '.$awbNumber.') removed from manifest '.$manifestNumberRemoved.' by admin #'.$adminId.' → status back to packed.');

        return response()->json([
            'success' => true,
            'message' => 'Shipment removed from manifest '.$manifestNumberRemoved.' and moved back to Packed.',
        ]);
    }

    /**
     * Close a prepaid manifest (set its status to Close).
     *
     * All manifest rows sharing the given manifest number (prepaid shippers
     * only) are marked as closed.
     */
    public function prepaidCloseManifest(Request $request)
    {
        if (! auth()->guard('admin')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Your session has expired. Please login again.',
            ], 401);
        }

        $adminId = (int) auth()->guard('admin')->id();
        $manifestNumber = trim((string) $request->input('manifest_number'));

        if ($manifestNumber === '') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request. Please try again.',
            ], 422);
        }

        $rows = Manifest::query()
            ->where('manifest_number', $manifestNumber)
            ->whereHas('shipper', function ($q) {
                $q->where('shipment_type', 5);
            })
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Manifest not found.',
            ], 404);
        }

        $alreadyClosed = $rows->every(function ($row) {
            return (int) $row->status === Manifest::STATUS_CLOSE;
        });

        if ($alreadyClosed) {
            return response()->json([
                'success' => false,
                'message' => 'This manifest is already closed.',
            ], 422);
        }

        Manifest::query()
            ->where('manifest_number', $manifestNumber)
            ->whereHas('shipper', function ($q) {
                $q->where('shipment_type', 5);
            })
            ->update(['status' => Manifest::STATUS_CLOSE]);

        \Log::info('Prepaid manifest '.$manifestNumber.' closed by admin #'.$adminId.' ('.$rows->count().' manifest rows).');

        return response()->json([
            'success' => true,
            'message' => 'Manifest '.$manifestNumber.' has been closed successfully.',
        ]);
    }

    /**
     * Assign a closed prepaid manifest for pickup.
     *
     * Marks every prepaid manifest row sharing the given manifest number as
     * Pickup and stores the requested pickup date. Each underlying shipment
     * is moved to 'ready_for_pickup'.
     */
    public function prepaidAssignForPickup(Request $request)
    {
        if (! auth()->guard('admin')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Your session has expired. Please login again.',
            ], 401);
        }

        $adminId = (int) auth()->guard('admin')->id();
        $manifestNumber = trim((string) $request->input('manifest_number'));

        if ($manifestNumber === '') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request. Please try again.',
            ], 422);
        }

        $pickupDate = trim((string) $request->input('pickup_date'));

        if ($pickupDate !== '') {
            try {
                \Carbon\Carbon::parse($pickupDate)->format('Y-m-d');
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid pickup date selected.',
                ], 422);
            }
        }

        $rows = Manifest::query()
            ->where('manifest_number', $manifestNumber)
            ->whereHas('shipper', function ($q) {
                $q->where('shipment_type', 5);
            })
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Manifest not found.',
            ], 404);
        }

        $alreadyAssigned = $rows->every(function ($row) {
            return (int) $row->status === Manifest::STATUS_PICKUP;
        });

        if ($alreadyAssigned) {
            return response()->json([
                'success' => false,
                'message' => 'This manifest is already assigned for pickup.',
            ], 422);
        }

        Manifest::query()
            ->where('manifest_number', $manifestNumber)
            ->whereHas('shipper', function ($q) {
                $q->where('shipment_type', 5);
            })
            ->update([
                'status' => Manifest::STATUS_PICKUP,
                'pickup_date' => $pickupDate !== '' ? \Carbon\Carbon::parse($pickupDate)->format('Y-m-d') : null,
            ]);

        $updatedShippers = 0;
        foreach ($rows as $row) {
            $shipper = ShipperInfo::find($row->shipper_id);
            if (! $shipper || $shipper->status === 'assigned_for_pickup') {
                continue;
            }

            $oldStatus = $shipper->status;
            $shipper->status = 'ready_for_pickup';
            $shipper->save();

            ShipmentLog::logStatus(
                $shipper->id,
                $shipper->awb_number,
                'ready_for_pickup',
                $oldStatus ?: 'manifested',
                'Prepaid manifest '.$manifestNumber.' assigned for pickup.',
                $shipper->customer_id ?? 0,
                'admin'
            );

            $updatedShippers++;
        }

        \Log::info('Prepaid manifest '.$manifestNumber.' assigned for pickup by admin #'.$adminId.' ('.$rows->count().' manifest rows, '.$updatedShippers.' shipments marked ready for pickup).');

        return response()->json([
            'success' => true,
            'message' => 'Manifest '.$manifestNumber.' has been assigned for pickup.',
        ]);
    }

    private function getPickupDateOptions(): array
    {
        $now = \Carbon\Carbon::now('Asia/Kolkata');
        $startOffset = $now->hour < 12 ? 0 : 1;
        $labels = ['', '', '', ''];
        $options = [];

        for ($i = 0; $i < 3; $i++) {
            $date = $now->copy()->addDays($startOffset + $i);
            $options[] = [
                'label' => $labels[$startOffset + $i],
                'value' => $date->format('Y-m-d'),
                'display' => $date->format('D, j M'),
            ];
        }

        return $options;
    }

    /**
     * Load every manifest row sharing a manifest number, restricted to
     * prepaid shippers (shipment_type = 5).
     */
    private function loadPrepaidManifestRows(string $manifestNumber)
    {        return Manifest::with([
            'shipper.consigneeInfo' => function ($q) {
                $q->select(
                    'id',
                    'shipper_id',
                    'consignee_name',
                    'contact_person',
                    'phone_number',
                    'email',
                    'address_line1',
                    'address_line2',
                    'address_line3',
                    'city',
                    'state',
                    'zip_code',
                    'delivery_destination',
                    'origin_type'
                );
            },
            'shipper.packageDimensions',
            'shipper.shipmentTracking',
            'shipper.invoices.invoiceItems',
            'customer' => function ($q) {
                $q->select('id', 'first_name', 'last_name', 'phone_number', 'email');
            },
        ])
            ->where('manifest_number', $manifestNumber)
            ->whereHas('shipper', function ($q) {
                $q->where('shipment_type', 5);
            })
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Owning customer for a prepaid manifest row: the manifest's customer
     * first, else the shipper's exporter customer record.
     */
    private function resolvePrepaidManifestCustomer($manifest, $shipper)
    {
        if ($manifest && $manifest->customer) {
            return $manifest->customer;
        }

        $customerId = $shipper ? (int) ($shipper->customer_id ?? 0) : 0;
        if ($customerId > 0) {
            return Customer::select('id', 'first_name', 'last_name', 'phone_number', 'email')->find($customerId);
        }

        return null;
    }

    /**
     * Show all details for a single prepaid manifest (same
     * customer.manifest-detail page the customer flow uses).
     */
    public function prepaidViewManifestDetail($manifestNumber)
    {
        if (! auth()->guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $manifestRows = $this->loadPrepaidManifestRows((string) $manifestNumber);

        if ($manifestRows->isEmpty()) {
            abort(404, 'Manifest not found.');
        }

        $firstManifest = $manifestRows->first();

        $shipments = $manifestRows->map(function ($manifest) {
            $shipper = $manifest->shipper;
            $consignee = $shipper ? $shipper->consigneeInfo : null;
            $invoice = $shipper ? $shipper->invoices->sortByDesc('id')->first() : null;
            $rowCustomer = $this->resolvePrepaidManifestCustomer($manifest, $shipper);

            $from = $shipper ? trim(($shipper->city ?? '-') . ', ' . ($shipper->state ?? '-')) : '-';
            $to = $consignee ? trim(($consignee->city ?? '-') . ', ' . ($consignee->state ?? '-')) : '-';

            $totalWeight = 0.0;
            if ($shipper) {
                foreach ($shipper->packageDimensions as $pkg) {
                    $totalWeight += (float) ($pkg->chargeable_weight ?? $pkg->actual_weight_kg ?? 0);
                }
            }

            $originType = strtoupper(trim((string) ($consignee->origin_type ?? '')));
            $orderType = in_array($originType, ['CSB V', 'CSB 5'], true) ? 'CSB5' : 'CSB4';

            $consigneeAddress = $consignee
                ? trim(implode(', ', array_filter([
                    $consignee->address_line1 ?? '',
                    $consignee->address_line2 ?? '',
                    $consignee->address_line3 ?? '',
                    trim(($consignee->city ?? '') . ', ' . ($consignee->state ?? '')),
                    $consignee->zip_code ?? '',
                    $consignee->delivery_destination ?? '',
                ])))
                : '';

            return [
                'shipper_id' => $shipper ? (int) $shipper->id : null,
                'awb_number' => $shipper ? ($shipper->awb_number ?? 'N/A') : 'N/A',
                'invoice_number' => $invoice ? ($invoice->invoice_number ?? 'N/A') : 'N/A',
                'shipper_company' => $shipper ? ($shipper->company_name ?: ($shipper->contact_person ?: 'N/A')) : 'N/A',
                'consignee_name' => $consignee ? ($consignee->consignee_name ?: ($consignee->contact_person ?: 'N/A')) : 'N/A',
                'from' => $from,
                'to' => $to,
                'currency' => $invoice ? ($invoice->invoice_currency ?? '') : '',
                'amount' => (float) ($shipper ? ($shipper->total_price ?? 0) : 0),
                'amount_formatted' => $shipper && $shipper->total_price
                    ? number_format((float) $shipper->total_price, 2) . ' ' . ($invoice->invoice_currency ?? '')
                    : 'N/A',
                'status' => $shipper ? ($shipper->status ?: 'N/A') : 'N/A',
                'order_date' => $shipper && $shipper->created_at
                    ? $shipper->created_at->format('d-m-Y h:i A')
                    : 'N/A',
                'order_type' => $orderType,
                'total_weight' => $totalWeight > 0 ? number_format($totalWeight, 2) . ' kg' : '-',
                'package_count' => $shipper ? $shipper->packageDimensions->count() : 0,
                'address' => $consigneeAddress ?: '-',
                'customer' => [
                    'name' => trim(($rowCustomer->first_name ?? '') . ' ' . ($rowCustomer->last_name ?? '')),
                    'phone' => $rowCustomer->phone_number ?? 'N/A',
                    'email' => $rowCustomer->email ?? 'N/A',
                ],
            ];
        })->values();

        $currency = $shipments->pluck('currency')->filter()->first() ?? '';
        $totalValue = (float) $shipments->sum('amount');
        $totalCost = (float) $manifestRows->sum(function ($manifest) {
            $shipper = $manifest->shipper;
            if (! $shipper) {
                return 0;
            }
            return (float) $shipper->total_base_price
                + (float) $shipper->total_fuel_price
                + (float) $shipper->total_surcharge;
        });

        // Shipment detail data for the "View" modal (JS-friendly), keyed by shipper_id.
        $shipmentDetails = $manifestRows->mapWithKeys(function ($manifest) {
            $shipper = $manifest->shipper;
            $consignee = $shipper ? $shipper->consigneeInfo : null;
            $tracking = $shipper ? $shipper->shipmentTracking : null;
            $invoice = $shipper ? $shipper->invoices->sortByDesc('id')->first() : null;
            $items = $invoice ? $invoice->invoiceItems : collect([]);
            $packages = $shipper ? $shipper->packageDimensions : collect([]);
            $rowCustomer = $this->resolvePrepaidManifestCustomer($manifest, $shipper);

            $displayAmount = $shipper && $shipper->total_price !== null && (float) $shipper->total_price > 0
                ? (float) $shipper->total_price
                : round((float) $items->sum('amount'), 2);

            $orderDate = $shipper && $shipper->created_at
                ? $shipper->created_at->format('d-m-Y h:i A')
                : null;

            if (! $shipper || ! $shipper->id) {
                return [];
            }

            return [
                (int) $shipper->id => [
                    'shipper_id' => (int) $shipper->id,
                    'awb_number' => $shipper->awb_number ?? null,
                    'manifest_number' => $manifest->manifest_number,
                    'tracking_number' => $tracking ? ($tracking->shipment_identification_number ?? null) : null,
                    'invoice_number' => $invoice ? ($invoice->invoice_number ?? null) : null,
                    'invoice_date' => $invoice && $invoice->invoice_date ? $invoice->invoice_date->format('d-m-Y') : null,
                    'invoice_amount' => $invoice ? number_format((float) $items->sum('amount'), 2) : null,
                    'invoice_currency' => $invoice ? ($invoice->invoice_currency ?? null) : null,
                    'incoterms' => $invoice ? ($invoice->incoterms ?? null) : null,
                    'reference_number' => $invoice ? ($invoice->reference_number ?? null) : null,
                    'status' => $shipper->status ?: 'draft',
                    'order_date' => $orderDate,
                    'customer' => [
                        'name' => trim(($rowCustomer->first_name ?? '') . ' ' . ($rowCustomer->last_name ?? '')),
                        'phone' => $rowCustomer->phone_number ?? null,
                        'email' => $rowCustomer->email ?? null,
                    ],
                    'ship_from' => $shipper
                        ? trim(($shipper->city ?? '') . ', ' . ($shipper->state ?? '') . ' - ' . ($shipper->pincode ?? '') . ', India')
                        : null,
                    'ship_to' => $consignee
                        ? trim(($consignee->city ?? '') . ', ' . ($consignee->state ?? '') . ' - ' . ($consignee->zip_code ?? '') . ', ' . ($consignee->delivery_destination ?? ''))
                        : null,
                    'shipper' => [
                        'company' => $shipper->company_name,
                        'contact' => $shipper->contact_person,
                        'phone' => $shipper->phone_number,
                        'email' => $shipper->email,
                        'address' => trim(($shipper->address_line1 ?? '') . ' ' . ($shipper->address_line2 ?? '') . ' ' . ($shipper->address_line3 ?? '')),
                        'address_line1' => $shipper->address_line1,
                        'address_line2' => $shipper->address_line2,
                        'address_line3' => $shipper->address_line3,
                        'kyc_number' => $shipper->kyc_number,
                        'city_state_pin' => trim(($shipper->city ?? '') . ', ' . ($shipper->state ?? '') . ' - ' . ($shipper->pincode ?? '')),
                    ],
                    'consignee' => $consignee ? [
                        'name' => $consignee->consignee_name,
                        'contact' => $consignee->contact_person,
                        'phone' => $consignee->phone_number,
                        'email' => $consignee->email,
                        'address' => trim(($consignee->address_line1 ?? '') . ' ' . ($consignee->address_line2 ?? '') . ' ' . ($consignee->address_line3 ?? '')),
                        'address_line1' => $consignee->address_line1,
                        'address_line2' => $consignee->address_line2,
                        'address_line3' => $consignee->address_line3,
                        'city_state_zip' => trim(($consignee->city ?? '') . ', ' . ($consignee->state ?? '') . ' - ' . ($consignee->zip_code ?? '')),
                    ] : null,
                    'destination' => $consignee ? $consignee->delivery_destination : null,
                    'origin_type' => $consignee ? $consignee->origin_type : null,
                    'shipping_method' => $shipper ? $shipper->shipping_method : null,
                    'packages' => $packages->map(function ($pkg, $idx) {
                        return [
                            'index' => $idx + 1,
                            'weight' => $pkg->actual_weight_kg,
                            'length' => $pkg->length_cm,
                            'width' => $pkg->width_cm,
                            'height' => $pkg->height_cm,
                            'volumetric' => $pkg->volumetric_weight,
                            'chargeable' => $pkg->chargeable_weight,
                        ];
                    })->values()->toArray(),
                    'items' => $items->map(function ($item) {
                        $qty = $item->qty ?? 0;
                        $rate = $item->unit_rate ?? 0;
                        $igstAmt = $item->igst_amount ?? 0;
                        $baseAmount = $qty * $rate;
                        $amount = $item->amount ?? ($baseAmount + $igstAmt);

                        return [
                            'box_no' => $item->box_no,
                            'description' => $item->description,
                            'hs_code' => $item->hs_code,
                            'hts_code' => $item->hts_code,
                            'unit_type' => $item->unit_type,
                            'qty' => $qty,
                            'unit_rate' => $rate,
                            'igst_percentage' => $item->igst_percentage ?? 0,
                            'igst_amount' => number_format($igstAmt, 2),
                            'amount' => number_format($amount, 2),
                        ];
                    })->values()->toArray(),
                    'items_total' => number_format($displayAmount, 2),
                    'price_breakdown' => [
                        'base' => $shipper->total_base_price !== null ? (float) $shipper->total_base_price : ($shipper->base_price !== null ? (float) $shipper->base_price : null),
                        'fuel' => $shipper->total_fuel_price !== null ? (float) $shipper->total_fuel_price : ($shipper->fuel_price !== null ? (float) $shipper->fuel_price : null),
                        'surcharge' => $shipper->total_surcharge !== null ? (float) $shipper->total_surcharge : ($shipper->surcharge_total !== null ? (float) $shipper->surcharge_total : null),
                        'gst' => $shipper->gst_amount !== null ? (float) $shipper->gst_amount : null,
                        'total' => (float) $displayAmount,
                    ],
                    'charges' => [
                        'transport' => $shipper->total_base_price !== null
                            ? 'INR ' . number_format((float) $shipper->total_base_price, 2)
                            : ($shipper->base_price !== null ? 'INR ' . number_format((float) $shipper->base_price, 2) : null),
                        'service_options' => $shipper->total_fuel_price !== null
                            ? 'INR ' . number_format((float) $shipper->total_fuel_price, 2)
                            : ($shipper->fuel_price !== null ? 'INR ' . number_format((float) $shipper->fuel_price, 2) : null),
                        'surcharge' => $shipper->total_surcharge !== null
                            ? 'INR ' . number_format((float) $shipper->total_surcharge, 2)
                            : ($shipper->surcharge_total !== null ? 'INR ' . number_format((float) $shipper->surcharge_total, 2) : null),
                        'gst' => $shipper->gst_amount !== null
                            ? 'INR ' . number_format((float) $shipper->gst_amount, 2)
                            : null,
                        'total' => $shipper->total_price !== null && (float) $shipper->total_price > 0
                            ? 'INR ' . number_format((float) $shipper->total_price, 2)
                            : null,
                        'billing_weight' => $tracking && $tracking->billing_weight
                            ? trim(($tracking->billing_weight_uom ?? '') . ' ' . ($tracking->billing_weight ?? '-'))
                            : null,
                    ],
                ],
            ];
        })->all();

        $firstCustomer = $this->resolvePrepaidManifestCustomer($firstManifest, $firstManifest->shipper);
        $manifest = (object) [
            'manifest_number' => $firstManifest->manifest_number,
            'manifest_created_at' => $firstManifest->created_at,
            'customer_name' => trim(($firstCustomer->first_name ?? '') . ' ' . ($firstCustomer->last_name ?? '')),
            'shipment_count' => $shipments->count(),
            'total_value' => $totalValue,
            'total_cost' => $totalCost,
            'currency' => $currency,
            'status' => (int) ($firstManifest->status ?? Manifest::STATUS_OPEN),
            'shipments' => $shipments,
        ];

        return view('customer.manifest-detail', compact('manifest', 'shipmentDetails'))
            ->with('pickupDateOptions', $this->getPickupDateOptions())
            ->with('isAdminView', true);
    }

    /**
     * Print the prepaid manifest label(s) - same UWC label page the customer
     * flow uses.
     */
    public function prepaidManifestLabel($manifestNumber)
    {
        if (! auth()->guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $manifestRows = $this->loadPrepaidManifestRows((string) $manifestNumber);

        if ($manifestRows->isEmpty()) {
            abort(404, 'Manifest not found.');
        }

        $firstManifest = $manifestRows->first();

        $shipmentCount = $manifestRows->count();
        $totalValue = 0;
        $service = 'DIRECT';
        $senderCompany = 'UWC COURIERS PVT LTD';
        $senderAddress = 'UWC COURIERS PVT LTD, Khasra 4/2, Bandh Road, Sultanpur, Delhi - 110086, India';
        $senderPhone = '8130470109';

        foreach ($manifestRows as $manifest) {
            $shipper = $manifest->shipper;
            $invoice = $shipper ? $shipper->invoices->sortByDesc('id')->first() : null;
            $items = $invoice ? $invoice->invoiceItems : collect([]);

            $displayAmount = $shipper && $shipper->total_price !== null && (float) $shipper->total_price > 0
                ? (float) $shipper->total_price
                : round((float) $items->sum('amount'), 2);

            $totalValue += (float) $displayAmount;

            if ($shipper) {
                $service = strtoupper(trim((string) ($shipper->shipping_method ?: $service)));
                $senderCompany = ($shipper->company_name ?: $shipper->contact_person) ?: $senderCompany;

                $shipperAddress = trim(implode(', ', array_filter([
                    $shipper->address_line1 ?? '',
                    $shipper->address_line2 ?? '',
                    $shipper->address_line3 ?? '',
                    trim(($shipper->city ?? '') . ', ' . ($shipper->state ?? '') . ' - ' . ($shipper->pincode ?? '')),
                ])));

                if ($shipperAddress !== '') {
                    $senderAddress = $shipperAddress;
                }

                if ($shipper->phone_number) {
                    $senderPhone = $shipper->phone_number;
                }
            }
        }

        $deliveryCompany = 'Multiple Destinations';
        $deliveryAddress = $shipmentCount . ' shipments in this manifest';
        $deliveryPhone = '';

        if ($shipmentCount === 1) {
            $firstShipper = $manifestRows->first()->shipper;
            $consignee = $firstShipper ? $firstShipper->consigneeInfo : null;

            if ($consignee) {
                $deliveryCompany = $consignee->consignee_name ?: ($consignee->contact_person ?: 'N/A');
                $deliveryAddress = trim(implode(', ', array_filter([
                    $consignee->address_line1 ?? '',
                    $consignee->address_line2 ?? '',
                    $consignee->address_line3 ?? '',
                    trim(($consignee->city ?? '') . ', ' . ($consignee->state ?? '') . ' - ' . ($consignee->zip_code ?? '')),
                    $consignee->delivery_destination ?? '',
                ])));
                $deliveryAddress = $deliveryAddress !== '' ? $deliveryAddress : '-';
                $deliveryPhone = $consignee->phone_number ?? '';
            }
        }

        $labels = [[
            'manifest_number' => $firstManifest->manifest_number,
            'awb_number' => $firstManifest->manifest_number,
            'service' => $service,
            'sender_company' => $senderCompany,
            'sender_address' => $senderAddress,
            'sender_phone' => $senderPhone,
            'delivery_company' => $deliveryCompany,
            'delivery_address' => $deliveryAddress,
            'delivery_phone' => $deliveryPhone,
            'items' => [[
                'description' => 'Assorted Goods',
                'qty' => $shipmentCount,
                'unit_type' => 'shipment(s)',
                'amount' => $totalValue,
            ]],
            'items_total' => number_format($totalValue, 2),
            'date' => now('Asia/Kolkata')->format('Y-m-d H:i:s'),
        ]];

        $firstCustomer = $this->resolvePrepaidManifestCustomer($firstManifest, $firstManifest->shipper);
        $manifest = (object) [
            'manifest_number' => $firstManifest->manifest_number,
            'shipment_count' => $shipmentCount,
            'customer_name' => trim(($firstCustomer->first_name ?? '') . ' ' . ($firstCustomer->last_name ?? '')),
            'total_value' => $totalValue,
        ];

        return view('customer.manifest-label', compact('manifest', 'labels'));
    }

    /**
     * Print the prepaid manifest document (A4 summary sheet).
     */
    public function prepaidManifestDocument($manifestNumber)
    {
        if (! auth()->guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $manifestRows = $this->loadPrepaidManifestRows((string) $manifestNumber);

        if ($manifestRows->isEmpty()) {
            abort(404, 'Manifest not found.');
        }

        $firstManifest = $manifestRows->first();
        $customer = $this->resolvePrepaidManifestCustomer($firstManifest, $firstManifest->shipper);

        $shipments = $manifestRows->map(function ($manifest) {
            $shipper = $manifest->shipper;
            $consignee = $shipper ? $shipper->consigneeInfo : null;
            $invoice = $shipper ? $shipper->invoices->sortByDesc('id')->first() : null;
            $items = $invoice ? $invoice->invoiceItems : collect([]);

            $totalWeight = 0.0;
            if ($shipper) {
                foreach ($shipper->packageDimensions as $pkg) {
                    $totalWeight += (float) ($pkg->chargeable_weight ?? $pkg->actual_weight_kg ?? 0);
                }
            }

            return [
                'awb_number' => $shipper ? ($shipper->awb_number ?? 'N/A') : 'N/A',
                'invoice_number' => $invoice ? ($invoice->invoice_number ?? 'N/A') : 'N/A',
                'shipper_company' => $shipper ? ($shipper->company_name ?: ($shipper->contact_person ?: 'N/A')) : 'N/A',
                'consignee_name' => $consignee ? ($consignee->consignee_name ?: ($consignee->contact_person ?: 'N/A')) : 'N/A',
                'from' => $shipper ? trim(($shipper->city ?? '-') . ', ' . ($shipper->state ?? '-')) : '-',
                'to' => $consignee ? trim(($consignee->city ?? '-') . ', ' . ($consignee->state ?? '-')) : '-',
                'delivery_destination' => $consignee ? ($consignee->delivery_destination ?? '') : '',
                'package_count' => $shipper ? $shipper->packageDimensions->count() : 0,
                'total_weight' => $totalWeight > 0 ? number_format($totalWeight, 2) . ' kg' : '-',
                'currency' => $invoice ? ($invoice->invoice_currency ?? '') : '',
                'amount' => (float) ($shipper ? ($shipper->total_price ?? 0) : 0),
                'items' => $items->map(function ($item) {
                    $qty = (float) ($item->qty ?? 0);
                    $rate = (float) ($item->unit_rate ?? 0);
                    $igstAmt = (float) ($item->igst_amount ?? 0);
                    $baseAmount = $qty * $rate;
                    $amount = $item->amount ?? ($baseAmount + $igstAmt);

                    return [
                        'description' => $item->description ?: 'Item',
                        'qty' => $qty,
                        'amount' => (float) $amount,
                    ];
                })->values()->all(),
            ];
        })->values();

        $currency = $shipments->pluck('currency')->filter()->first() ?? '';
        $totalValue = (float) $shipments->sum('amount');
        $totalCost = (float) $manifestRows->sum(function ($manifest) {
            $shipper = $manifest->shipper;
            if (! $shipper) {
                return 0;
            }
            return (float) $shipper->total_base_price
                + (float) $shipper->total_fuel_price
                + (float) $shipper->total_surcharge;
        });

        $manifest = (object) [
            'manifest_number' => $firstManifest->manifest_number,
            'manifest_created_at' => $firstManifest->created_at,
            'pickup_date' => $firstManifest->pickup_date,
            'status' => (int) ($firstManifest->status ?? Manifest::STATUS_OPEN),
            'customer_name' => trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
            'customer_phone' => $customer->phone_number ?? '',
            'shipment_count' => $shipments->count(),
            'total_value' => $totalValue,
            'total_cost' => $totalCost,
            'currency' => $currency,
            'shipments' => $shipments,
        ];

        return view('customer.manifest-document', compact('manifest'));
    }

    // ====================================================================
    // Shared manifest helpers (same logic as the customer manifest helpers
    // in CustomerController). Carrier helpers are customer-agnostic; wallet
    // helpers receive the exporter customer id (0 = none, blocks paid
    // manifests exactly like a missing wallet does).
    // ====================================================================

    private function resolveShippingMethod($shipper)
    {
        $shippingMethod = $shipper->shipping_method;

        if (! $shippingMethod) {
            $createShipmentMethod = CreateShipment::where('shipper_id', $shipper->id)->value('shipping_method');
            if ($createShipmentMethod) {
                $shippingMethod = $createShipmentMethod;
            }
        }

        if (! $shippingMethod) {
            $pkgMethod = PackageDimension::where('shipper_id', $shipper->id)
                ->whereNotNull('shipping_method')
                ->value('shipping_method');
            if ($pkgMethod) {
                $shippingMethod = $pkgMethod;
            }
        }

        if (! $shippingMethod) {
            $defaultService = CourierService::orderBy('id', 'asc')->first();
            if ($defaultService) {
                $shippingMethod = $defaultService->method;
            }
        }

        return $shippingMethod ?? '';
    }

    private function isPostShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return false;
        }

        $methodUpper = strtoupper(trim($shippingMethod));

        // PostShipping API is called only for UNITED AIR PREMIUM DDP shipments.
        $isDdp = str_contains($methodUpper, 'DDP');
        $isAirPremium = str_contains($methodUpper, 'UNITED AIR PREMIUM');

        return $isDdp && $isAirPremium;
    }

    private function resolveApiProvider($shippingMethod, $shipper, $courierService = null)
    {
        // Default provider when nothing else matches.
        $fallback = 'ups';

        // Reuse a pre-resolved service when the caller already has one;
        // otherwise look it up now.
        if (! $courierService) {
            $courierService = $this->findCourierService($shippingMethod, $shipper->id);
        }

        if ($courierService) {
            $provider = strtolower(trim($courierService->api_provider ?? ''));
            if (! empty($provider)) {
                return $provider;
            }

            // No explicit api_provider — derive the network for the
            // ShipUniversal/ShipGlobal/UPS fallback branches below.
            $network = strtolower(trim($courierService->network ?? ''));
        } else {
            $network = '';
        }

        // Legacy fallback chain (mirrors the original if/elseif order).
        if ($network === 'ship universal' || $network === 'shipuniversal') {
            return 'shipuniversal';
        }
        if ($this->isOverseasLogisticMethod($shippingMethod)) {
            return 'overseas';
        }
        if ($this->isPostShippingMethod($shippingMethod)) {
            return 'postshipping';
        }
        if ($this->isFlyingTigersMethod($shippingMethod)) {
            return 'flyingtigers';
        }
        if ($network === 'ship global' || $network === 'shipglobal') {
            return 'shipglobal';
        }

        return $fallback;
    }

    private function overseasValueToString($value)
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_null($value)) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        // Arrays / objects -> JSON string (never triggers array-to-string).
        $json = json_encode($value);

        return ($json === false) ? '' : $json;
    }

    /**
     * Persist a successful ShipUniversal prepaid manifest using the common
     * tracking tables. Audit entries are performed by "admin".
     */
    private function persistPrepaidShipUniversalManifest(
        $shipper,
        $customerId,
        array $apiResponse,
        $trackingNumber,
        $labelUrl,
        $isBulk = false,
        $targetStatus = 'manifested',
        ?string $manifestNumber = null,
        int $adminId = 0
    ) {
        $targetStatus = $targetStatus === 'ready' ? 'ready' : 'manifested';
        $createShipment = CreateShipment::where('shipper_id', $shipper->id)->first();

        ShipmentTracking::updateOrCreate(
            ['shipper_id' => $shipper->id],
            [
                'customer_id' => $customerId,
                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                'response_status_code' => '1',
                'response_status_description' => 'ShipUniversal shipment created',
                'shipment_identification_number' => $trackingNumber,
                'total_charges_currency' => 'INR',
                'total_charges_amount' => null,
                'billing_weight_uom' => 'KGS',
                'billing_weight' => null,
                'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                'raw_response' => $apiResponse,
                'status' => 'created',
            ]
        );

        $previousStatus = $shipper->status;
        $shipper->status = $targetStatus;
        $shipper->save();

        Tracking::firstOrCreate(
            [
                'shipper_id' => $shipper->id,
                'status' => $targetStatus,
            ],
            [
                'awb_number' => $shipper->awb_number,
                'shipping_id' => $createShipment ? $createShipment->id : null,
                'uwc_id' => $shipper->awb_number,
                'title' => Tracking::getTitleForStatus($targetStatus),
            ]
        );

        // Create a manifest record from the manifests table
        // (bulk flow shares a single manifest number across the whole batch)
        $this->createManifestRecord($shipper->id, $customerId, $manifestNumber);

        ShipmentLog::logStatus(
            $shipper->id,
            $shipper->awb_number,
            $targetStatus,
            $previousStatus,
            'Prepaid shipment manifested via ShipUniversal'
                .($isBulk ? ' (bulk)' : '')
                .'. Tracking: '.($trackingNumber ?? 'N/A'),
            $customerId,
            'admin'
        );

        \Log::info('Prepaid shipment #'.$shipper->id.' manifested via ShipUniversal by admin #'.$adminId.'.');
    }

    /**
     * Create a manifest record for a successfully manifested shipment.
     *
     * Uses the existing record when the same shipper is manifested again
     * (e.g. Confirm Payment flow) so a duplicate manifest is never created.
     */
    private function createManifestRecord(int $shipperId, int $customerId, ?string $manifestNumber = null)
    {
        $manifest = Manifest::where('shipper_id', $shipperId)->first();

        if ($manifest) {
            return $manifest;
        }

        return Manifest::createForShipper($shipperId, $customerId, $manifestNumber);
    }

    private function revertPrepaidReadyToDraftOnManifestFailure(ShipperInfo $shipper, int $customerId, string $previousStatus, int $adminId): bool
    {
        // Only revert when the failed manifest attempt actually started from 'ready'.
        if ($previousStatus !== 'ready' || $shipper->status !== 'ready') {
            return false;
        }

        $shipper->status = 'draft';
        $shipper->save();

        ShipmentLog::logStatus(
            $shipper->id,
            $shipper->awb_number,
            'draft',
            'ready',
            'Prepaid manifest (carrier booking) failed. Shipment moved back to Draft. No payment was deducted.',
            $customerId,
            'admin'
        );

        \Log::info('Prepaid manifest failed for shipper #'.$shipper->id.' → reverted from ready to draft. No payment was deducted.');

        return true;
    }

    /**
     * Compute the payable amount for a shipment and check whether it has already been charged.
     * Payment is cut ONLY after a successful manifest booking.
     */
    private function getShipmentChargeInfo(ShipperInfo $shipper, int $customerId): array
    {
        $amount = $shipper->total_price !== null && (float) $shipper->total_price > 0
            ? (float) $shipper->total_price
            : ($shipper->serviceRate
                ? (float) $shipper->serviceRate->inclusive_total
                : round((float) ($shipper->invoices()->first()->total_amount ?? 0), 2));

        $amount = round((float) $amount, 2);

        $wallet = $customerId > 0 ? Wallet::where('customer_id', $customerId)->first() : null;
        $balance = $wallet ? (float) $wallet->balance : 0;

        if ($amount <= 0) {
            // Free shipment — nothing to charge, still manifestable.
            return [
                'amount' => $amount,
                'already_charged' => false,
                'balance' => $balance,
                'can_manifest' => true,
                'message' => null,
            ];
        }

        if (! $wallet) {
            return [
                'amount' => $amount,
                'already_charged' => false,
                'balance' => 0,
                'can_manifest' => false,
                'message' => 'Wallet not found. Please contact support.',
            ];
        }

        if ($wallet->balance < $amount) {
            return [
                'amount' => $amount,
                'already_charged' => false,
                'balance' => $balance,
                'can_manifest' => false,
                'message' => 'Insufficient wallet balance to manifest this shipment. Current balance is ₹'.number_format($wallet->balance, 2).', required ₹'.number_format($amount, 2).'.',
            ];
        }

        return [
            'amount' => $amount,
            'already_charged' => false,
            'balance' => $balance,
            'can_manifest' => true,
            'message' => null,
        ];
    }

    /**
     * Deduct the shipment charge from the wallet.
     * Must be called AFTER a successful carrier booking so that payment is cut only when
     * the manifest succeeds.
     */
    private function chargeShipmentIfNotPaid(ShipperInfo $shipper, int $customerId): array
    {
        $info = $this->getShipmentChargeInfo($shipper, $customerId);
        $amount = $info['amount'];
        $balance = $info['balance'];

        if ($amount <= 0) {
            return [
                'charged' => false,
                'already_charged' => false,
                'amount' => 0,
                'new_balance' => $balance,
                'message' => null,
            ];
        }

        if (! $info['can_manifest']) {
            return [
                'charged' => false,
                'already_charged' => false,
                'amount' => $amount,
                'new_balance' => $balance,
                'message' => $info['message'],
            ];
        }

        $wallet = Wallet::where('customer_id', $customerId)->first();
        if (! $wallet) {
            return [
                'charged' => false,
                'already_charged' => false,
                'amount' => $amount,
                'new_balance' => 0,
                'message' => 'Wallet not found. Please contact support.',
            ];
        }

        DB::transaction(function () use ($wallet, $amount, $shipper, $customerId) {
            $wallet->decrement('balance', $amount);
            $wallet->refresh();

            WalletTransaction::create([
                'customer_id' => $customerId,
                'type' => 'debit',
                'reason' => 'shipment_charge',
                'amount' => $amount,
                'balance_after' => $wallet->balance,
                'reference' => $shipper->awb_number,
                'description' => 'Payment of ₹'.number_format($amount, 2).' for shipment '.($shipper->awb_number ?: '#'.$shipper->id),
            ]);
        });

        return [
            'charged' => true,
            'already_charged' => false,
            'amount' => $amount,
            'new_balance' => (float) $wallet->refresh()->balance,
            'message' => null,
        ];
    }

    // ====================================================================
    // Carrier manifest helpers ported verbatim from CustomerController
    // (customer-agnostic: shipper-driven, wallet via passed customer id).
    // ====================================================================


    /**
     * Call the Ship Global API to create an order/shipment.
     * Two-step process:
     * 1. Generate Bearer token from customers.php
     * 2. Create order via addOrder.php using the Bearer token
     *
     * @param  ShipperInfo  $shipper
     * @return array
     */
    private function callShipGlobalApiFromDb($shipper)
    {
        try {
            // Step 1: Generate Bearer token from Ship Global
            $tokenResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://labels.shipglobal.in/api/v1/customers.php', [
                'email' => 'csd@unitedcouriers.biz',
                'password' => 'mSN7KbhrZ0uvb229YWO',
            ]);

            if (! $tokenResponse->successful()) {
                $tokenError = $tokenResponse->json();
                $errorMessage = 'Ship Global token generation failed.';
                if (is_array($tokenError)) {
                    if (isset($tokenError['error'])) {
                        $errorMessage = is_string($tokenError['error']) ? $tokenError['error'] : json_encode($tokenError['error']);
                    } elseif (isset($tokenError['message'])) {
                        $errorMessage = $tokenError['message'];
                    }
                }
                \Log::error('Ship Global token generation failed: '.$errorMessage.' | Status: '.$tokenResponse->status());

                return [
                    'success' => false,
                    'message' => $errorMessage,
                ];
            }

            $tokenData = $tokenResponse->json();
            $bearerToken = null;

            // Extract token from various possible response formats
            if (isset($tokenData['token'])) {
                $bearerToken = $tokenData['token'];
            } elseif (isset($tokenData['data']) && isset($tokenData['data']['token'])) {
                $bearerToken = $tokenData['data']['token'];
            } elseif (isset($tokenData['access_token'])) {
                $bearerToken = $tokenData['access_token'];
            } elseif (isset($tokenData['data']) && isset($tokenData['data']['access_token'])) {
                $bearerToken = $tokenData['data']['access_token'];
            }

            if (! $bearerToken) {
                \Log::error('Ship Global: No token found in response. Response: '.json_encode($tokenData));

                return [
                    'success' => false,
                    'message' => 'No bearer token found in Ship Global authentication response.',
                ];
            }

            \Log::info('Ship Global token generated successfully.');

            // Step 2: Build the order payload and create the order
            $consignee = $shipper->consigneeInfo;
            $packages = $shipper->packageDimensions;
            $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();

            if (! $consignee) {
                return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
            }

            // Get package dimensions (use first package)
            $firstPackage = $packages->first();
            $packageWeightKg = $firstPackage ? (float) $firstPackage->actual_weight_kg * 1000 : 0.5;
            $packageLength = $firstPackage ? (float) $firstPackage->length_cm : 10;
            $packageBreadth = $firstPackage ? (float) $firstPackage->width_cm : 10;
            $packageHeight = $firstPackage ? (float) $firstPackage->height_cm : 10;

            // Get consignee country code from delivery_destination
            $consigneeCountryCode = $this->getCountryCodeFromDestination($consignee->delivery_destination ?? '');

            // Build shipper address string
            $shipperAddress = trim(
                ($shipper->address_line1 ?? '').' '.
                ($shipper->address_line2 ?? '').' '.
                ($shipper->address_line3 ?? '')
            );

            // Build consignee address string
            $consigneeAddress = trim(
                ($consignee->address_line1 ?? '').' '.
                ($consignee->address_line2 ?? '').' '.
                ($consignee->address_line3 ?? '')
            );

            // Split shipper name into first/last
            // Ship Global requires both firstname and lastname to be non-empty
            $shipperNameParts = preg_split('/\s+/', trim($shipper->contact_person ?? $shipper->company_name ?? 'Shipper'), 2);
            $sellerFirstname = $shipperNameParts[0] ?? 'Shipper';
            $sellerLastname = ! empty($shipperNameParts[1]) ? $shipperNameParts[1] : $sellerFirstname;

            // Split consignee name into first/last
            // Ship Global requires both firstname and lastname to be non-empty
            $consigneeNameParts = preg_split('/\s+/', trim($consignee->consignee_name ?? $consignee->contact_person ?? 'Consignee'), 2);
            $consigneeFirstname = $consigneeNameParts[0] ?? 'Consignee';
            $consigneeLastname = ! empty($consigneeNameParts[1]) ? $consigneeNameParts[1] : $consigneeFirstname;

            // Get invoice details
            $invoiceNo = $invoice ? ($invoice->invoice_number ?? '') : '';
            $invoiceDate = $invoice ? ($invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '') : '';
            $currencyCode = $invoice ? ($invoice->invoice_currency ?? 'USD') : 'USD';
            $orderReference = $shipper->awb_number ?? ($invoice ? ($invoice->reference_number ?? '') : '');

            // Get csb5_status from customer
            $customer = Customer::find($shipper->customer_id);
            $csb5Status = 0;
            if ($customer && $customer->csb_status) {
                $csb5Status = (int) $customer->csb_status;
            }

            // Get the courier service code for the shipping method
            $shippingMethod = $this->resolveShippingMethod($shipper);
            $courierService = $this->findCourierService($shippingMethod, $shipper->id);
            $serviceCode = $courierService ? ($courierService->service_code ?? $courierService->scode ?? '') : '';

            // Build vendor_order_items from invoice items
            $vendorOrderItems = [];
            if ($invoice) {
                $invoiceItems = ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get();
                foreach ($invoiceItems as $item) {
                    $vendorOrderItems[] = [
                        'vendor_order_item_name' => $item->description ?? '',
                        'vendor_order_item_sku' => $item->hs_code ?? $item->hts_code ?? '',
                        'vendor_order_item_quantity' => (int) $item->qty,
                        'vendor_order_item_unit_price' => (float) $item->unit_rate,
                        'vendor_order_item_hsn' => $item->hs_code ?? '',
                        'vendor_order_item_tax_rate' => (float) $item->igst_percentage,
                    ];
                }
            }

            // If no invoice items, add a default item
            if (empty($vendorOrderItems)) {
                $vendorOrderItems[] = [
                    'vendor_order_item_name' => 'General Merchandise',
                    'vendor_order_item_sku' => '',
                    'vendor_order_item_quantity' => 1,
                    'vendor_order_item_unit_price' => (float) ($invoice ? $invoice->invoice_amount : 0),
                    'vendor_order_item_hsn' => '',
                    'vendor_order_item_tax_rate' => 0,
                ];
            }

            // Build the Ship Global order payload
            $payload = [
                'invoice_no' => $invoiceNo,
                'invoice_date' => $invoiceDate,
                'order_reference' => $orderReference,
                'service' => $serviceCode,
                'package_weight' => (float) $packageWeightKg,
                'package_length' => (float) $packageLength,
                'package_breadth' => (float) $packageBreadth,
                'package_height' => (float) $packageHeight,
                'currency_code' => $currencyCode,
                'csb5_status' => $csb5Status,
                'seller_nickname' => 'UnitedW',
                'seller_firstname' => $sellerFirstname,
                'seller_lastname' => $sellerLastname,
                'seller_mobile' => $shipper->phone_number ?? '',
                'seller_email' => $shipper->email ?? '',
                'seller_company' => $shipper->company_name ?? '',
                'seller_address' => $shipperAddress ?: 'Address not provided',
                'seller_address_2' => $shipperAddress ?: 'Address not provided',
                'seller_city' => $shipper->city ?? '',
                'seller_postcode' => $shipper->pincode ?? '',
                'seller_country_code' => 'IN',
                'seller_state' => $shipper->state ?? '',
                'customer_shipping_firstname' => $consigneeFirstname,
                'customer_shipping_lastname' => $consigneeLastname,
                'customer_shipping_mobile' => $consignee->phone_number ?? '',
                'customer_shipping_email' => $consignee->email ?? '',
                'customer_shipping_company' => $consignee->consignee_name ?? '',
                'customer_shipping_address' => $consigneeAddress ?: 'Address not provided',
                'customer_shipping_address_2' => $consigneeAddress ?: 'Address not provided',
                'customer_shipping_city' => $consignee->city ?? '',
                'customer_shipping_postcode' => $consignee->zip_code ?? '',
                'customer_shipping_country_code' => $consigneeCountryCode,
                'customer_shipping_state' => $consignee->state ?? '',
                'vendor_order_items' => $vendorOrderItems,
                // i want abw number in tracking field but ship global api is not accepting it so i am leaving it blank for now
                'tracking' => $shipper->awb_number ?? '',
                // 'mailClass' => '',
                // 'deliveryConfirmation' => '',
                // 'retry' => false,
            ];

            // print_r($payload);
            // return;

            \Log::info('Ship Global order payload for shipper #'.$shipper->id.': '.json_encode($payload));

            // Step 2: Call the addOrder.php API with Bearer token
            $orderResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$bearerToken,
            ])->post('https://labels.shipglobal.in/api/v1/addOrder.php', $payload);

            $apiResponse = $orderResponse->json();

            // Ship Global may return HTTP 200 but with "success": false in the body
            // (e.g., label: "manual" means order created but label needs manual generation)
            // We treat it as success if an order_number was returned, regardless of body success flag
            $orderNumber = null;
            if (isset($apiResponse['data']) && isset($apiResponse['data']['order_number'])) {
                $orderNumber = $apiResponse['data']['order_number'];
            } elseif (isset($apiResponse['order_number'])) {
                $orderNumber = $apiResponse['order_number'];
            }

            if ($orderResponse->successful() && $orderNumber) {
                \Log::info('Ship Global order created for shipper #'.$shipper->id.'. Order#: '.$orderNumber.'. Response: '.json_encode($apiResponse));

                return [
                    'success' => true,
                    'message' => 'Ship Global order created successfully. Order#: '.$orderNumber,
                    'data' => $apiResponse,
                ];
            } elseif ($orderResponse->successful() && ! $orderNumber) {
                // HTTP 200 but no order_number — could be a validation/business error in the body
                $errorMessage = 'Ship Global API returned no order number.';
                if (is_array($apiResponse)) {
                    if (isset($apiResponse['error'])) {
                        $errorMessage = is_string($apiResponse['error']) ? $apiResponse['error'] : json_encode($apiResponse['error']);
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $apiResponse['message'];
                    }
                    if (isset($apiResponse['details']) && is_array($apiResponse['details']) && ! empty($apiResponse['details'])) {
                        $errorMessage .= ' — '.implode('; ', $apiResponse['details']);
                    }
                }
                \Log::error('Ship Global order creation: HTTP 200 but no order_number. Response: '.json_encode($apiResponse));

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $apiResponse,
                ];
            } else {
                $apiResponse = $orderResponse->json();
                $errorMessage = 'Ship Global API returned error.';
                if (is_array($apiResponse)) {
                    if (isset($apiResponse['error'])) {
                        $errorMessage = is_string($apiResponse['error']) ? $apiResponse['error'] : json_encode($apiResponse['error']);
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $apiResponse['message'];
                    } elseif (isset($apiResponse['errors'])) {
                        $errorMessage = is_string($apiResponse['errors']) ? $apiResponse['errors'] : json_encode($apiResponse['errors']);
                    }
                    // Append validation details if available (Ship Global returns field-level errors in "details")
                    if (isset($apiResponse['details']) && is_array($apiResponse['details']) && ! empty($apiResponse['details'])) {
                        $errorMessage .= ' — '.implode('; ', $apiResponse['details']);
                    }
                }
                \Log::error('Ship Global order creation failed: '.$errorMessage.' | Status: '.$orderResponse->status().' | Response: '.json_encode($apiResponse));

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $apiResponse,
                    'status_code' => $orderResponse->status(),
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Ship Global API call failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Ship Global API call failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Determine if a shipping method should be routed to the PostShipping API.
     * Triggered for DDP variants of UNITED AIR PREMIUM and UNITED PRIOR POST.
     *
     * @param  string|null  $shippingMethod
     * @return bool
     */


    /**
     * Determine the PostShipping ServiceTypeName based on weight, parcel count,
     * and destination. Precedence:
     *   1. Offshore (Northern Ireland, Scottish Highlands & Islands) → DPD111
     *   2. Multiple Parcels (>1)                                   → MDPD112
     *   3. 0-5 Kg (single parcel)                                  → DPDUKEPND
     *   4. 5-30 Kg (single parcel)                                 → DPD112
     *
     * @param  string  $shippingMethod
     * @param  CourierService|null  $courierService
     * @param  float  $totalWeight  Total shipment weight in Kg
     * @param  int  $noOfItems  Number of parcels
     * @param  ConsigneeInfo|null  $consignee
     * @return string
     */
    private function getPostShippingServiceTypeName($shippingMethod, $courierService, $totalWeight = 0, $noOfItems = 1, $consignee = null)
    {
        // Priority 1: Offshore deliveries → DPD111 (DPD OFFSHORE- TWO DAY)
        if ($this->isPostShippingOffshoreDestination($consignee)) {
            \Log::info('getPostShippingServiceTypeName: Offshore destination → DPD111');

            return 'DPD111';
        }

        // Priority 2: Multiple parcels → MDPD112 (Multi DPD UK MAINLAND- NEXT DAY)
        if ((int) $noOfItems > 1) {
            \Log::info('getPostShippingServiceTypeName: Multiple parcels ('.$noOfItems.') → MDPD112');

            return 'MDPD112';
        }

        // Priority 3 & 4: Weight-based for single parcel
        if ($totalWeight <= 5) {
            \Log::info('getPostShippingServiceTypeName: Weight '.$totalWeight.'kg (≤5) → DPDUKEPND');

            return 'DPDUKEPND'; // DPD UK Mainland Express PAK
        }

        \Log::info('getPostShippingServiceTypeName: Weight '.$totalWeight.'kg (>5) → DPD112');

        return 'DPD112'; // DPD UK Mainland Next Day
    }

    /**
     * Map a PostShipping ServiceTypeName to the DPD UK NetworkCode.
     *
     * The DPD UK API validates consignment.networkCode (error 1021 "Service Denied"
     * when missing). NetworkCode identifies the delivery service network:
     *   - DPD111 (Offshore - Two Day)        → "7"
     *   - DPDUKEPND / DPD112 / MDPD112       → "1" (Next Day mainland)
     *
     * @param  string  $serviceTypeName
     * @return string
     */


    /**
     * Map a PostShipping ServiceTypeName to the DPD UK NetworkCode.
     *
     * The DPD UK API validates consignment.networkCode (error 1021 "Service Denied"
     * when missing). NetworkCode identifies the delivery service network:
     *   - DPD111 (Offshore - Two Day)        → "7"
     *   - DPDUKEPND / DPD112 / MDPD112       → "1" (Next Day mainland)
     *
     * @param  string  $serviceTypeName
     * @return string
     */
    private function getPostShippingNetworkCode($serviceTypeName)
    {
        $code = strtoupper(trim((string) $serviceTypeName));

        // Offshore (DPD OFFSHORE - TWO DAY) uses network code "7"
        if ($code === 'DPD111') {
            return '7';
        }

        // All mainland next-day services (DPDUKEPND, DPD112, MDPD112) use network code "1"
        return '1';
    }

    /**
     * Check if a consignee destination is an offshore area requiring DPD111 service.
     * Offshore = Northern Ireland (BT postcodes) + Scottish Highlands & Islands
     * (IV, HS, KA, KW, PA, PH, ZE postcodes) + Isle of Man (IM) + Channel Islands (JE, GY).
     *
     * @param  ConsigneeInfo|null  $consignee
     * @return bool
     */


    /**
     * Check if a consignee destination is an offshore area requiring DPD111 service.
     * Offshore = Northern Ireland (BT postcodes) + Scottish Highlands & Islands
     * (IV, HS, KA, KW, PA, PH, ZE postcodes) + Isle of Man (IM) + Channel Islands (JE, GY).
     *
     * @param  ConsigneeInfo|null  $consignee
     * @return bool
     */
    private function isPostShippingOffshoreDestination($consignee)
    {
        if (! $consignee) {
            return false;
        }

        $postcode = strtoupper(preg_replace('/\s+/', '', (string) ($consignee->zip_code ?? '')));
        $city = strtoupper(trim((string) ($consignee->city ?? '')));
        $state = strtoupper(trim((string) ($consignee->state ?? '')));

        // Northern Ireland: postcodes start with BT
        if ($postcode !== '' && str_starts_with($postcode, 'BT')) {
            return true;
        }

        // Scottish Highlands & Islands + other UK offshore postcode prefixes
        $offshorePrefixes = ['IV', 'HS', 'KA', 'KW', 'PA', 'PH', 'ZE', 'IM', 'JE', 'GY'];
        if ($postcode !== '') {
            foreach ($offshorePrefixes as $prefix) {
                if (str_starts_with($postcode, $prefix)) {
                    return true;
                }
            }
        }

        // Fallback: keyword matching on city/state for cases where postcode is missing
        $offshoreKeywords = [
            'NORTHERN IRELAND', 'HIGHLAND', 'ISLAND', 'ISLE OF',
            'ORKNEY', 'SHETLAND', 'HEBRIDES', 'SKYE', 'ISLE OF MAN',
            'CHANNEL ISLANDS', 'JERSEY', 'GUERNSEY',
        ];
        foreach ($offshoreKeywords as $keyword) {
            if (str_contains($city, $keyword) || str_contains($state, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the ThirdPartyToken for PostShipping.
     * A single fixed token is used for all UNITED AIR PREMIUM DDP shipments.
     *
     * @param  float  $totalWeight  Total shipment weight in Kg (kept for signature compatibility)
     * @return string
     */


    /**
     * Return the ThirdPartyToken for PostShipping.
     * A single fixed token is used for all UNITED AIR PREMIUM DDP shipments.
     *
     * @param  float  $totalWeight  Total shipment weight in Kg (kept for signature compatibility)
     * @return string
     */
    private function getPostShippingThirdPartyToken($totalWeight)
    {
        $token = config('services.postshipping.third_party_token');
        \Log::info('getPostShippingThirdPartyToken: Using fixed token for UNITED AIR PREMIUM DDP');

        return $token;
    }

    /**
     * Build the PostShipping API payload from database records.
     * Payload structure mirrors the documented https://api.postshipping.com/api2/shipments format.
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */


    /**
     * Build the PostShipping API payload from database records.
     * Payload structure mirrors the documented https://api.postshipping.com/api2/shipments format.
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */
    private function buildPostShippingPayloadFromDb($shipper)
    {
        $consignee = $shipper->consigneeInfo;
        if (! $consignee) {
            \Log::warning('buildPostShippingPayloadFromDb: No consignee found for shipper #'.$shipper->id);

            return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
        }

        $packages = $shipper->packageDimensions;
        if ($packages->isEmpty()) {
            \Log::warning('buildPostShippingPayloadFromDb: No packages found for shipper #'.$shipper->id);

            return ['success' => false, 'message' => 'No package dimensions found for this shipment.'];
        }

        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();

        // Resolve shipping method + courier service
        $shippingMethod = $this->resolveShippingMethod($shipper);
        $courierService = $this->findCourierService($shippingMethod, $shipper->id);

        // Consignee country code from delivery_destination
        $consigneeCountryCode = $this->getCountryCodeFromDestination($consignee->delivery_destination ?? '');

        // Currency code (default INR to match example payload)
        $currencyCode = $invoice ? ($invoice->invoice_currency ?? 'INR') : 'INR';

        // Total weight & package count (computed first so ServiceTypeName + ThirdPartyToken
        // can be selected based on weight/parcel-count/destination).
        $totalWeight = 0;
        $noOfItems = 0;
        foreach ($packages as $pkg) {
            $w = (float) ($pkg->actual_weight_kg ?? 0);
            if ($w <= 0) {
                $w = 0.5;
            }
            $totalWeight += $w;
            $noOfItems++;
        }
        if ($totalWeight <= 0) {
            $totalWeight = 0.5;
        }
        if ($noOfItems <= 0) {
            $noOfItems = 1;
        }

        // ServiceTypeName is weight/parcel-count/destination-dependent:
        //   Offshore → DPD111 | Multiple parcels → MDPD112 | ≤5kg → DPDUKEPND | >5kg → DPD112
        $serviceTypeName = $this->getPostShippingServiceTypeName($shippingMethod, $courierService, $totalWeight, $noOfItems, $consignee);

        // ThirdPartyToken is weight-dependent:
        //   1-10kg → DPDNW token | 10.1-30kg → DPD112 token
        $thirdPartyToken = $this->getPostShippingThirdPartyToken($totalWeight);

        // First package dimensions for top-level PackageDetails
        $firstPackage = $packages->first();
        $cubicL = (float) ($firstPackage->length_cm ?? 0) ?: 10;
        $cubicW = (float) ($firstPackage->width_cm ?? 0) ?: 10;
        $cubicH = (float) ($firstPackage->height_cm ?? 0) ?: 10;

        // Goods description: prefer first invoice item description, else invoice reference, else default
        $goodsDescription = 'General Merchandise';
        $invoiceItems = collect();
        if ($invoice) {
            $invoiceItems = ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get();
            $firstItem = $invoiceItems->first();
            if ($firstItem && ! empty($firstItem->description)) {
                $goodsDescription = $firstItem->description;
            }
        }

        // Custom value: invoice amount (default 30.00 to match example floor)
        $customValue = $invoice ? (float) ($invoice->invoice_amount ?? 0) : 0;
        if ($customValue <= 0) {
            $customValue = 30.00;
        }

        // Incoterms
        $incoterms = $invoice ? ($invoice->incoterms ?? 'CIF') : 'CIF';
        $orderNumber = $invoice ? ($invoice->invoice_number ?? '') : ($shipper->awb_number ?? '');

        // Build ShipmentResponseItem array — one entry per package, with Pieces from invoice items.
        $shipmentResponseItems = [];
        $packageIndex = 0;
        foreach ($packages as $pkg) {
            $packageIndex++;
            $pkgWeight = (float) ($pkg->actual_weight_kg ?? 0);
            if ($pkgWeight <= 0) {
                $pkgWeight = 0.5;
            }
            $pkgL = (float) ($pkg->length_cm ?? 0) ?: 10;
            $pkgW = (float) ($pkg->width_cm ?? 0) ?: 10;
            $pkgH = (float) ($pkg->height_cm ?? 0) ?: 10;

            // Find invoice items mapped to this package via box_no (1-based)
            $boxItems = $invoiceItems->filter(function ($item) use ($packageIndex) {
                return ((int) ($item->box_no ?? 0)) === $packageIndex;
            });

            // If no items mapped to this box, use all items for the first package
            if ($boxItems->isEmpty() && $packageIndex === 1) {
                $boxItems = $invoiceItems;
            }

            $pieces = [];
            if ($boxItems->isNotEmpty()) {
                foreach ($boxItems as $item) {
                    $itemQty = (float) ($item->qty ?? 1);
                    if ($itemQty <= 0) {
                        $itemQty = 1;
                    }
                    $itemValue = (float) ($item->amount ?? 0);
                    if ($itemValue <= 0) {
                        $itemValue = $customValue;
                    }
                    $pieces[] = [
                        'HarmonisedCode' => (string) ($item->hs_code ?? $item->hts_code ?? ''),
                        'GoodsDescription' => $item->description ?? $goodsDescription,
                        'Content' => $item->description ?? $goodsDescription,
                        'Quantity' => $itemQty,
                        'Weight' => $pkgWeight,
                        'ManufactureCountryCode' => 'IN',
                        'OriginCountryCode' => 'IN',
                        'CurrencyCode' => $currencyCode,
                        'CustomsValue' => $itemValue,
                    ];
                }
            } else {
                // Fallback single piece when no invoice items exist
                $pieces[] = [
                    'HarmonisedCode' => '',
                    'GoodsDescription' => $goodsDescription,
                    'Content' => $goodsDescription,
                    'Quantity' => 1,
                    'Weight' => $pkgWeight,
                    'ManufactureCountryCode' => 'IN',
                    'OriginCountryCode' => 'IN',
                    'CurrencyCode' => $currencyCode,
                    'CustomsValue' => $customValue,
                ];
            }

            $shipmentResponseItems[] = [
                'ItemNoOfPcs' => 1,
                'ItemCubicL' => $pkgL,
                'ItemCubicW' => $pkgW,
                'ItemCubicH' => $pkgH,
                'ItemWeight' => $pkgWeight,
                'ItemDescription' => $goodsDescription,
                'ItemCustomValue' => $customValue,
                'ItemCustomCurrencyCode' => $currencyCode,
                'Notes' => 'Commercial shipment',
                'Pieces' => $pieces,
            ];
        }

        // Pickup times — ReadyTime = now + 2h, CloseTime = now + 5h (format Y/m/d H:i:s)
        $readyTime = now()->addHours(2)->format('Y/m/d H:i:s');
        $closeTime = now()->addHours(5)->format('Y/m/d H:i:s');

        // Build the single shipment object (API expects an array of these)
        $shipmentObject = [
            'ThirdPartyToken' => $thirdPartyToken,
            'SenderDetails' => [
                // 'SenderName'                 => $shipper->contact_person ?? ($shipper->company_name ?? 'Ved'),
                // 'SenderCompanyName'          => $shipper->company_name ?? 'United Worldwide Couriers Pvt Ltd',
                // 'SenderCountryCode'          => 'IN',
                // 'SenderAdd1'                 => $shipper->address_line1 ?? '',
                // 'SenderAdd2'                 => $shipper->address_line2 ?? '',
                // 'SenderAdd3'                 => $shipper->address_line3 ?? '',
                // 'SenderAddCity'              => strtoupper($shipper->city ?? 'NEW DELHI'),
                // 'SenderAddState'             => strtoupper($shipper->state ?? 'DELHI'),
                // 'SenderAddPostcode'          => $shipper->pincode ?? '110037',
                // 'SenderPhone'                => $shipper->phone_number ?? '01146122222',
                // 'SenderEmail'                => $shipper->email ?? 'abc@abc.com',
                // 'SenderFax'                  => '',
                // 'SenderKycType'              => $shipper->kyc_type ?? 'Passport',
                // 'SenderKycNumber'            => $shipper->kyc_number ?? '',
                // 'SenderReceivingCountryTaxID' => '',
                'SenderName' => 'Ved',
                'SenderCompanyName' => 'United Worldwide Couriers Pvt Ltd',
                'SenderCountryCode' => 'IN',
                'SenderAdd1' => 'BUILDING NO 1 BYPASS ROAD',
                'SenderAdd2' => 'MAHIPALPUR',
                'SenderAdd3' => '',
                'SenderAddCity' => 'NEW DELHI',
                'SenderAddState' => 'DELHI',
                'SenderAddPostcode' => '110037',
                'SenderPhone' => '01146122222',
                'SenderEmail' => 'abc@abc.com',
                'SenderFax' => '',
                'SenderKycType' => 'Passport',
                'SenderKycNumber' => 'P00001',
                'SenderReceivingCountryTaxID' => '',
            ],
            'ReceiverDetails' => [
                'ReceiverName' => $consignee->consignee_name ?? ($consignee->contact_person ?? 'Consignee'),
                'ReceiverCompanyName' => $consignee->consignee_name ?? ($consignee->contact_person ?? ''),
                // 'ReceiverCountryCode'   => $consigneeCountryCode,
                'ReceiverCountryCode' => 'GB',
                'ReceiverAdd1' => $consignee->address_line1 ?? '',
                'ReceiverAdd2' => $consignee->address_line2 ?? '',
                'ReceiverAdd3' => $consignee->address_line3 ?? '',
                'ReceiverAddCity' => $consignee->city ?? '',
                'ReceiverAddState' => $consignee->state ?? '',
                'ReceiverAddPostcode' => $consignee->zip_code ?? '',
                'ReceiverMobile' => $consignee->phone_number ?? '',
                'ReceiverPhone' => $consignee->phone_number ?? '',
                'ReceiverEmail' => $consignee->email ?? 'abc@abc.com',
                'ReceiverAddResidential' => 'N',
                'ReceiverFax' => '',
                'ReceiverKycType' => 'Passport',
                'ReceiverKycNumber' => '',
            ],
            'PackageDetails' => [
                'GoodsDescription' => $goodsDescription,
                'CustomValue' => (float) $customValue,
                'CustomCurrencyCode' => $currencyCode,
                'InsuranceValue' => 0.00,
                'InsuranceCurrencyCode' => $currencyCode,
                'ShipmentTerm' => '',
                'GoodsOriginCountryCode' => 'IN',
                'Weight' => (float) $totalWeight,
                'WeightMeasurement' => 'KG',
                'NoOfItems' => (int) $noOfItems,
                'CubicL' => (float) $cubicL,
                'CubicW' => (float) $cubicW,
                'CubicH' => (float) $cubicH,
                'CubicWeight' => 0,
                'ServiceTypeName' => $serviceTypeName,
                'NetworkCode' => $this->getPostShippingNetworkCode($serviceTypeName),
                'BookPickUP' => false,
                'SenderRef1' => $shipper->awb_number ?? ('TEST-SHIPMENT-'.$shipper->id),
                'BusinessType' => 'B2B',
                'ShipmentResponseItem' => $shipmentResponseItems,
                'CODAmount' => 0,
                'CODCurrencyCode' => $currencyCode,
                'DeadWeight' => (float) $totalWeight,
                'ReasonExport' => 'Sale',
                'OrderNumber' => $orderNumber,
                'Incoterms' => $incoterms,
            ],
            'PickupDetails' => [
                'ReadyTime' => $readyTime,
                'CloseTime' => $closeTime,
                'SpecialInstructions' => 'Call before pickup',
                'Address1' => $shipper->address_line1 ?? '',
                'Address2' => $shipper->address_line2 ?? '',
                'Address3' => $shipper->address_line3 ?? '',
                'AddressCity' => strtoupper($shipper->city ?? 'NEW DELHI'),
                'AddressState' => strtoupper($shipper->state ?? 'DELHI'),
                'AddressPostalCode' => $shipper->pincode ?? '110037',
                'AddressCountryCode' => 'IN',
            ],
        ];

        // PostShipping expects an array of shipment objects.
        // The fixed ThirdPartyToken is also returned so the caller can
        // send it as the API-Key request header.
        return [
            'success' => true,
            'payload' => [$shipmentObject],
            'third_party_token' => $thirdPartyToken,
        ];
    }

    /**
     * Call the PostShipping API to create a shipment.
     * Endpoint: https://api.postshipping.com/api2/shipments
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */


    /**
     * Call the PostShipping API to create a shipment.
     * Endpoint: https://api.postshipping.com/api2/shipments
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    private function callPostShippingApiFromDb($shipper)
    {
        try {
            $payloadResult = $this->buildPostShippingPayloadFromDb($shipper);
            if (! $payloadResult['success']) {
                return [
                    'success' => false,
                    'message' => $payloadResult['message'] ?? 'Failed to build PostShipping payload.',
                ];
            }

            $payload = $payloadResult['payload'];
            // The ThirdPartyToken is sent inside the request body.
            // A SEPARATE api_token is sent as the "token" request header for authentication.
            $apiToken = config('services.postshipping.api_token');
            $baseUrl = rtrim(config('services.postshipping.base_url'), '/');
            $endpoint = config('services.postshipping.endpoint', '/api2/shipments');
            $url = $baseUrl.$endpoint;
            $timeout = (int) config('services.postshipping.timeout', 60);

            \Log::info('PostShipping payload for shipper #'.$shipper->id.': '.substr(json_encode($payload), 0, 2000));

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Connection' => 'keep-alive',
            ];
            if (! empty($apiToken)) {
                $headers['token'] = $apiToken;
            }

            $response = Http::withHeaders($headers)
                ->withOptions(['verify' => false])
                ->timeout($timeout)
                ->post($url, $payload);

            $apiResponse = $response->json();

            if (! $response->successful()) {
                $errorMessage = 'PostShipping API returned error.';
                if (is_array($apiResponse)) {
                    if (isset($apiResponse['error'])) {
                        $errorMessage = is_string($apiResponse['error']) ? $apiResponse['error'] : json_encode($apiResponse['error']);
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $apiResponse['message'];
                    } elseif (isset($apiResponse['errors'])) {
                        $errorMessage = is_string($apiResponse['errors']) ? $apiResponse['errors'] : json_encode($apiResponse['errors']);
                    }
                    if (isset($apiResponse['details']) && is_array($apiResponse['details']) && ! empty($apiResponse['details'])) {
                        $errorMessage .= ' — '.implode('; ', $apiResponse['details']);
                    }
                }
                \Log::error('PostShipping API failed: '.$errorMessage.' | Status: '.$response->status().' | Body: '.$response->body());

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $apiResponse,
                    'request_payload' => $payload,
                    'status_code' => $response->status(),
                ];
            }

            \Log::info('PostShipping response for shipper #'.$shipper->id.': '.substr($response->body(), 0, 2000));

            // The DPD/PostShipping API may return HTTP 200 but still reject the
            // shipment by embedding an error inside the "ErrMessage" field of
            // each consignment object (e.g. "Service Denied (1021) - ...").
            // Detect this so the caller treats it as a failure instead of success.
            $errMessage = $this->extractPostShippingErrorMessage($apiResponse);
            if ($errMessage !== null) {
                \Log::error('PostShipping API rejected shipment (ErrMessage): '.$errMessage.' | Body: '.$response->body());

                return [
                    'success' => false,
                    'message' => 'PostShipping API rejected shipment: '.$errMessage,
                    'data' => $apiResponse,
                    'request_payload' => $payload,
                    'status_code' => $response->status(),
                ];
            }

            return [
                'success' => true,
                'message' => 'PostShipping shipment created successfully.',
                'data' => $apiResponse,
                'request_payload' => $payload,
            ];
        } catch (\Exception $e) {
            \Log::error('PostShipping API call failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'PostShipping API call failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Detect an embedded error in a PostShipping API response.
     *
     * The DPD/PostShipping API sometimes returns HTTP 200 but rejects the
     * shipment by placing an "ErrMessage" string inside each consignment
     * object of the response array. This method scans the response for any
     * non-empty ErrMessage and returns the first one found (trimmed), or
     * null when the response is genuinely successful.
     *
     * Supported response shapes:
     *   - Indexed array of consignments: [ {ErrMessage: "..."}, ... ]
     *   - Single object: {ErrMessage: "..."}
     *   - Nested under a "data" / "shipments" key
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */


    /**
     * Detect an embedded error in a PostShipping API response.
     *
     * The DPD/PostShipping API sometimes returns HTTP 200 but rejects the
     * shipment by placing an "ErrMessage" string inside each consignment
     * object of the response array. This method scans the response for any
     * non-empty ErrMessage and returns the first one found (trimmed), or
     * null when the response is genuinely successful.
     *
     * Supported response shapes:
     *   - Indexed array of consignments: [ {ErrMessage: "..."}, ... ]
     *   - Single object: {ErrMessage: "..."}
     *   - Nested under a "data" / "shipments" key
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */
    private function extractPostShippingErrorMessage($apiResponse)
    {
        if (! is_array($apiResponse)) {
            return null;
        }

        // Candidate containers that may hold consignment objects.
        $containers = [];

        // Top-level indexed array of consignments.
        $hasStringKeys = false;
        foreach (array_keys($apiResponse) as $k) {
            if (is_string($k)) {
                $hasStringKeys = true;
                break;
            }
        }
        if (! $hasStringKeys) {
            $containers[] = $apiResponse;
        }

        // Common nested keys.
        foreach (['data', 'shipments', 'Shipment', 'Shipments', 'consignment', 'consignments'] as $key) {
            if (isset($apiResponse[$key]) && is_array($apiResponse[$key])) {
                $containers[] = $apiResponse[$key];
            }
        }

        // If the response itself looks like a single consignment object.
        if (isset($apiResponse['ErrMessage'])) {
            $containers[] = [$apiResponse];
        }

        foreach ($containers as $container) {
            foreach ($container as $item) {
                if (! is_array($item)) {
                    continue;
                }
                if (! empty($item['ErrMessage']) && is_string($item['ErrMessage'])) {
                    $msg = trim($item['ErrMessage']);
                    if ($msg !== '') {
                        return $msg;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract a tracking/reference number from a PostShipping API response.
     *
     * The documented response format is an array of shipment objects, each
     * containing: ShipmentNumber, AlternateRef, LabelURL, ErrMessage, AcccountCode.
     * We also keep fallbacks for other possible shapes (top-level, nested under
     * "data", or wrapped under "Shipments") for resilience.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */


    /**
     * Extract a tracking/reference number from a PostShipping API response.
     *
     * The documented response format is an array of shipment objects, each
     * containing: ShipmentNumber, AlternateRef, LabelURL, ErrMessage, AcccountCode.
     * We also keep fallbacks for other possible shapes (top-level, nested under
     * "data", or wrapped under "Shipments") for resilience.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */
    private function extractPostShippingTrackingNumber($apiResponse)
    {
        if (! is_array($apiResponse)) {
            return null;
        }

        // Priority keys for the documented PostShipping response format.
        $priorityKeys = ['ShipmentNumber', 'shipment_number', 'AlternateRef', 'alternate_ref'];
        $fallbackKeys = [
            'tracking_number', 'TrackingNumber', 'waybill_number', 'WaybillNumber',
            'awb_number', 'AwbNumber', 'consignment_number', 'ConsignmentNumber',
            'order_number', 'OrderNumber', 'waybill', 'Waybill',
            'reference', 'Reference', 'shipment_id', 'ShipmentId',
        ];
        $candidateKeys = array_merge($priorityKeys, $fallbackKeys);

        // Case A: The response itself is a list of shipment objects
        // (documented format: [{ ShipmentNumber, LabelURL, ... }])
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($candidateKeys as $key) {
                if (isset($apiResponse[0][$key]) && ! empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Check top-level keys (single shipment object returned directly)
        foreach ($candidateKeys as $key) {
            if (isset($apiResponse[$key]) && ! empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "data"
        $data = $apiResponse['data'] ?? null;
        if (is_array($data)) {
            // data is a list of shipments
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($candidateKeys as $key) {
                    if (isset($data[0][$key]) && ! empty($data[0][$key])) {
                        return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                    }
                }
            }
            // data is a single shipment object
            foreach ($candidateKeys as $key) {
                if (isset($data[$key]) && ! empty($data[$key])) {
                    return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                }
            }
        }

        // Case D: Nested under "Shipments" / "shipments" / "Shipment" / "shipment"
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment'] as $wrapKey) {
            if (isset($apiResponse[$wrapKey])) {
                $wrap = $apiResponse[$wrapKey];
                if (is_array($wrap)) {
                    $first = isset($wrap[0]) ? $wrap[0] : $wrap;
                    if (is_array($first)) {
                        foreach ($candidateKeys as $key) {
                            if (isset($first[$key]) && ! empty($first[$key])) {
                                return is_string($first[$key]) ? $first[$key] : (string) $first[$key];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract the LabelURL from a PostShipping API response.
     *
     * The documented response format is an array of shipment objects, each
     * containing a LabelURL field with the shipping label download link.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */


    /**
     * Extract the LabelURL from a PostShipping API response.
     *
     * The documented response format is an array of shipment objects, each
     * containing a LabelURL field with the shipping label download link.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */
    private function extractPostShippingLabelUrl($apiResponse)
    {
        if (! is_array($apiResponse)) {
            return null;
        }

        $labelKeys = ['LabelURL', 'label_url', 'LabelUrl', 'labelurl', 'Label', 'label'];

        // Case A: The response itself is a list of shipment objects
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($labelKeys as $key) {
                if (isset($apiResponse[0][$key]) && ! empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Check top-level keys
        foreach ($labelKeys as $key) {
            if (isset($apiResponse[$key]) && ! empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "data"
        $data = $apiResponse['data'] ?? null;
        if (is_array($data)) {
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($labelKeys as $key) {
                    if (isset($data[0][$key]) && ! empty($data[0][$key])) {
                        return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                    }
                }
            }
            foreach ($labelKeys as $key) {
                if (isset($data[$key]) && ! empty($data[$key])) {
                    return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                }
            }
        }

        // Case D: Nested under "Shipments" / "shipments"
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment'] as $wrapKey) {
            if (isset($apiResponse[$wrapKey])) {
                $wrap = $apiResponse[$wrapKey];
                if (is_array($wrap)) {
                    $first = isset($wrap[0]) ? $wrap[0] : $wrap;
                    if (is_array($first)) {
                        foreach ($labelKeys as $key) {
                            if (isset($first[$key]) && ! empty($first[$key])) {
                                return is_string($first[$key]) ? $first[$key] : (string) $first[$key];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Flying Tigers API (UNITED ECO POST)
    |--------------------------------------------------------------------------
    | Endpoint: https://app.flyingtigers.in/api/Shipment/CustomerBookingAPI
    | Auth headers: ClientCode, UserCode, AuthToken
    |
    */

    /**
     * Determine if a shipping method should be routed to the Flying Tigers API.
     * Triggered for UNITED ECO POST shipments.
     *
     * @param  string|null  $shippingMethod
     * @return bool
     */


    /**
     * Generate a Bearer token using ShipUniversal HTTP Basic authentication.
     *
     * @return array ['success' => bool, 'token' => string|null, 'message' => string|null]
     */
    private function getShipUniversalToken()
    {
        try {
            $tokenUrl = config('services.shipuniversal.token_url');
            $username = config('services.shipuniversal.username');
            $password = config('services.shipuniversal.password');
            $grantType = config('services.shipuniversal.grant_type', 'client_credentials');
            $timeout = (int) config('services.shipuniversal.timeout', 60);

            if (empty($tokenUrl) || empty($username) || empty($password)) {
                return [
                    'success' => false,
                    'message' => 'ShipUniversal credentials are not configured.',
                ];
            }

            $response = Http::withBasicAuth($username, $password)
                ->acceptJson()
                ->asForm()
                ->timeout($timeout)
                ->post($tokenUrl, [
                    'grant_type' => $grantType,
                ]);

            if (! $response->successful()) {
                $responseData = $response->json();
                $message = $this->extractShipUniversalErrorMessage(
                    $responseData,
                    'ShipUniversal token generation failed.'
                );

                \Log::error(
                    'ShipUniversal token generation failed. Status: '
                    .$response->status().' | Body: '.$response->body()
                );

                return ['success' => false, 'message' => $message];
            }

            $tokenData = $response->json();
            $token = $this->findShipUniversalResponseValue(
                $tokenData,
                ['access_token', 'accessToken', 'token', 'Token', 'bearer_token', 'bearerToken']
            );

            // Some token endpoints return a plain token instead of JSON.
            if (empty($token)) {
                $rawToken = trim($response->body(), " \t\n\r\0\x0B\"");
                if ($rawToken !== '' && ! str_starts_with($rawToken, '{') && ! str_starts_with($rawToken, '[')) {
                    $token = $rawToken;
                }
            }

            if (! is_scalar($token) || trim((string) $token) === '') {
                \Log::error('ShipUniversal token response did not contain a token.');

                return [
                    'success' => false,
                    'message' => 'No bearer token found in ShipUniversal authentication response.',
                ];
            }

            return ['success' => true, 'token' => trim((string) $token)];
        } catch (\Exception $e) {
            \Log::error('ShipUniversal token generation exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'ShipUniversal token generation failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Build the ShipUniversal shipment-create payload from stored shipment data.
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */


    /**
     * Build the ShipUniversal shipment-create payload from stored shipment data.
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */
    private function buildShipUniversalPayloadFromDb($shipper)
    {
        $consignee = $shipper->consigneeInfo;
        if (! $consignee) {
            return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
        }

        $packages = $shipper->packageDimensions;
        if ($packages->isEmpty()) {
            return ['success' => false, 'message' => 'No package dimensions found for this shipment.'];
        }

        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();
        $invoiceItems = $invoice
            ? ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get()
            : collect();
        $csbInformation = $shipper->csbInformation;

        $shippingMethod = $this->resolveShippingMethod($shipper);
        $courierService = ! empty($shipper->service_id)
            ? CourierService::find($shipper->service_id)
            : null;
        if (! $courierService) {
            $courierService = $this->findCourierService($shippingMethod, $shipper->id);
        }

        $serviceCode = trim((string) (
            $courierService->service_code
            ?? $courierService->scode
            ?? ''
        ));
        if ($serviceCode === '') {
            return [
                'success' => false,
                'message' => 'ShipUniversal service code is not configured for the selected courier service.',
            ];
        }

        $kycType = match ($shipper->kyc_type) {
            'GST (Normal)' => 'GSTIN (Normal)',
            'Aadhar Card' => 'Aadhaar Number',
            'PAN Card' => 'PAN Number',
            default => (string) ($shipper->kyc_type ?? ''),
        };

        $packageDetails = $packages->map(function ($package) {
            return [
                'Length' => (float) ($package->length_cm ?? 0),
                'Width' => (float) ($package->width_cm ?? 0),
                'Height' => (float) ($package->height_cm ?? 0),
                'ActualWeight' => (float) ($package->actual_weight_kg ?? 0),
            ];
        })->values()->all();

        $totalPackageWeight = (float) $packages->sum(function ($package) {
            return (float) ($package->actual_weight_kg ?? 0);
        });
        $totalItemQuantity = (float) $invoiceItems->sum(function ($item) {
            return max(0, (float) ($item->qty ?? 0));
        });
        $pieceWeight = $totalItemQuantity > 0
            ? round($totalPackageWeight / $totalItemQuantity, 3)
            : round($totalPackageWeight, 3);

        $productDetails = $invoiceItems->map(function ($item) use ($pieceWeight) {
            return [
                'BoxNo' => (string) ($item->box_no ?? 1),
                'Description' => (string) ($item->description ?? ''),
                'HSNCode' => (string) ($item->hs_code ?? ''),
                'HTSCode' => (string) ($item->hts_code ?? ''),
                'UnitType' => (string) ($item->unit_type ?? 'PCS'),
                'Qty' => (float) ($item->qty ?? 1),
                'UnitRate' => (float) ($item->unit_rate ?? 0),
                'ShipPieceIGST' => (float) ($item->igst_percentage ?? 0),
                'PieceWt' => $pieceWeight,
            ];
        })->values()->all();

        if (empty($productDetails)) {
            $productDetails[] = [
                'BoxNo' => '1',
                'Description' => 'General Merchandise',
                'HSNCode' => '',
                'HTSCode' => '',
                'UnitType' => 'PCS',
                'Qty' => 1,
                'UnitRate' => (float) ($invoice->invoice_amount ?? 0),
                'ShipPieceIGST' => 0,
                'PieceWt' => $pieceWeight,
            ];
        }

        $originType = strtoupper(trim((string) ($consignee->origin_type ?? '')));
        $csbType = in_array($originType, ['CSB V', 'CSB 5'], true) ? 'CSB 5' : 'CSB 4';
        $bondUtIgst = trim((string) ($csbInformation->bond_ut_igst ?? ''));
        $isIgstPaid = str_contains(strtoupper($bondUtIgst), 'IGST');
        $igstAmount = (float) $invoiceItems->sum(function ($item) {
            return (float) ($item->igst_amount ?? 0);
        });

        $invoiceDate = $invoice && $invoice->invoice_date
            ? $invoice->invoice_date->format('Y-m-d').'T00:00:00Z'
            : now()->format('Y-m-d').'T00:00:00Z';
        $referenceNumber = trim((string) ($invoice->reference_number ?? ''));
        if ($referenceNumber === '') {
            $referenceNumber = (string) ($shipper->awb_number ?? ('SU-'.$shipper->id));
        }

        $senderAddressLine1 = trim((string) ($shipper->address_line1 ?? ''));
        $senderAddressLine2 = trim((string) ($shipper->address_line2 ?? ''));
        if ($senderAddressLine2 === '') {
            $senderAddressLine2 = $senderAddressLine1;
        }

        $receiverAddressLine1 = trim((string) ($consignee->address_line1 ?? ''));
        $receiverAddressLine2 = trim((string) ($consignee->address_line2 ?? ''));
        if ($receiverAddressLine2 === '') {
            $receiverAddressLine2 = $receiverAddressLine1;
        }

        $payload = [
            'AccountCode' => config('services.shipuniversal.account_code', 'SU0119'),
            'Sender' => [
                'SenderName' => (string) ($shipper->company_name ?? $shipper->contact_person ?? 'Shipper'),
                'SenderContactPerson' => (string) ($shipper->contact_person ?? $shipper->company_name ?? 'Shipper'),
                'SenderAddressLine1' => $senderAddressLine1,
                'SenderAddressLine2' => $senderAddressLine2,
                'SenderAddressLine3' => (string) ($shipper->address_line3 ?? ''),
                'SenderPincode' => (string) ($shipper->pincode ?? ''),
                'SenderCity' => (string) ($shipper->city ?? ''),
                'SenderState' => (string) ($shipper->state ?? ''),
                'SenderTelephone' => (string) ($shipper->phone_number ?? ''),
                'SenderEmailId' => (string) ($shipper->email ?? ''),
                'KYCType' => $kycType,
                'KYCNo' => (string) ($shipper->kyc_number ?? ''),
            ],
            'Receiver' => [
                'ReceiverName' => (string) ($consignee->consignee_name ?? $consignee->contact_person ?? 'Consignee'),
                'ReceiverContactPerson' => (string) ($consignee->contact_person ?? $consignee->consignee_name ?? 'Consignee'),
                'ReceiverAddressLine1' => $receiverAddressLine1,
                'ReceiverAddressLine2' => $receiverAddressLine2,
                'ReceiverAddressLine3' => (string) ($consignee->address_line3 ?? ''),
                'ReceiverZipcode' => (string) ($consignee->zip_code ?? ''),
                'ReceiverCity' => (string) ($consignee->city ?? ''),
                'ReceiverState' => (string) ($consignee->state ?? ''),
                'ReceiverCountry' => $this->getShipUniversalCountryCode($consignee->delivery_destination),
                'ReceiverTelephone' => (string) ($consignee->phone_number ?? ''),
                'ReceiverEmailid' => (string) ($consignee->email ?? ''),
                'VatId' => '',
            ],
            'ServiceDetails' => [
                'Service' => $serviceCode,
                'GoodsType' => 'NDox',
                'PackageType' => 'PACKAGE',
            ],
            'PackageDetails' => [
                'PackageDetail' => $packageDetails,
            ],
            'AdditionalDetails' => [
                'IsThirdParty' => true,
                'ProductDetails' => $productDetails,
                'InvoiceCurrency' => (string) ($invoice->invoice_currency ?? 'INR'),
                'InvoiceNo' => (string) ($invoice->invoice_number ?? ''),
                'InvoiceDate' => $invoiceDate,
                'TermsOfSale' => (string) ($invoice->incoterms ?? 'FOB'),
                'ReasonForExport' => 'SALE',
                'FreightCharge' => 0,
                'InsuranceCharge' => 0,
                'CSB_Type' => $csbType,
                'CustomerRefNo' => $referenceNumber,
                'DeliveryConfirmation' => 'No',
                'DutyTax' => 'DDU',
                'DutiesAccountNo' => '',
                'TransactionId' => '',
                'ShipperImage' => '',
                'ShipperKYC' => '',
                'FileName' => '',
                'IECNo' => (string) ($csbInformation->iec_code ?? ''),
                'ADCode' => (string) ($csbInformation->ad_code ?? ''),
                'BankType' => 'G',
                'BankAccount' => (string) ($csbInformation->bank_account_number ?? ''),
                'NFEI' => false,
                'Ecom' => $this->shipUniversalValueIsYes($csbInformation->ecommerce ?? null),
                'MEIS' => $this->shipUniversalValueIsYes($csbInformation->scheme ?? null),
                'BoundUT' => $bondUtIgst !== '' ? $bondUtIgst : 'NA',
                'IGSTPaid' => $isIgstPaid ? 'Yes' : 'No',
                'IGSTAmount' => $isIgstPaid ? $igstAmount : 0,
            ],
        ];

        return ['success' => true, 'payload' => $payload];
    }

    /**
     * Create a shipment through ShipUniversal using a generated Bearer token.
     */


    /**
     * Create a shipment through ShipUniversal using a generated Bearer token.
     */
    private function callShipUniversalApiFromDb($shipper)
    {
        try {
            $tokenResult = $this->getShipUniversalToken();
            if (! $tokenResult['success']) {
                return $tokenResult;
            }

            $payloadResult = $this->buildShipUniversalPayloadFromDb($shipper);
            if (! $payloadResult['success']) {
                return $payloadResult;
            }

            $shipmentUrl = config('services.shipuniversal.shipment_url');
            $timeout = (int) config('services.shipuniversal.timeout', 60);
            $payload = $payloadResult['payload'];

            if (empty($shipmentUrl)) {
                return [
                    'success' => false,
                    'message' => 'ShipUniversal shipment URL is not configured.',
                    'request_payload' => $payload,
                ];
            }

            \Log::info(
                'ShipUniversal shipment request for shipper #'.$shipper->id,
                ['service' => $payload['ServiceDetails']['Service'] ?? null]
            );

            $response = Http::withToken($tokenResult['token'])
                ->acceptJson()
                ->asJson()
                ->timeout($timeout)
                ->post($shipmentUrl, $payload);

            $apiResponse = $response->json();
            if (! is_array($apiResponse)) {
                $apiResponse = ['raw_body' => $response->body()];
            }

            if (! $response->successful()) {
                $message = $this->extractShipUniversalErrorMessage(
                    $apiResponse,
                    'ShipUniversal shipment creation failed.'
                );
                \Log::error(
                    'ShipUniversal shipment creation failed. Status: '
                    .$response->status().' | Body: '.$response->body()
                );

                return [
                    'success' => false,
                    'message' => $message,
                    'data' => $apiResponse,
                    'request_payload' => $payload,
                    'status_code' => $response->status(),
                ];
            }

            $status = $this->findShipUniversalResponseValue($apiResponse, ['Status', 'status', 'Success', 'success']);
            if ($status === false || (is_string($status) && in_array(strtoupper($status), ['ERROR', 'FAILED', 'FAILURE', 'FALSE'], true))) {
                return [
                    'success' => false,
                    'message' => $this->extractShipUniversalErrorMessage(
                        $apiResponse,
                        'ShipUniversal API returned an error status.'
                    ),
                    'data' => $apiResponse,
                    'request_payload' => $payload,
                ];
            }

            return [
                'success' => true,
                'message' => 'ShipUniversal shipment created successfully.',
                'data' => $apiResponse,
                'request_payload' => $payload,
            ];
        } catch (\Exception $e) {
            \Log::error('ShipUniversal API call failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'ShipUniversal API call failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Find the first matching scalar value anywhere in a ShipUniversal response.
     */


    /**
     * Find the first matching scalar value anywhere in a ShipUniversal response.
     */
    private function findShipUniversalResponseValue($value, array $keys)
    {
        if (! is_array($value)) {
            return null;
        }

        $normalizedKeys = array_map('strtolower', $keys);
        foreach ($value as $key => $child) {
            if (in_array(strtolower((string) $key), $normalizedKeys, true) && is_scalar($child)) {
                return $child;
            }
        }

        foreach ($value as $child) {
            if (is_array($child)) {
                $found = $this->findShipUniversalResponseValue($child, $keys);
                if ($found !== null && $found !== '') {
                    return $found;
                }
            }
        }

        return null;
    }


    private function extractShipUniversalTrackingNumber($apiResponse)
    {
        $value = $this->findShipUniversalResponseValue($apiResponse, [
            'AwbNo', 'AwbNumber', 'AWBNumber', 'awb_number', 'awb',
            'TrackingNumber', 'tracking_number', 'WaybillNumber', 'waybill_number',
            'ConsignmentNumber', 'consignment_number', 'ShipmentNumber', 'shipment_number',
        ]);

        return is_scalar($value) && trim((string) $value) !== '' ? trim((string) $value) : null;
    }


    private function extractShipUniversalLabelUrl($apiResponse)
    {
        $value = $this->findShipUniversalResponseValue($apiResponse, [
            'AirwaybillUrl', 'BoxlabelUrl', 'LabelUrl', 'label_url', 'LabelURL',
            'LabelLink', 'label_link', 'PdfUrl', 'pdf_url', 'LabelBase64', 'label_base64',
        ]);

        return is_scalar($value) && trim((string) $value) !== '' ? trim((string) $value) : null;
    }


    private function extractShipUniversalErrorMessage($apiResponse, $fallback)
    {
        $message = $this->findShipUniversalResponseValue($apiResponse, [
            'ErrorMessage', 'error_message', 'Error', 'error', 'Message', 'message',
            'Description', 'description', 'Detail', 'detail',
        ]);

        if (is_scalar($message) && trim((string) $message) !== '') {
            return trim((string) $message);
        }

        return $fallback;
    }


    private function shipUniversalValueIsYes($value)
    {
        return in_array(strtolower(trim((string) $value)), ['1', 'yes', 'true', 'y'], true);
    }


    private function getShipUniversalCountryCode($destination)
    {
        $destination = strtoupper(trim((string) $destination));
        if (preg_match('/^[A-Z]{2}$/', $destination)) {
            return $destination;
        }

        $countries = [
            'UNITED STATES' => 'US',
            'UNITED STATE' => 'US',
            'USA' => 'US',
            'UNITED KINGDOM' => 'GB',
            'GREAT BRITAIN' => 'GB',
            'UK' => 'GB',
            'CANADA' => 'CA',
            'AUSTRALIA' => 'AU',
            'INDIA' => 'IN',
            'CHINA' => 'CN',
            'RUSSIA' => 'RU',
            'SRI LANKA' => 'LK',
            'SRILANKA' => 'LK',
            'UNITED ARAB EMIRATES' => 'AE',
            'UAE' => 'AE',
            'GERMANY' => 'DE',
            'FRANCE' => 'FR',
            'ITALY' => 'IT',
            'SPAIN' => 'ES',
            'NETHERLANDS' => 'NL',
            'SINGAPORE' => 'SG',
            'MALAYSIA' => 'MY',
        ];

        foreach ($countries as $name => $code) {
            if (str_contains($destination, $name)) {
                return $code;
            }
        }

        // Destination dropdown values commonly begin with an ISO code (e.g. "US-").
        if (preg_match('/^([A-Z]{2})\s*[-–:]/', $destination, $matches)) {
            return $matches[1];
        }

        return 'US';
    }

    /*
    |--------------------------------------------------------------------------
    | Overseas Logistic API (UNITED CANADA DDP / UNITED CANADA E-COMMERCE)
    |--------------------------------------------------------------------------
    | Token URL:    https://api.overseaslogistic.com/token
    | Shipment URL: https://api.overseaslogistic.com/api/shipment/create
    |
    | Two-step flow:
    |   1. POST /token (OAuth2 client_credentials grant) -> Bearer access_token
    |   2. POST /api/shipment/create with Authorization: Bearer <token>
    |
    | The /token endpoint is an OAuth2 token server. It requires a
    | client_credentials grant with client_id/client_secret sent as
    | form-encoded data (application/x-www-form-urlencoded). Sending
    | username/password as JSON returns "invalid_client".
    |
    | The same endpoint/payload is used for both DDP and E-Commerce variants;
    | the Service field inside ServiceDetails differentiates the service.
    |
    */

    /**
     * Generate a Bearer token from the Overseas Logistic /token endpoint.
     *
     * Uses the OAuth2 client_credentials grant type. The username configured
     * in services.overseas.username is sent as client_id and the password as
     * client_secret, form-encoded (NOT JSON).
     *
     * @return array ['success' => bool, 'token' => string|null, 'message' => string|null]
     */


    /*
    |--------------------------------------------------------------------------
    | Overseas Logistic API (UNITED CANADA DDP / UNITED CANADA E-COMMERCE)
    |--------------------------------------------------------------------------
    | Token URL:    https://api.overseaslogistic.com/token
    | Shipment URL: https://api.overseaslogistic.com/api/shipment/create
    |
    | Two-step flow:
    |   1. POST /token (OAuth2 client_credentials grant) -> Bearer access_token
    |   2. POST /api/shipment/create with Authorization: Bearer <token>
    |
    | The /token endpoint is an OAuth2 token server. It requires a
    | client_credentials grant with client_id/client_secret sent as
    | form-encoded data (application/x-www-form-urlencoded). Sending
    | username/password as JSON returns "invalid_client".
    |
    | The same endpoint/payload is used for both DDP and E-Commerce variants;
    | the Service field inside ServiceDetails differentiates the service.
    |
    */

    /**
     * Generate a Bearer token from the Overseas Logistic /token endpoint.
     *
     * Uses the OAuth2 client_credentials grant type. The username configured
     * in services.overseas.username is sent as client_id and the password as
     * client_secret, form-encoded (NOT JSON).
     *
     * @return array ['success' => bool, 'token' => string|null, 'message' => string|null]
     */
    private function getOverseasLogisticToken()
    {
        try {
            $tokenUrl = config('services.overseas.token_url');
            $clientId = config('services.overseas.username');
            $clientSec = config('services.overseas.password');
            $timeout = (int) config('services.overseas.timeout', 60);

            if (empty($tokenUrl) || empty($clientId) || empty($clientSec)) {
                return [
                    'success' => false,
                    'message' => 'Overseas Logistic credentials are not configured.',
                ];
            }

            // OAuth2 client_credentials grant — must be form-encoded.
            $response = Http::asForm()
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->timeout($timeout)
                ->post($tokenUrl, [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSec,
                ]);

            if (! $response->successful()) {
                $errorBody = $response->json() ?: $response->body();
                $errorMessage = 'Overseas Logistic token generation failed.';
                if (is_array($errorBody)) {
                    if (isset($errorBody['error_description'])) {
                        $errorMessage = $this->overseasValueToString($errorBody['error_description']);
                    } elseif (isset($errorBody['error'])) {
                        $errorMessage = $this->overseasValueToString($errorBody['error']);
                    } elseif (isset($errorBody['message'])) {
                        $errorMessage = $this->overseasValueToString($errorBody['message']);
                    }
                }
                \Log::error('Overseas Logistic token generation failed: '.$errorMessage.' | Status: '.$response->status().' | Body: '.$response->body());

                return [
                    'success' => false,
                    'message' => $errorMessage,
                ];
            }

            $tokenData = $response->json();
            $bearerToken = null;

            // Extract token from various possible response formats.
            // The OAuth2 server returns "access_token" at the top level.
            if (isset($tokenData['access_token'])) {
                $bearerToken = $tokenData['access_token'];
            } elseif (isset($tokenData['data']) && isset($tokenData['data']['access_token'])) {
                $bearerToken = $tokenData['data']['access_token'];
            } elseif (isset($tokenData['token'])) {
                $bearerToken = $tokenData['token'];
            } elseif (isset($tokenData['data']) && isset($tokenData['data']['token'])) {
                $bearerToken = $tokenData['data']['token'];
            } elseif (isset($tokenData['Token'])) {
                $bearerToken = $tokenData['Token'];
            }

            if (! $bearerToken) {
                \Log::error('Overseas Logistic: No token found in response. Response: '.json_encode($tokenData));

                return [
                    'success' => false,
                    'message' => 'No bearer token found in Overseas Logistic authentication response.',
                ];
            }

            \Log::info('Overseas Logistic token generated successfully.');

            return [
                'success' => true,
                'token' => $bearerToken,
            ];
        } catch (\Exception $e) {
            \Log::error('Overseas Logistic token generation exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Overseas Logistic token generation failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Build the Overseas Logistic shipment-create payload from database records.
     * Payload structure mirrors the documented /api/shipment/create format.
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */


    /**
     * Build the Overseas Logistic shipment-create payload from database records.
     * Payload structure mirrors the documented /api/shipment/create format.
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */
    private function buildOverseasLogisticPayloadFromDb($shipper)
    {

        $consignee = $shipper->consigneeInfo;
        if (! $consignee) {
            \Log::warning('buildOverseasLogisticPayloadFromDb: No consignee found for shipper #'.$shipper->id);

            return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
        }

        $packages = $shipper->packageDimensions;
        if ($packages->isEmpty()) {
            \Log::warning('buildOverseasLogisticPayloadFromDb: No packages found for shipper #'.$shipper->id);

            return ['success' => false, 'message' => 'No package dimensions found for this shipment.'];
        }

        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();

        if ($shipper->kyc_type == 'GST (Normal)') {
            $kycType_data = 'GSTIN (Normal)';
        } elseif ($shipper->kyc_type == 'Aadhar Card') {
            $kycType_data = 'Aadhaar Number';
        } elseif ($shipper->kyc_type == 'PAN Card') {
            $kycType_data = 'PAN Number';
        } else {
            $kycType_data = $shipper->kyc_type ?? '';
        }

        // ---- Sender ----
        $senderName = $shipper->company_name ?? $shipper->contact_person ?? 'Shipper';
        $senderContact = $shipper->contact_person ?? $shipper->company_name ?? 'Shipper';
        $senderAddress1 = $shipper->address_line1 ?? '';
        $senderAddress2 = $shipper->address_line2 ?? '';
        $senderAddress3 = $shipper->address_line3 ?? '';
        $senderPincode = (string) ($shipper->pincode ?? '');
        $senderCity = $shipper->city ?? '';
        $senderState = $shipper->state ?? '';

        // ---- Validation: shipper state must not exceed 2 characters ----
        // The Overseas Logistic API expects a 2-letter state code (e.g. "GJ",
        // "MH"). If the shipper's state field contains more than 2 letters
        // (e.g. a full state name like "Gujarat"), shipment creation is blocked
        // here with an error so the API is never called with invalid data.
        // This applies to ALL Overseas Logistic shipments (Canada DDP /
        // E-Commerce and ARAMEX GPX / Australia).
        $senderStateTrimmed = trim((string) $senderState);
        if (strlen($senderStateTrimmed) > 2) {
            \Log::warning('buildOverseasLogisticPayloadFromDb: Shipper state exceeds 2 characters for shipper #'.$shipper->id.' | state="'.$senderStateTrimmed.'"');

            return [
                'success' => false,
                'message' => 'Shipper state must be a 2-letter code (e.g. "GJ", "MH"). The provided state "'.$senderStateTrimmed.'" is too long. Please update the shipper state to a 2-letter code and try again.',
            ];
        }

        $senderTelephone = (string) ($shipper->phone_number ?? '');
        $senderEmail = $shipper->email ?? '';
        // $kycType         = $shipper->kyc_type ?? 'GSTIN (Normal)';
        $kycType = $kycType_data;
        $kycNo = (string) ($shipper->kyc_number ?? '');

        // ---- Receiver ----
        // $receiverType      = $consignee->origin_type ? ucfirst(strtolower($consignee->origin_type)) : 'Business';
        // if($consignee->origin_type == "CSB IV") {
        //     $receiverType = "customer";
        // } else if($consignee->origin_type == "CSB V") {
        //     $receiverType = "Business";
        // }
        $receiverName = $consignee->consignee_name ?? $consignee->contact_person ?? 'Consignee';
        $receiverContact = $consignee->contact_person ?? $consignee->consignee_name ?? 'Consignee';
        $receiverAddress1 = $consignee->address_line1 ?? '';
        $receiverAddress2 = $consignee->address_line2 ?? '';
        $receiverAddress3 = $consignee->address_line3 ?? '';
        $receiverZipcode = (string) ($consignee->zip_code ?? '');
        $receiverCity = $consignee->city ?? '';
        $receiverState = $consignee->state ?? '';
        $receiverCountry = $this->getOverseasCountryCode($consignee->delivery_destination ?? '', 'CA');
        $receiverTelephone = (string) ($consignee->phone_number ?? '');
        $receiverEmail = $consignee->email ?? '';

        // ---- Service details ----
        // Resolve the courier service to get the Service code (e.g. CANADA_YVR_SELF).
        // The overseas payload is identical for every overseas service; only the
        // Service field inside ServiceDetails changes per service. We now prefer
        // the explicit shipper_info.service_id (stored at manifest time) to look
        // up the courier_services row directly — this is more reliable than the
        // fuzzy string matching below, which can pick the wrong service when
        // several overseas services share similar method names.
        $shippingMethod = $this->resolveShippingMethod($shipper);
        $courierService = null;

        // 1) Preferred path: resolve directly from shipper_info.service_id.
        if (! empty($shipper->service_id)) {
            $serviceById = CourierService::find($shipper->service_id);
            if ($serviceById && $this->isOverseasLogisticMethod($serviceById->method)) {
                $courierService = $serviceById;
                // Keep shipping_method in sync with the resolved service so the
                // DutyTax / CSB logic below and any logging use the real method.
                $shippingMethod = $serviceById->method;
            }
        }

        // 2) Fallback: legacy fuzzy match (for older rows where service_id is NULL
        //    or points at a non-overseas service).
        if (! $courierService) {
            $courierService = $this->findCourierService($shippingMethod, $shipper->id);
        }

        $serviceCode = $courierService ? ($courierService->service_code ?? $courierService->scode ?? '') : '';
        if (empty($serviceCode)) {
            // Fallback to a sensible default if the service code is missing.
            $serviceCode = 'CANADA_YVR_SELF';
        }

        // GoodsType: NDox (documents) vs NDox (non-documents). Default to NDox.
        $goodsType = 'NDox';
        $packageType = 'PACKAGE';

        // ---- Package details ----
        $packageDetail = [];
        foreach ($packages as $pkg) {
            $packageDetail[] = [
                'Length' => (float) ($pkg->length_cm ?? 0),
                'Width' => (float) ($pkg->width_cm ?? 0),
                'Height' => (float) ($pkg->height_cm ?? 0),
                'ActualWeight' => (float) ($pkg->actual_weight_kg ?? 0),
            ];
        }

        // ---- Additional details / product details ----
        $productDetails = [];
        if ($invoice) {
            $invoiceItems = ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get();
            foreach ($invoiceItems as $item) {
                $productDetails[] = [
                    'BoxNo' => (string) ($item->box_no ?? 1),
                    'Description' => $item->description ?? '',
                    'HSNCode' => (string) ($item->hs_code ?? ''),
                    'HTSCode' => (string) ($item->hts_code ?? ''),
                    'UnitType' => $item->unit_type ?? 'PCS',
                    'Qty' => (int) $item->qty,
                    'UnitRate' => (float) $item->unit_rate,
                    'ShipPieceIGST' => (float) ($item->igst_percentage ?? 0),
                    'PieceWt' => (float) ($item->amount > 0 && $item->qty > 0 ? round($item->amount / $item->qty, 3) : 0),
                ];
            }
        }

        // Fallback product detail when no invoice items exist.
        if (empty($productDetails)) {
            $productDetails[] = [
                'BoxNo' => '1',
                'Description' => 'General Merchandise',
                'HSNCode' => '',
                'HTSCode' => '',
                'UnitType' => 'PCS',
                'Qty' => 1,
                'UnitRate' => (float) ($invoice ? $invoice->invoice_amount : 0),
                'ShipPieceIGST' => 0.00,
                'PieceWt' => 0.3,
            ];
        }

        // Invoice / export details.
        $invoiceNo = $invoice ? ($invoice->invoice_number ?? '') : '';
        $invoiceDate = $invoice && $invoice->invoice_date
            ? $invoice->invoice_date->format('Y-m-d').'T00:00:00Z'
            : now()->format('Y-m-d').'T00:00:00Z';
        $invoiceCurrency = $invoice ? ($invoice->invoice_currency ?? 'INR') : 'INR';
        $termsOfSale = $invoice ? ($invoice->incoterms ?? 'FOB') : 'FOB';
        $customerRefNo = $invoice ? ($invoice->reference_number ?? '') : '';
        $transactionId = $shipper->awb_number ?? ('TXN-'.$shipper->id);

        // DutyTax: DDP for UNITED CANADA DDP, DDU otherwise (E-Commerce).
        $methodUpper = strtoupper(trim($shippingMethod));
        $dutyTax = str_contains($methodUpper, 'DDP') ? 'DDP' : 'DDU';

        // CSB type from customer csb_status (1..5 -> "CSB 1".."CSB 5"); default CSB 4.
        $customer = Customer::find($shipper->customer_id);
        $csbType = 'CSB 4';
        // if ($customer && $customer->csb_status) {
        //     // m chahata hu ki ek condition ho ki agar csb_status 1 h toh csbtype "csb 1" ho toh $csbType = "CSB 4" print ho agar csb_status 2 h toh csbtype "csb 2" h toh $csbType = "CSB 5" print ho

        //     if ($customer->csb_status == 1) {
        //         $csbType = 'CSB 4';
        //     } elseif ($customer->csb_status == 2) {
        //         $csbType = 'CSB 5';
        //     }
        //     print_r($csbType);
        // }

        if ($consignee->origin_type == 'CSB IV') {

            $csbType = 'CSB 4';
        } elseif ($consignee->origin_type == 'CSB V') {

            $csbType = 'CSB 5';
        }

        $payload = [
            'AccountCode' => config('services.overseas.account_code', 'PR-U02'),
            'Sender' => [
                'SenderName' => $senderName,
                'SenderContactPerson' => $senderContact,
                'SenderAddressLine1' => $senderAddress1,
                'SenderAddressLine2' => $senderAddress2,
                'SenderAddressLine3' => $senderAddress3,
                'SenderPincode' => $senderPincode,
                'SenderCity' => $senderCity,
                'SenderState' => $senderState,
                'SenderTelephone' => $senderTelephone,
                'SenderEmailId' => $senderEmail,
                'KYCType' => $kycType,
                'KYCNo' => $kycNo,
            ],
            'Receiver' => [
                'ReceiverType' => 'Business',
                'ReceiverName' => $receiverName,
                'ReceiverContactPerson' => $receiverContact,
                'ReceiverAddressLine1' => $receiverAddress1,
                'ReceiverAddressLine2' => $receiverAddress2,
                'ReceiverAddressLine3' => $receiverAddress3,
                'ReceiverZipcode' => $receiverZipcode,
                'ReceiverCity' => $receiverCity,
                'ReceiverState' => $receiverState,
                'ReceiverCountry' => $receiverCountry,
                'ReceiverTelephone' => $receiverTelephone,
                'ReceiverEmailid' => $receiverEmail,
                'VatId' => '',
            ],
            'ServiceDetails' => [
                'Service' => $serviceCode,
                'GoodsType' => $goodsType,
                'PackageType' => $packageType,
            ],
            'PackageDetails' => [
                'PackageDetail' => $packageDetail,
            ],
            'AdditionalDetails' => [
                'ProductDetails' => $productDetails,
                'InvoiceCurrency' => $invoiceCurrency,
                'InvoiceNo' => $invoiceNo,
                'InvoiceDate' => $invoiceDate,
                'TermsOfSale' => $termsOfSale,
                'ReasonForExport' => 'GIFT',
                'FreightCharge' => 0,
                'InsuranceCharge' => 0,
                'CSB_Type' => $csbType,
                'CustomerRefNo' => $customerRefNo,
                'DeliveryConfirmation' => 'No',
                'DutyTax' => $dutyTax,
                'DutiesAccountNo' => '',
                'TransactionId' => $transactionId,
                'ShipperImage' => '',
                'ShipperKYC' => '',
                'FileName' => '',
            ],
        ];

        return ['success' => true, 'payload' => $payload];
    }

    /**
     * Safely convert any value (string, array, object, null) into a string
     * for use in log messages and error responses. Prevents
     * "Array to string conversion" errors when API error fields are arrays.
     *
     * @param  mixed  $value
     * @return string
     */


    /**
     * Call the Overseas Logistic API to create a shipment.
     * Two-step process:
     * 1. Generate Bearer token from /token
     * 2. Create shipment via /api/shipment/create using the Bearer token
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'message' => string, 'data' => array|null, 'request_payload' => array|null]
     */
    private function callOverseasLogisticApiFromDb($shipper)
    {
        try {
            // Step 1: Generate Bearer token.
            $tokenResult = $this->getOverseasLogisticToken();
            if (! $tokenResult['success']) {
                return [
                    'success' => false,
                    'message' => $tokenResult['message'] ?? 'Overseas Logistic token generation failed.',
                ];
            }
            $bearerToken = $tokenResult['token'];

            // Step 2: Build the shipment payload.
            $payloadResult = $this->buildOverseasLogisticPayloadFromDb($shipper);
            if (! $payloadResult['success']) {
                return [
                    'success' => false,
                    'message' => $payloadResult['message'] ?? 'Failed to build Overseas Logistic payload.',
                ];
            }
            $payload = $payloadResult['payload'];

            $shipmentUrl = config('services.overseas.shipment_url');
            $timeout = (int) config('services.overseas.timeout', 60);

            \Log::info('Overseas Logistic shipment payload for shipper #'.$shipper->id.': '.substr(json_encode($payload), 0, 2000));

            // Step 3: Call the shipment/create API with the Bearer token.
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$bearerToken,
            ])
                ->timeout($timeout)
                ->post($shipmentUrl, $payload);

            $apiResponse = $response->json();

            if (! $response->successful()) {
                $errorMessage = 'Overseas Logistic API returned error.';
                if (is_array($apiResponse)) {
                    if (isset($apiResponse['error'])) {
                        $errorMessage = $this->overseasValueToString($apiResponse['error']);
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $this->overseasValueToString($apiResponse['message']);
                    } elseif (isset($apiResponse['errors'])) {
                        $errorMessage = $this->overseasValueToString($apiResponse['errors']);
                    }
                    if (isset($apiResponse['details']) && ! empty($apiResponse['details'])) {
                        $details = $apiResponse['details'];
                        if (is_array($details)) {
                            $flat = array_map([$this, 'overseasValueToString'], $details);
                            $errorMessage .= ' — '.implode('; ', $flat);
                        } else {
                            $errorMessage .= ' — '.$this->overseasValueToString($details);
                        }
                    }
                }
                \Log::error('Overseas Logistic shipment creation failed: '.$errorMessage.' | Status: '.$response->status().' | Body: '.$response->body());

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $apiResponse,
                    'request_payload' => $payload,
                    'status_code' => $response->status(),
                ];
            }

            // Check for a body-level error/status even on HTTP 200.
            // The Overseas Logistic API returns { "Status": true/false, "Error": "...", "Data": {...} }.
            // Treat Status === false (boolean) OR status === "ERROR" (string) as a failure.
            if (is_array($apiResponse)) {
                $responseStatus = $apiResponse['Status'] ?? $apiResponse['status'] ?? null;
                $isError = false;
                if ($responseStatus === false) {
                    $isError = true;
                } elseif ($responseStatus !== null && strtoupper((string) $responseStatus) === 'ERROR') {
                    $isError = true;
                }
                if ($isError) {
                    $rawError = $apiResponse['Error'] ?? $apiResponse['error']
                        ?? $apiResponse['message'] ?? $apiResponse['Message']
                        ?? 'Overseas Logistic API returned an error status.';
                    $errorMessage = $this->overseasValueToString($rawError);
                    \Log::error('Overseas Logistic API returned error in body for shipper #'.$shipper->id.': '.$errorMessage.' | Body: '.$response->body());

                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'data' => $apiResponse,
                        'request_payload' => $payload,
                    ];
                }
            }

            \Log::info('Overseas Logistic shipment created for shipper #'.$shipper->id.'. Response: '.substr($response->body(), 0, 2000));

            return [
                'success' => true,
                'message' => 'Overseas Logistic shipment created successfully.',
                'data' => $apiResponse,
                'request_payload' => $payload,
            ];
        } catch (\Exception $e) {
            \Log::error('Overseas Logistic API call failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Overseas Logistic API call failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Extract a tracking/AWB number from an Overseas Logistic API response.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */


    /**
     * Extract a tracking/AWB number from an Overseas Logistic API response.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */
    private function extractOverseasTrackingNumber($apiResponse)
    {
        if (! is_array($apiResponse)) {
            return null;
        }

        // Priority keys for the confirmed Overseas Logistic response format:
        // { "Status": true, "Data": { "AwbNo": "55141977", ... } }
        $priorityKeys = ['AwbNo', 'AwbNumber', 'awb_number', 'AWBNumber'];
        $candidateKeys = [
            'TrackingNumber', 'tracking_number',
            'WaybillNumber', 'waybill_number', 'ConsignmentNumber', 'consignment_number',
            'ShipmentNumber', 'shipment_number', 'OrderNumber', 'order_number',
            'ReferenceNo', 'reference_no', 'RefNo', 'BookingId', 'booking_id',
            'Waybill', 'waybill', 'Reference', 'reference', 'ShipmentId', 'shipment_id',
        ];
        $allKeys = array_merge($priorityKeys, $candidateKeys);

        // Case A: The response itself is a list of shipment objects.
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($allKeys as $key) {
                if (isset($apiResponse[0][$key]) && ! empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Check top-level keys.
        foreach ($allKeys as $key) {
            if (isset($apiResponse[$key]) && ! empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "Data" (capital, confirmed format) or "data".
        foreach (['Data', 'data'] as $dataKey) {
            $data = $apiResponse[$dataKey] ?? null;
            if (is_array($data)) {
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($allKeys as $key) {
                        if (isset($data[0][$key]) && ! empty($data[0][$key])) {
                            return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                        }
                    }
                }
                foreach ($allKeys as $key) {
                    if (isset($data[$key]) && ! empty($data[$key])) {
                        return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                    }
                }
            }
        }

        // Case D: Nested under common wrapper keys.
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment', 'Result', 'result', 'Response', 'response'] as $wrapKey) {
            if (isset($apiResponse[$wrapKey])) {
                $wrap = $apiResponse[$wrapKey];
                if (is_array($wrap)) {
                    $first = isset($wrap[0]) ? $wrap[0] : $wrap;
                    if (is_array($first)) {
                        foreach ($candidateKeys as $key) {
                            if (isset($first[$key]) && ! empty($first[$key])) {
                                return is_string($first[$key]) ? $first[$key] : (string) $first[$key];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract a label URL (or base64 label) from an Overseas Logistic API response.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */


    /**
     * Extract a label URL (or base64 label) from an Overseas Logistic API response.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */
    private function extractOverseasLabelUrl($apiResponse)
    {
        if (! is_array($apiResponse)) {
            return null;
        }

        // Priority keys for the confirmed Overseas Logistic response format:
        // { "Data": { "Airwaybill": { "AirwaybillUrl": "...", "BoxlabelUrl": "...", "CustomInvoiceUrl": "..." } } }
        $priorityKeys = ['AirwaybillUrl', 'BoxlabelUrl', 'LabelUrl', 'label_url', 'LabelURL', 'LabelLink', 'label_link'];
        $labelKeys = [
            'PdfUrl', 'pdf_url', 'PdfLink', 'pdf_link', 'Label', 'label',
            'LabelData', 'label_data', 'PdfBase64', 'pdf_base64', 'LabelBase64', 'label_base64',
        ];
        $allKeys = array_merge($priorityKeys, $labelKeys);

        // Case 0 (confirmed format): Data.Airwaybill.<key>
        foreach (['Data', 'data'] as $dataKey) {
            $data = $apiResponse[$dataKey] ?? null;
            if (is_array($data)) {
                foreach (['Airwaybill', 'airwaybill', 'AirwayBill', 'Label', 'label'] as $awbKey) {
                    if (isset($data[$awbKey]) && is_array($data[$awbKey])) {
                        foreach ($priorityKeys as $key) {
                            if (isset($data[$awbKey][$key]) && ! empty($data[$awbKey][$key])) {
                                return is_string($data[$awbKey][$key]) ? $data[$awbKey][$key] : (string) $data[$awbKey][$key];
                            }
                        }
                    }
                }
            }
        }

        // Case A: List of shipment objects.
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($allKeys as $key) {
                if (isset($apiResponse[0][$key]) && ! empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Top-level keys.
        foreach ($allKeys as $key) {
            if (isset($apiResponse[$key]) && ! empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "Data" (capital) or "data".
        foreach (['Data', 'data'] as $dataKey) {
            $data = $apiResponse[$dataKey] ?? null;
            if (is_array($data)) {
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($allKeys as $key) {
                        if (isset($data[0][$key]) && ! empty($data[0][$key])) {
                            return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                        }
                    }
                }
                foreach ($allKeys as $key) {
                    if (isset($data[$key]) && ! empty($data[$key])) {
                        return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                    }
                }
            }
        }

        // Case D: Nested under common wrapper keys.
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment', 'Result', 'result', 'Response', 'response'] as $wrapKey) {
            if (isset($apiResponse[$wrapKey])) {
                $wrap = $apiResponse[$wrapKey];
                if (is_array($wrap)) {
                    $first = isset($wrap[0]) ? $wrap[0] : $wrap;
                    if (is_array($first)) {
                        foreach ($labelKeys as $key) {
                            if (isset($first[$key]) && ! empty($first[$key])) {
                                return is_string($first[$key]) ? $first[$key] : (string) $first[$key];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract the 4x6 box label URL from an Overseas Logistic API response.
     *
     * Same case-walking as extractOverseasLabelUrl(), but prioritizes the
     * BoxlabelUrl key so the 4x6 label is stored alongside the full
     * airwaybill (LabelURL) in package_results.
     */
    private function extractOverseasBoxLabelUrl($apiResponse)
    {
        if (! is_array($apiResponse)) {
            return null;
        }

        $boxKeys = ['BoxlabelUrl', 'BoxLabelURL', 'boxlabelUrl', 'box_label_url', 'BoxLabelUrl'];
        $awbKeys = ['Airwaybill', 'airwaybill', 'AirwayBill', 'Label', 'label'];

        $pick = function ($arr) use ($boxKeys) {
            if (! is_array($arr)) {
                return null;
            }
            foreach ($boxKeys as $key) {
                if (isset($arr[$key]) && ! empty($arr[$key])) {
                    return is_string($arr[$key]) ? $arr[$key] : (string) $arr[$key];
                }
            }

            return null;
        };

        // Case 0 (confirmed format): Data.Airwaybill.BoxlabelUrl
        foreach (['Data', 'data'] as $dataKey) {
            $data = $apiResponse[$dataKey] ?? null;
            if (is_array($data)) {
                foreach ($awbKeys as $awbKey) {
                    if (isset($data[$awbKey]) && is_array($data[$awbKey])) {
                        $found = $pick($data[$awbKey]);
                        if ($found) {
                            return $found;
                        }
                    }
                }
            }
        }

        // Case A: List of shipment objects.
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            $found = $pick($apiResponse[0]);
            if ($found) {
                return $found;
            }
        }

        // Case B: Top-level keys.
        $found = $pick($apiResponse);
        if ($found) {
            return $found;
        }

        // Case C: Nested under "Data" (capital) or "data".
        foreach (['Data', 'data'] as $dataKey) {
            $data = $apiResponse[$dataKey] ?? null;
            if (is_array($data)) {
                if (isset($data[0]) && is_array($data[0])) {
                    $found = $pick($data[0]);
                    if ($found) {
                        return $found;
                    }
                }
                $found = $pick($data);
                if ($found) {
                    return $found;
                }
            }
        }

        // Case D: Nested under common wrapper keys.
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment', 'Result', 'result', 'Response', 'response'] as $wrapKey) {
            if (isset($apiResponse[$wrapKey])) {
                $wrap = $apiResponse[$wrapKey];
                if (is_array($wrap)) {
                    $first = isset($wrap[0]) ? $wrap[0] : $wrap;
                    if (is_array($first)) {
                        $found = $pick($first);
                        if ($found) {
                            return $found;
                        }
                    }
                }
            }
        }

        return null;
    }


    /**
     * Normalize a delivery-destination string into an ISO country code for the
     * Overseas Logistic API. Defaults to the provided fallback (CA for Canada).
     *
     * @param  string|null  $destination
     * @param  string  $fallback
     * @return string
     */
    private function getOverseasCountryCode($destination, $fallback = 'CA')
    {
        $destUpper = strtoupper(trim($destination ?? ''));

        if ($destUpper === '') {
            return $fallback;
        }

        // Canada detection.
        $isCanada = (
            $destUpper === 'CANADA'
            || $destUpper === 'CA'
            || str_contains($destUpper, 'CANADA')
        );
        if ($isCanada) {
            return 'CA';
        }

        // Australia detection — covers "Australia", "AU", "AUS", and any
        // string containing "Australia". Returns ISO code "AU" for the
        // Overseas Logistic API ReceiverCountry field.
        $isAustralia = (
            $destUpper === 'AUSTRALIA'
            || $destUpper === 'AU'
            || $destUpper === 'AUS'
            || str_contains($destUpper, 'AUSTRALIA')
        );
        if ($isAustralia) {
            return 'AU';
        }

        // UK detection.
        $isUk = (
            $destUpper === 'UK'
            || $destUpper === 'GB'
            || str_contains($destUpper, 'UNITED KINGDOM')
            || str_starts_with($destUpper, 'UK -')
            || str_contains($destUpper, 'GREAT BRITAIN')
        );
        if ($isUk) {
            return 'GB';
        }

        // US detection.
        $isUs = (
            $destUpper === 'US'
            || $destUpper === 'USA'
            || str_contains($destUpper, 'UNITED STATE')
            || str_starts_with($destUpper, 'US-')
        );
        if ($isUs) {
            return 'US';
        }

        return $fallback;
    }

    /**
     * Build the Flying Tigers API payload from database records.
     * Payload structure mirrors the documented CustomerBookingAPI format.
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */


    /**
     * Build the Flying Tigers API payload from database records.
     * Payload structure mirrors the documented CustomerBookingAPI format.
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */
    private function buildFlyingTigersPayloadFromDb($shipper)
    {
        $consignee = $shipper->consigneeInfo;
        if (! $consignee) {
            \Log::warning('buildFlyingTigersPayloadFromDb: No consignee found for shipper #'.$shipper->id);

            return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
        }

        $packages = $shipper->packageDimensions;
        if ($packages->isEmpty()) {
            \Log::warning('buildFlyingTigersPayloadFromDb: No packages found for shipper #'.$shipper->id);

            return ['success' => false, 'message' => 'No package dimensions found for this shipment.'];
        }

        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();

        // Consignee country code from delivery_destination (e.g. "US", "UK")
        $consigneeCountryCode = $this->getCountryCodeFromDestination($consignee->delivery_destination ?? '');

        // Currency code (default INR to match example payload)
        $currencyCode = $invoice ? ($invoice->invoice_currency ?? 'INR') : 'INR';

        // Booking date in "d-M-Y" format (e.g. "25-May-2026")
        $bookingDate = now()->format('d-M-Y');

        // Reference number: prefer invoice reference, else AWB number
        $refNo = $invoice ? ($invoice->reference_number ?? '') : '';
        if (empty($refNo)) {
            $refNo = $shipper->awb_number ?? ('FT-'.$shipper->id);
        }

        // Consignee name: prefer consignee_name, else contact_person
        $consigneeName = $consignee->consignee_name ?? '';
        if (empty(trim($consigneeName))) {
            $consigneeName = $consignee->contact_person ?? '';
        }

        // Consignee phone
        $consigneePhone = $consignee->phone_number ?? '';

        // Consignee address (combine line1 + line2 + line3 if line1 is short)
        $consigneeAddress1 = trim($consignee->address_line1 ?? '');
        if (empty($consigneeAddress1)) {
            $consigneeAddress1 = trim(($consignee->address_line2 ?? '').' '.($consignee->address_line3 ?? ''));
        }

        // Invoice number & date for packet details
        $invoiceNo = $invoice ? ($invoice->invoice_number ?? '') : '';
        if (empty($invoiceNo)) {
            $invoiceNo = $shipper->awb_number ?? ('INV-'.$shipper->id);
        }
        $invoiceDate = $invoice && $invoice->invoice_date
            ? Carbon::parse($invoice->invoice_date)->format('d-M-Y')
            : now()->format('d-M-Y');

        // Build addPacketDetailList — one entry per package, with boxInvoiceDetails from invoice items.
        $addPacketDetailList = [];
        $invoiceItems = collect();
        if ($invoice) {
            $invoiceItems = ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get();
        }

        $packageIndex = 0;
        foreach ($packages as $pkg) {
            $packageIndex++;

            $pkgWeight = (float) ($pkg->actual_weight_kg ?? 0);
            if ($pkgWeight <= 0) {
                $pkgWeight = 0.5;
            }
            $pkgL = (float) ($pkg->length_cm ?? 0) ?: 1;
            $pkgW = (float) ($pkg->width_cm ?? 0) ?: 1;
            $pkgH = (float) ($pkg->height_cm ?? 0) ?: 1;

            // Find invoice items mapped to this package via box_no (1-based)
            $boxItems = $invoiceItems->filter(function ($item) use ($packageIndex) {
                return ((int) ($item->box_no ?? 0)) === $packageIndex;
            });

            // If no items mapped to this box, use all items for the first package
            if ($boxItems->isEmpty() && $packageIndex === 1) {
                $boxItems = $invoiceItems;
            }

            $boxInvoiceDetails = [];
            if ($boxItems->isNotEmpty()) {
                foreach ($boxItems as $item) {
                    $itemQty = (float) ($item->qty ?? 1);
                    if ($itemQty <= 0) {
                        $itemQty = 1;
                    }
                    $itemUnitPrice = (float) ($item->unit_rate ?? 0);
                    if ($itemUnitPrice <= 0) {
                        $itemUnitPrice = (float) ($item->amount ?? 0) / $itemQty;
                    }
                    if ($itemUnitPrice <= 0) {
                        $itemUnitPrice = 5.00;
                    }
                    $boxInvoiceDetails[] = [
                        'ProductName' => (string) ($item->description ?? 'General Merchandise'),
                        'UnitPrice' => number_format($itemUnitPrice, 2, '.', ''),
                        'Quantity' => (string) (int) $itemQty,
                    ];
                }
            } else {
                // Fallback single item when no invoice items exist
                $boxInvoiceDetails[] = [
                    'ProductName' => 'General Merchandise',
                    'UnitPrice' => '5.00',
                    'Quantity' => '1',
                ];
            }

            $addPacketDetailList[] = [
                'BoxWeight' => number_format($pkgWeight, 3, '.', ''),
                'BoxLength' => number_format($pkgL, 2, '.', ''),
                'BoxWidth' => number_format($pkgW, 2, '.', ''),
                'BoxHeight' => number_format($pkgH, 2, '.', ''),
                'InvoiceNo' => (string) $invoiceNo,
                'InvoiceDate' => (string) $invoiceDate,
                'boxInvoiceDetails' => $boxInvoiceDetails,
            ];
        }

        $payload = [
            'shipmentType' => 'Forward',
            // 'consigneeCountry'  => (string) ($consignee->delivery_destination ?? $consigneeCountryCode ?? 'US'),
            'consigneeCountry' => (string) ('US'),
            'RefNo' => (string) $refNo,
            'BookingDate' => (string) $bookingDate,
            'Consignee' => (string) $consigneeName,
            'ConsigneePhoneNo' => (string) $consigneePhone,
            'ConsigneeAddress1' => (string) $consigneeAddress1,
            'ConsigneePinCode' => (string) ($consignee->zip_code ?? ''),
            'ConsigneeState' => (string) ($consignee->state ?? ''),
            'ConsigneeCity' => (string) ($consignee->city ?? ''),
            'BusinessType' => 'B2C',
            'Vendor' => 'USPS Work',
            'Service' => 'Uniuni',
            'PickupPoint' => '2',
            'addPacketDetailList' => $addPacketDetailList,
            'PackageType' => 'NONDOC',
            'currencyCode' => (string) $currencyCode,
        ];

        // print_r($payload); // Debugging line to inspect the payload structure
        // die;

        return [
            'success' => true,
            'payload' => $payload,
        ];
    }

    /**
     * Call the Flying Tigers API to create a shipment.
     * Endpoint: https://app.flyingtigers.in/api/Shipment/CustomerBookingAPI
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */


    /**
     * Call the Flying Tigers API to create a shipment.
     * Endpoint: https://app.flyingtigers.in/api/Shipment/CustomerBookingAPI
     *
     * @param  ShipperInfo  $shipper
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    private function callFlyingTigersApiFromDb($shipper)
    {
        try {
            $payloadResult = $this->buildFlyingTigersPayloadFromDb($shipper);
            if (! $payloadResult['success']) {
                return [
                    'success' => false,
                    'message' => $payloadResult['message'] ?? 'Failed to build Flying Tigers payload.',
                ];
            }

            $payload = $payloadResult['payload'];

            $clientCode = config('services.flyingtigers.client_code');
            $userCode = config('services.flyingtigers.user_code');
            $authToken = config('services.flyingtigers.auth_token');
            $baseUrl = rtrim(config('services.flyingtigers.base_url'), '/');
            $endpoint = config('services.flyingtigers.endpoint', '/api/Shipment/CustomerBookingAPI');
            $url = $baseUrl.$endpoint;
            $timeout = (int) config('services.flyingtigers.timeout', 60);

            \Log::info('Flying Tigers payload for shipper #'.$shipper->id.': '.substr(json_encode($payload), 0, 2000));

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'ClientCode' => $clientCode,
                'UserCode' => $userCode,
                'AuthToken' => $authToken,
            ];

            $response = Http::withHeaders($headers)
                ->withOptions(['verify' => false])
                ->timeout($timeout)
                ->post($url, $payload);

            $apiResponse = $response->json();

            if (! $response->successful()) {
                $errorMessage = 'Flying Tigers API returned error.';
                if (is_array($apiResponse)) {
                    if (isset($apiResponse['error'])) {
                        $errorMessage = is_string($apiResponse['error']) ? $apiResponse['error'] : json_encode($apiResponse['error']);
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $apiResponse['message'];
                    } elseif (isset($apiResponse['errors'])) {
                        $errorMessage = is_string($apiResponse['errors']) ? $apiResponse['errors'] : json_encode($apiResponse['errors']);
                    }
                    if (isset($apiResponse['details']) && is_array($apiResponse['details']) && ! empty($apiResponse['details'])) {
                        $errorMessage .= ' — '.implode('; ', $apiResponse['details']);
                    }
                }
                \Log::error('Flying Tigers API failed: '.$errorMessage.' | Status: '.$response->status().' | Body: '.$response->body());

                // Check if this is an address-related error (for auto-fallback to UNITED CLASSIC)
                $isAddressError = $this->isFlyingTigersAddressError($errorMessage, $apiResponse, $response->body());

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $apiResponse,
                    'status_code' => $response->status(),
                    'is_address_error' => $isAddressError,
                ];
            }

            \Log::info('Flying Tigers response for shipper #'.$shipper->id.': '.substr($response->body(), 0, 2000));

            // Some APIs return 200 OK but with an error in the body — check for address error
            $isAddressError = $this->isFlyingTigersAddressError(null, $apiResponse, $response->body());
            if ($isAddressError) {
                \Log::warning('Flying Tigers API returned address error in success response for shipper #'.$shipper->id.': '.$response->body());

                return [
                    'success' => false,
                    'message' => 'Cannot create order: Provided address appears to be incorrect or incomplete.',
                    'data' => $apiResponse,
                    'is_address_error' => true,
                ];
            }

            // Check if the API returned an error status in the body (HTTP 200 but status=ERROR)
            // e.g. {"status": "ERROR", "message": "Ref no already exists."}
            if (is_array($apiResponse)) {
                $responseStatus = $apiResponse['status'] ?? null;
                if ($responseStatus !== null && strtoupper((string) $responseStatus) === 'ERROR') {
                    $errorMessage = $apiResponse['message'] ?? 'Flying Tigers API returned an error status.';
                    \Log::error('Flying Tigers API returned ERROR in body for shipper #'.$shipper->id.': '.$errorMessage.' | Body: '.$response->body());

                    // Check if this is also an address error (for fallback to UNITED CLASSIC)
                    $isAddressError = $this->isFlyingTigersAddressError($errorMessage, $apiResponse, $response->body());

                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'data' => $apiResponse,
                        'is_address_error' => $isAddressError,
                    ];
                }
            }

            return [
                'success' => true,
                'message' => 'Flying Tigers shipment created successfully.',
                'data' => $apiResponse,
            ];
        } catch (\Exception $e) {
            \Log::error('Flying Tigers API call failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Flying Tigers API call failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Extract a tracking/reference number from a Flying Tigers API response.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */


    /**
     * Extract a tracking/reference number from a Flying Tigers API response.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */
    private function extractFlyingTigersTrackingNumber($apiResponse)
    {
        if (! is_array($apiResponse)) {
            return null;
        }

        $candidateKeys = [
            'TrackingNumber', 'tracking_number', 'WaybillNumber', 'waybill_number',
            'AwbNumber', 'awb_number', 'ConsignmentNumber', 'consignment_number',
            'OrderNumber', 'order_number', 'ShipmentNumber', 'shipment_number',
            'ReferenceNo', 'reference_no', 'RefNo', 'BookingId', 'booking_id',
            'Waybill', 'waybill', 'Reference', 'reference', 'ShipmentId', 'shipment_id',
        ];

        // Case A: The response itself is a list of shipment objects
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($candidateKeys as $key) {
                if (isset($apiResponse[0][$key]) && ! empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Check top-level keys
        foreach ($candidateKeys as $key) {
            if (isset($apiResponse[$key]) && ! empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "data"
        $data = $apiResponse['data'] ?? null;
        if (is_array($data)) {
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($candidateKeys as $key) {
                    if (isset($data[0][$key]) && ! empty($data[0][$key])) {
                        return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                    }
                }
            }
            foreach ($candidateKeys as $key) {
                if (isset($data[$key]) && ! empty($data[$key])) {
                    return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                }
            }
        }

        // Case D: Nested under "Shipments" / "shipments" / "Shipment" / "shipment"
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment', 'Result', 'result'] as $wrapKey) {
            if (isset($apiResponse[$wrapKey])) {
                $wrap = $apiResponse[$wrapKey];
                if (is_array($wrap)) {
                    $first = isset($wrap[0]) ? $wrap[0] : $wrap;
                    if (is_array($first)) {
                        foreach ($candidateKeys as $key) {
                            if (isset($first[$key]) && ! empty($first[$key])) {
                                return is_string($first[$key]) ? $first[$key] : (string) $first[$key];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract the LabelURL from a Flying Tigers API response.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */


    /**
     * Extract the LabelURL from a Flying Tigers API response.
     *
     * @param  mixed  $apiResponse
     * @return string|null
     */
    private function extractFlyingTigersLabelUrl($apiResponse)
    {
        if (! is_array($apiResponse)) {
            return null;
        }

        $labelKeys = ['LabelURL', 'label_url', 'LabelUrl', 'labelurl', 'Label', 'label', 'PdfUrl', 'pdf_url', 'LabelLink', 'label_link'];

        // Case A: The response itself is a list of shipment objects
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($labelKeys as $key) {
                if (isset($apiResponse[0][$key]) && ! empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Check top-level keys
        foreach ($labelKeys as $key) {
            if (isset($apiResponse[$key]) && ! empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "data"
        $data = $apiResponse['data'] ?? null;
        if (is_array($data)) {
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($labelKeys as $key) {
                    if (isset($data[0][$key]) && ! empty($data[0][$key])) {
                        return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                    }
                }
            }
            foreach ($labelKeys as $key) {
                if (isset($data[$key]) && ! empty($data[$key])) {
                    return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                }
            }
        }

        // Case D: Nested under "Shipments" / "shipments" / "Result" / "result"
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment', 'Result', 'result'] as $wrapKey) {
            if (isset($apiResponse[$wrapKey])) {
                $wrap = $apiResponse[$wrapKey];
                if (is_array($wrap)) {
                    $first = isset($wrap[0]) ? $wrap[0] : $wrap;
                    if (is_array($first)) {
                        foreach ($labelKeys as $key) {
                            if (isset($first[$key]) && ! empty($first[$key])) {
                                return is_string($first[$key]) ? $first[$key] : (string) $first[$key];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Check if a Flying Tigers API response/error indicates an address-related error.
     * Used to trigger auto-fallback to UNITED CLASSIC (Ship Global).
     *
     * @param  string|null  $errorMessage
     * @param  mixed  $apiResponse
     * @param  string|null  $rawBody
     * @return bool
     */


    /**
     * Check if a Flying Tigers API response/error indicates an address-related error.
     * Used to trigger auto-fallback to UNITED CLASSIC (Ship Global).
     *
     * @param  string|null  $errorMessage
     * @param  mixed  $apiResponse
     * @param  string|null  $rawBody
     * @return bool
     */
    private function isFlyingTigersAddressError($errorMessage, $apiResponse, $rawBody = null)
    {
        $addressErrorPatterns = [
            'address appears to be incorrect',
            'address appears to be incomplete',
            'address is incorrect',
            'address is incomplete',
            'incorrect or incomplete address',
            'address incorrect or incomplete',
            'provided address appears to be incorrect',
            'provided address appears to be incomplete',
        ];

        // Combine all text sources to search
        $searchText = '';
        if (! empty($errorMessage)) {
            $searchText .= ' '.strtolower((string) $errorMessage);
        }
        if (is_array($apiResponse)) {
            $searchText .= ' '.strtolower(json_encode($apiResponse));
        }
        if (! empty($rawBody)) {
            $searchText .= ' '.strtolower((string) $rawBody);
        }

        if (empty(trim($searchText))) {
            return false;
        }

        foreach ($addressErrorPatterns as $pattern) {
            if (str_contains($searchText, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate UNITED CLASSIC (Ship Global) fallback info for a Flying Tigers address error.
     * Does NOT modify any data — only calculates the rate, paid amount, difference,
     * and wallet impact so the frontend can present a dropdown option to the customer.
     *
     * @param  ShipperInfo  $shipper
     * @param  int  $customerId
     * @return array
     */


    /**
     * Calculate UNITED CLASSIC (Ship Global) fallback info for a Flying Tigers address error.
     * Does NOT modify any data — only calculates the rate, paid amount, difference,
     * and wallet impact so the frontend can present a dropdown option to the customer.
     *
     * @param  ShipperInfo  $shipper
     * @param  int  $customerId
     * @return array
     */
    private function getFlyingTigersAddressErrorFallbackInfo($shipper, $customerId)
    {
        // 1. Find the UNITED CLASSIC courier service
        $classicService = CourierService::whereRaw('UPPER(method) LIKE ?', ['%UNITED CLASSIC%'])->first();
        if (! $classicService) {
            \Log::error('Flying Tigers address fallback: UNITED CLASSIC service not found in database.');

            return [
                'success' => false,
                'message' => 'Address is incorrect for UNITED ECO POST. Could not find UNITED CLASSIC service for fallback. Please contact support.',
                'is_address_error' => true,
            ];
        }

        // 2. Calculate the total chargeable weight from packages
        $packages = $shipper->packageDimensions;
        $totalWeight = 0;
        foreach ($packages as $pkg) {
            $totalWeight += floatval($pkg->chargeable_weight ?? $pkg->actual_weight_kg ?? 0);
        }

        // 3. Get consignee state for zone lookup
        $consignee = $shipper->consigneeInfo;
        $consigneeState = $consignee ? ($consignee->state ?? '') : '';

        // 4. Calculate the UNITED CLASSIC rate
        $classicRate = app(BulkUploadController::class)->calculateBulkRate($customerId, $classicService, $totalWeight, $consigneeState);
        $classicTotal = floatval($classicRate['total'] ?? 0);

        \Log::info('Flying Tigers address fallback: UNITED CLASSIC rate calculated: '.$classicTotal.' for shipper #'.$shipper->id);

        // 5. Determine what was actually paid. Under the new flow payment is only cut
        //    AFTER a successful manifest, so a packed shipment may still be unpaid.
        $paidCharge = WalletTransaction::where('customer_id', $customerId)
            ->where('type', 'debit')
            ->where('reason', 'shipment_charge')
            ->where('reference', $shipper->awb_number)
            ->first();
        $paidAmount = $paidCharge ? floatval($paidCharge->amount) : 0;

        // 6. Calculate the difference
        $difference = $classicTotal - $paidAmount;

        // 7. Determine wallet impact (preview only — no actual deduction yet)
        $walletAction = 'none';
        $walletAmount = 0;
        $walletBalance = 0;

        $wallet = Wallet::where('customer_id', $customerId)->first();
        if ($wallet) {
            $walletBalance = (float) $wallet->balance;
            if ($difference > 0.01) {
                $walletAction = 'deduct';
                $walletAmount = $difference;
            } elseif ($difference < -0.01) {
                $walletAction = 'refund';
                $walletAmount = abs($difference);
            }
        }

        return [
            'success' => true,
            'is_address_error' => true,
            'shipper_id' => $shipper->id,
            'classic_service' => $classicService->method,
            'classic_rate' => $classicTotal,
            'paid_amount' => $paidAmount,
            'difference' => $difference,
            'wallet_action' => $walletAction,
            'wallet_amount' => $walletAmount,
            'wallet_balance' => $walletBalance,
            'total_weight' => $totalWeight,
        ];
    }

    /**
     * Execute the Ship Global (UNITED CLASSIC) fallback for a Flying Tigers address error.
     * Called when the customer confirms the dropdown option in the frontend.
     * Performs: update shipping method, call Ship Global API, store tracking,
     * wallet deduction/refund, update invoice.
     *
     * @param  ShipperInfo  $shipper
     * @param  int  $customerId
     * @return array
     */


    /**
     * Execute the Ship Global (UNITED CLASSIC) fallback for a Flying Tigers address error.
     * Called when the customer confirms the dropdown option in the frontend.
     * Performs: update shipping method, call Ship Global API, store tracking,
     * wallet deduction/refund, update invoice.
     *
     * @param  ShipperInfo  $shipper
     * @param  int  $customerId
     * @return array
     */
    private function executeShipGlobalFallback($shipper, $customerId)
    {
        // 1. Find the UNITED CLASSIC courier service
        $classicService = CourierService::whereRaw('UPPER(method) LIKE ?', ['%UNITED CLASSIC%'])->first();
        if (! $classicService) {
            \Log::error('Flying Tigers address fallback: UNITED CLASSIC service not found in database.');

            return [
                'success' => false,
                'message' => 'Could not find UNITED CLASSIC service for fallback. Please contact support.',
                'is_address_error' => true,
            ];
        }

        // 2. Calculate the total chargeable weight from packages
        $packages = $shipper->packageDimensions;
        $totalWeight = 0;
        foreach ($packages as $pkg) {
            $totalWeight += floatval($pkg->chargeable_weight ?? $pkg->actual_weight_kg ?? 0);
        }

        // 3. Get consignee state for zone lookup
        $consignee = $shipper->consigneeInfo;
        $consigneeState = $consignee ? ($consignee->state ?? '') : '';

        // 4. Calculate the UNITED CLASSIC rate
        $classicRate = app(BulkUploadController::class)->calculateBulkRate($customerId, $classicService, $totalWeight, $consigneeState);
        $classicTotal = floatval($classicRate['total'] ?? 0);

        \Log::info('Flying Tigers address fallback: UNITED CLASSIC rate calculated: '.$classicTotal.' for shipper #'.$shipper->id);

        // 5. Determine what was actually paid. Under the new flow payment is only cut
        //    AFTER a successful manifest, so a packed shipment may still be unpaid.
        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();
        $paidCharge = WalletTransaction::where('customer_id', $customerId)
            ->where('type', 'debit')
            ->where('reason', 'shipment_charge')
            ->where('reference', $shipper->awb_number)
            ->first();
        $paidAmount = $paidCharge ? floatval($paidCharge->amount) : 0;

        // 6. Calculate the difference
        $difference = $classicTotal - $paidAmount;

        // 7. Update the shipper's shipping method to UNITED CLASSIC
        $shipper->shipping_method = $classicService->method;
        $shipper->save();

        // Also update package dimensions shipping method
        PackageDimension::where('shipper_id', $shipper->id)->update(['shipping_method' => $classicService->method]);

        // 8. Call Ship Global API to create the shipment
        $shipGlobalResult = $this->callShipGlobalApiFromDb($shipper);
        if (! $shipGlobalResult['success']) {
            \Log::error('Flying Tigers address fallback: Ship Global API failed: '.($shipGlobalResult['message'] ?? 'Unknown'));

            return [
                'success' => false,
                'message' => 'Fallback to UNITED CLASSIC failed: '.($shipGlobalResult['message'] ?? 'Unknown error'),
                'is_address_error' => true,
                'classic_rate' => $classicTotal,
                'paid_amount' => $paidAmount,
            ];
        }

        // 9. Extract tracking number from Ship Global response
        $apiResponse = $shipGlobalResult['data'] ?? [];
        $trackingNumber = null;
        if (isset($apiResponse['data']) && isset($apiResponse['data']['waybill_number']) && ! empty($apiResponse['data']['waybill_number'])) {
            $trackingNumber = $apiResponse['data']['waybill_number'];
        } elseif (isset($apiResponse['waybill_number']) && ! empty($apiResponse['waybill_number'])) {
            $trackingNumber = $apiResponse['waybill_number'];
        } elseif (isset($apiResponse['tracking_number'])) {
            $trackingNumber = $apiResponse['tracking_number'];
        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['tracking_number'])) {
            $trackingNumber = $apiResponse['data']['tracking_number'];
        } elseif (isset($apiResponse['awb_number'])) {
            $trackingNumber = $apiResponse['awb_number'];
        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['awb_number'])) {
            $trackingNumber = $apiResponse['data']['awb_number'];
        } elseif (isset($apiResponse['waybill'])) {
            $trackingNumber = $apiResponse['waybill'];
        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['waybill'])) {
            $trackingNumber = $apiResponse['data']['waybill'];
        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['order_number'])) {
            $trackingNumber = $apiResponse['data']['order_number'];
        } elseif (isset($apiResponse['order_number'])) {
            $trackingNumber = $apiResponse['order_number'];
        }

        // 10. Store tracking data
        $createShipment = CreateShipment::where('shipper_id', $shipper->id)->first();
        try {
            ShipmentTracking::updateOrCreate(
                ['shipper_id' => $shipper->id],
                [
                    'customer_id' => $customerId,
                    'create_shipment_id' => $createShipment ? $createShipment->id : null,
                    'response_status_code' => '1',
                    'response_status_description' => 'Ship Global shipment created (fallback from Flying Tigers address error)',
                    'shipment_identification_number' => $trackingNumber,
                    'total_charges_currency' => 'INR',
                    'total_charges_amount' => $classicTotal,
                    'billing_weight_uom' => 'KGS',
                    'billing_weight' => $totalWeight,
                    'package_results' => null,
                    'raw_response' => $apiResponse,
                    'status' => 'created',
                ]
            );

            // Update shipper status to manifested
            $shipper->status = 'manifested';
            $shipper->save();

            // Create a manifest record from the manifests table
            $this->createManifestRecord($shipper->id, $customerId);

            // Create tracking record for manifested status
            Tracking::create([
                'awb_number' => $shipper->awb_number,
                'shipper_id' => $shipper->id,
                'shipping_id' => $createShipment ? $createShipment->id : null,
                'uwc_id' => $shipper->awb_number,
                'title' => Tracking::getTitleForStatus('manifested'),
                'status' => 'manifested',
            ]);

            // Log the manifested status change (Ship Global fallback from Flying Tigers address error)
            ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', 'packed', 'Shipment manifested via Ship Global (UNITED CLASSIC fallback from Flying Tigers address error). Tracking: '.($trackingNumber ?? 'N/A'), $customerId, 'customer');
        } catch (\Exception $e) {
            \Log::error('Flying Tigers address fallback: Failed to store tracking data: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Fallback to UNITED CLASSIC succeeded but failed to store tracking: '.$e->getMessage(),
                'is_address_error' => true,
                'classic_rate' => $classicTotal,
                'paid_amount' => $paidAmount,
            ];
        }

        // 11. Handle wallet charge/deduction/refund AFTER a successful booking.
        //     If nothing was ever paid, charge the full UNITED CLASSIC rate now.
        //     If it was already paid, only handle the rate difference.
        $walletAction = 'none';
        $walletAmount = 0;
        $newBalance = 0;

        $wallet = Wallet::where('customer_id', $customerId)->first();
        if ($wallet) {
            if (! $paidCharge) {
                // New flow: nothing paid before → charge the full UNITED CLASSIC rate.
                if ($classicTotal > 0) {
                    if ($wallet->balance < $classicTotal) {
                        $newBalance = (float) $wallet->balance;
                        $walletAction = 'insufficient';
                        \Log::warning('Flying Tigers address fallback: Insufficient wallet balance for full charge ₹'.$classicTotal.' on shipper #'.$shipper->id);
                    } else {
                        $wallet->decrement('balance', $classicTotal);
                        $wallet->refresh();
                        $walletAction = 'charged';
                        $walletAmount = $classicTotal;
                        $newBalance = (float) $wallet->balance;

                        WalletTransaction::create([
                            'customer_id' => $customerId,
                            'type' => 'debit',
                            'reason' => 'shipment_charge',
                            'amount' => $classicTotal,
                            'balance_after' => $wallet->balance,
                            'reference' => $shipper->awb_number,
                            'description' => 'Payment of ₹'.number_format($classicTotal, 2).' for shipment '.($shipper->awb_number ?: '#'.$shipper->id).' (UNITED CLASSIC fallback).',
                        ]);

                        \Log::info('Flying Tigers address fallback: Charged full ₹'.$classicTotal.' from wallet (UNITED CLASSIC fallback) for shipper #'.$shipper->id);
                    }
                } else {
                    $newBalance = (float) $wallet->balance;
                }
            } else {
                // Old flow: already paid → handle the rate difference only.
                if ($difference > 0.01) {
                    // UNITED CLASSIC is more expensive → deduct difference from wallet
                    $wallet->decrement('balance', $difference);
                    $wallet->refresh();
                    $walletAction = 'deducted';
                    $walletAmount = $difference;
                    $newBalance = (float) $wallet->balance;
                    \Log::info('Flying Tigers address fallback: Deducted ₹'.$difference.' from wallet (CLASSIC rate ₹'.$classicTotal.' > paid ₹'.$paidAmount.')');
                } elseif ($difference < -0.01) {
                    // UNITED CLASSIC is cheaper → refund difference to wallet
                    $refundAmount = abs($difference);
                    $wallet->increment('balance', $refundAmount);
                    $wallet->refresh();
                    $walletAction = 'refunded';
                    $walletAmount = $refundAmount;
                    $newBalance = (float) $wallet->balance;
                    \Log::info('Flying Tigers address fallback: Refunded ₹'.$refundAmount.' to wallet (CLASSIC rate ₹'.$classicTotal.' < paid ₹'.$paidAmount.')');
                } else {
                    $newBalance = (float) $wallet->balance;
                }
            }
        }

        // 12. Update the invoice total to reflect the new rate
        if ($invoice && $classicTotal > 0) {
            $invoice->update(['total_amount' => $classicTotal]);
        }

        // Build the user-facing message
        $message = 'Shipment manifested successfully via UNITED CLASSIC (Ship Global).';
        if ($walletAction === 'charged') {
            $message .= ' ₹'.number_format($walletAmount, 2).' has been deducted from your wallet.';
        } elseif ($walletAction === 'insufficient') {
            $message .= ' The shipment is booked, but your wallet could not be charged (insufficient balance). Please recharge your wallet.';
        } elseif ($walletAction === 'deducted') {
            $message .= ' ₹'.number_format($walletAmount, 2).' has been deducted from your wallet (rate difference).';
        } elseif ($walletAction === 'refunded') {
            $message .= ' ₹'.number_format($walletAmount, 2).' has been refunded to your wallet (rate difference).';
        }

        return [
            'success' => true,
            'message' => $message,
            'tracking_number' => $trackingNumber,
            'shipper_id' => $shipper->id,
            'manifest_number' => Manifest::where('shipper_id', $shipper->id)->value('manifest_number'),
            'network' => 'Ship Global (Fallback)',
            'is_address_error' => true,
            'classic_rate' => $classicTotal,
            'paid_amount' => $paidAmount,
            'wallet_action' => $walletAction,
            'wallet_amount' => $walletAmount,
            'new_balance' => $newBalance,
            'ship_global_response' => $apiResponse,
        ];
    }

    /**
     * Manifest a prepaid shipment via Ship Global (UNITED CLASSIC) fallback.
     * Called when the admin confirms the dropdown option after a Flying Tigers address error.
     *
     * @return JsonResponse
     */
    public function prepaidManifestShipGlobalFallback(Request $request)
    {
        try {
            if (! auth()->guard('admin')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $validated = $request->validate([
                'shipper_id' => 'required|integer',
            ]);

            $shipperId = $validated['shipper_id'];

            $shipper = ShipperInfo::where('id', $shipperId)
                ->where('shipment_type', 5)
                ->first();

            if (! $shipper) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found.',
                ], 404);
            }

            if ($shipper->status === 'manifested') {
                return response()->json([
                    'success' => false,
                    'message' => 'This shipment has already been manifested.',
                ], 400);
            }

            $manifestCustomerId = (int) ($shipper->customer_id ?? 0);
            if ($manifestCustomerId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This prepaid shipment has no linked customer. Link an exporter customer before manifesting.',
                ], 422);
            }

            $result = $this->executeShipGlobalFallback($shipper, $manifestCustomerId);

            if ($result['success']) {
                return response()->json($result);
            } else {
                return response()->json($result, 500);
            }
        } catch (\Exception $e) {
            \Log::error('Prepaid Ship Global fallback manifest error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Manifest a single prepaid shipment - check network and call appropriate API.
     * Works for prepaid shipments in 'ready' or 'packed' status. Wallet is
     * charged from the exporter customer's wallet (when linked) only AFTER a
     * successful carrier booking.
     */
    public function prepaidManifestShipment(Request $request)
    {
        try {
            if (! auth()->guard('admin')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $adminId = (int) auth()->guard('admin')->id();
            $shipperId = $request->input('shipper_id');

            $shipper = ShipperInfo::where('id', $shipperId)
                ->where('shipment_type', 5)
                ->first();

            if (! $shipper) {
                return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
            }

            if (! in_array($shipper->status, ['ready', 'packed'])) {
                return response()->json(['success' => false, 'message' => 'Shipment must be in Ready or Packed status to manifest.'], 400);
            }

            $manifestCustomerId = (int) ($shipper->customer_id ?? 0);
            if ($manifestCustomerId <= 0) {
                return response()->json(['success' => false, 'message' => 'This prepaid shipment has no linked customer. Link an exporter customer before manifesting.'], 422);
            }

            $previousStatus = $shipper->status;
            // Confirm Payment se manifest hone par status Ready rakha jata hai (target_status=ready).
            $targetStatus = $request->input('target_status') === 'ready' ? 'ready' : 'manifested';

            // Agar carrier booking pehle ho chuki hai (Confirm Payment par), to dobara API call mat karo.
            // Sirf status aage badhao taaki duplicate AWB / double charge na ho.
            if ($targetStatus === 'manifested') {
                $existingTracking = ShipmentTracking::where('shipper_id', $shipper->id)
                    ->whereNotNull('shipment_identification_number')
                    ->first();
                if ($existingTracking && ! empty($existingTracking->shipment_identification_number)) {
                    $createShipmentExisting = CreateShipment::where('shipper_id', $shipper->id)->first();
                    $shipper->status = 'manifested';
                    $shipper->save();

                    // Create a manifest record from the manifests table
                    $this->createManifestRecord($shipper->id, $manifestCustomerId);

                    Tracking::firstOrCreate(
                        ['shipper_id' => $shipper->id, 'status' => 'manifested'],
                        [
                            'awb_number' => $shipper->awb_number,
                            'shipping_id' => $createShipmentExisting ? $createShipmentExisting->id : null,
                            'uwc_id' => $shipper->awb_number,
                            'title' => Tracking::getTitleForStatus('manifested'),
                        ]
                    );
                    ShipmentLog::logStatus(
                        $shipper->id,
                        $shipper->awb_number,
                        'manifested',
                        $previousStatus,
                        'Status moved to Manifested (carrier booking already done on payment). Tracking: '.$existingTracking->shipment_identification_number,
                        $manifestCustomerId,
                        'admin'
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Already manifested on payment. Status moved to Manifested.',
                        'tracking_number' => $existingTracking->shipment_identification_number,
                        'shipper_id' => $shipperId,
                        'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                        'already_manifested' => true,
                    ]);
                }
            }

            // Ready flow me Primus ke liye custom label chahiye hota hai (jo normally Packed me banta hai).
            // Yahan auto-label bana kar store kar dete hain taaki manifest fail na ho, status change nahi hota.
            if ($targetStatus === 'ready' && empty($shipper->custom_label)) {
                try {
                    [$autoLabelPath, $autoLabelUrl] = $this->storeCustomLabelFile(
                        $shipper,
                        '<div style="font-family:Arial,sans-serif;padding:16px;"><h2>Shipping Label (Auto on payment)</h2><p>AWB: '.htmlspecialchars((string) ($shipper->awb_number ?: $shipper->id), ENT_QUOTES, 'UTF-8').'</p></div>'
                    );
                    $shipper->custom_label = $autoLabelUrl;
                    $shipper->save();
                } catch (\Throwable $e) {
                    \Log::warning('Auto custom label failed before prepaid manifest (ready flow).', [
                        'shipper_id' => $shipper->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Determine the network from the shipping method's CourierService
            $shippingMethod = $this->resolveShippingMethod($shipper);
            $courierService = $this->findCourierService($shippingMethod, $shipper->id);
            $network = $courierService ? strtolower(trim($courierService->network)) : 'ups';

            // Resolve the API provider: database-first (courier_services.api_provider)
            // with a fallback to the legacy string-matching methods.
            $apiProvider = $this->resolveApiProvider($shippingMethod, $shipper, $courierService);

            \Log::info('prepaidManifest: Shipper #'.$shipperId.' → shipping_method="'.$shippingMethod.'" → network="'.$network.'" → api_provider="'.$apiProvider.'"');

            // Route to appropriate API based on the resolved provider.
            if ($apiProvider === 'shipuniversal') {
                $shipUniversalResult = $this->callShipUniversalApiFromDb($shipper);
                if (! $shipUniversalResult['success']) {
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json([
                        'success' => false,
                        'message' => 'ShipUniversal API Failed: '.($shipUniversalResult['message'] ?? 'Unknown error'),
                        'shipuniversal_response' => $shipUniversalResult['data'] ?? null,
                        'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                    ], 500);
                }

                $apiResponse = $shipUniversalResult['data'] ?? [];
                $trackingNumber = $this->extractShipUniversalTrackingNumber($apiResponse);
                $labelUrl = $this->extractShipUniversalLabelUrl($apiResponse);

                if (empty($trackingNumber)) {
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json([
                        'success' => false,
                        'message' => $previousStatus === 'ready'
                            ? 'ShipUniversal created no usable AWB number. The shipment has been moved back to Draft.'
                            : 'ShipUniversal created no usable AWB number. The shipment remains in '.$previousStatus.' status.',
                        'shipuniversal_response' => $apiResponse,
                        'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                    ], 502);
                }

                try {
                    $this->persistPrepaidShipUniversalManifest(
                        $shipper,
                        $manifestCustomerId,
                        $apiResponse,
                        $trackingNumber,
                        $labelUrl,
                        false,
                        $targetStatus,
                        null,
                        $adminId
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to store prepaid ShipUniversal manifest: '.$e->getMessage());
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to store tracking data: '.$e->getMessage(),
                    ], 500);
                }

                // Payment is cut only AFTER the manifest succeeds.
                $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);
                $chargeNote = $chargeResult['charged']
                    ? ' Payment of ₹'.number_format($chargeResult['amount'], 2).' deducted from the customer wallet.'
                    : ($chargeResult['message'] ? ' '.$chargeResult['message'] : '');

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via ShipUniversal!'.$chargeNote,
                    'tracking_number' => $trackingNumber,
                    'label_url' => $labelUrl,
                    'shipper_id' => $shipperId,
                    'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                    'network' => 'ShipUniversal',
                    'shipuniversal_response' => $apiResponse,
                    'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                    'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                    'new_balance' => $chargeResult['new_balance'],
                ]);
            } elseif ($apiProvider === 'primus') {
                $primusResult = app(PrimusShipmentService::class)->manifest(
                    $shipper,
                    (int) $manifestCustomerId,
                    false,
                    $targetStatus
                );

                if (! $primusResult['success']) {
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json([
                        'success' => false,
                        'message' => 'Primus API Failed: '.($primusResult['message'] ?? 'Unknown error'),
                        'request_payload' => $primusResult['payload'] ?? null,
                    ], 422);
                }

                // Create a manifest record from the manifests table
                $this->createManifestRecord($shipper->id, $manifestCustomerId);

                // Payment is cut only AFTER the manifest succeeds.
                $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);
                $chargeNote = $chargeResult['charged']
                    ? ' Payment of ₹'.number_format($chargeResult['amount'], 2).' deducted from the customer wallet.'
                    : ($chargeResult['message'] ? ' '.$chargeResult['message'] : '');

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via Primus!'.$chargeNote,
                    'tracking_number' => $primusResult['tracking_number'],
                    'label_url' => $primusResult['label'] ?? null,
                    'shipper_id' => $shipperId,
                    'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                    'network' => 'Primus',
                    'request_payload' => $primusResult['payload'] ?? null,
                    'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                    'new_balance' => $chargeResult['new_balance'],
                ]);
                // Priority 0: Overseas Logistic for UNITED CANADA DDP /
                //              UNITED CANADA E-COMMERCE and ARAMEX GPX (Australia).
            } elseif ($apiProvider === 'overseas' || $this->isOverseasLogisticMethod($shippingMethod)) {
                // Call Overseas Logistic API
                $overseasResult = $this->callOverseasLogisticApiFromDb($shipper);
                if (! $overseasResult['success']) {
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);
                    $overseasMsg = $this->overseasValueToString($overseasResult['message'] ?? 'Unknown error');

                    return response()->json([
                        'success' => false,
                        'message' => 'Overseas Logistic API Failed: '.$overseasMsg,
                        'overseas_response' => $overseasResult['data'] ?? null,
                        'request_payload' => $overseasResult['request_payload'] ?? null,
                    ], 500);
                }

                // Overseas Logistic succeeded - store tracking data
                $apiResponse = $overseasResult['data'] ?? [];
                $trackingNumber = $this->extractOverseasTrackingNumber($apiResponse);
                $labelUrl = $this->extractOverseasLabelUrl($apiResponse);
                $boxLabelUrl = $this->extractOverseasBoxLabelUrl($apiResponse);

                $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                try {
                    ShipmentTracking::updateOrCreate(
                        ['shipper_id' => $shipperId],
                        [
                            'customer_id' => $manifestCustomerId,
                            'create_shipment_id' => $createShipment ? $createShipment->id : null,
                            'response_status_code' => '1',
                            'response_status_description' => 'Overseas Logistic shipment created',
                            'shipment_identification_number' => $trackingNumber,
                            'total_charges_currency' => 'INR',
                            'total_charges_amount' => null,
                            'billing_weight_uom' => 'KGS',
                            'billing_weight' => null,
                            'package_results' => ($labelUrl || $boxLabelUrl) ? array_filter([
                                'LabelURL' => $labelUrl,
                                'BoxLabelURL' => $boxLabelUrl,
                            ]) : null,
                            'raw_response' => $apiResponse,
                            'status' => 'created',
                        ]
                    );

                    // Update shipper status to target (manifested, or ready when from Confirm Payment)
                    $shipper->status = $targetStatus;
                    $shipper->save();

                    // Create tracking record for target status
                    Tracking::create([
                        'awb_number' => $shipper->awb_number,
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment ? $createShipment->id : null,
                        'uwc_id' => $shipper->awb_number,
                        'title' => Tracking::getTitleForStatus($targetStatus),
                        'status' => $targetStatus,
                    ]);

                    // Log the manifested status change
                    ShipmentLog::logStatus(
                        $shipper->id,
                        $shipper->awb_number,
                        $targetStatus,
                        $previousStatus,
                        'Prepaid shipment manifested via Overseas Logistic. Tracking: '.($trackingNumber ?? 'N/A'),
                        $manifestCustomerId,
                        'admin'
                    );

                    \Log::info('Prepaid shipment manifested via Overseas Logistic: '.($trackingNumber ?? 'N/A'));

                    // Create a manifest record from the manifests table
                    $this->createManifestRecord($shipper->id, $manifestCustomerId);
                } catch (\Exception $e) {
                    \Log::error('Failed to store prepaid shipment tracking for Overseas Logistic manifest: '.$e->getMessage());
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json(['success' => false, 'message' => 'Failed to store tracking data: '.$e->getMessage()], 500);
                }

                // Payment is cut only AFTER the manifest succeeds.
                $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);
                $chargeNote = $chargeResult['charged']
                    ? ' Payment of ₹'.number_format($chargeResult['amount'], 2).' deducted from the customer wallet.'
                    : ($chargeResult['message'] ? ' '.$chargeResult['message'] : '');

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via Overseas Logistic!'.$chargeNote,
                    'tracking_number' => $trackingNumber,
                    'label_url' => $labelUrl,
                    'shipper_id' => $shipperId,
                    'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                    'network' => 'Overseas Logistic',
                    'overseas_response' => $apiResponse,
                    'request_payload' => $overseasResult['request_payload'] ?? null,
                    'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                    'new_balance' => $chargeResult['new_balance'],
                ]);
            } elseif ($apiProvider === 'postshipping' || $this->isPostShippingMethod($shippingMethod)) {
                // Priority 1: PostShipping (DPD/UK) for UNITED AIR PREMIUM DDP / UNITED PRIOR POST DDP
                // Call PostShipping API
                $postShippingResult = $this->callPostShippingApiFromDb($shipper);
                if (! $postShippingResult['success']) {
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json([
                        'success' => false,
                        'message' => 'PostShipping API Failed: '.($postShippingResult['message'] ?? 'Unknown error'),
                        'postshipping_response' => $postShippingResult['data'] ?? null,
                        'request_payload' => $postShippingResult['request_payload'] ?? null,
                    ], 500);
                }

                // PostShipping succeeded - store tracking data
                $apiResponse = $postShippingResult['data'] ?? [];
                $trackingNumber = $this->extractPostShippingTrackingNumber($apiResponse);
                $labelUrl = $this->extractPostShippingLabelUrl($apiResponse);

                $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                try {
                    ShipmentTracking::updateOrCreate(
                        ['shipper_id' => $shipperId],
                        [
                            'customer_id' => $manifestCustomerId,
                            'create_shipment_id' => $createShipment ? $createShipment->id : null,
                            'response_status_code' => '1',
                            'response_status_description' => 'PostShipping shipment created',
                            'shipment_identification_number' => $trackingNumber,
                            'total_charges_currency' => 'INR',
                            'total_charges_amount' => null,
                            'billing_weight_uom' => 'KGS',
                            'billing_weight' => null,
                            'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                            'raw_response' => $apiResponse,
                            'status' => 'created',
                        ]
                    );

                    // Update shipper status to target (manifested, or ready when from Confirm Payment)
                    $shipper->status = $targetStatus;
                    $shipper->save();

                    // Create tracking record for target status
                    Tracking::create([
                        'awb_number' => $shipper->awb_number,
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment ? $createShipment->id : null,
                        'uwc_id' => $shipper->awb_number,
                        'title' => Tracking::getTitleForStatus($targetStatus),
                        'status' => $targetStatus,
                    ]);

                    \Log::info('Prepaid shipment manifested via PostShipping: '.($trackingNumber ?? 'N/A'));

                    // Create a manifest record from the manifests table
                    $this->createManifestRecord($shipper->id, $manifestCustomerId);
                } catch (\Exception $e) {
                    \Log::error('Failed to store prepaid shipment tracking for PostShipping manifest: '.$e->getMessage());
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json(['success' => false, 'message' => 'Failed to store tracking data: '.$e->getMessage()], 500);
                }

                // Payment is cut only AFTER the manifest succeeds.
                $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);
                $chargeNote = $chargeResult['charged']
                    ? ' Payment of ₹'.number_format($chargeResult['amount'], 2).' deducted from the customer wallet.'
                    : ($chargeResult['message'] ? ' '.$chargeResult['message'] : '');

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via PostShipping!'.$chargeNote,
                    'tracking_number' => $trackingNumber,
                    'label_url' => $labelUrl,
                    'shipper_id' => $shipperId,
                    'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                    'network' => 'PostShipping',
                    'postshipping_response' => $apiResponse,
                    'request_payload' => $postShippingResult['request_payload'] ?? null,
                    'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                    'new_balance' => $chargeResult['new_balance'],
                ]);
            } elseif ($apiProvider === 'flyingtigers' || $this->isFlyingTigersMethod($shippingMethod)) {
                // Call Flying Tigers API (UNITED ECO POST)
                $flyingTigersResult = $this->callFlyingTigersApiFromDb($shipper);
                if (! $flyingTigersResult['success']) {
                    // Check if this is an address error → return fallback info for dropdown option
                    if (! empty($flyingTigersResult['is_address_error'])) {
                        // The booking failed. If this shipment was in Ready (Confirm Payment flow),
                        // take it back to Draft immediately — the admin can still pick the
                        // UNITED CLASSIC fallback or cancel from the modal.
                        $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);
                        $fallbackInfo = $this->getFlyingTigersAddressErrorFallbackInfo($shipper, $manifestCustomerId);

                        return response()->json([
                            'success' => false,
                            'message' => 'The address provided appears to be incorrect or incomplete for UNITED ECO POST. You can ship via UNITED CLASSIC (Ship Global) instead.',
                            'is_address_error' => true,
                            'shipper_id' => $shipperId,
                            'classic_rate' => $fallbackInfo['classic_rate'] ?? null,
                            'paid_amount' => $fallbackInfo['paid_amount'] ?? null,
                            'difference' => $fallbackInfo['difference'] ?? null,
                            'wallet_action' => $fallbackInfo['wallet_action'] ?? 'none',
                            'wallet_amount' => $fallbackInfo['wallet_amount'] ?? 0,
                            'wallet_balance' => $fallbackInfo['wallet_balance'] ?? 0,
                            'total_weight' => $fallbackInfo['total_weight'] ?? 0,
                        ], 422);
                    }

                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json([
                        'success' => false,
                        'message' => 'Flying Tigers API Failed: '.($flyingTigersResult['message'] ?? 'Unknown error'),
                        'flyingtigers_response' => $flyingTigersResult['data'] ?? null,
                    ], 500);
                }

                // Flying Tigers succeeded - store tracking data
                $apiResponse = $flyingTigersResult['data'] ?? [];
                $trackingNumber = $this->extractFlyingTigersTrackingNumber($apiResponse);
                $labelUrl = $this->extractFlyingTigersLabelUrl($apiResponse);

                $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                try {
                    ShipmentTracking::updateOrCreate(
                        ['shipper_id' => $shipperId],
                        [
                            'customer_id' => $manifestCustomerId,
                            'create_shipment_id' => $createShipment ? $createShipment->id : null,
                            'response_status_code' => '1',
                            'response_status_description' => 'Flying Tigers shipment created',
                            'shipment_identification_number' => $trackingNumber,
                            'total_charges_currency' => 'INR',
                            'total_charges_amount' => null,
                            'billing_weight_uom' => 'KGS',
                            'billing_weight' => null,
                            'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                            'raw_response' => $apiResponse,
                            'status' => 'created',
                        ]
                    );

                    // Update shipper status to target (manifested, or ready when from Confirm Payment)
                    $shipper->status = $targetStatus;
                    $shipper->save();

                    // Create tracking record for target status
                    Tracking::create([
                        'awb_number' => $shipper->awb_number,
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment ? $createShipment->id : null,
                        'uwc_id' => $shipper->awb_number,
                        'title' => Tracking::getTitleForStatus($targetStatus),
                        'status' => $targetStatus,
                    ]);

                    \Log::info('Prepaid shipment manifested via Flying Tigers: '.($trackingNumber ?? 'N/A'));

                    // Create a manifest record from the manifests table
                    $this->createManifestRecord($shipper->id, $manifestCustomerId);
                } catch (\Exception $e) {
                    \Log::error('Failed to store prepaid shipment tracking for Flying Tigers manifest: '.$e->getMessage());
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json(['success' => false, 'message' => 'Failed to store tracking data: '.$e->getMessage()], 500);
                }

                // Payment is cut only AFTER the manifest succeeds.
                $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);
                $chargeNote = $chargeResult['charged']
                    ? ' Payment of ₹'.number_format($chargeResult['amount'], 2).' deducted from the customer wallet.'
                    : ($chargeResult['message'] ? ' '.$chargeResult['message'] : '');

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via Flying Tigers!'.$chargeNote,
                    'tracking_number' => $trackingNumber,
                    'label_url' => $labelUrl,
                    'shipper_id' => $shipperId,
                    'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                    'network' => 'Flying Tigers',
                    'flyingtigers_response' => $apiResponse,
                    'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                    'new_balance' => $chargeResult['new_balance'],
                ]);
            } elseif ($apiProvider === 'shipglobal' || $network === 'ship global' || $network === 'shipglobal') {
                // Call Ship Global API
                $shipGlobalResult = $this->callShipGlobalApiFromDb($shipper);
                if (! $shipGlobalResult['success']) {
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json([
                        'success' => false,
                        'message' => 'Ship Global API Failed: '.($shipGlobalResult['message'] ?? 'Unknown error'),
                        'ship_global_response' => $shipGlobalResult['data'] ?? null,
                    ], 500);
                }

                // Ship Global succeeded - store tracking data
                $apiResponse = $shipGlobalResult['data'] ?? [];
                $trackingNumber = null;
                // Ship Global returns tracking/reference number in various possible formats
                // Priority: waybill_number > tracking_number > awb_number > order_number
                if (isset($apiResponse['data']) && isset($apiResponse['data']['waybill_number']) && ! empty($apiResponse['data']['waybill_number'])) {
                    $trackingNumber = $apiResponse['data']['waybill_number'];
                } elseif (isset($apiResponse['waybill_number']) && ! empty($apiResponse['waybill_number'])) {
                    $trackingNumber = $apiResponse['waybill_number'];
                } elseif (isset($apiResponse['tracking_number'])) {
                    $trackingNumber = $apiResponse['tracking_number'];
                } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['tracking_number'])) {
                    $trackingNumber = $apiResponse['data']['tracking_number'];
                } elseif (isset($apiResponse['awb_number'])) {
                    $trackingNumber = $apiResponse['awb_number'];
                } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['awb_number'])) {
                    $trackingNumber = $apiResponse['data']['awb_number'];
                } elseif (isset($apiResponse['waybill'])) {
                    $trackingNumber = $apiResponse['waybill'];
                } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['waybill'])) {
                    $trackingNumber = $apiResponse['data']['waybill'];
                } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['order_number'])) {
                    // If no waybill/tracking yet, use order_number as reference (label: "manual" case)
                    $trackingNumber = $apiResponse['data']['order_number'];
                } elseif (isset($apiResponse['order_number'])) {
                    $trackingNumber = $apiResponse['order_number'];
                }

                $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                try {
                    ShipmentTracking::updateOrCreate(
                        ['shipper_id' => $shipperId],
                        [
                            'customer_id' => $manifestCustomerId,
                            'create_shipment_id' => $createShipment ? $createShipment->id : null,
                            'response_status_code' => '1',
                            'response_status_description' => 'Ship Global order created',
                            'shipment_identification_number' => $trackingNumber,
                            'total_charges_currency' => 'INR',
                            'total_charges_amount' => null,
                            'billing_weight_uom' => 'KGS',
                            'billing_weight' => null,
                            'package_results' => null,
                            'raw_response' => $apiResponse,
                            'status' => 'created',
                        ]
                    );

                    // Update shipper status to target (manifested, or ready when from Confirm Payment)
                    $shipper->status = $targetStatus;
                    $shipper->save();

                    // Create tracking record for target status
                    Tracking::create([
                        'awb_number' => $shipper->awb_number,
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment ? $createShipment->id : null,
                        'uwc_id' => $shipper->awb_number,
                        'title' => Tracking::getTitleForStatus($targetStatus),
                        'status' => $targetStatus,
                    ]);

                    \Log::info('Prepaid shipment manifested via Ship Global: '.($trackingNumber ?? 'N/A'));

                    // Create a manifest record from the manifests table
                    $this->createManifestRecord($shipper->id, $manifestCustomerId);
                } catch (\Exception $e) {
                    \Log::error('Failed to store prepaid shipment tracking for Ship Global manifest: '.$e->getMessage());
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json(['success' => false, 'message' => 'Failed to store tracking data: '.$e->getMessage()], 500);
                }

                // Payment is cut only AFTER the manifest succeeds.
                $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);
                $chargeNote = $chargeResult['charged']
                    ? ' Payment of ₹'.number_format($chargeResult['amount'], 2).' deducted from the customer wallet.'
                    : ($chargeResult['message'] ? ' '.$chargeResult['message'] : '');

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via Ship Global!'.$chargeNote,
                    'tracking_number' => $trackingNumber,
                    'shipper_id' => $shipperId,
                    'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                    'network' => 'Ship Global',
                    'ship_global_response' => $apiResponse,
                    'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                    'new_balance' => $chargeResult['new_balance'],
                ]);

            } else {
                // Default: Call UPS Ship API
                $payloadResult = $this->buildUpsShipPayloadFromDb($shipper);
                if (! $payloadResult['success']) {
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json(['success' => false, 'message' => $payloadResult['message']], 400);
                }
                $upsPayload = $payloadResult['payload'];

                $upsResult = $this->callUpsShipApiInternal($upsPayload);

                if (! $upsResult['success']) {
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);
                    $errorMessage = $upsResult['message'] ?? 'Unknown UPS error';

                    return response()->json([
                        'success' => false,
                        'message' => 'UPS Shipment Failed: '.$errorMessage,
                        'rawResponse' => $upsResult['rawResponse'] ?? null,
                    ], 500);
                }

                // UPS succeeded - store tracking data
                $shipmentResponse = $upsResult['shipmentResponse'];
                $trackingNumber = $shipmentResponse['ShipmentResults']['PackageResults']['TrackingNumber']
                    ?? $shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber']
                    ?? null;

                $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                try {
                    ShipmentTracking::updateOrCreate(
                        ['shipper_id' => $shipperId],
                        [
                            'customer_id' => $manifestCustomerId,
                            'create_shipment_id' => $createShipment ? $createShipment->id : null,
                            'response_status_code' => $shipmentResponse['Response']['ResponseStatus']['Code'] ?? null,
                            'response_status_description' => $shipmentResponse['Response']['ResponseStatus']['Description'] ?? null,
                            'transaction_identifier' => $shipmentResponse['Response']['TransactionReference']['TransactionIdentifier'] ?? null,
                            'customer_context' => $shipmentResponse['Response']['TransactionReference']['CustomerContext'] ?? null,
                            'shipment_identification_number' => $shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber'] ?? null,
                            'transportation_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TransportationCharges']['CurrencyCode'] ?? null,
                            'transportation_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TransportationCharges']['MonetaryValue'] ?? null,
                            'service_options_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['ServiceOptionsCharges']['CurrencyCode'] ?? null,
                            'service_options_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['ServiceOptionsCharges']['MonetaryValue'] ?? null,
                            'total_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TotalCharges']['CurrencyCode'] ?? null,
                            'total_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TotalCharges']['MonetaryValue'] ?? null,
                            'billing_weight_uom' => $shipmentResponse['ShipmentResults']['BillingWeight']['UnitOfMeasurement']['Code'] ?? null,
                            'billing_weight' => $shipmentResponse['ShipmentResults']['BillingWeight']['Weight'] ?? null,
                            'package_results' => $shipmentResponse['ShipmentResults']['PackageResults'] ?? null,
                            'raw_response' => $shipmentResponse,
                            'status' => 'created',
                        ]
                    );

                    // Update shipper status to target (manifested, or ready when from Confirm Payment)
                    $shipper->status = $targetStatus;
                    $shipper->save();

                    // Create tracking record for target status
                    Tracking::create([
                        'awb_number' => $shipper->awb_number,
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment ? $createShipment->id : null,
                        'uwc_id' => $shipper->awb_number,
                        'title' => Tracking::getTitleForStatus($targetStatus),
                        'status' => $targetStatus,
                    ]);

                    // Log the manifested status change
                    ShipmentLog::logStatus(
                        $shipper->id,
                        $shipper->awb_number,
                        $targetStatus,
                        $previousStatus,
                        'Prepaid shipment manifested via UPS. Tracking: '.($trackingNumber ?? 'N/A'),
                        $manifestCustomerId,
                        'admin'
                    );

                    \Log::info('Prepaid shipment manifested via UPS: '.($shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber'] ?? 'N/A'));

                    // Create a manifest record from the manifests table
                    $this->createManifestRecord($shipper->id, $manifestCustomerId);
                } catch (\Exception $e) {
                    \Log::error('Failed to store prepaid shipment tracking for manifest: '.$e->getMessage());
                    $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $previousStatus, $adminId);

                    return response()->json(['success' => false, 'message' => 'Failed to store tracking data: '.$e->getMessage()], 500);
                }

                // Payment is cut only AFTER the manifest succeeds.
                $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);
                $chargeNote = $chargeResult['charged']
                    ? ' Payment of ₹'.number_format($chargeResult['amount'], 2).' deducted from the customer wallet.'
                    : ($chargeResult['message'] ? ' '.$chargeResult['message'] : '');

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via UPS!'.$chargeNote,
                    'tracking_number' => $trackingNumber,
                    'shipper_id' => $shipperId,
                    'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                    'network' => 'UPS',
                    'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                    'new_balance' => $chargeResult['new_balance'],
                ]);
            }
        } catch (\Exception $e) {
            if (isset($shipper, $previousStatus)) {
                $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId ?? 0, $previousStatus, $adminId ?? 0);
            }

            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    /**
     * Bulk manifest multiple prepaid shipments at once.
     */
    public function prepaidBulkManifestShipments(Request $request)
    {
        try {
            if (! auth()->guard('admin')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $adminId = (int) auth()->guard('admin')->id();
            $shipperIds = $request->input('shipper_ids', []);

            if (empty($shipperIds) || ! is_array($shipperIds)) {
                return response()->json(['success' => false, 'message' => 'No shipments selected.'], 400);
            }

            $results = [
                'success' => [],
                'failed' => [],
                'total' => count($shipperIds),
            ];

            // Bulk manifest: ALL selected shipments share ONE manifest number.
            // Generate it once before the loop and reuse it for every successful
            // shipment in the batch (single manifest flow keeps unique numbers).
            $bulkManifestNumber = Manifest::generateManifestNumber();

            foreach ($shipperIds as $shipperId) {
                try {
                    $shipper = ShipperInfo::where('id', $shipperId)
                        ->where('shipment_type', 5)
                        ->first();

                    if (! $shipper) {
                        $results['failed'][] = ['shipper_id' => $shipperId, 'message' => 'Shipment not found'];

                        continue;
                    }

                    if (! in_array($shipper->status, ['ready', 'packed'])) {
                        $results['failed'][] = ['shipper_id' => $shipperId, 'message' => 'Not in Ready or Packed status'];

                        continue;
                    }

                    $manifestCustomerId = (int) ($shipper->customer_id ?? 0);
                    if ($manifestCustomerId <= 0) {
                        $results['failed'][] = ['shipper_id' => $shipperId, 'message' => 'Shipment has no linked customer. Link an exporter customer before manifesting.'];

                        continue;
                    }

                    $bulkPreviousStatus = $shipper->status;

                    // Determine the network from the shipping method's CourierService
                    $shippingMethod = $this->resolveShippingMethod($shipper);
                    $courierService = $this->findCourierService($shippingMethod, $shipper->id);
                    $network = $courierService ? strtolower(trim($courierService->network)) : 'ups';

                    // Resolve the API provider: database-first (courier_services.api_provider)
                    // with a fallback to the legacy string-matching methods.
                    $apiProvider = $this->resolveApiProvider($shippingMethod, $shipper, $courierService);

                    \Log::info('prepaidBulkManifest: Shipper #'.$shipperId.' → network="'.$network.'" → api_provider="'.$apiProvider.'"');

                    if ($apiProvider === 'shipuniversal') {
                        $shipUniversalResult = $this->callShipUniversalApiFromDb($shipper);
                        if (! $shipUniversalResult['success']) {
                            $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $bulkPreviousStatus, $adminId);
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'ShipUniversal API error: '.($shipUniversalResult['message'] ?? 'Unknown'),
                                'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                                'shipuniversal_response' => $shipUniversalResult['data'] ?? null,
                            ];

                            continue;
                        }

                        $apiResponse = $shipUniversalResult['data'] ?? [];
                        $trackingNumber = $this->extractShipUniversalTrackingNumber($apiResponse);
                        $labelUrl = $this->extractShipUniversalLabelUrl($apiResponse);

                        if (empty($trackingNumber)) {
                            $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $bulkPreviousStatus, $adminId);
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => $bulkPreviousStatus === 'ready'
                                    ? 'ShipUniversal created no usable AWB number. The shipment has been moved back to Draft.'
                                    : 'ShipUniversal created no usable AWB number. The shipment remains in '.$shipper->status.' status.',
                                'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                                'shipuniversal_response' => $apiResponse,
                            ];

                            continue;
                        }

                        $this->persistPrepaidShipUniversalManifest(
                            $shipper,
                            $manifestCustomerId,
                            $apiResponse,
                            $trackingNumber,
                            $labelUrl,
                            true,
                            'manifested',
                            $bulkManifestNumber,
                            $adminId
                        );

                        // Payment is cut only AFTER the manifest succeeds.
                        $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $trackingNumber,
                            'label_url' => $labelUrl,
                            'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                            'network' => 'ShipUniversal',
                            'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                            'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                            'new_balance' => $chargeResult['new_balance'],
                        ];

                        \Log::info('Prepaid bulk manifest: shipment '.$shipperId.' manifested via ShipUniversal.');
                    } elseif ($apiProvider === 'primus') {
                        $primusResult = app(PrimusShipmentService::class)->manifest(
                            $shipper,
                            (int) $manifestCustomerId,
                            true
                        );

                        if (! $primusResult['success']) {
                            $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $bulkPreviousStatus, $adminId);
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'Primus API error: '.($primusResult['message'] ?? 'Unknown'),
                                'request_payload' => $primusResult['payload'] ?? null,
                            ];

                            continue;
                        }

                        // Create a manifest record from the manifests table
                        // (bulk: share the single batch manifest number)
                        $this->createManifestRecord($shipper->id, $manifestCustomerId, $bulkManifestNumber);

                        // Payment is cut only AFTER the manifest succeeds.
                        $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $primusResult['tracking_number'],
                            'label_url' => $primusResult['label'] ?? null,
                            'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                            'network' => 'Primus',
                            'request_payload' => $primusResult['payload'] ?? null,
                            'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                            'new_balance' => $chargeResult['new_balance'],
                        ];

                        \Log::info('Prepaid bulk manifest: shipment '.$shipperId.' manifested via Primus.');
                        // Priority 0: Overseas Logistic for UNITED CANADA DDP /
                        //              UNITED CANADA E-COMMERCE and ARAMEX GPX (Australia).
                    } elseif ($apiProvider === 'overseas' || $this->isOverseasLogisticMethod($shippingMethod)) {
                        // Call Overseas Logistic API
                        $overseasResult = $this->callOverseasLogisticApiFromDb($shipper);

                        if (! $overseasResult['success']) {
                            $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $bulkPreviousStatus, $adminId);
                            $overseasMsg = $this->overseasValueToString($overseasResult['message'] ?? 'Unknown');
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'Overseas Logistic API error: '.$overseasMsg,
                                'request_payload' => $overseasResult['request_payload'] ?? null,
                                'overseas_response' => $overseasResult['data'] ?? null,
                            ];

                            continue;
                        }

                        $apiResponse = $overseasResult['data'] ?? [];
                        $trackingNumber = $this->extractOverseasTrackingNumber($apiResponse);
                        $labelUrl = $this->extractOverseasLabelUrl($apiResponse);
                        $boxLabelUrl = $this->extractOverseasBoxLabelUrl($apiResponse);

                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                        ShipmentTracking::updateOrCreate(
                            ['shipper_id' => $shipperId],
                            [
                                'customer_id' => $manifestCustomerId,
                                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                                'response_status_code' => '1',
                                'response_status_description' => 'Overseas Logistic shipment created',
                                'shipment_identification_number' => $trackingNumber,
                                'total_charges_currency' => 'INR',
                                'total_charges_amount' => null,
                                'billing_weight_uom' => 'KGS',
                                'billing_weight' => null,
                                'package_results' => ($labelUrl || $boxLabelUrl) ? array_filter([
                                    'LabelURL' => $labelUrl,
                                    'BoxLabelURL' => $boxLabelUrl,
                                ]) : null,
                                'raw_response' => $apiResponse,
                                'status' => 'created',
                            ]
                        );

                        $shipper->status = 'manifested';
                        $shipper->save();

                        // Create a manifest record from the manifests table
                        // (bulk: share the single batch manifest number)
                        $this->createManifestRecord($shipper->id, $manifestCustomerId, $bulkManifestNumber);

                        // Create tracking record for manifested status
                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();
                        Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                            'title' => Tracking::getTitleForStatus('manifested'),
                            'status' => 'manifested',
                        ]);

                        // Payment is cut only AFTER the manifest succeeds.
                        $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $trackingNumber,
                            'label_url' => $labelUrl,
                            'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                            'network' => 'Overseas Logistic',
                            'request_payload' => $overseasResult['request_payload'] ?? null,
                            'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                            'new_balance' => $chargeResult['new_balance'],
                        ];

                        \Log::info('Prepaid bulk manifest: shipment '.$shipperId.' manifested via Overseas Logistic.');

                        ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', $bulkPreviousStatus, 'Prepaid shipment manifested via Overseas Logistic (bulk). Tracking: '.($trackingNumber ?? 'N/A'), $manifestCustomerId, 'admin');

                    } elseif ($apiProvider === 'postshipping' || $this->isPostShippingMethod($shippingMethod)) {
                        // Priority 1: PostShipping (DPD/UK) for UNITED AIR PREMIUM DDP / UNITED PRIOR POST DDP
                        // Call PostShipping API
                        $postShippingResult = $this->callPostShippingApiFromDb($shipper);

                        if (! $postShippingResult['success']) {
                            $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $bulkPreviousStatus, $adminId);
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'PostShipping API error: '.($postShippingResult['message'] ?? 'Unknown'),
                                'request_payload' => $postShippingResult['request_payload'] ?? null,
                                'postshipping_response' => $postShippingResult['data'] ?? null,
                            ];

                            continue;
                        }

                        $apiResponse = $postShippingResult['data'] ?? [];
                        $trackingNumber = $this->extractPostShippingTrackingNumber($apiResponse);
                        $labelUrl = $this->extractPostShippingLabelUrl($apiResponse);

                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                        ShipmentTracking::updateOrCreate(
                            ['shipper_id' => $shipperId],
                            [
                                'customer_id' => $manifestCustomerId,
                                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                                'response_status_code' => '1',
                                'response_status_description' => 'PostShipping shipment created',
                                'shipment_identification_number' => $trackingNumber,
                                'total_charges_currency' => 'INR',
                                'total_charges_amount' => null,
                                'billing_weight_uom' => 'KGS',
                                'billing_weight' => null,
                                'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                                'raw_response' => $apiResponse,
                                'status' => 'created',
                            ]
                        );

                        $shipper->status = 'manifested';
                        $shipper->save();

                        // Create a manifest record from the manifests table
                        // (bulk: share the single batch manifest number)
                        $this->createManifestRecord($shipper->id, $manifestCustomerId, $bulkManifestNumber);

                        // Create tracking record for manifested status
                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();
                        Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                            'title' => Tracking::getTitleForStatus('manifested'),
                            'status' => 'manifested',
                        ]);

                        // Payment is cut only AFTER the manifest succeeds.
                        $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $trackingNumber,
                            'label_url' => $labelUrl,
                            'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                            'network' => 'PostShipping',
                            'request_payload' => $postShippingResult['request_payload'] ?? null,
                            'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                            'new_balance' => $chargeResult['new_balance'],
                        ];

                        \Log::info('Prepaid bulk manifest: shipment '.$shipperId.' manifested via PostShipping.');

                        ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', $bulkPreviousStatus, 'Prepaid shipment manifested via PostShipping (bulk). Tracking: '.($trackingNumber ?? 'N/A'), $manifestCustomerId, 'admin');

                    } elseif ($apiProvider === 'flyingtigers' || $this->isFlyingTigersMethod($shippingMethod)) {
                        // Call Flying Tigers API (UNITED ECO POST)
                        $flyingTigersResult = $this->callFlyingTigersApiFromDb($shipper);

                        if (! $flyingTigersResult['success']) {
                            // Check if this is an address error → return fallback info for dropdown option
                            if (! empty($flyingTigersResult['is_address_error'])) {
                                // The booking failed. If this shipment was in Ready (Confirm Payment flow),
                                // take it back to Draft immediately — the admin can still pick the
                                // UNITED CLASSIC fallback or cancel.
                                $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $bulkPreviousStatus, $adminId);
                                $fallbackInfo = $this->getFlyingTigersAddressErrorFallbackInfo($shipper, $manifestCustomerId);
                                $results['address_errors'][] = [
                                    'shipper_id' => $shipperId,
                                    'message' => 'Address is incorrect for UNITED ECO POST. You can ship via UNITED CLASSIC (Ship Global) instead.',
                                    'is_address_error' => true,
                                    'classic_rate' => $fallbackInfo['classic_rate'] ?? null,
                                    'paid_amount' => $fallbackInfo['paid_amount'] ?? null,
                                    'difference' => $fallbackInfo['difference'] ?? null,
                                    'wallet_action' => $fallbackInfo['wallet_action'] ?? 'none',
                                    'wallet_amount' => $fallbackInfo['wallet_amount'] ?? 0,
                                    'wallet_balance' => $fallbackInfo['wallet_balance'] ?? 0,
                                    'total_weight' => $fallbackInfo['total_weight'] ?? 0,
                                ];
                                \Log::info('Prepaid bulk manifest: shipment '.$shipperId.' address error — awaiting admin decision for UNITED CLASSIC fallback.');

                                continue;
                            }
                            $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $bulkPreviousStatus, $adminId);
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'Flying Tigers API error: '.($flyingTigersResult['message'] ?? 'Unknown'),
                            ];

                            continue;
                        }

                        $apiResponse = $flyingTigersResult['data'] ?? [];
                        $trackingNumber = $this->extractFlyingTigersTrackingNumber($apiResponse);
                        $labelUrl = $this->extractFlyingTigersLabelUrl($apiResponse);

                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                        ShipmentTracking::updateOrCreate(
                            ['shipper_id' => $shipperId],
                            [
                                'customer_id' => $manifestCustomerId,
                                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                                'response_status_code' => '1',
                                'response_status_description' => 'Flying Tigers shipment created',
                                'shipment_identification_number' => $trackingNumber,
                                'total_charges_currency' => 'INR',
                                'total_charges_amount' => null,
                                'billing_weight_uom' => 'KGS',
                                'billing_weight' => null,
                                'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                                'raw_response' => $apiResponse,
                                'status' => 'created',
                            ]
                        );

                        $shipper->status = 'manifested';
                        $shipper->save();

                        // Create a manifest record from the manifests table
                        // (bulk: share the single batch manifest number)
                        $this->createManifestRecord($shipper->id, $manifestCustomerId, $bulkManifestNumber);

                        // Create tracking record for manifested status
                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();
                        Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                            'title' => Tracking::getTitleForStatus('manifested'),
                            'status' => 'manifested',
                        ]);

                        // Payment is cut only AFTER the manifest succeeds.
                        $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $trackingNumber,
                            'label_url' => $labelUrl,
                            'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                            'network' => 'Flying Tigers',
                            'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                            'new_balance' => $chargeResult['new_balance'],
                        ];

                        \Log::info('Prepaid bulk manifest: shipment '.$shipperId.' manifested via Flying Tigers.');

                        ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', $bulkPreviousStatus, 'Prepaid shipment manifested via Flying Tigers (bulk). Tracking: '.($trackingNumber ?? 'N/A'), $manifestCustomerId, 'admin');

                    } elseif ($apiProvider === 'shipglobal' || $network === 'ship global' || $network === 'shipglobal') {
                        // Call Ship Global API
                        $shipGlobalResult = $this->callShipGlobalApiFromDb($shipper);

                        if (! $shipGlobalResult['success']) {
                            $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $bulkPreviousStatus, $adminId);
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'Ship Global API error: '.($shipGlobalResult['message'] ?? 'Unknown'),
                            ];

                            continue;
                        }

                        $apiResponse = $shipGlobalResult['data'] ?? [];
                        $trackingNumber = null;
                        // Ship Global returns tracking/reference number in various possible formats
                        // Priority: waybill_number > tracking_number > awb_number > order_number
                        if (isset($apiResponse['data']) && isset($apiResponse['data']['waybill_number']) && ! empty($apiResponse['data']['waybill_number'])) {
                            $trackingNumber = $apiResponse['data']['waybill_number'];
                        } elseif (isset($apiResponse['waybill_number']) && ! empty($apiResponse['waybill_number'])) {
                            $trackingNumber = $apiResponse['waybill_number'];
                        } elseif (isset($apiResponse['tracking_number'])) {
                            $trackingNumber = $apiResponse['tracking_number'];
                        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['tracking_number'])) {
                            $trackingNumber = $apiResponse['data']['tracking_number'];
                        } elseif (isset($apiResponse['awb_number'])) {
                            $trackingNumber = $apiResponse['awb_number'];
                        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['awb_number'])) {
                            $trackingNumber = $apiResponse['data']['awb_number'];
                        } elseif (isset($apiResponse['waybill'])) {
                            $trackingNumber = $apiResponse['waybill'];
                        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['waybill'])) {
                            $trackingNumber = $apiResponse['data']['waybill'];
                        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['order_number'])) {
                            // If no waybill/tracking yet, use order_number as reference (label: "manual" case)
                            $trackingNumber = $apiResponse['data']['order_number'];
                        } elseif (isset($apiResponse['order_number'])) {
                            $trackingNumber = $apiResponse['order_number'];
                        }

                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                        ShipmentTracking::updateOrCreate(
                            ['shipper_id' => $shipperId],
                            [
                                'customer_id' => $manifestCustomerId,
                                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                                'response_status_code' => '1',
                                'response_status_description' => 'Ship Global order created',
                                'shipment_identification_number' => $trackingNumber,
                                'total_charges_currency' => 'INR',
                                'total_charges_amount' => null,
                                'billing_weight_uom' => 'KGS',
                                'billing_weight' => null,
                                'package_results' => null,
                                'raw_response' => $apiResponse,
                                'status' => 'created',
                            ]
                        );

                        $shipper->status = 'manifested';
                        $shipper->save();

                        // Create a manifest record from the manifests table
                        // (bulk: share the single batch manifest number)
                        $this->createManifestRecord($shipper->id, $manifestCustomerId, $bulkManifestNumber);

                        // Create tracking record for manifested status
                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();
                        Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                            'title' => Tracking::getTitleForStatus('manifested'),
                            'status' => 'manifested',
                        ]);

                        // Payment is cut only AFTER the manifest succeeds.
                        $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $trackingNumber,
                            'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                            'network' => 'Ship Global',
                            'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                            'new_balance' => $chargeResult['new_balance'],
                        ];

                        \Log::info('Prepaid bulk manifest: shipment '.$shipperId.' manifested via Ship Global.');

                        ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', $bulkPreviousStatus, 'Prepaid shipment manifested via Ship Global (bulk). Tracking: '.($trackingNumber ?? 'N/A'), $manifestCustomerId, 'admin');

                    } else {
                        // Default: Call UPS Ship API
                        $payloadResult = $this->buildUpsShipPayloadFromDb($shipper);
                        if (! $payloadResult['success']) {
                            $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $bulkPreviousStatus, $adminId);
                            $results['failed'][] = ['shipper_id' => $shipperId, 'message' => $payloadResult['message']];

                            continue;
                        }
                        $upsPayload = $payloadResult['payload'];

                        $upsResult = $this->callUpsShipApiInternal($upsPayload);

                        if (! $upsResult['success']) {
                            $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId, $bulkPreviousStatus, $adminId);
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'UPS API error: '.($upsResult['message'] ?? 'Unknown'),
                            ];

                            continue;
                        }

                        $shipmentResponse = $upsResult['shipmentResponse'];
                        $trackingNumber = $shipmentResponse['ShipmentResults']['PackageResults']['TrackingNumber']
                            ?? $shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber']
                            ?? null;

                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                        ShipmentTracking::updateOrCreate(
                            ['shipper_id' => $shipperId],
                            [
                                'customer_id' => $manifestCustomerId,
                                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                                'response_status_code' => $shipmentResponse['Response']['ResponseStatus']['Code'] ?? null,
                                'response_status_description' => $shipmentResponse['Response']['ResponseStatus']['Description'] ?? null,
                                'transaction_identifier' => $shipmentResponse['Response']['TransactionReference']['TransactionIdentifier'] ?? null,
                                'customer_context' => $shipmentResponse['Response']['TransactionReference']['CustomerContext'] ?? null,
                                'shipment_identification_number' => $shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber'] ?? null,
                                'transportation_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TransportationCharges']['CurrencyCode'] ?? null,
                                'transportation_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TransportationCharges']['MonetaryValue'] ?? null,
                                'service_options_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['ServiceOptionsCharges']['CurrencyCode'] ?? null,
                                'service_options_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['ServiceOptionsCharges']['MonetaryValue'] ?? null,
                                'total_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TotalCharges']['CurrencyCode'] ?? null,
                                'total_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TotalCharges']['MonetaryValue'] ?? null,
                                'billing_weight_uom' => $shipmentResponse['ShipmentResults']['BillingWeight']['UnitOfMeasurement']['Code'] ?? null,
                                'billing_weight' => $shipmentResponse['ShipmentResults']['BillingWeight']['Weight'] ?? null,
                                'package_results' => $shipmentResponse['ShipmentResults']['PackageResults'] ?? null,
                                'raw_response' => $shipmentResponse,
                                'status' => 'created',
                            ]
                        );

                        $shipper->status = 'manifested';
                        $shipper->save();

                        // Create a manifest record from the manifests table
                        // (bulk: share the single batch manifest number)
                        $this->createManifestRecord($shipper->id, $manifestCustomerId, $bulkManifestNumber);

                        // Create tracking record for manifested status
                        Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                            'title' => Tracking::getTitleForStatus('manifested'),
                            'status' => 'manifested',
                        ]);

                        // Payment is cut only AFTER the manifest succeeds.
                        $chargeResult = $this->chargeShipmentIfNotPaid($shipper, $manifestCustomerId);

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $trackingNumber,
                            'manifest_number' => Manifest::where('shipper_id', $shipperId)->value('manifest_number'),
                            'network' => 'UPS',
                            'amount_charged' => $chargeResult['charged'] ? $chargeResult['amount'] : 0,
                            'new_balance' => $chargeResult['new_balance'],
                        ];

                        \Log::info('Prepaid bulk manifest: shipment '.$shipperId.' manifested via UPS.');

                        ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', $bulkPreviousStatus, 'Prepaid shipment manifested via UPS (bulk). Tracking: '.($trackingNumber ?? 'N/A'), $manifestCustomerId, 'admin');
                    }

                } catch (\Exception $e) {
                    if (isset($shipper, $bulkPreviousStatus)) {
                        $this->revertPrepaidReadyToDraftOnManifestFailure($shipper, $manifestCustomerId ?? 0, $bulkPreviousStatus, $adminId ?? 0);
                    }
                    $results['failed'][] = ['shipper_id' => $shipperId, 'message' => $e->getMessage()];
                    \Log::error('Prepaid bulk manifest error for shipper '.$shipperId.': '.$e->getMessage());
                }
            }

            $bulkNewBalance = null;
            foreach ($results['success'] as $bulkSuccessEntry) {
                if (isset($bulkSuccessEntry['new_balance'])) {
                    $bulkNewBalance = $bulkSuccessEntry['new_balance'];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk manifest completed. '.count($results['success']).' succeeded, '.count($results['failed']).' failed out of '.$results['total'].' shipments.',
                'results' => $results,
                'new_balance' => $bulkNewBalance,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    /**
     * Book the SELECTED service's carrier API at prepaid order creation.
     *
     * Same per-service routing as manifest-time in the customer flow
     * (shipuniversal / primus / overseas / postshipping / flyingtigers /
     * shipglobal / UPS default): the chosen service's own carrier is booked,
     * never a hardcoded one. Only persists carrier tracking data — shipper
     * status, manifest record, wallet and logs are owned by the caller.
     *
     * Returns ['success' => bool, 'tracking_number' => ?string,
     *          'shipment_response' => ?array (UPS only),
     *          'rawResponse' => mixed, 'message' => ?string, ...].
     */
    private function bookPrepaidCarrierAtCreation($shipper, int $adminId): array
    {
        $manifestCustomerId = (int) ($shipper->customer_id ?? 0);
        if ($manifestCustomerId <= 0) {
            return ['success' => false, 'message' => 'This prepaid shipment has no linked customer. Link an exporter customer before creating the order.'];
        }

        $shipperId = $shipper->id;
        $awbNumber = $shipper->awb_number;

        $shippingMethod = $this->resolveShippingMethod($shipper);
        $courierService = $this->findCourierService($shippingMethod, $shipper->id);
        $apiProvider = $this->resolveApiProvider($shippingMethod, $shipper, $courierService);

        \Log::info('prepaidCarrierBooking: Shipper #'.$shipperId.' → shipping_method="'.$shippingMethod.'" → api_provider="'.$apiProvider.'"');

        $storeTracking = function (array $fields) use ($shipperId, $manifestCustomerId) {
            ShipmentTracking::updateOrCreate(
                ['shipper_id' => $shipperId],
                array_merge([
                    'customer_id' => $manifestCustomerId,
                    'create_shipment_id' => CreateShipment::where('shipper_id', $shipperId)->value('id'),
                    'status' => 'created',
                ], $fields)
            );
        };

        if ($apiProvider === 'shipuniversal') {
            $shipUniversalResult = $this->callShipUniversalApiFromDb($shipper);
            if (! $shipUniversalResult['success']) {
                return [
                    'success' => false,
                    'message' => 'ShipUniversal API Failed: '.($shipUniversalResult['message'] ?? 'Unknown error'),
                    'rawResponse' => $shipUniversalResult['data'] ?? null,
                ];
            }

            $apiResponse = $shipUniversalResult['data'] ?? [];
            $trackingNumber = $this->extractShipUniversalTrackingNumber($apiResponse);
            $labelUrl = $this->extractShipUniversalLabelUrl($apiResponse);

            if (empty($trackingNumber)) {
                return ['success' => false, 'message' => 'ShipUniversal created no usable AWB number.', 'rawResponse' => $apiResponse];
            }

            $storeTracking([
                'response_status_code' => '1',
                'response_status_description' => 'ShipUniversal shipment created',
                'shipment_identification_number' => $trackingNumber,
                'total_charges_currency' => 'INR',
                'total_charges_amount' => null,
                'billing_weight_uom' => 'KGS',
                'billing_weight' => null,
                'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                'raw_response' => $apiResponse,
            ]);

            return ['success' => true, 'tracking_number' => $trackingNumber, 'label_url' => $labelUrl];
        }

        if ($apiProvider === 'primus') {
            $primusResult = app(PrimusShipmentService::class)->manifest(
                $shipper,
                (int) $manifestCustomerId,
                false,
                'manifested'
            );

            if (! $primusResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Primus API Failed: '.($primusResult['message'] ?? 'Unknown error'),
                    'rawResponse' => $primusResult,
                ];
            }

            return [
                'success' => true,
                'tracking_number' => $primusResult['tracking_number'] ?? null,
                'label_url' => $primusResult['label'] ?? null,
            ];
        }

        if ($apiProvider === 'overseas' || $this->isOverseasLogisticMethod($shippingMethod)) {
            $overseasResult = $this->callOverseasLogisticApiFromDb($shipper);
            if (! $overseasResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Overseas Logistic API Failed: '.$this->overseasValueToString($overseasResult['message'] ?? 'Unknown error'),
                    'rawResponse' => $overseasResult['data'] ?? null,
                ];
            }

            $apiResponse = $overseasResult['data'] ?? [];
            $trackingNumber = $this->extractOverseasTrackingNumber($apiResponse);
            $labelUrl = $this->extractOverseasLabelUrl($apiResponse);
            $boxLabelUrl = $this->extractOverseasBoxLabelUrl($apiResponse);

            $storeTracking([
                'response_status_code' => '1',
                'response_status_description' => 'Overseas Logistic shipment created',
                'shipment_identification_number' => $trackingNumber,
                'total_charges_currency' => 'INR',
                'total_charges_amount' => null,
                'billing_weight_uom' => 'KGS',
                'billing_weight' => null,
                'package_results' => ($labelUrl || $boxLabelUrl) ? array_filter([
                    'LabelURL' => $labelUrl,
                    'BoxLabelURL' => $boxLabelUrl,
                ]) : null,
                'raw_response' => $apiResponse,
            ]);

            return ['success' => true, 'tracking_number' => $trackingNumber, 'label_url' => $labelUrl];
        }

        if ($apiProvider === 'postshipping' || $this->isPostShippingMethod($shippingMethod)) {
            $postShippingResult = $this->callPostShippingApiFromDb($shipper);
            if (! $postShippingResult['success']) {
                return [
                    'success' => false,
                    'message' => 'PostShipping API Failed: '.($postShippingResult['message'] ?? 'Unknown error'),
                    'rawResponse' => $postShippingResult['data'] ?? null,
                ];
            }

            $apiResponse = $postShippingResult['data'] ?? [];
            $trackingNumber = $this->extractPostShippingTrackingNumber($apiResponse);
            $labelUrl = $this->extractPostShippingLabelUrl($apiResponse);

            $storeTracking([
                'response_status_code' => '1',
                'response_status_description' => 'PostShipping shipment created',
                'shipment_identification_number' => $trackingNumber,
                'total_charges_currency' => 'INR',
                'total_charges_amount' => null,
                'billing_weight_uom' => 'KGS',
                'billing_weight' => null,
                'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                'raw_response' => $apiResponse,
            ]);

            return ['success' => true, 'tracking_number' => $trackingNumber, 'label_url' => $labelUrl];
        }

        if ($apiProvider === 'flyingtigers' || $this->isFlyingTigersMethod($shippingMethod)) {
            $flyingTigersResult = $this->callFlyingTigersApiFromDb($shipper);
            if (! $flyingTigersResult['success']) {
                if (! empty($flyingTigersResult['is_address_error'])) {
                    $fallbackInfo = $this->getFlyingTigersAddressErrorFallbackInfo($shipper, $manifestCustomerId);

                    return array_merge([
                        'success' => false,
                        'message' => 'The address provided appears to be incorrect or incomplete for UNITED ECO POST. You can ship via UNITED CLASSIC (Ship Global) instead.',
                        'is_address_error' => true,
                    ], $fallbackInfo);
                }

                return [
                    'success' => false,
                    'message' => 'Flying Tigers API Failed: '.($flyingTigersResult['message'] ?? 'Unknown error'),
                    'rawResponse' => $flyingTigersResult['data'] ?? null,
                ];
            }

            $apiResponse = $flyingTigersResult['data'] ?? [];
            $trackingNumber = $this->extractFlyingTigersTrackingNumber($apiResponse);
            $labelUrl = $this->extractFlyingTigersLabelUrl($apiResponse);

            $storeTracking([
                'response_status_code' => '1',
                'response_status_description' => 'Flying Tigers shipment created',
                'shipment_identification_number' => $trackingNumber,
                'total_charges_currency' => 'INR',
                'total_charges_amount' => null,
                'billing_weight_uom' => 'KGS',
                'billing_weight' => null,
                'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                'raw_response' => $apiResponse,
            ]);

            return ['success' => true, 'tracking_number' => $trackingNumber, 'label_url' => $labelUrl];
        }

        if ($apiProvider === 'shipglobal' || ($courierService && in_array(strtolower(trim($courierService->network ?? '')), ['ship global', 'shipglobal'], true))) {
            $shipGlobalResult = $this->callShipGlobalApiFromDb($shipper);
            if (! $shipGlobalResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Ship Global API Failed: '.($shipGlobalResult['message'] ?? 'Unknown error'),
                    'rawResponse' => $shipGlobalResult['data'] ?? null,
                ];
            }

            $apiResponse = $shipGlobalResult['data'] ?? [];
            $trackingNumber = null;
            if (isset($apiResponse['data']) && isset($apiResponse['data']['waybill_number']) && ! empty($apiResponse['data']['waybill_number'])) {
                $trackingNumber = $apiResponse['data']['waybill_number'];
            } elseif (isset($apiResponse['waybill_number']) && ! empty($apiResponse['waybill_number'])) {
                $trackingNumber = $apiResponse['waybill_number'];
            } elseif (isset($apiResponse['tracking_number'])) {
                $trackingNumber = $apiResponse['tracking_number'];
            } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['tracking_number'])) {
                $trackingNumber = $apiResponse['data']['tracking_number'];
            } elseif (isset($apiResponse['awb_number'])) {
                $trackingNumber = $apiResponse['awb_number'];
            } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['awb_number'])) {
                $trackingNumber = $apiResponse['data']['awb_number'];
            } elseif (isset($apiResponse['waybill'])) {
                $trackingNumber = $apiResponse['waybill'];
            } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['waybill'])) {
                $trackingNumber = $apiResponse['data']['waybill'];
            } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['order_number'])) {
                $trackingNumber = $apiResponse['data']['order_number'];
            } elseif (isset($apiResponse['order_number'])) {
                $trackingNumber = $apiResponse['order_number'];
            }

            $storeTracking([
                'response_status_code' => '1',
                'response_status_description' => 'Ship Global order created',
                'shipment_identification_number' => $trackingNumber,
                'total_charges_currency' => 'INR',
                'total_charges_amount' => null,
                'billing_weight_uom' => 'KGS',
                'billing_weight' => null,
                'package_results' => null,
                'raw_response' => $apiResponse,
            ]);

            return ['success' => true, 'tracking_number' => $trackingNumber];
        }

        // Default: UPS Ship API (also covers legacy/unresolved services).
        $upsPayloadResult = $this->buildUpsShipPayloadFromDb($shipper);
        if (! $upsPayloadResult['success']) {
            return ['success' => false, 'message' => $upsPayloadResult['message'] ?? 'Unable to build the UPS shipment payload.'];
        }

        $upsResult = $this->callUpsShipApiInternal($upsPayloadResult['payload']);
        if (! $upsResult['success']) {
            Log::error('Prepaid order rejected by UPS Ship API.', [
                'awb_number' => $awbNumber,
                'message' => $upsResult['message'] ?? 'Unknown UPS error',
                'raw_response' => $upsResult['rawResponse'] ?? null,
            ]);

            return [
                'success' => false,
                'message' => 'UPS Shipment Failed: '.($upsResult['message'] ?? 'Unknown UPS error'),
                'rawResponse' => $upsResult['rawResponse'] ?? null,
            ];
        }

        $shipmentResponse = $upsResult['shipmentResponse'];
        $trackingNumber = $shipmentResponse['ShipmentResults']['PackageResults']['TrackingNumber']
            ?? $shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber']
            ?? null;

        $storeTracking([
            'response_status_code' => $shipmentResponse['Response']['ResponseStatus']['Code'] ?? null,
            'response_status_description' => $shipmentResponse['Response']['ResponseStatus']['Description'] ?? null,
            'transaction_identifier' => $shipmentResponse['Response']['TransactionReference']['TransactionIdentifier'] ?? null,
            'customer_context' => $shipmentResponse['Response']['TransactionReference']['CustomerContext'] ?? null,
            'shipment_identification_number' => $shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber'] ?? null,
            'transportation_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TransportationCharges']['CurrencyCode'] ?? null,
            'transportation_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TransportationCharges']['MonetaryValue'] ?? null,
            'service_options_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['ServiceOptionsCharges']['CurrencyCode'] ?? null,
            'service_options_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['ServiceOptionsCharges']['MonetaryValue'] ?? null,
            'total_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TotalCharges']['CurrencyCode'] ?? null,
            'total_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TotalCharges']['MonetaryValue'] ?? null,
            'billing_weight_uom' => $shipmentResponse['ShipmentResults']['BillingWeight']['UnitOfMeasurement']['Code'] ?? null,
            'billing_weight' => $shipmentResponse['ShipmentResults']['BillingWeight']['Weight'] ?? null,
            'package_results' => $shipmentResponse['ShipmentResults']['PackageResults'] ?? null,
            'raw_response' => $shipmentResponse,
        ]);

        Log::info('Prepaid order accepted by UPS Ship API.', [
            'admin_id' => $adminId,
            'awb_number' => $awbNumber,
            'carrier_tracking_number' => $trackingNumber,
        ]);

        return ['success' => true, 'tracking_number' => $trackingNumber, 'shipment_response' => $shipmentResponse];
    }
}
