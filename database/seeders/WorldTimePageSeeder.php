<?php

namespace Database\Seeders;

use App\Models\WorldTimePage;
use Illuminate\Database\Seeder;

class WorldTimePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            // ===================== HERO =====================
            [
                'section'     => 'hero',
                'item_key'    => 'hero_content',
                'title'       => 'World Time',
                'description' => 'Check current time across different countries and time zones instantly with accurate and real-time updates.',
                'image'       => 'public/website_images/world-time.webp',
                'content'     => [
                    'title' => 'Global <span class="moving-gradient-text">Time Zones</span>',
                ],
                'sort_order'  => 1,
                'status'      => 'Active',
            ],

            // ===================== TIME CITIES HEADER =====================
            [
                'section'     => 'time_cities',
                'item_key'    => 'time_cities_header',
                'title'       => 'Current Time Around the <span>World</span>',
                'description' => 'Track real-time clocks across major global cities and stay synchronized worldwide.',
                'content'     => [
                    'badge_text' => 'Live World Time',
                    'badge_icon' => 'fa-clock',
                ],
                'sort_order'  => 2,
                'status'      => 'Active',
            ],

            // ===================== CITY CARDS =====================
            [
                'section'     => 'time_cities',
                'item_key'    => 'city_delhi',
                'title'       => 'Delhi',
                'description' => 'IST',
                'content'     => [
                    'emoji'         => '🇮🇳',
                    'timezone'      => 'Asia/Kolkata',
                    'clock_id'      => 'clock-delhi',
                    'timezone_abbr' => 'IST',
                    'color_class'   => 'feat-blue',
                ],
                'sort_order'  => 3,
                'status'      => 'Active',
            ],
            [
                'section'     => 'time_cities',
                'item_key'    => 'city_new_york',
                'title'       => 'New York',
                'description' => 'EST',
                'content'     => [
                    'emoji'         => '🇺🇸',
                    'timezone'      => 'America/New_York',
                    'clock_id'      => 'clock-ny',
                    'timezone_abbr' => 'EST',
                    'color_class'   => 'feat-purple',
                ],
                'sort_order'  => 4,
                'status'      => 'Active',
            ],
            [
                'section'     => 'time_cities',
                'item_key'    => 'city_london',
                'title'       => 'London',
                'description' => 'GMT',
                'content'     => [
                    'emoji'         => '🇬🇧',
                    'timezone'      => 'Europe/London',
                    'clock_id'      => 'clock-london',
                    'timezone_abbr' => 'GMT',
                    'color_class'   => 'feat-green',
                ],
                'sort_order'  => 5,
                'status'      => 'Active',
            ],
            [
                'section'     => 'time_cities',
                'item_key'    => 'city_dubai',
                'title'       => 'Dubai',
                'description' => 'GST',
                'content'     => [
                    'emoji'         => '🇦🇪',
                    'timezone'      => 'Asia/Dubai',
                    'clock_id'      => 'clock-dubai',
                    'timezone_abbr' => 'GST',
                    'color_class'   => 'feat-orange',
                ],
                'sort_order'  => 6,
                'status'      => 'Active',
            ],
            [
                'section'     => 'time_cities',
                'item_key'    => 'city_tokyo',
                'title'       => 'Tokyo',
                'description' => 'JST',
                'content'     => [
                    'emoji'         => '🇯🇵',
                    'timezone'      => 'Asia/Tokyo',
                    'clock_id'      => 'clock-tokyo',
                    'timezone_abbr' => 'JST',
                    'color_class'   => 'feat-blue',
                ],
                'sort_order'  => 7,
                'status'      => 'Active',
            ],
            [
                'section'     => 'time_cities',
                'item_key'    => 'city_sydney',
                'title'       => 'Sydney',
                'description' => 'AEST',
                'content'     => [
                    'emoji'         => '🇦🇺',
                    'timezone'      => 'Australia/Sydney',
                    'clock_id'      => 'clock-sydney',
                    'timezone_abbr' => 'AEST',
                    'color_class'   => 'feat-purple',
                ],
                'sort_order'  => 8,
                'status'      => 'Active',
            ],
            [
                'section'     => 'time_cities',
                'item_key'    => 'city_paris',
                'title'       => 'Paris',
                'description' => 'CET',
                'content'     => [
                    'emoji'         => '🇫🇷',
                    'timezone'      => 'Europe/Paris',
                    'clock_id'      => 'clock-paris',
                    'timezone_abbr' => 'CET',
                    'color_class'   => 'feat-green',
                ],
                'sort_order'  => 9,
                'status'      => 'Active',
            ],
            [
                'section'     => 'time_cities',
                'item_key'    => 'city_singapore',
                'title'       => 'Singapore',
                'description' => 'SGT',
                'content'     => [
                    'emoji'         => '🇸🇬',
                    'timezone'      => 'Asia/Singapore',
                    'clock_id'      => 'clock-singapore',
                    'timezone_abbr' => 'SGT',
                    'color_class'   => 'feat-orange',
                ],
                'sort_order'  => 10,
                'status'      => 'Active',
            ],

            // ===================== FEATURES HEADER =====================
            [
                'section'     => 'features',
                'item_key'    => 'features_header',
                'title'       => 'Importance of World Time Tracking',
                'description' => 'People use world time tools to stay synchronized across different countries and regions. Whether for travel, business meetings, or communication, knowing the exact time in another location is essential. A world time calculator helps users easily check and compare time zones, ensuring better planning and coordination globally.',
                'content'     => [],
                'sort_order'  => 11,
                'status'      => 'Active',
            ],

            // ===================== FEATURE CARDS =====================
            [
                'section'     => 'features',
                'item_key'    => 'feature_card_1',
                'title'       => 'Real-time Global Time',
                'description' => 'Get accurate and up-to-date current time from any country or city around the world instantly.',
                'content'     => [
                    'icon'        => 'fa-clock',
                    'color_class' => 'feat-blue',
                ],
                'sort_order'  => 12,
                'status'      => 'Active',
            ],
            [
                'section'     => 'features',
                'item_key'    => 'feature_card_2',
                'title'       => 'Worldwide Coverage',
                'description' => 'Access time zones from all over the globe and stay connected no matter where you are.',
                'content'     => [
                    'icon'        => 'fa-globe',
                    'color_class' => 'feat-purple',
                ],
                'sort_order'  => 13,
                'status'      => 'Active',
            ],
            [
                'section'     => 'features',
                'item_key'    => 'feature_card_3',
                'title'       => 'Instant Time Convert',
                'description' => 'Easily convert time between different time zones for meetings, travel, or events.',
                'content'     => [
                    'icon'        => 'fa-sync-alt',
                    'color_class' => 'feat-green',
                ],
                'sort_order'  => 14,
                'status'      => 'Active',
            ],
            [
                'section'     => 'features',
                'item_key'    => 'feature_card_4',
                'title'       => 'Smart Time Planning',
                'description' => 'Plan your schedule efficiently by comparing time zones and avoiding confusion across regions.',
                'content'     => [
                    'icon'        => 'fa-user-clock',
                    'color_class' => 'feat-orange',
                ],
                'sort_order'  => 15,
                'status'      => 'Active',
            ],
        ];

        foreach ($records as $record) {
            WorldTimePage::create($record);
        }
    }
}