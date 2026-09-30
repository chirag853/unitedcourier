<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create-shipment ke warehouse dropdown ki selection order par save hoti hai,
     * taaki Delhivery pickup wahi registered warehouse lage (pin/city match nahi).
     * No FK constraint (shipper_info.id int unsigned vs warehouse_addresses.id bigint).
     */
    public function up(): void
    {
        Schema::table('shipper_info', function (Blueprint $table) {
            if (! Schema::hasColumn('shipper_info', 'warehouse_address_id')) {
                $table->unsignedBigInteger('warehouse_address_id')->nullable()->after('service_id');
                $table->index('warehouse_address_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipper_info', function (Blueprint $table) {
            if (Schema::hasColumn('shipper_info', 'warehouse_address_id')) {
                $table->dropIndex(['warehouse_address_id']);
                $table->dropColumn('warehouse_address_id');
            }
        });
    }
};
