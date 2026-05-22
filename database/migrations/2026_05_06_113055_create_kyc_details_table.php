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
        Schema::create('kyc_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            
            // Step 1: KYC Verification
            $table->string('gst_number', 20)->nullable();
            $table->boolean('gst_verified')->default(false);
            $table->boolean('otp_verified')->default(false);
            
            // Step 2: Business Info
            $table->string('organization_name', 255)->nullable();
            $table->string('authorized_signatory', 255)->nullable();
            
            // Step 3: Agreement
            $table->boolean('terms_accepted')->default(false);
            $table->datetime('terms_accepted_at')->nullable();
            
            // Step 4: Final Status
            $table->enum('kyc_status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            
            $table->timestamps();
            
            // Foreign Key
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_details');
    }
};
