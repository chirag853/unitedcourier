<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds Personal KYC (CSB-IV) fields to the kyc_details table:
     *  - kyc_type: distinguishes personal (CSB-IV) vs business (CSB-V)
     *  - PAN fields: pan_number, pan_holder_name, pan_dob, pan_document, pan_verified
     *  - Aadhaar document uploads: aadhar_front_document, aadhar_back_document
     *  - Signature as file upload: signature_document
     *  - Merchant agreement: merchant_agreement, merchant_agreement_accepted_at
     */
    public function up(): void
    {
        Schema::table('kyc_details', function (Blueprint $table) {
            $table->string('kyc_type', 20)->default('personal')->after('customer_id')->comment('personal or business');
            $table->string('pan_number', 10)->nullable()->after('aadhar_verified')->comment('PAN number (10 chars)');
            $table->string('pan_holder_name', 255)->nullable()->after('pan_number')->comment('PAN holder name');
            $table->date('pan_dob')->nullable()->after('pan_holder_name')->comment('Date of birth for PAN');
            $table->string('pan_document', 500)->nullable()->after('pan_dob')->comment('Uploaded PAN file path');
            $table->boolean('pan_verified')->default(false)->after('pan_document')->comment('Whether PAN was verified');
            $table->string('aadhar_front_document', 500)->nullable()->after('pan_verified')->comment('Uploaded Aadhaar front file path');
            $table->string('aadhar_back_document', 500)->nullable()->after('aadhar_front_document')->comment('Uploaded Aadhaar back file path');
            $table->string('signature_document', 500)->nullable()->after('aadhar_back_document')->comment('Uploaded signature file path');
            $table->string('merchant_agreement', 500)->nullable()->after('signature_document')->comment('Uploaded merchant agreement file path');
            $table->timestamp('merchant_agreement_accepted_at')->nullable()->after('merchant_agreement')->comment('When merchant agreement was accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_details', function (Blueprint $table) {
            $table->dropColumn([
                'kyc_type',
                'pan_number',
                'pan_holder_name',
                'pan_dob',
                'pan_document',
                'pan_verified',
                'aadhar_front_document',
                'aadhar_back_document',
                'signature_document',
                'merchant_agreement',
                'merchant_agreement_accepted_at',
            ]);
        });
    }
};
