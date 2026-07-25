<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Convert the `country` column on the `courier_services` table from the
 * friendly-name / short-code format ("US", "UK", "Canada", "Australia")
 * to the short country codes stored in the `destinations` table's
 * `country_code` column ("US", "UK", "CA", "AUS").
 *
 * After this migration courier_services.country holds the same code as
 * destinations.country_code, so the two tables can be matched directly
 * instead of through the friendly-name lookup maps.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('courier_services') || !Schema::hasTable('destinations')) {
            return;
        }

        // ------------------------------------------------------------------
        // Build a lookup that maps every possible courier_services.country
        // value (short code, friendly name, or country code) to the matching
        // destination's country_code.
        // ------------------------------------------------------------------
        $destinations = DB::table('destinations')->get();

        $countryToIso = [];
        foreach ($destinations as $dest) {
            $iso = $dest->country_code ?: $dest->code;
            if ($iso === null || $iso === '') {
                continue;
            }
            // Map the destination's own code, name and country_code to the ISO code.
            foreach ([$dest->code, $dest->name, $dest->country_code] as $key) {
                if ($key === null || $key === '') {
                    continue;
                }
                $countryToIso[strtolower(trim($key))] = $iso;
            }
        }

        // Friendly-name fallbacks for the values historically written by the
        // 2026_07_13_000004_add_country_to_courier_services_table migration
        // ("US", "UK", "Canada", "Australia"). UK maps to "UK" and Australia
        // maps to "AUS" to match the country_code stored in the destinations
        // table.
        $friendlyMap = [
            'us'             => 'US',
            'usa'            => 'US',
            'uk'             => 'UK',
            'united kingdom' => 'UK',
            'great britain'  => 'UK',
            'gb'             => 'UK',
            'canada'         => 'CA',
            'ca'             => 'CA',
            'australia'      => 'AUS',
            'aus'            => 'AUS',
            'au'             => 'AUS',
        ];
        foreach ($friendlyMap as $k => $v) {
            if (!isset($countryToIso[$k])) {
                $countryToIso[$k] = $v;
            }
        }

        // ------------------------------------------------------------------
        // Re-write every courier_services.country value to its country code.
        // Rows whose country is empty or already an unknown code are left
        // untouched so no data is lost.
        // ------------------------------------------------------------------
        $services = DB::table('courier_services')->get();

        foreach ($services as $service) {
            $current = $service->country ?? null;
            if ($current === null || $current === '') {
                continue;
            }

            $key = strtolower(trim($current));
            if (!isset($countryToIso[$key])) {
                // Unknown value — leave it as-is.
                continue;
            }

            $iso = $countryToIso[$key];
            if ($iso !== $current) {
                DB::table('courier_services')
                    ->where('id', $service->id)
                    ->update(['country' => $iso]);
            }
        }
    }

    public function down(): void
    {
        // The conversion only changes data (no schema change). The original
        // friendly-name values cannot be reliably restored because new ISO
        // codes may have been added since, so we leave the data as-is on
        // rollback.
    }
};
