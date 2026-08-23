<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('shipper_info', 'base_price')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->decimal('base_price', 10, 2)->nullable()->after('status');
                $table->decimal('fuel_price', 10, 2)->nullable()->after('base_price');
                $table->decimal('gst_percentage', 5, 2)->nullable()->after('fuel_price');
                $table->decimal('gst_amount', 10, 2)->nullable()->after('gst_percentage');
                $table->json('surcharge')->nullable()->after('gst_amount');
            });
        }   
    }

    public function down(): void
    {
        if (Schema::hasColumn('shipper_info', 'base_price')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->dropColumn(['base_price', 'fuel_price', 'gst_percentage', 'gst_amount', 'surcharge']);
            });
        }
    }
};