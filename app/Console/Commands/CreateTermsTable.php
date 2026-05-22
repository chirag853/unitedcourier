<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTermsTable extends Command
{
    protected $signature = 'create:terms-table';
    protected $description = 'Create terms_and_condition_page table and insert data';

    public function handle()
    {
        $this->info('Creating terms_and_condition_page table...');
        
        // Drop existing table if it exists
        Schema::dropIfExists('terms_and_condition_page');
        
        // Create the table
        Schema::create('terms_and_condition_page', function ($table) {
            $table->id();
            $table->string('section_key', 100)->unique();
            $table->string('title')->nullable();
            $table->text('paragraphs')->nullable();
            $table->json('list_items')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('effective_date', 50)->nullable();
            $table->string('footer_heading')->nullable();
            $table->string('footer_email')->nullable();
            $table->timestamps();
            
            $table->index('sort_order');
        });
        
        $this->info('Table created successfully!');
        
        // Insert data
        $this->info('Inserting sample data...');
        
        $sections = [
            [
                'section_key' => '_page_meta',
                'effective_date' => 'October 2025',
                'footer_heading' => 'Questions about our Terms?',
                'footer_email' => 'contact@unitedcourier.com',
                'sort_order' => 0
            ],
            [
                'section_key' => 'service_agreement',
                'title' => 'Service Agreement',
                'paragraphs' => 'By engaging services of United Worldwide Couriers, you agree to be bound by these Terms and Conditions. These terms govern the relationship between the sender (customer) and the courier (UWD).

We provide global logistics, express delivery, and freight forwarding services subject to availability and compliance with international transport regulations.',
                'sort_order' => 1
            ],
            [
                'section_key' => 'shipment_rules',
                'title' => 'Shipment Rules',
                'paragraphs' => 'Customers are responsible for ensuring that all shipments are packed securely and declared accurately. The following items are strictly prohibited:',
                'list_items' => json_encode([
                    'Hazardous materials or flammable liquids.',
                    'Perishable goods (unless pre-authorized).',
                    'Currency, gold, or precious stones.',
                    'Illegal substances or counterfeit goods.'
                ]),
                'sort_order' => 2
            ],
            [
                'section_key' => 'liability_claims',
                'title' => 'Liability & Claims',
                'paragraphs' => 'While we take every precaution, our liability for loss or damage is limited to the declared value of the shipment or standard industry limits, whichever is lower.',
                'list_items' => json_encode([
                    'Claims must be submitted in writing within 14 days of delivery.',
                    'Liability does not extend to indirect or consequential losses.',
                    'We highly recommend purchasing additional transit insurance for high-value items.'
                ]),
                'sort_order' => 3
            ],
            [
                'section_key' => 'payments_fees',
                'title' => 'Payments & Fees',
                'paragraphs' => 'Service rates are calculated based on weight, dimensions, and destination. Rates are subject to change without prior notice due to fuel surcharges or exchange rate fluctuations.

All invoices must be settled prior to delivery unless a corporate credit facility has been established.',
                'sort_order' => 4
            ],
            [
                'section_key' => 'customs_clearance',
                'title' => 'Customs Clearance',
                'paragraphs' => 'United Worldwide Couriers acts as the customer\'s agent for customs clearance. The customer is solely responsible for all duties, taxes, and regulatory fees imposed by the destination country.

Delays caused by customs inspections are outside of our control and do not qualify for service guarantees.',
                'sort_order' => 5
            ],
            [
                'section_key' => 'termination',
                'title' => 'Termination',
                'paragraphs' => 'We reserve the right to refuse, suspend, or terminate any shipment service if we suspect illegal activity, non-payment, or a breach of these Terms and Conditions.',
                'sort_order' => 6
            ]
        ];

        foreach ($sections as $section) {
            DB::table('terms_and_condition_page')->insert($section);
        }
        
        $this->info('Sample data inserted successfully!');
        $this->info('Terms and conditions table setup complete!');
    }
}
