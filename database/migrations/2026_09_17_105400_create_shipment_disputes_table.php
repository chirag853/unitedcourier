<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Applied dispute charges (admin/companies modal se apply hone wale orders).
     * Yehi rows admin/dispute-orders page par table me dikhengi.
     */
    public function up(): void
    {
        Schema::create('shipment_disputes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipment_invoice_id')->nullable()->index();
            $table->unsignedBigInteger('shipper_id')->nullable()->index();
            $table->string('awb_number')->nullable()->index();
            $table->unsignedBigInteger('dispute_charge_id')->nullable()->index();
            $table->string('charge_type')->nullable();
            $table->text('conditions')->nullable();
            $table->string('destination')->nullable();
            $table->string('service_id')->nullable();
            $table->string('calculation_type')->nullable();
            $table->string('values')->nullable();
            $table->decimal('rate', 12, 2)->nullable();
            $table->unsignedInteger('boxes')->nullable();
            $table->decimal('base_amount', 12, 2)->nullable();
            $table->decimal('gst_percentage', 8, 2)->default(0);
            $table->decimal('gst_amount', 12, 2)->default(0);
            $table->decimal('total_incl_gst', 12, 2)->nullable();
            $table->string('currency', 8)->default('Rs');
            $table->unsignedBigInteger('applied_by')->nullable();
            $table->string('status')->default('applied')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_disputes');
    }
};
