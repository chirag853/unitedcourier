<?php

namespace Tests\Unit\Services;

use App\Models\ShipperInfo;
use App\Services\PrimusApiClient;
use App\Services\PrimusShipmentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class PrimusShipmentServiceTest extends TestCase
{
    private string $originalPublicPath;

    private string $testPublicPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalPublicPath = public_path();
        $this->testPublicPath = storage_path('framework/testing/primus-shipment-'.uniqid());
        app()->usePublicPath($this->testPublicPath);

        config()->set('app.url', 'http://localhost');
        config()->set('services.primus', [
            'account_code' => 'TEST-ACCOUNT',
            'username' => 'test-user',
            'password' => 'test-password',
            'access_key' => 'test-access-key',
            'customer_name' => 'Test Exporter',
        ]);

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
        mkdir($this->testPublicPath.'/custom_label', 0777, true);
        mkdir($this->testPublicPath.'/uploads/custom_labels', 0777, true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        $this->deleteDirectory($this->testPublicPath);
        app()->usePublicPath($this->originalPublicPath);

        parent::tearDown();
    }

    public function test_it_builds_the_payload_from_database_records_and_encodes_file_bytes(): void
    {
        $labelBytes = "%PDF-1.4\nActual label bytes";
        $shipper = $this->createShipmentFixture($labelBytes);

        $payload = $this->service()->buildPayload($shipper);
        $shipment = $payload['Shipment'][0];

        $this->assertSame('TEST-ACCOUNT', $payload['ValidateAccount'][0]['AccountCode']);
        $this->assertSame('test-user', $payload['ValidateAccount'][0]['Username']);
        $this->assertSame('S', $shipment['ServiceTypeCode']);
        $this->assertSame('United Kingdom', $shipment['DestinationCode']);
        $this->assertSame('1.500', $shipment['ActWeight']);
        $this->assertSame('Sender Street', $shipment['ConsignorAddressLine1']);
        $this->assertSame('Sender Street', $shipment['ConsignorAddressLine2']);
        $this->assertSame('Receiver Street', $shipment['ConsigneeAddressLine1']);
        $this->assertSame('Receiver Street', $shipment['ConsigneeAddressLine2']);
        $this->assertSame('INV-100', $shipment['ExporterInvNo']);
        $this->assertSame('Cotton shirts', $shipment['ItemDetails'][0]['Description']);
        $this->assertSame('label-10.pdf', $shipment['filename']);
        $this->assertSame(base64_encode($labelBytes), $shipment['Base64StringInvoice']);
        $this->assertNotSame(base64_encode((string) $shipper->custom_label), $shipment['Base64StringInvoice']);
    }

    public function test_it_reads_legacy_public_custom_label_urls(): void
    {
        $labelBytes = "%PDF-1.4\nLegacy public label bytes";
        $shipper = $this->createShipmentFixture('new public label bytes');
        file_put_contents($this->testPublicPath.'/uploads/custom_labels/label-10.pdf', $labelBytes);
        $shipper->custom_label = 'http://localhost/uploads/custom_labels/label-10.pdf';

        $payload = $this->service()->buildPayload($shipper);

        $this->assertSame('label-10.pdf', $payload['Shipment'][0]['filename']);
        $this->assertSame(base64_encode($labelBytes), $payload['Shipment'][0]['Base64StringInvoice']);
    }

    public function test_it_reads_legacy_public_disk_custom_label_urls(): void
    {
        $labelBytes = "%PDF-1.4\nLegacy storage label bytes";
        $shipper = $this->createShipmentFixture('new public label bytes');
        $publicDiskRoot = $this->testPublicPath.'/legacy-public-disk';
        mkdir($publicDiskRoot.'/custom_labels', 0777, true);
        file_put_contents($publicDiskRoot.'/custom_labels/label-10.pdf', $labelBytes);
        config()->set('filesystems.disks.public.root', $publicDiskRoot);
        $shipper->custom_label = 'http://localhost/storage/custom_labels/label-10.pdf';

        $payload = $this->service()->buildPayload($shipper);

        $this->assertSame('label-10.pdf', $payload['Shipment'][0]['filename']);
        $this->assertSame(base64_encode($labelBytes), $payload['Shipment'][0]['Base64StringInvoice']);
    }

    public function test_it_uses_the_second_consignor_address_line_when_present(): void
    {
        $shipper = $this->createShipmentFixture('label bytes');
        DB::table('shipper_info')->where('id', 10)->update(['address_line2' => 'Sender Street 2']);
        $shipper->refresh();

        $payload = $this->service()->buildPayload($shipper);
        $shipment = $payload['Shipment'][0];

        $this->assertSame('Sender Street', $shipment['ConsignorAddressLine1']);
        $this->assertSame('Sender Street 2', $shipment['ConsignorAddressLine2']);
    }

    public function test_it_uses_the_second_consignee_address_line_when_present(): void
    {
        $shipper = $this->createShipmentFixture('label bytes');
        DB::table('consignee_info')->where('shipper_id', 10)->update(['address_line2' => 'Receiver Street 2']);

        $payload = $this->service()->buildPayload($shipper);
        $shipment = $payload['Shipment'][0];

        $this->assertSame('Receiver Street', $shipment['ConsigneeAddressLine1']);
        $this->assertSame('Receiver Street 2', $shipment['ConsigneeAddressLine2']);
    }

    public function test_it_rejects_an_external_custom_label_host(): void
    {
        $shipper = $this->createShipmentFixture('private label');
        $shipper->custom_label = 'http://attacker.example/uploads/custom_labels/label-10.pdf';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stored custom label URL is not a trusted local URL.');

        $this->service()->buildPayload($shipper);
    }

    public function test_it_rejects_a_custom_label_url_with_a_mismatched_port(): void
    {
        config()->set('app.url', 'http://localhost:8080');
        $shipper = $this->createShipmentFixture('private label');
        $shipper->custom_label = 'http://localhost:9090/uploads/custom_labels/label-10.pdf';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stored custom label URL is not a trusted local URL.');

        $this->service()->buildPayload($shipper);
    }

    public function test_it_rejects_traversal_and_nested_custom_label_paths(): void
    {
        $shipper = $this->createShipmentFixture('private label');
        $shipper->custom_label = 'http://localhost/uploads/custom_labels/nested/label-10.pdf';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stored custom label URL contains an invalid file path.');

        $this->service()->buildPayload($shipper);
    }

    public function test_it_rejects_a_missing_custom_label_file(): void
    {
        $shipper = $this->createShipmentFixture('private label');
        unlink($this->testPublicPath.'/custom_label/label-10.pdf');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stored custom label file is missing or unreadable.');

        $this->service()->buildPayload($shipper);
    }

    public function test_it_interprets_business_failure_even_when_the_http_request_succeeded(): void
    {
        $result = $this->service()->interpretResponse([[
            'ShipmentResponses' => [[
                'Status' => 'Failed',
                'Code' => '400',
                'Description' => 'Consignee postal code is invalid',
            ]],
            'shipmentDetails' => [],
        ]]);

        $this->assertFalse($result['success']);
        $this->assertSame('Consignee postal code is invalid', $result['message']);
    }

    public function test_it_requires_an_identifier_after_business_success(): void
    {
        $result = $this->service()->interpretResponse([[
            'ShipmentResponses' => [[
                'Status' => 'Success',
                'Code' => '100',
                'Description' => 'Saved',
            ]],
            'shipmentDetails' => [['PDF' => 'label-data']],
        ]]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('without a usable AWB or tracking number', $result['message']);
    }

    public function test_it_prioritizes_awb_and_preserves_the_returned_pdf_value(): void
    {
        $result = $this->service()->interpretResponse([[
            'ShipmentResponses' => [[
                'Status' => 'Success',
                'Code' => '100',
                'Description' => 'Saved',
            ]],
            'shipmentDetails' => [[
                'AwbNo' => 'AWB-FIRST',
                'TrackingNo' => 'TRACK-SECOND',
                'TrackingNo2' => 'TRACK-THIRD',
                'PDF' => 'base64-or-url-from-vendor',
            ]],
        ]]);

        $this->assertTrue($result['success']);
        $this->assertSame('AWB-FIRST', $result['tracking_number']);
        $this->assertSame('base64-or-url-from-vendor', $result['label']);
    }

    public function test_it_handles_malformed_status_and_detail_elements_without_php_errors(): void
    {
        $result = $this->service()->interpretResponse([[
            'ShipmentResponses' => ['malformed'],
            'shipmentDetails' => ['malformed'],
        ]]);

        $this->assertFalse($result['success']);
        $this->assertSame('Primus shipment creation failed.', $result['message']);
    }

    public function test_manifest_persists_a_successful_response_transactionally(): void
    {
        $shipper = $this->createShipmentFixture('manifest label');
        $response = $this->successResponse();
        $client = Mockery::mock(PrimusApiClient::class);
        $client->shouldReceive('createShipment')
            ->once()
            ->with(Mockery::on(fn (array $payload): bool => $payload['Shipment'][0]['Base64StringInvoice'] === base64_encode('manifest label')))
            ->andReturn($response);

        $result = (new PrimusShipmentService($client))->manifest($shipper, 1, true);

        $this->assertTrue($result['success']);
        $this->assertSame('PRIMUS-AWB-100', $result['tracking_number']);
        $this->assertSame('TEST-ACCOUNT', $result['payload']['ValidateAccount'][0]['AccountCode']);
        $this->assertSame('***', $result['payload']['ValidateAccount'][0]['Password']);
        $this->assertSame('***', $result['payload']['ValidateAccount'][0]['AccessKey']);
        $this->assertSame('(omitted base64 invoice bytes)', $result['payload']['Shipment'][0]['Base64StringInvoice']);
        $this->assertDatabaseHas('shipper_info', ['id' => 10, 'status' => 'manifested']);
        $this->assertDatabaseHas('shipment_tracking', [
            'shipper_id' => 10,
            'customer_id' => 1,
            'shipment_identification_number' => 'PRIMUS-AWB-100',
            'status' => 'created',
        ]);
        $this->assertDatabaseHas('tracking', ['shipper_id' => 10, 'status' => 'manifested']);
        $this->assertDatabaseHas('shipment_logs', [
            'shipper_id' => 10,
            'status' => 'manifested',
            'previous_status' => 'packed',
        ]);
        $this->assertStringContainsString('(bulk)', (string) DB::table('shipment_logs')->value('description'));
    }

    public function test_manifest_leaves_the_shipment_packed_on_business_failure(): void
    {
        $shipper = $this->createShipmentFixture('manifest label');
        $client = Mockery::mock(PrimusApiClient::class);
        $client->shouldReceive('createShipment')->once()->andReturn([[
            'ShipmentResponses' => [[
                'Status' => 'Failed',
                'Code' => '400',
                'Description' => 'Invalid shipment',
            ]],
            'shipmentDetails' => [],
        ]]);

        $result = (new PrimusShipmentService($client))->manifest($shipper, 1);

        $this->assertFalse($result['success']);
        $this->assertSame('Invalid shipment', $result['message']);
        $this->assertSame('***', $result['payload']['ValidateAccount'][0]['Password']);
        $this->assertSame('Sender Street', $result['payload']['Shipment'][0]['ConsignorAddressLine2']);
        $this->assertDatabaseHas('shipper_info', ['id' => 10, 'status' => 'packed']);
        $this->assertDatabaseCount('shipment_tracking', 0);
        $this->assertDatabaseCount('tracking', 0);
        $this->assertDatabaseCount('shipment_logs', 0);
    }

    public function test_manifest_rolls_back_all_changes_when_persistence_fails(): void
    {
        $shipper = $this->createShipmentFixture('manifest label');
        Schema::drop('tracking');
        $client = Mockery::mock(PrimusApiClient::class);
        $client->shouldReceive('createShipment')->once()->andReturn($this->successResponse());

        $result = (new PrimusShipmentService($client))->manifest($shipper, 1);

        $this->assertFalse($result['success']);
        $this->assertSame('Primus shipment could not be saved. The shipment remains packed.', $result['message']);
        $this->assertDatabaseHas('shipper_info', ['id' => 10, 'status' => 'packed']);
        $this->assertDatabaseCount('shipment_tracking', 0);
        $this->assertDatabaseCount('shipment_logs', 0);
    }

    private function service(): PrimusShipmentService
    {
        return new PrimusShipmentService(Mockery::mock(PrimusApiClient::class));
    }

    private function createShipmentFixture(string $labelBytes): ShipperInfo
    {
        file_put_contents($this->testPublicPath.'/custom_label/label-10.pdf', $labelBytes);

        DB::table('courier_services')->insert([
            'id' => 5,
            'network' => 'FDX',
            'service_code' => 'PRIMUS-FDX',
            'method' => 'FedEx Priority',
            'description' => 'FedEx Priority',
            'real_name' => 'FedEx International Priority',
            'country' => 'UK',
            'status' => 1,
            'api_provider' => 'primus',
        ]);

        DB::table('shipper_info')->insert([
            'id' => 10,
            'customer_id' => 1,
            'awb_number' => 'UWC000010',
            'shipping_method' => 'FedEx Priority',
            'company_name' => 'Sender Company',
            'contact_person' => 'Sender Person',
            'address_line1' => 'Sender Street',
            'pincode' => '110001',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'phone_number' => '9999999999',
            'kyc_type' => 'GST',
            'kyc_number' => 'TESTGST123',
            'service_id' => 5,
            'status' => 'packed',
            'custom_label' => 'http://localhost/custom_label/label-10.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('consignee_info')->insert([
            'shipper_id' => 10,
            'delivery_destination' => 'United States',
            'origin_type' => 'CSB 5',
            'consignee_name' => 'Receiver Name',
            'contact_person' => 'Receiver Person',
            'address_line1' => 'Receiver Street',
            'zip_code' => '10001',
            'city' => 'New York',
            'state' => 'NY',
            'phone_number' => '1111111111',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('package_dimension')->insert([
            'id' => 20,
            'shipper_id' => 10,
            'actual_weight_kg' => 1.5,
            'length_cm' => 20,
            'width_cm' => 15,
            'height_cm' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('shipment_invoice')->insert([
            'id' => 30,
            'shipper_id' => 10,
            'invoice_number' => 'INV-100',
            'invoice_date' => '2026-08-10',
            'invoice_amount' => 100,
            'incoterms' => 'CIF',
            'invoice_currency' => 'USD',
            'reference_number' => 'REF-100',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('shipment_invoice_items')->insert([
            'invoice_id' => 30,
            'package_dimension_id' => 20,
            'box_no' => 1,
            'description' => 'Cotton shirts',
            'hs_code' => '610510',
            'unit_type' => 'PCS',
            'qty' => 2,
            'unit_rate' => 50,
            'igst_percentage' => 18,
            'igst_amount' => 18,
            'amount' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('csb_information')->insert([
            'shipper_id' => 10,
            'ecommerce' => 'Yes',
            'scheme' => 'No',
            'bond_ut_igst' => 'IGST',
            'gst_number' => 'TESTGST123',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('create_shipment')->insert(['id' => 40, 'shipper_id' => 10]);

        return ShipperInfo::findOrFail(10);
    }

    private function successResponse(): array
    {
        return [[
            'ShipmentResponses' => [[
                'Status' => 'Success',
                'Code' => '100',
                'Description' => 'Shipment saved successfully',
            ]],
            'shipmentDetails' => [[
                'AwbNo' => 'PRIMUS-AWB-100',
                'PDF' => 'vendor-pdf-value',
                'Amount' => '125.50',
                'Weight' => '1.50',
            ]],
        ]];
    }

    private function createSchema(): void
    {
        Schema::create('shipper_info', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedBigInteger('customer_id');
            $table->string('awb_number')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('company_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->string('pincode')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('kyc_type')->nullable();
            $table->string('kyc_number')->nullable();
            $table->unsignedInteger('service_id')->nullable();
            $table->string('status');
            $table->longText('custom_label')->nullable();
            $table->timestamps();
        });

        Schema::create('consignee_info', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('shipper_id');
            $table->string('delivery_destination')->nullable();
            $table->string('origin_type')->nullable();
            $table->string('consignee_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('phone_number')->nullable();
            $table->timestamps();
        });

        Schema::create('package_dimension', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('shipper_id');
            $table->decimal('actual_weight_kg', 10, 2)->nullable();
            $table->decimal('length_cm', 10, 2)->nullable();
            $table->decimal('width_cm', 10, 2)->nullable();
            $table->decimal('height_cm', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_invoice', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('shipper_id');
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->decimal('invoice_amount', 12, 2)->nullable();
            $table->string('incoterms')->nullable();
            $table->string('invoice_currency')->nullable();
            $table->string('reference_number')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_invoice_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('package_dimension_id')->nullable();
            $table->integer('box_no')->nullable();
            $table->string('description')->nullable();
            $table->string('hs_code')->nullable();
            $table->string('unit_type')->nullable();
            $table->decimal('qty', 10, 2)->nullable();
            $table->decimal('unit_rate', 12, 2)->nullable();
            $table->decimal('igst_percentage', 10, 2)->nullable();
            $table->decimal('igst_amount', 12, 2)->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('csb_information', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('shipper_id');
            $table->string('ecommerce')->nullable();
            $table->string('scheme')->nullable();
            $table->string('bond_ut_igst')->nullable();
            $table->string('gst_number')->nullable();
            $table->timestamps();
        });

        Schema::create('courier_services', function (Blueprint $table): void {
            $table->id();
            $table->string('network')->nullable();
            $table->string('service_code')->nullable();
            $table->string('scode')->nullable();
            $table->string('method')->nullable();
            $table->string('description')->nullable();
            $table->string('real_name')->nullable();
            $table->string('country')->nullable();
            $table->integer('status')->default(1);
            $table->string('api_provider')->nullable();
        });

        Schema::create('destinations', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->string('country_code', 5)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('destinations')->insert([
            'name' => 'UK - United Kingdom',
            'code' => 'UK',
            'country_code' => 'UK',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::create('create_shipment', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('shipper_id');
        });

        Schema::create('shipment_tracking', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedInteger('shipper_id')->unique();
            $table->unsignedBigInteger('create_shipment_id')->nullable();
            $table->string('response_status_code')->nullable();
            $table->text('response_status_description')->nullable();
            $table->string('shipment_identification_number')->nullable();
            $table->string('total_charges_currency')->nullable();
            $table->decimal('total_charges_amount', 12, 2)->nullable();
            $table->string('billing_weight_uom')->nullable();
            $table->decimal('billing_weight', 12, 2)->nullable();
            $table->json('package_results')->nullable();
            $table->json('raw_response')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('tracking', function (Blueprint $table): void {
            $table->id();
            $table->string('awb_number')->nullable();
            $table->unsignedInteger('shipper_id')->nullable();
            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->string('uwc_id');
            $table->string('title');
            $table->string('status');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('shipment_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('shipper_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('awb_number');
            $table->string('status');
            $table->string('previous_status')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('performed_by')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    private function deleteDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        foreach (scandir($directory) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory.DIRECTORY_SEPARATOR.$item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($directory);
    }
}
