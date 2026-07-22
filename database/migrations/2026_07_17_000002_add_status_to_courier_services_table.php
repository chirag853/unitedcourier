<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a `status` column to the `courier_services` table.
 *
 *  - 1 = enabled  (service shows rates to customers)
 *  - 0 = disabled (service is hidden from rate calculations)
 *
 * The column defaults to 1 so every existing service remains enabled
 * after the migration runs — no behaviour changes until an admin
 * explicitly disables a service from the new admin "Courier Services"
 * page.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('courier_services')) {
            return;
        }

        if (!Schema::hasColumn('courier_services', 'status')) {
            Schema::table('courier_services', function (Blueprint $table) {
                // Placed right after `country` (the last column added by the
                // 2026_07_13_000004_add_country_to_courier_services_table migration).
                $table->tinyInteger('status')->default(1)->after('country');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('courier_services')) {
            return;
        }

        if (Schema::hasColumn('courier_services', 'status')) {
            Schema::table('courier_services', function (Blueprint $table) {
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            });
        }
    }
};
