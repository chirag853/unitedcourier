<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Temporarily relax SQL mode to avoid "Invalid default value for 'updated_at'" error
        DB::statement("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
        DB::statement("ALTER TABLE shipment_invoice_items ADD COLUMN hts_code VARCHAR(50) NULL AFTER hs_code");
        DB::statement("SET SESSION sql_mode = DEFAULT");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
        DB::statement("ALTER TABLE shipment_invoice_items DROP COLUMN hts_code");
        DB::statement("SET SESSION sql_mode = DEFAULT");
    }
};