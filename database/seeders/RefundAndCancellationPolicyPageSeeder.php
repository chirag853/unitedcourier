<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RefundAndCancellationPolicyPage;

class RefundAndCancellationPolicyPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Page Meta
        RefundAndCancellationPolicyPage::create([
            'section_key' => '_page_meta',
            'title' => 'Refund & Cancellation Policy Meta',
            'paragraphs' => 'Your privacy is our priority. This policy outlines how United Worldwide Couriers collects, uses, and protects your information.',
            'effective_date' => 'October 2025',
            'footer_heading' => 'Need Assistance?',
            'footer_email' => 'contact@unitedcourier.com',
            'sort_order' => 0,
        ]);

        // Cancellation Policy Section
        RefundAndCancellationPolicyPage::create([
            'section_key' => 'cancellation_policy',
            'title' => 'Cancellation Policy',
            'paragraphs' => 'At United Worldwide Couriers, we understand that plans change. Our cancellation rules are as follows:',
            'list_items_text' => "Pre-Pickup: Shipments can be cancelled at no charge before the courier has been dispatched for pickup.\nPost-Pickup: Once a shipment is picked up, it cannot be cancelled. However, you may request a \"Return to Sender\" service, which will incur standard return shipping fees.\nLabel Generation: If a shipping label was generated but not used, a cancellation request must be filed within 24 hours to avoid processing fees.",
            'sort_order' => 1,
        ]);

        // Refund Eligibility Section
        RefundAndCancellationPolicyPage::create([
            'section_key' => 'refund_eligibility',
            'title' => 'Refund Eligibility',
            'paragraphs' => 'Refunds are evaluated on a case-by-case basis. You may be eligible for a refund if:',
            'list_items_text' => "The service was not provided due to an error on our part.\nThe shipment was lost within our network (verified after investigation).\nA duplicate payment was accidentally made for the same tracking number.\nA guaranteed delivery service failed to meet the specified timeline (excluding customs delays).",
            'sort_order' => 2,
        ]);

        // Refund Process Section
        RefundAndCancellationPolicyPage::create([
            'section_key' => 'refund_process',
            'title' => 'Refund Process',
            'paragraphs' => 'To request a refund, please follow these steps:',
            'list_items_text' => "Submit a formal request via our support portal or email within 7 business days of the incident.\nProvide the Tracking Number and Proof of Payment.\nOnce approved, refunds are processed within 7-10 business days to the original payment method.",
            'sort_order' => 3,
        ]);

        // Non-Refundable Items Section
        RefundAndCancellationPolicyPage::create([
            'section_key' => 'non_refundable_items',
            'title' => 'Non-Refundable Items',
            'paragraphs' => 'Certain fees and charges are strictly non-refundable:',
            'list_items_text' => "Customs duties, taxes, and government regulatory fees.\nInsurance premiums once the shipment has departed the origin.\nHandling fees for prohibited or restricted items.\nSurcharges related to incorrect address provided by the sender.",
            'sort_order' => 4,
        ]);

        // Service Delays Section
        RefundAndCancellationPolicyPage::create([
            'section_key' => 'service_delays',
            'title' => 'Service Delays',
            'paragraphs' => 'Refunds are not provided for delays caused by circumstances beyond our control (Force Majeure), including but not limited to:',
            'list_items_text' => "Severe weather conditions or natural disasters.\nCustoms clearance procedures or inspections.\nGlobal supply chain disruptions or strikes.\nIncorrect or incomplete documentation provided by the customer.",
            'sort_order' => 5,
        ]);
    }
}
