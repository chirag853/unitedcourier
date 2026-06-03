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
        Schema::create('shipment_tracking', function (Blueprint $table) {
            $table->id();

            // customer_id: integer() to match customers.id (int(11) signed)
            $table->integer('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            // shipper_id: unsignedInteger() to match shipper_info.id (int(10) unsigned)
            $table->unsignedInteger('shipper_id')->nullable();
            $table->foreign('shipper_id')->references('id')->on('shipper_info')->onDelete('set null');

            // create_shipment_id: unsignedBigInteger() to match create_shipment.id (bigInteger unsigned from $table->id())
            $table->unsignedBigInteger('create_shipment_id')->nullable();
            $table->foreign('create_shipment_id')->references('id')->on('create_shipment')->onDelete('set null');

            // Response Status
            $table->string('response_status_code', 10)->nullable();
            $table->string('response_status_description', 255)->nullable();

            // Transaction Reference
            $table->string('transaction_identifier', 255)->nullable();
            $table->string('customer_context', 100)->nullable();

            // Shipment Identification
            $table->string('shipment_identification_number', 100)->nullable();

            // Charges
            $table->string('transportation_charges_currency', 10)->nullable();
            $table->decimal('transportation_charges_amount', 15, 2)->nullable();
            $table->string('service_options_charges_currency', 10)->nullable();
            $table->decimal('service_options_charges_amount', 15, 2)->nullable();
            $table->string('total_charges_currency', 10)->nullable();
            $table->decimal('total_charges_amount', 15, 2)->nullable();

            // Billing Weight
            $table->string('billing_weight_uom', 20)->nullable();
            $table->decimal('billing_weight', 10, 2)->nullable();

            // Package Results (JSON for multiple packages)
            $table->json('package_results')->nullable();

            // Full raw response (JSON)
            $table->json('raw_response')->nullable();

            $table->string('status', 50)->default('created');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking');
    }
};