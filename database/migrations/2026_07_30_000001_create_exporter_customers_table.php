<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exporter_customers', function (Blueprint $table) {
            $table->id();
            // The existing customers.id column is a signed INT in the live schema.
            $table->integer('exporter_id');
            $table->foreign('exporter_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->string('company_name', 150);
            $table->string('contact_person', 100);
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->string('pincode', 20);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('phone_number', 30);
            $table->string('email', 150);
            $table->boolean('email_opt_out')->default(false);
            $table->string('kyc_type', 50)->nullable();
            $table->string('kyc_number', 100)->nullable();
            $table->timestamps();

            $table->index(['exporter_id', 'company_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exporter_customers');
    }
};
