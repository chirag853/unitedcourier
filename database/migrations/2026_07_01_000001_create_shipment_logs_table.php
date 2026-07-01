<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the shipment_logs table to track every shipment status change
     * (draft → ready → packed → manifested → cancelled, etc.) with timestamps.
     */
    public function up(): void
    {
        Schema::create('shipment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('shipper_id')->index();
            $table->integer('customer_id')->nullable()->index();
            $table->string('awb_number', 100)->index();
            $table->string('status', 50)->index();
            $table->string('previous_status', 50)->nullable();
            $table->string('title', 150)->nullable();
            $table->text('description')->nullable();
            $table->string('performed_by', 100)->nullable()->comment('customer, system, admin, etc.');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_logs');
    }
};
