<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>United Worldwide Couriers</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/customer-style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- External Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <!-- Lenis for Smooth Inertial Scrolling -->
    <script src="https://unpkg.com/@studio-freight/lenis@1.0.34/dist/lenis.min.js"></script>

</head>

<body>


<!-- NAVBAR section  -->
<nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
               <img src="{{ asset('assets/images/logo-new.png') }}" class="img-fluid" style="max-width: 215px;">
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <!-- Service DROPDOWN -->
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item dropdown-mobile">
                        <a class="nav-link" href="javascript:void(0)">Services <i class="fas fa-chevron-down"></i></a>
                        <div class="mega-menu">
                            <div class="menu-col-left">
                                <span class="menu-label">INTEGRATIONS</span>
                                <a href="#" class="menu-item">
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-door-open"></i>
                                    </div>
                                    <div class="menu-info">
                                        <h6>Door-to-Door Delivery</h6>
                                        <p>United Courier ensures seamless pickup from your doorstep and delivery directly to your international customer.</p>
                                    </div>
                                </a>
                                <a href="#" class="menu-item">
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-people-carry-box"></i>
                                    </div>
                                    <div class="menu-info">
                                        <h6>Supply Chain for eCommerce</h6>
                                        <p>We will handle your shipping while you focus on growing your business.</p>
                                    </div>
                                </a>
                                <a href="#" class="menu-item">
                                    <div class="menu-icon-box" style="background: #5f91ff1f; color: #2563eb;">
                                        <i class="fa-solid fa-clipboard-list"></i>
                                    </div>
                                    <div class="menu-info">
                                        <h6>Customs Clearance Support</h6>
                                        <p>Get your export documentation right the first time. Our experts guide you through every requirement.</p>
                                    </div>
                                </a>
                            </div>
                            <div class="menu-col-right">
                                <span class="menu-label">PARTNER PROGRAM</span>
                                <a href="#" class="partner-card">
                                    <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&q=80&w=200" class="partner-img" alt="Partner">
                                    <div class="menu-info">
                                        <h6>Become a Partner</h6>
                                        <p>Collaborate with United Worldwide Couriers and unlock new growth opportunities.</p>
                                    </div>
                                </a>
                                <div class="share-docs-bar">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-file-earmark-text fs-4 text-warning"></i>
                                        <div>
                                            <h6 class="mb-0" style="font-size: 14px;">Share Documents</h6>
                                            <p class="mb-0 fw-bold" style="font-size: 18px; color: #1a1a1a;">₹7,00,000</p>
                                        </div>
                                    </div>
                                    <button class="btn-signup-sm">Get Funded</button>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/platform') }}">Platform</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/tracking') }}">Tracking</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/networking') }}">Networking</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/contact') }}">Contact</a></li>
                </ul>
                <div class="d-flex flex-column flex-lg-row align-items-center gap-3">
                    <a href="{{ url('/customer/') }}" class="text-decoration-none fw-bold text-dark" style="font-size: 15px;">Login</a>
                    <a href="{{ url('/customer/register') }}" class="btn-signup-main w-100 w-lg-auto text-decoration-none">Sign Up for Free</a>
                </div>
            </div>
        </div>
</nav>
