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
        Schema::create('bill_to', function (Blueprint $table) {
            $table->id();

            // shipper_id: unsignedInteger() to match shipper_info.id (int(10) unsigned)
            $table->unsignedInteger('shipper_id');
            $table->foreign('shipper_id')->references('id')->on('shipper_info')->onDelete('cascade');

            // customer_id: integer() to match customers.id (int(11) signed)
            $table->integer('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_to');
    }
};
