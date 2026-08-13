@include('website_include.header')

<style>


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
                        {{ $heroContent->page_badge_text ?? 'About Us' }}
                    </div>
                    <h1 class="hero-title mb-4">
                       {!! $heroContent->title !!}
                    </h1>
                    <p style="max-width: 100%;" class="text-center mb-5 lead">
                        {!! $heroContent->description !!}
                    </p>
                   @if($heroContent->image)
                   <img src="{{ asset(ltrim($heroContent->image, 'public/')) }}" class="img-fluid hero-main-img" style="max-width:1000px; margin-bottom:-100px">
                   @endif
                
                </div>
            </div>
        </div>
</header>


@include('website_include.fact-number-section')


    <!-- OVERVIEW About Section -->
    <section class="about-section" style="background-image:radial-gradient(circle at 10% 20%, rgba(255, 237, 213, 0.4) 0%, rgba(219, 234, 254, 0.4) 100%)">
        <div class="container">
            <div class="row align-items-center">
                               
                <!-- Content Side -->
                <div class="col-lg-6">
                    <div class="about-content">
                        <h2 class="about-title"><span class="gradient-text">{{ $overview->title }}</span> </h2>
                                <p class="lead">{!! $overview->description !!}</p>
                                @if($overview->page_button_text)
                                <a href="./get-started" class="sr-demo-btn-live mt-3">{{ $overview->page_button_text }}</a>
                                @endif
                    </div>
                </div>
                <div class="col-lg-6 animate-on-scroll" data-anim="animate__fadeInLeft" style="animation-delay: 0.1s;">
                    <div class="about-image-grid">
                        @if($overview->image)
                        <img src="{{ asset(ltrim($overview->image, 'public/')) }}" class="img-fluid">
                        @endif
                    </div>
                </div>
            </div>
        </div>
</section>


<!-- MISSION AND VISION SECTION -->
    <div class="app-container">

        <section class="purpose-section">
            <div class="purpose-container">
                <h2 style="font-weight:500" class="text-white section-title mb-4">{{ $missionVisionIntro->title }}</h2>
                <p style="color:#9ba5b2" class="about-desc text-center mb-5">
                    {!! $missionVisionIntro->description !!}
                </p>
                <div class="purpose-row">
                    
                    <!-- Mission Card -->
                    <div class="purpose-card reveal" style="transition-delay: 0.1s;">
                        <div class="card-glow" id="glow-1"></div>
                        <div class="content-wrap">
                            <div class="icon-box">
                                {!! $mission->icon_svg !!}
                            </div>
                            <span class="purpose-tag">{{ $mission->page_tag ?? 'Our Mission' }}</span>
                            <h2 class="purpose-title">{{ $mission->title }}</h2>
                            <p class="purpose-description">
                                {!! $mission->description !!}
                            </p>
                        </div>
                    </div>

                    <!-- Vision Card -->
                    <div class="purpose-card reveal" style="transition-delay: 0.3s;">
                        <div class="card-glow" id="glow-2"></div>
                        <div class="content-wrap">
                            <div class="icon-box">
                                {!! $vision->icon_svg !!}
                            </div>
                            <span class="purpose-tag" style="color: #a855f7">{{ $vision->page_tag ?? 'Our Vision' }}</span>
                            <h2 class="purpose-title">{{ $vision->title }}</h2>
                            <p class="purpose-description">
                                {!! $vision->description !!}
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
                    {{ $journeyIntro->title }}
                </h1>
                
                <div class="image-showcase">
                    <!-- Swirl decoration -->
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath d='M10,80 Q30,20 80,50' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-dasharray='5,5'/%3E%3Cpolyline points='75,45 80,50 75,55' fill='none' stroke='%2394a3b8' stroke-width='2'/%3E%3C/svg%3E" class="swirl-icon" alt="">

                    <div class="img-card">
                        @if($journeyIntro->image)
                        <img src="{{ asset(ltrim($journeyIntro->image, 'public/')) }}" alt="Logistic Success Story">
                        @endif
                    </div>

                    <!-- Floating Badges -->
                    <div class="floating-badge badge-1">
                        <div class="icon-circle" style="background: #f0fdf4;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #0f172a; font-size: 1.1rem;">{{ $journeyIntro->page_countries ?? '220+' }}</div>
                            <div style="font-size: 0.75rem; color: #64748b;">Countries & Territories</div>
                        </div>
                    </div>

                    <div class="floating-badge badge-2">
                        <div class="icon-circle" style="background: #f0f7ff;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #0f172a; font-size: 1.1rem;">{{ $journeyIntro->page_pin_codes ?? '19000+' }}</div>
                            <div style="font-size: 0.75rem; color: #64748b;">Serviceable PIN codes</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Timeline Scroll -->
            <div class="timeline-scroll">
                <div class="timeline-line"></div>

                @foreach($milestones as $milestone)
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-card {{ $milestone->page_card_color_class ?? 'card-blue' }}">
                        <div class="card-icon-wrap">
                            {!! $milestone->icon_svg !!}
                        </div>
                        <h4>{{ $milestone->title }}</h4>
                        <p>{{ $milestone->description }}</p>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>


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
                <h2 class="about-title">Trust We've Earned Over the Years</h2>
                
                <p class="about-desc text-center">
                    For more than 30 years, businesses have relied on United Worldwide Couriers to keep their deliveries moving safely, smoothly, and on time. Our customers keep coming back not just because we're reliable but because we're honest, responsive, and genuinely care about getting things right.
                </p>
            </div>
        </div>

        <div class="slider-wrapper">
            <div class="slider-track">
                @foreach($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="stars">{{ str_repeat('★', $testimonial->rating ?? 5) }}</div>
                    <p class="testimonial-text">{!! $testimonial->content !!}</p>
                    <div class="user-info">
                        @if($testimonial->customer_image)
                        <img src="{{ asset($testimonial->customer_image) }}" class="img-fluid" alt="{{ $testimonial->customer_name }}">
                        @endif
                        <h6>{{ $testimonial->customer_name }}</h6>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>



<!-- FAQ Section -->
 <section class="faq-section">
    <div class="container">
        <div class="faq-header">
            <span class="heading-badge">{{ $faqHeader->subtitle ?? 'Common Questions' }}</span>
            <h2 class="about-title">{{ $faqHeader->title ?? 'Frequently Asked Questions' }}</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
               @include('website_include.faq-support-form')
           </div>
            

            <div class="col-lg-8">
                <div class="accordion" id="logisticsFaq">
                    @foreach($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index + 1 }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq{{ $index + 1 }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#logisticsFaq">
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

<!-- PARTNERS SECTION -->


<!-- NEWSLETTER CTA Section -->
<!-- <section class="newsletter-cta py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="cta-title">Subscribe to Our Newsletter</h2>
                <p class="cta-desc">Get the latest updates and insights delivered to your inbox</p>
            </div>
            <div class="col-lg-6">
                <form class="newsletter-form">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section> -->

@include('website_include.footer')