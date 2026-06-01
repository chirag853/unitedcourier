<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            FactNumberSectionCommonPageSeeder::class,
            PartnersSectionCommonPageSeeder::class,
            WarehousingSolutionsPageSeeder::class,
            WarehousingTestimonialsSeeder::class,
            TestimonialsSeeder::class,
            ContactUsPageSeeder::class,
            EcommerceLogisticsSolutionsPageSeeder::class,
            ExpressAirFreightSolutionsPageSeeder::class,
            BlogsPageSeeder::class,
            EbookPageSeeder::class,
            TrackOrderPageSeeder::class,
            WebinarPageSeeder::class,
            CurrencyCalculatorPageSeeder::class,
            WorldWeatherPageSeeder::class,
            WorldTimePageSeeder::class,
            PartnershipPageSeeder::class,
            DocumentDownloadPageSeeder::class,
        ]);
    }
}
