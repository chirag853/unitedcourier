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
        if (Schema::hasTable('consignee_info') && ! Schema::hasColumn('consignee_info', 'business_type')) {
            Schema::table('consignee_info', function (Blueprint $table) {
                $table->string('business_type', 50)->nullable()->after('reason_for_export');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('consignee_info') && Schema::hasColumn('consignee_info', 'business_type')) {
            Schema::table('consignee_info', function (Blueprint $table) {
                $table->dropColumn('business_type');
            });
        }
    }
};
