<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_drafts', function (Blueprint $table) {
            $table->id();
            // The legacy customers.id column is a signed INT, not BIGINT.
            $table->integer('customer_id');
            $table->string('kyc_type', 20);
            $table->unsignedTinyInteger('current_step')->default(1);
            $table->json('form_data')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->cascadeOnDelete();
            $table->unique(['customer_id', 'kyc_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_drafts');
    }
};
