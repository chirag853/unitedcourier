<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Temporarily disable strict mode to avoid "Invalid default value for 'updated_at'" error
        DB::statement("SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION'");

        // Column was already added as bigint unsigned from a previous failed attempt
        // Drop it first, then re-add with the correct int unsigned type to match package_dimension.id
        Schema::table('shipment_invoice_items', function (Blueprint $table) {
            $table->dropColumn('package_dimension_id');
        });

        Schema::table('shipment_invoice_items', function (Blueprint $table) {
            $table->unsignedInteger('package_dimension_id')->nullable()->after('invoice_id');

            $table->foreign('package_dimension_id')
                ->references('id')
                ->on('package_dimension')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        // Restore original strict mode
        DB::statement("SET SESSION sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION'");

        Schema::table('shipment_invoice_items', function (Blueprint $table) {
            $table->dropForeign(['package_dimension_id']);
            $table->dropColumn('package_dimension_id');
        });

        DB::statement("SET SESSION sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }
};
