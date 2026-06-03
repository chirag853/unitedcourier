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
        DB::statement("ALTER TABLE `shipment_invoice` ADD `delivery_type` VARCHAR(20) NULL DEFAULT NULL COMMENT 'DDU, DDP, Self' AFTER `status`");
        DB::statement("ALTER TABLE `shipment_invoice` ADD `assigned_delivery_person` INT NULL DEFAULT NULL COMMENT 'FK to admin_user.id' AFTER `delivery_type`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("SET @@sql_mode = ''");
        DB::statement("ALTER TABLE `shipment_invoice` DROP COLUMN `assigned_delivery_person`");
        DB::statement("ALTER TABLE `shipment_invoice` DROP COLUMN `delivery_type`");
    }
};