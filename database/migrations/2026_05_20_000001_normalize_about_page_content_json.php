<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalize about_page_content by extracting extra_data JSON to proper columns.
     * Keys used across sections: badge_text, target_number, suffix, button_text,
     * tag, color_scheme, year, card_color_class, rating, countries, pin_codes
     */
    public function up(): void
    {
        // Step 1: Add new columns for extra_data keys
        Schema::table('about_page_content', function (Blueprint $table) {
            $table->string('page_badge_text')->nullable()->after('link');
            $table->string('page_target_number', 50)->nullable()->after('page_badge_text');
            $table->string('page_suffix', 50)->nullable()->after('page_target_number');
            $table->string('page_button_text')->nullable()->after('page_suffix');
            $table->string('page_tag', 100)->nullable()->after('page_button_text');
            $table->string('page_color_scheme', 100)->nullable()->after('page_tag');
            $table->string('page_year', 20)->nullable()->after('page_color_scheme');
            $table->string('page_card_color_class', 100)->nullable()->after('page_year');
            $table->decimal('page_rating', 3, 1)->nullable()->after('page_card_color_class');
            $table->text('page_countries')->nullable()->after('page_rating');
            $table->text('page_pin_codes')->nullable()->after('page_countries');
        });

        // Step 2: Migrate existing JSON data to new columns
        $records = DB::table('about_page_content')->whereNotNull('extra_data')->get();
        foreach ($records as $record) {
            $extra = json_decode($record->extra_data, true);
            if (is_array($extra)) {
                $update = [];
                if (isset($extra['badge_text'])) $update['page_badge_text'] = $extra['badge_text'];
                if (isset($extra['target_number'])) $update['page_target_number'] = $extra['target_number'];
                if (isset($extra['suffix'])) $update['page_suffix'] = $extra['suffix'];
                if (isset($extra['button_text'])) $update['page_button_text'] = $extra['button_text'];
                if (isset($extra['tag'])) $update['page_tag'] = $extra['tag'];
                if (isset($extra['color_scheme'])) $update['page_color_scheme'] = $extra['color_scheme'];
                if (isset($extra['year'])) $update['page_year'] = $extra['year'];
                if (isset($extra['card_color_class'])) $update['page_card_color_class'] = $extra['card_color_class'];
                if (isset($extra['rating'])) $update['page_rating'] = $extra['rating'];
                if (isset($extra['countries'])) $update['page_countries'] = is_array($extra['countries']) ? json_encode($extra['countries']) : $extra['countries'];
                if (isset($extra['pin_codes'])) $update['page_pin_codes'] = is_array($extra['pin_codes']) ? json_encode($extra['pin_codes']) : $extra['pin_codes'];
                
                if (!empty($update)) {
                    DB::table('about_page_content')->where('id', $record->id)->update($update);
                }
            }
        }

        // Step 3: Drop the extra_data JSON column
        Schema::table('about_page_content', function (Blueprint $table) {
            $table->dropColumn('extra_data');
        });
    }

    public function down(): void
    {
        Schema::table('about_page_content', function (Blueprint $table) {
            $table->json('extra_data')->nullable()->after('display_order');
        });

        // Restore data from individual columns back to JSON
        $records = DB::table('about_page_content')->get();
        foreach ($records as $record) {
            $extra = [];
            if ($record->page_badge_text) $extra['badge_text'] = $record->page_badge_text;
            if ($record->page_target_number) $extra['target_number'] = $record->page_target_number;
            if ($record->page_suffix) $extra['suffix'] = $record->page_suffix;
            if ($record->page_button_text) $extra['button_text'] = $record->page_button_text;
            if ($record->page_tag) $extra['tag'] = $record->page_tag;
            if ($record->page_color_scheme) $extra['color_scheme'] = $record->page_color_scheme;
            if ($record->page_year) $extra['year'] = $record->page_year;
            if ($record->page_card_color_class) $extra['card_color_class'] = $record->page_card_color_class;
            if ($record->page_rating) $extra['rating'] = $record->page_rating;
            if ($record->page_countries) $extra['countries'] = json_decode($record->page_countries, true) ?? $record->page_countries;
            if ($record->page_pin_codes) $extra['pin_codes'] = json_decode($record->page_pin_codes, true) ?? $record->page_pin_codes;
            
            if (!empty($extra)) {
                DB::table('about_page_content')->where('id', $record->id)->update(['extra_data' => json_encode($extra)]);
            }
        }

        Schema::table('about_page_content', function (Blueprint $table) {
            $table->dropColumn([
                'page_badge_text', 'page_target_number', 'page_suffix',
                'page_button_text', 'page_tag', 'page_color_scheme',
                'page_year', 'page_card_color_class', 'page_rating',
                'page_countries', 'page_pin_codes'
            ]);
        });
    }
};