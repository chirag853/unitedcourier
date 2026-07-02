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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->enum('type', ['credit', 'debit'])->default('credit');
            $table->enum('reason', ['recharge', 'refund', 'shipment_charge', 'adjustment'])->default('recharge');
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->decimal('balance_after', 15, 2)->default(0.00);
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Indexes for faster lookups
            $table->index('customer_id');
            $table->index(['customer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
