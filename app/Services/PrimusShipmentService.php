<?php

namespace App\Services;

use App\Models\CourierService;
use App\Models\CreateShipment;
use App\Models\ShipmentInvoice;
use App\Models\ShipmentInvoiceItem;
use App\Models\ShipmentLog;
use App\Models\ShipmentTracking;
use App\Models\ShipperInfo;
use App\Models\Tracking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class PrimusShipmentService
{
    public function __construct(private readonly PrimusApiClient $client)
    {
    }

    /**
     * Create and persist a Primus shipment without returning credentials or invoice bytes.
     *
     * @return array{success: bool, message: string, data?: array, tracking_number?: string, label?: string|null}
     */
    public function manifest(ShipperInfo $shipper, int $customerId, bool $isBulk = false): array
    {
        try {
            $payload = $this->buildPayload($shipper);
            $maskedPayload = $this->maskedPayload($payload);
            $response = $this->client->createShipment($payload);
            $result = $this->interpretResponse($response);

            if (! $result['success']) {
                return [
                    'success' => false,
                    'message' => $result['message'],
                    'data' => $response,
                    'payload' => $maskedPayload,
                ];
            }

            try {
                $this->persistManifest(
                    $shipper,
                    $customerId,
                    $response,
                    $result['tracking_number'],
                    $result['label'],
                    $result['status_code'],
                    $result['description'],
                    $result['details'],
                    $isBulk
                );
            } catch (Throwable $exception) {
                Log::error('Primus shipment persistence failed.', [
                    'shipper_id' => $shipper->id,
                    'customer_id' => $customerId,
                    'exception_class' => $exception::class,
                ]);

                return [
                    'success' => false,
                    'message' => 'Primus shipment could not be saved. The shipment remains packed.',
                ];
            }

            return [
                'success' => true,
                'message' => 'Primus shipment created successfully.',
                'data' => $response,
                'tracking_number' => $result['tracking_number'],
                'label' => $result['label'],
                'payload' => $maskedPayload,
            ];
        } catch (RuntimeException $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage(),
                'payload' => $maskedPayload ?? null,
            ];
        } catch (Throwable $exception) {
            Log::error('Primus shipment manifestation failed.', [
                'shipper_id' => $shipper->id,
                'customer_id' => $customerId,
                'exception_class' => $exception::class,
            ]);

            return [
                'success' => false,
                'message' => 'Primus shipment could not be saved. The shipment remains packed.',
            ];
        }
    }

    public function buildPayload(ShipperInfo $shipper): array
    {
        $shipper->loadMissing(['consigneeInfo', 'packageDimensions', 'csbInformation']);

        $consignee = $shipper->consigneeInfo;
        if (! $consignee) {
            throw new RuntimeException('No consignee information found for this shipment.');
        }

        $packages = $shipper->packageDimensions;
        if ($packages->isEmpty()) {
            throw new RuntimeException('No package dimensions found for this shipment.');
        }

        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();
        $invoiceItems = $invoice
            ? ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get()
            : collect();
        $csb = $shipper->csbInformation;
        $courierService = $this->resolveCourierService($shipper);

        $accountCode = trim((string) config('services.primus.account_code'));
        $username = trim((string) config('services.primus.username'));
        $password = (string) config('services.primus.password');
        $accessKey = trim((string) config('services.primus.access_key'));
        $customerName = trim((string) config('services.primus.customer_name'));

        if ($accountCode === '' || $username === '' || $password === '' || $accessKey === '') {
            throw new RuntimeException('Primus account validation credentials are not configured.');
        }

        $serviceCode = trim((string) ($courierService?->service_code ?: $courierService?->scode));
        if ($serviceCode === '') {
            throw new RuntimeException('Primus service code is not configured for the selected courier service.');
        }

        [$invoiceFilename, $invoiceBytes] = $this->readCustomLabel($shipper);
        $totalWeight = (float) $packages->sum(fn ($package) => (float) ($package->actual_weight_kg ?? 0));
        $totalQuantity = (float) $invoiceItems->sum(fn ($item) => max(0, (float) ($item->qty ?? 0)));
        $pieceWeight = round($totalWeight / max($totalQuantity, 1), 3);
        $invoiceAmount = (float) ($invoice?->invoice_amount ?? $invoiceItems->sum('amount'));
        $igstAmount = (float) $invoiceItems->sum(fn ($item) => (float) ($item->igst_amount ?? 0));
        $bondUtIgst = trim((string) ($csb?->bond_ut_igst ?? ''));
        $isIgstPaid = str_contains(strtoupper($bondUtIgst), 'IGST');
        $originType = strtoupper(trim((string) ($consignee->origin_type ?? '')));
        $csbType = in_array($originType, ['CSB V', 'CSB 5'], true) ? 'CSB 5' : 'CSB 4';
        $reference = trim((string) ($invoice?->reference_number ?? $shipper->awb_number ?? ''));
        $invoiceNumber = trim((string) ($invoice?->invoice_number ?? $reference));
        $invoiceDate = $invoice?->invoice_date?->format('d/m/Y') ?? now()->format('d/m/Y');
        $network = trim((string) ($courierService?->network ?? ''));
        $serviceName = trim((string) ($courierService?->real_name ?: $courierService?->method ?: $courierService?->description));
        $consignorAddressLine1 = trim((string) ($shipper->address_line1 ?? ''));
        $consignorAddressLine2 = trim((string) ($shipper->address_line2 ?? ''));
        $consignorAddressLine3 = trim((string) ($shipper->address_line3 ?? ''));

        if ($consignorAddressLine2 === '') {
            $consignorAddressLine2 = $consignorAddressLine1;
        }

        $consigneeAddressLine1 = trim((string) ($consignee->address_line1 ?? ''));
        $consigneeAddressLine2 = trim((string) ($consignee->address_line2 ?? ''));
        $consigneeAddressLine3 = trim((string) ($consignee->address_line3 ?? ''));

        if ($consigneeAddressLine2 === '') {
            $consigneeAddressLine2 = $consigneeAddressLine1;
        }

        $itemDetails = $invoiceItems->map(function ($item) use ($pieceWeight): array {
            return [
                'BoxNo' => (string) ($item->box_no ?? 1),
                'Description' => (string) ($item->description ?? 'General Merchandise'),
                'HSNCode' => (string) ($item->hs_code ?? ''),
                'Qty' => $this->numberString($item->qty ?? 1),
                'Rate' => $this->numberString($item->unit_rate ?? 0),
                'Amount' => $this->numberString($item->amount ?? 0),
                'Unit' => (string) ($item->unit_type ?? 'PCS'),
                'ShipPieceIGST' => $this->numberString($item->igst_percentage ?? 0),
                'PieceWt' => $this->numberString($pieceWeight),
            ];
        })->values()->all();

        if ($itemDetails === []) {
            $itemDetails[] = [
                'BoxNo' => '1',
                'Description' => 'General Merchandise',
                'HSNCode' => '',
                'Qty' => '1',
                'Rate' => $this->numberString($invoiceAmount),
                'Amount' => $this->numberString($invoiceAmount),
                'Unit' => 'PCS',
                'ShipPieceIGST' => '0',
                'PieceWt' => $this->numberString($totalWeight),
            ];
        }

        $volWeights = $packages->map(fn ($package): array => [
            'ActWeight' => $this->numberString($package->actual_weight_kg ?? 0),
            'Length' => $this->numberString($package->length_cm ?? 0),
            'Width' => $this->numberString($package->width_cm ?? 0),
            'Height' => $this->numberString($package->height_cm ?? 0),
            'Pcs' => '1',
        ])->values()->all();

        return [
            'ValidateAccount' => [[
                'AccountCode' => $accountCode,
                'Username' => $username,
                'Password' => $password,
                'AccessKey' => $accessKey,
            ]],
            'Shipment' => [[
                'CustomerCode' => $accountCode,
                'CustomerName' => $customerName !== '' ? $customerName : (string) ($shipper->company_name ?? ''),
                'DestinationCode' => $this->destinationCode(
                    (string) ($courierService?->country ?: $consignee->delivery_destination)
                ),
                'ThirdPartyLabel' => true,
                'VATID' => '',
                'ConsignorName' => (string) ($shipper->company_name ?? $shipper->contact_person ?? ''),
                'ConsignorContactPerson' => (string) ($shipper->contact_person ?? $shipper->company_name ?? ''),
                'ConsignorAddressLine1' => $consignorAddressLine1,
                'ConsignorAddressLine2' => $consignorAddressLine2,
                'ConsignorAddressLine3' => $consignorAddressLine3,
                'ConsignorCity' => (string) ($shipper->city ?? ''),
                'ConsignorPostCode' => (string) ($shipper->pincode ?? ''),
                'ConsignorState' => (string) ($shipper->state ?? ''),
                'ConsignorPhoneNo' => (string) ($shipper->phone_number ?? ''),
                'GSTType' => $this->gstType((string) ($shipper->kyc_type ?? '')),
                'GSTIN' => (string) ($csb?->gst_number ?: $shipper->kyc_number ?? ''),
                'ConsigneeName' => (string) ($consignee->consignee_name ?? $consignee->contact_person ?? ''),
                'ConsigneeContactPerson' => (string) ($consignee->contact_person ?? $consignee->consignee_name ?? ''),
                'ConsigneeAddressLine1' => $consigneeAddressLine1,
                'ConsigneeAddressLine2' => $consigneeAddressLine2,
                'ConsigneeAddressLine3' => $consigneeAddressLine3,
                'ConsigneeCity' => (string) ($consignee->city ?? ''),
                'ConsigneeZipCode' => (string) ($consignee->zip_code ?? ''),
                'ConsigneeState' => (string) ($consignee->state ?? ''),
                'ConsigneePhoneNo' => (string) ($consignee->phone_number ?? ''),
                'ServiceTypeCode' => 'S',
                // 'ServiceType' => $serviceName !== '' ? $serviceName : $serviceCode,
                'ServiceType' => $serviceCode,
                'NetworkCode' => $network,
                'GoodsDesc' => 'NDox',
                'NumofItems' => (string) $packages->count(),
                'ActWeight' => number_format($totalWeight, 3, '.', ''),
                'VolWeights' => $volWeights,
                'CustomsValue' => number_format($invoiceAmount, 2, '.', ''),
                'CustomsCurrencyCode' => (string) ($invoice?->invoice_currency ?? 'INR'),
                'ShipmentContent' => (string) ($invoiceItems->first()?->description ?? 'General Merchandise'),
                'ItemDetails' => $itemDetails,
                'CSB_Type' => $csbType,
                'TermsOfSale' => (string) ($invoice?->incoterms ?? 'CIF'),
                'ShipPurpose' => 'SALE',
                'EComm' => $this->yesNo($csb?->ecommerce),
                'ExporterType' => 'NA',
                'ExporterInvDate' => $invoiceDate,
                'ExporterInvNo' => $invoiceNumber,
                'IGSTPayment' => $isIgstPaid ? 'Yes' : 'NA',
                'IGSTAmount' => $this->numberString($isIgstPaid ? $igstAmount : 0),
                'FreightCharge' => '0',
                'InsuranceCharge' => '0',
                'ReferenceNumber' => $reference,
                'PackageType' => 'Your Packaging',
                'MEIS' => $this->yesNo($csb?->scheme),
                'DutyTax' => 'RECIPIENT',
                'DutiesAccountNo' => '',
                // 'ForwarderService' => '',
                // 'InsuredValue' => '',
                'filename' => $invoiceFilename,
                'Base64StringInvoice' => base64_encode($invoiceBytes),
            ]],
        ];
    }

    /**
     * @return array{success: bool, message: string, tracking_number?: string, label?: string|null, status_code?: string, description?: string, details?: array}
     */
    public function interpretResponse(array $response): array
    {
        $root = array_is_list($response) ? ($response[0] ?? []) : $response;
        $statuses = is_array($root) ? ($root['ShipmentResponses'] ?? []) : [];
        $detailsList = is_array($root) ? ($root['shipmentDetails'] ?? []) : [];
        $status = is_array($statuses) ? ($statuses[0] ?? []) : [];
        $details = is_array($detailsList) ? ($detailsList[0] ?? []) : [];

        if (! is_array($status)) {
            $status = [];
        }
        if (! is_array($details)) {
            $details = [];
        }
        $statusText = strtolower(trim((string) ($status['Status'] ?? '')));
        $statusCode = trim((string) ($status['Code'] ?? ''));
        $description = trim((string) ($status['Description'] ?? 'Primus shipment creation failed.'));

        if ($statusText !== 'success' && $statusCode !== '100') {
            return ['success' => false, 'message' => $description];
        }

        $trackingNumber = '';
        foreach (['AwbNo', 'TrackingNo', 'TrackingNo2'] as $key) {
            $candidate = trim((string) ($details[$key] ?? ''));
            if ($candidate !== '') {
                $trackingNumber = $candidate;
                break;
            }
        }

        if ($trackingNumber === '') {
            return [
                'success' => false,
                'message' => 'Primus returned success without a usable AWB or tracking number. The shipment remains packed.',
            ];
        }

        $label = $details['PDF'] ?? null;

        return [
            'success' => true,
            'message' => $description,
            'tracking_number' => $trackingNumber,
            'label' => is_scalar($label) && trim((string) $label) !== '' ? (string) $label : null,
            'status_code' => $statusCode !== '' ? $statusCode : '100',
            'description' => $description,
            'details' => is_array($details) ? $details : [],
        ];
    }

    private function persistManifest(
        ShipperInfo $shipper,
        int $customerId,
        array $response,
        string $trackingNumber,
        ?string $label,
        string $statusCode,
        string $description,
        array $details,
        bool $isBulk
    ): void {
        DB::transaction(function () use ($shipper, $customerId, $response, $trackingNumber, $label, $statusCode, $description, $details, $isBulk): void {
            $lockedShipper = ShipperInfo::whereKey($shipper->id)
                ->where('customer_id', $customerId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedShipper->status !== 'packed') {
                throw new RuntimeException('Shipment is no longer in Packed status.');
            }

            $createShipment = CreateShipment::where('shipper_id', $lockedShipper->id)->first();

            ShipmentTracking::updateOrCreate(
                ['shipper_id' => $lockedShipper->id],
                [
                    'customer_id' => $customerId,
                    'create_shipment_id' => $createShipment?->id,
                    'response_status_code' => $statusCode,
                    'response_status_description' => $description,
                    'shipment_identification_number' => $trackingNumber,
                    'total_charges_currency' => 'INR',
                    'total_charges_amount' => is_numeric($details['Amount'] ?? null) ? $details['Amount'] : null,
                    'billing_weight_uom' => 'KGS',
                    'billing_weight' => is_numeric($details['Weight'] ?? null) ? $details['Weight'] : null,
                    'package_results' => $label ? ['PDF' => $label] : null,
                    'raw_response' => $response,
                    'status' => 'created',
                ]
            );

            $lockedShipper->status = 'manifested';
            $lockedShipper->save();

            Tracking::firstOrCreate(
                ['shipper_id' => $lockedShipper->id, 'status' => 'manifested'],
                [
                    'awb_number' => $lockedShipper->awb_number,
                    'shipping_id' => $createShipment?->id,
                    'uwc_id' => $lockedShipper->awb_number,
                    'title' => Tracking::getTitleForStatus('manifested'),
                ]
            );

            ShipmentLog::logStatus(
                $lockedShipper->id,
                $lockedShipper->awb_number,
                'manifested',
                'packed',
                'Shipment manifested via Primus'.($isBulk ? ' (bulk)' : '').'. Tracking: '.$trackingNumber,
                $customerId,
                'customer'
            );
        });
    }

    /** @return array{0: string, 1: string} */
    private function readCustomLabel(ShipperInfo $shipper): array
    {
        $url = trim((string) $shipper->custom_label);
        if ($url === '') {
            throw new RuntimeException('A stored custom label is required before creating a Primus shipment.');
        }

        $parts = parse_url($url);
        if ($parts === false) {
            throw new RuntimeException('The stored custom label URL is invalid.');
        }

        if (isset($parts['host'])) {
            $trustedHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
            $storedHost = strtolower((string) $parts['host']);
            $trustedPort = parse_url((string) config('app.url'), PHP_URL_PORT);
            $storedPort = $parts['port'] ?? null;

            if ($trustedHost === '' || $storedHost !== $trustedHost || $storedPort !== $trustedPort) {
                throw new RuntimeException('The stored custom label URL is not a trusted local URL.');
            }
        }

        $path = rawurldecode((string) ($parts['path'] ?? ''));
        $marker = '/uploads/custom_labels/';
        $markerPosition = strpos(str_replace('\\', '/', $path), $marker);
        if ($markerPosition === false) {
            throw new RuntimeException('The stored custom label URL is outside the custom label directory.');
        }

        $filename = substr(str_replace('\\', '/', $path), $markerPosition + strlen($marker));
        if ($filename === '' || $filename !== basename($filename) || str_contains($filename, '..')) {
            throw new RuntimeException('The stored custom label URL contains an invalid file path.');
        }

        $directory = realpath(public_path('uploads/custom_labels'));
        $file = $directory !== false ? realpath($directory.DIRECTORY_SEPARATOR.$filename) : false;
        if ($directory === false || $file === false || ! is_file($file) || ! is_readable($file)) {
            throw new RuntimeException('The stored custom label file is missing or unreadable.');
        }

        $directoryPrefix = rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (! str_starts_with($file, $directoryPrefix)) {
            throw new RuntimeException('The stored custom label file is outside the custom label directory.');
        }

        $bytes = file_get_contents($file);
        if ($bytes === false) {
            throw new RuntimeException('The stored custom label file could not be read.');
        }

        return [basename($file), $bytes];
    }

    private function resolveCourierService(ShipperInfo $shipper): ?CourierService
    {
        if ($shipper->service_id) {
            $service = CourierService::find($shipper->service_id);
            if ($service) {
                return $service;
            }
        }

        return CourierService::query()
            ->where('api_provider', 'primus')
            ->where(function ($query) use ($shipper): void {
                $query->where('method', $shipper->shipping_method)
                    ->orWhere('real_name', $shipper->shipping_method)
                    ->orWhere('description', $shipper->shipping_method);
            })
            ->first();
    }

    private function gstType(string $kycType): string
    {
        return match ($kycType) {
            'GST (Normal)' => 'GSTIN (Normal)',
            'Aadhar Card' => 'Aadhaar Number',
            'PAN Card' => 'PAN Number',
            default => $kycType,
        };
    }

    private function destinationCode(string $destination): string
    {
        $normalized = strtoupper(trim($destination));
        $aliases = [
            'US' => 'USA',
            'UNITED STATES' => 'USA',
            'UNITED STATES OF AMERICA' => 'USA',
            'UK' => 'GBR',
            'UNITED KINGDOM' => 'GBR',
            'CANADA' => 'CAN',
            'AUSTRALIA' => 'AUS',
            'UNITED ARAB EMIRATES' => 'ARE',
            'UAE' => 'ARE',
        ];

        if (isset($aliases[$normalized])) {
            return $aliases[$normalized];
        }

        if (preg_match('/\(([A-Z]{2,3})\)$/', $normalized, $matches) === 1) {
            return $aliases[$matches[1]] ?? $matches[1];
        }

        return $normalized;
    }

    private function yesNo(mixed $value): string
    {
        return in_array(strtolower(trim((string) $value)), ['1', 'yes', 'true', 'y'], true) ? 'Yes' : 'No';
    }

    private function numberString(mixed $value): string
    {
        $number = (float) $value;

        return rtrim(rtrim(number_format($number, 3, '.', ''), '0'), '.') ?: '0';
    }

    /**
     * Return a copy of the payload suitable for debugging output with
     * credential fields masked and the potentially large base64 invoice
     * bytes replaced with a placeholder.
     */
    private function maskedPayload(array $payload): array
    {
        $payload['ValidateAccount'] = array_map(
            static fn (array $account): array => [
                ...$account,
                'Password' => '***',
                'AccessKey' => '***',
            ],
            $payload['ValidateAccount'] ?? []
        );

        $payload['Shipment'] = array_map(
            static fn (array $shipment): array => [
                ...$shipment,
                'Base64StringInvoice' => '(omitted base64 invoice bytes)',
            ],
            $payload['Shipment'] ?? []
        );

        return $payload;
    }
}
