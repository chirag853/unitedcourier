<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VolumetricCalculatorPage;

class VolumetricCalculatorPageSeeder extends Seeder
{
    public function run()
    {
        // Hero section
        VolumetricCalculatorPage::create([
            'page' => 'volumetric-calculator',
            'section' => 'hero',
            'data_title' => 'Weight Calculator',
            'data_description' => 'Enter your package dimensions to instantly calculate dimensional weight and understand how carriers determine your chargeable weight.',
            'data_button_text' => 'Calculate Now',
            'data_extra' => json_encode([
                'badge_text' => 'Free Tool · Instant Results',
                'button_url' => '#calculator'
            ]),
            'sort_order' => 1,
            'is_active' => true
        ]);

        // Features header
        VolumetricCalculatorPage::create([
            'page' => 'volumetric-calculator',
            'section' => 'features_header',
            'data_title' => 'Understanding volumetric weight',
            'data_description' => 'Carriers use dimensional weight to price large, light packages — here\'s what you need to know.',
            'sort_order' => 2,
            'is_active' => true
        ]);

        // Features items
        $features = [
            [
                'data_icon' => 'fa-solid fa-ruler',
                'data_title' => 'Measure all three dimensions',
                'data_description' => 'Use a tape measure to get the length, width, and height of your package in centimetres. Always measure the outer dimensions including packaging material.'
            ],
            [
                'data_icon' => 'fa-solid fa-divide',
                'data_title' => 'Apply the divisor formula',
                'data_description' => 'Multiply length × width × height to get the cubic volume, then divide by the carrier\'s divisor (typically 5000 for air freight, 4000 for express couriers).'
            ],
            [
                'data_icon' => 'fa-solid fa-scale-balanced',
                'data_title' => 'Compare with actual weight',
                'data_description' => 'Carriers charge based on whichever is higher — the actual weight or the volumetric weight. Knowing both helps you choose the right packaging to minimise costs.'
            ]
        ];

        foreach ($features as $index => $feature) {
            VolumetricCalculatorPage::create(array_merge([
                'page' => 'volumetric-calculator',
                'section' => 'features',
                'sort_order' => 3 + $index,
                'is_active' => true
            ], $feature));
        }

        // Track CTA section
        VolumetricCalculatorPage::create([
            'page' => 'volumetric-calculator',
            'section' => 'track_cta',
            'data_title' => 'Track any shipment in real-time',
            'data_description' => 'Get live updates across all major carriers — end-to-end visibility from pickup to delivery for every order you export worldwide.',
            'data_button_text' => 'Track Shipment →',
            'data_extra' => json_encode([
                'live_badge' => '● LIVE TRACKING'
            ]),
            'sort_order' => 6,
            'is_active' => true
        ]);

        // Testimonials header
        VolumetricCalculatorPage::create([
            'page' => 'volumetric-calculator',
            'section' => 'testimonials_header',
            'data_title' => 'Trusted by the Brands You Trust',
            'data_description' => 'Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record speaks for itself. From on-time delivery and zero-compromise on quality to customer satisfaction, United Worldwide Couriers is completely reliable and trustworthy.',
            'data_extra' => json_encode([
                'badge_url' => '#',
                'badge_image' => 'public/website_images/google-review.png',
                'badge_alt' => 'Google'
            ]),
            'sort_order' => 7,
            'is_active' => true
        ]);

        // Testimonials
        $testimonials = [
            [
                'stars' => '★★★★★',
                'text' => 'Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues.',
                'name' => 'Shelly Kapoor',
                'image' => 'public/website_images/review-1.png'
            ],
            [
                'stars' => '★★★★★',
                'text' => 'Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance.',
                'name' => 'Vansh Agarwal',
                'image' => 'public/website_images/review-2.png'
            ],
            [
                'stars' => '★★★★★',
                'text' => 'Best courier service for international deliveries, offering smooth documentation, fast processing, and quick dispatch with no delays or complications.',
                'name' => 'Rahul Mehta',
                'image' => 'public/website_images/review-3.png'
            ],
            [
                'stars' => '★★★★★',
                'text' => 'Affordable pricing combined with premium quality service makes United Worldwide Couriers a highly recommended choice for all shipping and logistics needs.',
                'name' => 'Anjali Sharma',
                'image' => 'public/website_images/review-4.png'
            ],
            [
                'stars' => '★★★★★',
                'text' => 'Professional and efficient team handled my bulk shipments perfectly, ensuring timely delivery, careful handling, and a completely hassle-free logistics experience.',
                'name' => 'Karan Singh',
                'image' => 'public/website_images/review-5.png'
            ]
        ];

        foreach ($testimonials as $index => $testimonial) {
            VolumetricCalculatorPage::create([
                'page' => 'volumetric-calculator',
                'section' => 'testimonials',
                'data_image' => $testimonial['image'],
                'data_extra' => json_encode([
                    'stars' => $testimonial['stars'],
                    'text' => $testimonial['text'],
                    'name' => $testimonial['name']
                ]),
                'sort_order' => 8 + $index,
                'is_active' => true
            ]);
        }

        // FAQ sidebar
        VolumetricCalculatorPage::create([
            'page' => 'volumetric-calculator',
            'section' => 'faq_sidebar',
            'data_image' => 'https://i.pinimg.com/originals/f6/5d/46/f65d4681649d85bc91c86872a1775919.gif',
            'data_title' => 'Need personalized help?',
            'data_description' => 'Our logistics experts are available 24/7 to assist your requirements.',
            'data_button_text' => 'Message Support',
            'data_extra' => json_encode([
                'illustration_alt' => 'Help',
                'contact_title' => 'Contact Us',
                'contact_description' => 'For urgent inquiries regarding your current shipment status.',
            ]),
            'sort_order' => 13,
            'is_active' => true
        ]);

        // FAQ items
        $faqs = [
            [
                'question' => 'How do I get started?',
                'answer' => 'To connect with our team, you have to register yourself, get a quote, and schedule your first pickup. Thereafter, the team will guide you through every step of the process.'
            ],
            [
                'question' => 'How does United Worldwide Couriers meet your shipping and logistics needs?',
                'answer' => 'To provide the best freight management solutions, we work with broad strategies, technologies, and services to simplify the planning, storage, and movement of goods.'
            ],
            [
                'question' => 'What packaging standards should we follow for shipping?',
                'answer' => 'We utilize study and secure packaging for small to large packages to protect your goods during transit. In case of fragile items, they will be cushioned enough and clearly labelled as "Fragile". In addition, we also provide a packaging and labelling guide for all new on boarders for no confusion and faster results.'
            ],
            [
                'question' => 'How do we calculate cost?',
                'answer' => 'Our pricing is transparent and competitive. We calculate costs based on package weight, dimensions, destination, and service type. Use our volumetric calculator to estimate dimensional weight.'
            ]
        ];

        foreach ($faqs as $index => $faq) {
            VolumetricCalculatorPage::create([
                'page' => 'volumetric-calculator',
                'section' => 'faq',
                'data_extra' => json_encode($faq),
                'sort_order' => 14 + $index,
                'is_active' => true
            ]);
        }

        // Calculator settings
        VolumetricCalculatorPage::create([
            'page' => 'volumetric-calculator',
            'section' => 'calculator',
            'data_extra' => json_encode([
                'divisor_options' => [
                    [
                        'value' => '5000',
                        'text' => '5000 Air',
                        'width' => '105px'
                    ],
                    [
                        'value' => '400',
                        'text' => '400 Express',
                        'width' => '145px'
                    ],
                    [
                        'value' => '6000',
                        'text' => '6000 Sea',
                        'width' => '135px'
                    ]
                ]
            ]),
            'sort_order' => 18,
            'is_active' => true
        ]);
    }
}
