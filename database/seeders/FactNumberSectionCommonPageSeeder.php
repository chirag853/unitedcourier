<?php

namespace Database\Seeders;

use App\Models\FactNumberSectionCommonPage;
use Illuminate\Database\Seeder;

class FactNumberSectionCommonPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stats = [
            [
                'title'         => 'Cities Covered',
                'target_number' => '150',
                'suffix'        => '+',
                'display_order' => 0,
                'status'        => true,
            ],
            [
                'title'         => 'Daily Parcels',
                'target_number' => '100',
                'suffix'        => 'K+',
                'display_order' => 1,
                'status'        => true,
            ],
            [
                'title'         => 'Delivery Riders',
                'target_number' => '5',
                'suffix'        => 'K+',
                'display_order' => 2,
                'status'        => true,
            ],
            [
                'title'         => 'On-time Rate',
                'target_number' => '99',
                'suffix'        => '.9%',
                'display_order' => 3,
                'status'        => true,
            ],
            [
                'title'         => 'Live Tracking',
                'target_number' => '24',
                'suffix'        => '/7',
                'display_order' => 4,
                'status'        => true,
            ],
            [
                'title'         => 'Happy Clients',
                'target_number' => '50',
                'suffix'        => 'K+',
                'display_order' => 5,
                'status'        => true,
            ],
        ];

        foreach ($stats as $stat) {
            FactNumberSectionCommonPage::create($stat);
        }
    }
}