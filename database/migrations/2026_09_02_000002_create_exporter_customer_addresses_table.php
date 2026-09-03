<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exporter_customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exporter_customer_id')
                ->constrained('exporter_customers')
                ->cascadeOnDelete();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->string('pincode', 20);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->timestamps();

            $table->index('exporter_customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exporter_customer_addresses');
    }
};
