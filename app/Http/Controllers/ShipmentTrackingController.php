<?php

namespace App\Http\Controllers;

use App\Models\CourierService;
use App\Models\ShipmentTracking;
use App\Models\Tracking;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ShipmentTrackingController extends Controller
{
    /**
     * Synchronize every non-terminal manifested shipment with its carrier.
     * This method is intentionally usable from both the scheduler and an HTTP route.
     */
    public function sync(): array
    {
        $summary = ['processed' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0];

        ShipmentTracking::query()
            ->whereNotNull('shipment_identification_number')
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->with(['shipperInfo'])
            ->chunkById((int) config('services.tracking.chunk_size', 100), function ($records) use (&$summary): void {
                foreach ($records as $tracking) {
                    $summary['processed']++;

                    try {
                        $provider = $this->resolveProvider($tracking);
                        $trackingNumber = trim((string) $tracking->shipment_identification_number);

                        if ($trackingNumber === '') {
                            $this->persistTrackingFailure($tracking, $provider, 'Tracking number is missing.');
                            $summary['skipped']++;

                            continue;
                        }

                        $result = $this->trackByProvider($provider, $trackingNumber);

                        if ($result['state'] === 'skipped') {
                            $this->persistTrackingFailure($tracking, $provider, $result['message'] ?? 'Tracking configuration is missing.');
                            $summary['skipped']++;

                            continue;
                        }

                        if ($result['state'] !== 'success') {
                            $this->persistTrackingFailure(
                                $tracking,
                                $provider,
                                $result['message'] ?? 'Carrier tracking request failed.',
                                $result['response'] ?? null
                            );
                            $summary['failed']++;
                            Log::warning('Shipment tracking provider failed.', [
                                'shipment_tracking_id' => $tracking->id,
                                'provider' => $provider,
                                'message' => $result['message'] ?? null,
                            ]);

                            continue;
                        }

                        $this->persistTrackingResponse($tracking, $provider, $result['response'], $result['status']);
                        $summary['updated']++;
                    } catch (Throwable $exception) {
                        $summary['failed']++;
                        Log::error('Shipment tracking synchronization failed.', [
                            'shipment_tracking_id' => $tracking->id,
                            'exception' => $exception,
                        ]);
                    }
                }
            });

        Log::info('Shipment tracking synchronization completed.', $summary);

        return $summary;
    }

    /** Optional protected endpoint for manual administration/testing. */
    public function run(): JsonResponse
    {
        return response()->json(['success' => true, 'summary' => $this->sync()]);
    }

    private function resolveProvider(ShipmentTracking $tracking): string
    {
        $service = null;
        if ($tracking->shipperInfo?->service_id) {
            $service = CourierService::find($tracking->shipperInfo->service_id);
        }

        $provider = strtolower(trim((string) ($service?->api_provider ?? '')));
        if ($provider !== '') {
            return $provider;
        }

        $method = strtolower((string) ($tracking->shipperInfo?->shipping_method ?? ''));

        return match (true) {
            str_contains($method, 'canada') => 'overseas',
            str_contains($method, 'air premium') => 'postshipping',
            str_contains($method, 'eco post') => 'flyingtigers',
            str_contains($method, 'classic') => 'shipglobal',
            default => 'ups',
        };
    }

    private function trackByProvider(string $provider, string $number): array
    {
        return match ($provider) {
            'shipuniversal', 'ship universal' => $this->shipUniversal($number),
            'overseas', 'overseaslogistic', 'overseas logistic' => $this->overseas($number),
            'ups' => $this->ups($number),
            'primus' => $this->primus($number),
            'postshipping', 'dpd' => $this->genericProvider('postshipping', $number),
            'flyingtigers', 'delivery' => $this->genericProvider('flyingtigers', $number),
            'shipglobal', 'ship global' => $this->genericProvider('shipglobal', $number),
            default => ['state' => 'skipped', 'message' => 'No tracking branch configured for provider '.$provider.'.'],
        };
    }

    private function shipUniversal(string $number): array
    {
        $trackingUrl = (string) config('services.shipuniversal.tracking_url');
        $token = trim((string) config('services.shipuniversal.tracking_token'));

        if ($trackingUrl === '' || $token === '') {
            return ['state' => 'skipped', 'message' => 'ShipUniversal tracking configuration is missing.'];
        }

        return $this->sendTrackingRequest(
            'shipuniversal',
            str_replace('{tracking_number}', rawurlencode($number), $trackingUrl),
            ['Authorization' => 'Bearer '.$token]
        );
    }

    private function overseas(string $number): array
    {
        $tokenUrl = (string) config('services.overseas.token_url');
        $trackingUrl = (string) config('services.overseas.tracking_url');
        if ($tokenUrl === '' || $trackingUrl === '') {
            return ['state' => 'skipped', 'message' => 'Overseas tracking configuration is missing.'];
        }

        $tokenResponse = Http::withBasicAuth(
            (string) config('services.overseas.username'),
            (string) config('services.overseas.password')
        )->asForm()->timeout($this->timeout('overseas'))->post($tokenUrl);
        $token = $tokenResponse->json('access_token');
        if (! $tokenResponse->successful() || ! is_string($token) || $token === '') {
            return ['state' => 'failed', 'message' => 'Overseas token request failed.'];
        }

        return $this->sendTrackingRequest(
            'overseas',
            str_replace('{tracking_number}', rawurlencode($number), $trackingUrl),
            ['Authorization' => 'Bearer '.$token]
        );
    }

    private function ups(string $number): array
    {
        $url = (string) config('services.ups.tracking_url');
        if ($url === '') {
            return ['state' => 'skipped', 'message' => 'UPS tracking URL is not configured.'];
        }

        $token = trim((string) config('services.ups.tracking_token'));
        if ($token === '') {
            $clientId = (string) config('services.ups.client_id');
            $clientSecret = (string) config('services.ups.client_secret');
            $token = Cache::remember('ups_tracking_access_token', 3300, function () use ($clientId, $clientSecret) {
                if ($clientId === '' || $clientSecret === '') {
                    return null;
                }

                $response = Http::withBasicAuth($clientId, $clientSecret)
                    ->asForm()
                    ->timeout($this->timeout('ups'))
                    ->post((string) config('services.ups.token_url'), [
                        'grant_type' => 'client_credentials',
                    ]);

                return $response->successful() ? $response->json('access_token') : null;
            });
        }

        if (! is_string($token) || $token === '') {
            return ['state' => 'skipped', 'message' => 'UPS credentials/token configuration is missing.'];
        }

        $result = $this->sendTrackingRequest(
            'ups',
            str_replace('{tracking_number}', rawurlencode($number), $url),
            [
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json',
                'transId' => (string) config('services.ups.transaction_id', uniqid('tracking_', true)),
                'transactionSrc' => (string) config('services.ups.transaction_src', 'Production'),
            ]
        );

        if ($result['state'] === 'success') {
            $activities = $this->extractUpsActivities($result['response']);
            if ($activities !== []) {
                $result['status'] = $activities[0]['status']['description'] ?? $result['status'];
            }
        }

        return $result;
    }

    private function primus(string $awbNumber): array
    {
        $url = (string) config('services.primus.tracking_url');
        if ($url === '') {
            return ['state' => 'skipped', 'message' => 'Primus tracking URL is not configured.'];
        }

        $accountCode = trim((string) config('services.primus.account_code'));
        $username = trim((string) config('services.primus.username'));
        $password = trim((string) config('services.primus.password'));
        $accessKey = trim((string) config('services.primus.access_key'));

        if ($accountCode === '' || $username === '' || $password === '' || $accessKey === '') {
            return ['state' => 'skipped', 'message' => 'Primus tracking credentials are not configured.'];
        }

        try {
            $response = Http::asJson()
                ->acceptJson()
                ->withHeaders([
                    'Authorization' => (string) config('services.primus.tracking_authorization', ''),
                ])
                ->timeout($this->timeout('primus'))
                ->retry((int) config('services.tracking.retries', 2), (int) config('services.tracking.retry_delay', 500))
                ->post($url, [
                    'ValidateAccount' => [[
                        'AccountCode' => $accountCode,
                        'Username' => $username,
                        'Password' => $password,
                        'AccessKey' => $accessKey,
                    ]],
                    'Awbno' => $awbNumber,
                ]);
        } catch (ConnectionException $exception) {
            return ['state' => 'failed', 'message' => $exception->getMessage()];
        }

        $body = $response->json();
        if (! is_array($body)) {
            $body = ['raw_body' => $response->body()];
        }

        if (! $response->successful()) {
            return ['state' => 'failed', 'message' => 'HTTP '.$response->status(), 'response' => $body];
        }

        return [
            'state' => 'success',
            'response' => $body,
            'status' => $this->extractStatus($body),
        ];
    }

    private function genericProvider(string $provider, string $number): array
    {
        $url = (string) config("services.{$provider}.tracking_url");
        if ($url === '') {
            return ['state' => 'skipped', 'message' => ucfirst($provider).' tracking endpoint is not configured yet.'];
        }

        $headers = [];
        $token = trim((string) config("services.{$provider}.tracking_token"));
        if ($token !== '') {
            $header = (string) config("services.{$provider}.tracking_auth_header", 'Authorization');
            $prefix = trim((string) config("services.{$provider}.tracking_auth_prefix", 'Bearer'));
            $headers[$header] = $prefix !== '' ? $prefix.' '.$token : $token;
        }

        return $this->sendTrackingRequest($provider, str_replace('{tracking_number}', rawurlencode($number), $url), $headers);
    }

    private function sendTrackingRequest(string $provider, string $url, array $headers): array
    {
        try {
            $response = Http::withHeaders($headers)->acceptJson()->timeout($this->timeout($provider))
                ->retry((int) config('services.tracking.retries', 2), (int) config('services.tracking.retry_delay', 500))
                ->get($url);
        } catch (ConnectionException $exception) {
            return ['state' => 'failed', 'message' => $exception->getMessage()];
        }

        $body = $response->json();
        if (! is_array($body)) {
            $body = ['raw_body' => $response->body()];
        }
        if (! $response->successful()) {
            return ['state' => 'failed', 'message' => 'HTTP '.$response->status(), 'response' => $body];
        }

        return ['state' => 'success', 'response' => $body, 'status' => $this->extractStatus($body)];
    }

    private function persistTrackingResponse(
        ShipmentTracking $tracking,
        string $provider,
        array $response,
        ?string $status
    ): void {
        $oldStatus = strtolower(trim((string) $tracking->status));
        $newStatus = $status !== null ? $this->normalizeStatus($status) : ($oldStatus ?: 'in_transit');

        $tracking->forceFill([
            'status' => $newStatus,
            'response_status_description' => $status ?: $tracking->response_status_description,
            'tracking_provider' => $provider,
            'tracking_status' => $status,
            'tracking_response' => $response,
            'tracking_error' => null,
            'tracking_synced_at' => now(),
        ])->save();

        if ($provider === 'ups') {
            $this->persistUpsActivities($tracking, $response);
        }

        if ($provider === 'primus') {
            $this->persistPrimusEvents($tracking, $response);
        }

        if (in_array($provider, ['shipuniversal', 'ship universal'], true)) {
            $this->persistShipUniversalEvents($tracking, $response);
        }

        if ($newStatus !== '' && $newStatus !== $oldStatus) {
            Tracking::firstOrCreate([
                'shipper_id' => $tracking->shipper_id,
                'status' => $newStatus,
            ], [
                'awb_number' => $tracking->shipperInfo?->awb_number,
                'shipping_id' => $tracking->create_shipment_id,
                'uwc_id' => $tracking->shipperInfo?->awb_number ?? $tracking->shipment_identification_number,
                'title' => Tracking::getTitleForStatus($newStatus),
            ]);
            if ($tracking->shipperInfo && in_array($newStatus, ['dispatched', 'delivered'], true)) {
                $tracking->shipperInfo->update(['status' => $newStatus]);
            }
        }
    }

    private function persistTrackingFailure(
        ShipmentTracking $tracking,
        string $provider,
        string $message,
        ?array $response = null
    ): void {
        $tracking->forceFill([
            'tracking_provider' => $provider,
            'tracking_response' => $response,
            'tracking_error' => $message,
            'tracking_synced_at' => now(),
        ])->save();
    }

    private function persistUpsActivities(ShipmentTracking $tracking, array $response): void
    {
        foreach ($this->extractUpsActivities($response) as $activity) {
            $description = trim((string) ($activity['status']['description'] ?? ''));
            if ($description === '') {
                continue;
            }

            $occurredAt = $this->parseUpsActivityDate($activity);
            $status = $this->normalizeStatus($description);

            Tracking::firstOrCreate([
                'shipper_id' => $tracking->shipper_id,
                'title' => $description,
                'created_at' => $occurredAt,
            ], [
                'awb_number' => $tracking->shipperInfo?->awb_number,
                'shipping_id' => $tracking->create_shipment_id,
                'uwc_id' => $tracking->shipperInfo?->awb_number ?? $tracking->shipment_identification_number,
                'status' => $status,
            ]);
        }
    }

    private function persistPrimusEvents(ShipmentTracking $tracking, array $response): void
    {
        foreach ($this->extractPrimusEvents($response) as $event) {
            $description = trim((string) ($event['EventDescription'] ?? ''));
            if ($description === '') {
                continue;
            }

            $occurredAt = $this->parsePrimusEventDate($event);

            Tracking::firstOrCreate([
                'shipper_id' => $tracking->shipper_id,
                'title' => $description,
                'created_at' => $occurredAt,
            ], [
                'awb_number' => $tracking->shipperInfo?->awb_number,
                'shipping_id' => $tracking->create_shipment_id,
                'uwc_id' => $tracking->shipperInfo?->awb_number ?? $tracking->shipment_identification_number,
                'status' => $this->normalizeStatus($description),
            ]);
        }
    }

    private function persistShipUniversalEvents(ShipmentTracking $tracking, array $response): void
    {
        foreach ($this->extractShipUniversalEvents($response) as $event) {
            $description = trim((string) ($event['EventDescription'] ?? ''));
            if ($description === '') {
                continue;
            }

            $occurredAt = $this->parseShipUniversalEventDate($event);

            Tracking::firstOrCreate([
                'shipper_id' => $tracking->shipper_id,
                'title' => $description,
                'created_at' => $occurredAt,
            ], [
                'awb_number' => $tracking->shipperInfo?->awb_number,
                'shipping_id' => $tracking->create_shipment_id,
                'uwc_id' => $tracking->shipperInfo?->awb_number ?? $tracking->shipment_identification_number,
                'status' => $this->normalizeStatus($description),
            ]);
        }
    }

    private function extractShipUniversalEvents(array $response): array
    {
        $events = $response['Data']['Events'] ?? [];

        if (! is_array($events)) {
            return [];
        }

        return array_values(array_filter($events, static function ($event): bool {
            return is_array($event) && isset($event['EventDescription']);
        }));
    }

    private function parseShipUniversalEventDate(array $event): Carbon
    {
        $date = trim((string) ($event['EventDate'] ?? ''));
        $time = trim((string) ($event['EventTime'] ?? ''));

        if ($date !== '') {
            try {
                $eventDate = Carbon::parse($date, (string) config('app.timezone'))->format('Y-m-d');

                return Carbon::parse(trim($eventDate.' '.$time), (string) config('app.timezone'));
            } catch (Throwable) {
                // Fall back to the synchronization time for malformed carrier dates.
            }
        }

        return now();
    }

    private function extractPrimusEvents(array $response): array
    {
        $events = [];

        foreach ($response as $value) {
            if (! is_array($value)) {
                continue;
            }

            if (isset($value['Event']) && is_array($value['Event'])) {
                foreach ($value['Event'] as $event) {
                    if (is_array($event) && isset($event['EventDescription'])) {
                        $events[] = $event;
                    }
                }
            }

            $events = [...$events, ...$this->extractPrimusEvents($value)];
        }

        return $events;
    }

    private function parsePrimusEventDate(array $event): Carbon
    {
        $date = trim((string) ($event['EventDate'] ?? ''));
        $time = trim((string) ($event['EventTime'] ?? ''));

        if ($date !== '') {
            try {
                return Carbon::parse(trim($date.' '.$time), (string) config('app.timezone'));
            } catch (Throwable) {
                // Fall back to the synchronization time for malformed carrier dates.
            }
        }

        return now();
    }

    private function extractUpsActivities(array $response): array
    {
        if (isset($response['activity']) && is_array($response['activity'])) {
            return array_values(array_filter($response['activity'], static function ($activity): bool {
                return is_array($activity) && isset($activity['status']) && is_array($activity['status']);
            }));
        }

        foreach ($response as $value) {
            if (! is_array($value)) {
                continue;
            }

            $activities = $this->extractUpsActivities($value);
            if ($activities !== []) {
                return $activities;
            }
        }

        return [];
    }

    private function parseUpsActivityDate(array $activity): Carbon
    {
        $date = (string) ($activity['gmtDate'] ?? $activity['date'] ?? '');
        $time = str_replace(':', '', (string) ($activity['gmtTime'] ?? $activity['time'] ?? ''));

        if (preg_match('/^\d{8}$/', $date) === 1 && preg_match('/^\d{6}$/', $time) === 1) {
            try {
                return Carbon::createFromFormat('Ymd His', $date.' '.$time, 'UTC')
                    ->setTimezone((string) config('app.timezone'));
            } catch (Throwable) {
                // Fall back to the synchronization time for malformed carrier dates.
            }
        }

        return now();
    }

    private function extractStatus(array $response): ?string
    {
        foreach (['status', 'Status', 'current_status', 'CurrentStatus', 'EventDescription', 'description', 'Description'] as $key) {
            if (isset($response[$key]) && is_scalar($response[$key])) {
                return trim((string) $response[$key]);
            }
        }
        foreach ($response as $value) {
            if (! is_array($value)) {
                continue;
            }

            $status = $this->extractStatus($value);
            if ($status !== null) {
                return $status;
            }
        }

        return null;
    }

    private function normalizeStatus(string $status): string
    {
        $value = strtolower(trim($status));

        return match (true) {
            str_contains($value, 'deliver') => 'delivered',
            str_contains($value, 'cancel') || str_contains($value, 'return') => 'cancelled',
            str_contains($value, 'out for') => 'dispatched',
            str_contains($value, 'dispatch') || str_contains($value, 'transit') || str_contains($value, 'ship') => 'dispatched',
            str_contains($value, 'hold') => 'on_hold',
            default => $value !== '' ? preg_replace('/[^a-z0-9]+/', '_', $value) : 'in_transit',
        };
    }

    private function timeout(string $provider): int
    {
        return (int) config("services.{$provider}.timeout", config('services.tracking.timeout', 30));
    }
}
