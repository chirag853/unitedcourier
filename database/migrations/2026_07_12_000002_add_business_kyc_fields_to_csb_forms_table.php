<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds Business KYC (CSB-V) fields to the csb_forms table:
     *  - Aadhaar: aadhar_number, aadhar_verified, aadhar_document
     *  - Authorized signature with company stamp: signature_document
     *  - AD Code document upload: ad_code_document
     *  - LUT additional fields: lut_expiry_date, lut_bond_year
     *  - Billing details (from GST address): billing_address, billing_gst, billing_contact, billing_email
     *  - Merchant agreement: merchant_agreement, merchant_agreement_accepted_at
     */
    public function up(): void
    {
        Schema::table('csb_forms', function (Blueprint $table) {
            $table->string('aadhar_number', 20)->nullable()->after('customer_id')->comment('Aadhaar number');
            $table->boolean('aadhar_verified')->default(false)->after('aadhar_number')->comment('Whether Aadhaar was verified');
            $table->string('aadhar_document', 500)->nullable()->after('aadhar_verified')->comment('Uploaded Aadhaar file path');
            $table->string('signature_document', 500)->nullable()->after('aadhar_document')->comment('Uploaded authorized signature with company stamp file path');
            $table->string('ad_code_document', 500)->nullable()->after('ad_code')->comment('Uploaded AD Code document file path');
            $table->date('lut_expiry_date')->nullable()->after('lut_verified')->comment('LUT expiry date');
            $table->string('lut_bond_year', 10)->nullable()->after('lut_expiry_date')->comment('LUT bond year');
            $table->text('billing_address')->nullable()->after('gst_document')->comment('Billing address from GST');
            $table->string('billing_gst', 15)->nullable()->after('billing_address')->comment('Billing GST number');
            $table->string('billing_contact', 20)->nullable()->after('billing_gst')->comment('Billing contact number');
            $table->string('billing_email', 255)->nullable()->after('billing_contact')->comment('Billing email');
            $table->string('merchant_agreement', 500)->nullable()->after('billing_email')->comment('Uploaded merchant agreement file path');
            $table->timestamp('merchant_agreement_accepted_at')->nullable()->after('merchant_agreement')->comment('When merchant agreement was accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('csb_forms', function (Blueprint $table) {
            $table->dropColumn([
                'aadhar_number',
                'aadhar_verified',
                'aadhar_document',
                'signature_document',
                'ad_code_document',
                'lut_expiry_date',
                'lut_bond_year',
                'billing_address',
                'billing_gst',
                'billing_contact',
                'billing_email',
                'merchant_agreement',
                'merchant_agreement_accepted_at',
            ]);
        });
    }
};
