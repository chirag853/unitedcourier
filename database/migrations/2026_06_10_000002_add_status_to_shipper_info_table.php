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
        Schema::table('shipper_info', function (Blueprint $table) {
            $table->string('status', 50)->default('draft')->after('kyc_number')->comment('Shipment status: draft, submitted, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipper_info', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};