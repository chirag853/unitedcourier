<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Change the United Kingdom's `country_code` on the `destinations` table
 * from the ISO 3166-1 alpha-2 code "GB" to the short code "UK", and update
 * every matching `courier_services.country` value so the two tables stay
 * in sync.
 *
 * After this migration:
 *   - destinations.country_code for the UK row  → "UK"  (was "GB")
 *   - courier_services.country for UK services  → "UK"  (was "GB")
 *
 * External API integrations (UPS, PostShipping/DPD, Overseas Logistic)
 * continue to use the real ISO code "GB" via their own dedicated mapping
 * functions (getCountryCodeFromDestination, getOverseasCountryCode, and
 * the hardcoded ReceiverCountryCode payload field), so those are NOT
 * affected by this change.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Update the destinations table: UK's country_code "GB" → "UK".
        if (Schema::hasTable('destinations')) {
            DB::table('destinations')
                ->where('code', 'UK')
                ->where('country_code', 'GB')
                ->update(['country_code' => 'UK']);
        }

        // 2) Update courier_services: every service whose country is "GB"
        //    (the old ISO code) becomes "UK" so it matches the new
        //    destinations.country_code value used for internal rate
        //    calculation.
        if (Schema::hasTable('courier_services')) {
            DB::table('courier_services')
                ->where('country', 'GB')
                ->update(['country' => 'UK']);
        }
    }

    public function down(): void
    {
        // Revert the destinations country_code back to "GB".
        if (Schema::hasTable('destinations')) {
            DB::table('destinations')
                ->where('code', 'UK')
                ->where('country_code', 'UK')
                ->update(['country_code' => 'GB']);
        }

        // Revert courier_services.country back to "GB".
        if (Schema::hasTable('courier_services')) {
            DB::table('courier_services')
                ->where('country', 'UK')
                ->update(['country' => 'GB']);
        }
    }
};
