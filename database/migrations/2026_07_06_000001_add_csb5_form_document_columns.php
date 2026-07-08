<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds support for:
     *  - GST document upload
     *  - IEC document upload
     *  - Bank type (private / government)
     *  - GST certificate number + document upload
     *  - LUT verification flag
     */
    public function up(): void
    {
        Schema::table('csb_forms', function (Blueprint $table) {
            $table->string('gst_document', 255)->nullable()->after('lut_document')->comment('Uploaded GST file path');
            $table->string('iec_document', 255)->nullable()->after('gst_document')->comment('Uploaded IEC file path');
            $table->enum('bank_type', ['private', 'government'])->default('private')->after('bank_account_number')->comment('Bank type: private or government');
            $table->string('gst_certificate_number', 50)->nullable()->after('iec_number')->comment('GST Certificate Number');
            $table->string('gst_certificate_document', 255)->nullable()->after('gst_document')->comment('Uploaded GST certificate file path');
            $table->boolean('lut_verified')->default(false)->after('is_lut')->comment('Whether LUT was verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('csb_forms', function (Blueprint $table) {
            $table->dropColumn([
                'gst_document',
                'iec_document',
                'bank_type',
                'gst_certificate_number',
                'gst_certificate_document',
                'lut_verified',
            ]);
        });
    }
};
