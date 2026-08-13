@include('website_include.header')

<!-- JsBarcode & QRCode Libraries -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>


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
    background-image: radial-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px);
    background-size: 18px 18px;
    opacity: 0.3;
}

/* content above pattern */
#resultBox>* {
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



.track-cta.light {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 40px 50px;
    border-radius: 20px;

    background: transparent linear-gradient(255deg, #ffc46554, #5338ff26);
    color: #111;

    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}

/* LEFT */
.track-left {
    max-width: 600px;
}

.live-badge {
    display: inline-block;
    background: rgba(34, 197, 94, 0.1);
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
    .barcode-format-label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 90px;
        padding: 6px 14px;
        border-radius: 37px;
        background: #2662eb21;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }
    .barcode-format-label:hover {
        background: #2662eb40;
    }
    .barcode-format-label.active-format {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    .barcode-preview-box {
        background: rgba(255,255,255,0.08);
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        min-height: 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .barcode-preview-box svg {
        max-width: 100%;
        height: auto;
    }
    .barcode-preview-box #qrcodeContainer img {
        margin: 0 auto;
    }
    .download-btns a {
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
    }
    .barcode-info {
        background: rgba(255,255,255,0.06);
        border-radius: 10px;
        padding: 10px 14px;
        color: rgba(255,255,255,0.85);
        font-size: 13px;
    }
    .track-right a{
        text-decoration: none;

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
                    {{ $heroContent->page_badge_text ?? 'Free Tool · Instant Download' }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $heroContent->title ?? 'AWB Barcode <span class="moving-gradient-text">Generator</span>' !!}
                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ $heroContent->description ?? 'Instantly generate shipping barcodes with your AWB or tracking number.' }}
                </p>
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
                        <h3 class="h4-title">Barcode <span class="gradient-text">Details</span></h3>
                    </div>

                    <form id="barcodeForm" onsubmit="return false;">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">AWB / Tracking Number</label>
                                <div class="input-group-custom">
                                    <input type="text" class="form-control input-custom" id="barcodeText"
                                        placeholder="e.g. 1Z999AA10123456784" value="SHIP123456">
                                    <i class="fa-solid fa-barcode"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Label Title (optional)</label>
                                <div class="input-group-custom">
                                    <input type="text" class="form-control input-custom" id="labelTitle"
                                        placeholder="e.g. United Express" value="United Worldwide Couriers">
                                    <i class="fa-solid fa-tag"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Note / Reference (optional)</label>
                                <div class="input-group-custom">
                                    <input type="text" class="form-control input-custom" id="noteReference"
                                        placeholder="e.g. Order #12345 · New Delhi → London">
                                    <i class="fa-solid fa-pen"></i>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Barcode Format</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 6px;" class="input-group-custom">
                                    <label class="m-0 text-center barcode-format-label active-format"
                                        data-format="CODE128">
                                        <input type="radio" name="barcodeFormat" value="CODE128" checked class="d-none"> CODE128
                                    </label>
                                    <label class="m-0 text-center barcode-format-label" data-format="CODE39">
                                        <input type="radio" name="barcodeFormat" value="CODE39" class="d-none"> CODE39
                                    </label>
                                    <label class="m-0 text-center barcode-format-label" data-format="EAN13">
                                        <input type="radio" name="barcodeFormat" value="EAN13" class="d-none"> EAN13
                                    </label>
                                    <label class="m-0 text-center barcode-format-label" data-format="ITF14">
                                        <input type="radio" name="barcodeFormat" value="ITF14" class="d-none"> ITF14
                                    </label>
                                    <label class="m-0 text-center barcode-format-label" data-format="MSI">
                                        <input type="radio" name="barcodeFormat" value="MSI" class="d-none"> MSI
                                    </label>
                                    <!-- <label class="m-0 text-center barcode-format-label" data-format="QR">
                                        <input type="radio" name="barcodeFormat" value="QR" class="d-none"> QR CODE
                                    </label> -->
                                </div>
                                <div id="formatHint" class=" mt-2">
    CODE128: Letters, numbers & common characters. Example: SHIP123456
</div>
                            </div>
                        </div>

                        <button type="button" style="width: 240px;"
                            class="btn moving-gradient-bg btn-primary-custom m-2" onclick="generateBarcode()">
                            <i class="fa-solid fa-qrcode me-1"></i> Generate Barcode
                        </button>

                        <button type="button" style="width: 150px; color: #525252; border: 1px solid #525252;"
                            class="btn btn-primary-custom m-2" onclick="resetForm()">
                            <i class="fa-solid fa-rotate-left me-1"></i> Reset
                        </button>
                    </form>

                </div>
            </div>

            <div class="col-md-4" id="resultBox">
                <div>
                    <h5><i class="fa-solid fa-barcode me-1"></i> Barcode Result</h5>
                    <hr style="border-color: rgba(255,255,255,0.2);">
                    <div class="barcode-preview-box">
                        <h6 id="previewTitle" class="text-white mb-2"></h6>
                        <svg id="barcodeSvg"></svg>
                        <div id="qrcodeContainer"></div>
                        <p id="noDataMsg" class="text-white-50 small mt-2 mb-0">Enter a tracking number and generate</p>
                    </div>
                    <div class="download-btns mt-3 text-center" id="downloadBtns" style="display: none;">
                        <a href="#" id="downloadPngBtn" class="btn btn-sm btn-light me-1"><i class="fa-solid fa-image me-1"></i>PNG</a>
                        <a href="#" id="downloadSvgBtn" class="btn btn-sm btn-warning"><i class="fa-solid fa-code me-1"></i>SVG</a>
                    </div>
                    <div class="barcode-info mt-3" id="barcodeInfo" style="display: none;">
                        <p class="mb-1 small"><strong>Format:</strong> <span id="formatText"></span></p>
                        <p class="mb-0 small"><strong>Characters:</strong> <span id="charCount"></span></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>





<!-- Features cards section -->

<section class="features-section" style="padding:40px 0;">
    <div class="container">


        <div class="row justify-content-center mb-3 d-none">
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
                    <div class="feature-icon fi-blue"><i
                            class="{{ $feature->page_icon_class ?? 'fa-solid fa-barcode' }}"></i></div>
                    <h5>{{ $feature->title }}</h5>
                    <p>{{ $feature->description }}</p>
                </div>
            </div>
            @empty
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon fi-blue"><i class="fa-solid fa-barcode"></i></div>
                    <h5>Enter your AWB number</h5>
                    <p>Paste your Air Waybill or tracking number from any courier service such as DHL, FedEx, UPS, India
                        Post, or ShipGlobal to instantly create a scannable barcode.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon fi-orange"><i class="fa-solid fa-sliders"></i></div>
                    <h5>Select barcode format</h5>
                    <p>Choose the barcode type that fits your shipping or logistics needs. CODE128 is widely supported
                        and works perfectly for most shipment labels.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon fi-navy"><i class="fa-solid fa-download"></i></div>
                    <h5>Download & print instantly</h5>
                    <p>Download high-quality PNG or scalable SVG barcode files for shipping labels, packaging, invoices,
                        warehouse management, and tracking documents.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>




@if($trackCta)
<div class="container my-5">
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
                    <a href="{{ $trackCta->link }}"
                        class="track-btn">{{ $trackCta->page_button_text ?? 'Track Shipment →' }}</a>
                    @else
                    <a target="_blank" href="./tracking" class="track-btn">
                        {{ $trackCta->page_button_text ?? 'Track Shipment →' }}
                    </a>
                    @endif
                </div>
            </section>

        </div>
    </div>
</div>
@endif









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
                <h2 class="about-title">{{ $testimonialsHeader->title ?? 'Trusted by the Brands You Trust' }}</h2>

                <p class="about-desc text-center">
                    {{ $testimonialsHeader->description ?? 'Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions.' }}
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
                        <img src="{{ asset($testimonial->customer_image) }}" class="img-fluid"
                            alt="{{ $testimonial->customer_name }}">
                        @endif
                        <h6>{{ $testimonial->customer_name }}</h6>
                    </div>
                </div>
                @empty
                <!-- Fallback static testimonials -->
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Very reliable logistics partner with consistent performance, ensuring
                        all my shipments arrive safely, securely, and always on time without any issues."</p>
                    <div class="user-info"> <img src="{{ asset('website_images/review-1.png') }}" class="img-fluid">
                        <h6>Shelly Kapoor</h6>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Customer support team is highly responsive and helpful, assisting me in
                        tracking my urgent shipment with accurate real-time updates and guidance."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-2.png') }}" class="img-fluid">
                        <h6>Vansh Agarwal</h6>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Best courier service for international deliveries, offering smooth
                        documentation, fast processing, and quick dispatch with no delays or complications."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-3.png') }}" class="img-fluid">
                        <h6>Rahul Mehta</h6>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Affordable pricing combined with premium quality service makes United
                        Worldwide Couriers a highly recommended choice for all shipping and logistics needs."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-4.png') }}" class="img-fluid">
                        <h6>Anjali Sharma</h6>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Professional and efficient team handled my bulk shipments perfectly,
                        ensuring timely delivery, careful handling, and a completely hassle-free logistics experience"
                    </p>
                    <div class="user-info"> <img src="{{ asset('website_images/review-5.png') }}" class="img-fluid">
                        <h6>Karan Singh</h6>
                    </div>
                </div>

                <!-- Duplicate for seamless loop -->
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"United Worldwide Couriers delivered my international parcel quickly and
                        safely, with smooth handling and excellent service throughout the entire process."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-5.png') }}" class="img-fluid">
                        <h6>Vinay Verma</h6>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Very reliable logistics partner with consistent performance, ensuring
                        all my shipments arrive safely, securely, and always on time without any issues."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-6.png') }}" class="img-fluid">
                        <h6>Shelly Kapoor</h6>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Customer support team is highly responsive and helpful, assisting me in
                        tracking my urgent shipment with accurate real-time updates and guidance."</p>
                    <div class="user-info"><img src="{{ asset('website_images/review-7.png') }}" class="img-fluid">
                        <h6>Vansh Agarwal</h6>
                    </div>
                </div>
                @endforelse

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
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq1">
                                How do I get started?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                To connect with our team, you have to register yourself, get a quote, and schedule your
                                first pickup. Thereafter, the team will guide you through every step of the process.
                            </div>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq2">
                                How does United Worldwide Couriers meet your shipping and logistics needs?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                To provide the best freight management solutions, we work with broad strategies,
                                technologies, and services to simplify the planning, storage, and movement of goods.
                            </div>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq3">
                                What packaging standards should we follow for shipping?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                We utilize study and secure packaging for small to large packages to protect your goods
                                during transit. In case of fragile items, they will be cushioned enough and clearly
                                labelled as "Fragile". In addition, we also provide a packaging and labelling guide for
                                all new on boarders for no confusion and faster results.
                            </div>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq4">
                                How do we calculate cost?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                The exact shipping cost will be calculated based on your goods' weight, dimensions,
                                destinations, where it is expected to be delivered, and the delivery speed. If you are
                                interested in knowing the accurate pricing, you are welcome to connect with the team.
                            </div>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq5">
                                Will I be notified about my shipment status?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes. To keep the clients informed, our team provides regular updates via email or SMS
                                throughout the shipping process.
                            </div>
                        </div>
                    </div>

                    <!-- Item 6 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq6">
                                Does United Worldwide Couriers handle bulk or commercial shipments?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes, we specialize in managing bulk as well as commercial consignments with easy-flowing
                                coordination, dedicated handling, secure transit, and timely delivery. This remains the
                                same for both small and large volumes.
                            </div>
                        </div>
                    </div>

                    <!-- Item 7 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq7">
                                Can I schedule a pickup for my shipment?
                            </button>
                        </h2>
                        <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes, you can easily schedule a pick up at your preferred time either by reaching out to
                                our team online (via our website) or by directly contacting our customer support.
                            </div>
                        </div>
                    </div>

                    <!-- Item 8 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq8">
                                How can I track my shipment?
                            </button>
                        </h2>
                        <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Through our online tracking system, you can track your shipment in real-time. For that,
                                you just have to enter the tracking ID provided on our website dashboard to get live
                                updates.
                            </div>
                        </div>
                    </div>

                    <!-- Item 9 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq9">
                                Will my package be picked up by the United Worldwide Couriers team only?
                            </button>
                        </h2>
                        <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                It depends on the service and location. So, your shipment may be picked up either by our
                                in-house delivery team or by third-party courier partners. On the contrary, we ensure
                                strict quality control and safe handling for whatever the case may be.
                            </div>
                        </div>
                    </div>

                    <!-- Item 10 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq10">
                                Do you provide customs clearance support?
                            </button>
                        </h2>
                        <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes, while others face limited support, we complete international customs documentation
                                and clearance faster. This way, we support rapid shipping without delays.
                            </div>
                        </div>
                    </div>

                    <!-- Item 11 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq11">
                                What happens if my shipment is delayed or stuck?
                            </button>
                        </h2>
                        <div id="faq11" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                First things first, the team proactively monitors the shipment to identify and resolve
                                potential issues before they escalate. Still, if an issue occurs, the internal team
                                coordinates with the partners to resolve the situation quickly.
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



<!-- BARCODE GENERATOR SCRIPT -->
<script>
// Auto-generate on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set CODE128 as default active format
    setActiveFormat('CODE128');
    generateBarcode();
});

// Format label click handler
document.querySelectorAll('.barcode-format-label').forEach(function(label) {
    label.addEventListener('click', function() {
        var radio = this.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            setActiveFormat(radio.value);
        }
    });
});

function setActiveFormat(format) {
    document.querySelectorAll('.barcode-format-label').forEach(function(el) {
        var radio = el.querySelector('input[type="radio"]');

        if (radio && radio.value === format) {
            el.classList.add('active-format');
        } else {
            el.classList.remove('active-format');
        }
    });

    const hints = {
        CODE128: 'Letters, numbers & common characters. Example: SHIP123456',
        CODE39: 'A-Z, 0-9 and - . $ / + %. Example: ABC-12345',
        EAN13: '12 or 13 digits. Example: 890123456789',
        ITF14: '13 or 14 digits. Example: 1234567890123',
        MSI: 'Numbers only. Example: 1234567890'
    };

    document.getElementById('formatHint').textContent =
        hints[format] || '';
}

function generateBarcode() {
    var text = document.getElementById('barcodeText').value.trim();
    var title = document.getElementById('labelTitle').value.trim();
    
    if (!text) {
        document.getElementById('noDataMsg').textContent = 'Please enter a tracking number';
        document.getElementById('noDataMsg').style.display = 'block';
        document.getElementById('barcodeSvg').style.display = 'none';
        document.getElementById('qrcodeContainer').innerHTML = '';
        document.getElementById('downloadBtns').style.display = 'none';
        document.getElementById('barcodeInfo').style.display = 'none';
        document.getElementById('previewTitle').textContent = '';
        return;
    }

    // Get selected format
    var format = 'CODE128';
    var selectedRadio = document.querySelector('input[name="barcodeFormat"]:checked');
    if (selectedRadio) {
        format = selectedRadio.value;
    }

    // Update preview title
    document.getElementById('previewTitle').textContent = title;

    // Update info
    document.getElementById('formatText').textContent = format;
    document.getElementById('charCount').textContent = text.length;

    // Hide no data message
    document.getElementById('noDataMsg').style.display = 'none';
    document.getElementById('downloadBtns').style.display = 'block';
    document.getElementById('barcodeInfo').style.display = 'block';

    var barcodeSvg = document.getElementById('barcodeSvg');
    var qrcodeContainer = document.getElementById('qrcodeContainer');

    // Clear previous
    qrcodeContainer.innerHTML = '';

    if (format === 'QR') {
        // Show QR code
        barcodeSvg.style.display = 'none';
        new QRCode(qrcodeContainer, {
            text: text,
            width: 180,
            height: 180,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    } else {
        // Show JsBarcode
        barcodeSvg.style.display = 'block';
        try {
            JsBarcode('#barcodeSvg', text, {
                format: format,
                lineColor: '#000000',
                width: 2,
                height: 80,
                displayValue: true,
                fontSize: 16,
                margin: 10,
                background: '#ffffff'
            });
        } catch (err) {
            document.getElementById('noDataMsg').textContent = 'Invalid data for ' + format + ' format';
            document.getElementById('noDataMsg').style.display = 'block';
            barcodeSvg.style.display = 'none';
            document.getElementById('downloadBtns').style.display = 'none';
            document.getElementById('barcodeInfo').style.display = 'none';
            return;
        }
    }
}

function resetForm() {
    document.getElementById('barcodeText').value = '';
    document.getElementById('labelTitle').value = '';
    document.getElementById('noteReference').value = '';
    
    // Reset format to CODE128
    document.querySelectorAll('input[name="barcodeFormat"]').forEach(function(radio) {
        radio.checked = (radio.value === 'CODE128');
    });
    setActiveFormat('CODE128');
    
    generateBarcode();
}

// Download PNG handler
document.getElementById('downloadPngBtn').addEventListener('click', function(e) {
    e.preventDefault();
    var format = document.querySelector('input[name="barcodeFormat"]:checked');
    var fmt = format ? format.value : 'CODE128';
    
    if (fmt === 'QR') {
        var qrImg = document.querySelector('#qrcodeContainer img');
        if (qrImg) {
            var link = document.createElement('a');
            link.download = 'qrcode.png';
            link.href = qrImg.src;
            link.click();
        }
    } else {
        var svg = document.getElementById('barcodeSvg');
        if (svg) {
            var svgData = new XMLSerializer().serializeToString(svg);
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');
            var img = new Image();
            img.onload = function() {
                canvas.width = img.width * 2;
                canvas.height = img.height * 2;
                ctx.scale(2, 2);
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);
                var link = document.createElement('a');
                link.download = 'barcode.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            };
            img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
        }
    }
});

// Download SVG handler
document.getElementById('downloadSvgBtn').addEventListener('click', function(e) {
    e.preventDefault();
    var format = document.querySelector('input[name="barcodeFormat"]:checked');
    var fmt = format ? format.value : 'CODE128';
    
    if (fmt === 'QR') {
        var qrImg = document.querySelector('#qrcodeContainer img');
        if (qrImg) {
            var link = document.createElement('a');
            link.download = 'qrcode.png';
            link.href = qrImg.src;
            link.click();
        }
    } else {
        var svg = document.getElementById('barcodeSvg');
        if (svg) {
            var svgData = new XMLSerializer().serializeToString(svg);
            var blob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
            var link = document.createElement('a');
            link.download = 'barcode.svg';
            link.href = URL.createObjectURL(blob);
            link.click();
            URL.revokeObjectURL(link.href);
        }
    }
});
</script>

@include('website_include.footer')