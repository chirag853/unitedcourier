<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a new `zone_number_testing` column to the `zone` table.
     *
     * This column stores the zone number that courier rates reference via
     * their `zone_no` field. Rate matching now compares a rate's `zone_no`
     * against the zone's `zone_number_testing` (instead of the zone's `id`),
     * so that zone-independent rates (zone_no = null/0) and zone-matched
     * rates are both shown weight-wise on the create-shipment page.
     */
    public function up(): void
    {
        // Guard against re-running: only add the column if it doesn't already exist.
        if (!Schema::hasColumn('zone', 'zone_number_testing')) {
            Schema::table('zone', function (Blueprint $table) {
                $table->integer('zone_number_testing')->nullable()->after('zone_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zone', function (Blueprint $table) {
            $table->dropColumn('zone_number_testing');
        });
    }
};
