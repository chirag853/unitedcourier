<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->string('cashfree_order_id', 45)->unique();
            $table->decimal('order_amount', 15, 2)->default(0.00);
            $table->string('currency', 10)->default('INR');
            $table->string('status', 20)->default('pending')->index();
            $table->string('payment_session_id', 191)->nullable();
            $table->string('recharge_type', 50)->nullable();
            $table->string('cf_payment_id', 45)->nullable();
            $table->string('payment_method', 100)->nullable();
            $table->timestamp('payment_time')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_orders');
    }
};
