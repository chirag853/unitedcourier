<?php

namespace Database\Seeders;

use App\Models\ConsigneeInfo;
use App\Models\CourierService;
use App\Models\Customer;
use App\Models\Destination;
use App\Models\PackageDimension;
use App\Models\ShipmentInvoice;
use App\Models\ShipmentInvoiceItem;
use App\Models\ShipmentTracking;
use App\Models\ShipperInfo;
use App\Models\Tracking;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class DummyManifestedShipmentsSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::query()
            ->where('status', 1)
            ->orderBy('id')
            ->first();

        if (!$customer) {
            throw new RuntimeException('No active customer is available for dummy manifested shipments.');
        }

        $services = CourierService::query()
            ->where('status', 1)
            ->whereNotNull('api_provider')
            ->where('api_provider', '<>', '')
            ->orderBy('id')
            ->get();

        foreach ($services as $service) {
            DB::transaction(function () use ($customer, $service): void {
                $this->seedServiceShipment($customer, $service);
            });
        }

        $this->command?->info(sprintf(
            'Created or refreshed %d dummy manifested shipments for customer #%d.',
            $services->count(),
            $customer->getKey()
        ));
    }

    private function consigneeAddress(string $serviceCountry): array
    {
        $country = trim($serviceCountry);
        $normalized = strtoupper($country);

        $destination = Destination::query()
            ->where('is_active', 1)
            ->where(function ($query) use ($normalized): void {
                $query->whereRaw('UPPER(code) = ?', [$normalized])
                    ->orWhereRaw('UPPER(country_code) = ?', [$normalized])
                    ->orWhereRaw('UPPER(name) = ?', [$normalized]);
            })
            ->first();

        $profiles = [
            'US' => ['city' => 'New York', 'state' => 'New York', 'zip_code' => '10001', 'street' => 'Broadway'],
            'UK' => ['city' => 'London', 'state' => 'Greater London', 'zip_code' => 'SW1A 1AA', 'street' => 'Baker Street'],
            'CA' => ['city' => 'Toronto', 'state' => 'Ontario', 'zip_code' => 'M5V 2T6', 'street' => 'King Street'],
            'AUS' => ['city' => 'Sydney', 'state' => 'New South Wales', 'zip_code' => '2000', 'street' => 'George Street'],
            'UAE' => ['city' => 'Dubai', 'state' => 'Dubai', 'zip_code' => '00000', 'street' => 'Sheikh Zayed Road'],
            'NZ' => ['city' => 'Auckland', 'state' => 'Auckland', 'zip_code' => '1010', 'street' => 'Queen Street'],
            'SG' => ['city' => 'Singapore', 'state' => 'Singapore', 'zip_code' => '018956', 'street' => 'Orchard Road'],
            'MY' => ['city' => 'Kuala Lumpur', 'state' => 'Kuala Lumpur', 'zip_code' => '50000', 'street' => 'Jalan Bukit Bintang'],
            'DE' => ['city' => 'Berlin', 'state' => 'Berlin', 'zip_code' => '10115', 'street' => 'Friedrichstrasse'],
        ];

        if ($destination) {
            $destinationCode = strtoupper((string) ($destination->country_code ?: $destination->code));
            $profile = $profiles[$destinationCode] ?? [
                'city' => $destination->name . ' City',
                'state' => $destination->name,
                'zip_code' => '00000',
                'street' => 'Central Avenue',
            ];

            return array_merge($profile, ['destination' => $destination->name]);
        }

        return [
            'destination' => $country,
            'city' => $country . ' City',
            'state' => $country,
            'zip_code' => '00000',
            'street' => 'Central Avenue',
        ];
    }

    private function seedServiceShipment(Customer $customer, CourierService $service): void
    {
        $serviceId = (int) $service->getKey();
        $awbNumber = sprintf('UWC-DUMMY-SVC-%06d', $serviceId);
        $invoiceNumber = sprintf('DUMMY-INV-SVC-%06d', $serviceId);
        $provider = (string) $service->api_provider;
        $shippingMethod = $service->method ?: ($service->real_name ?: $service->service_code ?: $service->method_code);
        $serviceDescription = $service->real_name ?: ($service->method ?: $service->service_code ?: 'Dummy API service');
        $providerTrackingNumber = sprintf('DUMMY-%s-%06d', strtoupper(preg_replace('/[^A-Z0-9]+/i', '', $provider)), $serviceId);
        $consigneeAddress = $this->consigneeAddress((string) $service->country);

        $shipper = ShipperInfo::query()->updateOrCreate(
            [
                'customer_id' => $customer->getKey(),
                'awb_number' => $awbNumber,
            ],
            [
                'shipping_method' => $shippingMethod,
                'shipper_same_as_customer' => false,
                'company_name' => 'United Courier Dummy Shipper',
                'contact_person' => 'Dummy Shipper',
                'address_line1' => '100 Dummy Logistics Park',
                'address_line2' => null,
                'address_line3' => null,
                'pincode' => '110001',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'phone_number' => '9999999999',
                'email' => 'dummy-shipper@example.test',
                'email_opt_out' => true,
                'kyc_type' => null,
                'kyc_number' => null,
                'service_rate_id' => null,
                'service_id' => $serviceId,
                'status' => 'manifested',
                'custom_label' => null,
                'base_price' => 100.00,
                'fuel_price' => 0.00,
                'gst_percentage' => 18.00,
                'gst_amount' => 18.00,
                'surcharge' => null,
                'surcharge_total' => 0.00,
                'total_base_price' => 100.00,
                'total_fuel_price' => 0.00,
                'total_surcharge' => 0.00,
                'total_price' => 118.00,
            ]
        );

        ConsigneeInfo::query()->updateOrCreate(
            ['shipper_id' => $shipper->getKey()],
            [
                'delivery_destination' => $consigneeAddress['destination'],
                'origin_type' => 'business',
                'consignee_name' => 'United Courier Dummy Consignee',
                'contact_person' => 'Dummy Consignee',
                'address_line1' => '200 ' . $consigneeAddress['street'],
                'address_line2' => null,
                'address_line3' => null,
                'zip_code' => $consigneeAddress['zip_code'],
                'city' => $consigneeAddress['city'],
                'state' => $consigneeAddress['state'],
                'phone_number' => '8888889999',
                'email' => 'dummy-consignee@example.test',
                'email_opt_out' => true,
            ]
        );

        $package = PackageDimension::query()->updateOrCreate(
            [
                'shipper_id' => $shipper->getKey(),
                'shipping_method' => $shippingMethod,
            ],
            [
                'actual_weight_kg' => 1.00,
                'length_cm' => 20.00,
                'width_cm' => 15.00,
                'height_cm' => 10.00,
            ]
        );

        $invoice = ShipmentInvoice::query()->updateOrCreate(
            [
                'shipper_id' => $shipper->getKey(),
                'invoice_number' => $invoiceNumber,
            ],
            [
                'invoice_date' => now()->toDateString(),
                'invoice_amount' => 100.00,
                'incoterms' => 'DDP',
                'invoice_currency' => 'INR',
                'reference_number' => $awbNumber,
                'status' => 'active',
                'delivery_type' => 'door_delivery',
                'assigned_delivery_person' => null,
            ]
        );

        ShipmentInvoiceItem::query()->updateOrCreate(
            [
                'invoice_id' => $invoice->getKey(),
                'box_no' => 1,
            ],
            [
                'package_dimension_id' => $package->getKey(),
                'description' => 'Dummy shipment merchandise for ' . $serviceDescription,
                'hs_code' => '85171200',
                'hts_code' => null,
                'unit_type' => 'PCS',
                'qty' => 1.00,
                'unit_rate' => 100.00,
                'igst_percentage' => 0.00,
                'igst_amount' => 0.00,
            ]
        );

        $trackingPayload = [
            'customer_id' => $customer->getKey(),
            'shipper_id' => $shipper->getKey(),
            'create_shipment_id' => null,
            'response_status_code' => '200',
            'response_status_description' => 'Dummy manifested shipment created',
            'transaction_identifier' => $providerTrackingNumber,
            'customer_context' => 'dummy-seeder',
            'shipment_identification_number' => $providerTrackingNumber,
            'transportation_charges_currency' => 'INR',
            'transportation_charges_amount' => 100.00,
            'service_options_charges_currency' => 'INR',
            'service_options_charges_amount' => 0.00,
            'total_charges_currency' => 'INR',
            'total_charges_amount' => 100.00,
            'billing_weight_uom' => 'KGS',
            'billing_weight' => 1.00,
            'package_results' => [[
                'tracking_number' => $providerTrackingNumber,
                'service_id' => $serviceId,
                'service' => $serviceDescription,
            ]],
            'raw_response' => [
                'dummy' => true,
                'provider' => $provider,
                'service_id' => $serviceId,
                'service' => $serviceDescription,
            ],
            'status' => 'manifested',
        ];

        if (Schema::hasColumn('shipment_tracking', 'tracking_provider')) {
            $trackingPayload['tracking_provider'] = $provider;
            $trackingPayload['tracking_status'] = 'manifested';
            $trackingPayload['tracking_response'] = ['dummy' => true, 'tracking_number' => $providerTrackingNumber];
            $trackingPayload['tracking_error'] = null;
            $trackingPayload['tracking_synced_at'] = now();
        }

        ShipmentTracking::query()->updateOrCreate(
            [
                'shipper_id' => $shipper->getKey(),
                'shipment_identification_number' => $providerTrackingNumber,
            ],
            $trackingPayload
        );

        Tracking::query()->updateOrCreate(
            [
                'awb_number' => $awbNumber,
                'shipper_id' => $shipper->getKey(),
                'uwc_id' => $awbNumber,
            ],
            [
                'shipping_id' => null,
                'title' => Tracking::getTitleForStatus('manifested'),
                'status' => 'manifested',
                'created_at' => now(),
            ]
        );
    }
}
