<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a signature column to store the customer's uploaded signature
     * (base64 data URL) captured during the KYC flow, plus the billing
     * details collected in the Bill step.
     */
    public function up(): void
    {
        Schema::table('kyc_details', function (Blueprint $table) {
            $table->longText('signature')->nullable()->after('authorized_signatory');
            $table->text('billing_address')->nullable()->after('signature');
            $table->string('billing_gst', 20)->nullable()->after('billing_address');
            $table->string('billing_contact', 20)->nullable()->after('billing_gst');
            $table->string('billing_email', 255)->nullable()->after('billing_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_details', function (Blueprint $table) {
            $table->dropColumn([
                'signature',
                'billing_address',
                'billing_gst',
                'billing_contact',
                'billing_email',
            ]);
        });
    }
};
