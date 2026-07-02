<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kyc_details', function (Blueprint $table) {
            // Step 2: Aadhar Verification
            $table->string('aadhar_number', 20)->nullable()->after('otp_verified');
            $table->boolean('aadhar_verified')->default(false)->after('aadhar_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_details', function (Blueprint $table) {
            $table->dropColumn(['aadhar_number', 'aadhar_verified']);
        });
    }
};
