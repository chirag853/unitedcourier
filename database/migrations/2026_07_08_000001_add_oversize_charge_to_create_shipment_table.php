<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds an oversize_charge column to the create_shipment table so that
     * shipments whose volumetric weight falls between 40.001 kg and 68 kg
     * can record the automatically-applied ₹21,000 oversize surcharge.
     */
    public function up(): void
    {
        Schema::table('create_shipment', function (Blueprint $table) {
            $table->decimal('oversize_charge', 10, 2)->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('create_shipment', function (Blueprint $table) {
            $table->dropColumn('oversize_charge');
        });
    }
};
