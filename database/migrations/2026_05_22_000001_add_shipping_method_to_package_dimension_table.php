<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE package_dimension MODIFY updated_at TIMESTAMP NULL DEFAULT NULL');

        Schema::table('package_dimension', function (Blueprint $table) {
            $table->string('shipping_method', 100)->nullable()->after('shipper_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_dimension', function (Blueprint $table) {
            $table->dropColumn('shipping_method');
        });
    }
};
