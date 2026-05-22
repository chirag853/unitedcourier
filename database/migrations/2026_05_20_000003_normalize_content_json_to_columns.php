<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalize all Pattern A tables (section/item_key/content JSON) by
     * extracting content JSON keys into proper typed columns.
     *
     * Tables affected: service_page, warehousing_solutions_page,
     * ecommerce_logistics_solutions_page, express_air_freight_solutions_page,
     * track_order_page, webinar_page, currency_calculator_page,
     * world_weather_page, world_time_page, partnership_page, ebook_page
     *
     * Some tables (service_page, warehousing_solutions_page, ecommerce, express)
     * do NOT have title/description/image columns; others do.
     */
    public function up(): void
    {
        // Group A: Tables WITH title, description, image columns
        $tablesWithImage = [
            'track_order_page', 'webinar_page', 'currency_calculator_page',
            'world_weather_page', 'world_time_page', 'partnership_page', 'ebook_page',
        ];

        // Group B: Tables WITHOUT title, description, image columns
        $tablesWithoutImage = [
            'service_page', 'warehousing_solutions_page',
            'ecommerce_logistics_solutions_page', 'express_air_freight_solutions_page',
        ];

        // Process Group A (have image column - position new cols after 'image')
        foreach ($tablesWithImage as $table) {
            if (!Schema::hasColumn($table, 'content')) continue;
            $this->addNormalizedColumns($table, 'image');
            $this->migrateContentData($table);
            $this->convertContentToText($table);
        }

        // Process Group B (no image column - position after 'content')
        foreach ($tablesWithoutImage as $table) {
            if (!Schema::hasColumn($table, 'content')) continue;
            $this->addNormalizedColumns($table, 'content');
            $this->migrateContentData($table);
            $this->convertContentToText($table);
        }
    }

    private function addNormalizedColumns(string $table, string $afterColumn): void
    {
        Schema::table($table, function (Blueprint $table) use ($afterColumn) {
            $table->text('icon_svg')->nullable()->after($afterColumn);
            $table->string('icon_class', 100)->nullable()->after('icon_svg');
            $table->string('color_scheme', 100)->nullable()->after('icon_class');

            $table->string('badge_text', 200)->nullable()->after('color_scheme');
            $table->string('button_text', 200)->nullable()->after('badge_text');
            $table->string('button_url', 500)->nullable()->after('button_text');
            $table->string('btn_text', 200)->nullable()->after('button_url');

            $table->text('subtitle')->nullable()->after('btn_text');
            $table->text('paragraphs')->nullable()->after('subtitle');

            $table->string('question', 500)->nullable()->after('paragraphs');
            $table->text('answer')->nullable()->after('question');
            $table->string('name', 255)->nullable()->after('answer');
            $table->string('avatar_url', 500)->nullable()->after('name');
            $table->decimal('rating', 3, 1)->nullable()->after('avatar_url');
            $table->text('text_content')->nullable()->after('rating');

            $table->string('stat_value', 100)->nullable()->after('text_content');
            $table->string('stat_label', 255)->nullable()->after('stat_value');
            $table->string('stat_suffix', 50)->nullable()->after('stat_label');

            $table->string('logo_url', 500)->nullable()->after('stat_suffix');
            $table->string('alt_text', 255)->nullable()->after('logo_url');

            $table->text('list_items_text')->nullable()->after('alt_text');
            $table->text('check_list_text')->nullable()->after('list_items_text');
            $table->text('extra_content')->nullable()->after('check_list_text');
        });
    }

    private function migrateContentData(string $table): void
    {
        DB::table($table)->orderBy('id')->chunk(100, function ($records) use ($table) {
            foreach ($records as $record) {
                if (empty($record->content)) continue;

                $data = is_string($record->content) ? json_decode($record->content, true) : $record->content;
                if (!is_array($data)) continue;

                $update = [];

                // Direct key mappings
                if (isset($data['icon_svg'])) $update['icon_svg'] = $data['icon_svg'];
                if (isset($data['icon_class'])) $update['icon_class'] = $data['icon_class'];
                if (isset($data['icon'])) $update['icon_svg'] = $data['icon'];
                if (isset($data['color_class'])) $update['color_scheme'] = $data['color_class'];
                if (isset($data['color_scheme'])) $update['color_scheme'] = $data['color_scheme'];

                if (isset($data['badge_text'])) $update['badge_text'] = $data['badge_text'];
                if (isset($data['badge'])) $update['badge_text'] = $data['badge'];
                if (isset($data['button_text'])) $update['button_text'] = $data['button_text'];
                if (isset($data['button_url'])) $update['button_url'] = $data['button_url'];
                if (isset($data['btn_text'])) $update['btn_text'] = $data['btn_text'];
                if (isset($data['link']) && empty($record->link)) {
                    $update['button_url'] = $data['link'];
                }

                if (isset($data['subtitle'])) $update['subtitle'] = $data['subtitle'];
                if (isset($data['paragraphs'])) {
                    $update['paragraphs'] = is_array($data['paragraphs'])
                        ? implode("\n", $data['paragraphs'])
                        : $data['paragraphs'];
                }

                if (isset($data['question'])) $update['question'] = $data['question'];
                if (isset($data['answer'])) $update['answer'] = $data['answer'];
                if (isset($data['name'])) $update['name'] = $data['name'];
                if (isset($data['avatar'])) $update['avatar_url'] = $data['avatar'];
                if (isset($data['avatar_url'])) $update['avatar_url'] = $data['avatar_url'];
                if (isset($data['rating'])) $update['rating'] = $data['rating'];
                if (isset($data['text'])) $update['text_content'] = $data['text'];

                if (isset($data['value'])) $update['stat_value'] = $data['value'];
                if (isset($data['label'])) $update['stat_label'] = $data['label'];
                if (isset($data['suffix'])) $update['stat_suffix'] = $data['suffix'];
                if (isset($data['stat_number'])) $update['stat_value'] = $data['stat_number'];
                if (isset($data['stat_label'])) $update['stat_label'] = $data['stat_label'];

                if (isset($data['logo_url'])) $update['logo_url'] = $data['logo_url'];
                if (isset($data['alt'])) $update['alt_text'] = $data['alt'];
                if (isset($data['alt_text'])) $update['alt_text'] = $data['alt_text'];

                if (isset($data['list_items']) && is_array($data['list_items'])) {
                    $update['list_items_text'] = implode("\n", $data['list_items']);
                }
                if (isset($data['check_list']) && is_array($data['check_list'])) {
                    $update['check_list_text'] = implode("\n", $data['check_list']);
                }

                // Store remaining unmapped keys
                $mappedKeys = [
                    'icon_svg', 'icon_class', 'icon', 'color_class', 'color_scheme',
                    'badge_text', 'badge', 'button_text', 'button_url', 'btn_text', 'link',
                    'subtitle', 'paragraphs', 'question', 'answer', 'name', 'avatar',
                    'avatar_url', 'rating', 'text', 'value', 'label', 'suffix',
                    'stat_number', 'stat_label', 'stat_value', 'stat_suffix',
                    'logo_url', 'alt', 'alt_text', 'list_items', 'check_list',
                    'title', 'description', 'image'
                ];
                $extra = array_diff_key($data, array_flip($mappedKeys));
                if (!empty($extra)) {
                    $update['extra_content'] = json_encode($extra);
                }

                if (!empty($update)) {
                    DB::table($table)->where('id', $record->id)->update($update);
                }
            }
        });
    }

    private function convertContentToText(string $table): void
    {
        DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `content` TEXT NULL");
    }

    public function down(): void
    {
        $allTables = [
            'service_page', 'warehousing_solutions_page',
            'ecommerce_logistics_solutions_page', 'express_air_freight_solutions_page',
            'track_order_page', 'webinar_page', 'currency_calculator_page',
            'world_weather_page', 'world_time_page', 'partnership_page', 'ebook_page',
        ];

        foreach ($allTables as $table) {
            if (!Schema::hasColumn($table, 'icon_svg')) continue;

            // Rebuild content JSON from individual columns
            DB::table($table)->orderBy('id')->chunk(100, function ($records) use ($table) {
                foreach ($records as $record) {
                    $data = [];
                    if ($record->title) $data['title'] = $record->title;
                    if ($record->description) $data['description'] = $record->description;
                    if ($record->icon_svg) $data['icon_svg'] = $record->icon_svg;
                    if ($record->icon_class) $data['icon_class'] = $record->icon_class;
                    if ($record->color_scheme) $data['color_class'] = $record->color_scheme;
                    if ($record->badge_text) $data['badge_text'] = $record->badge_text;
                    if ($record->button_text) $data['button_text'] = $record->button_text;
                    if ($record->button_url) $data['button_url'] = $record->button_url;
                    if ($record->btn_text) $data['btn_text'] = $record->btn_text;
                    if ($record->subtitle) $data['subtitle'] = $record->subtitle;
                    if ($record->paragraphs) $data['paragraphs'] = explode("\n", $record->paragraphs);
                    if ($record->question) $data['question'] = $record->question;
                    if ($record->answer) $data['answer'] = $record->answer;
                    if ($record->name) $data['name'] = $record->name;
                    if ($record->avatar_url) $data['avatar'] = $record->avatar_url;
                    if ($record->rating) $data['rating'] = $record->rating;
                    if ($record->text_content) $data['text'] = $record->text_content;
                    if ($record->stat_value) $data['value'] = $record->stat_value;
                    if ($record->stat_label) $data['label'] = $record->stat_label;
                    if ($record->stat_suffix) $data['suffix'] = $record->stat_suffix;
                    if ($record->logo_url) $data['logo_url'] = $record->logo_url;
                    if ($record->alt_text) $data['alt'] = $record->alt_text;
                    if ($record->list_items_text) $data['list_items'] = explode("\n", $record->list_items_text);
                    if ($record->check_list_text) $data['check_list'] = explode("\n", $record->check_list_text);

                    if ($record->extra_content) {
                        $extra = json_decode($record->extra_content, true);
                        if (is_array($extra)) {
                            $data = array_merge($data, $extra);
                        }
                    }

                    if (!empty($data)) {
                        DB::table($table)->where('id', $record->id)->update(['content' => json_encode($data)]);
                    }
                }
            });

            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `content` JSON NULL");

            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn([
                    'icon_svg', 'icon_class', 'color_scheme',
                    'badge_text', 'button_text', 'button_url', 'btn_text',
                    'subtitle', 'paragraphs',
                    'question', 'answer', 'name', 'avatar_url', 'rating', 'text_content',
                    'stat_value', 'stat_label', 'stat_suffix',
                    'logo_url', 'alt_text',
                    'list_items_text', 'check_list_text', 'extra_content'
                ]);
            });
        }
    }
};