<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
use App\Models\Zone;
use App\Services\AdomantraApiClient;

class CodController extends Controller
{
    /**
     * Show the "Create COD Order" form.
     *
     * Mirrors the customer create-shipment page exactly (same fields and
     * validation), but with no logged-in customer: default rates only,
     * no saved exporters and no CSB pre-fill.
     */
    public function codCreateOrder()
    {
        $customer = null;
        $csbForm = null;
        // Only enabled services (status = 1) are offered.
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
        // the COD order shipper. Each entry shows whether the customer is CSB 4
        // (csb_status = 1) or CSB 5 (csb_status = 2).
        $codCustomers = Customer::query()
            ->where('status', 1)
            ->whereHas('kycDetail', function ($query) {
                $query->where('kyc_status', 'approved');
            })
            ->with(['kycDetail', 'csbForm'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.cod-create-order', compact(
            'customer',
            'csbForm',
            'courierServices',
            'zones',
            'destinations',
            'canCreateShipment',
            'codCustomers'
        ));
    }

    /**
     * Store a new COD order (shipment_type = 2).
     *
     * Mirrors the customer create-shipment flow (storeShipment) exactly:
     * same validation, same per-box re-pricing, same tracking/logging and
     * the same Adomantra submission. Differences for the admin flow:
     *   - no authenticated customer (default rates only, customer_id = 0)
     *   - shipper customer_id is null / shipment_type is forced to 2
     *   - COD AWB numbers are generated (generateCodAwbNumber)
     *   - the shipment invoice keeps status "draft" + delivery_type "DDU"
     *     so codAllOrders() lists it correctly
     *   - tracking/shipment-log entries are performed by "admin"
     *   - the JSON success payload includes a top-level "tracking_number"
     *     (the generated AWB) which the admin page displays after submit
     */
    public function codStoreOrder(Request $request, AdomantraApiClient $adomantra)
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
                'entry_remark' => 'nullable|string|max:1000',
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
            if (empty($validatedData['shipping_method']) && $serviceId) {
                $courierService = CourierService::find($serviceId);
                if ($courierService) {
                    $validatedData['shipping_method'] = $courierService->method;
                    \Log::info('codStoreOrder: Resolved shipping_method from service_id #'.$serviceId.' → "'.$courierService->method.'"');
                }
            }

            // Ensure we have a service_id (courier_services.id) to persist on the
            // shipper_info row. Prefer the value sent by the frontend; otherwise
            // resolve it from the (possibly just-resolved) shipping_method.
            if (! $serviceId && ! empty($validatedData['shipping_method'])) {
                $resolvedService = CourierService::whereRaw('LOWER(method) = ?', [strtolower($validatedData['shipping_method'])])->first();
                if ($resolvedService) {
                    $serviceId = $resolvedService->id;
                }
            }

            // Resolve the selected rate from server-owned records. The admin flow
            // uses the shared default rates only (customer_id = 0).
            $courierRate = null;
            if (! empty($validatedData['service_rate_id'])) {
                $courierRate = CourierRate::whereKey((int) $validatedData['service_rate_id'])
                    ->whereIn('customer_id', [0])
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
            $awbNumber = $this->generateCodAwbNumber();

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
                $boxRate = $findBoxRate(0, $chargeableWeight)
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
                'status' => 'manifested',
                'shipment_type' => 2,
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
            $invoiceNumber = $this->generateCodInvoiceNumber();
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
            \Log::info('COD order items data received:', $validatedData['items'] ?? []);
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
                \Log::info('No COD order items data received');
            }

            // Store into create_shipment table.
            // create_shipment.customer_id is NOT NULL (no default) — the admin COD
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
            // A COD order is auto-manifested on creation, so the first tracking
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
                'COD order created (manifested)',
                $validatedData['selected_exporter_customer_id'] ?? null,
                'admin'
            );

            // Create the manifest record so the COD order shows up under the
            // Manifested tab with a manifest number, exactly like the customer
            // manifest flow does for regular shipments.
            Manifest::createForShipper(
                $shipper->id,
                (int) ($validatedData['selected_exporter_customer_id'] ?? 0)
            );

            // ============================================================
            // Carrier API call (UPS Ship API) at creation time.
            // The COD flow only offers UPS courier services, so the selected
            // service's carrier API is called right here — while the DB
            // transaction is still open. The shipment is only committed if
            // the carrier accepts it; otherwise everything rolls back and the
            // UPS error (with raw response) is surfaced to the admin.
            // ============================================================
            $carrierTrackingNumber = null;
            $upsPayloadResult = $this->buildUpsShipPayloadFromDb($shipper);

            if (! $upsPayloadResult['success']) {
                Log::error('COD order: failed to build UPS payload.', [
                    'awb_number' => $awbNumber,
                    'message' => $upsPayloadResult['message'] ?? 'Unknown error',
                ]);
                throw new \RuntimeException($upsPayloadResult['message'] ?? 'Unable to build the UPS shipment payload.');
            }

            $upsResult = $this->callUpsShipApiInternal($upsPayloadResult['payload']);

            if (! $upsResult['success']) {
                Log::error('COD order rejected by UPS Ship API.', [
                    'awb_number' => $awbNumber,
                    'message' => $upsResult['message'] ?? 'Unknown UPS error',
                    'raw_response' => $upsResult['rawResponse'] ?? null,
                ]);

                $carrierErrorRaw = $upsResult['rawResponse'] ?? null;

                throw new \RuntimeException(
                    'UPS Shipment Failed: '.($upsResult['message'] ?? 'Unknown UPS error')
                );
            }

            // UPS accepted the shipment — persist the carrier tracking data
            // so the COD order carries the real UPS tracking number.
            $shipmentResponse = $upsResult['shipmentResponse'];
            $carrierTrackingNumber = $shipmentResponse['ShipmentResults']['PackageResults']['TrackingNumber']
                ?? $shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber']
                ?? null;

            ShipmentTracking::updateOrCreate(
                ['shipper_id' => $shipper->id],
                [
                    'customer_id' => $validatedData['selected_exporter_customer_id'] ?? null,
                    'create_shipment_id' => $createShipment->id,
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

            Log::info('COD order accepted by UPS Ship API.', [
                'admin_id' => $admin->id,
                'awb_number' => $awbNumber,
                'carrier_tracking_number' => $carrierTrackingNumber,
            ]);

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
            Log::info('Adomantra COD order payload generated.', [
                'admin_id' => $admin->id,
                'awb_number' => $awbNumber,
                'payload' => $adomantraPayload,
            ]);

            $adomantraResponse = $adomantra->createOrder($adomantraPayload);

            Log::info('Adomantra COD order created.', [
                'admin_id' => $admin->id,
                'awb_number' => $awbNumber,
            ]);

            DB::commit();
            $transactionStarted = false;

            if (! $request->expectsJson()) {
                return back()->with('success', 'COD order created successfully!');
            }

            return response()->json([
                'success' => true,
                'message' => 'COD order created successfully!',
                'tracking_number' => $carrierTrackingNumber ?? $awbNumber,
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

            Log::error('COD order database failure.', [
                'awb_number' => $awbNumber ?? null,
                'exception' => $e->getMessage(),
            ]);

            $message = 'Unable to save the COD order because of a database error. No shipment was saved. Please try again.';

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

            Log::error('COD order carrier submission failed.', [
                'awb_number' => $awbNumber ?? null,
                'exception' => $e->getMessage(),
                'raw_response' => $carrierErrorRaw,
            ]);

            $message = 'The COD order could not be submitted to the carrier. No shipment was saved. '.$e->getMessage();

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

            Log::error('COD order creation failed.', [
                'awb_number' => $awbNumber ?? null,
                'exception' => $e->getMessage(),
            ]);

            $message = 'Failed to create COD order. Please try again.';

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
     * List all COD orders (shipment_type = 2).
     */
    public function codAllOrders(Request $request)
    {
        $status = $request->query('status');

        $query = DB::table('shipment_invoice')
            ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
            ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
            ->leftJoin('customers', 'shipper_info.customer_id', '=', 'customers.id')
            ->leftJoin('manifests', 'manifests.shipper_id', '=', 'shipper_info.id')
            ->where('shipper_info.shipment_type', 2)
            ->select(
                'shipment_invoice.id',
                'shipment_invoice.invoice_number',
                'shipment_invoice.invoice_date',
                'shipment_invoice.invoice_amount',
                'shipment_invoice.status as invoice_status',
                'shipment_invoice.delivery_type',
                'shipment_invoice.created_at as order_created_at',
                'shipper_info.id as shipper_id',
                'shipper_info.awb_number',
                'shipper_info.company_name',
                'shipper_info.contact_person as shipper_contact',
                'shipper_info.phone_number as shipper_phone',
                'shipper_info.city as shipper_city',
                'shipper_info.status as shipper_status',
                'manifests.manifest_number',
                'customers.first_name',
                'customers.last_name',
                'consignee_info.consignee_name',
                'consignee_info.city as consignee_city'
            )
            ->orderBy('shipment_invoice.created_at', 'desc')
            ->get();

        if ($status && $status !== 'all') {
            $query = $query->filter(function ($row) use ($status) {
                return $row->shipper_status === $status || $row->invoice_status === $status;
            })->values();
        }

        $counts = [
            'all'        => $query->count(),
            'draft'      => $query->where('shipper_status', 'draft')->count(),
            'manifested' => $query->where('shipper_status', 'manifested')->count(),
            'cod'        => $query->where('invoice_status', 'cod')->count(),
        ];

        return view('admin.cod-all-orders', [
            'orders' => $query,
            'counts' => $counts,
            'status' => $status,
        ]);
    }

    /**
     * Generate a unique AWB number for COD orders (UWCCOD + yymmdd + serial).
     */
    private function generateCodAwbNumber()
    {
        $prefix = 'UWCCOD';
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
     * Generate a unique invoice number for COD orders.
     */
    private function generateCodInvoiceNumber()
    {
        return 'INV-COD-'.now()->format('ymdHis').'-'.random_int(1000, 9999);
    }

    // ============================================================
    // COD ORDER HELPERS (mirrors CustomerController so the admin COD
    // create-order page behaves identically to the customer
    // create-shipment page)
    // ============================================================

    /**
     * Build the Adomantra order payload for a COD order.
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
     * Proxy UPS Rate API call for the admin COD create-order page.
     *
     * Mirrors CustomerController::getUpsRate() exactly (same box-wise
     * calculation, zone resolution and response shape) with these admin
     * differences:
     *   - uses the logged-in admin for customer_name
     *   - customer_exists is always false (default rates only, customer_id = 0)
     *   - the response key is `zone` (not `selected_zone`) because the admin
     *     blade reads data.zone
     */
    public function codUpsRate(Request $request)
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
        $isMultiPackage = is_array($packageWeights) ? count($packageWeights) : 1;

        // 3. Weight validation
        if ($totalWeight <= 0) {
            return response()->json([
                'success' => true,
                'customer_exists' => false,
                'customer_name' => $adminName,
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
            if ($destinationCountry === 'UAE' && isset($uaeEmirateCodes[strtoupper($stateValue)])) {
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
        // 5. Get services - ONLY UPS services (network = 'UPS' or api_provider = 'ups')
        // are offered on the admin COD create-order page.
        $services = CourierService::where('country', $destinationCountry)
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereRaw('LOWER(api_provider) = ?', ['ups'])
                    ->orWhereRaw('LOWER(network) = ?', ['ups']);
            })
            ->get();

        if (empty($services)) {
            return response()->json([
                'success' => false,
                'message' => 'Service not available.',
            ], 404);
        }

        // 6. Process rates - SINGLE LOOP (default rates only, customer_id = 0)
        $allRates = [];

        foreach ($services as $key => $service) {

            // ========== SPECIAL CASE: US Multi-package with United Ground Premium ==========
            if ($destinationCountry === 'US' && strtolower($service->method) == 'united ground premium') {
                $boxBreakdown = [];
                $combinedBase = 0;
                $combinedFuel = 0;
                $combinedGst = 0;
                $combinedSurcharge = 0;
                $surchargeList = [];
                $firstMatchedRate = null;
                $allBoxesMatched = true;

                foreach ($packageWeights as $index => $pkgWt) {
                    $pkgWt = floatval($pkgWt);

                    $boxRate = \DB::select(
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
                        [$service->id, $destinationCountry, $zoneNumber, $pkgWt]
                    );

                    if (! empty($boxRate)) {
                        $boxRate = $boxRate[0];
                        if ($firstMatchedRate === null) {
                            $firstMatchedRate = $boxRate;
                        }

                        $base = floatval($boxRate->price);
                        $fuel = floatval($boxRate->fuel_charge) > 0
                            ? floatval($boxRate->fuel_charge)
                            : ($base * floatval($boxRate->fuel_percentage) / 100);

                        $boxSurchargeIds = $this->normalizeSurchargeIds($boxRate->surcharge_id ?? null);
                        $boxSurcharge = (float) SurCharge::whereIn('id', $boxSurchargeIds)->sum('price');
                        if (! empty($boxSurchargeIds)) {
                            foreach (SurCharge::whereIn('id', $boxSurchargeIds)->get() as $s) {
                                $surchargeList[$s->id] = [
                                    'name' => $s->name,
                                    'code' => $s->code,
                                    'price' => (float) $s->price,
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
                    'box_breakdown' => $boxBreakdown,
                ];
            }

            if ($destinationCountry === 'US' && $isMultiPackage <= 1 && strtolower($service->method) != 'united ground premium') {
                $boxBreakdown = [];
                $combinedBase = 0;
                $combinedFuel = 0;
                $combinedGst = 0;
                $combinedSurcharge = 0;
                $surchargeList = [];
                $firstMatchedRate = null;
                $allBoxesMatched = true;

                foreach ($packageWeights as $index => $pkgWt) {
                    $pkgWt = floatval($pkgWt) ?: 1;

                    $boxRate = \DB::select(
                        'SELECT cr.*, cs.country, cs.service_code, cs.method
                FROM courier_rates cr
                INNER JOIN courier_services cs ON cr.service_id = cs.id
                WHERE cr.customer_id = 0
                AND cr.service_id = ?
                AND cs.country = ?
                AND (cr.zone_no = ? OR cr.zone_no IS NULL OR cr.zone_no = 0)
                AND ? BETWEEN cr.wt_range_start AND cr.wt_range_end
                ORDER BY cr.zone_no DESC, cr.wt_range_start
                LIMIT 1',
                        [$service->id, $destinationCountry, $zoneNumber, $pkgWt]
                    );

                    if (! empty($boxRate)) {
                        $boxRate = $boxRate[0];
                        if ($firstMatchedRate === null) {
                            $firstMatchedRate = $boxRate;
                        }

                        $base = floatval($boxRate->price);
                        $fuel = floatval($boxRate->fuel_charge) > 0
                            ? floatval($boxRate->fuel_charge)
                            : ($base * floatval($boxRate->fuel_percentage) / 100);

                        $boxSurchargeIds = $this->normalizeSurchargeIds($boxRate->surcharge_id ?? null);
                        $boxSurcharge = (float) SurCharge::whereIn('id', $boxSurchargeIds)->sum('price');
                        if (! empty($boxSurchargeIds)) {
                            foreach (SurCharge::whereIn('id', $boxSurchargeIds)->get() as $s) {
                                $surchargeList[$s->id] = [
                                    'name' => $s->name,
                                    'code' => $s->code,
                                    'price' => (float) $s->price,
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
                    'box_breakdown' => $boxBreakdown,
                ];
            }

            if ($destinationCountry === 'UK') {
                $boxBreakdown = [];
                $combinedBase = 0;
                $combinedFuel = 0;
                $combinedGst = 0;
                $combinedSurcharge = 0;
                $surchargeList = [];
                $firstMatchedRate = null;
                $allBoxesMatched = true;

                foreach ($packageWeights as $index => $pkgWt) {
                    $pkgWt = floatval($pkgWt);

                    $boxRate = \DB::select(
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
                        [$service->id, $destinationCountry, $zoneNumber, $pkgWt]
                    );

                    if (! empty($boxRate)) {
                        $boxRate = $boxRate[0];
                        if ($firstMatchedRate === null) {
                            $firstMatchedRate = $boxRate;
                        }

                        $base = floatval($boxRate->price);
                        $fuel = floatval($boxRate->fuel_charge) > 0
                            ? floatval($boxRate->fuel_charge)
                            : ($base * floatval($boxRate->fuel_percentage) / 100);

                        $boxSurchargeIds = $this->normalizeSurchargeIds($boxRate->surcharge_id ?? null);
                        $boxSurcharge = (float) SurCharge::whereIn('id', $boxSurchargeIds)->sum('price');
                        if (! empty($boxSurchargeIds)) {
                            foreach (SurCharge::whereIn('id', $boxSurchargeIds)->get() as $s) {
                                $surchargeList[$s->id] = [
                                    'name' => $s->name,
                                    'code' => $s->code,
                                    'price' => (float) $s->price,
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
                    'box_breakdown' => $boxBreakdown,
                ];
            }

            // Australia uses the same box-wise rate calculation as Canada
            // (both are zipcode-category destinations with zone_no-based
            // rates). The query below is fully parameterized by
            // $destinationCountry, so adding 'AUS' here makes the
            // ARAMEX GPX ALL IN service rates resolve correctly.
            if ($destinationCountry === 'CA' || $destinationCountry === 'AUS' || $destinationCountry === 'NZ' || $destinationCountry === 'UAE' || $destinationCountry === 'SG' || $destinationCountry === 'MY' || $destinationCountry === 'DE' || $destinationCountry === 'BD' || $destinationCountry === 'ZW') {
                $boxBreakdown = [];
                $combinedBase = 0;
                $combinedFuel = 0;
                $combinedGst = 0;
                $combinedSurcharge = 0;
                $surchargeList = [];
                $firstMatchedRate = null;
                $allBoxesMatched = true;

                foreach ($packageWeights as $index => $pkgWt) {
                    $pkgWt = floatval($pkgWt);

                    $boxRate = \DB::select(
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
                        [$service->id, $destinationCountry, $zoneNumber, $pkgWt]
                    );

                    if (! empty($boxRate)) {
                        $boxRate = $boxRate[0];
                        if ($firstMatchedRate === null) {
                            $firstMatchedRate = $boxRate;
                        }

                        $base = floatval($boxRate->price);
                        $fuel = floatval($boxRate->fuel_charge) > 0
                            ? floatval($boxRate->fuel_charge)
                            : ($base * floatval($boxRate->fuel_percentage) / 100);

                        $boxSurchargeIds = $this->normalizeSurchargeIds($boxRate->surcharge_id ?? null);
                        $boxSurcharge = (float) SurCharge::whereIn('id', $boxSurchargeIds)->sum('price');
                        if (! empty($boxSurchargeIds)) {
                            foreach (SurCharge::whereIn('id', $boxSurchargeIds)->get() as $s) {
                                $surchargeList[$s->id] = [
                                    'name' => $s->name,
                                    'code' => $s->code,
                                    'price' => (float) $s->price,
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
                    'box_breakdown' => $boxBreakdown,
                ];
            }

        }// end foreach

        // Filter out rate cards whose total price (base + fuel + gst) is 0.
        $allRates = array_values(array_filter($allRates, function ($r) {
            $base = floatval($r['price'] ?? 0);
            $fuel = floatval($r['fuel_charge'] ?? 0);
            $gst = floatval($r['gst_amount'] ?? 0);

            return ($base + $fuel + $gst) > 0;
        }));

        // Attach consistent selected-zone metadata to every card.
        $allRates = array_map(function ($rate) use ($zoneNumber, $zoneName, $zoneCode) {
            $rate['zone_no'] = $rate['zone_no'] ?? $zoneNumber;
            $rate['zone_name'] = $zoneName;
            $rate['zone_code'] = $zoneCode;

            return $rate;
        }, $allRates);

        // Attach surcharge breakdown to every rate card.
        $allRates = array_map(function ($rate) {
            $surcharges = $rate['surcharges'] ?? collect();
            $surchargeTotal = (float) ($rate['surcharge_total'] ?? 0);
            $cr = ! empty($rate['rate_id']) ? CourierRate::find((int) $rate['rate_id']) : null;
            if ($cr && $surchargeTotal <= 0) {
                $surchargeTotal = $cr->surcharge_amount;
                $surcharges = $cr->surchargeModels()->map(function ($s) {
                    return [
                        'name' => $s->name,
                        'code' => $s->code,
                        'price' => (float) $s->price,
                    ];
                })->values();
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

            'customer_exists' => false,
            'customer_name' => $adminName,
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
     * Return the zones for a destination for the admin COD create-order page.
     *
     * Verbatim copy of CustomerController::getZonesByDestination() minus the
     * customer guard (the admin route is already protected by the admin
     * middleware).
     */
    public function codZonesByDestination(Request $request)
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
     * Call the UPS Ship API directly (internal method).
     * Returns ['success' => bool, 'shipmentResponse' => array] or
     * ['success' => false, 'message' => string, 'rawResponse' => mixed].
     */
    private function callUpsShipApiInternal($payload)
    {
        try {
            Log::info('UPS Ship payload (internal): '.substr(json_encode($payload), 0, 2000));

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
}
