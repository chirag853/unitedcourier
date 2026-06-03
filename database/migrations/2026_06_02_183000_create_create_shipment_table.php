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
        Schema::create('create_shipment', function (Blueprint $table) {
            $table->id();
            
            // Use integer() for customer_id to match customers.id (int(11) signed)
            $table->integer('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            
            // use unsignedInteger() for shipper_id to match shipper_info.id (int(10) unsigned)
            $table->unsignedInteger('shipper_id');
            $table->foreign('shipper_id')->references('id')->on('shipper_info')->onDelete('cascade');

            $table->string('awb_number', 50)->nullable();
            $table->string('delivery_destination', 100)->nullable();
            $table->string('origin_type', 50)->nullable();
            $table->string('shipping_method', 100)->nullable();
            $table->boolean('shipper_same_as_customer')->default(false);
            $table->string('shipper_company_name', 150)->nullable();
            $table->string('shipper_contact_person', 100)->nullable();
            $table->string('shipper_address_line1', 255)->nullable();
            $table->string('shipper_address_line2', 255)->nullable();
            $table->string('shipper_address_line3', 255)->nullable();
            $table->string('shipper_pincode', 20)->nullable();
            $table->string('shipper_city', 100)->nullable();
            $table->string('shipper_state', 100)->nullable();
            $table->string('shipper_phone_number', 30)->nullable();
            $table->string('shipper_email', 150)->nullable();
            $table->boolean('shipper_email_opt_out')->default(false);
            $table->string('shipper_kyc_type', 50)->nullable();
            $table->string('shipper_kyc_number', 100)->nullable();

            // Consignee fields
            $table->string('consignee_name', 150)->nullable();
            $table->string('consignee_contact_person', 100)->nullable();
            $table->string('consignee_address_line1', 255)->nullable();
            $table->string('consignee_address_line2', 255)->nullable();
            $table->string('consignee_address_line3', 255)->nullable();
            $table->string('consignee_zip_code', 20)->nullable();
            $table->string('consignee_city', 100)->nullable();
            $table->string('consignee_state', 100)->nullable();
            $table->string('consignee_phone_number', 30)->nullable();
            $table->string('consignee_email', 150)->nullable();
            $table->boolean('consignee_email_opt_out')->default(false);

            // Invoice fields
            $table->string('invoice_number', 100)->nullable();
            $table->date('invoice_date')->nullable();
            $table->decimal('invoice_amount', 15, 2)->nullable();
            $table->string('incoterms', 50)->nullable();
            $table->string('invoice_currency', 20)->nullable();
            $table->string('reference_number', 100)->nullable();

            // CSB fields
            $table->string('ecommerce', 10)->nullable();
            $table->string('scheme', 10)->nullable();
            $table->string('bond_ut_igst', 20)->nullable();
            $table->string('lut_number', 100)->nullable();
            $table->string('iec_code', 50)->nullable();
            $table->string('gst_number', 50)->nullable();
            $table->string('ad_code', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_ifsc_code', 20)->nullable();

            $table->string('status', 50)->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('create_shipment');
    }
};