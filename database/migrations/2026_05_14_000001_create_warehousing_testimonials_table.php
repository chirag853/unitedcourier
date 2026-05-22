<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warehousing_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('section', 50)->default('testimonials');
            $table->string('item_key', 100)->nullable();
            $table->json('content')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['section', 'item_key']);
            $table->index(['section', 'is_active', 'sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehousing_testimonials');
    }
};
