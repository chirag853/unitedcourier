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
        Schema::create('csb_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->comment('Reference to the customer');
            $table->boolean('is_csb_v')->default(true)->comment('CSB V toggle (1 = CSB V, 0 = CSB IV)');
            $table->boolean('is_gst')->default(true)->comment('GST selected');
            $table->boolean('is_lut')->default(false)->comment('LUT selected');
            $table->string('ad_code', 50)->comment('AD Code');
            $table->string('iec_number', 50)->comment('IEC Number');
            $table->string('bank_account_number', 50)->comment('Bank Account Number');
            $table->string('lut_document', 255)->nullable()->comment('Uploaded LUT file path/name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csb_forms');
    }
};
