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
            $table->string('gst_certificate_document', 500)
                ->nullable()
                ->after('gst_verified')
                ->comment('Uploaded Personal GST certificate PDF path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_details', function (Blueprint $table) {
            $table->dropColumn('gst_certificate_document');
        });
    }
};
