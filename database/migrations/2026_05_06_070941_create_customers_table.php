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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255)->unique();
            $table->string('phone_number', 20)->nullable();
            $table->string('alternate_phone_number', 20)->nullable();
            $table->string('password_hash', 255)->comment('Store hashed password, not plain text');
            $table->string('aadhar_number', 20)->nullable()->comment('12-digit Aadhar number');
            $table->integer('business_category_id')->nullable();
            $table->boolean('is_terms_accepted')->default(false)->comment('Checkbox for Terms of Service');
            $table->boolean('email_verified')->default(false);
            $table->boolean('aadhar_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
