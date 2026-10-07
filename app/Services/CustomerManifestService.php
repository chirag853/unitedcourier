<?php

namespace App\Services;

use App\Http\Controllers\CustomerController;
use App\Models\CourierRate;
use App\Models\CourierService;
use App\Models\CreateShipment;
use App\Models\ConsigneeInfo;
use App\Models\CsbInformation;
use App\Models\Customer;
use App\Models\CustomerApi;
use App\Models\Wallet;
use App\Models\Destination;
use App\Models\PackageDimension;
use App\Models\ShipmentInvoice;
use App\Models\ShipmentInvoiceItem;
use App\Models\ShipperInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Unified customer manifest service.
 *
 * One payload in, provider routing inside:
 * api_provider + method -> courier_services -> existing carrier branch
 * (self / shipuniversal / primus / overseas / postshipping /
 *  flyingtigers / shipglobal / ups).
 */
class CustomerManifestService
{
    public const PROVIDERS = [
        'self',
        'shipuniversal',
        'primus',
        'overseas',
        'postshipping',
        'flyingtigers',
        'shipglobal',
        'ups',
    ];

    /**
     * Resolve a CourierService from api_provider + method.
     * Tier 1: exact provider+method. Tier 2: provider + case-insensitive method.
     * Tier 3: method-only fallback (keeps old web behaviour).
     */
    public function resolveService(string $apiProvider, string $method): ?CourierService
    {
        $provider = strtolower(trim($apiProvider));
        $method = trim($method);

        $service = CourierService::whereRaw('LOWER(api_provider) = ?', [$provider])
            ->where('method', $method)
            ->where('status', 1)
            ->first();

        if ($service) {
            return $service;
        }

        $service = CourierService::whereRaw('LOWER(api_provider) = ?', [$provider])
            ->whereRaw('LOWER(method) = ?', [strtolower($method)])
            ->where('status', 1)
            ->first();

        if ($service) {
            return $service;
        }

        // Method-only fallback (any provider) so legacy method names keep working.
        $service = CourierService::where('method', $method)->where('status', 1)->first();
        if ($service) {
            return $service;
        }

        return CourierService::whereRaw('LOWER(method) = ?', [strtolower($method)])
            ->where('status', 1)
            ->first();
    }

    /**
     * Resolve a CourierService from api_code (e.g. 1500).
     * One api_code maps to one service_code group; multiple rows can share
     * it (different country/method). delivery_destination + method narrow
     * it down; customer_api access is honoured when the customer is
     * restricted. Returns the best match or null.
     */
    public function resolveServiceByApiCode(int $apiCode, ?string $method = null, mixed $destination = null, ?Customer $customer = null): ?CourierService
    {
        $query = CourierService::where('api_code', $apiCode)->where('status', 1);

        if ($customer && $this->isAccessRestricted($customer)) {
            $allowed = $this->allowedServiceIds($customer);
            if (empty($allowed)) {
                return null;
            }
            $query->whereIn('id', $allowed);
        }

        $candidates = $query->orderBy('api_provider')->orderBy('method')->get();
        if ($candidates->isEmpty()) {
            return null;
        }

        return $this->pickBestServiceMatch($candidates, $method, $destination);
    }

    /**
     * Resolve a CourierService from service_code string (e.g. "EC01").
     * Same narrowing rules as resolveServiceByApiCode.
     */
    public function resolveServiceByServiceCode(string $serviceCode, ?string $method = null, mixed $destination = null, ?Customer $customer = null): ?CourierService
    {
        $code = trim($serviceCode);
        if ($code === '') {
            return null;
        }

        // Numeric string (e.g. "1500") is an api_code, not a service_code.
        if (ctype_digit($code)) {
            return $this->resolveServiceByApiCode((int) $code, $method, $destination, $customer);
        }

        $query = CourierService::whereRaw('LOWER(service_code) = ?', [strtolower($code)])
            ->where('status', 1);

        if ($customer && $this->isAccessRestricted($customer)) {
            $allowed = $this->allowedServiceIds($customer);
            if (empty($allowed)) {
                return null;
            }
            $query->whereIn('id', $allowed);
        }

        $candidates = $query->orderBy('api_provider')->orderBy('method')->get();
        if ($candidates->isEmpty()) {
            return null;
        }

        return $this->pickBestServiceMatch($candidates, $method, $destination);
    }

    /**
     * Pick the best row from candidates: exact method match wins,
     * destination country match breaks ties.
     */
    private function pickBestServiceMatch($candidates, ?string $method = null, mixed $destination = null): ?CourierService
    {
        $method = $method !== null ? strtolower(trim($method)) : null;
        $hints = $this->destinationCountryHints($destination);

        $best = null;
        $bestScore = -1;
        foreach ($candidates as $svc) {
            $score = 0;
            if ($method !== null && $method !== '' && strtolower(trim((string) $svc->method)) === $method) {
                $score += 2;
            }
            if (! empty($hints)) {
                $svcCountry = strtoupper(trim((string) ($svc->country ?? '')));
                if ($svcCountry !== '' && in_array($svcCountry, $hints, true)) {
                    $score += 1;
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $svc;
            }
        }

        return $best ?? $candidates->first();
    }

    /**
     * Destination input -> list of equivalent country codes.
     * Handles numeric destination ids via destinations table plus
     * common name variants (USA/US, UK/GB, Canada/CA, Australia/AU/AUS).
     */
    private function destinationCountryHints(mixed $destination): array
    {
        if ($destination === null) {
            return [];
        }

        $value = trim((string) $destination);
        if ($value === '') {
            return [];
        }

        if (ctype_digit($value)) {
            $record = Destination::find((int) $value);
            if ($record) {
                $raw = $record->country_code ?: $record->code ?: $record->name;
                $value = trim((string) $raw);
                if ($value === '') {
                    return [];
                }
            }
        }

        $upper = strtoupper($value);

        if ($upper === 'USA' || $upper === 'US' || str_contains($upper, 'UNITED STATES') || str_starts_with($upper, 'US -')) {
            return ['US', 'USA'];
        }
        if ($upper === 'UK' || $upper === 'GB' || str_contains($upper, 'UNITED KINGDOM') || str_contains($upper, 'GREAT BRITAIN') || str_starts_with($upper, 'UK -')) {
            return ['UK', 'GB'];
        }
        if ($upper === 'CA' || $upper === 'CANADA' || str_contains($upper, 'CANADA')) {
            return ['CA', 'CANADA'];
        }
        if ($upper === 'AU' || $upper === 'AUS' || str_contains($upper, 'AUSTRALIA')) {
            return ['AU', 'AUS', 'AUSTRALIA'];
        }

        return [$upper];
    }

    public function validateProviderCombination(string $apiProvider, ?CourierService $service, ?Customer $customer = null): void
    {
        $provider = strtolower(trim($apiProvider));

        if ($provider === '') {
            throw ValidationException::withMessages([
                'api_provider' => ['api_provider is required.'],
            ]);
        }

        if (! $service) {
            throw ValidationException::withMessages([
                'method' => ['No enabled courier service found for this method.'],
            ]);
        }

        // SELF is internal-only: any enabled method can be manifested
        // internally; downstream manifestShipment routes on api_provider=self.
        if ($provider === 'self') {
            return;
        }

        // Accept any provider that exists in courier_services (covers
        // gofo/cirro/aj-worldwide/dhl-plans extras) plus the core 8.
        $exists = CourierService::whereRaw('LOWER(api_provider) = ?', [$provider])->exists();
        if (! $exists && ! in_array($provider, self::PROVIDERS, true)) {
            throw ValidationException::withMessages([
                'api_provider' => ['Unsupported api_provider. Allowed: '.implode(', ', self::PROVIDERS).' (or any provider present in courier_services).'],
            ]);
        }

        // Strict check: resolved service must belong to the requested provider,
        // except ups which is the legacy default fallback.
        if ($provider !== 'ups' && strtolower(trim((string) $service->api_provider)) !== $provider) {
            // Allow method-only fallback only when the caller explicitly asked
            // for the service's own provider via service_code lookup below.
            throw ValidationException::withMessages([
                'method' => ["Method '{$service->method}' belongs to provider '{$service->api_provider}', not '{$provider}'."],
            ]);
        }

        // Per-customer API access (customer_api table): when the admin has
        // created ANY mapping rows for this customer, only rows with
        // status=1 may be used. No rows = unrestricted (backward compatible).
        if ($customer && $this->isAccessRestricted($customer)) {
            $allowed = CustomerApi::where('customer_id', $customer->id)
                ->where('service_id', $service->id)
                ->where('status', 1)
                ->exists();
            if (! $allowed) {
                throw ValidationException::withMessages([
                    'method' => ['This service is not enabled for your API access.'],
                ]);
            }
        }
    }

    /**
     * True when the admin has created at least one customer_api mapping
     * row for this customer (allowed or blocked). No rows = open access.
     */
    public function isAccessRestricted(Customer $customer): bool
    {
        return CustomerApi::where('customer_id', $customer->id)->exists();
    }

    /**
     * IDs of services explicitly allowed (status=1) for this customer.
     */
    public function allowedServiceIds(Customer $customer): array
    {
        return CustomerApi::where('customer_id', $customer->id)
            ->where('status', 1)
            ->pluck('service_id')
            ->all();
    }

    /**
     * Create a draft shipment (status=ready) from the unified API payload.
     * Shape mirrors CustomerController::storeShipment essentials so all
     * downstream build*PayloadFromDb() helpers keep working.
     */
    public function createDraftShipment(Customer $customer, array $data, CourierService $service, ?string $requestedProvider = null): ShipperInfo
    {
        $destName = $this->resolveDestinationName($data['delivery_destination'] ?? null);

        $packages = $data['packages'] ?? [];
        if (empty($packages)) {
            throw ValidationException::withMessages(['packages' => ['At least one package is required.']]);
        }

        $items = $data['items'] ?? [];
        if (empty($items)) {
            throw ValidationException::withMessages(['items' => ['At least one invoice item is required.']]);
        }

        // SELF must route to the internal manifest branch even when the
        // resolved method belongs to another provider's service row.
        $isSelf = strtolower(trim((string) ($requestedProvider ?? $data['api_provider'] ?? ''))) === 'self';
        $shippingMethod = $isSelf ? 'SELF' : $service->method;

        return DB::transaction(function () use ($customer, $data, $service, $destName, $packages, $items, $shippingMethod) {
            $controller = app(CustomerController::class);
            $awbNumber = $controller->generateAwbNumber();

            $shipper = ShipperInfo::create([
                'customer_id' => $customer->id,
                'awb_number' => $awbNumber,
                'shipping_method' => $shippingMethod,
                'shipper_same_as_customer' => false,
                'company_name' => $data['shipper']['company_name'],
                'contact_person' => $data['shipper']['contact_person'],
                'address_line1' => $data['shipper']['address_line1'],
                'address_line2' => $data['shipper']['address_line2'] ?? null,
                'address_line3' => $data['shipper']['address_line3'] ?? null,
                'pincode' => $data['shipper']['pincode'],
                'city' => $data['shipper']['city'],
                'state' => strtoupper((string) $data['shipper']['state']),
                'phone_number' => $data['shipper']['phone_number'],
                'email' => $data['shipper']['email'],
                'kyc_type' => $data['shipper']['kyc_type'] ?? null,
                'kyc_number' => $data['shipper']['kyc_number'] ?? null,
                'service_id' => $service->id,
                'service_rate_id' => $data['service_rate_id'] ?? null,
                'status' => 'ready',
                'shipment_type' => 1,
                'base_price' => 0,
                'fuel_price' => 0,
                'gst_percentage' => 0,
                'gst_amount' => 0,
                'total_price' => 0,
            ]);

            ConsigneeInfo::create([
                'shipper_id' => $shipper->id,
                'delivery_destination' => $destName,
                'origin_type' => $data['origin_type'] ?? 'CSB IV',
                'consignee_name' => $data['consignee']['name'],
                'contact_person' => $data['consignee']['contact_person'] ?? $data['consignee']['name'],
                'address_line1' => $data['consignee']['address_line1'],
                'address_line2' => $data['consignee']['address_line2'] ?? null,
                'address_line3' => $data['consignee']['address_line3'] ?? null,
                'zip_code' => $data['consignee']['zip_code'],
                'city' => $data['consignee']['city'],
                'state' => $data['consignee']['state'] ?? null,
                'phone_number' => $data['consignee']['phone_number'],
                'email' => $data['consignee']['email'],
            ]);

            $packageIds = [];
            foreach ($packages as $pkg) {
                $package = PackageDimension::create([
                    'shipper_id' => $shipper->id,
                    'shipping_method' => $shippingMethod,
                    'actual_weight_kg' => $pkg['actual_weight_kg'] ?? null,
                    'length_cm' => $pkg['length_cm'] ?? null,
                    'width_cm' => $pkg['width_cm'] ?? null,
                    'height_cm' => $pkg['height_cm'] ?? null,
                    'volumetric_weight' => $pkg['volumetric_weight'] ?? null,
                    'chargeable_weight' => $pkg['chargeable_weight'] ?? null,
                ]);
                $packageIds[] = $package->id;
            }

            // CSB details sirf CSB V par store hote hain; CSB IV par
            // koi csb_information row nahi banti (bheja hua csb_v ignore).
            if (($data['origin_type'] ?? 'CSB IV') === 'CSB V') {
                CsbInformation::create([
                    'shipper_id' => $shipper->id,
                    'ecommerce' => $data['csb']['ecommerce'] ?? 'No',
                    'scheme' => $data['csb']['scheme'] ?? 'No',
                    'bond_ut_igst' => $data['csb']['bond_ut_igst'] ?? null,
                    'lut_number' => $data['csb']['lut_number'] ?? null,
                    'iec_code' => $data['csb']['iec_code'] ?? null,
                    'inv_terms' => $data['csb']['inv_terms'] ?? null,
                    'gst_number' => $data['csb']['gst_number'] ?? null,
                    'ad_code' => $data['csb']['ad_code'] ?? null,
                    'bank_account_number' => $data['csb']['bank_account_number'] ?? null,
                    'bank_ifsc_code' => $data['csb']['bank_ifsc_code'] ?? null,
                ]);
            }

            $invoiceData = $data['invoice'];
            $invoice = ShipmentInvoice::create([
                'shipper_id' => $shipper->id,
                'invoice_number' => $invoiceData['invoice_number'],
                'invoice_date' => $invoiceData['invoice_date'],
                'invoice_amount' => $invoiceData['invoice_amount'],
                'incoterms' => $invoiceData['incoterms'] ?? 'CIF',
                'invoice_currency' => $invoiceData['invoice_currency'] ?? 'INR',
                'reference_number' => $invoiceData['reference_number'] ?? null,
            ]);

            foreach ($items as $item) {
                $boxNo = (int) ($item['box_no'] ?? 1);
                ShipmentInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'package_dimension_id' => $packageIds[$boxNo - 1] ?? $packageIds[0] ?? null,
                    'box_no' => $boxNo,
                    'description' => $item['description'] ?? 'General Merchandise',
                    'hs_code' => $item['hs_code'] ?? null,
                    'hts_code' => $item['hts_code'] ?? null,
                    'unit_type' => $item['unit_type'] ?? 'PCS',
                    'qty' => $item['qty'] ?? 1,
                    'unit_rate' => $item['unit_rate'] ?? 0,
                    'igst_percentage' => $item['igst_percentage'] ?? 0,
                    'igst_amount' => $item['igst_amount'] ?? 0,
                    'amount' => $item['amount'] ?? (($item['qty'] ?? 1) * ($item['unit_rate'] ?? 0)),
                ]);
            }

            CreateShipment::create([
                'customer_id' => $customer->id,
                'shipper_id' => $shipper->id,
                'awb_number' => $awbNumber,
                'delivery_destination' => $destName,
                'origin_type' => $data['origin_type'] ?? 'CSB IV',
                'shipping_method' => $shippingMethod,
                'shipper_company_name' => $data['shipper']['company_name'],
                'shipper_contact_person' => $data['shipper']['contact_person'],
                'shipper_address_line1' => $data['shipper']['address_line1'],
                'shipper_pincode' => $data['shipper']['pincode'],
                'shipper_city' => $data['shipper']['city'],
                'shipper_state' => strtoupper((string) $data['shipper']['state']),
                'shipper_phone_number' => $data['shipper']['phone_number'],
                'shipper_email' => $data['shipper']['email'],
                'shipper_kyc_type' => $data['shipper']['kyc_type'] ?? null,
                'shipper_kyc_number' => $data['shipper']['kyc_number'] ?? null,
                'consignee_name' => $data['consignee']['name'],
                'consignee_contact_person' => $data['consignee']['contact_person'] ?? $data['consignee']['name'],
                'consignee_address_line1' => $data['consignee']['address_line1'],
                'consignee_zip_code' => $data['consignee']['zip_code'],
                'consignee_city' => $data['consignee']['city'],
                'consignee_state' => $data['consignee']['state'] ?? null,
                'consignee_phone_number' => $data['consignee']['phone_number'],
                'consignee_email' => $data['consignee']['email'],
                'invoice_number' => $invoiceData['invoice_number'],
                'invoice_date' => $invoiceData['invoice_date'],
                'invoice_amount' => $invoiceData['invoice_amount'],
                'incoterms' => $invoiceData['incoterms'] ?? 'CIF',
                'invoice_currency' => $invoiceData['invoice_currency'] ?? 'INR',
                'reference_number' => $invoiceData['reference_number'] ?? null,
            ]);

            // Best-effort pricing from the customer's own rate card so the
            // wallet gate deducts the real amount instead of zero.
            $this->applyRateCardPricing($customer, $shipper, $service);

            return $shipper->fresh();
        });
    }

    /**
     * Manifest a ready/packed shipper by reusing the exact web flow
     * (CustomerController::manifestShipment) so all 8 provider branches
     * behave identically for web + API.
     *
     * Before manifesting, the Ready-style custom label PDF is generated
     * (same content as the view-all-shipments Print Label) so custom_label
     * is populated exactly like the web Ready -> Packed step.
     */
    public function manifestShipper(ShipperInfo $shipper, Customer $customer): array
    {
        auth()->guard('customer')->setUser($customer);

        $shipper = $shipper->fresh();

        // Fail-fast wallet gate FIRST — for ready AND packed. Paise kam hue
        // to na label PDF banega, na koi carrier API hit hogi.
        // (Web flow packed ka gate skip karta hai; API flow me wahi leak thi.)
        $gate = $this->checkWalletForManifest($shipper, (int) $customer->id);
        if (! $gate['ok']) {
            return [
                'http_status' => 422,
                'payload' => [
                    'success' => false,
                    'message' => $gate['message'],
                    'shipper_id' => $shipper->id,
                    'awb_number' => $shipper->awb_number,
                    'status' => $shipper->status,
                ],
            ];
        }

        // Same label the customer gets on Ready Print Label (web mark-packed).
        // Only when missing; never overwrites an existing custom label.
        // Status ready -> packed here is valid input for manifestShipment.
        app(CustomerLabelService::class)->ensureReadyLabel($shipper->fresh());

        $request = Request::create('/customer/manifest-shipment', 'POST', [
            'shipper_id' => $shipper->id,
        ]);
        $request->setUserResolver(fn () => $customer);

        /** @var CustomerController $controller */
        $controller = app(CustomerController::class);
        $response = $controller->manifestShipment($request);
        $payload = $response->getData(true) ?? [];

        // Surface the Ready-style label URL on the API response.
        $customLabel = ShipperInfo::where('id', $shipper->id)->value('custom_label');
        if ($customLabel) {
            $payload['custom_label_url'] = $customLabel;
        }

        return [
            'http_status' => $response->getStatusCode(),
            'payload' => $payload,
        ];
    }

    /**
     * Wallet pre-check for API manifests (ready AND packed).
     * Mirrors CustomerController::getShipmentChargeInfo amount math so the
     * API gate matches the web gate — but without the packed bypass.
     *
     * @return array{ok: bool, message: string|null, amount: float, balance: float}
     */
    public function checkWalletForManifest(ShipperInfo $shipper, int $customerId): array
    {
        $shipper->loadMissing(['serviceRate', 'invoices']);

        $amount = $shipper->total_price !== null && (float) $shipper->total_price > 0
            ? (float) $shipper->total_price
            : ($shipper->serviceRate
                ? (float) $shipper->serviceRate->inclusive_total
                : round((float) ($shipper->invoices()->first()->total_amount ?? 0), 2));
        $amount = round((float) $amount, 2);

        $wallet = Wallet::where('customer_id', $customerId)->first();
        $balance = $wallet ? (float) $wallet->balance : 0;

        if ($amount <= 0) {
            return ['ok' => true, 'message' => null, 'amount' => $amount, 'balance' => $balance];
        }

        if (! $wallet || $balance < $amount) {
            return [
                'ok' => false,
                'message' => 'Insufficient wallet balance to manifest this shipment. Current balance is ₹' . number_format($balance, 2) . ', required ₹' . number_format($amount, 2) . '.',
                'amount' => $amount,
                'balance' => $balance,
            ];
        }

        return ['ok' => true, 'message' => null, 'amount' => $amount, 'balance' => $balance];
    }

    private function resolveDestinationName(mixed $input): string
    {
        if (is_numeric($input)) {
            $name = Destination::where('id', (int) $input)->value('name');
            if (! $name) {
                throw ValidationException::withMessages([
                    'delivery_destination' => ['The selected destination is invalid.'],
                ]);
            }

            return $name;
        }

        $name = trim((string) $input);
        if ($name === '') {
            throw ValidationException::withMessages([
                'delivery_destination' => ['Delivery destination is required.'],
            ]);
        }

        return $name;
    }

    private function applyRateCardPricing(Customer $customer, ShipperInfo $shipper, CourierService $service): void
    {
        try {
            $weight = (float) PackageDimension::where('shipper_id', $shipper->id)->sum('chargeable_weight');
            if ($weight <= 0) {
                $weight = (float) PackageDimension::where('shipper_id', $shipper->id)->sum('actual_weight_kg');
            }
            if ($weight <= 0) {
                return;
            }

            $rate = CourierRate::where('customer_id', $customer->id)
                ->where('service_id', $service->id)
                ->where('wt_range_start', '<=', $weight)
                ->where('wt_range_end', '>=', $weight)
                ->first()
                ?: CourierRate::where('customer_id', 0)
                    ->where('service_id', $service->id)
                    ->where('wt_range_start', '<=', $weight)
                    ->where('wt_range_end', '>=', $weight)
                    ->first()
                ?: CourierRate::where('service_id', $service->id)->first();

            if (! $rate) {
                return;
            }

            $shipper->forceFill([
                'service_rate_id' => $rate->id,
                'base_price' => $rate->price,
                'fuel_price' => $rate->fuel_charge,
                'gst_percentage' => $rate->gst_percentage,
                'gst_amount' => $rate->gst_amount,
                'total_price' => $rate->inclusive_total,
                'total_base_price' => $rate->price,
            ])->save();
        } catch (\Throwable $e) {
            \Log::warning('API rate-card pricing skipped: '.$e->getMessage(), ['shipper_id' => $shipper->id]);
        }
    }
}
