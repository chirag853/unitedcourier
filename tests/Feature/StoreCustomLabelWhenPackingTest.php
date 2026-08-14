<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreCustomLabelWhenPackingTest extends TestCase
{
    private string $originalPublicPath;

    private string $testPublicPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalPublicPath = public_path();
        $this->testPublicPath = storage_path('framework/testing/custom-label-public-' . uniqid());
        app()->usePublicPath($this->testPublicPath);

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->timestamps();
        });

        Schema::create('shipper_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('customer_id');
            $table->string('awb_number')->nullable();
            $table->string('status')->default('draft');
            $table->longText('custom_label')->nullable();
            $table->timestamps();
        });

        Schema::create('create_shipment', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shipper_id');
        });

        Schema::create('tracking', function (Blueprint $table) {
            $table->id();
            $table->string('awb_number')->nullable();
            $table->unsignedInteger('shipper_id')->nullable();
            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->string('uwc_id');
            $table->string('title');
            $table->string('status');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('shipment_logs', function (Blueprint $table) {
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

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->testPublicPath);
        app()->usePublicPath($this->originalPublicPath);

        parent::tearDown();
    }

    public function test_it_stores_a_label_file_url_and_marks_a_ready_shipment_as_packed(): void
    {
        $customer = $this->createCustomer(1, 'owner@example.com');
        $this->createShipper(10, $customer->id, 'ready');
        $label = '<div class="label"><svg><rect width="2" height="100"></rect></svg></div>';

        $response = $this->actingAs($customer, 'customer')->postJson('http://localhost/customer/mark-packed', [
            'shipper_id' => 10,
            'custom_label' => $label,
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $storedUrl = DB::table('shipper_info')->where('id', 10)->value('custom_label');
        $this->assertIsString($storedUrl);
        $this->assertStringStartsWith(asset('uploads/custom_labels/'), $storedUrl);
        $this->assertStringEndsWith('.pdf', $storedUrl);
        $response->assertJsonPath('custom_label_url', $storedUrl);

        $filename = basename((string) parse_url($storedUrl, PHP_URL_PATH));
        $labelPath = $this->testPublicPath . '/uploads/custom_labels/' . $filename;
        $this->assertFileExists($labelPath);

        $storedDocument = file_get_contents($labelPath);
        $this->assertIsString($storedDocument);
        $this->assertStringStartsWith('%PDF-', $storedDocument);
        $this->assertGreaterThan(100, strlen($storedDocument));

        $this->assertDatabaseHas('shipper_info', [
            'id' => 10,
            'customer_id' => $customer->id,
            'status' => 'packed',
            'custom_label' => $storedUrl,
        ]);
        $this->assertDatabaseHas('tracking', [
            'shipper_id' => 10,
            'awb_number' => 'UWC000010',
            'status' => 'packed',
        ]);
        $this->assertDatabaseHas('shipment_logs', [
            'shipper_id' => 10,
            'customer_id' => $customer->id,
            'status' => 'packed',
            'previous_status' => 'ready',
        ]);
    }

    public function test_it_does_not_use_laravel_storage_for_custom_label_generation(): void
    {
        $customer = $this->createCustomer(1, 'owner@example.com');
        $this->createShipper(10, $customer->id, 'ready');
        $originalStoragePath = storage_path();
        $isolatedStoragePath = $this->testPublicPath . '-storage';
        $legacyTemporaryPath = $isolatedStoragePath . '/app/custom-label-temp';

        mkdir($isolatedStoragePath . '/app', 0777, true);
        file_put_contents($legacyTemporaryPath, 'blocked');
        app()->useStoragePath($isolatedStoragePath);

        try {
            $response = $this->actingAs($customer, 'customer')->postJson(
                'http://localhost/customer/mark-packed',
                [
                    'shipper_id' => 10,
                    'custom_label' => '<div>In-memory label</div>',
                ]
            );

            $response->assertOk()->assertJsonPath('success', true);
            $this->assertDatabaseHas('shipper_info', [
                'id' => 10,
                'status' => 'packed',
            ]);
            $this->assertCount(1, $this->customLabelFiles());
            $this->assertFileExists($legacyTemporaryPath);
            $this->assertSame('blocked', file_get_contents($legacyTemporaryPath));
            $this->assertFalse(is_dir($legacyTemporaryPath));
        } finally {
            app()->useStoragePath($originalStoragePath);
            $this->deleteDirectory($isolatedStoragePath);
        }
    }

    public function test_it_keeps_the_shipment_ready_when_the_public_directory_cannot_be_created(): void
    {
        $customer = $this->createCustomer(1, 'owner@example.com');
        $this->createShipper(10, $customer->id, 'ready');
        mkdir($this->testPublicPath . '/uploads', 0777, true);
        file_put_contents($this->testPublicPath . '/uploads/custom_labels', 'not a directory');

        $response = $this->actingAs($customer, 'customer')->postJson('http://localhost/customer/mark-packed', [
            'shipper_id' => 10,
            'custom_label' => '<div>Blocked public label</div>',
        ]);

        $response->assertServerError()
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['message', 'error_reference']);
        $this->assertDatabaseHas('shipper_info', [
            'id' => 10,
            'status' => 'ready',
            'custom_label' => null,
        ]);
        $this->assertSame([], $this->customLabelFiles());
        $this->assertDatabaseCount('tracking', 0);
        $this->assertDatabaseCount('shipment_logs', 0);
    }

    public function test_it_requires_the_custom_label_payload(): void
    {
        $customer = $this->createCustomer(1, 'owner@example.com');
        $this->createShipper(10, $customer->id, 'ready');

        $response = $this->actingAs($customer, 'customer')->postJson('http://localhost/customer/mark-packed', [
            'shipper_id' => 10,
        ]);

        $response->assertStatus(422)->assertJsonPath('success', false);
        $this->assertDatabaseHas('shipper_info', ['id' => 10, 'status' => 'ready', 'custom_label' => null]);
        $this->assertSame([], $this->customLabelFiles());
        $this->assertDatabaseCount('tracking', 0);
        $this->assertDatabaseCount('shipment_logs', 0);
    }

    public function test_it_does_not_allow_a_customer_to_pack_another_customers_shipment(): void
    {
        $customer = $this->createCustomer(1, 'owner@example.com');
        $otherCustomer = $this->createCustomer(2, 'other@example.com');
        $this->createShipper(10, $otherCustomer->id, 'ready');

        $response = $this->actingAs($customer, 'customer')->postJson('http://localhost/customer/mark-packed', [
            'shipper_id' => 10,
            'custom_label' => '<div>Private label</div>',
        ]);

        $response->assertNotFound()->assertJsonPath('success', false);
        $this->assertDatabaseHas('shipper_info', ['id' => 10, 'status' => 'ready', 'custom_label' => null]);
        $this->assertSame([], $this->customLabelFiles());
    }

    public function test_it_still_packs_when_an_audit_table_is_unavailable(): void
    {
        $customer = $this->createCustomer(1, 'owner@example.com');
        $this->createShipper(10, $customer->id, 'ready');
        Schema::drop('tracking');

        $response = $this->actingAs($customer, 'customer')->postJson('http://localhost/customer/mark-packed', [
            'shipper_id' => 10,
            'custom_label' => '<div>Audit fallback label</div>',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('shipper_info', [
            'id' => 10,
            'status' => 'packed',
        ]);
        $this->assertCount(1, $this->customLabelFiles());
        $this->assertDatabaseHas('shipment_logs', [
            'shipper_id' => 10,
            'customer_id' => $customer->id,
            'status' => 'packed',
        ]);
    }

    public function test_it_removes_the_label_file_when_the_essential_database_transaction_fails(): void
    {
        $customer = $this->createCustomer(1, 'owner@example.com');
        $this->createShipper(10, $customer->id, 'ready');
        DB::statement(<<<'SQL'
            CREATE TRIGGER fail_packed_shipment_update
            BEFORE UPDATE ON shipper_info
            BEGIN
                SELECT RAISE(FAIL, 'forced shipment update failure');
            END
        SQL);

        $response = $this->actingAs($customer, 'customer')->postJson('http://localhost/customer/mark-packed', [
            'shipper_id' => 10,
            'custom_label' => '<div>Rollback label</div>',
        ]);

        $response->assertServerError()
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['message', 'error_reference']);
        $errorReference = (string) $response->json('error_reference');
        $this->assertTrue(Str::isUuid($errorReference));
        $this->assertSame(
            'Unable to save the custom label. The shipment remains ready. Reference: ' . $errorReference,
            $response->json('message')
        );
        $this->assertStringNotContainsString('SQLSTATE', (string) $response->json('message'));
        $this->assertDatabaseHas('shipper_info', [
            'id' => 10,
            'status' => 'ready',
            'custom_label' => null,
        ]);
        $this->assertSame([], $this->customLabelFiles());
        $this->assertDatabaseCount('tracking', 0);
        $this->assertDatabaseCount('shipment_logs', 0);
    }

    public function test_it_rejects_a_shipment_that_is_not_ready(): void
    {
        $customer = $this->createCustomer(1, 'owner@example.com');
        $this->createShipper(10, $customer->id, 'packed', '<div>Existing label</div>');

        $response = $this->actingAs($customer, 'customer')->postJson('http://localhost/customer/mark-packed', [
            'shipper_id' => 10,
            'custom_label' => '<div>Replacement label</div>',
        ]);

        $response->assertBadRequest()->assertJsonPath('success', false);
        $this->assertDatabaseHas('shipper_info', [
            'id' => 10,
            'status' => 'packed',
            'custom_label' => '<div>Existing label</div>',
        ]);
        $this->assertSame([], $this->customLabelFiles());
        $this->assertDatabaseCount('tracking', 0);
        $this->assertDatabaseCount('shipment_logs', 0);
    }

    private function customLabelFiles(): array
    {
        return glob($this->testPublicPath . '/uploads/custom_labels/*.pdf') ?: [];
    }

    private function deleteDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = scandir($directory) ?: [];

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($directory);
    }

    private function createCustomer(int $id, string $email): Customer
    {
        DB::table('customers')->insert([
            'id' => $id,
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => $email,
            'password_hash' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Customer::findOrFail($id);
    }

    private function createShipper(int $id, int $customerId, string $status, ?string $customLabel = null): void
    {
        DB::table('shipper_info')->insert([
            'id' => $id,
            'customer_id' => $customerId,
            'awb_number' => 'UWC' . str_pad((string) $id, 6, '0', STR_PAD_LEFT),
            'status' => $status,
            'custom_label' => $customLabel,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
