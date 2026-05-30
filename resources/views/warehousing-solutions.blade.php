@include('website_include.header')

<style>
  /* FACTS NUMBER CSS */
  :root {
            --brand-blue: #2563eb;
            --brand-gradient: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-border: rgba(0, 0, 0, 0.06);
        }

     :root {
      --primary:   #1a73e8;
      --primary-dark: #0d5abf;
      --accent:    #ff6b00;
      --accent-light: #ff8c38;
      --dark:      #0d1b2a;
      --dark-2:    #1c2e44;
      --text:      #3d4f60;
      --text-light:#6b7c93;
      --light-bg:  #f4f8ff;
      --white:     #ffffff;
      --border:    #e2ecf8;
      --success:   #28a745;
    }    

        .stats-wrapper {
            width: 100%;
            padding: 0px 10px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .stats-container {
            display: grid;
            /* Force all items into 1 row */
            grid-template-columns: repeat(6, 1fr);
            gap: 15px;
            width: 100%;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 10px;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-card:hover {
            border-color: var(--brand-blue);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
            transform: translateY(-4px);
        }

        .stat-number-wrapper {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            /* Lighter font weight */
            font-weight: 500; 
            color: var(--brand-blue);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 4px;
            letter-spacing: -0.01em;
        }

        .stat-label {
            font-size: 0.85rem;
            /* Lighter font weight */
            font-weight: 400;
            color: var(--text-muted);
            margin: 0;
            letter-spacing: 0.02em;
        }

        /* Adjustments for smaller screens to prevent overcrowding */
        @media (max-width: 1200px) {
            .stat-number-wrapper { font-size: 1.8rem; }
            .stat-label { font-size: 0.75rem; }
        }

        @media (max-width: 992px) {
            .stats-container {
                grid-template-columns: repeat(3, 1fr); /* 2 rows on tablet */
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr); /* 3 rows on mobile */
            }
        }


        /* MISSION AND VISION CSS */

         .app-container {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #020617;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            color: #f8fafc;
            min-height: 100vh;
            position: relative;
        }

        /* --- Interactive Dark Background --- */
        .hero-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(168, 85, 247, 0.15) 0%, transparent 40%);
            z-index: 1;
        }

       

        /* --- Mission & Vision Section Styling --- */
        .purpose-section {
            padding: 70px 0;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 3;
        }

        .purpose-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 40px;
        }

        .purpose-row {
            display: flex;
            gap: 40px;
            perspective: 1200px;
        }

        .purpose-card {
            flex: 1;
            position: relative;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 40px;
            padding: 30px;
            transition: all 0.7s cubic-bezier(0.23, 1, 0.32, 1);
            overflow: hidden;
            box-shadow: 0 20px 50px -20px rgba(0,0,0,0.5);
        }

        /* Hover Glow Effect */
        .purpose-card:hover {
            transform: translateY(-20px) rotateX(2deg);
            box-shadow: 0 50px 100px -30px rgba(0, 0, 0, 0.8);
            border-color: rgba(255, 255, 255, 0.25);
            background: rgba(15, 23, 42, 0.8);
        }

        .card-glow {
            position: absolute;
            width: 400px; height: 400px;
            background: linear-gradient(135deg, #3b82f6 0%, #a855f7 100%);
            filter: blur(100px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.6s ease;
            z-index: 0;
            border-radius: 50%;
        }

        .purpose-card:hover .card-glow {
            opacity: 0.3;
        }

        .content-wrap {
            position: relative;
            z-index: 1;
        }

        .icon-box {
            width: 65px; height: 65px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            transition: all 0.5s ease;
        }

        .purpose-card:hover .icon-box {
            background: linear-gradient(135deg, #3b82f6 0%, #a855f7 100%);
            transform: scale(1.1) rotate(-8deg);
            border-color: transparent;
        }

        .purpose-tag {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: #3b82f6;
            font-size: 18px;
            display: block;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .purpose-title {
            font-weight: 400;
            font-size: 35px;
            line-height: 1.2;
            margin-bottom: 30px;
            color: #f8fafc;
        }

        .purpose-title span {
            font-weight: 500;
            color: #ffffff;
            text-shadow: 0 0 20px rgba(255,255,255,0.2);
        }

        .purpose-description {
            font-size: 1.15rem;
            line-height: 1.9;
            color: #94a3b8;
            font-weight: 300;
            margin: 0;
        }

        /* --- Scroll Reveal Animations --- */
        .reveal {
            opacity: 0;
            transform: translateY(80px);
            transition: all 1.4s cubic-bezier(0.19, 1, 0.22, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* --- Responsive Design --- */
        @media (max-width: 992px) {
            .purpose-row { flex-direction: column; gap: 40px; }
            .purpose-card { padding: 50px 30px; }
            .purpose-title { font-size: 2.4rem; }
            .purpose-section { padding: 100px 0; }
        }

        @media (max-width: 576px) {
            .purpose-title { font-size: 2rem; }
            .purpose-container { padding: 0 24px; }
            .icon-box { margin-bottom: 30px; }
        }


        /* TIMELINE JOURNEY CSS */

         .journey-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 80px 40px;
        }

        .journey-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 100px;
            align-items: start;
        }

        /* --- Left Side: Sticky Content --- */
        .sticky-content {
            position: sticky;
            top: 90px;
        }

        .main-title {
            font-family: 'Outfit', sans-serif;
            font-size: 45px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 30px;
            color: #0f172a;
        }

        

        .image-showcase {
            position: relative;
            margin-top: 50px;
        }

        .img-card {
            background: #ffffff;
            border-radius: 40px;
            overflow: hidden;
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.12);
            border: 1px solid #f1f5f9;
            position: relative;
            z-index: 2;
        }

        .img-card img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Floating Badge Design */
        .floating-badge {
            position: absolute;
            background: #ffffff;
            padding: 18px 24px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid #f1f5f9;
            z-index: 10;
        }

        .badge-1 { bottom: -15px; right: -25px; }
        .badge-2 { top: 140px; left: -35px; }

        .icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- Right Side: Timeline --- */
        .timeline-scroll {
            position: relative;
            padding-left: 60px;
        }

        .timeline-line {
            position: absolute;
            left: 0;
            top: 20px;
            bottom: 0;
            width: 2px;
            background: #f1f5f9;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 40px;
        }

        .timeline-dot {
            position: absolute;
            left: -66px;
            top: 30px;
            width: 14px;
            height: 14px;
            background: #ffffff;
            border: 4px solid #cbd5e1;
            border-radius: 50%;
            z-index: 5;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .timeline-item.active .timeline-dot {
            border-color: #2563eb;
            background: #2563eb;
            transform: scale(1.4);
            box-shadow: 0 0 20px rgba(37, 99, 235, 0.3);
        }

        .timeline-card {
            border-radius: 32px;
            padding: 18px;
            border: 1px solid transparent;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        /* Colorful Variations */
        .card-blue { background-color: #f0f7ff; }
        .card-green { background-color: #f0fdf4; }
        .card-purple { background-color: #faf5ff; }
        .card-orange { background-color: #fffaf0; }

        .timeline-item:hover .timeline-card {
            transform: translateX(12px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.03);
        }

        .card-icon-wrap {
            width: 56px;
            height: 56px;
            background: #ffffff;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.04);
        }

        .timeline-card h4 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: #1e293b;
        }

        .timeline-card p {
            color: #64748b;
            line-height: 1.7;
            margin: 0;
            font-size: 1.05rem;
            font-weight: 400;
        }

        .swirl-icon {
            position: absolute;
            top: 25%;
            right: 115%;
            width: 80px;
            opacity: 0.3;
        }

        @media (max-width: 1100px) {
            .journey-grid { gap: 40px; }
            .main-title { font-size: 3rem; }
        }

        @media (max-width: 992px) {
            .journey-grid { grid-template-columns: 1fr; }
            .sticky-content { position: relative; top: 0; margin-bottom: 60px; }
        }

</style>    

 








<!-- Hero section -->
<header class="hero-gradient">
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;"></div>
    <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;"></div>

    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <!-- Left Content -->
            <div class="col-md-6 text-md-start text-center animate__animated animate__fadeInLeft">
                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    {{ data_get($heroContent, 'content.badge_text', 'Warehousing Solutions') }}
                </div>

                <h1 class="hero-title mb-4">
                    {!! data_get($heroContent, 'content.title', 'Store. Manage. Fulfil—Smarter <span class="moving-gradient-text">from The USA</span>') !!}
                </h1>

                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ data_get($heroContent, 'content.paragraphs', 'Our US warehousing facility helps businesses reduce delivery timelines, manage inventory efficiently, and fulfil international orders with greater speed, control, and reliability.') }}
                </p>

                <a href="#" class="book-btn-service"><i class="fas fa-paper-plane"></i> Book a Shipping</a> &nbsp;  <a href="#" class="quote-btn-service"><i class="fas fa-calculator"></i> Get a Quote</a>

                <div class="hero-badges">
                    <!-- @foreach(data_get($heroContent, 'content.list_items', [
                        'Inventory Management',
                        'Fast Fulfillment',
                        '220+ Countries',
                    ]) as $badge)
                        <div class="hero-badge"><i class="fas fa-check-circle"></i> {{ $badge }}</div>
                    @endforeach -->

                    <div class="hero-badge"><i class="fas fa-clock"></i>Inventory Management</div>
                    <div class="hero-badge"><i class="fas fa-shield-alt"></i>Fast Fulfillment</div>
                    <div class="hero-badge"><i class="fas fa-map-marker-alt"></i>220+ Countries</div>
                </div>
            </div>

            <!-- Right Image -->
            <!-- <div class="col-md-6 text-center">
                <div class="hero-graphic">
                    <div style="width: 400px; height: 400px;" class="plane-circle">
                        <img src="{{ data_get($heroContent, 'content.image', 'images/warehousing.webp') }}" class="img-fluid" style="border-radius: 40px;">
                    </div>
                </div>
            </div> -->
            <div class="col-md-6 text-center">
                <div class="hero-graphic">
                    <div style="width: 400px; height: 400px;" class="plane-circle">
                        <img src="{{ asset('assets/images/warehousing.webp') }}" class="img-fluid" style="border-radius: 40px;">
                        <!-- Stat pills -->
                        <div class="stat-pill pill-1">
                            <div class="sp-icon" style="background:rgba(26,115,232,.1);color:var(--primary)"><i
                                    class="fas fa-box"></i></div>
                            <div>
                                <div class="sp-val">50K+</div>
                                <div class="sp-lbl">Shipments/Month</div>
                            </div>
                        </div>
                        <div class="stat-pill pill-2">
                            <div class="sp-icon" style="background:rgba(255,107,0,.1);color:var(--accent)"><i
                                    class="fas fa-star"></i></div>
                            <div>
                                <div class="sp-val">4.9★</div>
                                <div class="sp-lbl">Avg Rating</div>
                            </div>
                        </div>
                        <div class="stat-pill pill-3">
                            <div class="sp-icon" style="background:rgba(40,167,69,.1);color:var(--success)"><i
                                    class="fas fa-check-circle"></i></div>
                            <div>
                                <div class="sp-val">99.2%</div>
                                <div class="sp-lbl">On-Time Rate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>




<!-- FACTS NUMBER section -->

 <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">{!! data_get($statsContent->first(), 'content.title', 'Trusted by over <span class="gradient-text">50,000 Businesses</span> for daily logistics') !!}</h2>
            </div>
            
            <div class="stats-wrapper">
                <div class="stats-container">
                    @forelse($statsContent as $stat)
                        <div class="stat-card">
                            <div class="stat-number-wrapper">
                                <span class="stat-number" data-target="{{ data_get($stat, 'content.stat_number', 0) }}">0</span>{{ data_get($stat, 'content.suffix', '') }}
                            </div>
                            <p class="stat-label">{{ data_get($stat, 'content.stat_label', 'Statistic') }}</p>
                        </div>
                    @empty
                        <div class="stat-card">
                            <div class="stat-number-wrapper"><span class="stat-number" data-target="150">0</span>+</div>
                            <p class="stat-label">Cities Covered</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number-wrapper"><span class="stat-number" data-target="100">0</span>K+</div>
                            <p class="stat-label">Daily Parcels</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number-wrapper"><span class="stat-number" data-target="5">0</span>K+</div>
                            <p class="stat-label">Delivery Riders</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number-wrapper"><span class="stat-number" data-target="99">0</span>.9%</div>
                            <p class="stat-label">On-time Rate</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number-wrapper"><span class="stat-number" data-target="24">0</span>/7</div>
                            <p class="stat-label">Live Tracking</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number-wrapper"><span class="stat-number" data-target="50">0</span>K+</div>
                            <p class="stat-label">Happy Clients</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
  </section>


    <!-- OVERVIEW About Section -->
    <section class="about-section">
        <div class="container">
            <div class="row align-items-center">

            <div class="col-lg-6 animate-on-scroll" data-anim="animate__fadeInLeft" style="animation-delay: 0.1s;">
                    <div class="about-image-grid">
                        <div class="world-map-placeholder">
                            <!-- <img src="{{ data_get($overviewContent, 'content.image', 'images/map-pattern.png') }}" class="img-fluid"> -->
                            <img src="{{ asset('assets/images/map-pattern.png') }}" class="img-fluid">
                        </div>
                    </div>
                </div>
                               
                <!-- Content Side -->
                <div class="col-lg-6">
                    <div class="about-content">
                        <h2 class="about-title">{!! data_get($overviewContent, 'content.title', 'Store. Manage. Fulfil—Smarter <span class="moving-gradient-text">from The USA</span>') !!}</h2>
                                <p class="lead">{{ data_get($overviewContent, 'content.paragraphs', 'United Worldwide Couriers offers an exceptional Express Air Freight service built for B2B enterprises, e-commerce businesses, and time-critical international shipments. We operate an end-to-end ecosystem from collection to customs clearance to last-mile delivery all under one roof. Our services span air freight to over 220 countries, including door-to-door personal import pickups and full freight-forwarding with seamless documentation. Each shipment is handled by a dedicated logistics specialist and backed by real-time tracking.') }}</p>

                                <ul class="check-list lead">
                                    @foreach(data_get($overviewContent, 'content.list_items', [
                                        'Priority loading on partner airline networks across 6 continents',
                                        'Full customs brokerage with pre-clearance documentation support',
                                        'Dedicated account manager and 24/7 live shipment support',
                                        'Door-to-door delivery with real-time GPS tracking portal',
                                        'Fulfilment services, returns management, and COD options',
                                    ]) as $bullet)
                                        <li><i class="fas fa-check-circle"></i> {{ $bullet }}</li>
                                    @endforeach
                                </ul>

                                <a href="{{ data_get($overviewContent, 'content.button_url', '#') }}" class="sr-demo-btn-live mt-3">{{ data_get($overviewContent, 'content.button_text', 'Book Shipments') }}</a>
                    </div>
                </div>

            </div>
        </div>
</section>


<!-- Features cards section -->

<section class="features-section" style="padding:40px 0;">
  <div class="container">

  <div class="row justify-content-center mb-3">
            <div class="col-lg-10 text-center">
                <h2 class="about-title">{{ data_get($featuresHeaderContent, 'content.title', 'What Makes Our E-commerce Logistics Stand Out') }}</h2>

                <p class="about-desc text-center">
                    {{ data_get($featuresHeaderContent, 'content.paragraphs', 'Every feature is designed to give your business a competitive edge — speed, transparency, and reliability, every single shipment. Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record speaks for itself.') }}
                </p>
            </div>
        </div>

    <div class="row g-4">
        @forelse($featuresContent as $feature)
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon {{ data_get($feature, 'content.icon_class', 'fi-blue') }}">
                        {!! data_get($feature, 'content.icon_svg', '<i class="fas fa-satellite"></i>') !!}
                    </div>
                    <h5>{{ data_get($feature, 'content.subtitle', 'Feature Title') }}</h5>
                    <p>{{ data_get($feature, 'content.paragraphs', 'Feature description goes here.') }}</p>
                </div>
            </div>
        @empty
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon fi-blue"><i class="fas fa-satellite"></i></div>
                    <h5>Real-Time Tracking</h5>
                    <p>Monitor your shipment at every checkpoint — from pickup to customs to final-mile — through our live dashboard and SMS alerts.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon fi-orange"><i class="fas fa-thermometer-half"></i></div>
                    <h5>Temperature Control</h5>
                    <p>Specialized cold-chain and pharma-grade shipping options for sensitive or perishable cargo types.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon fi-navy"><i class="fas fa-headset"></i></div>
                    <h5>24/7 Support</h5>
                    <p>Our logistics experts are available around the clock — via phone, chat, or email — to resolve any issue in real time.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon fi-green"><i class="fas fa-leaf"></i></div>
                    <h5>Eco-Warehousing Options</h5>
                    <p>Carbon-offset shipping options and sustainable packaging solutions for businesses committed to reducing their footprint.</p>
                </div>
            </div>
        @endforelse
    </div>
  </div>
</section>


  










@if($ctaContent)
<section class="cta-section py-5 bg-primary text-white" style="display:none">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="about-title text-white">{{ data_get($ctaContent, 'content.title', 'Ready to simplify your warehousing and fulfillment?') }}</h2>
                <p class="about-desc text-white">{{ data_get($ctaContent, 'content.subtitle', 'Let our warehousing solutions take the complexity out of your supply chain, so you can focus on growth and customer satisfaction.') }}</p>
            </div>
            <div class="col-lg-4 text-lg-end text-center mt-4 mt-lg-0">
                <a href="{{ data_get($ctaContent, 'content.button_url', '#') }}" class="btn btn-light btn-lg">{{ data_get($ctaContent, 'content.button_text', 'Get Started Today') }}</a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- testimonial -->
<section class="testimonial-section" style="display:none">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-3">
            <div class="col-lg-8 text-center">
                <div class="google-badge">
                    <a href="#">
                        <img src="{{ asset('public/website_images/google-review.png') }}" alt="Google">
                    </a>
                </div>
                <h2 class="about-title">Trusted by the Brands You Trust</h2>

                <p class="about-desc text-center">
                    Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record speaks for itself. From on-time delivery and zero-compromise on quality to customer satisfaction, United Worldwide Couriers is completely reliable and trustworthy.
                </p>
            </div>
        </div>

        <div class="slider-wrapper">
            <div class="slider-track">
                @forelse($testimonials as $testimonial)
                    <div class="testimonial-card">
                        <div class="stars">{{ str_repeat('★', $testimonial->rating ?? 5) }}</div>
                        <p class="testimonial-text">"{{ $testimonial->content }}"</p>
                        <div class="user-info">
                            <img src="{{ asset($testimonial->customer_image ?? 'public/website_images/review-1.png') }}" class="img-fluid" alt="testimonial" />
                            <h6>{{ $testimonial->customer_name }}</h6>
                        </div>
                    </div>
                @empty
                    <div class="testimonial-card">
                        <div class="stars">★★★★★</div>
                        <p class="testimonial-text">"Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues."</p>
                        <div class="user-info"> <img src="{{ asset('public/website_images/review-1.png') }}" class="img-fluid"> <h6>Shelly Kapoor</h6></div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>



<!-- FAQ Section -->
 <section class="faq-section" style="display:none">
    <div class="container">
        <div class="faq-header">
            <span class="heading-badge">{{ data_get($faqHeaderContent, 'content.badge', 'Common Questions') }}</span>
            <h2 class="about-title">{{ data_get($faqHeaderContent, 'content.title', 'Frequently Asked Questions') }}</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="faq-illustration">
                    <img src="{{ data_get($faqHeaderContent, 'content.illustration_image', 'https://i.pinimg.com/originals/f6/5d/46/f65d4681649d85bc91c86872a1775919.gif') }}" alt="Help" style="width: 200px; margin-top: -40px;">
                    <h4 class="fw-bold mb-3">{{ data_get($faqHeaderContent, 'content.help_title', 'Need personalized help?') }}</h4>
                    <p class="text-muted">{{ data_get($faqHeaderContent, 'content.help_description', 'Our logistics experts are available 24/7 to assist your requirements.') }}</p>
                    
                    <div class="moving-gradient-bg contact-box">
                        <h4>{{ data_get($faqHeaderContent, 'content.contact_title', 'Contact Us') }}</h4>
                        <p>{{ data_get($faqHeaderContent, 'content.contact_description', 'For urgent inquiries regarding your current shipment status.') }}</p>
                        <button style="background-color: #fff; color: #2563eb;" class="btn btn-contact">{{ data_get($faqHeaderContent, 'content.contact_button_text', 'Message Support') }}</button>
                    </div>
                </div>
            </div>
            

            <div class="col-lg-8">
                <div class="accordion" id="logisticsFaq" style="height: 70vh; overflow-y: auto;">
                    @forelse($faqContent as $index => $faqItem)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index + 1 }}">
                                    {{ $faqItem->question }}
                                </button>
                            </h2>
                            <div id="faq{{ $index + 1 }}" class="accordion-collapse collapse{{ $index === 0 ? ' show' : '' }}" data-bs-parent="#logisticsFaq">
                                <div class="accordion-body">
                                    {{ $faqItem->answer }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How do I get started?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#logisticsFaq">
                                <div class="accordion-body">
                                    To connect with our team, you have to register yourself, get a quote, and schedule your first pickup. Thereafter, the team will guide you through every step of the process.
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
            }, { threshold: 0.2 });

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
