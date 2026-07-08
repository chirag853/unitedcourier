<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a handling_charge column to the create_shipment table.
     * Used for the USA – United Ground Premium handling charge rule:
     * if actual weight > 22 kg, a ₹5,000 handling charge is applied.
     */
    public function up(): void
    {
        Schema::table('create_shipment', function (Blueprint $table) {
            $table->decimal('handling_charge', 10, 2)->default(0)->after('oversize_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('create_shipment', function (Blueprint $table) {
            $table->dropColumn('handling_charge');
        });
    }
};
