<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add `start_date` and `end_date` columns to the courier_rates table.
     *
     * Every existing rate (default + customer-specific) is back-filled with
     * start_date = 2026-01-01 and end_date = 2026-12-31 so that the new
     * columns are never NULL for legacy rows.
     */
    public function up(): void
    {
        Schema::table('courier_rates', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('is_default');
            $table->date('end_date')->nullable()->after('start_date');
        });

        // Back-fill every existing row with the default validity window.
        DB::table('courier_rates')
            ->whereNull('start_date')
            ->orWhereNull('end_date')
            ->update([
                'start_date' => '2026-01-01',
                'end_date'   => '2026-12-31',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courier_rates', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
