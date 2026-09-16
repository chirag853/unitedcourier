<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispute_charges', function (Blueprint $table) {
            $table->id();
            $table->string('additional_charges');
            $table->text('conditions')->nullable();
            $table->string('destination')->default('ALL');
            $table->string('service_id')->default('ALL');
            $table->string('calculation_type')->nullable()->comment('awb, box, kg');
            $table->string('values')->nullable();
            $table->string('place_of_apply')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispute_charges');
    }
};
