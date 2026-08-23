<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('shipper_info', 'surcharge_total')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->decimal('surcharge_total', 10, 2)->nullable()->after('surcharge');
            });
        }

        if (! Schema::hasColumn('shipper_info', 'total_base_price')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->decimal('total_base_price', 10, 2)->nullable()->after('surcharge_total');
                $table->decimal('total_fuel_price', 10, 2)->nullable()->after('total_base_price');
                $table->decimal('total_surcharge', 10, 2)->nullable()->after('total_fuel_price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('shipper_info', 'total_base_price')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->dropColumn(['total_base_price', 'total_fuel_price', 'total_surcharge']);
            });
        }
    }
};