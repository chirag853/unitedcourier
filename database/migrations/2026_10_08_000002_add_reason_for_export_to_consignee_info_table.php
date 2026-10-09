<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('consignee_info') && ! Schema::hasColumn('consignee_info', 'reason_for_export')) {
            Schema::table('consignee_info', function (Blueprint $table) {
                $table->string('reason_for_export', 20)->nullable()->after('origin_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('consignee_info') && Schema::hasColumn('consignee_info', 'reason_for_export')) {
            Schema::table('consignee_info', function (Blueprint $table) {
                $table->dropColumn('reason_for_export');
            });
        }
    }
};
