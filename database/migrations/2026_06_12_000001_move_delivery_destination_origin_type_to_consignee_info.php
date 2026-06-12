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
        // Add delivery_destination and origin_type to consignee_info
        Schema::table('consignee_info', function (Blueprint $table) {
            $table->string('delivery_destination', 100)->nullable()->after('shipper_id');
            $table->string('origin_type', 50)->nullable()->after('delivery_destination');
        });

        // Remove delivery_destination and origin_type from shipper_info
        Schema::table('shipper_info', function (Blueprint $table) {
            $table->dropColumn(['delivery_destination', 'origin_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add columns back to shipper_info
        Schema::table('shipper_info', function (Blueprint $table) {
            $table->string('delivery_destination', 100)->nullable()->after('shipping_method');
            $table->string('origin_type', 50)->nullable()->after('delivery_destination');
        });

        // Remove columns from consignee_info
        Schema::table('consignee_info', function (Blueprint $table) {
            $table->dropColumn(['delivery_destination', 'origin_type']);
        });
    }
};