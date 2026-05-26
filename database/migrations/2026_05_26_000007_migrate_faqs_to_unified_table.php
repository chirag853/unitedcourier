<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ================================================================
        // 1. HOME PAGE (home_page table) - section='faq', field_name in
        //    ('question','answer'), grouped by sort_order
        // ================================================================
        $homeFaqs = DB::table('home_page')
            ->where('section', 'faq')
            ->whereIn('field_name', ['question', 'answer'])
            ->orderBy('sort_order')
            ->get();

        $homeGrouped = $homeFaqs->groupBy('sort_order');
        foreach ($homeGrouped as $sortOrder => $pair) {
            $question = $pair->firstWhere('field_name', 'question')->content ?? '';
            $answer   = $pair->firstWhere('field_name', 'answer')->content ?? '';
            if (!empty($question) && !empty($answer)) {
                DB::table('faq')->insert([
                    'question'   => $question,
                    'answer'     => $answer,
                    'page'       => 'home',
                    'sort_order' => (int) $sortOrder,
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ================================================================
        // 2. ABOUT PAGE (about_page_content table) - section_type='faq'
        //    Columns: title (question), description (answer), display_order
        // ================================================================
        $aboutFaqs = DB::table('about_page_content')
            ->where('section_type', 'faq')
            ->orderBy('display_order')
            ->get();

        foreach ($aboutFaqs as $faq) {
            DB::table('faq')->insert([
                'question'   => $faq->title ?? '',
                'answer'     => $faq->description ?? '',
                'page'       => 'about',
                'sort_order' => (int) ($faq->display_order ?? 0),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ================================================================
        // 3. SERVICE PAGE (service_page table) - section='faq'
        //    JSON content column with 'question' and 'answer' keys
        // ================================================================
        $serviceFaqs = DB::table('service_page')
            ->where('section', 'faq')
            ->orderBy('sort_order')
            ->get();

        foreach ($serviceFaqs as $faq) {
            $content = json_decode($faq->content ?? '{}', true);
            $question = $content['question'] ?? ($faq->question ?? '');
            $answer   = $content['answer'] ?? ($faq->answer ?? '');
            if (!empty($question) || !empty($answer)) {
                DB::table('faq')->insert([
                    'question'   => $question,
                    'answer'     => $answer,
                    'page'       => 'service',
                    'sort_order' => (int) ($faq->sort_order ?? 0),
                    'is_active'  => (bool) ($faq->is_active ?? true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ================================================================
        // 4. VOLUMETRIC CALCULATOR (volumetric_calculator_page) -
        //    section='faq', data_title (question), data_description (answer)
        // ================================================================
        $volFaqs = DB::table('volumetric_calculator_page')
            ->where('section', 'faq')
            ->orderBy('sort_order')
            ->get();

        foreach ($volFaqs as $faq) {
            $data = json_decode($faq->data ?? '{}', true);
            $question = $data['question'] ?? ($faq->data_title ?? '');
            $answer   = $data['answer'] ?? ($faq->data_description ?? '');
            if (!empty($question) || !empty($answer)) {
                DB::table('faq')->insert([
                    'question'   => $question,
                    'answer'     => $answer,
                    'page'       => 'volumetric-calculator',
                    'sort_order' => (int) ($faq->sort_order ?? 0),
                    'is_active'  => (bool) ($faq->is_active ?? true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ================================================================
        // 5. PARTNERSHIP (partnership_page table) - section='faq_item'
        //    Columns: title (question), description (answer)
        // ================================================================
        $partnerFaqs = DB::table('partnership_page')
            ->where('section', 'faq_item')
            ->orderBy('sort_order')
            ->get();

        foreach ($partnerFaqs as $faq) {
            DB::table('faq')->insert([
                'question'   => $faq->title ?? '',
                'answer'     => $faq->description ?? '',
                'page'       => 'partnership',
                'sort_order' => (int) ($faq->sort_order ?? 0),
                'is_active'  => (bool) ($faq->status === 'active' || $faq->status === '1' || $faq->status === 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ================================================================
        // 6. WAREHOUSING SOLUTIONS (warehousing_solutions_page) -
        //    section='faq', content JSON with 'subtitle'/'description' keys
        //    EXCLUDE the header record (has 'badge' or 'title' in content)
        // ================================================================
        $warehouseFaqs = DB::table('warehousing_solutions_page')
            ->where('section', 'faq')
            ->orderBy('sort_order')
            ->get();

        foreach ($warehouseFaqs as $faq) {
            $content = json_decode($faq->content ?? '{}', true);
            // Skip header records that have badge/title keys (section heading)
            if (!empty($content['badge']) || !empty($content['title'])) {
                continue;
            }
            $question = $content['subtitle'] ?? $content['question'] ?? ($faq->subtitle ?? $faq->question ?? '');
            $answer   = $content['description'] ?? $content['answer'] ?? ($faq->description ?? $faq->answer ?? '');
            if (!empty($question) || !empty($answer)) {
                DB::table('faq')->insert([
                    'question'   => $question,
                    'answer'     => $answer,
                    'page'       => 'warehousing',
                    'sort_order' => (int) ($faq->sort_order ?? 0),
                    'is_active'  => (bool) ($faq->is_active ?? true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ================================================================
        // 7. ECOMMERCE LOGISTICS (ecommerce_logistics_solutions_page) -
        //    section='faq', item_key != 'faq_header'
        //    content JSON with 'question' and 'answer' keys
        // ================================================================
        $ecomFaqs = DB::table('ecommerce_logistics_solutions_page')
            ->where('section', 'faq')
            ->where('item_key', '!=', 'faq_header')
            ->orderBy('sort_order')
            ->get();

        foreach ($ecomFaqs as $faq) {
            $content = json_decode($faq->content ?? '{}', true);
            $question = $content['question'] ?? ($faq->question ?? '');
            $answer   = $content['answer'] ?? ($faq->answer ?? '');
            if (!empty($question) || !empty($answer)) {
                DB::table('faq')->insert([
                    'question'   => $question,
                    'answer'     => $answer,
                    'page'       => 'ecommerce-logistics',
                    'sort_order' => (int) ($faq->sort_order ?? 0),
                    'is_active'  => (bool) ($faq->is_active ?? true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ================================================================
        // 8. EXPRESS AIR FREIGHT (express_air_freight_solutions_page) -
        //    section='faq', item_key != 'faq_header'
        //    content JSON with 'question' and 'answer' keys
        // ================================================================
        $expressFaqs = DB::table('express_air_freight_solutions_page')
            ->where('section', 'faq')
            ->where('item_key', '!=', 'faq_header')
            ->orderBy('sort_order')
            ->get();

        foreach ($expressFaqs as $faq) {
            $content = json_decode($faq->content ?? '{}', true);
            $question = $content['question'] ?? ($faq->question ?? '');
            $answer   = $content['answer'] ?? ($faq->answer ?? '');
            if (!empty($question) || !empty($answer)) {
                DB::table('faq')->insert([
                    'question'   => $question,
                    'answer'     => $answer,
                    'page'       => 'express-air-freight',
                    'sort_order' => (int) ($faq->sort_order ?? 0),
                    'is_active'  => (bool) ($faq->is_active ?? true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ================================================================
        // 9. TRACK ORDER (track_order_page) -
        //    section='faq', item_key != 'faq_header'
        //    content JSON with 'question'/'answer' keys (or fallback to
        //    title/description columns)
        // ================================================================
        $trackFaqs = DB::table('track_order_page')
            ->where('section', 'faq')
            ->where('item_key', '!=', 'faq_header')
            ->orderBy('sort_order')
            ->get();

        foreach ($trackFaqs as $faq) {
            $content = json_decode($faq->content ?? '{}', true);
            $question = $content['question'] ?? ($faq->title ?? '');
            $answer   = $content['answer'] ?? ($faq->description ?? '');
            if (!empty($question) || !empty($answer)) {
                DB::table('faq')->insert([
                    'question'   => $question,
                    'answer'     => $answer,
                    'page'       => 'track-order',
                    'sort_order' => (int) ($faq->sort_order ?? 0),
                    'is_active'  => (bool) ($faq->status === 'active' || $faq->status === '1' || $faq->status === 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ================================================================
        // 10. E-BOOKS (ebook_page) -
        //     section='faq', item_key != 'faq_header'
        //     content JSON with 'question' and 'answer' keys
        // ================================================================
        $ebookFaqs = DB::table('ebook_page')
            ->where('section', 'faq')
            ->where('item_key', '!=', 'faq_header')
            ->orderBy('sort_order')
            ->get();

        foreach ($ebookFaqs as $faq) {
            $content = json_decode($faq->content ?? '{}', true);
            $question = $content['question'] ?? ($faq->question ?? '');
            $answer   = $content['answer'] ?? ($faq->answer ?? '');
            if (!empty($question) || !empty($answer)) {
                DB::table('faq')->insert([
                    'question'   => $question,
                    'answer'     => $answer,
                    'page'       => 'e-books',
                    'sort_order' => (int) ($faq->sort_order ?? 0),
                    'is_active'  => (bool) ($faq->status === 'active' || $faq->status === '1' || $faq->status === 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ================================================================
        // 11. BARCODE GENERATOR (barcode_generator_page) -
        //     section_type='faq', status=true
        //     Columns: title (question), description (answer), display_order
        // ================================================================
        $barcodeFaqs = DB::table('barcode_generator_page')
            ->where('section_type', 'faq')
            ->where('status', true)
            ->orderBy('display_order')
            ->get();

        foreach ($barcodeFaqs as $faq) {
            DB::table('faq')->insert([
                'question'   => $faq->title ?? '',
                'answer'     => $faq->description ?? '',
                'page'       => 'barcode-generator',
                'sort_order' => (int) ($faq->display_order ?? 0),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ================================================================
        // 12. SHIPPING RATE CALCULATOR (shipping_rate_calculator_page) -
        //     section_type='faq', status=true
        //     Columns: title (question), description (answer), display_order
        // ================================================================
        $shippingFaqs = DB::table('shipping_rate_calculator_page')
            ->where('section_type', 'faq')
            ->where('status', true)
            ->orderBy('display_order')
            ->get();

        foreach ($shippingFaqs as $faq) {
            DB::table('faq')->insert([
                'question'   => $faq->title ?? '',
                'answer'     => $faq->description ?? '',
                'page'       => 'shipping-rate-calculator',
                'sort_order' => (int) ($faq->display_order ?? 0),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ================================================================
        // 13. HSN FINDER (hsn_finder_page) -
        //     section_type='faq', status=true
        //     Columns: title (question), description (answer), display_order
        // ================================================================
        $hsnFaqs = DB::table('hsn_finder_page')
            ->where('section_type', 'faq')
            ->where('status', true)
            ->orderBy('display_order')
            ->get();

        foreach ($hsnFaqs as $faq) {
            DB::table('faq')->insert([
                'question'   => $faq->title ?? '',
                'answer'     => $faq->description ?? '',
                'page'       => 'hsn-finder',
                'sort_order' => (int) ($faq->display_order ?? 0),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Delete all FAQs that were migrated from page-specific tables
        $pages = [
            'home', 'about', 'service', 'volumetric-calculator',
            'partnership', 'warehousing', 'ecommerce-logistics',
            'express-air-freight', 'track-order', 'e-books',
            'barcode-generator', 'shipping-rate-calculator', 'hsn-finder',
        ];
        DB::table('faq')->whereIn('page', $pages)->delete();
    }
};