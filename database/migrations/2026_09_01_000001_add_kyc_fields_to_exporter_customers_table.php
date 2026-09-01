<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guarded so this migration is safe to run even when some columns were
        // already created by a follow-up migration on the live database.
        Schema::table('exporter_customers', function (Blueprint $table) {
            if (! Schema::hasColumn('exporter_customers', 'aadhar_front_document')) {
                $table->string('aadhar_front_document')->nullable()->after('kyc_number');
            }
            if (! Schema::hasColumn('exporter_customers', 'aadhar_back_document')) {
                $table->string('aadhar_back_document')->nullable()->after('kyc_number');
            }
            if (! Schema::hasColumn('exporter_customers', 'pan_number')) {
                $table->string('pan_number', 20)->nullable()->after('aadhar_back_document');
            }
            if (! Schema::hasColumn('exporter_customers', 'pan_holder_name')) {
                $table->string('pan_holder_name')->nullable()->after('pan_number');
            }
            if (! Schema::hasColumn('exporter_customers', 'pan_dob')) {
                $table->date('pan_dob')->nullable()->after('pan_holder_name');
            }
            if (! Schema::hasColumn('exporter_customers', 'pan_document')) {
                $table->string('pan_document')->nullable()->after('pan_dob');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exporter_customers', function (Blueprint $table) {
            foreach ([
                'aadhar_front_document',
                'aadhar_back_document',
                'pan_number',
                'pan_holder_name',
                'pan_dob',
                'pan_document',
            ] as $column) {
                if (Schema::hasColumn('exporter_customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
