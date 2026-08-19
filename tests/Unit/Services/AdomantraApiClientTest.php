<?php

namespace Tests\Unit\Services;

use App\Services\AdomantraApiClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class AdomantraApiClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.adomantra', [
            'base_url' => 'https://adomantra.test',
            'endpoint' => '/api/shipment/customer',
            'order_endpoint' => '/api/shipment/order_create',
            'timeout' => 30,
            'connect_timeout' => 10,
            'retries' => 0,
            'retry_delay' => 0,
        ]);
    }

    public function test_it_posts_the_exact_order_payload_as_json(): void
    {
        $payload = [
            'Awbno' => 'UWC000001',
            'AccountCode' => 'CUST001',
            'Sender' => [
                'SenderName' => 'United Exporter',
            ],
            'Receiver' => [
                'ReceiverCountry' => 'US',
            ],
            'ServiceDetails' => [
                'ServiceCode' => 'EXP',
            ],
            'PackageDetails' => [
                'PackageDetail' => [[
                    'Length' => 10.5,
                    'ActualWeight' => 2.25,
                ]],
            ],
            'AdditionalDetails' => [
                'ProductDetails' => [[
                    'HSNCode' => '61091000',
                    'Qty' => 2.0,
                ]],
            ],
            'FreightDetails' => [
                'BasicAmount' => 1000.0,
            ],
            'MiscDetailsTable' => [],
        ];

        Http::fake([
            'adomantra.test/api/shipment/order_create' => Http::response([
                'success' => true,
                'awb' => 'UWC000001',
            ]),
        ]);

        $response = app(AdomantraApiClient::class)->createOrder($payload);

        $this->assertTrue($response['success']);
        $this->assertSame('UWC000001', $response['awb']);
        Http::assertSentCount(1);
        Http::assertSent(function (Request $request) use ($payload): bool {
            return $request->url() === 'https://adomantra.test/api/shipment/order_create'
                && str_starts_with($request->header('Content-Type')[0] ?? '', 'application/json')
                && $request->data() === $payload;
        });
    }

    public function test_it_surfaces_an_order_http_error(): void
    {
        Http::fake([
            'adomantra.test/api/shipment/order_create' => Http::response([
                'message' => 'Invalid shipment data',
            ], 422),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Adomantra order request failed: Invalid shipment data'
        );

        app(AdomantraApiClient::class)->createOrder([
            'Awbno' => 'UWC000002',
        ]);
    }

    public function test_it_rejects_a_non_json_success_response(): void
    {
        Http::fake([
            'adomantra.test/api/shipment/order_create' => Http::response(
                '<html>temporarily unavailable</html>',
                200,
                ['Content-Type' => 'text/html']
            ),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Adomantra order response was not valid JSON.'
        );

        app(AdomantraApiClient::class)->createOrder([
            'Awbno' => 'UWC000003',
        ]);
    }
}
