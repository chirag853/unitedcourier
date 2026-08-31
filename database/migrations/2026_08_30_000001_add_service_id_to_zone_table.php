<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Add a `service_id` column to the `zone` table.
 *
 * This links each zone to the courier service it belongs to (e.g. a zone
 * created for the "United Ground Premium" service). The column is nullable
 * so existing zones remain untouched and a zone can be created without
 * selecting a service.
 *
 * NOTE: The `zone` table carries an `updated_at` timestamp whose default
 * value is not accepted under MySQL strict mode. Any ALTER on that table
 * therefore fails with "Invalid default value for 'updated_at'". We relax
 * `sql_mode` for the duration of the ALTER to work around this legacy
 * default and restore it afterwards.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('zone', 'service_id')) {
            return;
        }

        $sqlMode = DB::selectOne('SELECT @@SESSION.sql_mode AS mode')->mode;
        $restore = $sqlMode === null || $sqlMode === '' ? "''" : "'" . addslashes($sqlMode) . "'";

        try {
            DB::statement("SET SESSION sql_mode = ''");

            Schema::table('zone', function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->nullable()->after('destination_id');
                $table->foreign('service_id')
                    ->references('id')
                    ->on('courier_services')
                    ->onDelete('set null');
            });
        } finally {
            DB::statement("SET SESSION sql_mode = " . $restore);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('zone', 'service_id')) {
            return;
        }

        $sqlMode = DB::selectOne('SELECT @@SESSION.sql_mode AS mode')->mode;
        $restore = $sqlMode === null || $sqlMode === '' ? "''" : "'" . addslashes($sqlMode) . "'";

        try {
            DB::statement("SET SESSION sql_mode = ''");

            Schema::table('zone', function (Blueprint $table) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            });
        } finally {
            DB::statement("SET SESSION sql_mode = " . $restore);
        }
    }
};
