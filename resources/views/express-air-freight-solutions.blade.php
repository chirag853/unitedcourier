@include('website_include.header')

<style>
</style>

<!-- Hero section -->
@php
    $heroBadgeText = $heroContent->content['badge_text'] ?? 'Express Air Freight Solutions';
    $heroTitle = $heroContent->content['title'] ?? 'Express <span class="moving-gradient-text">Air Freight Solutions</span> That Deliver';
    $heroDescription = $heroContent->content['description'] ?? 'Fast, reliable, and fully tracked international air freight — built for businesses that can\'t afford delays. Your cargo, delivered anywhere in the world with precision and care.';
    $heroBtnPrimaryText = $heroContent->content['button_primary_text'] ?? 'Book a Shipping';
    $heroBtnPrimaryIcon = $heroContent->content['button_primary_icon'] ?? 'fa-paper-plane';
    $heroBtnPrimaryUrl = $heroContent->content['button_primary_url'] ?? '#';
    $heroBtnSecondaryText = $heroContent->content['button_secondary_text'] ?? 'Get a Quote';
    $heroBtnSecondaryIcon = $heroContent->content['button_secondary_icon'] ?? 'fa-calculator';
    $heroBtnSecondaryUrl = $heroContent->content['button_secondary_url'] ?? '#';
    $heroImage = $heroContent->content['image'] ?? 'public/website_images/air-freight-service.webp';
    $heroBadges = $heroContent->content['badges'] ?? [
        ['icon' => 'fa-clock', 'text' => '24–72 Hr Delivery'],
        ['icon' => 'fa-shield-alt', 'text' => 'Fully Insured'],
        ['icon' => 'fa-map-marker-alt', 'text' => '220+ Countries'],
    ];
    $heroStatPills = $heroContent->content['stat_pills'] ?? [
        ['icon' => 'fa-box', 'value' => '50K+', 'label' => 'Shipments/Month', 'color' => 'rgba(26,115,232,.1)', 'text_color' => 'var(--primary)'],
        ['icon' => 'fa-star', 'value' => '4.9★', 'label' => 'Avg Rating', 'color' => 'rgba(255,107,0,.1)', 'text_color' => 'var(--accent)'],
        ['icon' => 'fa-check-circle', 'value' => '99.2%', 'label' => 'On-Time Rate', 'color' => 'rgba(40,167,69,.1)', 'text_color' => 'var(--success)'],
    ];
@endphp
<header class="hero-gradient">
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
    </div>
    <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;">
    </div>

    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <!-- Left Content -->
            <div class="col-md-6 text-md-start text-center animate__animated animate__fadeInLeft">

                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    {{ $heroBadgeText }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $heroTitle !!}
                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ $heroDescription }}
                </p>

                <a href="{{ $heroBtnPrimaryUrl }}" class="book-btn-service"><i class="fas {{ $heroBtnPrimaryIcon }}"></i> {{ $heroBtnPrimaryText }}</a> &nbsp; <a
                    href="{{ $heroBtnSecondaryUrl }}" class="quote-btn-service"><i class="fas {{ $heroBtnSecondaryIcon }}"></i> {{ $heroBtnSecondaryText }}</a>

                <div class="hero-badges">
                    @foreach($heroBadges as $badge)
                    <div class="hero-badge"><i class="fas {{ $badge['icon'] }}"></i> {{ $badge['text'] }}</div>
                    @endforeach
                </div>

            </div>

            <!-- Right Image -->
            <div class="col-md-6 text-center">
                <div class="hero-graphic">
                    <div class="plane-circle">
                        <img src="{{ asset( $heroImage) }}" class="img-fluid">
                        <!-- Stat pills -->
                        @foreach($heroStatPills as $pill)
                        <div class="stat-pill pill-{{ $loop->iteration }}">
                            <div class="sp-icon" style="background:{{ $pill['color'] }};color:{{ $pill['text_color'] }}"><i
                                    class="fas {{ $pill['icon'] }}"></i></div>
                            <div>
                                <div class="sp-val">{{ $pill['value'] }}</div>
                                <div class="sp-lbl">{{ $pill['label'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>



@include('website_include.fact-number-section')


<!-- OVERVIEW About Section -->
@php
    $overviewTitle = $overviewContent->content['title'] ?? 'Air Express Services <span class="gradient-text">Built Around Your Needs</span>';
    $overviewDescription = $overviewContent->content['description'] ?? 'United Worldwide Couriers offers an exceptional Express Air Freight service built for B2B enterprises, e-commerce businesses, and time-critical international shipments. We operate an end-to-end ecosystem from collection to customs clearance to last-mile delivery all under one roof.';
    $overviewImage = $overviewContent->content['image'] ?? 'public/website_images/map-pattern.png';
    $overviewButtonText = $overviewContent->content['button_text'] ?? 'Book Shipments';
    $overviewButtonUrl = $overviewContent->content['button_url'] ?? '#';
    $overviewCheckList = $overviewContent->content['check_list'] ?? [
        'Priority loading on partner airline networks across 6 continents',
        'Full customs brokerage with pre-clearance documentation support',
        'Dedicated account manager and 24/7 live shipment support',
        'Door-to-door delivery with real-time GPS tracking portal',
        'Fulfilment services, returns management, and COD options',
    ];
@endphp
<section class="about-section">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-6 animate-on-scroll" data-anim="animate__fadeInLeft" style="animation-delay: 0.1s;">
                <div class="about-image-grid">
                    <div class="world-map-placeholder">
                        <img src="{{ asset($overviewImage) }}" class="img-fluid">
                        <div class="map-dot md1"></div>
                        <div class="map-dot md2"></div>
                        <div class="map-dot md3"></div>
                        <div class="map-dot md4"></div>
                        <div class="map-dot md5"></div>
                        <div class="map-dot md6"></div>
                    </div>
                </div>
            </div>

            <!-- Content Side -->
            <div class="col-lg-6">
                <div class="about-content">
                    <h2 class="about-title">{!! $overviewTitle !!}</h2>
                    <p class="lead">{{ $overviewDescription }}</p>

                    <ul class="check-list lead">
                        @foreach($overviewCheckList as $item)
                        <li><i class="fas fa-check-circle"></i> {{ $item }}</li>
                        @endforeach
                    </ul>

                    <a href="{{ $overviewButtonUrl }}" class="sr-demo-btn-live mt-3">{{ $overviewButtonText }}</a>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- Features cards section -->
@php
    $featuresHeaderTitle = $featuresHeaderContent->content['title'] ?? 'What Makes Our Air Freight Stand Out';
    $featuresHeaderDescription = $featuresHeaderContent->content['description'] ?? 'Every feature is designed to give your business a competitive edge — speed, transparency, and reliability, every single shipment. Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions.';
@endphp
<section class="features-section" style="padding:40px 0;">
    <div class="container">

        <div class="row justify-content-center mb-3">
            <div class="col-lg-10 text-center">
                <h2 class="about-title">{{ $featuresHeaderTitle }}</h2>

                <p class="about-desc text-center">
                    {{ $featuresHeaderDescription }}
                </p>
            </div>
        </div>

        <div class="row g-4">
            @forelse($featuresContent as $feature)
            @php
                $feat = $feature->content;
            @endphp
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon {{ $feat['color_class'] ?? 'fi-blue' }}"><i class="fas {{ $feat['icon_class'] ?? $feat['icon'] ?? 'fa-satellite' }}"></i></div>
                    <h5>{{ $feat['title'] ?? '' }}</h5>
                    <p>{{ $feat['description'] ?? '' }}</p>
                </div>
            </div>
            @empty
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon fi-blue"><i class="fas fa-satellite"></i></div>
                    <h5>Real-Time Tracking</h5>
                    <p>Monitor your shipment at every checkpoint — from pickup to customs to final-mile — through our
                        live dashboard and SMS alerts.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon fi-orange"><i class="fas fa-thermometer-half"></i></div>
                    <h5>Temperature Control</h5>
                    <p>Specialized cold-chain and pharma-grade shipping options for sensitive or perishable cargo types.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon fi-navy"><i class="fas fa-headset"></i></div>
                    <h5>24/7 Support</h5>
                    <p>Our logistics experts are available around the clock — via phone, chat, or email — to resolve any
                        issue in real time.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon fi-green"><i class="fas fa-leaf"></i></div>
                    <h5>Eco-Freight Options</h5>
                    <p>Carbon-offset shipping options and sustainable packaging solutions for businesses committed to
                        reducing their footprint.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>











<!-- testimonial -->
@php
    $testimonialsHeaderTitle = $testimonialsHeader->content['title'] ?? 'Trusted by the Brands You Trust';
    $testimonialsHeaderDescription = $testimonialsHeader->content['description'] ?? 'Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions.';
    $testimonialsGoogleReviewImage = $testimonialsHeader->content['google_review_image'] ?? null;
@endphp
<section class="testimonial-section" style="display:none">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-3">
            <div class="col-lg-8 text-center">
                @if($testimonialsGoogleReviewImage)
                <div class="google-badge">
                    <a href="#">
                        <img src="{{ $testimonialsGoogleReviewImage }}" alt="Google">
                    </a>
                </div>
                @endif
                <h2 class="about-title">{{ $testimonialsHeaderTitle }}</h2>

                <p class="about-desc text-center">
                    {{ $testimonialsHeaderDescription }}
                </p>
            </div>
        </div>

        <div class="slider-wrapper">
            <div class="slider-track">

                @forelse($testimonials as $testimonial)
                @php
                    $tContent = $testimonial->content;
                @endphp
                <div class="testimonial-card">
                    <div class="stars">{{ str_repeat('★', min(5, (int)($tContent['rating'] ?? 5))) }}</div>
                    <p class="testimonial-text">"{{ $tContent['text'] ?? $tContent['content'] ?? '' }}"</p>
                    <div class="user-info">
                        <img src="{{ asset($tContent['avatar'] ?? $tContent['customer_image'] ?? 'public/website_images/review-1.png') }}" class="img-fluid">
                        <h6>{{ $tContent['name'] ?? $tContent['customer_name'] ?? '' }}</h6>
                    </div>
                </div>
                @empty
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time."</p>
                    <div class="user-info">
                        <img src="{{ asset('public/website_images/review-1.png') }}" class="img-fluid">
                        <h6>Shelly Kapoor</h6>
                    </div>
                </div>
                @endforelse

                <!-- Duplicate for seamless loop -->
                @forelse($testimonials as $testimonial)
                @php
                    $tContent = $testimonial->content;
                @endphp
                <div class="testimonial-card">
                    <div class="stars">{{ str_repeat('★', min(5, (int)($tContent['rating'] ?? 5))) }}</div>
                    <p class="testimonial-text">"{{ $tContent['text'] ?? $tContent['content'] ?? '' }}"</p>
                    <div class="user-info">
                        <img src="{{ asset($tContent['avatar'] ?? $tContent['customer_image'] ?? 'public/website_images/review-1.png') }}" class="img-fluid">
                        <h6>{{ $tContent['name'] ?? $tContent['customer_name'] ?? '' }}</h6>
                    </div>
                </div>
                @empty
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance."</p>
                    <div class="user-info"><img src="{{ asset('public/website_images/review-2.png') }}" class="img-fluid">
                        <h6>Vansh Agarwal</h6>
                    </div>
                </div>
                @endforelse

            </div>
        </div>
    </div>
</section>



<!-- FAQ Section -->
@php
    $faqBadge = $faqHeader->content['badge_text'] ?? $faqHeader->content['badge'] ?? 'Common Questions';
    $faqTitle = $faqHeader->content['title'] ?? 'Frequently Asked Questions';
    $faqSidebarImage = $faqHeader->content['sidebar_image'] ?? 'https://i.pinimg.com/originals/f6/5d/46/f65d4681649d85bc91c86872a1775919.gif';
    $faqSidebarTitle = $faqHeader->content['sidebar_title'] ?? 'Need personalized help?';
    $faqSidebarDescription = $faqHeader->content['sidebar_description'] ?? 'Our logistics experts are available 24/7 to assist your requirements.';
    $faqContactBoxTitle = $faqHeader->content['contact_box_title'] ?? 'Contact Us';
    $faqContactBoxDescription = $faqHeader->content['contact_box_description'] ?? 'For urgent inquiries regarding your current shipment status.';
    $faqContactButtonText = $faqHeader->content['contact_button_text'] ?? 'Message Support';
@endphp
<section class="faq-section" style="display:none">
    <div class="container">
        <div class="faq-header">
            <span class="heading-badge">{{ $faqBadge }}</span>
            <h2 class="about-title">{{ $faqTitle }}</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
               @include('website_include.faq-support-form')
           </div>


            <div class="col-lg-8">
                <div class="accordion" id="logisticsFaq" style="height: 70vh; overflow-y: auto;">
                    @forelse($faqs as $faq)
                    @php
                        $faqId = 'faq' . $loop->iteration;
                    @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                                data-bs-target="#{{ $faqId }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="{{ $faqId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                {!! $faq->answer !!}
                            </div>
                        </div>
                    </div>
                    @empty
                    <!-- Item 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq1">
                                How do I get started?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                To connect with our team, you have to register yourself, get a quote, and schedule your
                                first pickup. Thereafter, the team will guide you through every step of the process.
                            </div>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq2">
                                How does United Worldwide Couriers meet your shipping and logistics needs?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                To provide the best freight management solutions, we work with broad strategies,
                                technologies, and services to simplify the planning, storage, and movement of goods.
                            </div>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq3">
                                What packaging standards should we follow for shipping?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                We utilize study and secure packaging for small to large packages to protect your goods
                                during transit. In case of fragile items, they will be cushioned enough and clearly
                                labelled as "Fragile". In addition, we also provide a packaging and labelling guide for
                                all new on boarders for no confusion and faster results.
                            </div>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq4">
                                How do we calculate cost?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                The exact shipping cost will be calculated based on your goods' weight, dimensions,
                                destinations, where it is expected to be delivered, and the delivery speed.
                            </div>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq5">
                                Will I be notified about my shipment status?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes. To keep the clients informed, our team provides regular updates via email or SMS
                                throughout the shipping process.
                            </div>
                        </div>
                    </div>

                    <!-- Item 6 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq6">
                                Does United Worldwide Couriers handle bulk or commercial shipments?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes, we specialize in managing bulk as well as commercial consignments with easy-flowing
                                coordination, dedicated handling, secure transit, and timely delivery.
                            </div>
                        </div>
                    </div>

                    <!-- Item 7 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq7">
                                Can I schedule a pickup for my shipment?
                            </button>
                        </h2>
                        <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes, you can easily schedule a pick up at your preferred time either by reaching out to
                                our team online (via our website) or by directly contacting our customer support.
                            </div>
                        </div>
                    </div>

                    <!-- Item 8 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq8">
                                How can I track my shipment?
                            </button>
                        </h2>
                        <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Through our online tracking system, you can track your shipment in real-time. For that,
                                you just have to enter the tracking ID provided on our website dashboard to get live
                                updates.
                            </div>
                        </div>
                    </div>

                    <!-- Item 9 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq9">
                                Will my package be picked up by the United Worldwide Couriers team only?
                            </button>
                        </h2>
                        <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                It depends on the service and location. So, your shipment may be picked up either by our
                                in-house delivery team or by third-party courier partners.
                            </div>
                        </div>
                    </div>

                    <!-- Item 10 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq10">
                                Do you provide customs clearance support?
                            </button>
                        </h2>
                        <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes, while others face limited support, we complete international customs documentation
                                and clearance faster.
                            </div>
                        </div>
                    </div>

                    <!-- Item 11 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq11">
                                What happens if my shipment is delayed or stuck?
                            </button>
                        </h2>
                        <div id="faq11" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                First things first, the team proactively monitors the shipment to identify and resolve
                                potential issues before they escalate.
                            </div>
                        </div>
                    </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</section>





<!-- MOUSE HOVER GREDIENT EFFECT ON MISSION AND VISSION SCRIPS -->

<script>
    // Track mouse position for card glow effect
    document.querySelectorAll('.purpose-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const glow = card.querySelector('.card-glow');
            glow.style.left = `${x - 200}px`;
            glow.style.top = `${y - 200}px`;
        });
    });

    // Intersection Observer for reveal animations
    const observer3 = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, {
        threshold: 0.2
    });

    document.querySelectorAll('.reveal').forEach(el => observer3.observe(el));
</script>


<!-- TIMELINE JOURNEY SCRIPT -->

<script>
    // Highlighting active timeline items on scroll
    const observerOptions = {
        threshold: 0.4,
        rootMargin: "0px 0px -10% 0px"
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.timeline-item').forEach(item => observer.observe(item));
</script>




@include('website_include.footer')
