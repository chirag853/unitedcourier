<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ensures the `business_categories` table has a `user_type` column that
     * stores either 'Business' or 'Personal'. This column drives which KYC flow
     * (CSB-V Business 10-step vs CSB-IV Personal 7-step) is shown on the
     * customer dashboard.
     *
     * The column is seeded from the `parent_group` column so the Business
     * stepper appears for all Business categories (B2B, D2C, eCommerce,
     * Courier or Aggregator, Exporter, Marketplace) and the Personal stepper
     * for the Personal category.
     *
     * This migration is idempotent: if the `user_type` column already exists
     * (it may have been added manually in a prior session), the column creation
     * is skipped but the data is still re-synced from `parent_group` to fix any
     * inconsistent rows (e.g. B2B / D2C incorrectly set to 'Personal').
     */
    public function up(): void
    {
        if (!Schema::hasColumn('business_categories', 'user_type')) {
            Schema::table('business_categories', function (Blueprint $table) {
                $table->string('user_type', 20)->default('Personal')->after('parent_group')->index();
            });
        }

        // Sync user_type from the existing parent_group values.
        // Any category whose parent_group is 'Business' becomes user_type 'Business'.
        DB::table('business_categories')
            ->where('parent_group', 'Business')
            ->update(['user_type' => 'Business']);

        // Everything else (including null / 'Personal' parent_group) stays 'Personal'.
        DB::table('business_categories')
            ->where(function ($q) {
                $q->whereNull('user_type')
                  ->orWhere('user_type', '')
                  ->orWhere('parent_group', '!=', 'Business');
            })
            ->update(['user_type' => 'Personal']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('business_categories', 'user_type')) {
            Schema::table('business_categories', function (Blueprint $table) {
                $table->dropIndex(['user_type']);
                $table->dropColumn('user_type');
            });
        }
    }
};
