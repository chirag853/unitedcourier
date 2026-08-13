@include('website_include.header')

<!-- Hero section -->
<header class="hero-gradient">
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
    </div>
    <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;">
    </div>

    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <div class="col-lg-7 animate__animated animate__fadeInLeft">
                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    {{ $heroData['badge'] ?? 'Trusted by Growing Businesses Across India' }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $heroData['title'] ?? 'E-commerce Speed. B2B Reliability. Ship Simply <br
                        class="d-none d-md-block"> <span class="moving-gradient-text">United Couriers.</span>' !!}
                </h1>
                <p class="lead mb-5">
                    {!! $heroData['subtitle'] ?? 'From First Click to Delivery. Your Gateway to Seamless Shipping
                    Worldwide.' !!}
                </p>

                <div class="row g-3 mb-5">
                    <div class="col-sm-4">
                        <div class="stat-card">
                            <div class="text-success fs-4"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 14px;">1 Crore+</div>
                                <div class="text-muted" style="font-size: 11px;">Shipments Delivered</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="stat-card">
                            <div class="text-primary fs-4"><i class="fas fa-bolt"></i></div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 14px;">Instant Pickup</div>
                                <div class="text-muted" style="font-size: 11px;">Across major cities</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="integrations">
                    <p class="text-uppercase fw-bold text-muted mb-3" style="font-size: 13px; letter-spacing: 2px;">
                        Worldwide Marketplaces</p>

                </div>


                <div class="logo-slider">
                    <div class="logo-track">
                        @if($marketplaceLogos->count() > 0)
                            <!-- Original Logos -->
                            @foreach($marketplaceLogos as $card)
                            <div class="logo-item">
                                <img src="{{ asset($card->image) }}" alt="{{ $card->title ?? 'Marketplace' }}">
                            </div>
                            @endforeach

                            <!-- Cloned Logos (for seamless loop) -->
                            @foreach($marketplaceLogos as $card)
                            <div class="logo-item">
                                <img src="{{ asset($card->image) }}" alt="{{ $card->title ?? 'Marketplace' }}">
                            </div>
                            @endforeach
                        @else
                            <div class="logo-item">
                                <img src="{{ asset('/website_images/ebay.webp') }}" alt="eBay">
                            </div>
                            <div class="logo-item">
                                <img src="{{ asset('/website_images/etsy.webp') }}" alt="etsy">
                            </div>
                            <div class="logo-item">
                                <img src="{{ asset('/website_images/amazon.webp') }}" alt="Amazon">
                            </div>
                            <div class="logo-item">
                                <img src="{{ asset('/website_images/shopify.webp') }}" alt="Shopify">
                            </div>
                            <div class="logo-item">
                                <img src="{{ asset('/website_images/walmart.webp') }}" alt="Walmart">
                            </div>
                        @endif
                    </div>
                </div>



            </div>

            <div class="col-lg-5 animate__animated animate__fadeInRight">
                <div class="form-shadow mx-auto">
                    <!-- <div class="gradient-bg discount-badge">UP TO 40% OFF</div> -->
                    <div class="mb-4">
                        <h3 class="h4-title">Explore Our <span class="gradient-text">Pricing</span></h3>
                    </div>

                    <form>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="input-group-custom">
                                    <input type="text" class="form-control input-custom" placeholder="First Name">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group-custom">
                                    <input type="text" class="form-control input-custom" placeholder="Last Name">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="input-group-custom">
                                    <input type="email" class="form-control input-custom" placeholder="Email">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group-custom">
                                    <input type="tel" class="form-control input-custom" placeholder="Phone">
                                    <i class="fas fa-phone"></i>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="input-group-custom">
                                    <input type="text" class="form-control input-custom" placeholder="Origin">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group-custom">
                                    <select class="form-select input-custom">
                                        <option selected disabled>Destination</option>
                                        <option>USA</option>
                                        <option>UK</option>
                                        <option>Canada</option>
                                        <option>Australia</option>
                                    </select>
                                    <i class="fas fa-globe-americas"></i>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group-custom">
                                <select class="form-select input-custom">
                                    <option selected disabled>Select Your Business Category</option>
                                    <option>E-commerce</option>
                                    <option>B2B/Export</option>
                                    <option>Individual</option>
                                </select>
                                <i class="fas fa-briefcase"></i>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="input-group-custom">
                                <select class="form-select input-custom" style="border: 2px solid var(--brand-blue);">
                                    <option selected disabled>Average Monthly Volume?</option>
                                    <option>0-100</option>
                                    <option>100-500</option>
                                    <option>500-1000</option>
                                    <option>1000+</option>
                                </select>
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>

                        <button type="button" class="btn moving-gradient-bg btn-primary-custom">
                            Get Quotes
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>



<!-- About Section -->
<section class="about-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="">
                <span class="heading-badge animate-on-scroll" data-anim="animate__fadeInRight"
                    style="animation-delay: 0.2s;">{{ $aboutData['badge'] ?? 'About United Worldwide Couriers' }}</span>
                <h2 class="about-title">{{ $aboutData['heading'] ?? 'One Partner. Infinite Logistics Possibilities.' }}
                </h2>
                <p class="about-desc animate-on-scroll" data-anim="animate__fadeInUp" style="animation-delay: 0.5s;">
                    {!! $aboutData['description'] ?? 'United Worldwide Couriers delivers integrated logistics solutions
                    for modern B2B enterprises, e-commerce brands, and growing businesses. Our services cover
                    international Air Express & Freight, pan-India pickup, customs clearance with documentation support,
                    and fulfilment solutions, all managed under one reliable platform. <br> With a strong operational
                    network and an experienced logistics team, we help clients move shipments efficiently, reduce
                    delays, and manage complex requirements with confidence. Every shipment is handled with proactive
                    coordination, transparent tracking, and dedicated customer support to ensure a smooth and dependable
                    delivery experience.' !!}
                </p>
            </div>
            <!-- Image Side -->
            <!-- <div class="col-lg-6 animate-on-scroll" data-anim="animate__fadeInLeft" style="animation-delay: 0.1s;">
                    <div class="about-image-grid">
                        <img src="{{ asset('/website_images/logistic-ship.webp') }}" alt="About Logistics" class="main-about-img">
                        <img src="{{ asset('/website_images/truck.webp') }}" alt="Warehouse" class="floating-about-img">
                        <div class="gradient-bg experience-card">
                            <h3>15+</h3>
                            <p>Years of<br>Experience</p>
                        </div>
                    </div>
                </div> -->

            <div class="col-lg-6 animate-on-scroll" data-anim="animate__fadeInLeft" style="animation-delay: 0.1s;">
                <div class="about-image-grid">
                    @php
                        $aboutMediaPath = trim((string) ($aboutData['media_path'] ?? ''));
                        $aboutMediaType = strtolower(trim((string) ($aboutData['media_type'] ?? '')));
                        $aboutMediaUrl = $aboutMediaPath !== ''
                            ? asset(ltrim($aboutMediaPath, '/'))
                            : asset('website_images/truck-video.mp4');
                        $aboutMediaExtension = strtolower(pathinfo($aboutMediaPath, PATHINFO_EXTENSION));
                        $aboutVideoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];
                        $isAboutVideo = $aboutMediaType === 'video'
                            || in_array($aboutMediaExtension, $aboutVideoExtensions, true);
                    @endphp

                    @if ($aboutMediaPath !== '' && ! $isAboutVideo)
                        <img src="{{ $aboutMediaUrl }}" alt="About United Worldwide Couriers" class="main-about-img">
                    @else
                        <video src="{{ $aboutMediaUrl }}" autoplay muted loop playsinline preload="auto"
                            class="main-about-img">
                            Your browser does not support the video tag.
                        </video>
                    @endif
                </div>
            </div>

            <!-- Content Side -->
            <div class="col-lg-6">
                <div class="about-content">

                    <!-- Features with staggered delay -->
                    <div class="feature-item animate-on-scroll" data-anim="animate__fadeInUp"
                        style="animation-delay: 0.3s;">
                        <div class="feature-icon"><i class="fa-solid fa-user-shield"></i></div>
                        <div class="feature-text">
                            <h5>{{ $aboutData['feature1_title'] ?? 'Tailored Shipping for Every Business Model' }}</h5>
                            <p>{{ $aboutData['feature1_desc'] ?? 'Whether you are a B2B manufacturer, e-commerce brand, distributor, dropshipper, or individual shipper, we manage your complete delivery journey across 220+ countries with speed, visibility, and dependable support.' }}
                            </p>
                        </div>
                    </div>

                    <div class="feature-item animate-on-scroll" data-anim="animate__fadeInUp"
                        style="animation-delay: 0.4s;">
                        <div class="feature-icon"><i class="fa-solid fa-globe"></i></div>
                        <div class="feature-text">
                            <h5>{{ $aboutData['feature2_title'] ?? 'Smarter Cost for High-Volume Shipping' }}</h5>
                            <p>{{ $aboutData['feature2_desc'] ?? 'Our volume-based pricing, shipment consolidation, and optimized routing solutions help businesses reduce logistics costs while improving efficiency across international operations.' }}
                            </p>
                        </div>
                    </div>

                    <div class="feature-item animate-on-scroll" data-anim="animate__fadeInUp"
                        style="animation-delay: 0.4s;">
                        <div class="feature-icon"><i class="fa-solid fa-truck"></i></div>
                        <div class="feature-text">
                            <h5>{{ $aboutData['feature3_title'] ?? 'Built to Scale With Your Business' }}</h5>
                            <p>{{ $aboutData['feature3_desc'] ?? 'With warehousing, cross-docking, and fulfilment support, we help businesses scale from hundreds to thousands of shipments a day without compromising service quality, timelines, or customer experience.' }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>


<!-- Stack Section -->
<div class="container-fluid" style="background-color: #f5f9fc;">
    <section class="stack-container container">

        <h2 class="display-5 fw-bolder mb-3 animate-on-scroll" data-anim="animate__fadeInRight"
            style="animation-delay: 0.2s;">{!! $servicesHeading['heading'] ?? 'Powering Your Business with <span class="moving-gradient-text"> Our Services</span>' !!}</h2>
        <p class="about-desc mb-5 animate-on-scroll" data-anim="animate__fadeInUp" style="animation-delay: 0.5s;">{{ $servicesHeading['description'] ?? 'From urgent documents to high-volume commercial cargo, our logistics solutions are built to move every shipment with speed, precision, and complete reliability.' }}</p>

        <div class="stack-wrapper">
            <!-- Card 1 -->
            <div class="mainCardWrapper bluePurpelGradient card-item">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7">
                        <div class="numWrapper text-primary">
                            {{ $serviceCard1['label'] ?? 'Express Air Freight Solutions' }}</div>
                        <h2 class="gradient-text card-title">
                            {{ $serviceCard1['title'] ?? 'Air Express Services Built Around Your Needs' }}</h2>
                        <p class="card-desc mt-3">
                            {{ $serviceCard1['description'] ?? 'Our Express services are designed to give customers complete flexibility — from faster 3–4 day delivery options to more economical 8–10 day delivery solutions, allowing every customer to choose a service based on their urgency, budget, and delivery timeline.' }}
                        </p>

                        <div class="row g-2 g-sm-3 mt-3">
                            <div class="col-6">
                                <div class="smallCard">
                                    <div class="card-icon">✈️</div>
                                    <h5>{{ $serviceCard1['small1_title'] ?? 'Flexible Delivery Options' }}</h5>
                                    <p class="mb-0 text-muted small d-none d-sm-block">
                                        {{ $serviceCard1['small1_desc'] ?? 'Priority and economy services are designed around your budget and timeline.' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="smallCard">
                                    <div class="card-icon">📦</div>
                                    <h5>{{ $serviceCard1['small2_title'] ?? 'Real Time Tracking' }}</h5>
                                    <p class="mb-0 text-muted small d-none d-sm-block">
                                        {{ $serviceCard1['small2_desc'] ?? 'Instant tracking updates from pickup to final door-to-door delivery.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card-image-container">
                            <img src="{{ asset($serviceCard1['image'] ?? '/website_images/air-freight.webp') }}" alt="Ocean Freight">
                        </div>
                    </div>
                </div>
            </div>



            <!-- Card 4 -->
            <div class="mainCardWrapper purpelYellowGradient card-item">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7">
                        <div class="numWrapper text-secondary">
                            {{ $serviceCard2['label'] ?? 'E-commerce Logistics Solutions' }}</div>
                        <h2 class="card-title">{{ $serviceCard2['title'] ?? 'Built for Sellers. Designed for scale' }}
                        </h2>
                        <p class="card-desc mt-3">
                            {{ $serviceCard2['description'] ?? 'Connect your marketplace account directly with our platform and ship orders with ease — starting from lightweight parcels of just 50g to high-volume e-commerce shipments.' }}
                        </p>

                        <div class="row g-2 g-sm-3 mt-3">
                            <div class="col-6">
                                <div class="smallCard">
                                    <div class="card-icon">💰</div>
                                    <h5>{{ $serviceCard2['small1_title'] ?? 'No Platform Fee' }}</h5>
                                    <p class="mb-0 text-muted small d-none d-sm-block">
                                        {{ $serviceCard2['small1_desc'] ?? 'Reduce costs and protect your margins.' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="smallCard">
                                    <div class="card-icon">💳</div>
                                    <h5>{{ $serviceCard2['small2_title'] ?? 'Complete Fulfilment Support' }}</h5>
                                    <p class="mb-0 text-muted small d-none d-sm-block">
                                        {{ $serviceCard2['small2_desc'] ?? 'Order processing, pickup, dispatch, tracking, and final delivery.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card-image-container">
                            <img src="{{ asset($serviceCard2['image'] ?? '/website_images/supply-chain.webp') }}">
                        </div>
                    </div>
                </div>
            </div>



            <!-- Card 2 -->
            <div class="mainCardWrapper orangeGreenGradient card-item">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7">
                        <div class="numWrapper">{{ $serviceCard3['label'] ?? 'Warehousing Solutions' }}</div>
                        <h2 class="card-title">
                            {{ $serviceCard3['title'] ?? 'Store. Manage. Fulfil—Smarter from The USA' }}</h2>
                        <p class="card-desc mt-3">
                            {{ $serviceCard3['description'] ?? 'Our US warehousing facility helps businesses reduce delivery timelines, manage inventory efficiently, and fulfil international orders with greater speed, control, and reliability.' }}
                        </p>

                        <div class="row g-2 g-sm-3 mt-3">
                            <div class="col-6">
                                <div class="smallCard">
                                    <div class="card-icon">🏬</div>
                                    <h5>{{ $serviceCard3['small1_title'] ?? 'Inventory Management' }}</h5>
                                    <p class="mb-0 text-muted small d-none d-sm-block">
                                        {{ $serviceCard3['small1_desc'] ?? 'Real-time stock visibility with better operational control.' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="smallCard">
                                    <div class="card-icon">🚚</div>
                                    <h5>{{ $serviceCard3['small2_title'] ?? 'Fast Fulfillment' }}</h5>
                                    <p class="mb-0 text-muted small d-none d-sm-block">
                                        {{ $serviceCard3['small2_desc'] ?? 'Speedy dispatch support for e-commerce and B2B shipments.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card-image-container">
                            <img src="{{ asset($serviceCard3['image'] ?? '/website_images/warehousing.webp') }}" alt="Warehousing">
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </section>
</div>



<!-- our work process steps -->
<section class="process-section">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="section-tag">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                    </svg>
                    {{ $processData['section_tag'] ?? 'Getting Started' }}
                </div>
                <h2 class="section-title">
                    {{ $processData['heading'] ?? 'Start Shipping with United Couriers in 4 Simple Steps' }}</h2>
            </div>
        </div>

        <!-- Process Grid -->
        <div class="row process-row">
            <!-- Background Curved Line -->
            <svg class="curved-line" viewBox="0 0 1000 100" preserveAspectRatio="none">
                <path class="path-animation" d="M0,50 Q125,100 250,50 T500,50 T750,50 T1000,50" fill="none"
                    stroke="#2563eb" stroke-width="2" stroke-dasharray="8,8" />
            </svg>

            <!-- Step 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="process-step">
                    <div class="icon-box-wrapper">
                        <div class="moving-gradient-bg step-number">01</div>
                        <div class="icon-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path
                                    d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="step-title">{{ $processSteps[1]['step_title'] ?? 'Create Your Account' }}</h3>
                    <p class="step-description">
                        {{ $processSteps[1]['step_desc'] ?? 'Sign up and set up your shipping profile to manage orders, bookings, invoices, and tracking from one dashboard.' }}
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="process-step">
                    <div class="icon-box-wrapper">
                        <div class="moving-gradient-bg step-number">02</div>
                        <div class="icon-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                </path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                        </div>
                    </div>
                    <h3 class="step-title">{{ $processSteps[2]['step_title'] ?? 'Choose Your Service' }}</h3>
                    <p class="step-description">
                        {{ $processSteps[2]['step_desc'] ?? 'Enter your shipment details and compare available delivery options based on price, speed, and service type.' }}
                    </p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="process-step">
                    <div class="icon-box-wrapper">
                        <div class="moving-gradient-bg step-number">03</div>
                        <div class="icon-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z">
                                </path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                        </div>
                    </div>
                    <h3 class="step-title">{{ $processSteps[3]['step_title'] ?? 'Schedule Your Pickup' }}</h3>
                    <p class="step-description">
                        {{ $processSteps[3]['step_desc'] ?? 'Select your preferred service, confirm the shipment, and book your pickup.' }}
                    </p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="process-step">
                    <div class="icon-box-wrapper">
                        <div class="moving-gradient-bg step-number">04</div>
                        <div class="icon-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                        </div>
                    </div>
                    <h3 class="step-title">{{ $processSteps[4]['step_title'] ?? 'Track Your Shipment' }}</h3>
                    <p class="step-description">
                        {{ $processSteps[4]['step_desc'] ?? 'Stay updated from pickup to final delivery with real-time tracking and proactive shipment updates.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Tailor-Made Shipping Option SECTION -->
<section class="sr-demo-section">
    <div class="container">
        <h2 class="sr-demo-title"></h2>

        <div class="animate-on-scroll show animate__animated animate__fadeInRight" data-anim="animate__fadeInRight"
            style="animation-delay: 0.2s;">
            @php
            $headingRecord = $shippingSolutions->firstWhere('field_name', 'heading');
            $descRecord = $shippingSolutions->firstWhere('field_name', 'description');
            $shippingHeading = $headingRecord ? $headingRecord->content : 'Shipping Solutions Designed Around You!';
            $shippingDesc = $descRecord ? $descRecord->content : 'No two businesses ship the same way. That’s why United
            Worldwide Couriers offers flexible logistics solutions built around your shipment type, delivery timeline,
            budget, and business goals. Whether you need B2B Export Support, Dropshipping Solutions, Marketplace
            shipping, or personal deliveries for friends and family, we help you choose the right service with clarity,
            reliability, and complete support.';
            @endphp
            <h2 class="about-title">{{ $shippingHeading }}</h2>
            <p class="about-desc text-center">{{ $shippingDesc }}</p>
        </div>


        {{-- Dynamic toggle nav: labels come from the card_label field of each shipping solution card --}}
        <div class="sr-demo-toggle-nav">
            @foreach($shippingCards as $card)
                <div class="sr-demo-nav-item {{ $loop->first ? 'sr-active' : '' }}"
                    onclick="srShowTab({{ $loop->index }}, this)">
                    {{ $card['card_label'] ?? '' }}
                </div>
            @endforeach
        </div>

        {{-- Dynamic cards: each card is rendered from the shipping_solutions DB rows grouped by sort_order --}}
        <div class="sr-demo-card-wrapper">
            @php
                // Cycle through background classes to preserve visual variety across dynamic cards
                $cardBgClasses = ['sr-bg-engage', 'sr-bg-shipping', 'sr-bg-checkout', 'sr-bg-checkout'];
            @endphp
            @foreach($shippingCards as $card)
                <div class="sr-demo-product-card {{ $loop->first ? 'sr-active' : '' }} animate__animated animate__fadeInLeft"
                    id="sr-card-{{ $loop->index }}">
                    <div class="sr-demo-card-content">
                        <h3 class="sr-demo-card-title">{{ $card['card_title'] ?? '' }}</h3>
                        <p class="sr-demo-card-description">{{ $card['card_desc'] ?? '' }}</p>
                        <ul style="margin-left:-12px; margin-top:-12px;" class="sr-demo-card-description">
                            <li><strong>{!! $card['card_point1'] ?? '' !!}</strong></li>
                            <li><strong>{!! $card['card_point2'] ?? '' !!}</strong></li>
                        </ul>
                        <a href="#" class="sr-demo-btn-live">{{ $card['card_cta'] ?? 'Start Shipping' }}</a>
                    </div>
                    <div class="sr-demo-card-visual {{ $cardBgClasses[$loop->index % count($cardBgClasses)] }}">
                        <img src="{{ asset($card['card_image'] ?? '/website_images/b2b.webp') }}"
                            class="sr-image img-fluid">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>


<!-- testimonial -->
<section class="testimonial-section">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-3">
            <div class="col-lg-8 text-center">
                <div class="google-badge">
                    <a href="#">
                        <img src="{{ asset('/website_images/google-review.png') }}" alt="Google">
                    </a>
                </div>
                <h2 class="about-title">{{ $testimonialHeading['heading'] ?? 'Trusted by Businesses. Rated by Customers' }}</h2>

                <p class="about-desc text-center">
                    {{ $testimonialHeading['description'] ?? 'For over 30 years, United Worldwide Couriers has supported businesses and individuals with secure, timely, and dependable logistics solutions. Our clients trust us for consistent service, transparent communication, careful handling, and smooth delivery experiences across domestic and international shipments.' }}
                </p>
            </div>
        </div>

        <div class="slider-wrapper">
            <div class="slider-track">

                <!-- Cards -->
                @if($testimonials->count() > 0)
                @foreach($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="stars">{{ str_repeat('★', $testimonial->rating ?? 5) }}</div>
                    <p class="testimonial-text">{!! $testimonial->content !!}</p>
                    <div class="user-info">
                        <img src="{{ asset($testimonial->customer_image) }}" class="img-fluid"
                            alt="{{ $testimonial->customer_name }}">
                        <h6>{{ $testimonial->customer_name }}</h6>
                    </div>
                </div>
                @endforeach
                @else
                <p>No testimonials found.</p>
                @endif

                <!-- Duplicate for seamless loop -->
                @if($testimonials->count() > 0)
                @foreach($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="stars">{{ str_repeat('★', $testimonial->rating ?? 5) }}</div>
                    <p class="testimonial-text">{!! $testimonial->content !!}</p>
                    <div class="user-info">
                        <img src="{{ asset($testimonial->customer_image) }}" class="img-fluid"
                            alt="{{ $testimonial->customer_name }}">
                        <h6>{{ $testimonial->customer_name }}</h6>
                    </div>
                </div>
                @endforeach
                @endif

            </div>
        </div>
    </div>
</section>



<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="faq-header">
            <span class="heading-badge">Common Questions</span>
            <h2 class="about-title">Frequently Asked Questions</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
               @include('website_include.faq-support-form')
           </div>


            <div class="col-lg-8">
                <div class="accordion" id="logisticsFaq">
                    <!-- @php
                        echo "<pre>";
                        print_r($faqs);
                        echo "</pre>";
                    @endphp -->
                    @foreach($faqs as $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq{{ $loop->index }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq{{ $loop->index }}" class="accordion-collapse collapse"
                            data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                {!! $faq->answer !!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@include('website_include.footer')