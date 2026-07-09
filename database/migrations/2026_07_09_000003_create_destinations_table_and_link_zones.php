<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Creates a `destinations` table to hold delivery destinations
 * (US, UK, Canada, ...) and adds a `destination_id` foreign-key
 * column to the existing `zone` table so each zone can be linked
 * to the destination it belongs to.
 *
 * All existing zones (US states) are linked to the "US" destination.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Create the destinations table
        if (!Schema::hasTable('destinations')) {
            Schema::create('destinations', function (Blueprint $table) {
                $table->id();
                $table->string('name');                 // e.g. "US- United State of America"
                $table->string('code', 10)->unique();   // short code e.g. "US", "UK", "CA"
                $table->string('country_code', 5)->nullable(); // ISO country code e.g. "US", "GB", "CA"
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2) Seed the default destinations matching the create-shipment dropdown.
        $now = now()->format('Y-m-d H:i:s');

        $destinations = [
            [
                'name'         => 'US- United State of America',
                'code'         => 'US',
                'country_code' => 'US',
                'is_active'    => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'UK - United Kingdom',
                'code'         => 'UK',
                'country_code' => 'GB',
                'is_active'    => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'Canada',
                'code'         => 'CA',
                'country_code' => 'CA',
                'is_active'    => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        foreach ($destinations as $dest) {
            DB::table('destinations')->updateOrInsert(
                ['code' => $dest['code']],
                $dest
            );
        }

        // 3) Add destination_id column to the zone table
        if (!Schema::hasColumn('zone', 'destination_id')) {
            Schema::table('zone', function (Blueprint $table) {
                $table->unsignedBigInteger('destination_id')->nullable()->after('id');
            });
        }

        // 4) Link all existing zones to the US destination (id of "US" code).
        //    All current zones are US states, so they belong to the US destination.
        $usDestination = DB::table('destinations')->where('code', 'US')->first();
        if ($usDestination) {
            DB::table('zone')
                ->whereNull('destination_id')
                ->update(['destination_id' => $usDestination->id]);
        }
    }

    public function down(): void
    {
        // Remove the destination_id column from the zone table
        if (Schema::hasColumn('zone', 'destination_id')) {
            Schema::table('zone', function (Blueprint $table) {
                $table->dropColumn('destination_id');
            });
        }

        // Drop the destinations table
        Schema::dropIfExists('destinations');
    }
};
