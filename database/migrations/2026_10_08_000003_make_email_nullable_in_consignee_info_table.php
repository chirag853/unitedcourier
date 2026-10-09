<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('consignee_info') && Schema::hasColumn('consignee_info', 'email')) {
            DB::statement("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
            DB::statement('ALTER TABLE consignee_info MODIFY COLUMN email VARCHAR(150) NULL');
            DB::statement('SET SESSION sql_mode = DEFAULT');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('consignee_info') && Schema::hasColumn('consignee_info', 'email')) {
            DB::statement("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
            DB::statement('ALTER TABLE consignee_info MODIFY COLUMN email VARCHAR(150) NOT NULL');
            DB::statement('SET SESSION sql_mode = DEFAULT');
        }
    }
};
