<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add indexes for the admin COD create-order rate APIs (codUpsRate,
 * codServicesByDestination, codZonesByDestination).
 *
 * These endpoints filter courier_rates by (customer_id, service_id, zone_no),
 * courier_services by (country, status) and the zone table by destination_id —
 * none of which were indexed, forcing full table scans on every Calculate
 * Rate click. (Note: the zones table is named `zone`, see App\Models\Zone.)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('courier_rates') && !$this->indexExists('courier_rates', 'courier_rates_customer_service_zone_idx')) {
            Schema::table('courier_rates', function (Blueprint $table) {
                $table->index(['customer_id', 'service_id', 'zone_no'], 'courier_rates_customer_service_zone_idx');
            });
        }

        if (Schema::hasTable('courier_services') && !$this->indexExists('courier_services', 'courier_services_country_status_idx')) {
            Schema::table('courier_services', function (Blueprint $table) {
                $table->index(['country', 'status'], 'courier_services_country_status_idx');
            });
        }

        // NOTE: the legacy `zone` table declares
        // `updated_at` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00', which
        // strict sql_mode (NO_ZERO_DATE) rejects on ANY alter. The index is
        // added with that flag relaxed for the current session only — no data
        // is changed and the original mode is always restored.
        $this->addZoneIndex('zone_destination_category_idx', ['destination_id', 'zone_category']);
        $this->addZoneIndex('zone_destination_code_idx', ['destination_id', 'zone_code']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('courier_rates') && $this->indexExists('courier_rates', 'courier_rates_customer_service_zone_idx')) {
            Schema::table('courier_rates', function (Blueprint $table) {
                $table->dropIndex('courier_rates_customer_service_zone_idx');
            });
        }

        if (Schema::hasTable('courier_services') && $this->indexExists('courier_services', 'courier_services_country_status_idx')) {
            Schema::table('courier_services', function (Blueprint $table) {
                $table->dropIndex('courier_services_country_status_idx');
            });
        }

        $this->dropZoneIndex('zone_destination_category_idx');
        $this->dropZoneIndex('zone_destination_code_idx');
    }

    /**
     * Check whether an index with the given name already exists on the table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? '') === $indexName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add an index on the legacy `zone` table with zero-date strict flags
     * relaxed for the current session only. The original sql_mode is always
     * restored; no table data is modified.
     */
    private function addZoneIndex(string $indexName, array $columns): void
    {
        if (! Schema::hasTable('zone') || $this->indexExists('zone', $indexName)) {
            return;
        }

        $connection = Schema::getConnection();
        $originalMode = null;

        try {
            $originalMode = $connection->selectOne('SELECT @@SESSION.sql_mode AS mode')->mode ?? '';
            $relaxedMode = implode(',', array_diff(
                explode(',', (string) $originalMode),
                ['NO_ZERO_DATE', 'NO_ZERO_IN_DATE']
            ));
            $connection->statement('SET SESSION sql_mode = ?', [$relaxedMode]);

            Schema::table('zone', function (Blueprint $table) use ($indexName, $columns) {
                $table->index($columns, $indexName);
            });
        } finally {
            if ($originalMode !== null) {
                $connection->statement('SET SESSION sql_mode = ?', [$originalMode]);
            }
        }
    }
    /**
     * Drop an index on the legacy `zone` table (same zero-date session
     * relaxation as addZoneIndex).
     */
    private function dropZoneIndex(string $indexName): void
    {
        if (! Schema::hasTable('zone') || ! $this->indexExists('zone', $indexName)) {
            return;
        }

        $connection = Schema::getConnection();
        $originalMode = null;

        try {
            $originalMode = $connection->selectOne('SELECT @@SESSION.sql_mode AS mode')->mode ?? '';
            $relaxedMode = implode(',', array_diff(
                explode(',', (string) $originalMode),
                ['NO_ZERO_DATE', 'NO_ZERO_IN_DATE']
            ));
            $connection->statement('SET SESSION sql_mode = ?', [$relaxedMode]);

            Schema::table('zone', function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        } finally {
            if ($originalMode !== null) {
                $connection->statement('SET SESSION sql_mode = ?', [$originalMode]);
            }
        }
    }
};
