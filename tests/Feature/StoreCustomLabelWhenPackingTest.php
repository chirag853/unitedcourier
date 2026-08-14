<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StoreCustomLabelWhenPackingTest extends TestCase
{
    private string $testStoragePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testStoragePath = storage_path('framework/testing/custom-labels-' . uniqid());
        config()->set('filesystems.disks.public.root', $this->testStoragePath);
        config()->set('filesystems.disks.public.url', asset('storage'));

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
        $this->deleteDirectory($this->testStoragePath);

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
        $this->assertStringStartsWith(asset('storage/custom_labels/'), $storedUrl);
        $this->assertStringEndsWith('.pdf', $storedUrl);
        $response->assertJsonPath('custom_label_url', $storedUrl);

        $filename = basename((string) parse_url($storedUrl, PHP_URL_PATH));
        $labelPath = $this->testStoragePath . '/custom_labels/' . $filename;
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

    public function test_it_removes_the_label_file_when_the_database_transaction_fails(): void
    {
        $customer = $this->createCustomer(1, 'owner@example.com');
        $this->createShipper(10, $customer->id, 'ready');
        Schema::drop('tracking');

        $response = $this->actingAs($customer, 'customer')->postJson('http://localhost/customer/mark-packed', [
            'shipper_id' => 10,
            'custom_label' => '<div>Rollback label</div>',
        ]);

        $response->assertServerError()
            ->assertJsonPath('success', false)
            ->assertJsonPath(
                'message',
                'Unable to store the custom label PDF. The shipment remains ready.'
            );
        $this->assertStringNotContainsString('SQLSTATE', (string) $response->json('message'));
        $this->assertDatabaseHas('shipper_info', [
            'id' => 10,
            'status' => 'ready',
            'custom_label' => null,
        ]);
        $this->assertSame([], $this->customLabelFiles());
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
        return glob($this->testStoragePath . '/custom_labels/*.pdf') ?: [];
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
