@include('website_include.header')

<style>
    /* Right side container */
    #resultBox {
        
        border-radius: 20px;
        padding: 25px;
        color: #fff;

        background: linear-gradient(135deg, #0b1a2b, #0f2b5c);
        position: relative;
        overflow: hidden;
    }

    /* subtle dotted pattern */
    #resultBox::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
        background-size: 18px 18px;
        opacity: 0.3;
    }

    /* content above pattern */
    #resultBox > * {
        position: relative;
        z-index: 2;
    }

    /* heading */
    #resultBox h5 {
        font-size: 13px;
        letter-spacing: 1px;
        color: #ffb020;
        margin-bottom: 10px;
    }

    /* big weight */
    #finalWeight {
        font-size: 60px;
        font-weight: 700;
        margin: 0;
    }

    #finalWeight span {
        font-size: 18px;
        opacity: 0.7;
    }

    /* divider line */
    #resultBox hr {
        border-color: rgb(255 255 255 / 60%);
    }

    /* info rows */
    #resultBox p {
        margin: 8px 0;
        color: #7488a7;
        background: #ffffff14;
        padding: 8px 12px;
        font-size: 14px;
        border-radius: 9px;
    }

    /* highlight box (like 1.68 kg badge) */
    #volWeight {
        background: #1e66ff;
        padding: 4px 10px;
        border-radius: 6px;
        color: #fff;
        font-weight: 600;
    }

    /* small cards inside */
    .result-item {
        background: rgba(255,255,255,0.05);
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 10px;
    }

    /* mobile spacing */
    @media (max-width: 768px) {
        #resultBox {
            margin-top: 20px;
        }
    }


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
                    {{ $heroContent->page_badge_text ?? 'Free Tool · Instant Results' }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $heroContent->title ?? 'Shipping <span class="moving-gradient-text">Rate Calculator</span>' !!}
                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ $heroContent->description ?? 'Get accurate, all-inclusive rates in seconds — no hidden charges, no guesswork.' }}
                </p>

            </div>

            <!-- Right Image -->
            <div class="col-md-6 text-center">
                <div class="hero-graphic">
                    <div class="">
                        <img src="{{ asset($heroContent->image ?? 'website_images/shipping_rate_calculator.png') }}" class="img-fluid">
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>




<!-- Calculator section -->

 <section class="py-5 bg-white">
    <div class="container">
      <div class="row form-shadow ">
        <div class="col-md-8">
          <div class="mx-auto">
             <div class="mb-4">
                <h3 class="h4-title">Calculate Your <span class="gradient-text">Shipping Rate</span></h3>
             </div>
            
              <form id="shippingRateForm">

    <!-- Destination, location and postal code -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <label for="destinationCountry" class="form-label fw-bold">
                <i class="fa-solid fa-earth-asia me-2"></i>
                Destination Country
            </label>
            <div class="input-group-custom">
                <select id="destinationCountry" name="destination_id" class="form-select input-custom" required>
                    <option value="" selected>Select destination</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <label for="destinationLocation" class="form-label fw-bold">
                <i class="fa-solid fa-location-dot me-2"></i>
                State / City
            </label>
            <div class="input-group-custom">
                <select id="destinationLocation" name="location" class="form-select input-custom" disabled>
                    <option value="">Select destination first</option>
                </select>
                <input type="text" id="destinationLocationText" name="location" class="form-control input-custom d-none"
                    placeholder="Enter state or city" maxlength="100" disabled>
            </div>
            <small id="locationHelp" class="text-muted mt-2 d-block">Configured states and cities will appear as a dropdown; otherwise you can type the location.</small>
        </div>

        <div class="col-md-6">
            <label for="destinationZipcode" class="form-label fw-bold">
                <i class="fa-solid fa-map-pin me-2"></i>
                ZIP / Postal Code
            </label>
            <div class="input-group-custom">
                <select id="destinationZipcodeSelect" name="zipcode" class="form-select input-custom d-none" disabled>
                    <option value="">Select ZIP / postal code</option>
                </select>
                <input type="text" id="destinationZipcode" name="zipcode" class="form-control input-custom"
                    placeholder="Enter ZIP or postal code" maxlength="20" required disabled>
            </div>
            <small id="zipcodeHelp" class="text-muted mt-2 d-block">Configured ZIP/postal codes will appear as a dropdown; otherwise you can type one.</small>
        </div>

        <div class="col-md-6">
            <label for="shipmentWeight" class="form-label fw-bold">
                <i class="fa-solid fa-scale-balanced me-2"></i>
                Shipment Weight
            </label>
            <div class="input-group-custom">
                <input type="number" id="shipmentWeight" name="weight" class="form-control input-custom"
                    placeholder="Enter weight in kg" min="0.001" max="10000" step="0.001" required>
                <span style="padding: 14px 20px; border-left:1px solid #ddd;">kg</span>
            </div>
        </div>
    </div>

    <div class="mb-4 d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-secondary rounded-pill weight-chip" data-weight="0.1">0.1 kg</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill weight-chip" data-weight="0.25">0.25 kg</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill weight-chip" data-weight="0.5">0.5 kg</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill weight-chip" data-weight="1">1 kg</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill weight-chip" data-weight="2">2 kg</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill weight-chip" data-weight="5">5 kg</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill weight-chip" data-weight="10">10 kg</button>
    </div>

    <div id="rateFormError" class="alert alert-danger d-none" role="alert"></div>

    <hr>

    <!-- Buttons -->
    <div class="d-flex flex-wrap gap-3 mt-4">

        <button type="submit" id="getRateBtn"
            style="width: 320px;"
            class="btn moving-gradient-bg btn-primary-custom">
            <i class="fa-solid fa-arrow-right me-2"></i>
            Get Shipping Rate
        </button>

        <button type="reset" id="resetRateBtn"
            style="width: 150px; color: #525252; border: 1px solid #d7d7d7;"
            class="btn btn-primary-custom">
            <i class="fa-solid fa-rotate-left me-2"></i>
            Reset
        </button>

    </div>

</form>

            </div>
        </div>


        <div class="col-md-4" id="resultBox" style="color: white;">
            <div>
                <h5>HOW RATES ARE MATCHED</h5>
                <h2 class="mt-3">Destination-specific pricing</h2>
                <hr style="color: #fff;">
                <p>Select the destination first, then choose an available state or city and enter the ZIP/postal code.</p>
                <div class="mt-4">
                    <p><i class="fa-solid fa-circle-check me-2"></i>ZIP-specific rate is checked first</p>
                    <p><i class="fa-solid fa-circle-check me-2"></i>State/city rate is used as fallback</p>
                    <p><i class="fa-solid fa-circle-check me-2"></i>Only matching weight ranges are shown</p>
                </div>
            </div>
        </div>

      </div>
    </div>
  </section>





<!-- Destination-filtered rate results -->
<section class="py-5 d-none" id="defaultRatesSection" style="background: #f8fafc;">
    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8 text-center">
                <h2 class="about-title">Available <span class="gradient-text">Shipping Rates</span></h2>
                <p class="about-desc text-center" id="ratesSubtitle">Rates matching your destination, location, ZIP code and weight.</p>
            </div>
        </div>
        <div class="row g-4 justify-content-center" id="rateResults"></div>
        <div class="text-center py-5 d-none" id="noRateMatch">
            <i class="fa-solid fa-magnifying-glass fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0" id="noRateMessage">No matching shipping rate is available.</p>
        </div>
    </div>
</section>

<!-- Features cards section -->

<section class="features-section" style="padding:40px 0;">
  <div class="container">


  <div class="row justify-content-center mb-3">
            <div class="col-lg-10 text-center">
                <h2 class="about-title">{{ $featuresHeading->title ?? 'Understanding volumetric weight' }}</h2>
                
                <p class="about-desc text-center">
                    {{ $featuresHeading->description ?? 'Carriers use dimensional weight to price large, light packages — here\'s what you need to know.' }}
                </p>
            </div>
        </div>

    <div class="row g-4">
        @forelse($features as $feature)
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon fi-blue"><i class="{{ $feature->page_icon_class ?? 'fa-solid fa-location-dot' }}"></i></div>
                <h5>{{ $feature->title }}</h5>
                <p>{{ $feature->description }}</p>
            </div>
        </div>
        @empty
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon fi-blue"><i class="fa-solid fa-location-dot"></i></div>
                <h5>Enter Pickup Details</h5>
                <p>Add your pickup pincode and shipment information to begin calculating your courier charges across India and international destinations.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon fi-orange"><i class="fa-solid fa-earth-asia"></i></div>
                <h5>Select Destination Country</h5>
                <p>Choose the delivery country and shipment weight to get accurate international shipping rates with express delivery options.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon fi-navy"><i class="fa-solid fa-money-bill-wave"></i></div>
                <h5>Get Shipping Rate Instantly</h5>
                <p>View estimated courier pricing instantly with transparent charges, faster transit options, and no hidden costs at checkout.</p>
            </div>
        </div>
        @endforelse
    </div>
  </div>
</section>



@if($trackCta)
<div class="container my-5" style="display:none">
    <div class="row">
        <div class="col-12">
            
            <section class="track-cta light">
                <div class="track-left">
                    <span class="live-badge">{{ $trackCta->page_badge_text ?? '● LIVE TRACKING' }}</span>

                    <h2>{{ $trackCta->title ?? 'Track any shipment in real-time' }}</h2>

                    <p>
                        {{ $trackCta->description ?? 'Get live updates across all major carriers — end-to-end visibility from pickup to delivery for every order you export worldwide.' }}
                    </p>
                </div>

                <div class="track-right">
                    @if($trackCta->link)
                    <a href="{{ $trackCta->link }}" class="track-btn">{{ $trackCta->page_button_text ?? 'Track Shipment →' }}</a>
                    @else
                    <button class="track-btn">
                        {{ $trackCta->page_button_text ?? 'Track Shipment →' }}
                    </button>
                    @endif
                </div>
            </section>

        </div>
    </div>
</div>
@endif









<!-- testimonial -->
<section class="testimonial-section" style="display:none">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-3">
            <div class="col-lg-8 text-center">
                <div class="google-badge">
                    <a href="#">
                     <img src="{{ asset('website_images/google-review.png') }}" alt="Google">
                    </a>
                </div>
                <h2 class="about-title">{{ $testimonialsHeader->title ?? 'Trusted by the Brands You Trust' }}</h2>
                
                <p class="about-desc text-center">
                    {{ $testimonialsHeader->description ?? 'Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record speaks for itself. From on-time delivery and zero-compromise on quality to customer satisfaction, United Worldwide Couriers is completely reliable and trustworthy.' }}
                </p>
            </div>
        </div>

        <div class="slider-wrapper">
            <div class="slider-track">
                @forelse($testimonials as $testimonial)
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
                @empty
                <!-- Fallback static testimonials -->
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues."</p>
                    <div class="user-info"> <img src="{{ asset('website_images/review-1.png') }}" class="img-fluid"> <h6>Shelly Kapoor</h6></div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-2.png') }}" class="img-fluid"> <h6>Vansh Agarwal</h6></div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Best courier service for international deliveries, offering smooth documentation, fast processing, and quick dispatch with no delays or complications."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-3.png') }}" class="img-fluid"> <h6>Rahul Mehta</h6></div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Affordable pricing combined with premium quality service makes United Worldwide Couriers a highly recommended choice for all shipping and logistics needs."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-4.png') }}" class="img-fluid"> <h6>Anjali Sharma</h6></div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Professional and efficient team handled my bulk shipments perfectly, ensuring timely delivery, careful handling, and a completely hassle-free logistics experience"</p>
                    <div class="user-info"> <img src="{{ asset('website_images/review-5.png') }}" class="img-fluid"> <h6>Karan Singh</h6></div>
                </div>

                <!-- Duplicate for seamless loop -->
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"United Worldwide Couriers delivered my international parcel quickly and safely, with smooth handling and excellent service throughout the entire process."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-5.png') }}" class="img-fluid"> <h6>Vinay Verma</h6></div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-6.png') }}" class="img-fluid"> <h6>Shelly Kapoor</h6></div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-7.png') }}" class="img-fluid"> <h6>Vansh Agarwal</h6></div>
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
                            <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq{{ $index + 1 }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq{{ $index + 1 }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#logisticsFaq">
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
                                The exact shipping cost will be calculated based on your goods' weight, dimensions, destinations, where it is expected to be delivered, and the delivery speed. If you are interested in knowing the accurate pricing, you are welcome to connect with the team.
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
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</section>




  <!-- MOUSE HOVER GREDIENT EFFECT ON MISSION AND VISSION SCRIPS -->

    <script>
        (function () {
            const form = document.getElementById('shippingRateForm');
            const destination = document.getElementById('destinationCountry');
            const location = document.getElementById('destinationLocation');
            const locationText = document.getElementById('destinationLocationText');
            const zipcode = document.getElementById('destinationZipcode');
            const zipcodeSelect = document.getElementById('destinationZipcodeSelect');
            const weightInput = document.getElementById('shipmentWeight');
            const getRateBtn = document.getElementById('getRateBtn');
            const ratesSection = document.getElementById('defaultRatesSection');
            const results = document.getElementById('rateResults');
            const subtitle = document.getElementById('ratesSubtitle');
            const noMatchBox = document.getElementById('noRateMatch');
            const noRateMessage = document.getElementById('noRateMessage');
            const errorBox = document.getElementById('rateFormError');
            const locationHelp = document.getElementById('locationHelp');
            const zipcodeHelp = document.getElementById('zipcodeHelp');
            const weightChips = document.querySelectorAll('.weight-chip');
            const locationsUrl = @json(route('shipping-rate-calculator.locations'));
            const ratesUrl = @json(route('shipping-rate-calculator.rates'));
            const csrfToken = @json(csrf_token());

            function escapeHtml(value) {
                const element = document.createElement('div');
                element.textContent = value == null ? '' : String(value);
                return element.innerHTML;
            }

            function showError(message) {
                errorBox.textContent = message;
                errorBox.classList.remove('d-none');
            }

            function clearError() {
                errorBox.textContent = '';
                errorBox.classList.add('d-none');
            }

            weightChips.forEach(function (chip) {
                chip.addEventListener('click', function () {
                    weightInput.value = chip.dataset.weight;
                    weightChips.forEach(function (item) { item.classList.remove('active', 'btn-secondary'); });
                    chip.classList.add('active', 'btn-secondary');
                });
            });

            destination.addEventListener('change', async function () {
                clearError();
                location.disabled = true;
                locationText.disabled = true;
                zipcode.disabled = true;
                zipcodeSelect.disabled = true;
                location.classList.remove('d-none');
                locationText.classList.add('d-none');
                zipcode.classList.remove('d-none');
                zipcodeSelect.classList.add('d-none');
                location.innerHTML = '<option value="">Loading locations...</option>';
                zipcode.value = '';
                ratesSection.classList.add('d-none');

                if (!destination.value) {
                    location.innerHTML = '<option value="">Select destination first</option>';
                    return;
                }

                try {
                    const response = await fetch(locationsUrl + '?destination_id=' + encodeURIComponent(destination.value), {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Unable to load locations.');

                    if (data.locations.length) {
                        location.innerHTML = '<option value="">Select state / city</option>';
                        data.locations.forEach(function (item) {
                            const option = document.createElement('option');
                            option.value = item.value;
                            option.textContent = item.label + ' (' + item.category.charAt(0).toUpperCase() + item.category.slice(1) + ')';
                            location.appendChild(option);
                        });
                        location.disabled = false;
                        location.classList.remove('d-none');
                        locationText.classList.add('d-none');
                        locationHelp.textContent = 'Select the applicable state or city from the configured list.';
                    } else {
                        location.classList.add('d-none');
                        locationText.classList.remove('d-none');
                        locationText.disabled = false;
                        locationText.value = '';
                        locationHelp.textContent = 'No state/city list is configured. Type the state or city.';
                    }

                    if (data.zipcodes && data.zipcodes.length) {
                        zipcodeSelect.innerHTML = '<option value="">Select ZIP / postal code</option>';
                        data.zipcodes.forEach(function (item) {
                            const option = document.createElement('option');
                            option.value = item.value;
                            option.textContent = item.label;
                            zipcodeSelect.appendChild(option);
                        });
                        zipcode.classList.add('d-none');
                        zipcode.required = false;
                        zipcodeSelect.classList.remove('d-none');
                        zipcodeSelect.disabled = false;
                        zipcodeSelect.required = true;
                        zipcodeHelp.textContent = 'Select a configured ZIP/postal code.';
                        zipcodeSelect.focus();
                    } else {
                        zipcodeSelect.classList.add('d-none');
                        zipcodeSelect.required = false;
                        zipcode.classList.remove('d-none');
                        zipcode.disabled = false;
                        zipcode.required = true;
                        zipcodeHelp.textContent = 'No ZIP/postal-code list is configured. Type the ZIP/postal code.';
                        zipcode.focus();
                    }
                } catch (error) {
                    location.innerHTML = '<option value="">Locations unavailable</option>';
                    showError(error.message);
                }
            });

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                clearError();

                if (!form.reportValidity()) return;

                getRateBtn.disabled = true;
                getRateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Checking Rates...';
                results.innerHTML = '';
                noMatchBox.classList.add('d-none');

                try {
                    const response = await fetch(ratesUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            destination_id: destination.value,
                            location: locationText.classList.contains('d-none') ? location.value : locationText.value.trim(),
                            zipcode: zipcodeSelect.classList.contains('d-none') ? zipcode.value.trim() : zipcodeSelect.value,
                            weight: weightInput.value
                        })
                    });
                    const data = await response.json();
                    if (!response.ok) {
                        const validationMessage = data.errors
                            ? Object.values(data.errors).flat()[0]
                            : data.message;
                        throw new Error(validationMessage || 'Unable to calculate rates.');
                    }

                    ratesSection.classList.remove('d-none');
                    const matched = data.matched_location
                        ? ' Matched by ' + data.matched_location.category + ': ' + data.matched_location.name + '.'
                        : '';
                    subtitle.textContent = 'Rates for ' + weightInput.value + ' kg.' + matched;

                    if (!data.rates.length) {
                        noRateMessage.textContent = data.message || 'No matching shipping rate is available.';
                        noMatchBox.classList.remove('d-none');
                    } else {
                        results.innerHTML = data.rates.map(function (rate) {
                            const tat = rate.tat ? '<p class="text-muted mb-2"><i class="fa-regular fa-clock me-2"></i>' + escapeHtml(rate.tat) + '</p>' : '';
                            return '<div class="col-md-6 col-lg-4">' +
                                '<div class="card h-100 shadow-sm border-0" style="border-radius:16px;overflow:hidden">' +
                                '<div class="card-header py-3 text-white" style="background:linear-gradient(to right,#2563eb,#9333ea)">' +
                                '<h5 class="mb-0"><i class="fa-solid fa-truck-fast me-2"></i>' + escapeHtml(rate.method) + '</h5></div>' +
                                '<div class="card-body p-4">' + tat +
                                '<p class="mb-2">Weight: <strong>' + escapeHtml(weightInput.value) + ' kg</strong></p>'+
                                '<strong>Base Rate - INR ' + Number(rate.price).toFixed(2) + '</strong>' +
                                '</div></div></div>';
                        }).join('');
                    }
                    ratesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } catch (error) {
                    showError(error.message);
                } finally {
                    getRateBtn.disabled = false;
                    getRateBtn.innerHTML = '<i class="fa-solid fa-arrow-right me-2"></i>Get Shipping Rate';
                }
            });

            form.addEventListener('reset', function () {
                setTimeout(function () {
                    location.disabled = true;
                    locationText.disabled = true;
                    zipcode.disabled = true;
                    zipcodeSelect.disabled = true;
                    location.classList.remove('d-none');
                    locationText.classList.add('d-none');
                    zipcode.classList.remove('d-none');
                    zipcodeSelect.classList.add('d-none');
                    zipcode.required = true;
                    zipcodeSelect.required = false;
                    location.innerHTML = '<option value="">Select destination first</option>';
                    locationHelp.textContent = 'Configured states and cities will appear as a dropdown; otherwise you can type the location.';
                    zipcodeHelp.textContent = 'Configured ZIP/postal codes will appear as a dropdown; otherwise you can type one.';
                    ratesSection.classList.add('d-none');
                    results.innerHTML = '';
                    clearError();
                    weightChips.forEach(function (item) { item.classList.remove('active', 'btn-secondary'); });
                }, 0);
            });
        })();
    </script>

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