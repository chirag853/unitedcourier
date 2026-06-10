<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('shipment_invoice_items', function (Blueprint $table) {
            $table->decimal('igst_percentage', 5, 2)->nullable()->after('unit_rate');
            $table->decimal('igst_amount', 10, 2)->nullable()->after('igst_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('shipment_invoice_items', function (Blueprint $table) {
            $table->dropColumn('igst_amount');
            $table->dropColumn('igst_percentage');
        });
    }
};