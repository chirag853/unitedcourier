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
        if (Schema::hasTable('shipment_invoice_items') && ! Schema::hasColumn('shipment_invoice_items', 'product_sku')) {
            Schema::table('shipment_invoice_items', function (Blueprint $table) {
                $table->string('product_sku', 100)->nullable()->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('shipment_invoice_items') && Schema::hasColumn('shipment_invoice_items', 'product_sku')) {
            Schema::table('shipment_invoice_items', function (Blueprint $table) {
                $table->dropColumn('product_sku');
            });
        }
    }
};
