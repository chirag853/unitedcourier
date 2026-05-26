@include('website_include.header')


<style>
        :root {
            --uwd-primary: #2563eb;
            --uwd-primary-dark: #1d4ed8;
            --uwd-text-main: #0f172a;
            --uwd-text-muted: #64748b;
            --uwd-bg: #ffffff;
            --uwd-card-border: #e2e8f0;
        }


        .contact-container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .contact-wrapper {
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 30px 60px -12px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid var(--uwd-card-border);
        }

        /* Left Side: Info Panel */
        .contact-info-panel {
            background: linear-gradient(to right, #2563eb, #9333ea);
            color: white;
            padding: 50px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .info-item {
            display: flex;
            gap: 20px;
            margin-bottom: 35px;
        }

        .info-icon {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .info-content h5 {
            font-family: 'Outfit', sans-serif;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .info-content p, .info-content a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        .social-links {
            margin-top: auto;
            display: flex;
            gap: 15px;
            padding-top: 30px;
        }

        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        .social-link:hover {
            background: white;
            color: var(--uwd-primary);
            transform: translateY(-3px);
        }

        /* Right Side: Form Panel */
        .contact-form-panel {
            padding: 50px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--uwd-text-main);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            padding: 12px 18px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 0.95rem;
            background-color: #f8fafc;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--uwd-primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-send {
            background: var(--uwd-primary);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 700;
            width: 100%;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-send:hover {
            background: var(--uwd-primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
        }

        /* Map Section Styling */
        .map-section {
            width: 100%;
            height: 400px;
            border-top: 1px solid var(--uwd-card-border);
            border-bottom: 1px solid var(--uwd-card-border);
            filter: grayscale(10%) contrast(90%);
        }

        #form-success {
            display: none;
            text-align: center;
            padding: 40px 0;
        }

        /* Track CTA styles */
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

        .track-btn {
            background: #111827;
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .track-btn:hover {
            background: #1f2937;
            transform: translateY(-2px);
            color: #fff;
        }

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

        @media (max-width: 991px) {
            .contact-info-panel, .contact-form-panel {
                padding: 40px 30px;
            }
            .map-section {
                height: 300px;
            }
        }
    </style>



<!-- Hero section -->
<header style="min-height: 20vh; padding-top: 130px; padding-bottom: 30px;" class="hero-gradient">
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
    </div>
    <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;">
    </div>

    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <div class="col-lg-12 text-center animate__animated animate__fadeInLeft">
                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    {{ $heroContent->page_badge_text ?? 'HSN Finder' }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $heroContent->title ?? 'HSN Classification <span class="moving-gradient-text"> Made Simple</span>' !!}
                </h1>
                <p style="max-width: 100%;" class="text-center mb-3 lead">
                    {{ $heroContent->description ?? 'Want to know a product\'s Indian HSN or US HTS code in a snap? Just upload or take a photo.' }}
                </p>

                <a href="#" class="book-btn-service"><i class="{{ $heroContent->page_icon_class ?? 'fa-solid fa-camera' }}"></i> &nbsp; {{ $heroContent->page_button_text ?? 'Capture Again' }}</a>
                <a href="#" class="book-btn-service"><i class="fa-solid fa-upload"></i> &nbsp; Upload Here</a>
                <p style="max-width: 100%;" class="text-center my-4 lead">
                   {{ $heroContent->subtitle ?? 'JPG, PNG, WEBP • Max 15MB.' }}
                </p>
            </div>

        </div>
    </div>
</header>



<section class="" style="padding:40px 0;">
  <div class="container">

  <div class="row justify-content-center mb-3">
            <div class="col-lg-10 text-center">
                <h2 class="about-title">{{ $featuresHeading->title ?? 'How It Works?' }}</h2>
                
                <p class="about-desc text-center">
                    {{ $featuresHeading->description ?? 'Three simple steps to get accurate HSN and HTS codes for your products' }}
                </p>
            </div>
        </div>

    <div class="row g-4">
        @forelse($features as $feature)
        <div class="col-md-4">
            <div class="feature-card bg-light">
                <div class="feature-icon fi-blue">
                    <i class="{{ $feature->page_icon_class ?? 'fa-solid fa-image' }} text-primary"></i>
                </div>
                <h5>{{ $feature->title }}</h5>
                <p>{{ $feature->description }}</p>
            </div>
        </div>
        @empty
        <div class="col-md-4">
            <div class="feature-card bg-light">
                <div class="feature-icon fi-blue">
                    <i class="fa-solid fa-image text-primary"></i>
                </div>
                <h5>Upload Image</h5>
                <p>Snap a photo or upload an existing image in JPG, PNG, or WEBP format with a maximum file size of 15MB.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-card bg-light">
                <div class="feature-icon fi-orange">
                    <i class="fa-solid fa-robot" style="color:#f7a76d"></i>
                </div>
                <h5>AI Analysis</h5>
                <p>Our smart AI analyzes your product, checks materials, structure, features, and other important details for accurate classification.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-card bg-light">
                <div class="feature-icon fi-navy">
                    <i class="fa-solid fa-file-circle-check"></i>
                </div>
                <h5>Get Results</h5>
                <p>Instantly receive the HSN & HTS codes with confidence scores and additional insights for complex or sensitive products.</p>
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
                    <p class="testimonial-text">{{ $testimonial->content }}</p>
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
                <div class="faq-illustration">
                    <img src="https://i.pinimg.com/originals/f6/5d/46/f65d4681649d85bc91c86872a1775919.gif" alt="Help"
                        style="width: 200px; margin-top: -40px;">
                    <h4 class="fw-bold mb-3">{{ $faqContactSidebar->title ?? 'Need personalized help?' }}</h4>
                    <p class="text-muted">
                        {{ $faqContactSidebar->description ?? 'Our logistics experts are available 24/7 to assist your requirements.' }}
                    </p>

                    <div class="moving-gradient-bg contact-box">
                        <h4>Contact Us</h4>
                        <p>For urgent inquiries regarding your current shipment status.</p>
                        <button style="background-color: #fff; color: #2563eb;" class="btn btn-contact">Message
                            Support</button>
                    </div>
                </div>
            </div>


            <div class="col-lg-8">
                <div class="accordion" id="logisticsFaq" style="height: 70vh; overflow-y: auto;">
                    @forelse($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq{{ $index + 1 }}">
                                {{ $faq->title }}
                            </button>
                        </h2>
                        <div id="faq{{ $index + 1 }}"
                            class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                            data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                {!! $faq->description !!}
                            </div>
                        </div>
                    </div>
                    @empty
                    <!-- Item 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq1">
                                What is an HSN code and why is it important?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                HSN (Harmonized System of Nomenclature) is a standardized system of names and numbers used to classify traded products. It is essential for determining customs duties, taxes, and regulatory requirements for international shipments.
                            </div>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq2">
                                How does the HSN finder tool work?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Simply upload a photo or image of your product, and our AI-powered tool analyzes its features, materials, and structure to provide you with the most accurate HSN and HTS code classification.
                            </div>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq3">
                                What file formats are supported for image upload?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                We support JPG, PNG, and WEBP image formats with a maximum file size of 15MB. For best results, ensure your product image is clear and well-lit.
                            </div>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq4">
                                What is the difference between HSN and HTS codes?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                HSN codes are used in India for classifying goods under the GST regime, while HTS (Harmonized Tariff Schedule) codes are used in the United States for customs and tariff purposes. Both are based on the same international Harmonized System.
                            </div>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq5">
                                Is the HSN code lookup free to use?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                Yes, our HSN finder tool is completely free to use. You can upload images and get HSN/HTS code classifications without any charges or subscription fees.
                            </div>
                        </div>
                    </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</section>


<!-- Camera Modal -->
<div class="camera-modal-overlay" id="cameraModal" style="display: none;">
    <div class="camera-modal-content">
        <div class="camera-modal-header">
            <h5 class="camera-modal-title"><i class="fa-solid fa-camera"></i> Capture Product Photo</h5>
            <button type="button" class="camera-close-btn" id="closeCameraBtn">&times;</button>
        </div>
        <div class="camera-modal-body">
            <!-- Video stream container -->
            <div class="camera-view-container" id="cameraViewContainer">
                <video id="cameraFeed" autoplay playsinline></video>
                <div class="camera-overlay-inner">
                    <div class="camera-frame-guide">
                        <div class="frame-corner tl"></div>
                        <div class="frame-corner tr"></div>
                        <div class="frame-corner bl"></div>
                        <div class="frame-corner br"></div>
                    </div>
                </div>
            </div>

            <!-- Captured image preview -->
            <div class="camera-preview-container" id="cameraPreviewContainer" style="display: none;">
                <img id="capturedImage" alt="Captured photo preview">
            </div>

            <!-- Camera loading / error states -->
            <div class="camera-loading" id="cameraLoading" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Accessing camera...</p>
            </div>
            <div class="camera-error" id="cameraError" style="display: none;">
                <i class="fa-solid fa-exclamation-triangle text-warning"></i>
                <p id="cameraErrorMessage">Unable to access camera. Please check permissions.</p>
            </div>
        </div>
        <div class="camera-modal-footer">
            <button type="button" class="btn btn-secondary" id="retakeBtn" style="display: none;">
                <i class="fa-solid fa-rotate-left"></i> Retake
            </button>
            <button type="button" class="btn btn-primary" id="captureBtn">
                <i class="fa-solid fa-circle-dot"></i> Capture Photo
            </button>
            <button type="button" class="btn btn-success" id="usePhotoBtn" style="display: none;">
                <i class="fa-solid fa-check"></i> Use This Photo
            </button>
        </div>
    </div>
</div>


<style>
    /* Camera Modal Styles */
    .camera-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.85);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: cameraFadeIn 0.25s ease;
    }

    @keyframes cameraFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .camera-modal-content {
        background: #fff;
        border-radius: 20px;
        width: 90%;
        max-width: 560px;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
        animation: cameraSlideIn 0.3s ease;
    }

    @keyframes cameraSlideIn {
        from { transform: scale(0.95) translateY(20px); opacity: 0; }
        to { transform: scale(1) translateY(0); opacity: 1; }
    }

    .camera-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .camera-modal-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
    }

    .camera-modal-title i {
        color: #2563eb;
        margin-right: 8px;
    }

    .camera-close-btn {
        background: none;
        border: none;
        font-size: 1.8rem;
        line-height: 1;
        color: #64748b;
        cursor: pointer;
        padding: 0 4px;
        transition: color 0.2s;
    }

    .camera-close-btn:hover {
        color: #0f172a;
    }

    .camera-modal-body {
        position: relative;
        min-height: 300px;
        background: #000;
    }

    .camera-view-container {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background: #0a0a0a;
    }

    .camera-view-container video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .camera-overlay-inner {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .camera-frame-guide {
        position: relative;
        width: 80%;
        height: 70%;
    }

    .frame-corner {
        position: absolute;
        width: 30px;
        height: 30px;
        border-color: rgba(255, 255, 255, 0.7);
        border-style: solid;
    }

    .frame-corner.tl { top: 0; left: 0; border-width: 3px 0 0 3px; border-radius: 8px 0 0 0; }
    .frame-corner.tr { top: 0; right: 0; border-width: 3px 3px 0 0; border-radius: 0 8px 0 0; }
    .frame-corner.bl { bottom: 0; left: 0; border-width: 0 0 3px 3px; border-radius: 0 0 0 8px; }
    .frame-corner.br { bottom: 0; right: 0; border-width: 0 3px 3px 0; border-radius: 0 0 8px 0; }

    .camera-preview-container {
        width: 100%;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background: #0a0a0a;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .camera-preview-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    .camera-loading,
    .camera-error {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        color: #fff;
        background: #0a0a0a;
    }

    .camera-error i {
        font-size: 2.5rem;
    }

    .camera-error p {
        font-size: 0.95rem;
        color: #cbd5e1;
        text-align: center;
        max-width: 80%;
        margin: 0;
    }

    .camera-modal-footer {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .camera-modal-footer .btn {
        padding: 10px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .camera-modal-footer .btn:active {
        transform: scale(0.97);
    }

    .camera-modal-footer .btn-primary {
        background: #2563eb;
        border: none;
        color: #fff;
    }

    .camera-modal-footer .btn-primary:hover {
        background: #1d4ed8;
    }

    .camera-modal-footer .btn-success {
        background: #16a34a;
        border: none;
        color: #fff;
    }

    .camera-modal-footer .btn-success:hover {
        background: #15803d;
    }

    .camera-modal-footer .btn-secondary {
        background: #e2e8f0;
        border: none;
        color: #475569;
    }

    .camera-modal-footer .btn-secondary:hover {
        background: #cbd5e1;
    }
</style>


<!-- Hidden file input for HTTP fallback camera capture -->
<input type="file" id="cameraFileInput" accept="image/*" capture="environment" style="display: none;">


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ---- Camera Capture Logic ----
        const captureAgainBtn = document.querySelector('a.book-btn-service');
        const cameraModal = document.getElementById('cameraModal');
        const closeCameraBtn = document.getElementById('closeCameraBtn');
        const cameraFeed = document.getElementById('cameraFeed');
        const captureBtn = document.getElementById('captureBtn');
        const retakeBtn = document.getElementById('retakeBtn');
        const usePhotoBtn = document.getElementById('usePhotoBtn');
        const cameraViewContainer = document.getElementById('cameraViewContainer');
        const cameraPreviewContainer = document.getElementById('cameraPreviewContainer');
        const capturedImage = document.getElementById('capturedImage');
        const cameraLoading = document.getElementById('cameraLoading');
        const cameraError = document.getElementById('cameraError');
        const cameraErrorMessage = document.getElementById('cameraErrorMessage');
        const cameraFileInput = document.getElementById('cameraFileInput');

        let mediaStream = null;
        let capturedImageDataUrl = null;
        let capturedFile = null;

        // Check if we can use WebRTC (requires HTTPS/localhost)
        const canUseWebRTC = navigator.mediaDevices && typeof navigator.mediaDevices.getUserMedia === 'function';

        // Helper: Stop all camera tracks
        function stopCameraStream() {
            if (mediaStream) {
                mediaStream.getTracks().forEach(track => track.stop());
                mediaStream = null;
            }
        }

        // Helper: read a File as data URL
        function readFileAsDataURL(file) {
            return new Promise(function(resolve, reject) {
                var reader = new FileReader();
                reader.onload = function() { resolve(reader.result); };
                reader.onerror = function() { reject(reader.error); };
                reader.readAsDataURL(file);
            });
        }

        // Open camera: try WebRTC first, fall back to native camera file input
        async function startCamera() {
            // Reset UI
            cameraViewContainer.style.display = 'block';
            cameraPreviewContainer.style.display = 'none';
            captureBtn.style.display = 'inline-flex';
            retakeBtn.style.display = 'none';
            usePhotoBtn.style.display = 'none';
            cameraLoading.style.display = 'flex';
            cameraError.style.display = 'none';
            capturedImageDataUrl = null;
            capturedFile = null;

            stopCameraStream();

            if (canUseWebRTC) {
                // ---- WebRTC mode (HTTPS) ----
                try {
                    mediaStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'environment',
                            width: { ideal: 1920 },
                            height: { ideal: 1080 }
                        },
                        audio: false
                    });
                    cameraFeed.srcObject = mediaStream;
                    cameraLoading.style.display = 'none';
                } catch (err) {
                    console.error('Camera error:', err);
                    cameraLoading.style.display = 'none';
                    cameraError.style.display = 'flex';
                    if (err.name === 'NotAllowedError') {
                        cameraErrorMessage.textContent = 'Camera access denied. Please allow camera permissions in your browser settings.';
                    } else if (err.name === 'NotFoundError') {
                        cameraErrorMessage.textContent = 'No camera found on this device. Please use the upload option instead.';
                    } else {
                        cameraErrorMessage.textContent = 'Unable to access camera: ' + err.message;
                    }
                }
            } else {
                // ---- HTTP fallback: use native camera via file input ----
                cameraLoading.style.display = 'none';
                // Hide the WebRTC video view
                cameraViewContainer.style.display = 'none';
                captureBtn.style.display = 'none';

                // Show a message that we're opening the native camera
                cameraError.style.display = 'flex';
                cameraErrorMessage.innerHTML = '<i class="fa-solid fa-camera-retro" style="font-size:3rem;display:block;margin-bottom:10px;"></i> Opening device camera...';

                // Trigger the hidden file input (opens native camera on mobile, file picker on desktop)
                cameraFileInput.click();
            }
        }

        // Handle file selected from camera/fallback input
        cameraFileInput.addEventListener('change', async function(e) {
            var file = e.target.files && e.target.files[0];
            if (!file) {
                // User cancelled - close the modal
                closeCameraModal();
                return;
            }

            capturedFile = file;

            try {
                capturedImageDataUrl = await readFileAsDataURL(file);
                // Show preview
                capturedImage.src = capturedImageDataUrl;
                cameraError.style.display = 'none';
                cameraPreviewContainer.style.display = 'flex';
                captureBtn.style.display = 'none';
                retakeBtn.style.display = 'inline-flex';
                usePhotoBtn.style.display = 'inline-flex';
            } catch (err) {
                cameraError.style.display = 'flex';
                cameraErrorMessage.textContent = 'Failed to read the captured image.';
            }

            // Reset the input so the same file can be re-selected on retake
            cameraFileInput.value = '';
        });

        // Capture photo from video feed (WebRTC mode only)
        function capturePhoto() {
            if (!mediaStream) return;

            const canvas = document.createElement('canvas');
            const video = cameraFeed;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            capturedImageDataUrl = canvas.toDataURL('image/jpeg', 0.92);

            // Create a File from the data URL
            const byteString = atob(capturedImageDataUrl.split(',')[1]);
            const mimeString = 'image/jpeg';
            const ab = new ArrayBuffer(byteString.length);
            const ia = new Uint8Array(ab);
            for (var i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }
            const blob = new Blob([ab], { type: mimeString });
            capturedFile = new File([blob], 'captured-product.jpg', { type: mimeString });

            // Show preview
            capturedImage.src = capturedImageDataUrl;
            cameraViewContainer.style.display = 'none';
            cameraPreviewContainer.style.display = 'flex';
            captureBtn.style.display = 'none';
            retakeBtn.style.display = 'inline-flex';
            usePhotoBtn.style.display = 'inline-flex';

            // Stop the camera stream while reviewing
            stopCameraStream();
        }

        // Use the captured photo (shared between WebRTC and HTTP fallback)
        function usePhoto() {
            if (!capturedImageDataUrl) return;

            // Trigger custom event with the captured file
            var event = new CustomEvent('photoCaptured', {
                detail: { file: capturedFile, dataUrl: capturedImageDataUrl }
            });
            document.dispatchEvent(event);

            // Close modal
            closeCameraModal();

            alert('Photo captured successfully! You can now use it for HSN classification.');
        }

        // Close camera modal
        function closeCameraModal() {
            stopCameraStream();
            cameraModal.style.display = 'none';
            // Reset UI for next opening
            cameraViewContainer.style.display = 'block';
            cameraPreviewContainer.style.display = 'none';
            captureBtn.style.display = 'inline-flex';
            retakeBtn.style.display = 'none';
            usePhotoBtn.style.display = 'none';
            cameraLoading.style.display = 'none';
            cameraError.style.display = 'none';
            cameraFileInput.value = '';
        }

        // ---- Event Listeners ----

        // "Capture Again" button opens the camera modal
        if (captureAgainBtn) {
            captureAgainBtn.addEventListener('click', function(e) {
                e.preventDefault();
                cameraModal.style.display = 'flex';
                startCamera();
            });
        }

        // Close modal
        if (closeCameraBtn) {
            closeCameraBtn.addEventListener('click', closeCameraModal);
        }

        // Close modal when clicking overlay background
        cameraModal.addEventListener('click', function(e) {
            if (e.target === cameraModal) {
                closeCameraModal();
            }
        });

        // Escape key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && cameraModal.style.display === 'flex') {
                closeCameraModal();
            }
        });

        // Capture button (WebRTC mode)
        if (captureBtn) {
            captureBtn.addEventListener('click', capturePhoto);
        }

        // Retake button
        if (retakeBtn) {
            retakeBtn.addEventListener('click', startCamera);
        }

        // Use photo button
        if (usePhotoBtn) {
            usePhotoBtn.addEventListener('click', usePhoto);
        }
    });
</script>


@include('website_include.footer')