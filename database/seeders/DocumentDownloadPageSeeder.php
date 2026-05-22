<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentDownloadPage;

class DocumentDownloadPageSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            // Document 1: Commercial Invoice
            [
                'file_type' => 'pdf',
                'title' => 'Commercial Shipment Requirement',
                'file_size' => '2.4 MB',
                'file_url' => '#',
                'category' => 'invoice',
                'status_badge' => 'Verified',
                'description' => 'Commercial shipment requirement document for export processing.',
                'sort_order' => 1,
                'status' => 'Active',
            ],
            // Document 2: Shipping Label Pack
            [
                'file_type' => 'pdf',
                'title' => 'Bank Referral Letter',
                'file_size' => '850 KB',
                'file_url' => '#',
                'category' => 'label',
                'status_badge' => 'Verified',
                'description' => 'Bank referral letter for shipping documentation.',
                'sort_order' => 2,
                'status' => 'Active',
            ],
            // Document 3: Customs Declaration
            [
                'file_type' => 'zip',
                'title' => 'Shipping Declaration Form',
                'file_size' => '5.1 MB',
                'file_url' => '#',
                'category' => 'customs',
                'status_badge' => 'Pending Sign',
                'description' => 'Shipping declaration form package for customs clearance.',
                'sort_order' => 3,
                'status' => 'Active',
            ],
            // Document 4: Spend Report
            [
                'file_type' => 'xls',
                'title' => 'Single Country Declaration',
                'file_size' => '1.2 MB',
                'file_url' => '#',
                'category' => 'invoice',
                'status_badge' => 'Verified',
                'description' => 'Single country declaration spreadsheet.',
                'sort_order' => 4,
                'status' => 'Active',
            ],
            // Document 5: Cargo Packing List
            [
                'file_type' => 'pdf',
                'title' => 'Indeminity Certificate',
                'file_size' => '1.7 MB',
                'file_url' => '#',
                'category' => 'packing',
                'status_badge' => 'Verified',
                'description' => 'Indeminity certificate for cargo packing.',
                'sort_order' => 5,
                'status' => 'Active',
            ],
            // Document 6: Bill of Lading
            [
                'file_type' => 'pdf',
                'title' => 'Proforma Invoice',
                'file_size' => '920 KB',
                'file_url' => '#',
                'category' => 'bol',
                'status_badge' => 'Verified',
                'description' => 'Proforma invoice document for bill of lading.',
                'sort_order' => 6,
                'status' => 'Active',
            ],
            // Document 7: Export Clearance Manifest
            [
                'file_type' => 'zip',
                'title' => 'Commercial Invoice',
                'file_size' => '8.4 MB',
                'file_url' => '#',
                'category' => 'customs',
                'status_badge' => 'Pending Sign',
                'description' => 'Commercial invoice archive for export clearance manifest.',
                'sort_order' => 7,
                'status' => 'Active',
            ],
            // Document 8: Delivery Confirmation Certificate
            [
                'file_type' => 'pdf',
                'title' => 'Invoice',
                'file_size' => '410 KB',
                'file_url' => '#',
                'category' => 'receipt',
                'status_badge' => 'Verified',
                'description' => 'Delivery confirmation invoice certificate.',
                'sort_order' => 8,
                'status' => 'Active',
            ],
        ];

        foreach ($documents as $doc) {
            DocumentDownloadPage::create($doc);
        }

        // ──────────────────────────────────────────────
        // Page Meta (Hero section content)
        // ──────────────────────────────────────────────
        DocumentDownloadPage::create([
            'section'    => 'page_meta',
            'file_type'  => null,
            'title'      => null,
            'file_size'  => null,
            'file_url'   => null,
            'category'   => null,
            'status_badge' => null,
            'description'  => null,
            'sort_order'   => 0,
            'status'       => 'Active',
            'content'      => [
                'badge'       => 'Explore All Documents',
                'title'       => 'Documents <span class="moving-gradient-text">Download</span>',
                'description' => 'Must-read guides, handpicked for their popularity among global exporters',
            ],
        ]);

        $this->command->info('Document Download page seeded with ' . count($documents) . ' documents.');
    }
}