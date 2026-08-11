<?php

namespace Tests\Unit\Services;

use App\Services\PrimusApiClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class PrimusApiClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.primus', [
            'token_url' => 'http://api.primuslogistics.in/token',
            'shipment_url' => 'http://api.primuslogistics.in/api/ShippingFedEx/AddShipment',
            'username' => 'test-user',
            'password' => 'test-password',
            'grant_type' => 'password',
            'timeout' => 30,
        ]);
    }

    public function test_it_requests_and_returns_a_primus_access_token(): void
    {
        Http::fake([
            'api.primuslogistics.in/token' => Http::response([
                'access_token' => 'primus-access-token',
                'token_type' => 'bearer',
                'expires_in' => 86399,
            ]),
        ]);

        $token = app(PrimusApiClient::class)->createToken();

        $this->assertSame('primus-access-token', $token['access_token']);
        $this->assertSame('bearer', $token['token_type']);
        $this->assertSame(86399, $token['expires_in']);

        Http::assertSentCount(1);
        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'http://api.primuslogistics.in/token'
                && str_starts_with($request->header('Content-Type')[0] ?? '', 'application/x-www-form-urlencoded')
                && $request->data() === [
                    'grant_type' => 'password',
                    'username' => 'test-user',
                    'password' => 'test-password',
                ];
        });
    }

    public function test_it_surfaces_the_vendor_authentication_error(): void
    {
        Http::fake([
            'api.primuslogistics.in/token' => Http::response([
                'error' => 'invalid_grant',
                'error_description' => 'Provided username and password is incorrect',
            ], 400),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Primus token request failed: Provided username and password is incorrect'
        );

        app(PrimusApiClient::class)->createToken();
    }

    public function test_it_rejects_a_successful_response_without_an_access_token(): void
    {
        Http::fake([
            'api.primuslogistics.in/token' => Http::response([
                'token_type' => 'bearer',
                'expires_in' => 86399,
            ]),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Primus authentication response did not contain an access token.'
        );

        app(PrimusApiClient::class)->createToken();
    }

    public function test_it_uses_the_generated_token_to_create_a_json_shipment(): void
    {
        $payload = [
            'ValidateAccount' => [['AccountCode' => 'TEST-ACCOUNT']],
            'Shipment' => [['AwbNo' => 'UWC-1001']],
        ];

        Http::fake([
            'api.primuslogistics.in/token' => Http::response([
                'access_token' => 'exact-generated-token',
            ]),
            'api.primuslogistics.in/api/ShippingFedEx/AddShipment' => Http::response([
                [
                    'ShipmentResponses' => [[
                        'Status' => 'Success',
                        'Code' => '100',
                        'Description' => 'Shipment saved successfully',
                    ]],
                    'shipmentDetails' => [['AwbNo' => '220040818']],
                ],
            ]),
        ]);

        $response = app(PrimusApiClient::class)->createShipment($payload);

        $this->assertSame('220040818', $response[0]['shipmentDetails'][0]['AwbNo']);
        Http::assertSentCount(2);
        Http::assertSent(function (Request $request) use ($payload): bool {
            return $request->url() === 'http://api.primuslogistics.in/api/ShippingFedEx/AddShipment'
                && $request->hasHeader('Authorization', 'Bearer exact-generated-token')
                && str_starts_with($request->header('Content-Type')[0] ?? '', 'application/json')
                && $request->data() === $payload;
        });
    }

    public function test_it_surfaces_shipment_http_errors_without_exposing_secrets(): void
    {
        Http::fake([
            'api.primuslogistics.in/token' => Http::response([
                'access_token' => 'secret-generated-token',
            ]),
            'api.primuslogistics.in/api/ShippingFedEx/AddShipment' => Http::response([
                'message' => 'Vendor shipment validation failed',
            ], 422),
        ]);

        try {
            app(PrimusApiClient::class)->createShipment(['Shipment' => []]);
            $this->fail('Expected Primus shipment failure to throw an exception.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Primus shipment request failed: Vendor shipment validation failed',
                $exception->getMessage()
            );
            $this->assertStringNotContainsString('secret-generated-token', $exception->getMessage());
            $this->assertStringNotContainsString('test-password', $exception->getMessage());
        }
    }

    public function test_it_rejects_a_non_json_shipment_response(): void
    {
        Http::fake([
            'api.primuslogistics.in/token' => Http::response([
                'access_token' => 'exact-generated-token',
            ]),
            'api.primuslogistics.in/api/ShippingFedEx/AddShipment' => Http::response(
                '<html>temporarily unavailable</html>',
                200,
                ['Content-Type' => 'text/html']
            ),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Primus shipment response was not valid JSON.');

        app(PrimusApiClient::class)->createShipment(['Shipment' => []]);
    }

    public function test_it_rejects_missing_credentials_without_sending_a_request(): void
    {
        config()->set('services.primus.password', null);
        Http::fake();

        try {
            app(PrimusApiClient::class)->createToken();
            $this->fail('Expected missing Primus credentials to throw an exception.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Primus credentials are not configured.', $exception->getMessage());
        }

        Http::assertNothingSent();
    }
}
