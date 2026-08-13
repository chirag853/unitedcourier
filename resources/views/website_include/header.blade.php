<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(isset($blog) && $blog->seo_meta_title)
        {{-- Blog Detail SEO --}}
        <title>{{ $blog->seo_meta_title }}</title>
        @if($blog->meta_description)
            <meta name="description" content="{{ $blog->meta_description }}">
        @endif
        @if($blog->meta_keyword)
            <meta name="keywords" content="{{ $blog->meta_keyword }}">
        @endif
    @else
        {{-- Default Meta --}}
        <title>United Worldwide Couriers</title>
        <meta name="description" content="United Worldwide Couriers offers reliable international shipping, express air freight, ecommerce logistics, and warehousing solutions across the globe.">
        <meta name="keywords" content="courier, shipping, logistics, international shipping, express freight, ecommerce logistics, warehousing">
    @endif
    <!-- Favicon icons -->
    <link rel="icon" href="{{ asset('public/website_images/fav-icon.png') }}" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <!-- <link href="css/style.css" rel="stylesheet"> -->
    <link href="{{ asset('website_css/style.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- External Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <!-- Lenis for Smooth Inertial Scrolling -->
    <script src="https://unpkg.com/@studio-freight/lenis@1.0.34/dist/lenis.min.js"></script>

    <style>
        
        .menu-custom-item{
            border-radius: 73px;
            border: 1px solid #d6d6d6;
        }

        /* add hover */
        .menu-custom-item:hover {
            background: linear-gradient(to right, #2563eb, #9333ea);
            color: white !important;
            /* border-color: #2563eb; */
        }

         /* add hover */
        .btn-signup-main:hover {
            background: linear-gradient(to left, #2563eb, #9333ea);
            color: white !important;
        }
    </style>

</head>

<body>


    <!-- NAVBAR section  -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <img src="{{ asset('/website_images/logo-new.png') }}" class="img-fluid" style="max-width: 215px;">
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <!-- Service DROPDOWN -->
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}">About Us</a></li>
                    <li class="nav-item dropdown-mobile">
                        <a class="nav-link">Services <i class="fas fa-chevron-down"></i></a>
                        <div class="mega-menu">
                            <div class="menu-col-left">
                                <span class="menu-label">Our Services</span>
                                <a href="{{ url('/express-air-freight-solutions') }}" class="menu-item">
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-plane-departure"></i>
                                    </div>
                                    <div class="menu-info">
                                        <h6>Air Express Services Built Around Your Needs</h6>
                                        <p>Our Express services are designed to give customers complete flexibility.</p>
                                    </div>
                                </a>
                                <a href="{{ url('/e-commerce-logistics-solutions') }}" class="menu-item">
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-people-carry-box"></i>
                                    </div>
                                    <div class="menu-info">
                                        <h6>Ecommerce Logistics Solutions</h6>
                                        <p>Connect your marketplace account directly with our platform and ship orders
                                            with ease.</p>
                                    </div>
                                </a>
                                <a href="{{ url('/warehousing-solutions') }}" class="menu-item">
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-box"></i>
                                    </div>
                                    <div class="menu-info">
                                        <h6>Warehousing Solutions</h6>
                                        <p>Our US warehousing facility helps businesses reduce delivery timelines,
                                            manage inventory efficiently.</p>
                                    </div>
                                </a>
                            </div>
                            <div class="menu-col-right">
                                <span class="menu-label">PARTNER PROGRAM</span>
                                <a href="{{ url('/partnership') }}" class="partner-card">
                                    <img src="{{asset(asset('/website_images/partnership-menu.webp'))}}"
                                        class="partner-img" alt="Partner">
                                    <div class="menu-info">
                                        <h6>Become a Partner</h6>
                                        <p>Collaborate with United Worldwide Couriers and unlock new growth
                                            opportunities.</p>
                                    </div>
                                </a>
                                <!-- <div class="share-docs-bar">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-file-earmark-text fs-4 text-warning"></i>
                                        <div>
                                            <h6 class="mb-0" style="font-size: 14px;">Share Documents</h6>
                                            <p class="mb-0 fw-bold" style="font-size: 18px; color: #1a1a1a;">₹7,00,000</p>
                                        </div>
                                    </div>
                                    <button class="btn-signup-sm">Get Funded</button>
                                </div> -->
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown-mobile">
                        <a class="nav-link">Resources <i class="fas fa-chevron-down"></i></a>
                        <div class="mega-menu">
                            <div class="menu-col-left">
                                <span class="menu-label">Our Tools</span>
                                <a href="{{ url('/volumetric-calculator') }}" class="menu-item">
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-calculator"></i>
                                    </div>
                                    <div class="menu-info">
                                        <h6>Volumetric Calculator</h6>
                                        <p>Enter your package dimensions to instantly calculate</p>
                                    </div>
                                </a>
                                <a href="{{ url('/shipping-rate-calculator') }}" class="menu-item">
                                <!-- <a href="{{ url('/world-weather') }}" class="menu-item"> -->
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-calculator"></i>
                                    </div>
                                    <div class="menu-info">
                                        <!-- <h6>World Weather</h6> -->
                                        <h6>Shipping Rate Calculator</h6>
                                        <p>Get accurate, all‑inclusive rates in seconds.</p>
                                    </div>
                                </a>
                                <!-- <a href="{{ url('/world-time') }}" class="menu-item">
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-box"></i>
                                    </div>
                                    <div class="menu-info">
                                        <h6>World Time</h6>
                                        <p>Our US warehousing facility helps businesses reduce delivery timelines,
                                            manage inventory efficiently.</p>
                                    </div>
                                </a> -->
                                <a href="{{ url('/currency-calculator') }}" class="menu-item">
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-indian-rupee-sign"></i>
                                    </div>
                                    <div class="menu-info">
                                        <!-- <h6>Currency Calculator</h6> -->
                                        <h6>Forex Calculator</h6>
                                        <p>Convert USD, EUR, GBP, AED and other global currencies into INR instantly.</p>
                                    </div>
                                </a>


                                <!-- barcode generator -->
                                <a href="{{ url('/barcode-generator') }}" class="menu-item">
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-barcode"></i>
                                    </div>
                                    <div class="menu-info">
                                        <!-- <h6>Currency Calculator</h6> -->
                                        <h6>Barcode Generator</h6>
                                        <p>Create shipment barcodes in seconds.</p>
                                    </div>
                                </a>

                                <!-- HSN finder -->
                                <a href="{{ url('/hsn-finder') }}" class="menu-item">
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-h"></i>
                                    </div>
                                    <div class="menu-info">
                                        <!-- <h6>Currency Calculator</h6> -->
                                        <h6>HSN Finder</h6>
                                        <p>Want to know a product's Indian HSN or US HTS code in a snap?</p>
                                    </div>
                                </a>

                            </div>
                            <div class="menu-col-right">
                                <a href="{{ url('/e-books') }}" class="partner-card">
                                    <img src="{{asset(asset('/website_images/e-book.webp'))}}"
                                        class="partner-img" alt="Partner">
                                    <div class="menu-info">
                                        <h6>E-Books</h6>
                                        <p>Collaborate with United Worldwide Couriers and unlock new growth
                                            opportunities.</p>
                                    </div>
                                </a>
                                <div class="share-docs-bar">
                                    <button class="btn-signup-sm"><a href="{{url('/webinar')}}" style="text-decoration: none; color: inherit;"> Webinar</a></button>
                                    <button class="btn-signup-sm"><a href="{{url('/document-download')}}" style="text-decoration: none; color: inherit;">Document Download</a></button>
                                </div>

                            </div>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/partnership') }}">Partnership</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="{{ url('/tracking') }}">Tracking</a></li> -->
                    <!-- <li class="nav-item"><a class="nav-link" href="{{ url('/network') }}">Networking</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/blogs') }}">Blog</a></li> -->
                </ul>
                <div class="d-flex flex-column flex-lg-row align-items-center gap-3">
                    <a class="nav-link menu-custom-item" href="{{ url('/tracking') }}">Tracking</a>
                    <a href="{{ url('/login') }}" class="menu-custom-item nav-link">Login</a>
                    <a href="{{ url('/get-started') }}" class="btn-signup-main w-100 w-lg-auto text-decoration-none">Get Started</a>
                </div>
            </div>
        </div>
    </nav>
