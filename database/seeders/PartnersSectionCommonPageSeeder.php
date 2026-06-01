<?php

namespace Database\Seeders;

use App\Models\PartnersSectionCommonPage;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PartnersSectionCommonPageSeeder extends Seeder
{
    /**
     * Seed the partners section with default logos.
     * Uses external URLs for demonstration; admin can replace via the admin interface.
     */
    public function run(): void
    {
        $logos = [
            [
                'logo_image'    => 'https://1000logos.net/wp-content/uploads/2021/04/Fedex-logo.png',
                'alt_text'      => 'FedEx',
                'display_order' => 1,
                'status'        => true,
            ],
            [
                'logo_image'    => 'https://www.ups.com/webassets/icons/logo.svg',
                'alt_text'      => 'UPS',
                'display_order' => 2,
                'status'        => true,
            ],
            [
                'logo_image'    => 'https://www.usps.com/global-elements/header/images/utility-header/logo-sb.svg',
                'alt_text'      => 'USPS',
                'display_order' => 3,
                'status'        => true,
            ],
            [
                'logo_image'    => 'https://www.tnt.com/dam/tnt_express_media/en_gb/images/ChoosingTNT/TNT-Logo-edt.png',
                'alt_text'      => 'TNT',
                'display_order' => 4,
                'status'        => true,
            ],
            [
                'logo_image'    => 'https://dotcomaramexprod.blob.core.windows.net/default/docs/default-source/logo/aramex-logo-english.webp',
                'alt_text'      => 'Aramex',
                'display_order' => 5,
                'status'        => true,
            ],
        ];

        foreach ($logos as $logo) {
            PartnersSectionCommonPage::create($logo);
        }
    }
}