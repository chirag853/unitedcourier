<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalize privacy_policy_page, refund_and_cancellation_policy_page,
     * and terms_and_condition_page by converting list_items JSON to TEXT.
     * Also normalize contact_us_page's JSON columns.
     */
    public function up(): void
    {
        // --- PRIVACY POLICY PAGE ---
        Schema::table('privacy_policy_page', function (Blueprint $table) {
            $table->text('list_items_text')->nullable()->after('paragraphs');
        });

        DB::table('privacy_policy_page')->orderBy('id')->chunk(100, function ($records) {
            foreach ($records as $record) {
                if ($record->list_items) {
                    $items = json_decode($record->list_items, true);
                    if (is_array($items)) {
                        DB::table('privacy_policy_page')
                            ->where('id', $record->id)
                            ->update(['list_items_text' => implode("\n", $items)]);
                    }
                }
            }
        });

        Schema::table('privacy_policy_page', function (Blueprint $table) {
            $table->dropColumn('list_items');
        });

        // --- REFUND AND CANCELLATION POLICY PAGE ---
        Schema::table('refund_and_cancellation_policy_page', function (Blueprint $table) {
            $table->text('list_items_text')->nullable()->after('paragraphs');
        });

        DB::table('refund_and_cancellation_policy_page')->orderBy('id')->chunk(100, function ($records) {
            foreach ($records as $record) {
                if ($record->list_items) {
                    $items = json_decode($record->list_items, true);
                    if (is_array($items)) {
                        DB::table('refund_and_cancellation_policy_page')
                            ->where('id', $record->id)
                            ->update(['list_items_text' => implode("\n", $items)]);
                    }
                }
            }
        });

        Schema::table('refund_and_cancellation_policy_page', function (Blueprint $table) {
            $table->dropColumn('list_items');
        });

        // --- TERMS AND CONDITION PAGE ---
        Schema::table('terms_and_condition_page', function (Blueprint $table) {
            $table->text('list_items_text')->nullable()->after('paragraphs');
        });

        DB::table('terms_and_condition_page')->orderBy('id')->chunk(100, function ($records) {
            foreach ($records as $record) {
                if ($record->list_items) {
                    $items = json_decode($record->list_items, true);
                    if (is_array($items)) {
                        DB::table('terms_and_condition_page')
                            ->where('id', $record->id)
                            ->update(['list_items_text' => implode("\n", $items)]);
                    }
                }
            }
        });

        Schema::table('terms_and_condition_page', function (Blueprint $table) {
            $table->dropColumn('list_items');
        });

        // --- CONTACT US PAGE ---
        // Add new columns for list_items, phone_numbers, email_addresses, social_links
        Schema::table('contact_us_page', function (Blueprint $table) {
            $table->text('list_items_text')->nullable()->after('paragraphs');
            $table->text('phone_numbers_text')->nullable()->after('list_items_text');
            $table->text('email_addresses_text')->nullable()->after('phone_numbers_text');
            $table->text('social_links_text')->nullable()->after('email_addresses_text');
        });

        DB::table('contact_us_page')->orderBy('id')->chunk(100, function ($records) {
            foreach ($records as $record) {
                $updates = [];
                if ($record->list_items) {
                    $items = json_decode($record->list_items, true);
                    if (is_array($items)) {
                        $updates['list_items_text'] = implode("\n", $items);
                    }
                }
                if ($record->phone_numbers) {
                    $items = json_decode($record->phone_numbers, true);
                    if (is_array($items)) {
                        $updates['phone_numbers_text'] = implode("\n", $items);
                    }
                }
                if ($record->email_addresses) {
                    $items = json_decode($record->email_addresses, true);
                    if (is_array($items)) {
                        $updates['email_addresses_text'] = implode("\n", $items);
                    }
                }
                if ($record->social_links) {
                    $items = json_decode($record->social_links, true);
                    if (is_array($items)) {
                        // Store social links as JSON-encoded string since they have icon/url/text structure
                        $updates['social_links_text'] = $record->social_links;
                    }
                }
                if (!empty($updates)) {
                    DB::table('contact_us_page')->where('id', $record->id)->update($updates);
                }
            }
        });

        Schema::table('contact_us_page', function (Blueprint $table) {
            $table->dropColumn(['list_items', 'phone_numbers', 'email_addresses', 'social_links']);
        });
    }

    public function down(): void
    {
        // Reverse privacy_policy_page
        Schema::table('privacy_policy_page', function (Blueprint $table) {
            $table->json('list_items')->nullable()->after('paragraphs');
        });
        DB::table('privacy_policy_page')->orderBy('id')->chunk(100, function ($records) {
            foreach ($records as $record) {
                if ($record->list_items_text) {
                    $items = explode("\n", $record->list_items_text);
                    DB::table('privacy_policy_page')
                        ->where('id', $record->id)
                        ->update(['list_items' => json_encode($items)]);
                }
            }
        });
        Schema::table('privacy_policy_page', function (Blueprint $table) {
            $table->dropColumn('list_items_text');
        });

        // Reverse refund_and_cancellation_policy_page
        Schema::table('refund_and_cancellation_policy_page', function (Blueprint $table) {
            $table->json('list_items')->nullable()->after('paragraphs');
        });
        DB::table('refund_and_cancellation_policy_page')->orderBy('id')->chunk(100, function ($records) {
            foreach ($records as $record) {
                if ($record->list_items_text) {
                    $items = explode("\n", $record->list_items_text);
                    DB::table('refund_and_cancellation_policy_page')
                        ->where('id', $record->id)
                        ->update(['list_items' => json_encode($items)]);
                }
            }
        });
        Schema::table('refund_and_cancellation_policy_page', function (Blueprint $table) {
            $table->dropColumn('list_items_text');
        });

        // Reverse terms_and_condition_page
        Schema::table('terms_and_condition_page', function (Blueprint $table) {
            $table->json('list_items')->nullable()->after('paragraphs');
        });
        DB::table('terms_and_condition_page')->orderBy('id')->chunk(100, function ($records) {
            foreach ($records as $record) {
                if ($record->list_items_text) {
                    $items = explode("\n", $record->list_items_text);
                    DB::table('terms_and_condition_page')
                        ->where('id', $record->id)
                        ->update(['list_items' => json_encode($items)]);
                }
            }
        });
        Schema::table('terms_and_condition_page', function (Blueprint $table) {
            $table->dropColumn('list_items_text');
        });

        // Reverse contact_us_page
        Schema::table('contact_us_page', function (Blueprint $table) {
            $table->json('list_items')->nullable()->after('paragraphs');
            $table->json('phone_numbers')->nullable()->after('list_items');
            $table->json('email_addresses')->nullable()->after('phone_numbers');
            $table->json('social_links')->nullable()->after('email_addresses');
        });
        DB::table('contact_us_page')->orderBy('id')->chunk(100, function ($records) {
            foreach ($records as $record) {
                $updates = [];
                if ($record->list_items_text) {
                    $updates['list_items'] = json_encode(explode("\n", $record->list_items_text));
                }
                if ($record->phone_numbers_text) {
                    $updates['phone_numbers'] = json_encode(explode("\n", $record->phone_numbers_text));
                }
                if ($record->email_addresses_text) {
                    $updates['email_addresses'] = json_encode(explode("\n", $record->email_addresses_text));
                }
                if ($record->social_links_text) {
                    $updates['social_links'] = $record->social_links_text;
                }
                if (!empty($updates)) {
                    DB::table('contact_us_page')->where('id', $record->id)->update($updates);
                }
            }
        });
        Schema::table('contact_us_page', function (Blueprint $table) {
            $table->dropColumn(['list_items_text', 'phone_numbers_text', 'email_addresses_text', 'social_links_text']);
        });
    }
};