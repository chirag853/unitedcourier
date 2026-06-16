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
        Schema::table('shipper_info', function (Blueprint $table) {
            $table->unsignedBigInteger('service_rate_id')->nullable()->after('kyc_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipper_info', function (Blueprint $table) {
            $table->dropColumn('service_rate_id');
        });
    }
};
