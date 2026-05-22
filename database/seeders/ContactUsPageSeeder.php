<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactUsPage;

class ContactUsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Page Meta Section
        ContactUsPage::create([
            'section_key' => '_page_meta',
            'title' => 'Contact Us',
            'paragraphs' => 'If you need to know more about our services, or would like to contact someone who can help you with your request, please fill up the form below, and we will contact you as soon as possible.',
            'sort_order' => 1,
        ]);

        // Contact Info Section
        ContactUsPage::create([
            'section_key' => 'contact_info',
            'title' => 'Get in Touch',
            'paragraphs' => 'Have a query about your shipment? Our support team is available 24/7 to assist you.',
            'phone_numbers_text' => "+91-9999911176\n+91-11-46122222\n+91-11-26161261",
            'email_addresses_text' => "info@unitedcouriers.biz\ncsd@unitedcouriers.biz",
            'address' => 'Building No. 1, Bypass Road, <br>Mahipalpur New Delhi -110037',
            'social_links_text' => json_encode([
                'facebook' => '#',
                'twitter' => '#',
                'linkedin' => '#',
                'instagram' => '#'
            ]),
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d224026.25888083235!2d77.00429638410489!3d28.677370779651767!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d1b3400f3d737%3A0x746d7d6e610c0779!2sUnited%20worldwide%20courier%20pvt%20ltd!5e0!3m2!1sen!2sin!4v1778586557834!5m2!1sen!2sin',
            'sort_order' => 2,
        ]);
    }
}
