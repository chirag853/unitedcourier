<?php

namespace App\Http\Controllers;

use App\Models\BusinessCategory;
use App\Models\CsbForm;
use App\Models\Customer;
use App\Models\ExporterCustomer;
use App\Models\KycDetail;
use App\Models\KycDraft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KycController extends Controller
{
    public function saveKycDraft(Request $request)
    {
        $customer = auth()->guard('customer')->user();
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to save KYC progress.',
            ], 401);
        }

        $validated = $request->validate([
            'kyc_type' => ['required', Rule::in(['personal', 'business'])],
            'current_step' => ['required', 'integer', 'min:1', 'max:7'],
            'form_data' => ['nullable', 'array'],
        ]);

        $maxStep = $validated['kyc_type'] === 'business' ? 6 : 7;
        if ((int) $validated['current_step'] > $maxStep) {
            return response()->json([
                'success' => false,
                'message' => 'The selected KYC step is invalid.',
            ], 422);
        }

        $allowedFields = [
            'gst_number', 'gst_business_name', 'gst_verified', 'otp_verified', 'aadhar_number',
            'aadhar_verified', 'aadhar_address', 'pan_number', 'pan_holder_name',
            'pan_dob', 'pan_verified', 'organization_name', 'authorized_signatory',
            'billing_address', 'billing_gst', 'billing_contact', 'billing_email',
            'terms_accepted', 'is_csb_v', 'is_gst', 'is_lut',
            'gst_certificate_number', 'gst_certificate_verified', 'iec_number',
            'ad_code', 'lut_expiry_date', 'lut_bond_year', 'bank_account_number',
            'bank_type',
        ];
        $formData = array_intersect_key(
            $validated['form_data'] ?? [],
            array_flip($allowedFields)
        );

        $draft = KycDraft::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'kyc_type' => $validated['kyc_type'],
            ],
            [
                'current_step' => (int) $validated['current_step'],
                'form_data' => $formData,
            ]
        );

        return response()->json([
            'success' => true,
            'saved_at' => $draft->updated_at?->toIso8601String(),
        ]);
    }

    /**
     * Return chart data for the customer dashboard via AJAX.
     * Supports date filters: today, yesterday, this_month, last_month, last_year
     */

    public function kycSubmit(Request $request)
    {
        try {
            // Normalize boolean-ish fields that arrive as strings via FormData
            foreach (['gst_verified', 'otp_verified', 'aadhar_verified', 'pan_verified', 'terms_accepted'] as $boolField) {
                if ($request->has($boolField)) {
                    $val = $request->input($boolField);
                    if (is_string($val)) {
                        $request->merge([$boolField => in_array(strtolower($val), ['1', 'true', 'yes', 'on'], true)]);
                    }
                }
            }

            // Validate the request (text fields + file fields)
            $validated = $request->validate([
                'gst_number' => 'nullable|string|size:15|regex:/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/',
                'gst_business_name' => 'required_with:gst_number|string|max:255',
                'gst_certificate_document' => 'required_with:gst_number|file|mimes:pdf|max:5120',
                'gst_verified' => 'nullable|boolean',
                'otp_verified' => 'nullable|boolean',
                'aadhar_number' => 'nullable|string|max:20',
                'aadhar_verified' => 'nullable|boolean',
                'aadhar_address' => 'nullable|string|max:1000',
                'pan_number' => 'nullable|string|max:20',
                'pan_holder_name' => 'nullable|string|max:255',
                'pan_dob' => 'nullable|date|before:today',
                'pan_verified' => 'nullable|boolean',
                'organization_name' => 'nullable|string|max:255',
                'authorized_signatory' => 'nullable|string|max:255',
                'billing_address' => 'nullable|string|max:1000',
                'billing_gst' => 'nullable|string|max:15',
                'billing_contact' => 'nullable|string|max:20',
                'billing_email' => 'nullable|string|email|max:255',
                'terms_accepted' => 'nullable|boolean',
                'terms_accepted_at' => 'nullable|date',
                // File uploads
                'aadhar_front_document' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                'aadhar_back_document' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                'pan_document' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                'signature_document' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ], [
                'gst_number.regex' => 'The GST number format is invalid. It must be a valid 15-character GSTIN (e.g. 22AAAAA0000A1Z5).',
                'gst_number.size' => 'The GST number must be exactly 15 characters.',
                'gst_certificate_document.required_with' => 'Upload the GST Certificate PDF when submitting GST details.',
                'gst_certificate_document.mimes' => 'The GST Certificate must be a PDF file only.',
                'gst_certificate_document.max' => 'The GST Certificate PDF must not exceed 5 MB.',
            ]);

            // Get current customer
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to submit your KYC application.'
                ], 401);
            }

            $gstNumber = $request->gst_number
                ? strtoupper(preg_replace('/\s+/', '', $request->gst_number))
                : null;
            $gstBusinessName = trim((string) $request->input('gst_business_name'));

            if ($gstNumber && (
                !$request->boolean('gst_verified')
                || session('kyc_gst_number') !== $gstNumber
                || !session('kyc_gst_cashfree_verified')
                || !hash_equals(
                    (string) session('kyc_gst_business_name', ''),
                    $this->normalizeGstBusinessName($gstBusinessName)
                )
            )) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verify the submitted GSTIN through Cashfree before submitting KYC.',
                ], 422);
            }

            $aadharNumber = $request->aadhar_number
                ? preg_replace('/\s+/', '', $request->aadhar_number)
                : null;
            $panNumber = $request->pan_number
                ? strtoupper(preg_replace('/\s+/', '', $request->pan_number))
                : null;
            $panHolderName = $this->normalizePanHolderName((string) $request->input('pan_holder_name'));
            $panDob = $this->normalizePanDob((string) $request->input('pan_dob'));
            $hasAnyPanData = $panNumber
                || $panHolderName
                || $panDob
                || $request->boolean('pan_verified')
                || $request->hasFile('pan_document');

            if ($hasAnyPanData) {
                $panFile = $request->file('pan_document');
                $panDocumentHash = $panFile
                    ? hash_file('sha256', $panFile->getRealPath())
                    : null;
                if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', (string) $panNumber)
                    || $panHolderName === ''
                    || $panDob === null
                    || !$request->boolean('pan_verified')
                    || !$panFile
                    || !session('kyc_pan_cashfree_verified')
                    || session('kyc_pan_number') !== $panNumber
                    || !hash_equals((string) session('kyc_pan_holder_name', ''), $panHolderName)
                    || !hash_equals((string) session('kyc_pan_dob', ''), $panDob)
                    || !hash_equals(
                        (string) session('kyc_pan_document_hash', ''),
                        (string) $panDocumentHash
                    )) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Verify the submitted PAN number, holder name, date of birth, and selected PAN image through Cashfree before submitting KYC.',
                    ], 422);
                }
            }

            $isAadhaarOptional = $this->isCourierOrAggregator($customer);
            $hasAnyAadhaarData = $aadharNumber
                || $request->boolean('aadhar_verified')
                || $request->hasFile('aadhar_front_document')
                || $request->hasFile('aadhar_back_document');

            if (!$isAadhaarOptional || $hasAnyAadhaarData) {
                $aadharFrontFile = $request->file('aadhar_front_document');
                $aadharFrontHash = $aadharFrontFile
                    ? hash_file('sha256', $aadharFrontFile->getRealPath())
                    : null;
                if (!preg_match('/^[2-9][0-9]{11}$/', (string) $aadharNumber)
                    || !$request->boolean('aadhar_verified')
                    || !$aadharFrontFile
                    || !$request->hasFile('aadhar_back_document')
                    || !session('kyc_aadhar_cashfree_verified')
                    || session('kyc_aadhar_number') !== $aadharNumber
                    || !hash_equals(
                        (string) session('kyc_aadhar_front_hash', ''),
                        (string) $aadharFrontHash
                    )) {
                    return response()->json([
                        'success' => false,
                        'message' => $isAadhaarOptional
                            ? 'Complete Cashfree Aadhaar verification with the selected front image and upload both Aadhaar images, or leave all Aadhaar fields empty.'
                            : 'Cashfree verification of the submitted Aadhaar number and front image, plus both Aadhaar images, is required before submitting KYC.'
                    ], 422);
                }
            }

            if ($identifierConflict = $this->findKycIdentifierConflict($customer, $gstNumber, $aadharNumber, $panNumber)) {
                return $this->kycIdentifierConflictResponse($identifierConflict);
            }

            // A sole proprietorship and several other business structures use the
            // proprietor's individual PAN. PAN category is therefore not rejected
            // here; Cashfree verification and the duplicate-identifier check still
            // validate ownership and prevent reuse by another customer.

            // Ensure upload directories exist
            $uploadDirs = [
                'uploads/gst_certificate_documents',
                'uploads/aadhar_front_documents',
                'uploads/aadhar_back_documents',
                'uploads/pan_documents',
                'uploads/signature_documents',
            ];
            foreach ($uploadDirs as $dir) {
                $path = public_path($dir);
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
            }

            // Handle GST Certificate upload
            $gstCertificatePath = null;
            if ($request->hasFile('gst_certificate_document')) {
                $file = $request->file('gst_certificate_document');
                $filename = time() . '_gst_certificate_' . Str::uuid()
                    . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/gst_certificate_documents'), $filename);
                $gstCertificatePath = 'uploads/gst_certificate_documents/' . $filename;
            }

            // Handle Aadhaar front document upload
            $aadharFrontPath = null;
            if ($request->hasFile('aadhar_front_document')) {
                $file = $request->file('aadhar_front_document');
                $filename = time() . '_aadhar_front_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aadhar_front_documents'), $filename);
                $aadharFrontPath = 'uploads/aadhar_front_documents/' . $filename;
            }

            // Handle Aadhaar back document upload
            $aadharBackPath = null;
            if ($request->hasFile('aadhar_back_document')) {
                $file = $request->file('aadhar_back_document');
                $filename = time() . '_aadhar_back_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aadhar_back_documents'), $filename);
                $aadharBackPath = 'uploads/aadhar_back_documents/' . $filename;
            }

            // Handle PAN document upload
            $panDocumentPath = null;
            if ($request->hasFile('pan_document')) {
                $file = $request->file('pan_document');
                $filename = time() . '_pan_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pan_documents'), $filename);
                $panDocumentPath = 'uploads/pan_documents/' . $filename;
            }

            // Store the uploaded image and persist only its relative path.
            $file = $request->file('signature_document');
            $filename = time() . '_signature_' . \Illuminate\Support\Str::uuid()
                . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/signature_documents'), $filename);
            $signaturePath = 'uploads/signature_documents/' . $filename;

            // Prepare KYC data
            $kycData = [
                'customer_id' => $customer->id,
                'kyc_type' => 'personal',
                'gst_number' => $gstNumber,
                'gst_certificate_document' => $gstCertificatePath,
                'gst_verified' => $request->gst_verified ?? false,
                'otp_verified' => $request->otp_verified ?? false,
                'aadhar_number' => $aadharNumber,
                'aadhar_verified' => $request->aadhar_verified ?? false,
                'aadhar_address' => $request->aadhar_address,
                'aadhar_front_document' => $aadharFrontPath,
                'aadhar_back_document' => $aadharBackPath,
                'pan_number' => $panNumber,
                'pan_holder_name' => $request->pan_holder_name,
                'pan_dob' => $request->pan_dob,
                'pan_document' => $panDocumentPath,
                'pan_verified' => $request->pan_verified ?? false,
                'signature_document' => $signaturePath,
                'signature' => null,
                'organization_name' => $request->organization_name,
                'authorized_signatory' => $request->authorized_signatory,
                'billing_address' => $request->billing_address,
                'billing_gst' => $request->billing_gst,
                'billing_contact' => $request->billing_contact,
                'billing_email' => $request->billing_email,
                'terms_accepted' => $request->terms_accepted ?? true,
                'terms_accepted_at' => now(),
                'kyc_status' => 'pending', // Set status to under_review after submission
            ];

            // Create KYC record
            $kyc = KycDetail::create($kycData);
            KycDraft::where('customer_id', $customer->id)
                ->where('kyc_type', 'personal')
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'KYC application submitted successfully! Your application is now under review.',
                'kyc_id' => $kyc->id
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('KYC submit database error: ' . $e->getMessage());
            if ($e->getCode() === '23000' && ($identifier = $this->databaseKycIdentifierFromException($e))) {
                return $this->kycIdentifierConflictResponse($identifier);
            }

            return response()->json([
                'success' => false,
                'message' => 'KYC submission failed. Please try again.'
            ], 500);
        } catch (\Throwable $e) {
            \Log::error('KYC submit error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'KYC submission failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify GSTIN through Cashfree during either KYC flow.
     */
    public function verifyGst(Request $request)
    {
        try {
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to verify your GST number.',
                ], 401);
            }

            $validated = $request->validate([
                'gst_number' => ['required', 'string', 'size:15'],
                'business_name' => ['required', 'string', 'max:255'],
                'gst_certificate_document' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            ], [
                'gst_certificate_document.required' => 'Upload the GST Certificate PDF before verification.',
                'gst_certificate_document.mimes' => 'The GST Certificate must be a PDF file only.',
                'gst_certificate_document.max' => 'The GST Certificate PDF must not exceed 5 MB.',
            ]);

            $gst = strtoupper(preg_replace('/\s+/', '', $validated['gst_number']));
            if (!preg_match('/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', $gst)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid GST number format. Please enter a valid 15-character GSTIN.',
                ], 422);
            }

            $stateCode = (int) substr($gst, 0, 2);
            if ($stateCode < 1 || $stateCode > 38) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid state code in GST number. State code must be between 01 and 38.',
                ], 422);
            }

            if ($this->computeGstChecksum(substr($gst, 0, 14)) !== substr($gst, 14, 1)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid GST number. The checksum digit does not match.',
                ], 422);
            }

            if ($identifierConflict = $this->findKycIdentifierConflict($customer, $gst, null, null)) {
                return $this->kycIdentifierConflictResponse($identifierConflict);
            }

            $businessName = trim($validated['business_name']);

            $clientId = config('services.cashfree.verification_client_id');
            $clientSecret = config('services.cashfree.verification_client_secret');
            if (!$clientId || !$clientSecret) {
                \Log::error('Cashfree GST verification credentials are not configured.');
                return response()->json([
                    'success' => false,
                    'message' => 'GST verification is temporarily unavailable.',
                ], 503);
            }

            $cashfreeResponse = Http::acceptJson()
                ->withHeaders([
                    'x-client-id' => $clientId,
                    'x-client-secret' => $clientSecret,
                    'Content-Type' => 'application/json',
                ])
                ->timeout((int) config('services.cashfree.verification_timeout', 30))
                ->post(rtrim(config('services.cashfree.verification_base_url'), '/') . '/gstin', [
                    'GSTIN' => $gst,
                    'business_name' => $businessName,
                ]);

            $cashfreeData = $cashfreeResponse->json();
            if (!$cashfreeResponse->successful()) {
                \Log::warning('Cashfree GST verification rejected.', [
                    'customer_id' => $customer->id,
                    'http_status' => $cashfreeResponse->status(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not verify this GSTIN.',
                ], 422);
            }

            $providerStatus = strtoupper((string) (
                data_get($cashfreeData, 'verification_status')
                ?? data_get($cashfreeData, 'status')
                ?? data_get($cashfreeData, 'status_code')
                ?? ''
            ));
            $providerValid = data_get($cashfreeData, 'valid');
            $providerSuccess = data_get($cashfreeData, 'success');
            if (in_array($providerStatus, ['FAILED', 'FAILURE', 'INVALID', 'ERROR'], true)
                || $providerValid === false
                || $providerSuccess === false) {
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not verify this GSTIN.',
                ], 422);
            }

            $legalName = trim((string) (
                data_get($cashfreeData, 'legal_name_of_business')
                ?? data_get($cashfreeData, 'data.legal_name_of_business')
                ?? data_get($cashfreeData, 'legal_name')
                ?? data_get($cashfreeData, 'data.legal_name')
                ?? ''
            ));
            $tradeName = trim((string) (
                data_get($cashfreeData, 'trade_name_of_business')
                ?? data_get($cashfreeData, 'data.trade_name_of_business')
                ?? data_get($cashfreeData, 'trade_name')
                ?? data_get($cashfreeData, 'data.trade_name')
                ?? ''
            ));
            $normalizedBusinessName = $this->normalizeGstBusinessName($businessName);
            $providerNames = array_values(array_filter([$legalName, $tradeName]));
            $nameMatches = collect($providerNames)->contains(
                fn (string $providerName) => hash_equals(
                    $this->normalizeGstBusinessName($providerName),
                    $normalizedBusinessName
                )
            );

            if (!$providerNames) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cashfree did not return the registered business name for this GSTIN.',
                ], 422);
            }

            if (!$nameMatches) {
                return response()->json([
                    'success' => false,
                    'message' => 'The entered Business Name does not match the name registered for this GSTIN.',
                ], 422);
            }

            session([
                'kyc_gst_number' => $gst,
                'kyc_gst_business_name' => $normalizedBusinessName,
                'kyc_gst_verified' => true,
                'kyc_gst_cashfree_verified' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'GST number and Business Name verified successfully through Cashfree.',
                'gst_number' => $gst,
                'business_name' => $legalName ?: $tradeName,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::warning('Cashfree GST verification connection failed.', [
                'message' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to reach Cashfree for GST verification. Please try again.',
            ], 503);
        } catch (\Throwable $e) {
            \Log::error('GST verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'GST verification failed. Please try again.',
            ], 500);
        }
    }

    private function normalizeGstBusinessName(string $name): string
    {
        $asciiName = Str::ascii($name);

        return strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', $asciiName) ?? '');
    }

    private function normalizePanHolderName(string $name): string
    {
        $asciiName = Str::ascii($name);

        return strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', $asciiName) ?? '');
    }

    private function normalizePanDob(string $dob): ?string
    {
        $dob = trim($dob);
        if ($dob === '') {
            return null;
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y'] as $format) {
            $date = \DateTimeImmutable::createFromFormat('!' . $format, $dob);
            $errors = \DateTimeImmutable::getLastErrors();
            if ($date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * Compute the GSTIN checksum digit using the official algorithm.
     * Based on the GSTN checksum formula.
     *
     * @param string $gst14  The first 14 characters of the GSTIN
     * @return string         The computed checksum character (0-9 or A-Z)
     */
    private function computeGstChecksum(string $gst14): string
    {
        // Official GSTIN checksum (Luhn mod 36):
        //  - Iterate RIGHT-TO-LEFT over the 14 characters
        //  - Factor starts at 2 for the rightmost char, alternating 2,1,2,1,...
        //  - Each char value: 0-9 = 0-9, A-Z = 10-35
        //  - product = value * factor; reduce via base-36 digit sum: floor(product/36) + (product%36)
        //  - checksum = (36 - (sum % 36)) % 36
        $sum = 0;
        $factor = 2;

        for ($i = 13; $i >= 0; $i--) {
            $char = $gst14[$i];
            // Convert character to its numeric value (0-35): 0-9 = 0-9, A-Z = 10-35
            $ascii = ord($char);
            $value = ($ascii >= 48 && $ascii <= 57) ? ($ascii - 48) : ($ascii - 55);

            $product = $value * $factor;
            // Base-36 digit reduction (NOT decimal digit-summing)
            $sum += intdiv($product, 36) + ($product % 36);

            // Alternate factor: 2,1,2,1,...
            $factor = ($factor === 2) ? 1 : 2;
        }

        $remainder = $sum % 36;
        $checksumValue = (36 - $remainder) % 36;

        // Convert back to character: 0-9 = '0'-'9', 10-35 = 'A'-'Z'
        return ($checksumValue < 10)
            ? (string) $checksumValue
            : chr($checksumValue + 55);
    }

    /**
     * Determine whether the customer's selected business category is a business account.
     */
    private function isBusinessCustomer(Customer $customer): bool
    {
        $businessCategory = BusinessCategory::find($customer->business_category_id);

        return $businessCategory && strcasecmp((string) $businessCategory->user_type, 'Business') === 0;
    }

    /**
     * PAN entity code P identifies an individual PAN.
     */
    private function isIndividualPan(string $pan): bool
    {
        return strtoupper(substr($pan, 3, 1)) === 'P';
    }

    /**
     * Check GST, Aadhaar, and PAN across personal and business KYC storage.
     */
    private function findKycIdentifierConflict(Customer $customer, ?string $gst, ?string $aadhar, ?string $pan): ?string
    {
        if ($gst && (KycDetail::whereRaw("UPPER(REPLACE(REPLACE(REPLACE(gst_number, ' ', ''), CHAR(9), ''), CHAR(10), '')) = ?", [$gst])
            ->where('customer_id', '!=', $customer->id)
            ->exists()
            || CsbForm::whereRaw("UPPER(REPLACE(REPLACE(REPLACE(gst_certificate_number, ' ', ''), CHAR(9), ''), CHAR(10), '')) = ?", [$gst])
                ->where('customer_id', '!=', $customer->id)
                ->exists())) {
            return 'gst';
        }

        if ($aadhar && (Customer::whereRaw("REPLACE(REPLACE(REPLACE(aadhar_number, ' ', ''), CHAR(9), ''), CHAR(10), '') = ?", [$aadhar])
            ->where('id', '!=', $customer->id)
            ->exists()
            || KycDetail::whereRaw("REPLACE(REPLACE(REPLACE(aadhar_number, ' ', ''), CHAR(9), ''), CHAR(10), '') = ?", [$aadhar])
                ->where('customer_id', '!=', $customer->id)
                ->exists()
            || CsbForm::whereRaw("REPLACE(REPLACE(REPLACE(aadhar_number, ' ', ''), CHAR(9), ''), CHAR(10), '') = ?", [$aadhar])
                ->where('customer_id', '!=', $customer->id)
                ->exists())) {
            return 'aadhar';
        }

        if ($pan && (Customer::whereRaw("UPPER(REPLACE(REPLACE(REPLACE(pan_number, ' ', ''), CHAR(9), ''), CHAR(10), '')) = ?", [$pan])
            ->where('id', '!=', $customer->id)
            ->exists()
            || KycDetail::whereRaw("UPPER(REPLACE(REPLACE(REPLACE(pan_number, ' ', ''), CHAR(9), ''), CHAR(10), '')) = ?", [$pan])
                ->where('customer_id', '!=', $customer->id)
                ->exists())) {
            return 'pan';
        }

        return null;
    }

    private function databaseKycIdentifierFromException(\Illuminate\Database\QueryException $exception): ?string
    {
        $message = strtolower($exception->getMessage());

        foreach (['gst' => ['gst_number', 'gst_certificate_number'], 'aadhar' => ['aadhar_number'], 'pan' => ['pan_number']] as $identifier => $columns) {
            foreach ($columns as $column) {
                if (str_contains($message, $column)) {
                    return $identifier;
                }
            }
        }

        return null;
    }

    private function kycIdentifierConflictResponse(string $identifier)
    {
        $messages = [
            'gst' => 'This GST number is already registered with another account.',
            'aadhar' => 'This Aadhaar number is already registered with another account.',
            'pan' => 'This PAN number is already registered with another account.',
        ];

        return response()->json([
            'success' => false,
            'message' => $messages[$identifier] ?? 'This KYC number is already registered with another account.',
        ], 409);
    }

    /**
     * Verify an Aadhaar front image through Cashfree Bharat OCR during KYC.
     */
    public function verifyAadhar(Request $request)
    {
        try {
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to verify your Aadhaar.',
                ], 401);
            }

            session()->forget([
                'kyc_aadhar_number',
                'kyc_aadhar_verified',
                'kyc_aadhar_cashfree_verified',
                'kyc_aadhar_front_hash',
                'kyc_aadhar_verification_id',
            ]);

            $validated = $request->validate([
                'aadhar_number' => ['required', 'string', 'regex:/^[2-9][0-9]{11}$/'],
                'aadhar_front_document' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            ], [
                'aadhar_front_document.required' => 'Upload the Aadhaar front image before verification.',
                'aadhar_front_document.mimes' => 'The Aadhaar front document must be a JPG, JPEG, or PNG image.',
                'aadhar_front_document.max' => 'The Aadhaar front image must not exceed 5 MB.',
            ]);

            $aadhar = preg_replace('/\s+/', '', $validated['aadhar_number']);
            if ($identifierConflict = $this->findKycIdentifierConflict($customer, null, $aadhar, null)) {
                return $this->kycIdentifierConflictResponse($identifierConflict);
            }

            $clientId = config('services.cashfree.verification_client_id');
            $clientSecret = config('services.cashfree.verification_client_secret');
            if (!$clientId || !$clientSecret) {
                \Log::error('Cashfree Aadhaar OCR credentials are not configured.');
                return response()->json([
                    'success' => false,
                    'message' => 'Aadhaar verification is temporarily unavailable.',
                ], 503);
            }

            $frontFile = $request->file('aadhar_front_document');
            $verificationId = (string) random_int(1000, 9999);
            $fileStream = fopen($frontFile->getRealPath(), 'r');
            if ($fileStream === false) {
                throw new \RuntimeException('Unable to read the uploaded Aadhaar front image.');
            }

            try {
                $cashfreeResponse = Http::acceptJson()
                    ->withHeaders([
                        'x-client-id' => $clientId,
                        'x-client-secret' => $clientSecret,
                        'x-api-version' => '2024-12-01',
                    ])
                    ->attach('file', $fileStream, $frontFile->getClientOriginalName())
                    ->timeout((int) config('services.cashfree.verification_timeout', 30))
                    ->post(rtrim(config('services.cashfree.verification_base_url'), '/') . '/bharat-ocr', [
                        'verification_id' => $verificationId,
                        'document_type' => 'AADHAAR',
                        'do_verification' => 'false',
                    ]);
            } finally {
                fclose($fileStream);
            }

            $cashfreeData = $cashfreeResponse->json();
            if (!$cashfreeResponse->successful()) {
                \Log::warning('Cashfree Aadhaar OCR rejected.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                    'http_status' => $cashfreeResponse->status(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not read this Aadhaar image.',
                ], 422);
            }

            $providerStatus = strtoupper((string) (
                data_get($cashfreeData, 'verification_status')
                ?? data_get($cashfreeData, 'status')
                ?? data_get($cashfreeData, 'status_code')
                ?? ''
            ));
            if (in_array($providerStatus, ['FAILED', 'FAILURE', 'INVALID', 'ERROR'], true)
                || data_get($cashfreeData, 'success') === false) {
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not read this Aadhaar image.',
                ], 422);
            }

            $ocrAadhaar = preg_replace(
                '/\D+/',
                '',
                (string) data_get($cashfreeData, 'document_fields.uid', '')
            );

            if (!preg_match('/^[2-9][0-9]{11}$/', $ocrAadhaar)) {
                \Log::warning('Cashfree Aadhaar OCR response did not contain a valid document_fields.uid.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Cashfree could not read a valid Aadhaar number from the uploaded image.',
                ], 422);
            }

            if (!hash_equals($aadhar, $ocrAadhaar)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The Aadhaar number entered does not match the UID read from the uploaded Aadhaar image.',
                ], 422);
            }

            $frontHash = hash_file('sha256', $frontFile->getRealPath());
            session([
                'kyc_aadhar_number' => $aadhar,
                'kyc_aadhar_verified' => true,
                'kyc_aadhar_cashfree_verified' => true,
                'kyc_aadhar_front_hash' => $frontHash,
                'kyc_aadhar_verification_id' => $verificationId,
            ]);

            $customer->aadhar_number = $aadhar;
            $customer->aadhar_verified = true;
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Aadhaar document verified successfully',
                'aadhar_number' => $aadhar,
                'verification_id' => $verificationId,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Aadhaar verification database error: ' . $e->getMessage());
            if ($e->getCode() === '23000' && str_contains(strtolower($e->getMessage()), 'aadhar_number')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This Aadhaar number is already registered with another account.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Aadhaar verification failed. Please try again.',
            ], 500);
        } catch (\Throwable $e) {
            \Log::error('Aadhaar verification error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Aadhaar verification failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify an Aadhaar front image through Cashfree Bharat OCR for the
     * Add Customer (exporter-customers) page. Unlike the KYC verifyAadhar,
     * the duplication check runs only against the logged-in exporter's own
     * saved customers (exporter_customers) and does not write to the
     * Customer record.
     */
    public function verifyExporterCustomerAadhar(Request $request)
    {
        try {
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to verify your Aadhaar.',
                ], 401);
            }

            session()->forget([
                'kyc_aadhar_number',
                'kyc_aadhar_verified',
                'kyc_aadhar_cashfree_verified',
                'kyc_aadhar_front_hash',
                'kyc_aadhar_verification_id',
            ]);

            $validated = $request->validate([
                'aadhar_number' => ['required', 'string', 'regex:/^[2-9][0-9]{11}$/'],
                'aadhar_front_document' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            ], [
                'aadhar_front_document.required' => 'Upload the Aadhaar front image before verification.',
                'aadhar_front_document.mimes' => 'The Aadhaar front document must be a JPG, JPEG, or PNG image.',
                'aadhar_front_document.max' => 'The Aadhaar front image must not exceed 5 MB.',
            ]);

            $aadhar = preg_replace('/\s+/', '', $validated['aadhar_number']);

            // Duplication is checked only within this exporter's own saved customers.
            if (ExporterCustomer::query()
                ->where('exporter_id', $customer->id)
                ->where('kyc_type', 'Aadhar Card')
                ->where('kyc_number', $aadhar)
                ->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This Aadhaar number is already registered for another saved customer.',
                ], 409);
            }

            $clientId = config('services.cashfree.verification_client_id');
            $clientSecret = config('services.cashfree.verification_client_secret');
            if (!$clientId || !$clientSecret) {
                \Log::error('Cashfree Aadhaar OCR credentials are not configured.');
                return response()->json([
                    'success' => false,
                    'message' => 'Aadhaar verification is temporarily unavailable.',
                ], 503);
            }

            $frontFile = $request->file('aadhar_front_document');
            $verificationId = (string) random_int(1000, 9999);
            $fileStream = fopen($frontFile->getRealPath(), 'r');
            if ($fileStream === false) {
                throw new \RuntimeException('Unable to read the uploaded Aadhaar front image.');
            }

            try {
                $cashfreeResponse = Http::acceptJson()
                    ->withHeaders([
                        'x-client-id' => $clientId,
                        'x-client-secret' => $clientSecret,
                        'x-api-version' => '2024-12-01',
                    ])
                    ->attach('file', $fileStream, $frontFile->getClientOriginalName())
                    ->timeout((int) config('services.cashfree.verification_timeout', 30))
                    ->post(rtrim(config('services.cashfree.verification_base_url'), '/') . '/bharat-ocr', [
                        'verification_id' => $verificationId,
                        'document_type' => 'AADHAAR',
                        'do_verification' => 'false',
                    ]);
            } finally {
                fclose($fileStream);
            }

            $cashfreeData = $cashfreeResponse->json();
            if (!$cashfreeResponse->successful()) {
                \Log::warning('Cashfree Aadhaar OCR rejected.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                    'http_status' => $cashfreeResponse->status(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not read this Aadhaar image.',
                ], 422);
            }

            $providerStatus = strtoupper((string) (
                data_get($cashfreeData, 'verification_status')
                ?? data_get($cashfreeData, 'status')
                ?? data_get($cashfreeData, 'status_code')
                ?? ''
            ));
            if (in_array($providerStatus, ['FAILED', 'FAILURE', 'INVALID', 'ERROR'], true)
                || data_get($cashfreeData, 'success') === false) {
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not read this Aadhaar image.',
                ], 422);
            }

            $ocrAadhaar = preg_replace(
                '/\D+/',
                '',
                (string) data_get($cashfreeData, 'document_fields.uid', '')
            );

            if (!preg_match('/^[2-9][0-9]{11}$/', $ocrAadhaar)) {
                \Log::warning('Cashfree Aadhaar OCR response did not contain a valid document_fields.uid.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Cashfree could not read a valid Aadhaar number from the uploaded image.',
                ], 422);
            }

            if (!hash_equals($aadhar, $ocrAadhaar)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The Aadhaar number entered does not match the UID read from the uploaded Aadhaar image.',
                ], 422);
            }

            $frontHash = hash_file('sha256', $frontFile->getRealPath());
            session([
                'kyc_aadhar_number' => $aadhar,
                'kyc_aadhar_verified' => true,
                'kyc_aadhar_cashfree_verified' => true,
                'kyc_aadhar_front_hash' => $frontHash,
                'kyc_aadhar_verification_id' => $verificationId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Aadhaar document verified successfully',
                'aadhar_number' => $aadhar,
                'verification_id' => $verificationId,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('Exporter customer Aadhaar verification error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Aadhaar verification failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify a PAN number and its image through Cashfree Bharat OCR.
     */
    public function verifyPan(Request $request)
    {
        try {
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to verify your PAN.',
                ], 401);
            }

            session()->forget([
                'kyc_pan_number',
                'kyc_pan_holder_name',
                'kyc_pan_dob',
                'kyc_pan_verified',
                'kyc_pan_cashfree_verified',
                'kyc_pan_document_hash',
                'kyc_pan_verification_id',
            ]);

            $validated = $request->validate([
                'pan_number' => ['required', 'string', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/'],
                'pan_holder_name' => ['required', 'string', 'max:255'],
                'pan_dob' => ['required', 'date', 'before:today'],
                'pan_document' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            ], [
                'pan_document.required' => 'Upload the PAN image before verification.',
                'pan_document.mimes' => 'The PAN document must be a JPG, JPEG, or PNG image.',
                'pan_document.max' => 'The PAN image must not exceed 5 MB.',
            ]);

            $pan = strtoupper(preg_replace('/[^A-Z0-9]+/i', '', $validated['pan_number']));
            $panHolderName = $this->normalizePanHolderName($validated['pan_holder_name']);
            $panDob = $this->normalizePanDob($validated['pan_dob']);
            if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $pan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PAN number. It must be 10 characters: 5 letters, 4 digits, and 1 letter (e.g. ABCDE1234F).',
                ], 422);
            }

            // Business accounts may legitimately use the proprietor's individual
            // PAN (for example, a sole proprietorship). Keep PAN format, OCR,
            // holder-name, DOB, and uniqueness validation below instead of
            // rejecting the PAN based only on its fourth character.

            if ($identifierConflict = $this->findKycIdentifierConflict($customer, null, null, $pan)) {
                return $this->kycIdentifierConflictResponse($identifierConflict);
            }

            $clientId = config('services.cashfree.verification_client_id');
            $clientSecret = config('services.cashfree.verification_client_secret');
            if (!$clientId || !$clientSecret) {
                \Log::error('Cashfree PAN OCR credentials are not configured.');
                return response()->json([
                    'success' => false,
                    'message' => 'PAN verification is temporarily unavailable.',
                ], 503);
            }

            $panFile = $validated['pan_document'];
            $fileStream = fopen($panFile->getRealPath(), 'r');
            if ($fileStream === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'The PAN image could not be read. Please upload it again.',
                ], 422);
            }

            $verificationId = (string) random_int(1000, 9999);
            try {
                $cashfreeResponse = Http::acceptJson()
                    ->withHeaders([
                        'x-client-id' => $clientId,
                        'x-client-secret' => $clientSecret,
                        'x-api-version' => '2024-12-01',
                    ])
                    ->attach('file', $fileStream, $panFile->getClientOriginalName())
                    ->timeout((int) config('services.cashfree.verification_timeout', 30))
                    ->post(rtrim(config('services.cashfree.verification_base_url'), '/') . '/bharat-ocr', [
                        'verification_id' => $verificationId,
                        'document_type' => 'PAN',
                        'do_verification' => 'false',
                    ]);
            } finally {
                fclose($fileStream);
            }

            $cashfreeData = $cashfreeResponse->json();
            if (!$cashfreeResponse->successful()) {
                \Log::warning('Cashfree PAN OCR rejected.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                    'http_status' => $cashfreeResponse->status(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not read this PAN image.',
                ], 422);
            }

            $providerStatus = strtoupper((string) (
                data_get($cashfreeData, 'verification_status')
                ?? data_get($cashfreeData, 'status')
                ?? data_get($cashfreeData, 'status_code')
                ?? ''
            ));
            if (in_array($providerStatus, ['FAILED', 'FAILURE', 'INVALID', 'ERROR'], true)
                || data_get($cashfreeData, 'success') === false) {
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not read this PAN image.',
                ], 422);
            }

            $ocrPan = strtoupper(preg_replace(
                '/[^A-Z0-9]+/',
                '',
                (string) data_get($cashfreeData, 'document_fields.pan', '')
            ));
            $ocrPanHolderNameValue = collect([
                data_get($cashfreeData, 'document_fields.name'),
                data_get($cashfreeData, 'document_fields.pan_name'),
                data_get($cashfreeData, 'document_fields.full_name'),
            ])->first(fn ($value) => is_scalar($value) && trim((string) $value) !== '');
            $ocrPanDobValue = collect([
                data_get($cashfreeData, 'document_fields.dob'),
                data_get($cashfreeData, 'document_fields.date_of_birth'),
            ])->first(fn ($value) => is_scalar($value) && trim((string) $value) !== '');
            $ocrPanHolderName = $this->normalizePanHolderName((string) $ocrPanHolderNameValue);
            $ocrPanDob = $this->normalizePanDob((string) $ocrPanDobValue);
            if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $ocrPan)) {
                \Log::warning('Cashfree PAN OCR response did not contain a valid document_fields.pan.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cashfree could not read a valid PAN number from the uploaded image.',
                ], 422);
            }

            if (!hash_equals($pan, $ocrPan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The PAN number entered does not match the PAN read from the uploaded image.',
                ], 422);
            }

            if ($ocrPanHolderName === '') {
                \Log::warning('Cashfree PAN OCR response did not contain a holder name.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cashfree could not read the holder name from the uploaded PAN image.',
                ], 422);
            }

            if (!hash_equals($panHolderName, $ocrPanHolderName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The PAN holder name entered does not match the name read from the uploaded PAN image.',
                ], 422);
            }

            if ($ocrPanDob === null) {
                \Log::warning('Cashfree PAN OCR response did not contain a valid date of birth.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cashfree could not read a valid date of birth from the uploaded PAN image.',
                ], 422);
            }

            if ($panDob === null || !hash_equals($panDob, $ocrPanDob)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The PAN date of birth entered does not match the date read from the uploaded PAN image.',
                ], 422);
            }

            $documentHash = hash_file('sha256', $panFile->getRealPath());
            session([
                'kyc_pan_number' => $pan,
                'kyc_pan_holder_name' => $panHolderName,
                'kyc_pan_dob' => $panDob,
                'kyc_pan_verified' => true,
                'kyc_pan_cashfree_verified' => true,
                'kyc_pan_document_hash' => $documentHash,
                'kyc_pan_verification_id' => $verificationId,
            ]);

            if (\Schema::hasColumn('customers', 'pan_number')) {
                $customer->pan_number = $pan;
                $customer->pan_verified = true;
                $customer->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'PAN document verified successfully',
                'pan_number' => $pan,
                'verification_id' => $verificationId,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('PAN verification database error: ' . $e->getMessage());
            if ($e->getCode() === '23000' && str_contains(strtolower($e->getMessage()), 'pan_number')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This PAN number is already registered with another account.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'PAN verification failed. Please try again.',
            ], 500);
        } catch (\Throwable $e) {
            \Log::error('PAN verification error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'PAN verification failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Show the Personal KYC (CSB-IV) form.
     */
    public function personalKyc()
    {
        // Check if customer is logged in using auth guard
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();

        // Fetch existing KYC detail (if any) to pre-fill the form
        $kycDetail = KycDetail::where('customer_id', $customer->id)
            ->where('kyc_type', 'personal')
            ->latest()
            ->first();

        // Load the customer's business category to determine user_type (Personal / Business)
        $businessCategory = BusinessCategory::find($customer->business_category_id);
        $userType = $businessCategory ? $businessCategory->user_type : 'Personal';

        return view('customer.kyc-personal', compact('customer', 'kycDetail', 'userType', 'businessCategory'));
    }

    /**
     * Store the Personal KYC (CSB-IV) submission.
     * Handles Aadhaar (front/back), PAN, signature, billing details, and merchant agreement.
     */
    public function storePersonalKyc(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'aadhar_number' => 'required|string|size:12',
                'aadhar_front_document' => 'required|image|mimes:jpg,jpeg,png|max:5120',
                'aadhar_back_document' => 'required|image|mimes:jpg,jpeg,png|max:5120',
                'aadhar_address' => 'required|string|max:1000',
                'pan_number' => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
                'pan_holder_name' => 'required|string|max:255',
                'pan_dob' => 'required|date|before:today',
                'pan_document' => 'required|image|mimes:jpg,jpeg,png|max:5120',
                'signature_document' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'billing_address' => 'required|string|max:1000',
                'billing_contact' => 'required|string|max:20',
                'billing_email' => 'required|email|max:255',
                'merchant_agreement' => 'required|file|mimes:pdf|max:10240',
                'terms_accepted' => 'required|boolean',
            ], [
                'pan_number.regex' => 'The PAN number format is invalid. It must be 5 letters, 4 digits, and 1 letter (e.g. ABCDE1234F).',
                'pan_number.size' => 'The PAN number must be exactly 10 characters.',
                'aadhar_number.size' => 'The Aadhaar number must be exactly 12 digits.',
                'pan_dob.before' => 'The date of birth must be a valid date before today.',
            ]);

            // Get current customer
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to submit personal KYC.'
                ], 401);
            }

            // Basic Aadhaar format validation: 12 digits, not starting with 0 or 1
            $aadhar = preg_replace('/\s+/', '', $request->aadhar_number);
            if (!preg_match('/^[2-9][0-9]{11}$/', $aadhar)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Aadhaar number. It must be 12 digits and cannot start with 0 or 1.'
                ], 422);
            }

            $panNumber = strtoupper(preg_replace('/\s+/', '', $validated['pan_number']));
            $panHolderName = $this->normalizePanHolderName($validated['pan_holder_name']);
            $panDob = $this->normalizePanDob($validated['pan_dob']);
            $panFile = $request->file('pan_document');
            $panDocumentHash = hash_file('sha256', $panFile->getRealPath());

            if (!session('kyc_pan_cashfree_verified')
                || session('kyc_pan_number') !== $panNumber
                || !hash_equals((string) session('kyc_pan_holder_name', ''), $panHolderName)
                || $panDob === null
                || !hash_equals((string) session('kyc_pan_dob', ''), $panDob)
                || !hash_equals(
                    (string) session('kyc_pan_document_hash', ''),
                    (string) $panDocumentHash
                )) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verify the submitted PAN number, holder name, date of birth, and selected PAN image through Cashfree before submitting Personal KYC.',
                ], 422);
            }

            if ($identifierConflict = $this->findKycIdentifierConflict($customer, null, $aadhar, $panNumber)) {
                return $this->kycIdentifierConflictResponse($identifierConflict);
            }
            // A business account can be a sole proprietorship, where the valid
            // PAN is the proprietor's individual PAN. The PAN has already passed
            // Cashfree verification and the uniqueness check above.

            // Ensure upload directories exist
            $uploadDirs = [
                'uploads/aadhar_front_documents',
                'uploads/aadhar_back_documents',
                'uploads/pan_documents',
                'uploads/signature_documents',
                'uploads/merchant_agreements',
            ];
            foreach ($uploadDirs as $dir) {
                $path = public_path($dir);
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
            }

            // Handle Aadhaar front document upload
            $aadharFrontPath = null;
            if ($request->hasFile('aadhar_front_document')) {
                $file = $request->file('aadhar_front_document');
                $filename = time() . '_aadhar_front_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aadhar_front_documents'), $filename);
                $aadharFrontPath = 'uploads/aadhar_front_documents/' . $filename;
            }

            // Handle Aadhaar back document upload
            $aadharBackPath = null;
            if ($request->hasFile('aadhar_back_document')) {
                $file = $request->file('aadhar_back_document');
                $filename = time() . '_aadhar_back_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aadhar_back_documents'), $filename);
                $aadharBackPath = 'uploads/aadhar_back_documents/' . $filename;
            }

            // Handle PAN document upload
            $panDocumentPath = null;
            if ($request->hasFile('pan_document')) {
                $file = $request->file('pan_document');
                $filename = time() . '_pan_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pan_documents'), $filename);
                $panDocumentPath = 'uploads/pan_documents/' . $filename;
            }

            // Store the uploaded image and persist only its relative path.
            $file = $request->file('signature_document');
            $filename = time() . '_signature_' . \Illuminate\Support\Str::uuid()
                . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/signature_documents'), $filename);
            $signaturePath = 'uploads/signature_documents/' . $filename;

            // Handle merchant agreement document upload
            $merchantAgreementPath = null;
            if ($request->hasFile('merchant_agreement')) {
                $file = $request->file('merchant_agreement');
                $filename = time() . '_merchant_agreement_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/merchant_agreements'), $filename);
                $merchantAgreementPath = 'uploads/merchant_agreements/' . $filename;
            }

            // Create or update KYC Detail record (Personal KYC = CSB-IV)
            $kycDetail = KycDetail::where('customer_id', $customer->id)
                ->where('kyc_type', 'personal')
                ->latest()
                ->first();

            $kycData = [
                'customer_id' => $customer->id,
                'kyc_type' => 'personal',
                'aadhar_number' => $aadhar,
                'aadhar_verified' => true,
                'aadhar_front_document' => $aadharFrontPath,
                'aadhar_back_document' => $aadharBackPath,
                'aadhar_address' => $validated['aadhar_address'],
                'pan_number' => $panNumber,
                'pan_holder_name' => $validated['pan_holder_name'],
                'pan_dob' => $validated['pan_dob'],
                'pan_document' => $panDocumentPath,
                'pan_verified' => true,
                'signature_document' => $signaturePath,
                'signature' => null,
                'billing_address' => $validated['billing_address'],
                'billing_contact' => $validated['billing_contact'],
                'billing_email' => $validated['billing_email'],
                'merchant_agreement' => $merchantAgreementPath,
                'merchant_agreement_accepted_at' => $validated['terms_accepted'] ? now() : null,
                'terms_accepted' => $validated['terms_accepted'],
                'terms_accepted_at' => $validated['terms_accepted'] ? now() : null,
                'kyc_status' => 'under_review',
            ];

            if ($kycDetail) {
                $kycDetail->update($kycData);
            } else {
                $kycDetail = KycDetail::create($kycData);
            }

            // Update customer record with Aadhaar and PAN
            $customer->aadhar_number = $aadhar;
            $customer->aadhar_verified = true;
            $customer->pan_number = $panNumber;
            $customer->pan_verified = true;
            // Personal KYC = CSB-IV (status 1)
            $customer->csb_status = 1;
            $customer->save();
            KycDraft::where('customer_id', $customer->id)
                ->where('kyc_type', 'personal')
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Personal KYC (CSB-IV) submitted successfully! Your application is now under review.',
                'redirect' => route('customer.kyc.summary')
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Personal KYC database error: ' . $e->getMessage());
            if ($e->getCode() === '23000' && ($identifier = $this->databaseKycIdentifierFromException($e))) {
                return $this->kycIdentifierConflictResponse($identifier);
            }

            return response()->json([
                'success' => false,
                'message' => 'Personal KYC submission failed. Please try again.'
            ], 500);
        } catch (\Throwable $e) {
            \Log::error('Personal KYC submission error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Personal KYC submission failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Show the KYC Summary page.
     * Displays both Personal KYC (KycDetail) and Business KYC (CsbForm) details.
     */
    public function kycSummary()
    {
        // Check if customer is logged in using auth guard
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();

        // Fetch Personal KYC detail (if any)
        $personalKyc = KycDetail::where('customer_id', $customer->id)
            ->where('kyc_type', 'personal')
            ->latest()
            ->first();

        // Fetch Business KYC / CSB form (if any)
        $businessKyc = CsbForm::where('customer_id', $customer->id)
            ->latest()
            ->first();

        // Load the customer's business category to determine user_type (Personal / Business)
        $businessCategory = BusinessCategory::find($customer->business_category_id);
        $userType = $businessCategory ? $businessCategory->user_type : 'Personal';

        return view('customer.kyc-summary', compact('customer', 'personalKyc', 'businessKyc', 'userType', 'businessCategory'));
    }

    /**
     * Determine whether the customer belongs to the Courier / Aggregator category.
     */
    private function isCourierOrAggregator(Customer $customer): bool
    {
        $category = $customer->relationLoaded('businessCategory')
            ? $customer->businessCategory
            : $customer->businessCategory()->first();

        if (!$category) {
            return false;
        }

        $allowedCategories = [
            'courier-or-aggregator',
            'courier-aggregator',
            'courier or aggregator',
            'courier / aggregator',
            'courier/aggregator',
        ];

        return in_array(strtolower(trim((string) $category->category_slug)), $allowedCategories, true)
            || in_array(strtolower(trim((string) $category->category_name)), $allowedCategories, true);
    }
}
