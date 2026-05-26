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
    --primary: #1a73e8;
    --primary-dark: #0d5abf;
    --accent: #ff6b00;
    --accent-light: #ff8c38;
    --dark: #0d1b2a;
    --dark-2: #1c2e44;
    --text: #3d4f60;
    --text-light: #6b7c93;
    --light-bg: #f4f8ff;
    --white: #ffffff;
    --border: #e2ecf8;
    --success: #28a745;
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
    .stat-number-wrapper {
        font-size: 1.8rem;
    }

    .stat-label {
        font-size: 0.75rem;
    }
}

@media (max-width: 992px) {
    .stats-container {
        grid-template-columns: repeat(3, 1fr);
        /* 2 rows on tablet */
    }
}

@media (max-width: 576px) {
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
        /* 3 rows on mobile */
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
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
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
    box-shadow: 0 20px 50px -20px rgba(0, 0, 0, 0.5);
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
    width: 400px;
    height: 400px;
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
    width: 65px;
    height: 65px;
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
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
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
    .purpose-row {
        flex-direction: column;
        gap: 40px;
    }

    .purpose-card {
        padding: 50px 30px;
    }

    .purpose-title {
        font-size: 2.4rem;
    }

    .purpose-section {
        padding: 100px 0;
    }
}

@media (max-width: 576px) {
    .purpose-title {
        font-size: 2rem;
    }

    .purpose-container {
        padding: 0 24px;
    }

    .icon-box {
        margin-bottom: 30px;
    }
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
    box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.12);
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
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 15px;
    border: 1px solid #f1f5f9;
    z-index: 10;
}

.badge-1 {
    bottom: -15px;
    right: -25px;
}

.badge-2 {
    top: 140px;
    left: -35px;
}

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
.card-blue {
    background-color: #f0f7ff;
}

.card-green {
    background-color: #f0fdf4;
}

.card-purple {
    background-color: #faf5ff;
}

.card-orange {
    background-color: #fffaf0;
}

.timeline-item:hover .timeline-card {
    transform: translateX(12px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.03);
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
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.04);
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
    .journey-grid {
        gap: 40px;
    }

    .main-title {
        font-size: 3rem;
    }
}

@media (max-width: 992px) {
    .journey-grid {
        grid-template-columns: 1fr;
    }

    .sticky-content {
        position: relative;
        top: 0;
        margin-bottom: 60px;
    }
}
</style>


<!-- Hero section -->
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
                    {{ $heroContent->content['badge_text'] ?? 'E-commerce Logistics Solutions' }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $heroContent->content['title'] ?? 'Built for Sellers. <span class="moving-gradient-text">Designed for scale</span>' !!}
                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ $heroContent->content['description'] ?? 'Connect your marketplace account directly with our platform and ship orders with ease — starting from lightweight parcels of just 50g to high-volume e-commerce shipments.' }}
                </p>

                <a href="{{ $heroContent->content['button_primary_url'] ?? '#' }}" class="book-btn-service"><i class="fas {{ $heroContent->content['button_primary_icon'] ?? 'fa-paper-plane' }}"></i> {{ $heroContent->content['button_primary_text'] ?? 'Book a Shipping' }}</a> &nbsp; <a
                    href="{{ $heroContent->content['button_secondary_url'] ?? '#' }}" class="quote-btn-service"><i class="fas {{ $heroContent->content['button_secondary_icon'] ?? 'fa-calculator' }}"></i> {{ $heroContent->content['button_secondary_text'] ?? 'Get a Quote' }}</a>

                <div class="hero-badges">
                    @if(isset($heroContent->content['badges']) && is_array($heroContent->content['badges']))
                        @foreach($heroContent->content['badges'] as $badge)
                            <div class="hero-badge"><i class="fas {{ $badge['icon'] }}"></i> {{ $badge['text'] }}</div>
                        @endforeach
                    @else
                        <div class="hero-badge"><i class="fas fa-clock"></i> 24–72 Hr Delivery</div>
                        <div class="hero-badge"><i class="fas fa-shield-alt"></i> Fully Insured</div>
                        <div class="hero-badge"><i class="fas fa-map-marker-alt"></i> 220+ Countries</div>
                    @endif
                </div>

            </div>

            <!-- Right Image -->
            <div class="col-md-6 text-center">
                <div class="hero-graphic">
                    <div style="width: 400px; height: 400px;" class="plane-circle">
                        <img src="{{ asset('assets/' . ($heroContent->content['image'] ?? 'images/ecomm-service.webp')) }}" class="img-fluid">
                        <!-- Stat pills -->
                        @if(isset($heroContent->content['stat_pills']) && is_array($heroContent->content['stat_pills']))
                            @foreach($heroContent->content['stat_pills'] as $index => $pill)
                                <div class="stat-pill pill-{{ $index + 1 }}">
                                    <div class="sp-icon" style="background:{{ $pill['color'] }};color:{{ $pill['text_color'] }}"><i class="fas {{ $pill['icon'] }}"></i></div>
                                    <div>
                                        <div class="sp-val">{{ $pill['value'] }}</div>
                                        <div class="sp-lbl">{{ $pill['label'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="stat-pill pill-1">
                                <div class="sp-icon" style="background:rgba(26,115,232,.1);color:var(--primary)"><i class="fas fa-box"></i></div>
                                <div>
                                    <div class="sp-val">50K+</div>
                                    <div class="sp-lbl">Shipments/Month</div>
                                </div>
                            </div>
                            <div class="stat-pill pill-2">
                                <div class="sp-icon" style="background:rgba(255,107,0,.1);color:var(--accent)"><i class="fas fa-star"></i></div>
                                <div>
                                    <div class="sp-val">4.9★</div>
                                    <div class="sp-lbl">Avg Rating</div>
                                </div>
                            </div>
                            <div class="stat-pill pill-3">
                                <div class="sp-icon" style="background:rgba(40,167,69,.1);color:var(--success)"><i class="fas fa-check-circle"></i></div>
                                <div>
                                    <div class="sp-val">99.2%</div>
                                    <div class="sp-lbl">On-Time Rate</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>



<!-- FACTS NUMBER section -->
@if($statsContent && $statsContent->count() > 0)
<section class="py-5 bg-white">
    <div class="container">
        @php $statsHeader = $statsContent->firstWhere('item_key', 'stats_header'); @endphp
        @if($statsHeader)
        <div class="text-center mb-5">
            <h2 class="section-title">{!! $statsHeader->content['title'] ?? '' !!}</h2>
        </div>
        @endif

        <div class="stats-wrapper">
            <div class="stats-container">
                @foreach($statsContent as $stat)
                    @if($stat->item_key !== 'stats_header')
                    <div class="stat-card">
                        <div class="stat-number-wrapper">
                            <span class="stat-number" data-target="{{ $stat->content['value'] ?? '0' }}">0</span>{{ $stat->content['suffix'] ?? '' }}
                        </div>
                        <p class="stat-label">{{ $stat->content['label'] ?? '' }}</p>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif


<!-- OVERVIEW About Section -->
@if($overviewContent)
<section class="about-section">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-6 animate-on-scroll" data-anim="animate__fadeInLeft" style="animation-delay: 0.1s;">
                <div class="about-image-grid">
                    <div class="world-map-placeholder">
                        <img src="{{ asset('assets/' . ($overviewContent->content['image'] ?? 'images/map-pattern.png')) }}" class="img-fluid">
                    </div>
                </div>
            </div>

            <!-- Content Side -->
            <div class="col-lg-6">
                <div class="about-content">
                    <h2 class="about-title">{!! $overviewContent->content['title'] ?? 'Dedicated Supply Chain for <span class="moving-gradient-text">E-commerce</span>' !!}</h2>
                    <p class="lead">{{ $overviewContent->content['description'] ?? '' }}
                    </p>

                    @if(isset($overviewContent->content['check_list']) && is_array($overviewContent->content['check_list']))
                    <ul class="check-list lead">
                        @foreach($overviewContent->content['check_list'] as $item)
                        <li><i class="fas fa-check-circle"></i> {{ $item }}</li>
                        @endforeach
                    </ul>
                    @endif

                    <a href="{{ $overviewContent->content['button_url'] ?? '#' }}" class="sr-demo-btn-live mt-3">{{ $overviewContent->content['button_text'] ?? 'Book Shipments' }}</a>
                </div>
            </div>

        </div>
    </div>
</section>
@endif


<!-- Features cards section -->
@if($featuresHeaderContent || ($featuresContent && $featuresContent->count() > 0))
<section class="features-section" style="padding:40px 0;">
    <div class="container">

        @if($featuresHeaderContent)
        <div class="row justify-content-center mb-3">
            <div class="col-lg-10 text-center">
                <h2 class="about-title">{{ $featuresHeaderContent->content['title'] ?? '' }}</h2>
                <p class="about-desc text-center">
                    {{ $featuresHeaderContent->content['description'] ?? '' }}
                </p>
            </div>
        </div>
        @endif

        @if($featuresContent && $featuresContent->count() > 0)
        <div class="row g-4">
            @foreach($featuresContent as $feature)
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon {{ $feature->content['color_class'] ?? 'fi-blue' }}"><i class="fas {{ $feature->content['icon'] ?? 'fa-satellite' }}"></i></div>
                    <h5>{{ $feature->content['title'] ?? '' }}</h5>
                    <p>{{ $feature->content['description'] ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif


<!-- testimonial -->
@if($testimonialsHeader || ($testimonials && $testimonials->count() > 0))
<section class="testimonial-section">
    <div class="container">
        <!-- Header -->
        @if($testimonialsHeader)
        <div class="row justify-content-center mb-3">
            <div class="col-lg-8 text-center">
                @if(isset($testimonialsHeader->content['google_review_image']))
                <div class="google-badge">
                    <a href="#">
                        <img src="{{ asset('assets/' . $testimonialsHeader->content['google_review_image']) }}" alt="Google">
                    </a>
                </div>
                @endif
                <h2 class="about-title">{{ $testimonialsHeader->content['title'] ?? '' }}</h2>
                <p class="about-desc text-center">
                    {{ $testimonialsHeader->content['description'] ?? '' }}
                </p>
            </div>
        </div>
        @endif

        @if($testimonials && $testimonials->count() > 0)
        <div class="slider-wrapper">
            <div class="slider-track">
                @foreach($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="stars">
                        @for($i = 0; $i < ($testimonial->content['rating'] ?? 5); $i++)★@endfor
                    </div>
                    <p class="testimonial-text">"{{ $testimonial->content['text'] ?? '' }}"</p>
                    <div class="user-info">
                        <img src="{{ asset('assets/' . ($testimonial->content['avatar'] ?? '')) }}" class="img-fluid">
                        <h6>{{ $testimonial->content['name'] ?? '' }}</h6>
                    </div>
                </div>
                @endforeach

                <!-- Duplicate for seamless loop -->
                @foreach($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="stars">
                        @for($i = 0; $i < ($testimonial->content['rating'] ?? 5); $i++)★@endfor
                    </div>
                    <p class="testimonial-text">"{{ $testimonial->content['text'] ?? '' }}"</p>
                    <div class="user-info">
                        <img src="{{ $testimonial->content['avatar'] ?? '' }}" class="img-fluid">
                        <h6>{{ $testimonial->content['name'] ?? '' }}</h6>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endif


<!-- FAQ Section -->
@if($faqHeader || ($faqs && $faqs->count() > 0))
<section class="faq-section">
    <div class="container">
        @if($faqHeader)
        <div class="faq-header">
            <span class="heading-badge">{{ $faqHeader->content['badge'] ?? 'Common Questions' }}</span>
            <h2 class="about-title">{{ $faqHeader->content['title'] ?? 'Frequently Asked Questions' }}</h2>
        </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="faq-illustration">
                    @if(isset($faqHeader->content['sidebar_image']))
                    <img src="{{ $faqHeader->content['sidebar_image'] }}" alt="Help"
                        style="width: 200px; margin-top: -40px;">
                    @endif
                    <h4 class="fw-bold mb-3">{{ $faqHeader->content['sidebar_title'] ?? 'Need personalized help?' }}</h4>
                    <p class="text-muted">{{ $faqHeader->content['sidebar_description'] ?? 'Our logistics experts are available 24/7 to assist your requirements.' }}</p>

                    <div class="moving-gradient-bg contact-box">
                        <h4>{{ $faqHeader->content['contact_box_title'] ?? 'Contact Us' }}</h4>
                        <p>{{ $faqHeader->content['contact_box_description'] ?? 'For urgent inquiries regarding your current shipment status.' }}</p>
                        <button style="background-color: #fff; color: #2563eb;" class="btn btn-contact">{{ $faqHeader->content['contact_button_text'] ?? 'Message Support' }}</button>
                    </div>
                </div>
            </div>

            @if($faqs && $faqs->count() > 0)
            <div class="col-lg-8">
                <div class="accordion" id="logisticsFaq" style="height: 70vh; overflow-y: auto;">
                    @foreach($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq{{ $index + 1 }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq{{ $index + 1 }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                {{ $faq->answer }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endif


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

<!-- Animated counter for stats -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.stat-number');
    const speed = 200;

    const animateCounter = (counter) => {
        const target = parseInt(counter.getAttribute('data-target'));
        let count = 0;
        const increment = Math.ceil(target / speed);

        const updateCount = () => {
            count += increment;
            if (count < target) {
                counter.innerText = count;
                requestAnimationFrame(updateCount);
            } else {
                counter.innerText = target;
            }
        };

        updateCount();
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
});
</script>

@include('website_include.footer')