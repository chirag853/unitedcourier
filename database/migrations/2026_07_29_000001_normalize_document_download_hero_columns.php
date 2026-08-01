<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('document_download_page', 'badge_text')) {
            Schema::table('document_download_page', function (Blueprint $table) {
                $table->string('badge_text', 255)->nullable()->after('section');
            });
        }

        DB::table('document_download_page')
            ->where('section', 'page_meta')
            ->orderBy('id')
            ->chunkById(100, function ($records) {
                foreach ($records as $record) {
                    $meta = !empty($record->page_meta)
                        ? json_decode($record->page_meta, true)
                        : [];

                    if (!is_array($meta)) {
                        $meta = [];
                    }

                    DB::table('document_download_page')
                        ->where('id', $record->id)
                        ->update([
                            'badge_text' => $record->badge_text ?? ($meta['badge'] ?? null),
                            'title' => $record->title ?? ($meta['title'] ?? null),
                            'description' => $record->description ?? ($meta['description'] ?? null),
                            'page_meta' => null,
                        ]);
                }
            });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('document_download_page', 'badge_text')) {
            return;
        }

        DB::table('document_download_page')
            ->where('section', 'page_meta')
            ->orderBy('id')
            ->chunkById(100, function ($records) {
                foreach ($records as $record) {
                    DB::table('document_download_page')
                        ->where('id', $record->id)
                        ->update([
                            'page_meta' => json_encode([
                                'badge' => $record->badge_text,
                                'title' => $record->title,
                                'description' => $record->description,
                            ]),
                        ]);
                }
            });

        Schema::table('document_download_page', function (Blueprint $table) {
            $table->dropColumn('badge_text');
        });
    }
};
