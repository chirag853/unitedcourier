<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WarehousingTestimonial;

class WarehousingTestimonialsSeeder extends Seeder
{
    public function run()
    {
        WarehousingTestimonial::updateOrCreate(
            ['section' => 'testimonials', 'item_key' => 'testimonials_header'],
            [
                'section' => 'testimonials',
                'item_key' => 'testimonials_header',
                'content' => [
                    'header_image' => 'images/google-review.png',
                    'title' => 'Trusted by the Brands You Trust',
                    'paragraphs' => 'Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record speaks for itself. From on-time delivery and zero-compromise on quality to customer satisfaction, United Worldwide Couriers is completely reliable and trustworthy.',
                ],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $testimonials = [
            [
                'item_key' => 'testimonial_1',
                'subtitle' => 'Shelly Kapoor',
                'description' => 'Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues.',
                'image' => 'images/review-1.png',
                'rating' => 5,
            ],
            [
                'item_key' => 'testimonial_2',
                'subtitle' => 'Vansh Agarwal',
                'description' => 'Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance.',
                'image' => 'images/review-2.png',
                'rating' => 5,
            ],
            [
                'item_key' => 'testimonial_3',
                'subtitle' => 'Rahul Mehta',
                'description' => 'Best courier service for international deliveries, offering smooth documentation, fast processing, and quick dispatch with no delays or complications.',
                'image' => 'images/review-3.png',
                'rating' => 5,
            ],
            [
                'item_key' => 'testimonial_4',
                'subtitle' => 'Anjali Sharma',
                'description' => 'Affordable pricing combined with premium quality service makes United Worldwide Couriers a highly recommended choice for all shipping and logistics needs.',
                'image' => 'images/review-4.png',
                'rating' => 5,
            ],
            [
                'item_key' => 'testimonial_5',
                'subtitle' => 'Karan Singh',
                'description' => 'Professional and efficient team handled my bulk shipments perfectly, ensuring timely delivery, careful handling, and a completely hassle-free logistics experience.',
                'image' => 'images/review-5.png',
                'rating' => 5,
            ],
            [
                'item_key' => 'testimonial_6',
                'subtitle' => 'Vinay Verma',
                'description' => 'United Worldwide Couriers delivered my international parcel quickly and safely, with smooth handling and excellent service throughout the entire process.',
                'image' => 'images/review-6.png',
                'rating' => 5,
            ],
        ];

        foreach ($testimonials as $index => $testimonial) {
            WarehousingTestimonial::updateOrCreate(
                ['section' => 'testimonials', 'item_key' => $testimonial['item_key']],
                [
                    'section' => 'testimonials',
                    'item_key' => $testimonial['item_key'],
                    'content' => [
                        'subtitle' => $testimonial['subtitle'],
                        'description' => $testimonial['description'],
                        'image' => $testimonial['image'],
                        'rating' => $testimonial['rating'],
                    ],
                    'sort_order' => 2 + $index,
                    'is_active' => true,
                ]
            );
        }
    }
}
