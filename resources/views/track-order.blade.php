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

            /* Tracking Results Section */
            .tracking-results-section {
                padding: 40px 0;
                background: #f8fafc;
            }

            .tracking-results-card {
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 4px 24px rgba(0,0,0,0.08);
                padding: 30px;
                margin-bottom: 20px;
            }

            .tracking-status-badge {
                display: inline-block;
                padding: 8px 18px;
                border-radius: 999px;
                font-size: 14px;
                font-weight: 600;
                margin-bottom: 15px;
            }

            .tracking-status-badge.status-draft { background: rgba(107,114,128,0.1); color: #6b7280; }
            .tracking-status-badge.status-ready { background: rgba(59,130,246,0.1); color: #3b82f6; }
            .tracking-status-badge.status-packed { background: rgba(249,115,22,0.1); color: #f97316; }
            .tracking-status-badge.status-manifested { background: rgba(139,92,246,0.1); color: #8b5cf6; }
            .tracking-status-badge.status-dispatched { background: rgba(6,182,212,0.1); color: #06b6d4; }
            .tracking-status-badge.status-delivered { background: rgba(34,197,94,0.1); color: #16a34a; }
            .tracking-status-badge.status-cancelled { background: rgba(239,68,68,0.1); color: #ef4444; }
            .tracking-status-badge.status-disputed { background: rgba(245,158,11,0.1); color: #f59e0b; }
            .tracking-status-badge.status-on_hold { background: rgba(234,179,8,0.1); color: #eab308; }
            .tracking-status-badge.status-received { background: rgba(20,184,166,0.1); color: #14b8a6; }

            .tracking-timeline {
                position: relative;
                padding-left: 30px;
            }

            .tracking-timeline::before {
                content: '';
                position: absolute;
                left: 14px;
                top: 0;
                bottom: 0;
                width: 2px;
                background: linear-gradient(to bottom, #8b5cf6, #06b6d4, #16a34a);
            }

            .timeline-entry {
                position: relative;
                padding-bottom: 25px;
                padding-left: 20px;
            }

            .timeline-entry:last-child {
                padding-bottom: 0;
            }

            .timeline-dot {
                position: absolute;
                left: -22px;
                top: 4px;
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background: #8b5cf6;
                border: 2px solid #fff;
                box-shadow: 0 0 0 3px rgba(139,92,246,0.2);
            }

            .timeline-entry:last-child .timeline-dot {
                background: #16a34a;
                box-shadow: 0 0 0 3px rgba(34,197,94,0.2);
            }

            .timeline-entry.active-entry .timeline-dot {
                background: #06b6d4;
                box-shadow: 0 0 0 3px rgba(6,182,212,0.2);
                width: 14px;
                height: 14px;
                left: -23px;
                top: 3px;
            }

            .timeline-title {
                font-size: 16px;
                font-weight: 600;
                color: #0f172a;
                margin-bottom: 4px;
            }

            .timeline-time {
                font-size: 13px;
                color: #64748b;
            }

            .detail-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }

            .detail-item {
                padding: 12px;
                background: #f1f5f9;
                border-radius: 10px;
            }

            .detail-item .detail-label {
                font-size: 12px;
                color: #64748b;
                font-weight: 500;
                margin-bottom: 4px;
            }

            .detail-item .detail-value {
                font-size: 14px;
                color: #0f172a;
                font-weight: 600;
            }

            .tracking-error-msg {
                text-align: center;
                padding: 30px;
                color: #ef4444;
                font-size: 16px;
                font-weight: 500;
            }

            .tracking-loading {
                text-align: center;
                padding: 30px;
                color: #64748b;
            }

            .tracking-loading .spinner {
                display: inline-block;
                width: 30px;
                height: 30px;
                border: 3px solid rgba(139,92,246,0.2);
                border-top-color: #8b5cf6;
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
            }

            @keyframes spin {
                to { transform: rotate(360deg); }
            }

            @media (max-width: 768px) {
                .detail-grid {
                    grid-template-columns: 1fr 1fr;
                }
                .tracking-results-card {
                    padding: 20px;
                }
            }

        </style>




<!-- Hero section -->
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
                    {{ $heroContent->badge_text ?? 'Free Tool · Instant Results' }}
                </div>
                <h1 class="hero-title mb-4">
                    <!-- {!! $heroContent->title ?? 'Track Your <span class="moving-gradient-text">Orders Easily</span>' !!} -->
                    {!! $heroContent->title !!}
                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ $heroContent->description ?? "Just enter AWB tracking number & it's done." }}
                </p>

            </div>

            <!-- Right Image -->
            <div class="col-md-6">
                <div class="form-shadow">
                 <form id="trackingForm" onsubmit="return false;">
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">{!! $trackFormContent->title ?? 'AWB Number' !!}</label>
                            <div class="input-group-custom">
                                <input type="text" id="awb_number_input" class="form-control input-custom" placeholder="{{ $trackFormContent->description ?? 'Airway Bill Number' }}" required>
                                <i class="fa-solid fa-box"></i>
                            </div>
                        </div>
                        
                    </div>
                    <button type="button" id="trackNowBtn" style="width: 240px;" class="btn moving-gradient-bg btn-primary-custom m-2" onclick="searchTracking()">
                        {{ $trackFormContent->button_text ?? 'Track Now' }}
                    </button>
                 </form>
                </div>

            </div>
    </div>
</header>

<!-- Tracking Results Section -->
<section id="trackingResultsSection" class="tracking-results-section" style="display: none;">
    <div class="container">
        <!-- Loading State -->
        <div id="trackingLoading" class="tracking-loading" style="display: none;">
            <div class="spinner"></div>
            <p style="margin-top: 10px;">Searching for your shipment...</p>
        </div>

        <!-- Error State -->
        <div id="trackingError" class="tracking-results-card" style="display: none;">
            <div class="tracking-error-msg">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 40px; margin-bottom: 10px;"></i>
                <p id="trackingErrorMsg">No tracking information found for this AWB number.</p>
            </div>
        </div>

        <!-- Success State -->
        <div id="trackingSuccess" style="display: none;">
            <!-- Current Status Card -->
            <div class="tracking-results-card">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3 style="font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 5px;">
                            AWB: <span id="resultAwbNumber" class="gradient-text"></span>
                        </h3>
                        <span id="resultStatusBadge" class="tracking-status-badge"></span>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 13px; color: #64748b;">Current Status</p>
                        <p id="resultCurrentTitle" style="font-size: 18px; font-weight: 600; color: #0f172a;"></p>
                    </div>
                </div>
            </div>

            <!-- Shipment & Consignee Details Card -->
            <div class="tracking-results-card">
                <h4 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 20px;">
                    <i class="fa-solid fa-file-lines" style="color: #8b5cf6;"></i> Shipment Details
                </h4>
                <div class="detail-grid" id="shipmentDetailsGrid">
                    <!-- Populated dynamically -->
                </div>
            </div>

            <!-- Tracking Timeline Card -->
            <div class="tracking-results-card">
                <h4 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 20px;">
                    <i class="fa-solid fa-timeline" style="color: #06b6d4;"></i> Tracking History
                </h4>
                <div class="tracking-timeline" id="trackingTimeline">
                    <!-- Populated dynamically -->
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Features cards section -->

<section class="features-section" style="padding:40px 0;">
  <div class="container">

  @if($featuresHeader)
  <div class="row justify-content-center mb-3">
            <div class="col-lg-10 text-center">
                <h2 class="about-title">{{ $featuresHeader->title ?? "What's your order status?" }}</h2>
                <p class="about-desc text-center">
                    {{ $featuresHeader->description ?? "Carriers use dimensional weight to price large, light packages — here's what you need to know." }}
                </p>
            </div>
        </div>
  @endif

    <div class="row g-4">
      @forelse($featuresContent as $feature)
        @php
          $c = $feature->content;
          $iconColor = $c['icon_color'] ?? 'blue';
          $iconColorCode = $c['icon_color_code'] ?? '#2563eb';
          $colorClassMap = ['blue' => 'fi-blue', 'orange' => 'fi-orange', 'green' => 'fi-green', 'purple' => 'fi-navy', 'navy' => 'fi-navy'];
          $colorClass = $colorClassMap[$iconColor] ?? 'fi-blue';
          $iconClass = !empty($c['icon_svg']) ? $c['icon_svg'] : (!empty($c['icon_class']) ? $c['icon_class'] : 'fa-solid fa-check-to-slot');
        @endphp
        <div class="col-md-3">
          <div class="feature-card">
            <div class="feature-icon {{ $colorClass }}">
              <i class="{{ $iconClass }}" style="color: {{ $iconColorCode }}"></i>
            </div>
            <h5>{{ $c['title'] ?? $feature->title }}</h5>
            <p>{{ $c['description'] ?? $feature->description }}</p>
          </div>
        </div>
      @empty
      <div class="col-md-3">
        <div class="feature-card">
          <div class="feature-icon fi-blue"><i class="fa-solid fa-check-to-slot text-primary"></i></div>
          <h5>Order Received</h5>
          <p>Your order has been received by your courier partner.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-card">
          <div class="feature-icon fi-orange"><i class="fa-solid fa-people-carry-box" style="color:#f7a76d"></i></div>
          <h5>Order Picked</h5>
          <p>Your order has been picked up by your courier partner</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-card">
          <div class="feature-icon fi-navy"><i class="fa-solid fa-truck-fast"></i></div>
          <h5>Out For Delivery</h5>
          <p>The courier executive is on its way to deliver the order at your customer's doorstep</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-card">
          <div class="feature-icon fi-green"><i class="fa-solid fa-map-location-dot text-success"></i></div>
          <h5>Reached Destination</h5>
          <p>Your order has reached your customer's city.</p>
        </div>
      </div>
      @endforelse
    </div>
  </div>
</section>



<section class="py-4 about-section">
        <div class="container">
            <div class="row align-items-center">
                               
                <!-- Content Side -->
                <div class="col-lg-6">
                    <div class="about-content">
                        <h2 class="about-title">{!! $aboutContent->title ?? 'Optimize your order <span class="gradient-text">tracking experience</span>' !!}</h2>
                                <p class="lead">{{ $aboutContent->description ?? 'Always stay informed about your shipments, regardless of your courier partner' }}<br><br>
                                </p>
                                @php $aboutData = $aboutContent ? $aboutContent->content : []; @endphp
                                @if(!empty($aboutData['check_list']))
                                <ul class="check-list lead">
                                    @foreach($aboutData['check_list'] as $item)
                                    <li><i class="fas fa-check-circle"></i> {{ $item }}</li>
                                    @endforeach
                                </ul>
                                @else
                                <ul class="check-list lead">
                                <li><i class="fas fa-check-circle"></i> Track orders shipped with 42 courier partners in one place.</li>
                                <li><i class="fas fa-check-circle"></i> Get real-time updates as and when your order status changes.</li>
                                </ul>
                                @endif
                                <a href="{{ $aboutData['button_url'] ?? 'contact-us.php' }}" class="sr-demo-btn-live mt-3">{{ $aboutData['button_text'] ?? $aboutContent->btn_text ?? 'Need help?' }}</a>
                    </div>
                </div>
                <div class="col-lg-6 animate-on-scroll show animate__animated animate__fadeInLeft" data-anim="animate__fadeInLeft" style="animation-delay: 0.1s;">
                    @if(!empty($aboutData['link']))
                    <iframe style="border-radius:20px" width="95%" height="300" src="{{ $aboutData['link'] }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    @else
                    <iframe style="border-radius:20px" width="95%" height="300" src="https://www.youtube.com/embed/tOvpjmnh5h4?si=-O5MSnO7OXm2Wspk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    @endif
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
                <div class="accordion" id="logisticsFaq" style="height: 70vh; overflow-y: auto;">
                    @forelse($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq{{ $index + 1 }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq{{ $index + 1 }}"
                            class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                            data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                {!! $faq->answer !!}
                            </div>
                        </div>
                    </div>
                    @empty
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
                                We utilize study and secure packaging for small to large packages to protect your goods during transit. In case of fragile items, they will be cushioned enough and clearly labelled as "Fragile". In addition, we also provide a packaging and labelling guide for all new on boarders for no confusion and faster results.
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
                                The exact shipping cost will be calculated based on your goods' weight, dimensions, destinations, where it is expected to be@dnlivdr('w bni t_e deuie.ry -pepd. If yom')re interested in knowing the accurate pricing, you are welcome to connect with the team.
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
                                Through our online tracking system, you can track youve');
                }
            });
        }, obserrerOptions);

        document.querySelectorAll('.timoline-item ).forEach(item => observer.observe(itemd)v
    </script>



@include('website_include.footer') </div>

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

    <!-- Tracking Search Script -->
    <script>
        function searchTracking() {
            const awbInput = document.getElementById('awb_number_input');
            const awbNumber = awbInput.value.trim();

            if (!awbNumber) {
                awbInput.focus();
                return;
            }

            const resultsSection = document.getElementById('trackingResultsSection');
            const loadingDiv = document.getElementById('trackingLoading');
            const errorDiv = document.getElementById('trackingError');
            const successDiv = document.getElementById('trackingSuccess');

            // Show loading, hide others
            resultsSection.style.display = 'block';
            loadingDiv.style.display = 'block';
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';

            // Scroll to results
            resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

            fetch('{{ route("tracking.search") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new URLSearchParams({
                    awb_number: awbNumber,
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                })
            })
            .then(response => {
                console.log('Tracking search response status:', response);
                if (!response.ok) {
                    throw new Error('Server returned ' + response.status + ': ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                loadingDiv.style.display = 'none';

                if (data.success) {
                    errorDiv.style.display = 'none';
                    successDiv.style.display = 'block';
                    renderTrackingResults(data);
                } else {
                    successDiv.style.display = 'none';
                    errorDiv.style.display = 'block';
                    document.getElementById('trackingErrorMsg').textContent = data.message || 'No tracking information found for this AWB number.';
                }
            })
            .catch(error => {
                loadingDiv.style.display = 'none';
                successDiv.style.display = 'none';
                errorDiv.style.display = 'block';
                document.getElementById('trackingErrorMsg').textContent = 'An error occurred while searching. Please try again.';
                console.error('Tracking search error:', error);
            });
        }

        function renderTrackingResults(data) {
            // AWB Number & Current Status
            document.getElementById('resultAwbNumber').textContent = data.awb_number;
            document.getElementById('resultCurrentTitle').textContent = data.current_title;

            const statusBadge = document.getElementById('resultStatusBadge');
            statusBadge.textContent = data.current_title;
            statusBadge.className = 'tracking-status-badge status-' + data.current_status;

            // Shipment Details
            const detailsGrid = document.getElementById('shipmentDetailsGrid');
            let detailsHtml = '';

            if (data.shipment) {
                const shipmentFields = [
                    { label: 'AWB Number', value: data.shipment?.awb_number, icon: 'fa-solid fa-barcode' },
                    { label: 'Shipping Method', value: data.shipment?.shipping_method, icon: 'fa-solid fa-truck' },
                    { label: 'Shipper Name', value: data.shipment?.shipper_name, icon: 'fa-solid fa-user' },
                    { label: 'Company', value: data.shipment?.shipper_company, icon: 'fa-solid fa-building' },
                    { label: 'Origin City', value: data.shipment?.shipper_city, icon: 'fa-solid fa-city' },
                    { label: 'Origin State', value: data.shipment?.shipper_state, icon: 'fa-solid fa-map' },
                    { label: 'Phone', value: data.shipment?.shipper_phone, icon: 'fa-solid fa-phone' },
                    { label: 'Email', value: data.shipment?.shipper_email, icon: 'fa-solid fa-envelope' },
                ];

                shipmentFields.forEach(field => {
                    if (field.value) {
                        detailsHtml += `<div class="detail-item">
                            <div class="detail-label"><i class="${field.icon}" style="margin-right: 5px;"></i>${field.label}</div>
                            <div class="detail-value">${field.value}</div>
                        </div>`;
                    }
                });
            }

            if (data.consignee) {
                const consigneeFields = [
                    { label: 'Consignee Name', value: data.consignee?.consignee_name, icon: 'fa-solid fa-user-tag' },
                    { label: 'Destination City', value: data.consignee?.consignee_city, icon: 'fa-solid fa-city' },
                    { label: 'Destination State', value: data.consignee?.consignee_state, icon: 'fa-solid fa-map' },
                    { label: 'Destination Country', value: data.consignee?.consignee_country, icon: 'fa-solid fa-globe' },
                    { label: 'Consignee Phone', value: data.consignee?.consignee_phone, icon: 'fa-solid fa-phone' },
                ];

                consigneeFields.forEach(field => {
                    if (field.value) {
                        detailsHtml += `<div class="detail-item">
                            <div class="detail-label"><i class="${field.icon}" style="margin-right: 5px;"></i>${field.label}</div>
                            <div class="detail-value">${field.value}</div>
                        </div>`;
                    }
                });
            }

            detailsGrid.innerHTML = detailsHtml || '<p style="color: #64748b;">No shipment details available.</p>';

            // Tracking Timeline
            const timelineDiv = document.getElementById('trackingTimeline');
            let timelineHtml = '';

            if (data.history && data.history.length > 0) {
                data.history.forEach((entry, index) => {
                    const isLast = index === data.history.length - 1;
                    const isActive = entry.status === data.current_status;
                    timelineHtml += `<div class="timeline-entry ${isActive ? 'active-entry' : ''}">
                        <div class="timeline-dot"></div>
                        <div class="timeline-title">${entry.title}</div>
                        <div class="timeline-time">${entry.timestamp || 'Time not available'}</div>
                    </div>`;
                });
            } else {
                timelineHtml = '<p style="color: #64748b;">No tracking history available.</p>';
            }

            timelineDiv.innerHTML = timelineHtml;
        }

        // Allow Enter key to trigger search
        document.getElementById('awb_number_input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchTracking();
            }
        });
    </script>



@include('website_include.footer')