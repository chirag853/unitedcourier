<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrivacyPolicyPage;

class PrivacyPolicyPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Page Meta
        PrivacyPolicyPage::create([
            'section_key' => '_page_meta',
            'title' => 'Privacy Policy Meta',
            'paragraphs' => 'Your privacy is our priority. This policy outlines how United Worldwide Couriers collects, uses, and protects your information.',
            'effective_date' => 'October 2025',
            'footer_heading' => 'Have Questions?',
            'footer_email' => 'contact@unitedcourier.com',
            'sort_order' => 0,
        ]);

        // Data Collection Section
        PrivacyPolicyPage::create([
            'section_key' => 'data_collection',
            'title' => 'Information Collection',
            'paragraphs' => 'When you use our worldwide courier services, we collect information that allows us to provide efficient logistics solutions. This includes:',
            'list_items_text' => "Personal Identity: Name, address, telephone number, and email.\nShipment Details: Recipient names, delivery addresses, and package contents.\nFinancial Info: Payment method details for billing and customs clearance.\nDigital Data: IP address, browser type, and tracking data from our website interactions.",
            'sort_order' => 1,
        ]);

        // Data Usage Section
        PrivacyPolicyPage::create([
            'section_key' => 'data_usage',
            'title' => 'How We Use Information',
            'paragraphs' => 'The information we collect is used primarily to fulfill your delivery requests and improve our global network efficiency.',
            'list_items_text' => "Processing and tracking international shipments.\nCommunicating real-time delivery status updates.\nEnhancing our website user experience.\nComplying with global customs and regulatory requirements.",
            'sort_order' => 2,
        ]);

        // Data Sharing Section
        PrivacyPolicyPage::create([
            'section_key' => 'data_sharing',
            'title' => 'Data Sharing',
            'paragraphs' => 'We may share your information with trusted third parties to facilitate our services.',
            'list_items_text' => "Shipping partners for delivery fulfillment.\nCustoms authorities for regulatory compliance.\nPayment processors for transaction processing.\nService providers who assist in operating our business.",
            'sort_order' => 3,
        ]);

        // Data Security Section
        PrivacyPolicyPage::create([
            'section_key' => 'data_security',
            'title' => 'Security & Protection',
            'paragraphs' => 'We implement enterprise-grade security protocols to safeguard your sensitive data from unauthorized access or disclosure. United Worldwide Couriers utilizes encrypted servers (SSL/TLS) for all data transmissions. Access to your personal data is strictly limited to authorized employees who require the information to perform their specific logistics roles.',
            'list_items_text' => '',
            'sort_order' => 4,
        ]);

        // Cookies Policy Section
        PrivacyPolicyPage::create([
            'section_key' => 'cookies_policy',
            'title' => 'Cookies & Tracking',
            'paragraphs' => 'Our digital platforms use "cookies" to recognize your preferences and provide a personalized experience. You can choose to disable cookies through your browser settings, though some website features may not function optimally as a result.',
            'list_items_text' => '',
            'sort_order' => 5,
        ]);

        // User Rights Section
        PrivacyPolicyPage::create([
            'section_key' => 'user_rights',
            'title' => 'Your Legal Rights',
            'paragraphs' => 'Depending on your jurisdiction (including GDPR or local Indian laws), you have the right to:',
            'list_items_text' => "Request a copy of the data we hold about you.\nAsk for corrections to inaccurate information.\nRequest the deletion of your personal data under certain conditions.\nOpt-out of marketing communications at any time.",
            'sort_order' => 6,
        ]);

        // Policy Updates Section
        PrivacyPolicyPage::create([
            'section_key' => 'policy_updates',
            'title' => 'Policy Updates',
            'paragraphs' => 'We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Effective Date" above.',
            'list_items_text' => "You are advised to review this policy periodically.\nContinued use of our services after changes constitutes acceptance.\nWe will not reduce your rights under this policy without your consent.",
            'sort_order' => 7,
        ]);
    }
}
