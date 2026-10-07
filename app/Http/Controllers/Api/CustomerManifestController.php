<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourierService;
use App\Models\CsbInformation;
use App\Models\Customer;
use App\Models\CustomerApi;
use App\Models\CustomerApiToken;
use App\Models\Manifest;
use App\Models\ShipperInfo;
use App\Models\Tracking;
use App\Services\CustomerManifestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomerManifestController extends Controller
{
    /**
     * Issue a Bearer token for a customer (email + password).
     * POST /api/v1/auth/token
     */
    public function token(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'name' => 'nullable|string|max:50',
        ]);

        $customer = Customer::where('email', $validated['email'])->first();
        if (! $customer || ! Hash::check($validated['password'], $customer->getAuthPassword())) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials.'], 401);
        }

        if (isset($customer->status) && ! $customer->status) {
            return response()->json(['success' => false, 'message' => 'Account deactivated.'], 403);
        }

        $plain = bin2hex(random_bytes(32));
        $name = $validated['name'] ?? 'api';

        // Same customer + name par wahi row update hogi, nayi row nahi banegi.
        // Isse repeat hit par tokens accumulate nahi hote; purana token
        // re-hit ke baad invalid ho jata hai.
        $record = CustomerApiToken::where('customer_id', $customer->id)
            ->where('name', $name)
            ->latest('id')
            ->first();

        if ($record) {
            $record->forceFill([
                'token' => hash('sha256', $plain),
                'last_used_at' => null,
            ])->save();
        } else {
            $record = CustomerApiToken::create([
                'customer_id' => $customer->id,
                'name' => $name,
                'token' => hash('sha256', $plain),
            ]);
        }

        return response()->json([
            'success' => true,
            'token' => $plain,
            'token_type' => 'Bearer',
            'customer_id' => $customer->id,
        ]);
    }

    /**
     * List enabled services grouped for API consumers.
     * GET /api/v1/services
     *
     * Adds per-service `api_allowed` for the token customer based on the
     * customer_api mapping table. No mapping rows = unrestricted.
     */
    public function services(Request $request, CustomerManifestService $manifestService)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('api_customer');

        $services = CourierService::where('status', 1)
            ->orderBy('api_provider')
            ->orderBy('method')
            ->get(['id', 'method', 'network', 'service_code', 'api_code', 'scode', 'api_provider']);

        $restricted = $manifestService->isAccessRestricted($customer);
        $allowedIds = $restricted ? $manifestService->allowedServiceIds($customer) : null;

        $services = $services->map(function ($s) use ($restricted, $allowedIds) {
            $arr = $s->toArray();
            $arr['api_allowed'] = $restricted ? in_array($s->id, $allowedIds ?? [], false) : true;

            return $arr;
        });

        return response()->json([
            'success' => true,
            'providers' => CustomerManifestService::PROVIDERS,
            'restricted' => $restricted,
            'services' => $services->values(),
        ]);
    }

    /**
     * Services for a customer_code — ONLY rows present in customer_api
     * with status=1 (and service enabled).
     * GET /api/v1/services/{customerCode}
     */
    public function servicesByCode(Request $request, string $customerCode)
    {
        $customer = Customer::where('customer_code', $customerCode)->first();
        if (! $customer) {
            // Fallback: numeric id bhi chalega.
            $customer = is_numeric($customerCode)
                ? Customer::find((int) $customerCode)
                : null;
        }
        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found.'], 404);
        }

        // Ek token sirf apne customer ke liye: URL ka customerCode
        // token wale customer se match hona chahiye.
        /** @var Customer|null $apiCustomer */
        $apiCustomer = $request->attributes->get('api_customer');
        if ($apiCustomer && (int) $apiCustomer->id !== (int) $customer->id) {
            return response()->json(['success' => false, 'message' => 'This token belongs to another customer.'], 403);
        }

        // $services = CourierService::join('customer_api', 'customer_api.service_id', '=', 'courier_services.id')
        //     ->where('customer_api.customer_id', $customer->id)
        //     ->where('customer_api.status', 1)
        //     ->where('courier_services.status', 1)
        //     ->orderBy('courier_services.api_provider')
        //     ->orderBy('courier_services.method')
        //     ->get([
        //         'courier_services.id',
        //         'courier_services.method',
        //         'courier_services.network',
        //         'courier_services.service_code',
        //         'courier_services.api_code',
        //         'courier_services.scode',
        //         'courier_services.api_provider',
        //         'courier_services.country',
        //     ]);

        $services = CourierService::join('customer_api', 'customer_api.service_id', '=', 'courier_services.id')
            ->where('customer_api.customer_id', $customer->id)
            ->where('customer_api.status', 1)
            ->where('courier_services.status', 1)
            ->orderBy('courier_services.api_provider')
            ->orderBy('courier_services.method')
            ->get([
                // 'courier_services.id',
                'courier_services.method',
                // 'courier_services.network',
                // 'courier_services.service_code',
                'courier_services.api_code as service_code',
                // 'courier_services.scode',
                // 'courier_services.api_provider',
                // 'courier_services.country',
            ]);


        return response()->json([
            'success' => true,
            'customer_code' => $customer->customer_code,
            // 'customer_id' => $customer->id,
            'services' => $services,
        ]);
    }

    /**
     * Customer's own API access list (from customer_api table).
     * GET /api/v1/access
     */
    public function access(Request $request, CustomerManifestService $manifestService)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('api_customer');

        $rows = CustomerApi::with('service:id,method,network,service_code,api_code,api_provider')
            ->where('customer_id', $customer->id)
            ->orderBy('service_id')
            ->get(['id', 'customer_id', 'service_id', 'status']);

        return response()->json([
            'success' => true,
            'restricted' => $manifestService->isAccessRestricted($customer),
            'access' => $rows,
        ]);
    }

    /**
     * Create a shipment draft (NO carrier API is hit here).
     * POST /api/v1/manifests
     *
     * Public keys: service_code (e.g. 1500 ya "EC01"),
     * sender (= shipper), reciever (= consignee), csb_v.
     * api_provider + method still works as fallback.
     * Only the resolved service is stored on the draft; the carrier API is
     * hit later via from-draft manifest.
     */
    public function store(Request $request, CustomerManifestService $manifestService)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('api_customer');

        if (! $customer->can_create_shipment) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have the right to create shipments.',
            ], 403);
        }

        // Public keys: service_code (number jaise 1500 bhi chalega),
        // sender (= shipper), reciever (= consignee). Purani keys fallback.
        if (! $request->filled('service_code') && $request->filled('api_code')) {
            $request->merge(['service_code' => (string) $request->input('api_code')]);
        }
        $rawServiceCode = $request->input('service_code');
        if (is_int($rawServiceCode) || is_float($rawServiceCode)) {
            $request->merge(['service_code' => (string) $rawServiceCode]);
        }
        if (! $request->filled('shipper') && $request->filled('sender')) {
            $request->merge(['shipper' => $request->input('sender')]);
        }
        if (! $request->filled('consignee') && $request->filled('reciever')) {
            $request->merge(['consignee' => $request->input('reciever')]);
        }

        try {
            $validated = $request->validate([
                'api_provider' => 'nullable|string|max:50',
                'service_code' => 'nullable|string|max:100',
                'method' => 'nullable|string|max:100',
                'service_rate_id' => 'nullable|integer',
                'origin_type' => 'nullable|string|in:CSB IV,CSB V',
                'delivery_destination' => 'required',

                'shipper.company_name' => 'required|string|max:150',
                'shipper.contact_person' => 'required|string|max:100',
                'shipper.address_line1' => 'required|string|max:255',
                'shipper.address_line2' => 'nullable|string|max:255',
                'shipper.address_line3' => 'nullable|string|max:255',
                'shipper.pincode' => 'required|string|max:20',
                'shipper.city' => 'required|string|max:100',
                'shipper.state' => 'required|string|max:100',
                'shipper.phone_number' => 'required|string|max:30',
                'shipper.email' => 'required|email|max:150',
                'shipper.kyc_type' => ['nullable', 'string', Rule::in(['Aadhar Card', 'PAN Card', 'GST (Normal)']), 'required_with:shipper.kyc_number'],
                'shipper.kyc_number' => ['nullable', 'string', 'max:100', 'required_with:shipper.kyc_type'],

                'consignee.name' => 'required|string|max:150',
                'consignee.contact_person' => 'nullable|string|max:100',
                'consignee.address_line1' => 'required|string|max:255',
                'consignee.address_line2' => 'nullable|string|max:255',
                'consignee.address_line3' => 'nullable|string|max:255',
                'consignee.zip_code' => 'required|string|max:20',
                'consignee.city' => 'required|string|max:100',
                'consignee.state' => 'nullable|string|max:100',
                'consignee.phone_number' => 'required|string|max:30',
                'consignee.email' => 'required|email|max:150',

                'packages' => 'required|array|min:1|max:50',
                'packages.*.actual_weight_kg' => 'required|numeric|min:0.01|max:30',
                'packages.*.length_cm' => 'required|numeric|min:1|max:120',
                'packages.*.width_cm' => 'required|numeric|min:1|max:76',
                'packages.*.height_cm' => 'required|numeric|min:1|max:120',
                'packages.*.volumetric_weight' => 'nullable|numeric|min:0',
                'packages.*.chargeable_weight' => 'nullable|numeric|min:0',

                'invoice.invoice_number' => 'required|string|max:100',
                'invoice.invoice_date' => 'required|date|before_or_equal:today',
                'invoice.invoice_amount' => 'required|numeric|min:0',
                'invoice.incoterms' => 'nullable|string|max:50',
                'invoice.invoice_currency' => 'nullable|string|max:20',
                'invoice.reference_number' => 'nullable|string|max:100',

                'items' => 'required|array|min:1',
                'items.*.box_no' => 'nullable|integer|min:1',
                'items.*.description' => 'nullable|string|max:500',
                'items.*.hs_code' => 'nullable|string|max:50',
                'items.*.hts_code' => 'nullable|string|max:50',
                'items.*.unit_type' => 'nullable|string|max:50',
                'items.*.qty' => 'nullable|numeric|min:0',
                'items.*.unit_rate' => 'nullable|numeric|min:0',
                'items.*.igst_percentage' => 'nullable|numeric|min:0|max:100',
                'items.*.igst_amount' => 'nullable|numeric|min:0',
                'items.*.amount' => 'nullable|numeric|min:0',

                'csb.ecommerce' => 'nullable|in:Yes,No',
                'csb.scheme' => 'nullable|in:Yes,No',
                'csb.bond_ut_igst' => 'nullable|in:Bond UT,IGST',
                'csb.lut_number' => 'nullable|string|max:100',
                'csb.iec_code' => 'nullable|string|max:50',
                'csb.gst_number' => 'nullable|string|max:50',
                'csb.ad_code' => 'nullable|string|max:100',
                'csb.bank_account_number' => 'nullable|string|max:50',
                'csb.bank_ifsc_code' => 'nullable|string|max:20',

                'csb_v' => 'nullable|array|required_if:origin_type,CSB V',
                'csb_v.ecommerce' => 'required_if:origin_type,CSB V|in:Yes,No',
                'csb_v.scheme' => 'required_if:origin_type,CSB V|in:Yes,No',
                'csb_v.bond_ut_igst' => 'nullable|in:Bond UT,IGST',
                'csb_v.lut_number' => 'nullable|string|max:100',
                'csb_v.iec_code' => 'required_if:origin_type,CSB V|string|max:50',
                'csb_v.gst_number' => 'required_if:origin_type,CSB V|string|max:50',
                'csb_v.ad_code' => 'required_if:origin_type,CSB V|string|max:100',
                'csb_v.bank_account_number' => 'required_if:origin_type,CSB V|string|max:50',
                'csb_v.bank_ifsc_code' => 'required_if:origin_type,CSB V|string|max:20',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        // csb_v is the public key; csb still works as fallback.
        // Internally everything runs on csb so CsbInformation flow is untouched.
        if (isset($validated['csb_v']) && is_array($validated['csb_v'])) {
            $validated['csb'] = $validated['csb_v'];
        }

        // Service identifier: service_code preferred (number jaise 1500 bhi
        // api_code ki tarah resolve hoga); api_provider + method as fallback.
        $hasServiceCode = isset($validated['service_code']) && trim((string) $validated['service_code']) !== '';
        $hasProvider = isset($validated['api_provider']) && trim((string) $validated['api_provider']) !== '';
        $requestMethod = isset($validated['method']) ? trim((string) $validated['method']) : '';
        $requestMethod = $requestMethod !== '' ? $requestMethod : null;

        $service = null;
        if ($hasServiceCode) {
            $service = $manifestService->resolveServiceByServiceCode(
                (string) $validated['service_code'],
                $requestMethod,
                $validated['delivery_destination'] ?? null,
                $customer
            );
            if (! $service) {
                return response()->json([
                    'success' => false,
                    'message' => 'No enabled service found for this service_code.',
                    'errors' => ['service_code' => ['No enabled service found for this service_code.']],
                ], 422);
            }
        } elseif ($hasProvider) {
            if ($requestMethod === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'The method field is required when api_provider is used.',
                    'errors' => ['method' => ['The method field is required when api_provider is used.']],
                ], 422);
            }
            $service = $manifestService->resolveService((string) $validated['api_provider'], $requestMethod);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Provide service_code, or api_provider with method.',
                'errors' => ['service_code' => ['Provide service_code, or api_provider with method.']],
            ], 422);
        }

        // Canonical provider + method from the resolved row so the
        // downstream carrier routing always matches the DB service.
        $resolvedProvider = strtolower(trim((string) $service->api_provider));
        $validated['api_provider'] = $resolvedProvider;
        $validated['method'] = $service->method;

        // KYC number format check — same patterns as web/COD flow.
        $kycType = $validated['shipper']['kyc_type'] ?? null;
        $kycNumber = $validated['shipper']['kyc_number'] ?? null;
        if ($kycType && $kycNumber !== null && $kycNumber !== '') {
            $normalized = match ($kycType) {
                'Aadhar Card' => preg_replace('/\s+/', '', (string) $kycNumber),
                'PAN Card' => strtoupper((string) preg_replace('/[^A-Z0-9]+/i', '', (string) $kycNumber)),
                'GST (Normal)' => strtoupper((string) preg_replace('/\s+/', '', (string) $kycNumber)),
                default => trim((string) $kycNumber),
            };
            $kycPatterns = [
                'GST (Normal)' => '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                'Aadhar Card' => '/^[2-9]{1}[0-9]{11}$/',
                'PAN Card' => '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            ];
            $kycHints = [
                'GST (Normal)' => 'GSTIN must be 15 characters (e.g. 27ABCDE1234F1Z5).',
                'Aadhar Card' => 'Aadhaar number must be 12 digits and the first digit must be between 2 and 9.',
                'PAN Card' => 'PAN must be 10 characters: 5 letters, 4 digits, 1 letter (e.g. ABCDE1234F).',
            ];
            if (isset($kycPatterns[$kycType]) && ! preg_match($kycPatterns[$kycType], (string) $normalized)) {
                $kycMessage = 'The KYC Number entered is not valid for the selected KYC Type ('.$kycType.'). '.($kycHints[$kycType] ?? '');

                return response()->json([
                    'success' => false,
                    'message' => $kycMessage,
                    'errors' => ['shipper.kyc_number' => [$kycMessage]],
                ], 422);
            }
            $validated['shipper']['kyc_number'] = $normalized;
        }

        try {
            $manifestService->validateProviderCombination($validated['api_provider'], $service, $customer);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Provider/method mismatch.',
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            $shipper = $manifestService->createDraftShipment($customer, $validated, $service, $validated['api_provider']);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('API draft shipment failed: '.$e->getMessage(), ['customer_id' => $customer->id]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipment draft: '.$e->getMessage(),
            ], 500);
        }

        // Draft-only: NO carrier API call, NO wallet deduction here.
        // Manifest (carrier booking) happens via POST /api/v1/manifests/from-draft.
        $shipper = $shipper->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Shipment draft created.',
            'awb_number' => $shipper->awb_number,
        ], 201);
    }

    /**
     * Manifest an existing draft — THIS is where the carrier API is hit
     * (api_provider + method of the draft decide which carrier).
     * POST /api/v1/manifests/from-draft
     */
    public function manifestDraft(Request $request, CustomerManifestService $manifestService)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('api_customer');

        $validated = $request->validate([
            'awb_number' => 'required|string|max:50',
        ]);

        $awbNumber = trim((string) $validated['awb_number']);

        $shipper = ShipperInfo::where('awb_number', $awbNumber)
            ->where('customer_id', $customer->id)
            ->first();
        if (! $shipper) {
            $shipper = ShipperInfo::whereRaw('UPPER(awb_number) = ?', [strtoupper($awbNumber)])
                ->where('customer_id', $customer->id)
                ->first();
        }

        if (! $shipper) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        // Enforce customer_api mapping on draft manifests too.
        if ($manifestService->isAccessRestricted($customer) && ! empty($shipper->service_id)) {
            $allowed = CustomerApi::where('customer_id', $customer->id)
                ->where('service_id', $shipper->service_id)
                ->where('status', 1)
                ->exists();
            if (! $allowed) {
                return response()->json([
                    'success' => false,
                    'message' => 'This service is not enabled for your API access.',
                ], 403);
            }
        }

        $result = $manifestService->manifestShipper($shipper, $customer);

        return response()->json($result['payload'], $result['http_status']);
    }

    /**
     * Fetch manifest + tracking by AWB or manifest number.
     * GET /api/v1/manifests/{reference}
     */
    public function show(Request $request, string $reference)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('api_customer');

        $shipper = ShipperInfo::where('customer_id', $customer->id)
            ->where(function ($q) use ($reference) {
                $q->where('awb_number', $reference)
                    ->orWhere('id', $reference);
            })
            ->first();

        $manifest = Manifest::where('customer_id', $customer->id)
            ->where('manifest_number', $reference)
            ->first();

        if (! $shipper && ! $manifest) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $shipperId = $shipper?->id ?? $manifest?->shipper_id;
        $shipper = ShipperInfo::with(['shipmentTracking', 'manifest', 'consigneeInfo'])->find($shipperId);

        // Full tracking timeline — same source/shape as the website
        // tracking page (WebsiteController::searchTracking).
        $trackingRecords = Tracking::where('awb_number', $shipper->awb_number)
            ->orderBy('created_at', 'asc')
            ->get();
        $statusMap = Tracking::getStatusTitleMap();
        $history = $trackingRecords->map(function ($record) use ($statusMap) {
            return [
                'status' => $record->status,
                'title' => $record->title ?? ($statusMap[$record->status] ?? ucfirst(str_replace('_', ' ', (string) $record->status))),
                'timestamp' => $record->created_at ? $record->created_at->format('d M Y, h:i A') : null,
                'uwc_id' => $record->uwc_id,
            ];
        })->values();

        $consignee = $shipper->consigneeInfo;

        return response()->json([
            'success' => true,
            'awb_number' => $shipper->awb_number,
            'status' => $shipper->status,
            'current_status' => $history->isNotEmpty() ? $history->last()['status'] : $shipper->status,
            'current_title' => $history->isNotEmpty()
                ? $history->last()['title']
                : Tracking::getTitleForStatus((string) $shipper->status),
            'tracking_history' => $history,
            'manifest_number' => $shipper->manifest?->manifest_number,
            'tracking_number' => $shipper->shipmentTracking?->shipment_identification_number,
            'label' => $shipper->shipmentTracking?->package_results['LabelURL'] ?? null,
            'custom_label_url' => $shipper->custom_label,
            'shipment' => [
                'awb_number' => $shipper->awb_number,
                'shipping_method' => $shipper->shipping_method,
                'shipper_name' => $shipper->contact_person,
                'shipper_company' => $shipper->company_name,
                'shipper_city' => $shipper->city,
                'shipper_state' => $shipper->state,
                'shipper_phone' => $shipper->phone_number,
                'shipper_email' => $shipper->email,
            ],
            'consignee' => $consignee ? [
                'consignee_name' => $consignee->contact_person ?? $consignee->consignee_name,
                'consignee_city' => $consignee->city,
                'consignee_state' => $consignee->state,
                'consignee_country' => $consignee->delivery_destination,
                'consignee_phone' => $consignee->phone_number,
            ] : null,
        ]);
    }
}
