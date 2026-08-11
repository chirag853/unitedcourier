<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PrimusApiClient
{
    /**
     * Request an OAuth password-grant access token from Primus Logistics.
     *
     * @return array{access_token: string, token_type?: string, expires_in?: int}
     */
    public function createToken(): array
    {
        $tokenUrl = (string) config('services.primus.token_url');
        $username = (string) config('services.primus.username');
        $password = (string) config('services.primus.password');
        $grantType = (string) config('services.primus.grant_type', 'password');
        $timeout = (int) config('services.primus.timeout', 30);

        if ($tokenUrl === '' || $username === '' || $password === '') {
            throw new RuntimeException('Primus credentials are not configured.');
        }

        try {
            $response = Http::acceptJson()
                ->asForm()
                ->timeout($timeout)
                ->post($tokenUrl, [
                    'grant_type' => $grantType,
                    'username' => $username,
                    'password' => $password,
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException(
                'Primus token request failed: '.$exception->getMessage(),
                previous: $exception
            );
        }

        $responseData = $response->json();

        if (! $response->successful()) {
            throw new RuntimeException($this->errorMessage($responseData, $response->status()));
        }

        if (! is_array($responseData) || ! isset($responseData['access_token']) || ! is_scalar($responseData['access_token'])) {
            throw new RuntimeException('Primus authentication response did not contain an access token.');
        }

        return $responseData;
    }

    /**
     * Create a shipment using a freshly issued Primus access token.
     */
    public function createShipment(array $payload): array
    {
        $shipmentUrl = trim((string) config('services.primus.shipment_url'));
        $timeout = (int) config('services.primus.timeout', 30);

        if ($shipmentUrl === '') {
            throw new RuntimeException('Primus shipment URL is not configured.');
        }

        $tokenData = $this->createToken();
        $accessToken = trim((string) $tokenData['access_token']);

        if ($accessToken === '') {
            throw new RuntimeException('Primus authentication returned an empty access token.');
        }

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->asJson()
                ->timeout($timeout)
                ->post($shipmentUrl, $payload);
        } catch (ConnectionException $exception) {
            throw new RuntimeException(
                'Primus shipment request could not connect to the vendor API.',
                previous: $exception
            );
        }

        $responseData = $response->json();

        if (! $response->successful()) {
            throw new RuntimeException($this->shipmentErrorMessage($responseData, $response->status()));
        }

        if (! is_array($responseData)) {
            throw new RuntimeException('Primus shipment response was not valid JSON.');
        }

        return $responseData;
    }

    private function errorMessage(mixed $responseData, int $status): string
    {
        if (is_array($responseData)) {
            $description = $responseData['error_description'] ?? $responseData['message'] ?? $responseData['error'] ?? null;

            if (is_scalar($description) && trim((string) $description) !== '') {
                return 'Primus token request failed: '.trim((string) $description);
            }
        }

        return 'Primus token request failed with HTTP status '.$status.'.';
    }

    private function shipmentErrorMessage(mixed $responseData, int $status): string
    {
        if (is_array($responseData)) {
            $description = $responseData['Description']
                ?? $responseData['description']
                ?? $responseData['Message']
                ?? $responseData['message']
                ?? $responseData['error']
                ?? null;

            if (is_scalar($description) && trim((string) $description) !== '') {
                return 'Primus shipment request failed: '.trim((string) $description);
            }
        }

        return 'Primus shipment request failed with HTTP status '.$status.'.';
    }
}
