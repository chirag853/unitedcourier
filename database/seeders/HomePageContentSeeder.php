<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomePageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('home_page')->insert([
            // Hero Section
            ['section' => 'hero', 'field_name' => 'title', 'content' => 'E-commerce Speed. B2B Reliability. Ship Simply <br class="d-none d-md-block"> <span class="moving-gradient-text">United Couriers.</span>', 'sort_order' => 0],
            ['section' => 'hero', 'field_name' => 'subtitle', 'content' => 'From First Click to Delivery. Your Gateway to Seamless Shipping Worldwide.', 'sort_order' => 1],
            ['section' => 'hero', 'field_name' => 'badge', 'content' => 'Trusted by Growing Businesses Across India', 'sort_order' => 2],
            
            // About Section
            ['section' => 'about', 'field_name' => 'badge', 'content' => 'About United Worldwide Couriers', 'sort_order' => 10],
            ['section' => 'about', 'field_name' => 'heading', 'content' => 'One Partner. Infinite Logistics Possibilities.', 'sort_order' => 11],
            ['section' => 'about', 'field_name' => 'description', 'content' => 'United Worldwide Couriers delivers integrated logistics solutions for modern B2B enterprises, e-commerce brands, and growing businesses. Our services cover international Air Express & Freight, pan-India pickup, customs clearance with documentation support, and fulfilment solutions, all managed under one reliable platform. <br> With a strong operational network and an experienced logistics team, we help clients move shipments efficiently, reduce delays, and manage complex requirements with confidence. Every shipment is handled with proactive coordination, transparent tracking, and dedicated customer support to ensure a smooth and dependable delivery experience.', 'sort_order' => 12],
            ['section' => 'about', 'field_name' => 'feature1_title', 'content' => 'Tailored Shipping for Every Business Model', 'sort_order' => 13],
            ['section' => 'about', 'field_name' => 'feature1_desc', 'content' => 'Whether you are a B2B manufacturer, e-commerce brand, distributor, dropshipper, or individual shipper, we manage your complete delivery journey across 220+ countries with speed, visibility, and dependable support.', 'sort_order' => 14],
            ['section' => 'about', 'field_name' => 'feature2_title', 'content' => 'Smarter Cost for High-Volume Shipping', 'sort_order' => 15],
            ['section' => 'about', 'field_name' => 'feature2_desc', 'content' => 'Our volume-based pricing, shipment consolidation, and optimized routing solutions help businesses reduce logistics costs while improving efficiency across international operations.', 'sort_order' => 16],
            ['section' => 'about', 'field_name' => 'feature3_title', 'content' => 'Built to Scale With Your Business', 'sort_order' => 17],
            ['section' => 'about', 'field_name' => 'feature3_desc', 'content' => 'With warehousing, cross-docking, and fulfilment support, we help businesses scale from hundreds to thousands of shipments a day without compromising service quality, timelines, or customer experience.', 'sort_order' => 18],
            
            // Process Section
            ['section' => 'process', 'field_name' => 'section_tag', 'content' => 'Getting Started', 'sort_order' => 20],
            ['section' => 'process', 'field_name' => 'heading', 'content' => 'Start Shipping with United Couriers in 4 Simple Steps', 'sort_order' => 21],
            ['section' => 'process', 'field_name' => 'step_title', 'content' => 'Create Your Account', 'sort_order' => 22],
            ['section' => 'process', 'field_name' => 'step_desc', 'content' => 'Sign up and set up your shipping profile to manage orders, bookings, invoices, and tracking from one dashboard.', 'sort_order' => 23],
            ['section' => 'process', 'field_name' => 'step_title', 'content' => 'Choose Your Service', 'sort_order' => 24],
            ['section' => 'process', 'field_name' => 'step_desc', 'content' => 'Enter your shipment details and compare available delivery options based on price, speed, and service type.', 'sort_order' => 25],
            ['section' => 'process', 'field_name' => 'step_title', 'content' => 'Schedule Your Pickup', 'sort_order' => 26],
            ['section' => 'process', 'field_name' => 'step_desc', 'content' => 'Select your preferred service, confirm the shipment, and book your pickup.', 'sort_order' => 27],
            ['section' => 'process', 'field_name' => 'step_title', 'content' => 'Track Your Shipment', 'sort_order' => 28],
            ['section' => 'process', 'field_name' => 'step_desc', 'content' => 'Stay updated from pickup to final delivery with real-time tracking and proactive shipment updates.', 'sort_order' => 29],
            
            // Service Card 1 — Express Air Freight Solutions
            ['section' => 'service_card', 'field_name' => 'label', 'content' => 'Express Air Freight Solutions', 'sort_order' => 30],
            ['section' => 'service_card', 'field_name' => 'title', 'content' => 'Air Express Services Built Around Your Needs', 'sort_order' => 31],
            ['section' => 'service_card', 'field_name' => 'description', 'content' => 'Our Express services are designed to give customers complete flexibility — from faster 3–4 day delivery options to more economical 8–10 day delivery solutions, allowing every customer to choose a service based on their urgency, budget, and delivery timeline.', 'sort_order' => 32],
            ['section' => 'service_card', 'field_name' => 'small1_title', 'content' => 'Flexible Delivery Options', 'sort_order' => 33],
            ['section' => 'service_card', 'field_name' => 'small1_desc', 'content' => 'Priority and economy services are designed around your budget and timeline.', 'sort_order' => 34],
            ['section' => 'service_card', 'field_name' => 'small2_title', 'content' => 'Real Time Tracking', 'sort_order' => 35],
            ['section' => 'service_card', 'field_name' => 'small2_desc', 'content' => 'Instant tracking updates from pickup to final door-to-door delivery.', 'sort_order' => 36],
            
            // Service Card 2 — E-commerce Logistics Solutions
            ['section' => 'service_card', 'field_name' => 'label', 'content' => 'E-commerce Logistics Solutions', 'sort_order' => 37],
            ['section' => 'service_card', 'field_name' => 'title', 'content' => 'Built for Sellers. Designed for Scale.', 'sort_order' => 38],
            ['section' => 'service_card', 'field_name' => 'description', 'content' => 'Connect your marketplace account directly with our platform and ship orders with ease — starting from lightweight parcels of just 50g to high-volume e-commerce shipments.', 'sort_order' => 39],
            ['section' => 'service_card', 'field_name' => 'small1_title', 'content' => 'No Platform Fee', 'sort_order' => 40],
            ['section' => 'service_card', 'field_name' => 'small1_desc', 'content' => 'Reduce costs and protect your margins.', 'sort_order' => 41],
            ['section' => 'service_card', 'field_name' => 'small2_title', 'content' => 'Complete Fulfilment Support', 'sort_order' => 42],
            ['section' => 'service_card', 'field_name' => 'small2_desc', 'content' => 'Order processing, pickup, dispatch, tracking, and final delivery.', 'sort_order' => 43],
            
            // Service Card 3 — Warehousing Solutions
            ['section' => 'service_card', 'field_name' => 'label', 'content' => 'Warehousing Solutions', 'sort_order' => 44],
            ['section' => 'service_card', 'field_name' => 'title', 'content' => 'Store. Manage. Fulfil—Smarter from The USA', 'sort_order' => 45],
            ['section' => 'service_card', 'field_name' => 'description', 'content' => 'Our US warehousing facility helps businesses reduce delivery timelines, manage inventory efficiently, and fulfil international orders with greater speed, control, and reliability.', 'sort_order' => 46],
            ['section' => 'service_card', 'field_name' => 'small1_title', 'content' => 'Inventory Management', 'sort_order' => 47],
            ['section' => 'service_card', 'field_name' => 'small1_desc', 'content' => 'Real-time stock visibility with better operational control.', 'sort_order' => 48],
            ['section' => 'service_card', 'field_name' => 'small2_title', 'content' => 'Fast Fulfillment', 'sort_order' => 49],
            ['section' => 'service_card', 'field_name' => 'small2_desc', 'content' => 'Speedy dispatch support for e-commerce and B2B shipments.', 'sort_order' => 50],
            
            // Services Heading Section
            ['section' => 'services_heading', 'field_name' => 'heading', 'content' => 'Powering Your Business with <span class="moving-gradient-text"> Our Services</span>', 'sort_order' => 60],
            ['section' => 'services_heading', 'field_name' => 'description', 'content' => 'From urgent documents to high-volume commercial cargo, our logistics solutions are built to move every shipment with speed, precision, and complete reliability.', 'sort_order' => 61],

            // Testimonial Heading Section (heading + description for testimonial section header)
            ['section' => 'testimonial_heading', 'field_name' => 'heading', 'content' => 'Trusted by Businesses. Rated by Customers', 'sort_order' => 70],
            ['section' => 'testimonial_heading', 'field_name' => 'description', 'content' => 'For over 30 years, United Worldwide Couriers has supported businesses and individuals with secure, timely, and dependable logistics solutions. Our clients trust us for consistent service, transparent communication, careful handling, and smooth delivery experiences across domestic and international shipments.', 'sort_order' => 71],
        ]);
    }
}
