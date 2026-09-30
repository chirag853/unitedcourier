<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_addresses', function (Blueprint $table) {
            $table->id();
            // customers.id is int(11) in DB, so use integer() (not foreignId/bigint).
            $table->integer('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('pin', 20)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('registered_name', 150)->nullable();
            $table->string('return_address', 255)->nullable();
            $table->string('return_pin', 20)->nullable();
            $table->string('return_city', 100)->nullable();
            $table->string('return_state', 100)->nullable();
            $table->string('return_country', 100)->nullable();
            $table->timestamps();

            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_addresses');
    }
};
