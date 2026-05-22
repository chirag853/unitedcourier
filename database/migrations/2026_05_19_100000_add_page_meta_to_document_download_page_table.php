<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_download_page', function (Blueprint $table) {
            $table->string('section', 100)->nullable()->after('status')->comment('Section identifier (e.g. hero, page_meta)');
            $table->text('content')->nullable()->after('section')->comment('JSON content for page meta sections');
        });
    }

    public function down(): void
    {
        Schema::table('document_download_page', function (Blueprint $table) {
            $table->dropColumn(['section', 'content']);
        });
    }
};