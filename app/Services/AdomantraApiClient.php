<?php

namespace App\Services;

use App\Models\CsbForm;
use App\Models\Customer;
use App\Models\KycDetail;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AdomantraApiClient
{
    /**
     * Create a customer in the Adomantra CMS system.
     *
     * @return array response payload as decoded by the vendor API
     */
    public function createCustomer(array $payload): array
    {
        $baseUrl = rtrim((string) config('services.adomantra.base_url'), '/');
        $endpoint = (string) config('services.adomantra.endpoint', '/api/shipment/customer');
        $timeout = (int) config('services.adomantra.timeout', 30);

        if ($baseUrl === '') {
            throw new RuntimeException('Adomantra base URL is not configured.');
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout($timeout)
                ->post($baseUrl . $endpoint, $payload);
        } catch (ConnectionException $exception) {
            throw new RuntimeException(
                'Adomantra customer request could not connect to the vendor API.',
                previous: $exception
            );
        }

        $responseData = $response->json();

        if (! $response->successful()) {
            throw new RuntimeException($this->errorMessage($responseData, $response->status()));
        }

        if (! is_array($responseData)) {
            throw new RuntimeException('Adomantra customer response was not valid JSON.');
        }

        return $responseData;
    }

    /**
     * Build the shipment/customer payload from a KYC-approved customer.
     */
    public function buildPayload(Customer $customer, KycDetail $kyc, ?CsbForm $csbForm = null): array
    {
        $name = trim((string) ($kyc->organization_name ?: $customer->first_name . ' ' . $customer->last_name));
        $contactPerson = trim((string) $customer->first_name . ' ' . $customer->last_name);
        $phone = trim((string) $customer->phone_number);
        $email = trim((string) $customer->email);
        $billingEmail = trim((string) ($kyc->billing_email ?: $email));
        $gstNumber = strtoupper((string) ($kyc->gst_number ?: $kyc->billing_gst ?: $csbForm?->gst_certificate_number));
        $address = $this->parseAddress((string) ($kyc->billing_address ?: ''), $gstNumber);

        return [
            'Client' => [
                'Type' => 'Client',
                'Code' => (string) ($customer->customer_code ?: 'CUST' . str_pad((string) $customer->id, 4, '0', STR_PAD_LEFT)),
                'Name' => $name,
                'ContactPerson' => $contactPerson,
                'Address1' => $address['address1'],
                'Address2' => $address['address2'],
                'Address3' => $address['address3'],
                'CityCode' => $address['city_code'],
                'CityName' => $address['city'],
                'StateCode' => $address['state_code'],
                'StateName' => $address['state'],
                'CountryCode' => 'IN',
                'CountryName' => 'India',
                'Pincode' => $address['pincode'],
                'Telephone' => $phone,
                'CSEmailID' => $email,
                'BillingEmailID' => $billingEmail,
                'GSTNo' => $gstNumber,
                'PANNo' => strtoupper((string) ($kyc->pan_number ?: $customer->pan_number)),
                'CINNo' => '',
                'SalePerson' => '',
                'AccountPerson' => '',
                'CollectionManager' => '',
                'OpeningBalance' => 0,
                'CreditLimit' => 0,
                'Password' => '',
                'StateGSTCode' => $address['state_gst_code'],
                'BusinessType' => '',
                'AadhaarNo' => (string) ($kyc->aadhar_number ?: $customer->aadhar_number),
                'WhatsAppPreFix' => '+91',
                'WhatsAppNo' => $phone,
                'IECNo' => (string) ($csbForm?->iec_number ?? ''),
                'TINNo' => '',
                'ADCode' => (string) ($csbForm?->ad_code ?? ''),
                'Category' => 'Regular',
                'AcOpeningDate' => now()->format('Y-m-d\TH:i:s'),
                'AcOpeningBy' => 'ADMIN',
                'BankName' => '',
                'AcNo' => (string) ($csbForm?->bank_account_number ?? ''),
                'IFSC' => '',
                'BankAddress' => '',
                'Loguser' => 'ADMIN',
                'ModifyBy' => 'ADMIN',
            ],
        ];
    }

    /**
     * Best-effort address parsing: pincode, city, state and GST state code
     * are resolved from the GSTIN when available, otherwise from the
     * free-text billing address.
     *
     * @return array{address1: string, address2: string, address3: string, city: string, city_code: string, state: string, state_code: string, pincode: string, state_gst_code: string}
     */
    private function parseAddress(string $address, string $gstNumber = ''): array
    {
        $result = [
            'address1' => '',
            'address2' => '',
            'address3' => '',
            'city' => '',
            'city_code' => '',
            'state' => '',
            'state_code' => '',
            'pincode' => '',
            'state_gst_code' => '',
        ];

        $gstNumber = strtoupper(trim($gstNumber));
        if (preg_match('/^[0-9]{2}/', $gstNumber, $gstStateMatch)) {
            $result['state_gst_code'] = $gstStateMatch[0];
        }

        $address = trim($address);
        if ($address === '') {
            return $result;
        }

        if (preg_match('/\b([1-9][0-9]{5})\b/', $address, $pinMatch)) {
            $result['pincode'] = $pinMatch[1];
            $address = trim((string) preg_replace('/[-,:;]*\s*' . $pinMatch[1] . '\b/', '', $address));
        }

        $parts = array_values(array_filter(
            array_map('trim', explode(',', $address)),
            fn (string $part): bool => $part !== ''
        ));
        if (! $parts) {
            return $result;
        }

        $stateIndex = null;
        $foundState = '';
        $foundStateGstCode = '';
        foreach ($this->states() as $gstCode => [$stateName, $stateCode]) {
            $index = $this->findPartIndex($parts, $stateName);
            if ($index !== null) {
                $stateIndex = $index;
                $foundState = $stateName;
                $foundStateGstCode = $gstCode;
                break;
            }
        }

        if ($stateIndex !== null) {
            $cityPart = $parts[$stateIndex - 1] ?? '';
            $result['state'] = $foundState;
            $result['state_code'] = $this->states()[$foundStateGstCode][1] ?? '';
            if ($result['state_gst_code'] === '') {
                $result['state_gst_code'] = $foundStateGstCode;
            }
            if (str_contains($cityPart, '-')) {
                [$cityPart] = explode('-', $cityPart, 2);
                $cityPart = trim($cityPart);
            }
            $result['city'] = $cityPart;
        } elseif ($result['state_gst_code'] !== '' && isset($this->states()[$result['state_gst_code']])) {
            $result['state'] = $this->states()[$result['state_gst_code']][0];
            $result['state_code'] = $this->states()[$result['state_gst_code']][1];
        } else {
            $result['city'] = $parts[array_key_last($parts)];
        }

        if ($result['city'] !== '') {
            $result['city_code'] = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $result['city']) ?? '', 0, 3));
        }

        $addressParts = array_values(array_filter($parts, function (string $part, int $index) use ($stateIndex, $result) {
            if ($stateIndex !== null && $index >= $stateIndex - 1) {
                return false;
            }
            return trim($part, ' -') !== '';
        }, ARRAY_FILTER_USE_BOTH));

        $result['address1'] = $addressParts[0] ?? '';
        $result['address2'] = $addressParts[1] ?? '';
        $result['address3'] = $addressParts[2] ?? '';

        if ($result['state'] === '' && $result['state_code'] === '') {
            $result['city'] = '';
            $result['city_code'] = '';
        }

        return $result;
    }

    /**
     * Find the index of the part that contains the given state name,
     * searching from the end because the state usually closes the address.
     */
    private function findPartIndex(array $parts, string $stateName): ?int
    {
        $needle = strtoupper(preg_replace('/[^A-Za-z ]/', '', $stateName) ?? '');
        for ($i = count($parts) - 1; $i >= 0; $i--) {
            $haystack = strtoupper(preg_replace('/[^A-Za-z ]/', '', $parts[$i]) ?? '');
            if ($haystack !== '' && (str_contains($haystack, $needle) || str_contains($needle, $haystack))) {
                return $i;
            }
        }

        return null;
    }

    /**
     * GST state code => [state name, two-letter code].
     */
    private function states(): array
    {
        return [
            '01' => ['Jammu And Kashmir', 'JK'],
            '02' => ['Himachal Pradesh', 'HP'],
            '03' => ['Punjab', 'PB'],
            '04' => ['Chandigarh', 'CH'],
            '05' => ['Uttarakhand', 'UK'],
            '06' => ['Haryana', 'HR'],
            '07' => ['Delhi', 'DL'],
            '08' => ['Rajasthan', 'RJ'],
            '09' => ['Uttar Pradesh', 'UP'],
            '10' => ['Bihar', 'BR'],
            '11' => ['Sikkim', 'SK'],
            '12' => ['Arunachal Pradesh', 'AR'],
            '13' => ['Nagaland', 'NL'],
            '14' => ['Manipur', 'MN'],
            '15' => ['Mizoram', 'MZ'],
            '16' => ['Tripura', 'TR'],
            '17' => ['Meghalaya', 'ML'],
            '18' => ['Assam', 'AS'],
            '19' => ['West Bengal', 'WB'],
            '20' => ['Jharkhand', 'JH'],
            '21' => ['Odisha', 'OD'],
            '22' => ['Chhattisgarh', 'CT'],
            '23' => ['Madhya Pradesh', 'MP'],
            '24' => ['Gujarat', 'GJ'],
            '26' => ['Dadra And Nagar Haveli And Daman And Diu', 'DN'],
            '27' => ['Maharashtra', 'MH'],
            '29' => ['Karnataka', 'KA'],
            '30' => ['Goa', 'GA'],
            '31' => ['Lakshadweep', 'LD'],
            '32' => ['Kerala', 'KL'],
            '33' => ['Tamil Nadu', 'TN'],
            '34' => ['Puducherry', 'PY'],
            '35' => ['Andaman And Nicobar Islands', 'AN'],
            '36' => ['Telangana', 'TG'],
            '37' => ['Andhra Pradesh', 'AP'],
            '38' => ['Ladakh', 'LA'],
        ];
    }

    private function errorMessage(mixed $responseData, int $status): string
    {
        if (is_array($responseData)) {
            $description = $responseData['message']
                ?? $responseData['Message']
                ?? $responseData['error']
                ?? $responseData['Description']
                ?? null;

            if (is_scalar($description) && trim((string) $description) !== '') {
                return 'Adomantra customer request failed: ' . trim((string) $description);
            }
        }

        return 'Adomantra customer request failed with HTTP status ' . $status . '.';
    }
}