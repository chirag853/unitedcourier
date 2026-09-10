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
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();

            // Manifest number format: MUWC + YYMMDD + 5-digit sequence (e.g. MUWC26090800001)
            $table->string('manifest_number', 30)->unique()->comment('Auto-generated manifest number: MUWC+YYMMDD+00001');

            // shipper_id: unsignedInteger() to match shipper_info.id (int(10) unsigned)
            $table->unsignedInteger('shipper_id')->index();

            // customer_id: integer() to match customers.id reference pattern used in shipment_logs
            $table->integer('customer_id')->index();

            // Status: 0 = open, 1 = remove, 3 = close, 4 = pickup
            $table->tinyInteger('status')->default(0)->comment('0=open, 1=remove, 3=close, 4=pickup');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifests');
    }
};
