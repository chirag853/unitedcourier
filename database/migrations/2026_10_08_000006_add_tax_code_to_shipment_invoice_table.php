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
        if (Schema::hasTable('shipment_invoice') && ! Schema::hasColumn('shipment_invoice', 'tax_code')) {
            Schema::table('shipment_invoice', function (Blueprint $table) {
                $table->string('tax_code', 50)->nullable()->after('reference_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('shipment_invoice') && Schema::hasColumn('shipment_invoice', 'tax_code')) {
            Schema::table('shipment_invoice', function (Blueprint $table) {
                $table->dropColumn('tax_code');
            });
        }
    }
};
