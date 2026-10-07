<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_api_tokens', function (Blueprint $table) {
            $table->id();
            // No FK constraint (customers table engine/type varies across envs);
            // integrity enforced at app level.
            $table->unsignedBigInteger('customer_id')->index();
            $table->string('name')->default('api');
            $table->string('token', 64)->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_api_tokens');
    }
};
