<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warehousing_solutions_page', function (Blueprint $table) {
            $table->id();
            $table->string('section_key');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('paragraphs')->nullable();
            $table->json('list_items')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('image')->nullable();
            $table->text('icon_svg')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('stat_number')->nullable();
            $table->string('stat_label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehousing_solutions_page');
    }
};
