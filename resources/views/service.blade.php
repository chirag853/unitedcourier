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
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
    </div>
    <div class="floating-blob bg-primary opacity-10"
        style="width: 200px; height: 200px; bottom: 10%; right: -100px;"></div>

    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <!-- Left Content -->
            <div class="col-md-6 text-md-start text-center animate__animated animate__fadeInLeft">
                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    What we offer
                </div>
                <h1 class="hero-title mb-4">
                    Powerful solutions for <span class="moving-gradient-text">Global Shipping.</span>
                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    From local deliveries to complex international supply chains, we provide the technology and infrastructure to move your business forward.
                </p>
            </div>

            <!-- Right Image -->
            <div class="col-md-6 text-center">
                <img src="{{ asset('website_images/service-img.webp') }}" class="img-fluid hero-main-img"
                    style="max-width:98%;">
            </div>

        </div>
    </div>
</header>



<style>
      /* --- Creative Service Layout --- */
        .service-layout {
            gap: 25px;
            padding: 40px 0;
        }

        .creative-card {
            border-radius: 40px;
            padding: 45px 35px;
            position: relative;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            border: 1px solid rgba(241, 245, 249, 0.8);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 380px;
            overflow: hidden;
        }

        .creative-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.1);
        }

        /* Unique Color Profiles per Service */
        .sc-air { background: #f0f7ff; }
        .sc-ecom { background: #faf5ff; }
        .sc-ware { background: #f0fdf4; }
        .sc-drop { background: #fffaf0; }
        .sc-tech { background: #fef2f2; }
        .sc-secure { background: #f1f5f9; }

        .sc-air .icon-wrap { background: #2563eb; }
        .sc-ecom .icon-wrap { background: #8b5cf6; }
        .sc-ware .icon-wrap { background: #16a34a; }
        .sc-drop .icon-wrap { background: #f59e0b; }
        .sc-tech .icon-wrap { background: #ef4444; }
        .sc-secure .icon-wrap { background: #475569; }

        .icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            box-shadow: 0 15px 25px -5px rgba(0,0,0,0.1);
        }

        .creative-card h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .creative-card p {
            font-size: 1rem;
            color: #475569;
            line-height: 1.6;
        }

        /* Abstract Decoration */
        .card-deco {
            position: absolute;
            bottom: -30px;
            right: -30px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: #ffffff;
            opacity: 0.4;
            filter: blur(40px);
            z-index: 0;
        }

        .card-content {
            position: relative;
            z-index: 1;
        }

        /* --- Stats/Trust Section --- */
        .trust-section {
            background: #0f172a;
            color: #ffffff;
            border-radius: 60px;
            margin: 0 20px;
            padding: 80px 40px;
        }

        .trust-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            text-align: center;
        }

        .trust-item h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .trust-item p {
            color: #94a3b8;
            font-weight: 500;
        }
</style>    

   <!-- Creative Service Grid -->
    <section class="pb-5 mt-4">
        <div class="container">
            <div class="service-layout">

            <div class="animate-on-scroll show animate__animated animate__fadeInRight" data-anim="animate__fadeInRight" style="animation-delay: 0.2s;">
                <h2 class="about-title text-center">Our Popular Services and Solutions</h2>
                <p class="about-desc text-center">
                    From local deliveries to complex international supply chains, we provide the technology and infrastructure to move your business forward. Whether you need B2B Export Support, Dropshipping Solutions, Marketplace shipping, or personal deliveries for friends and family, we help you choose the right service with clarity, reliability, and complete support.
                </p>
            </div>
            
            <div class="row">
                @foreach($services as $service)
                    @php
                        $content = is_string($service->content) ? json_decode($service->content, true) : $service->content;
                    @endphp
                    <div class="col-md-4 p-3"> 
                    <div class="creative-card {{ $content['color_class'] }}">
                        <div class="card-deco"></div>
                        <div class="card-content">
                            <div class="icon-wrap">
                                {!! $content['icon_svg'] !!}
                            </div>
                            <h2>{{ $content['title'] }}</h2>
                            <p>{{ $content['description'] }}</p>
                        </div>
                        <div class="card-content">
                            <a href="{{ $content['link'] }}" class="text-decoration-none fw-bold" style="color: {{ $content['color_class'] == 'sc-air' ? '#2563eb' : ($content['color_class'] == 'sc-ecom' ? '#8b5cf6' : ($content['color_class'] == 'sc-ware' ? '#16a34a' : ($content['color_class'] == 'sc-drop' ? '#f59e0b' : ($content['color_class'] == 'sc-tech' ? '#ef4444' : '#475569')))) }};">{{ $content['btn_text'] ?? 'View Routes →' }}</a>
                        </div>
                    </div>
                    </div>
                @endforeach


            </div>   

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
                     <img src="{{ asset('website_images/google-review.png') }}" alt="Google">
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
                @foreach($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="stars">{{ str_repeat('★', $testimonial->rating ?? 5) }}</div>
                    <p class="testimonial-text">{{ $testimonial->content }}</p>
                    <div class="user-info"> <img src="{{ asset($testimonial->customer_image ?? 'website_images/default-avatar.png') }}" class="img-fluid"> <h6>{{ $testimonial->customer_name ?? 'Name' }}</h6></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Partner Logos Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="about-title">Our Trusted Partners</h2>
            <p class="about-desc">We work with leading logistics companies worldwide to ensure your shipments reach every corner of the globe.</p>
        </div>
        <div class="row justify-content-center align-items-center">
            @foreach($partners as $partner)
            @php
                $partnerContent = is_string($partner->content) ? json_decode($partner->content, true) : $partner->content;
            @endphp
            <div class="col-md-2 col-4 mb-4 text-center">
                <img src="{{ $partnerContent['logo_url'] ?? '#' }}" alt="{{ $partnerContent['alt'] ?? 'Partner Logo' }}" class="img-fluid partner-logo" style="max-height: 60px; opacity: 0.8;">
            </div>
            @endforeach
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
                    @foreach($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#logisticsFaq">
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