@include('customer.partials.header')

<style>
  /* FACTS NUMBER CSS */
  :root {
            --brand-blue: #2563eb;
            --brand-gradient: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-border: rgba(0, 0, 0, 0.06);
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

        @media (max-width: 768px) {
            .purpose-row { flex-direction: column; gap: 30px; }
            .purpose-card { padding: 40px 25px; }
            .purpose-title { font-size: 2.2rem; }
            .purpose-section { padding: 80px 0; }
        }

        @media (max-width: 576px) {
            .purpose-title { font-size: 2rem; }
            .purpose-container { padding: 0 20px; }
            .icon-box { margin-bottom: 20px; }
            .sticky-content { 
                position: relative; 
                top: 60px; 
                margin-bottom: 40px; 
            }
            .main-title { font-size: 2.5rem; }
            .hero-title { font-size: 2.2rem; }
            .hero-title .moving-gradient-text { font-size: 2.2rem; }
            .lead { font-size: 0.95rem; }
            .section-title { font-size: 1.6rem; }
            .purpose-title { font-size: 1.6rem; }
            .purpose-description { font-size: 0.9rem; }
            .stats-container { padding: 0 20px; }
            .stat-card { padding: 20px 15px; }
            .stat-number { font-size: 1.8rem; }
            .stat-label { font-size: 0.75rem; }
            .journey-container { padding: 60px 20px; }
            .journey-grid { gap: 60px; }
            .timeline-scroll { padding-left: 40px; }
            .timeline-dot { left: -46px; top: 20px; }
            .timeline-card { padding: 15px; }
            .timeline-card h4 { font-size: 1.4rem; }
            .timeline-card p { font-size: 0.85rem; }
            .card-icon-wrap { width: 48px; height: 48px; margin-bottom: 15px; }
            .floating-badge { 
                display: none; /* Hide badges on very small screens */ 
                padding: 12px 15px; 
                border-radius: 15px; 
                gap: 8px; 
            }
            .floating-badge > div:first-child { 
                width: 32px; 
                height: 32px; 
                border-radius: 8px; 
            }
            .floating-badge > div:last-child div:first-child { 
                font-size: 0.8rem; 
            }
            .floating-badge > div:last-child div:last-child { 
                font-size: 0.6rem; 
            }
            .badge-1 { bottom: -10px; right: -15px; }
            .badge-2 { top: 100px; left: -20px; }
            .img-card { border-radius: 20px; }
            .about-section .row { flex-direction: column; }
            .about-section .col-lg-6 { width: 100%; }
            .about-image-grid { margin-top: 30px; }
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
        <div class="floating-blob bg-primary opacity-10"
            style="width: 200px; height: 200px; bottom: 10%; right: -100px;"></div>
        
        <div class="container">
            <div class="row align-items-center g-4 g-lg-5">
                
                <div class="col-lg-12 text-center animate__animated animate__fadeInLeft">
                    <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                        <span class="static-dot"></span>
                        About Us
                    </div>
                    <h1 class="hero-title mb-4">
                        Global Logistics Excellence with <span class="moving-gradient-text">United Couriers</span>
                    </h1>
                    <p style="max-width: 100%;" class="text-center mb-5 lead">
                        United Couriers is your trusted partner for comprehensive logistics solutions. We specialize in time-critical deliveries, freight forwarding, customs clearance, and supply chain management. With cutting-edge technology and a global network, we ensure your shipments reach their destination safely and on time, every time.
                    </p>
                   <img src="{{ asset('public/website_images/about-united.webp') }}" class="img-fluid hero-main-img" style="max-width:1000px; margin-bottom:-100px">
                
                </div>
            </div>
        </div>
</header>


<!-- FACTS NUMBER section -->

 <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Trusted by over <span class="gradient-text">50,000 Businesses</span> for daily logistics</h2>
            </div>
            
            <div class="stats-wrapper">
        <div class="stats-container">
            
            <!-- Card 1 -->
            <div class="stat-card">
                <div class="stat-number-wrapper">
                    <span class="stat-number" data-target="150">0</span>+
                </div>
                <p class="stat-label">Cities Covered</p>
            </div>

            <!-- Card 2 -->
            <div class="stat-card">
                <div class="stat-number-wrapper">
                    <span class="stat-number" data-target="100">0</span>K+
                </div>
                <p class="stat-label">Daily Parcels</p>
            </div>

            <!-- Card 3 -->
            <div class="stat-card">
                <div class="stat-number-wrapper">
                    <span class="stat-number" data-target="5">0</span>K+
                </div>
                <p class="stat-label">Delivery Riders</p>
            </div>

            <!-- Card 4 -->
            <div class="stat-card">
                <div class="stat-number-wrapper">
                    <span class="stat-number" data-target="99">0</span>.9%
                </div>
                <p class="stat-label">On-time Rate</p>
            </div>

            <!-- Card 5 -->
            <div class="stat-card">
                <div class="stat-number-wrapper">
                    <span class="stat-number" data-target="24">0</span>/7
                </div>
                <p class="stat-label">Live Tracking</p>
            </div>

            <!-- Card 6 -->
            <div class="stat-card">
                <div class="stat-number-wrapper">
                    <span class="stat-number" data-target="50">0</span>K+
                </div>
                <p class="stat-label">Happy Clients</p>
            </div>

        </div>
     </div>
        </div>
  </section>


    <!-- OVERVIEW About Section -->
    <section class="about-section" style="background-image:radial-gradient(circle at 10% 20%, rgba(255, 237, 213, 0.4) 0%, rgba(219, 234, 254, 0.4) 100%)">
        <div class="container">
            <div class="row align-items-center">
                               
                <!-- Content Side -->
                <div class="col-lg-6">
                    <div class="about-content">
                        <h2 class="about-title">Overview for <span class="gradient-text">United Couriers</span> </h2>
                                <p class="lead">United Worldwide Couriers has built an exceptional logistics ecosystem for modern B2B enterprises and scaling e-commerce businesses. Our services span air freight, road transport (including pan-India pickups), customs brokerage (clearance with adequate documentation), and fulfilment services, all under one roof. Every shipment is supported by a team of professionals who foresee and resolve potential challenges prior to their occurrence. Each client is entertained by dedicated support and a special personal touch.<br><br>
                                 United Worldwide Couriers has built an exceptional logistics ecosystem for modern B2B enterprises and scaling e-commerce businesses. Our services span air freight, road transport (including pan-India pickups), customs brokerage (clearance with adequate documentation), and fulfilment services, all under one roof.
                                </p>
                                <a href="#" class="sr-demo-btn-live mt-3">Book Shipments</a>
                    </div>
                </div>
                <div class="col-lg-6 animate-on-scroll" data-anim="animate__fadeInLeft" style="animation-delay: 0.1s;">
                    <div class="about-image-grid">
                        <img src="{{ asset('public/website_images/global-network.webp') }}" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
</section>


<!-- MISSION AND VISION SECTION -->
    <div class="app-container">

        <section class="purpose-section">
            <div class="purpose-container">
                <h2 style="font-weight:500" class="text-white section-title mb-4">Our Mission and Vision</h2>
                <p style="color:#9ba5b2" class="about-desc text-center mb-5">
                    That’s why United Worldwide Couriers offers flexible logistics solutions built around your shipment type, delivery timeline, budget, and business goals. Whether you need B2B Export Support, Dropshipping Solutions, Marketplace shipping, or personal deliveries for friends and family, we help you choose the right service with clarity, reliability, and complete support.
                </p>
                <div class="purpose-row">
                    
                    <!-- Mission Card -->
                    <div class="purpose-card reveal" style="transition-delay: 0.1s;">
                        <div class="card-glow" id="glow-1"></div>
                        <div class="content-wrap">
                            <div class="icon-box">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            </div>
                            <span class="purpose-tag">Our Mission</span>
                            <h2 class="purpose-title">Delivering with Care and Commitment</h2>
                            <p class="purpose-description">
                                To deliver excellence by ensuring timely, secure, and cost-effective courier services across every destination we serve. We are committed to building long-term relationships through reliability, transparency, and customer-first service, powered by innovation and a passionate team.
                            </p>
                        </div>
                    </div>

                    <!-- Vision Card -->
                    <div class="purpose-card reveal" style="transition-delay: 0.3s;">
                        <div class="card-glow" id="glow-2"></div>
                        <div class="content-wrap">
                            <div class="icon-box">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                            </div>
                            <span class="purpose-tag" style="color: #a855f7">Our Vision</span>
                            <h2 class="purpose-title">Connecting the World with Trust and Speed</h2>
                            <p class="purpose-description">
                                To become a trusted leader in logistics, connecting people and businesses through fast, reliable, and seamless delivery solutions. We envision a future where every shipment is handled with precision, every destination is within reach, and every customer experiences unmatched trust and efficiency.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

  <!-- journey timeline -->

    <div class="journey-container">
        <div class="journey-grid">
            
            <!-- Left Sticky Content -->
            <div class="sticky-content">
                <h1 class="main-title">
                    <span class="gradient-text">One-stop solution</span><br>
                    to achieve your dream
                </h1>
                
                <div class="image-showcase">
                    <!-- Swirl decoration -->
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath d='M10,80 Q30,20 80,50' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-dasharray='5,5'/%3E%3Cpolyline points='75,45 80,50 75,55' fill='none' stroke='%2394a3b8' stroke-width='2'/%3E%3C/svg%3E" class="swirl-icon" alt="">

                    <div class="img-card">
                        <img src="{{ asset('public/website_images/logistic.webp') }}" alt="Logistic Success Story">
                    </div>

                    <!-- Floating Badges -->
                    <div class="floating-badge badge-1">
                        <div class="icon-circle" style="background: #f0fdf4;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #0f172a; font-size: 1.1rem;">220+</div>
                            <div style="font-size: 0.75rem; color: #64748b;">Countries & Territories</div>
                        </div>
                    </div>

                    <div class="floating-badge badge-2">
                        <div class="icon-circle" style="background: #f0f7ff;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #0f172a; font-size: 1.1rem;">19000+</div>
                            <div style="font-size: 0.75rem; color: #64748b;">Serviceable PIN codes</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Timeline Scroll -->
            <div class="timeline-scroll">
                <div class="timeline-line"></div>

                <!-- Milestone 1 -->
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-card card-purple">
                        <div class="card-icon-wrap">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                        </div>
                        <h4>The Foundation (2017)</h4>
                        <p>Starting with a vision to revolutionize last-mile delivery, we launched our first hub with just 10 dedicated riders.</p>
                    </div>
                </div>

                <!-- Milestone 2 -->
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-card card-green">
                        <div class="card-icon-wrap">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <h4>Hyper-Growth (2019)</h4>
                        <p>Expanded to 50+ cities. Our partner network grew by 400%, becoming the preferred courier for top e-commerce players.</p>
                    </div>
                </div>

                <!-- Milestone 3 -->
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-card card-blue">
                        <div class="card-icon-wrap">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        </div>
                        <h4>Tech First (2021)</h4>
                        <p>Introduced real-time AI tracking and automated sorting, ensuring 99.9% accuracy across our entire logistics chain.</p>
                    </div>
                </div>

                <!-- Milestone 4 -->
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-card card-orange">
                        <div class="card-icon-wrap">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                        </div>
                        <h4>Global Reach (2023)</h4>
                        <p>Cross-border shipping launched, connecting local businesses to over 220 countries with effortless international logistics.</p>
                    </div>
                </div>

                <!-- Milestone 5 -->
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-card card-blue">
                        <div class="card-icon-wrap">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"></path></svg>
                        </div>
                        <h4>Sustainable Future (2025)</h4>
                        <p>Committing to 100% EV delivery for last-mile and pioneering zero-waste packaging for all corporate partners.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>





<!-- FAQ Section -->
 <section class="faq-section">
    <div class="container">
        <div class="faq-header">
            <span class="heading-badge">Common Questions</span>
            <h2 class="about-title">Frequently Asked Questions</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="faq-illustration">
                    <img src="https://i.pinimg.com/originals/f6/5d/46/f65d4681649d85bc91c86872a1775919.gif" alt="Help" style="width: 200px; margin-top: -40px;">
                    <h4 class="fw-bold mb-3">Need personalized help?</h4>
                    <p class="text-muted">Our logistics experts are available 24/7 to assist your requirements.</p>
                    
                    <div class="moving-gradient-bg contact-box">
                        <h4>Contact Us</h4>
                        <p>For urgent inquiries regarding your current shipment status.</p>
                        <button style="background-color: #fff; color: #2563eb;" class="btn btn-contact">Message Support</button>
                    </div>
                </div>
            </div>
            

            <div class="col-lg-8">
                <div class="accordion" id="logisticsFaq" style="height: 70vh; overflow-y: auto;">
                    <!-- Item 1 -->
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

                    <!-- Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                How does United Worldwide Couriers meet your shipping and logistics needs?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                To provide the best freight management solutions, we work with broad strategies, technologies, and services to simplify the planning, storage, and movement of goods.
                            </div>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                What packaging standards should we follow for shipping?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                We utilize study and secure packaging for small to large packages to protect your goods during transit. In case of fragile items, they will be cushioned enough and clearly labelled as “Fragile”. In addition, we also provide a packaging and labelling guide for all new on boarders for no confusion and faster results.
                            </div>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                               How do we calculate cost?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                The exact shipping cost will be calculated based on your goods’ weight, dimensions, destinations, where it is expected to be delivered, and the delivery speed. If you are interested in knowing the accurate pricing, you are welcome to connect with the team.
                            </div>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                Will I be notified about my shipment status?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes. To keep the clients informed, our team provides regular updates via email or SMS throughout the shipping process.
                            </div>
                        </div>
                    </div>

                    <!-- Item 6 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                Does United Worldwide Couriers handle bulk or commercial shipments?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes, we specialize in managing bulk as well as commercial consignments with easy-flowing coordination, dedicated handling, secure transit, and timely delivery. This remains the same for both small and large volumes.
                            </div>
                        </div>
                    </div>

                    <!-- Item 7 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">   
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                Can I schedule a pickup for my shipment?
                            </button>
                        </h2>
                        <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes, you can easily schedule a pick up at your preferred time either by reaching out to our team online (via our website) or by directly contacting our customer support.
                            </div>
                        </div>
                    </div>

                    <!-- Item 8 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                                How can I track my shipment?
                            </button>
                        </h2>
                        <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Through our online tracking system, you can track your shipment in real-time. For that, you just have to enter the tracking ID provided on our website dashboard to get live updates.
                            </div>
                        </div>
                    </div>

                    <!-- Item 9 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9">
                                Will my package be picked up by the United Worldwide Couriers team only?
                            </button>
                        </h2>
                        <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                It depends on the service and location. So, your shipment may be picked up either by our in-house delivery team or by third-party courier partners. On the contrary, we ensure strict quality control and safe handling for whatever the case may be.
                            </div>
                        </div>
                    </div>

                    <!-- Item 10 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10">
                                Do you provide customs clearance support?
                            </button>
                        </h2>
                        <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes, while others face limited support, we complete international customs documentation and clearance faster. This way, we support rapid shipping without delays.
                            </div>
                        </div>
                    </div>

                    <!-- Item 11 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq11">
                                What happens if my shipment is delayed or stuck?
                            </button>
                        </h2>
                        <div id="faq11" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                First things first, the team proactively monitors the shipment to identify and resolve potential issues before they escalate. Still, if an issue occurs, the internal team coordinates with the partners to resolve the situation quickly.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>



<!-- FACTS counter script -->
    <script>
        const animateCounter = (el) => {
            const target = parseInt(el.getAttribute('data-target'));
            const duration = 1500;
            const stepTime = 20;
            const steps = duration / stepTime;
            const increment = target / steps;
            let current = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    el.innerText = target;
                    clearInterval(timer);
                } else {
                    el.innerText = Math.floor(current);
                }
            }, stepTime);
        };

        const observer2 = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target.querySelector('.stat-number');
                    if (counter && !counter.classList.contains('counted')) {
                        animateCounter(counter);
                        counter.classList.add('counted');
                    }
                }
            });
        }, { threshold: 0.2 });

        document.querySelectorAll('.stat-card').forEach(card => observer2.observe(card));
    </script>


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


@include('customer.partials.footer')
