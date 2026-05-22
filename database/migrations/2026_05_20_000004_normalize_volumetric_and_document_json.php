<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalize volumetric_calculator_page's `data` JSON column
     * and document_download_page's `content` text column (page meta).
     */
    public function up(): void
    {
        // --- VOLUMETRIC CALCULATOR PAGE ---
        if (Schema::hasColumn('volumetric_calculator_page', 'data')) {
            Schema::table('volumetric_calculator_page', function (Blueprint $table) {
                $table->text('data_title')->nullable()->after('section');
                $table->text('data_description')->nullable()->after('data_title');
                $table->string('data_icon', 500)->nullable()->after('data_description');
                $table->string('data_image', 500)->nullable()->after('data_icon');
                $table->string('data_link', 500)->nullable()->after('data_image');
                $table->string('data_button_text', 200)->nullable()->after('data_link');
                $table->text('data_extra')->nullable()->after('data_button_text');
            });

            DB::table('volumetric_calculator_page')->orderBy('id')->chunk(100, function ($records) {
                foreach ($records as $record) {
                    if (empty($record->data)) continue;
                    $data = is_string($record->data) ? json_decode($record->data, true) : $record->data;
                    if (!is_array($data)) continue;

                    $update = [];
                    if (isset($data['title'])) $update['data_title'] = $data['title'];
                    if (isset($data['description'])) $update['data_description'] = $data['description'];
                    if (isset($data['icon'])) $update['data_icon'] = $data['icon'];
                    if (isset($data['image'])) $update['data_image'] = $data['image'];
                    if (isset($data['link'])) $update['data_link'] = $data['link'];
                    if (isset($data['button_text'])) $update['data_button_text'] = $data['button_text'];

                    $mappedKeys = ['title', 'description', 'icon', 'image', 'link', 'button_text'];
                    $extra = array_diff_key($data, array_flip($mappedKeys));
                    if (!empty($extra)) {
                        $update['data_extra'] = json_encode($extra);
                    }

                    if (!empty($update)) {
                        DB::table('volumetric_calculator_page')->where('id', $record->id)->update($update);
                    }
                }
            });

            DB::statement("ALTER TABLE `volumetric_calculator_page` MODIFY COLUMN `data` TEXT NULL");
        }

        // --- DOCUMENT DOWNLOAD PAGE ---
        // The content column stores page meta as JSON (e.g. {"hero_title": "...", "hero_description": "..."})
        if (Schema::hasColumn('document_download_page', 'content')) {
            Schema::table('document_download_page', function (Blueprint $table) {
                $table->text('page_meta')->nullable()->after('content');
            });

            DB::table('document_download_page')->orderBy('id')->chunk(100, function ($records) {
                foreach ($records as $record) {
                    if (empty($record->content)) continue;
                    $data = is_string($record->content) ? json_decode($record->content, true) : $record->content;
                    if (is_array($data)) {
                        DB::table('document_download_page')
                            ->where('id', $record->id)
                            ->update(['page_meta' => json_encode($data)]);
                    }
                }
            });

            Schema::table('document_download_page', function (Blueprint $table) {
                $table->dropColumn('content');
            });
        }
    }

    public function down(): void
    {
        // --- RESTORE VOLUMETRIC CALCULATOR PAGE ---
        if (Schema::hasColumn('volumetric_calculator_page', 'data_title')) {
            DB::table('volumetric_calculator_page')->orderBy('id')->chunk(100, function ($records) {
                foreach ($records as $record) {
                    $data = [];
                    if ($record->data_title) $data['title'] = $record->data_title;
                    if ($record->data_description) $data['description'] = $record->data_description;
                    if ($record->data_icon) $data['icon'] = $record->data_icon;
                    if ($record->data_image) $data['image'] = $record->data_image;
                    if ($record->data_link) $data['link'] = $record->data_link;
                    if ($record->data_button_text) $data['button_text'] = $record->data_button_text;
                    if ($record->data_extra) {
                        $extra = json_decode($record->data_extra, true);
                        if (is_array($extra)) {
                            $data = array_merge($data, $extra);
                        }
                    }

                    if (!empty($data)) {
                        DB::table('volumetric_calculator_page')
                            ->where('id', $record->id)
                            ->update(['data' => json_encode($data)]);
                    }
                }
            });

            DB::statement("ALTER TABLE `volumetric_calculator_page` MODIFY COLUMN `data` JSON NULL");

            Schema::table('volumetric_calculator_page', function (Blueprint $table) {
                $table->dropColumn([
                    'data_title', 'data_description', 'data_icon',
                    'data_image', 'data_link', 'data_button_text', 'data_extra'
                ]);
            });
        }

        // --- RESTORE DOCUMENT DOWNLOAD PAGE ---
        if (Schema::hasColumn('document_download_page', 'page_meta')) {
            Schema::table('document_download_page', function (Blueprint $table) {
                $table->text('content')->nullable()->after('section');
            });

            DB::table('document_download_page')->orderBy('id')->chunk(100, function ($records) {
                foreach ($records as $record) {
                    if ($record->page_meta) {
                        DB::table('document_download_page')
                            ->where('id', $record->id)
                            ->update(['content' => $record->page_meta]);
                    }
                }
            });

            Schema::table('document_download_page', function (Blueprint $table) {
                $table->dropColumn('page_meta');
            });
        }
    }
};