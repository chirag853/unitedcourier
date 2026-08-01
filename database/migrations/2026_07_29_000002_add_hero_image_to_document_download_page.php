<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('document_download_page', 'hero_image')) {
            Schema::table('document_download_page', function (Blueprint $table) {
                $table->string('hero_image', 500)->nullable()->after('badge_text');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('document_download_page', 'hero_image')) {
            Schema::table('document_download_page', function (Blueprint $table) {
                $table->dropColumn('hero_image');
            });
        }
    }
};
