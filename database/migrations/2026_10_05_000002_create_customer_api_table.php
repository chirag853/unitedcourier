<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_api', function (Blueprint $table) {
            $table->id();
            // customers.id is INT in this DB, courier_services.id is BIGINT.
            // No FK constraints (engine/type varies across envs); enforced at app level.
            $table->unsignedInteger('customer_id')->index();
            $table->unsignedBigInteger('service_id')->index();
            $table->tinyInteger('status')->default(1)->comment('1=allowed, 0=blocked');
            $table->timestamps();

            $table->unique(['customer_id', 'service_id'], 'customer_api_customer_service_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_api');
    }
};
