<?php

namespace App\Support;

use App\Models\BusinessCategory;
use App\Models\Customer;
use App\Models\KycDraft;

/**
 * Persists the Cashfree KYC verification state (GST / Aadhaar / PAN) into the
 * customer's KYC draft so it survives a logout + login. Logging out invalidates
 * the session, which previously wiped the verification flags and made resuming
 * an in-progress KYC submission fail with "Verify the submitted GSTIN through
 * Cashfree before submitting KYC."
 */
class KycVerificationState
{
    /**
     * Session keys recorded when a Cashfree verification succeeds. These are
     * mirrored into the KYC draft's form_data and restored on login / resume.
     */
    private const SESSION_KEYS = [
        'kyc_gst_number',
        'kyc_gst_business_name',
        'kyc_gst_address',
        'kyc_gst_verified',
        'kyc_gst_cashfree_verified',
        'kyc_aadhar_number',
        'kyc_aadhar_verified',
        'kyc_aadhar_cashfree_verified',
        'kyc_aadhar_front_hash',
        'kyc_aadhar_verification_id',
        'kyc_pan_number',
        'kyc_pan_holder_name',
        'kyc_pan_dob',
        'kyc_pan_verified',
        'kyc_pan_cashfree_verified',
        'kyc_pan_document_hash',
        'kyc_pan_verification_id',
    ];

    /**
     * Copy the current session verification state into the customer's KYC
     * draft (for the customer's flow type) so it can be restored later.
     */
    public static function persist(Customer $customer): void
    {
        $values = [];
        foreach (self::SESSION_KEYS as $key) {
            if (session()->has($key)) {
                $values[$key] = session($key);
            }
        }
        if (!$values) {
            return;
        }

        $kycType = self::flowType($customer);
        $draft = KycDraft::where('customer_id', $customer->id)
            ->where('kyc_type', $kycType)
            ->first();
        if (!$draft) {
            $draft = KycDraft::create([
                'customer_id' => $customer->id,
                'kyc_type' => $kycType,
                'current_step' => 1,
            ]);
        }
        $formData = is_array($draft->form_data) ? $draft->form_data : [];
        $draft->form_data = array_merge($formData, $values);
        $draft->save();
    }

    /**
     * KYC flow type for the customer (personal or business), based on the
     * selected business category — mirrors CustomerController::dashboard.
     */
    private static function flowType(Customer $customer): string
    {
        $businessCategory = BusinessCategory::find($customer->business_category_id);

        return $businessCategory && strcasecmp((string) $businessCategory->user_type, 'Business') === 0
            ? 'business'
            : 'personal';
    }

    /**
     * Restore the verification state saved in the customer's KYC drafts back
     * into the session (e.g. after a logout + login cleared the session).
     * Only keys whose Cashfree verification marker is present are restored, so
     * stale or partially recorded state is never re-activated.
     */
    public static function restore(Customer $customer): void
    {
        $stored = [];
        $drafts = KycDraft::where('customer_id', $customer->id)->get();
        foreach ($drafts as $draft) {
            if (!is_array($draft->form_data)) {
                continue;
            }
            foreach ($draft->form_data as $key => $value) {
                if (in_array($key, self::SESSION_KEYS, true)
                    && $value !== null
                    && $value !== '') {
                    $stored[$key] = $value;
                }
            }
        }

        if (!$stored) {
            return;
        }

        $groups = [
            'kyc_gst' => 'kyc_gst_cashfree_verified',
            'kyc_aadhar' => 'kyc_aadhar_cashfree_verified',
            'kyc_pan' => 'kyc_pan_cashfree_verified',
        ];
        $restore = [];
        foreach ($groups as $prefix => $marker) {
            if (empty($stored[$marker])) {
                continue;
            }
            foreach ($stored as $key => $value) {
                if (str_starts_with($key, $prefix . '_')) {
                    $restore[$key] = $value;
                }
            }
        }

        if ($restore) {
            session($restore);
        }
    }
}