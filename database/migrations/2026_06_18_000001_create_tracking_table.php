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
        Schema::create('tracking', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)->primary();
            $table->unsignedBigInteger('shipping_id')->index('idx_shipping_id');
            $table->string('uwc_id', 100)->index('idx_uwc_id');
            $table->string('title', 255);
            $table->string('status', 100);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->index('idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking');
    }
};
