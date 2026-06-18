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
        Schema::table('tracking', function (Blueprint $table) {
            $table->string('awb_number', 100)->nullable()->after('id')->index('idx_awb_number');
            $table->unsignedBigInteger('shipper_id')->nullable()->after('awb_number')->index('idx_shipper_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracking', function (Blueprint $table) {
            $table->dropIndex('idx_awb_number');
            $table->dropIndex('idx_shipper_id');
            $table->dropColumn('awb_number');
            $table->dropColumn('shipper_id');
        });
    }
};
