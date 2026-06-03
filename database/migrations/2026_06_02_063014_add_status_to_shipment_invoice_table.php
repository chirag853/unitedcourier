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
        // Temporarily disable strict mode to handle pre-existing zero-date defaults
        DB::statement("SET @@sql_mode = ''");
        DB::statement("ALTER TABLE `shipment_invoice` ADD `status` VARCHAR(20) NOT NULL DEFAULT 'active' COMMENT 'active, cancelled' AFTER `reference_number`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `shipment_invoice` DROP COLUMN `status`");
    }
};
