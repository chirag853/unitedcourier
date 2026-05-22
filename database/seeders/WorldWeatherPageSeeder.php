<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorldWeatherPage;

class WorldWeatherPageSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            // ===== Hero Section =====
            [
                'section' => 'hero',
                'item_key' => 'hero_content',
                'title' => 'World Weather',
                'description' => 'Check current weather conditions across different countries and cities instantly with accurate and real-time updates.',
                'image' => 'public/website_images/weather.webp',
                'link' => null,
                'content' => [
                    'title' => 'Global <span class="moving-gradient-text">Weather Updates</span>',
                ],
                'sort_order' => 0,
                'status' => 'Active',
            ],

            // ===== Weather Cities Section Header =====
            [
                'section' => 'weather_cities',
                'item_key' => 'weather_cities_header',
                'title' => '🌦️ World Weather',
                'description' => null,
                'image' => null,
                'link' => null,
                'content' => null,
                'sort_order' => 1,
                'status' => 'Active',
            ],

            // ===== Weather City Cards =====
            [
                'section' => 'weather_cities',
                'item_key' => 'city_1',
                'title' => 'Delhi',
                'description' => null,
                'image' => null,
                'link' => null,
                'content' => [
                    'emoji' => '🇮🇳',
                    'condition' => 'Sunny',
                    'temp' => 35,
                    'color_class' => 'feat-blue',
                    'lat' => 28.6139,
                    'lon' => 77.2090,
                ],
                'sort_order' => 2,
                'status' => 'Active',
            ],
            [
                'section' => 'weather_cities',
                'item_key' => 'city_2',
                'title' => 'New York',
                'description' => null,
                'image' => null,
                'link' => null,
                'content' => [
                    'emoji' => '🇺🇸',
                    'condition' => 'Cloudy',
                    'temp' => 22,
                    'color_class' => 'feat-purple',
                    'lat' => 40.7128,
                    'lon' => -74.0060,
                ],
                'sort_order' => 3,
                'status' => 'Active',
            ],
            [
                'section' => 'weather_cities',
                'item_key' => 'city_3',
                'title' => 'London',
                'description' => null,
                'image' => null,
                'link' => null,
                'content' => [
                    'emoji' => '🇬🇧',
                    'condition' => 'Rainy',
                    'temp' => 18,
                    'color_class' => 'feat-green',
                    'lat' => 51.5074,
                    'lon' => -0.1278,
                ],
                'sort_order' => 4,
                'status' => 'Active',
            ],
            [
                'section' => 'weather_cities',
                'item_key' => 'city_4',
                'title' => 'Tokyo',
                'description' => null,
                'image' => null,
                'link' => null,
                'content' => [
                    'emoji' => '🇯🇵',
                    'condition' => 'Clear',
                    'temp' => 26,
                    'color_class' => 'feat-orange',
                    'lat' => 35.6762,
                    'lon' => 139.6503,
                ],
                'sort_order' => 5,
                'status' => 'Active',
            ],

            // ===== Features Section Header =====
            [
                'section' => 'features',
                'item_key' => 'features_header',
                'title' => 'Importance of World Weather Tracking',
                'description' => 'People use world weather tools to stay informed about climate conditions across different regions. Whether for travel, business planning, or daily activities, knowing the weather in another location is essential. A world weather tool helps users easily check and compare weather conditions, ensuring better preparation and decision-making globally.',
                'image' => null,
                'link' => null,
                'content' => null,
                'sort_order' => 6,
                'status' => 'Active',
            ],

            // ===== Feature Cards =====
            [
                'section' => 'features',
                'item_key' => 'feature_card_1',
                'title' => 'Real-time Weather Updates',
                'description' => 'Get accurate and up-to-date weather conditions from any country or city around the world instantly.',
                'image' => null,
                'link' => null,
                'content' => [
                    'icon' => 'fa-cloud-sun',
                    'color_class' => 'feat-blue',
                ],
                'sort_order' => 7,
                'status' => 'Active',
            ],
            [
                'section' => 'features',
                'item_key' => 'feature_card_2',
                'title' => 'Worldwide Coverage',
                'description' => 'Access weather data from all over the globe and stay informed no matter where you are.',
                'image' => null,
                'link' => null,
                'content' => [
                    'icon' => 'fa-globe',
                    'color_class' => 'feat-purple',
                ],
                'sort_order' => 8,
                'status' => 'Active',
            ],
            [
                'section' => 'features',
                'item_key' => 'feature_card_3',
                'title' => 'Instant Weather Insights',
                'description' => 'Quickly check temperature, humidity, and conditions for better travel and daily planning.',
                'image' => null,
                'link' => null,
                'content' => [
                    'icon' => 'fa-sync-alt',
                    'color_class' => 'feat-green',
                ],
                'sort_order' => 9,
                'status' => 'Active',
            ],
            [
                'section' => 'features',
                'item_key' => 'feature_card_4',
                'title' => 'Smart Planning',
                'description' => 'Plan your day efficiently by staying updated with changing weather conditions worldwide.',
                'image' => null,
                'link' => null,
                'content' => [
                    'icon' => 'fa-umbrella',
                    'color_class' => 'feat-orange',
                ],
                'sort_order' => 10,
                'status' => 'Active',
            ],
        ];

        foreach ($records as $record) {
            WorldWeatherPage::create($record);
        }
    }
}