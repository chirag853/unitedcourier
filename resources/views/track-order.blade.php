@include('website_include.header')

<style>
  .track-cta.light {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 40px 50px;
        border-radius: 20px;

        background: transparent linear-gradient(255deg, #ffc46554, #5338ff26);
        color: #111;

        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    /* LEFT */
    .track-left {
        max-width: 600px;
    }

    .live-badge {
        display: inline-block;
        background: rgba(34,197,94,0.1);
        color: #16a34a;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .track-left h2 {
        font-size: 40px;
        font-weight: 700;
        margin: 0 0 15px;
        color: #0f172a;
    }

    .track-left p {
        color: #475569;
        line-height: 1.6;
        font-size: 15px;
    }

    /* BUTTON */
    .track-btn {
        background: #111827;
        color: #fff;
        border: none;
        padding: 14px 24px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    .track-btn:hover {
        background: #1f2937;
        transform: translateY(-2px);
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .track-cta.light {
            flex-direction: column;
            text-align: center;
            padding: 40px 20px;
        }

        .track-left h2 {
            font-size: 26px;
        }

        .track-right {
            margin-top: 20px;
        }
    }

</style>

<!-- Hero section -->
@php
    $heroBadge = $heroContent->content['badge_text'] ?? 'Free Tool · Instant Results';
    $heroTitle = $heroContent->content['title'] ?? 'Track Your <span class="moving-gradient-text">Orders Easily</span>';
    $heroSubtitle = $heroContent->content['subtitle'] ?? 'Just enter your Mobile Number, AWB tracking number or Order ID & it\'s done.';
    $heroImage = $heroContent->image ?? 'images/tracking.webp';
    $heroBtnText = $heroContent->content['button_text'] ?? 'Track Now';
@endphp
<header style="min-height: 70vh; padding-top: 140px; padding-bottom: 50px;" class="hero-gradient">
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
                    {{ $heroBadge }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $heroTitle !!}
                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ $heroSubtitle }}                                               
                </p>

                <a href="#track-now" class="book-btn-service"><i class="fa-solid fa-route fs-5"></i> &nbsp; {{ $heroBtnText }}</a>

            </div>

            <!-- Right Image -->
            <div class="col-md-6 text-center">
                <div class="hero-graphic">
                    <img src="{{ asset($heroImage) }}" class="img-fluid" style="width:80%;">
                </div>    

            </div>
    </div>
</header>


<!-- Track form section -->
@php
    $formTitle = $trackFormContent->content['title'] ?? 'Track <span class="gradient-text">Now</span>';
    $formBtnText = $trackFormContent->content['button_text'] ?? 'Track Now';
    $field1Label = $trackFormContent->content['field_1_label'] ?? 'AWB Number';
    $field1Placeholder = $trackFormContent->content['field_1_placeholder'] ?? 'Airway Bill Number';
    $field2Label = $trackFormContent->content['field_2_label'] ?? 'Order Id';
    $field2Placeholder = $trackFormContent->content['field_2_placeholder'] ?? 'eg: 983434599';
    $field3Label = $trackFormContent->content['field_3_label'] ?? 'Phone Number';
    $field3Placeholder = $trackFormContent->content['field_3_placeholder'] ?? '+91 9876543210';
@endphp
<section id="track-now" class="py-5 bg-white">
    <div class="container" style="max-width: 825px;">
      <div class="row form-shadow" style="max-width: 800px;">
        <div class="col-md-12">
          <div class="mx-auto">
             <div class="mb-4">
                <h3 class="h4-title">{!! $formTitle !!}</h3>
             </div>
            
              <form>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ $field1Label }}</label>
                        <div class="input-group-custom">
                            <input type="number" class="form-control input-custom" placeholder="{{ $field1Placeholder }}">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <label class="form-label">{{ $field2Label }}</label>
                        <div class="input-group-custom">
                            <input type="number" class="form-control input-custom" placeholder="{{ $field2Placeholder }}">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">{{ $field3Label }}</label>
                        <div class="input-group-custom">
                            <input type="number" class="form-control input-custom" placeholder="{{ $field3Placeholder }}">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                    </div> -->
                    
                </div>

        

                <button type="button" style="width: 240px;" class="btn moving-gradient-bg btn-primary-custom m-2">
                    {{ $formBtnText }}
                </button>
              </form>

              </div>
        </div>

      </div>
    </div>
  </section>


<!-- Features cards section -->
@php
    $featuresHeaderTitle = $featuresHeader->content['title'] ?? 'What\'s your order status?';
    $featuresHeaderDesc = $featuresHeader->content['description'] ?? 'Carriers use dimensional weight to price large, light packages — here\'s what you need to know.';
@endphp
<section class="features-section" style="padding:40px 0;">
  <div class="container">

  <div class="row justify-content-center mb-3">
            <div class="col-lg-10 text-center">
                <h2 class="about-title">{{ $featuresHeaderTitle }}</h2>
                <p class="about-desc text-center">
                    {{ $featuresHeaderDesc }}
                </p>
            </div>
        </div>

    <div class="row g-4">
        @forelse($featuresContent as $feature)
        <div class="col-md-3">
            <div class="feature-card">
                <div class="feature-icon fi-{{ $feature->content['icon_color'] ?? 'blue' }}">
                    <i class="{{ $feature->content['icon'] ?? 'fa-solid fa-check-to-slot' }}" style="color:{{ $feature->content['icon_color_code'] ?? '' }}"></i>
                </div>
                <h5>{{ $feature->title ?: ($feature->content['title'] ?? 'Feature') }}</h5>
                <p>{{ $feature->description ?: ($feature->content['description'] ?? '') }}</p>
            </div>
        </div>
        @empty
        <div class="col-12 text-center">
            <p class="text-muted">No feature cards available.</p>
        </div>
        @endforelse
    </div>
  </div>
</section>


<!-- About / Optimize section -->
@php
    $aboutTitle = $aboutContent->content['title'] ?? 'Optimize your order <span class="gradient-text">tracking experience</span>';
    $aboutDescription = $aboutContent->description ?: ($aboutContent->content['description'] ?? 'Always stay informed about your shipments, regardless of your courier partner');
    $aboutBtnText = $aboutContent->content['button_text'] ?? 'Need help?';
    $aboutBtnLink = $aboutContent->link ?: ($aboutContent->content['button_link'] ?? 'contact-us.php');
    $aboutVideoUrl = $aboutContent->content['video_url'] ?? 'https://www.youtube.com/embed/tOvpjmnh5h4?si=-O5MSnO7OXm2Wspk';
    $checklistItems = $aboutContent->content['checklist'] ?? [];
@endphp
<section class="pb-1 about-section">
        <div class="container">
            <div class="row align-items-center">
                                
                <!-- Content Side -->
                <div class="col-lg-6">
                    <div class="about-content">
                        <h2 class="about-title">{!! $aboutTitle !!}</h2>
                        <p class="lead">{{ $aboutDescription }}<br><br>
                        </p>
                        @if(is_array($checklistItems) && count($checklistItems) > 0)
                        <ul class="check-list lead">
                            @foreach($checklistItems as $item)
                            <li><i class="fas fa-check-circle"></i> {{ $item }}</li>
                            @endforeach
                        </ul>
                        @endif
                        <a href="{{ $aboutBtnLink }}" class="sr-demo-btn-live mt-3">{{ $aboutBtnText }}</a>
                    </div>
                </div>
                <div class="col-lg-6 animate-on-scroll show animate__animated animate__fadeInLeft" data-anim="animate__fadeInLeft" style="animation-delay: 0.1s;">
                    <iframe style="border-radius:20px" width="95%" height="300" src="{{ $aboutVideoUrl }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
            </div>
        </div>
</section>


<!-- CTA section -->
@php
    $ctaBadge = $ctaContent->content['badge_text'] ?? 'Get in Touch';
    $ctaTitle = $ctaContent->content['title'] ?? 'Need any help related order??';
    $ctaDescription = $ctaContent->description ?: ($ctaContent->content['description'] ?? 'Get live updates across all major carriers — end-to-end visibility from pickup to delivery for every order you export worldwide.');
    $ctaBtnText = $ctaContent->content['button_text'] ?? 'Contact Us Now →';
    $ctaBtnLink = $ctaContent->link ?: ($ctaContent->content['button_link'] ?? '#');
@endphp
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            
            <section class="track-cta light">
                <div class="track-left">
                    <span class="live-badge">{{ $ctaBadge }}</span>

                    <h2>{{ $ctaTitle }}</h2>

                    <p>
                        {{ $ctaDescription }}
                    </p>
                </div>

                <div class="track-right">
                    <a href="{{ $ctaBtnLink }}" class="track-btn">
                        {{ $ctaBtnText }}
                    </a>
                </div>
            </section>

        </div>
    </div>
</div>


<!-- FAQ Section -->
@php
    $faqHeaderBadge = $faqHeader->content['badge_text'] ?? 'Common Questions';
    $faqHeaderTitle = $faqHeader->content['title'] ?? 'Frequently Asked Questions';
    $faqSidebarTitle = $faqHeader->content['sidebar_title'] ?? 'Need personalized help?';
    $faqSidebarDesc = $faqHeader->content['sidebar_description'] ?? 'Our logistics experts are available 24/7 to assist your requirements.';
    $faqContactTitle = $faqHeader->content['contact_title'] ?? 'Contact Us';
    $faqContactDesc = $faqHeader->content['contact_description'] ?? 'For urgent inquiries regarding your current shipment status.';
    $faqContactBtn = $faqHeader->content['contact_button'] ?? 'Message Support';
    $faqImage = $faqHeader->image ?? 'https://i.pinimg.com/originals/f6/5d/46/f65d4681649d85bc91c86872a1775919.gif';
@endphp
<section class="faq-section">
    <div class="container">
        <div class="faq-header">
            <span class="heading-badge">{{ $faqHeaderBadge }}</span>
            <h2 class="about-title">{{ $faqHeaderTitle }}</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="faq-illustration">
                    <img src="{{ $faqImage }}" alt="Help" style="width: 200px; margin-top: -40px;">
                    <h4 class="fw-bold mb-3">{{ $faqSidebarTitle }}</h4>
                    <p class="text-muted">{{ $faqSidebarDesc }}</p>
                    
                    <div class="moving-gradient-bg contact-box">
                        <h4>{{ $faqContactTitle }}</h4>
                        <p>{{ $faqContactDesc }}</p>
                        <button style="background-color: #fff; color: #2563eb;" class="btn btn-contact">{{ $faqContactBtn }}</button>
                    </div>
                </div>
            </div>
            

            <div class="col-lg-8">
                <div class="accordion" id="logisticsFaq" style="height: 70vh; overflow-y: auto;">
                    @forelse($faqs as $index => $faq)
                    @php
                        $faqId = 'faq' . ($index + 1);
                        $isFirst = $index === 0;
                    @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ !$isFirst ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $faqId }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="{{ $faqId }}" class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                {!! $faq->answer !!}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqPlaceholder">
                                No FAQs available
                            </button>
                        </h2>
                        <div id="faqPlaceholder" class="accordion-collapse collapse show" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                FAQ content will be added soon.
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