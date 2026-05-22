<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('home_page', function (Blueprint $table) {
            $table->id();
            $table->string('section', 100)->comment('Section: hero, about, process, service_card, faq, testimonial');
            $table->string('field_name', 100)->comment('Field jaise title, description, question, answer');
            $table->text('content')->comment('Actual text ya HTML content');
            $table->integer('sort_order')->default(0)->comment('Ordering for lists (faq, steps, cards)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('home_page');
    }
};
