<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds an aadhar_address column to kyc_details so the customer's
     * Aadhaar-linked address can be stored and used as the billing address.
     */
    public function up(): void
    {
        Schema::table('kyc_details', function (Blueprint $table) {
            $table->text('aadhar_address')->nullable()->after('aadhar_back_document')
                ->comment('Address as printed on the Aadhaar card');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_details', function (Blueprint $table) {
            $table->dropColumn('aadhar_address');
        });
    }
};
