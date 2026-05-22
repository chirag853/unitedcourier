<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->json('detail_sections')->nullable()->after('content');
        });

        // Migrate existing data from blog_detail_page to detail_sections JSON column
        $blogs = DB::table('blogs')->get();
        foreach ($blogs as $blog) {
            $sections = DB::table('blog_detail_page')
                ->where('blog_id', $blog->id)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($section) {
                    return [
                        'section_key' => $section->section_key,
                        'section_title' => $section->section_title,
                        'section_content' => $section->section_content,
                        'section_type' => $section->section_type,
                        'sort_order' => $section->sort_order,
                        'is_active' => (bool) $section->is_active,
                    ];
                })
                ->toArray();

            if (!empty($sections)) {
                DB::table('blogs')
                    ->where('id', $blog->id)
                    ->update(['detail_sections' => json_encode($sections)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn('detail_sections');
        });
    }
};
