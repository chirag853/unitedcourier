<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exporter_customers', function (Blueprint $table) {
            $table->boolean('is_gst')->default(false)->after('is_lut');
            $table->string('gst_certificate_number', 15)->nullable()->after('is_gst');
            $table->string('gst_business_name')->nullable()->after('gst_certificate_number');
            $table->string('gst_certificate_document')->nullable()->after('gst_business_name');
        });
    }

    public function down(): void
    {
        Schema::table('exporter_customers', function (Blueprint $table) {
            $table->dropColumn([
                'is_gst',
                'gst_certificate_number',
                'gst_business_name',
                'gst_certificate_document',
            ]);
        });
    }
};
