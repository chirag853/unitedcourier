<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barcode_generator_page', function (Blueprint $table) {
            $table->id();
            $table->string('section_type', 50);           // e.g., 'hero', 'features', 'track_cta', 'testimonials_header', 'faq'
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->text('icon_svg')->nullable();
            $table->string('link')->nullable();
            $table->integer('display_order')->default(0);
            $table->string('page_badge_text')->nullable();
            $table->string('page_button_text')->nullable();
            $table->string('page_tag')->nullable();
            $table->string('page_label')->nullable();
            $table->string('page_placeholder')->nullable();
            $table->string('page_icon_class')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Insert seed data for the barcode generator page
        // Hero section
        DB::table('barcode_generator_page')->insert([
            [
                'section_type' => 'hero',
                'title' => 'AWB Barcode <span class="moving-gradient-text">Generator</span>',
                'description' => 'Instantly generate shipping barcodes with your AWB or tracking number. Choose between PNG or SVG formats and download print-ready files with a single click.',
                'page_badge_text' => 'Free Tool · Instant Download',
                'display_order' => 0,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Features cards
        $features = [
            [
                'section_type' => 'features',
                'title' => 'Enter your AWB number',
                'description' => 'Paste your Air Waybill or tracking number from any courier service such as DHL, FedEx, UPS, India Post, or ShipGlobal to instantly create a scannable barcode.',
                'page_icon_class' => 'fa-solid fa-barcode',
                'display_order' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'features',
                'title' => 'Select barcode format',
                'description' => 'Choose the barcode type that fits your shipping or logistics needs. CODE128 is widely supported and works perfectly for most shipment labels.',
                'page_icon_class' => 'fa-solid fa-sliders',
                'display_order' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'features',
                'title' => 'Download & print instantly',
                'description' => 'Download high-quality PNG or scalable SVG barcode files for shipping labels, packaging, invoices, warehouse management, and tracking documents.',
                'page_icon_class' => 'fa-solid fa-download',
                'display_order' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('barcode_generator_page')->insert($features);

        // Track CTA section
        DB::table('barcode_generator_page')->insert([
            [
                'section_type' => 'track_cta',
                'title' => 'Track any shipment in real-time',
                'description' => 'Get live updates across all major carriers — end-to-end visibility from pickup to delivery for every order you export worldwide.',
                'page_badge_text' => '● LIVE TRACKING',
                'page_button_text' => 'Track Shipment →',
                'display_order' => 0,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Testimonials section header
        DB::table('barcode_generator_page')->insert([
            [
                'section_type' => 'testimonials_header',
                'title' => 'Trusted by the Brands You Trust',
                'description' => 'Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record speaks for itself. From on-time delivery and zero-compromise on quality to customer satisfaction, United Worldwide Couriers is completely reliable and trustworthy.',
                'display_order' => 0,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // FAQ section header
        DB::table('barcode_generator_page')->insert([
            [
                'section_type' => 'faq_header',
                'title' => 'Frequently Asked Questions',
                'subtitle' => 'Common Questions',
                'display_order' => 0,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // FAQ items
        $faqItems = [
            [
                'section_type' => 'faq',
                'title' => 'How do I get started?',
                'description' => 'To connect with our team, you have to register yourself, get a quote, and schedule your first pickup. Thereafter, the team will guide you through every step of the process.',
                'display_order' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'faq',
                'title' => 'How does United Worldwide Couriers meet your shipping and logistics needs?',
                'description' => 'To provide the best freight management solutions, we work with broad strategies, technologies, and services to simplify the planning, storage, and movement of goods.',
                'display_order' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'faq',
                'title' => 'What packaging standards should we follow for shipping?',
                'description' => 'We utilize study and secure packaging for small to large packages to protect your goods during transit. In case of fragile items, they will be cushioned enough and clearly labelled as "Fragile". In addition, we also provide a packaging and labelling guide for all new on boarders for no confusion and faster results.',
                'display_order' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'faq',
                'title' => 'How do we calculate cost?',
                'description' => 'The exact shipping cost will be calculated based on your goods\' weight, dimensions, destinations, where it is expected to be delivered, and the delivery speed. If you are interested in knowing the accurate pricing, you are welcome to connect with the team.',
                'display_order' => 4,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'faq',
                'title' => 'Will I be notified about my shipment status?',
                'description' => 'Yes. To keep the clients informed, our team provides regular updates via email or SMS throughout the shipping process.',
                'display_order' => 5,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'faq',
                'title' => 'Does United Worldwide Couriers handle bulk or commercial shipments?',
                'description' => 'Yes, we specialize in managing bulk as well as commercial consignments with easy-flowing coordination, dedicated handling, secure transit, and timely delivery. This remains the same for both small and large volumes.',
                'display_order' => 6,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'faq',
                'title' => 'Can I schedule a pickup for my shipment?',
                'description' => 'Yes, you can easily schedule a pick up at your preferred time either by reaching out to our team online (via our website) or by directly contacting our customer support.',
                'display_order' => 7,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'faq',
                'title' => 'How can I track my shipment?',
                'description' => 'Through our online tracking system, you can track your shipment in real-time. For that, you just have to enter the tracking ID provided on our website dashboard to get live updates.',
                'display_order' => 8,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'faq',
                'title' => 'Will my package be picked up by the United Worldwide Couriers team only?',
                'description' => 'It depends on the service and location. So, your shipment may be picked up either by our in-house delivery team or by third-party courier partners. On the contrary, we ensure strict quality control and safe handling for whatever the case may be.',
                'display_order' => 9,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'faq',
                'title' => 'Do you provide customs clearance support?',
                'description' => 'Yes, while others face limited support, we complete international customs documentation and clearance faster. This way, we support rapid shipping without delays.',
                'display_order' => 10,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_type' => 'faq',
                'title' => 'What happens if my shipment is delayed or stuck?',
                'description' => 'First things first, the team proactively monitors the shipment to identify and resolve potential issues before they escalate. Still, if an issue occurs, the internal team coordinates with the partners to resolve the situation quickly.',
                'display_order' => 11,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('barcode_generator_page')->insert($faqItems);

        // Features section heading
        DB::table('barcode_generator_page')->insert([
            [
                'section_type' => 'features_heading',
                'title' => 'Understanding volumetric weight',
                'description' => 'Carriers use dimensional weight to price large, light packages — here\'s what you need to know.',
                'display_order' => 0,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // FAQ Contact sidebar section
        DB::table('barcode_generator_page')->insert([
            [
                'section_type' => 'faq_contact_sidebar',
                'title' => 'Need personalized help?',
                'description' => 'Our logistics experts are available 24/7 to assist your requirements.',
                'page_button_text' => 'Message Support',
                'display_order' => 0,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_generator_page');
    }
};