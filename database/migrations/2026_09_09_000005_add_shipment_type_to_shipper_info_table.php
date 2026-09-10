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
            if (! Schema::hasColumn('shipper_info', 'shipment_type')) {
                $table->unsignedTinyInteger('shipment_type')
                    ->default(1)
                    ->after('total_price')
                    ->comment('1=general, 2=cod, 3=foc, 4=prepare');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipper_info', function (Blueprint $table) {
            if (Schema::hasColumn('shipper_info', 'shipment_type')) {
                $table->dropColumn('shipment_type');
            }
        });
    }
};
