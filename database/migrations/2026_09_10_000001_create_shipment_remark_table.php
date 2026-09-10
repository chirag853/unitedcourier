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
        Schema::create('shipment_remark', function (Blueprint $table) {
            $table->id();

            // customer_id: integer() to match customers.id reference pattern used elsewhere
            $table->integer('customer_id')->index();

            // shipper_id: unsignedInteger() to match shipper_info.id (int(10) unsigned)
            $table->unsignedInteger('shipper_id')->index();

            // Entry remark captured at the time the COD order is created
            $table->text('entry_remark')->nullable()->comment('Remark entered while creating the shipment/COD order');

            // Finance remark filled later by the finance team
            $table->text('finance_remark')->nullable()->comment('Remark filled by the finance team');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_remark');
    }
};
