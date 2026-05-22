<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurrencyCalculatorPage;

class CurrencyCalculatorPageSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            // ===== Hero Section (section='hero') =====
            [
                'section' => 'hero',
                'item_key' => 'hero_content',
                'title' => 'Convert Currency',
                'description' => 'Just enter your Mobile Number, AWB tracking number or Order ID & it\'s done.',
                'image' => 'images/currency-calculator.webp',
                'link' => null,
                'content' => [
                    'title' => 'Currency <span class="moving-gradient-text">Calculator</span>',
                ],
                'sort_order' => 0,
                'status' => 'Active',
            ],

            // ===== Features Section Header (section='features', item_key='features_header') =====
            [
                'section' => 'features',
                'item_key' => 'features_header',
                'title' => 'Role of Currency Converters in Global Transactions',
                'description' => 'Why do people use currency converter One of the most general reasons is the following: people may plan to move from one country to another, because of which they will be unable to use their domestic currency. So, the currencies may be converted due to the need of usage in other territories, where the domestic one may not be accepted. An online Currency Exchange Rate Conversion Calculator is good to use for business, personal, or educational purposes.',
                'image' => null,
                'link' => null,
                'content' => null,
                'sort_order' => 1,
                'status' => 'Active',
            ],

            // ===== Feature Cards (section='features', item_key='feature_card_*') =====
            [
                'section' => 'features',
                'item_key' => 'feature_card_1',
                'title' => 'Real-time Exchange Rates',
                'description' => 'Get accurate and up-to-date currency exchange rates instantly for seamless global conversions.',
                'image' => null,
                'link' => null,
                'content' => [
                    'icon' => 'fa-dollar-sign',
                    'color_class' => 'feat-blue',
                ],
                'sort_order' => 2,
                'status' => 'Active',
            ],
            [
                'section' => 'features',
                'item_key' => 'feature_card_2',
                'title' => 'Reliable, Trusted & Secure',
                'description' => 'Our system ensures trusted data sources and secure calculations for dependable currency conversions.',
                'image' => null,
                'link' => null,
                'content' => [
                    'icon' => 'fa-shield-alt',
                    'color_class' => 'feat-purple',
                ],
                'sort_order' => 3,
                'status' => 'Active',
            ],
            [
                'section' => 'features',
                'item_key' => 'feature_card_3',
                'title' => 'Instant Currencies Conversion',
                'description' => 'Convert currencies in seconds with fast processing and accurate results for any amount worldwide.',
                'image' => null,
                'link' => null,
                'content' => [
                    'icon' => 'fa-exchange-alt',
                    'color_class' => 'feat-green',
                ],
                'sort_order' => 4,
                'status' => 'Active',
            ],
            [
                'section' => 'features',
                'item_key' => 'feature_card_4',
                'title' => 'Multi-Currency Support',
                'description' => 'Access a wide range of global currencies and convert between them easily for personal, business, or travel needs.',
                'image' => null,
                'link' => null,
                'content' => [
                    'icon' => 'fa-globe',
                    'color_class' => 'feat-orange',
                ],
                'sort_order' => 5,
                'status' => 'Active',
            ],
        ];

        foreach ($records as $record) {
            CurrencyCalculatorPage::create($record);
        }
    }
}