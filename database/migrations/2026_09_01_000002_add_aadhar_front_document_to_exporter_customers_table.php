<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The original KYC-fields migration (2026_09_01_000001) added
        // aadhar_back_document but forgot aadhar_front_document. This migration
        // ensures the column exists even on databases where that migration has
        // already been recorded as run.
        Schema::table('exporter_customers', function (Blueprint $table) {
            if (! Schema::hasColumn('exporter_customers', 'aadhar_front_document')) {
                $table->string('aadhar_front_document')->nullable()->after('kyc_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exporter_customers', function (Blueprint $table) {
            if (Schema::hasColumn('exporter_customers', 'aadhar_front_document')) {
                $table->dropColumn('aadhar_front_document');
            }
        });
    }
};
