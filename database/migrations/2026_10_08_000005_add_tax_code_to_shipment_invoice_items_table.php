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
        if (Schema::hasTable('shipment_invoice_items') && ! Schema::hasColumn('shipment_invoice_items', 'tax_code')) {
            Schema::table('shipment_invoice_items', function (Blueprint $table) {
                $table->string('tax_code', 50)->nullable()->after('hts_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('shipment_invoice_items') && Schema::hasColumn('shipment_invoice_items', 'tax_code')) {
            Schema::table('shipment_invoice_items', function (Blueprint $table) {
                $table->dropColumn('tax_code');
            });
        }
    }
};
