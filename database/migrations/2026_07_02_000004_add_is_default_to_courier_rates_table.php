<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds an `is_default` boolean column to the courier_rates table.
     * All existing rows are marked as default (1) so that current
     * behaviour is preserved — only rates explicitly marked as
     * non-default (0) will be excluded from admin rate changes.
     */
    public function up(): void
    {
        Schema::table('courier_rates', function (Blueprint $table) {
            $table->boolean('is_default')->default(1)->after('gst_amount');
        });

        // Ensure every existing row is marked as default
        \Illuminate\Support\Facades\DB::table('courier_rates')
            ->whereNull('is_default')
            ->orWhere('is_default', '!=', 1)
            ->update(['is_default' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courier_rates', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
