<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipper_consignee', function (Blueprint $table) {
            $table->id();
            // customers.id is int(11) in DB, so use integer() (not foreignId/bigint).
            $table->integer('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('delivery_destination', 100)->nullable();
            $table->string('origin_type', 50)->nullable();
            $table->string('reason_for_export', 20)->nullable();
            $table->string('business_type', 50)->nullable();
            $table->string('consignee_name', 150)->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->string('address_line1', 255)->nullable();
            $table->string('address_line2', 255)->nullable();
            $table->string('address_line3', 255)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->timestamps();

            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipper_consignee');
    }
};
