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
                 <form action="#">
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">{!! $trackFormContent->title ?? 'AWB Number' !!}</label>
                            <div class="input-group-custom">
                                <input type="number" class="form-control input-custom" placeholder="{{ $trackFormContent->description ?? 'Airway Bill Number' }}">
                                <i class="fa-solid fa-box"></i>
                            </div>
                        </div>
                        
                    </div>
                    <button type="button" style="width: 240px;" class="btn moving-gradient-bg btn-primary-custom m-2">
                        {{ $trackFormContent->button_text ?? 'Track Now' }}
                    </button>
                 </form>
                </div>

            </div>
    </div>
</header>





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



@include('website_include.footer')