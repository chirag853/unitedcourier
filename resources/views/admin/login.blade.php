<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Sign In | United Worldwide Couriers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" id="app-style">

    <style>
        :root {
            --uwc-navy: #0a2a5e;
            --uwc-blue: #0b5cd6;
            --uwc-sky: #38bdf8;
            --uwc-amber: #fbbf24;
        }

        /* ================= Entrance ================= */
        @keyframes uwcRise {
            from { opacity: 0; transform: translateY(28px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes uwcPop {
            0% { opacity: 0; transform: scale(.85); }
            100% { opacity: 1; transform: scale(1); }
        }
        .uwc-anim { opacity: 0; animation: uwcRise .7s cubic-bezier(.22, .8, .32, 1) forwards; }
        .uwc-d1 { animation-delay: .05s; }
        .uwc-d2 { animation-delay: .15s; }
        .uwc-d3 { animation-delay: .25s; }
        .uwc-d4 { animation-delay: .35s; }
        .uwc-d5 { animation-delay: .45s; }
        .uwc-d6 { animation-delay: .6s; }
        .uwc-d7 { animation-delay: .75s; }
        .uwc-d8 { animation-delay: .9s; }

        /* ================= Left : premium form side ================= */
        .uwc-form-side {
            position: relative;
            background:
                radial-gradient(560px 340px at 12% 8%, rgba(11, 92, 214, .10), transparent 65%),
                radial-gradient(520px 380px at 95% 95%, rgba(56, 189, 248, .14), transparent 60%),
                linear-gradient(180deg, #f7faff 0%, #eef3fd 100%);
            overflow: hidden;
        }
        .uwc-form-side::before,
        .uwc-form-side::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            border: 26px solid rgba(11, 92, 214, .06);
        }
        .uwc-form-side::before { width: 260px; height: 260px; top: -90px; right: -90px; }
        .uwc-form-side::after { width: 200px; height: 200px; bottom: -70px; left: -70px; border-color: rgba(56, 189, 248, .10); }

        .auth-logo img {
            max-height: 132px;
            width: auto;
            filter: drop-shadow(0 10px 22px rgba(10, 42, 94, .18));
            animation: uwcPop .7s cubic-bezier(.22, .8, .32, 1) both;
        }
        .uwc-login-card {
            background: #fff;
            border: 1px solid rgba(11, 92, 214, .12);
            border-radius: 22px;
            box-shadow: 0 30px 60px -18px rgba(10, 42, 94, .25);
            padding: 30px 28px;
            position: relative;
            overflow: hidden;
        }
        .uwc-login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--uwc-blue), var(--uwc-sky), var(--uwc-amber), var(--uwc-blue));
            background-size: 300% 100%;
            animation: uwcBarSlide 6s linear infinite;
        }
        @keyframes uwcBarSlide {
            to { background-position: 300% 0; }
        }
        .uwc-login-card .form-control {
            border-radius: 12px;
            padding: 11px 14px;
        }
        .uwc-login-card .form-control:focus {
            border-color: var(--uwc-blue);
            box-shadow: 0 0 0 .25rem rgba(11, 92, 214, .15);
        }
        .uwc-login-card .input-group-text {
            border-radius: 0 12px 12px 0;
        }
        .uwc-signin-btn {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            letter-spacing: .02em;
            transition: transform .15s ease, box-shadow .2s ease;
        }
        .uwc-signin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(11, 92, 214, .4);
        }
        .uwc-signin-btn::after {
            content: "";
            position: absolute;
            top: 0;
            left: -70%;
            width: 45%;
            height: 100%;
            background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .45), transparent);
            transform: skewX(-20deg);
            animation: uwcShine 3.2s ease-in-out infinite;
        }
        @keyframes uwcShine {
            0%, 55% { left: -70%; }
            100% { left: 140%; }
        }
        .uwc-mini-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 999px;
            background: rgba(11, 92, 214, .08);
            color: var(--uwc-blue);
            border: 1px solid rgba(11, 92, 214, .16);
        }

        /* ================= Right : animated scene ================= */
        .uwc-scene {
            position: relative;
            overflow: hidden;
            color: #fff;
            background:
                radial-gradient(900px 480px at 85% -10%, rgba(56, 189, 248, .35), transparent 60%),
                radial-gradient(700px 500px at 10% 110%, rgba(251, 191, 36, .18), transparent 60%),
                linear-gradient(150deg, #071f4a 0%, #0a3a8f 52%, #0b5cd6 100%);
        }
        .uwc-scene::before {
            content: "";
            position: absolute;
            inset: -60px;
            background-image: radial-gradient(rgba(255, 255, 255, .22) 1.2px, transparent 1.3px);
            background-size: 26px 26px;
            animation: uwcDrift 26s linear infinite;
            opacity: .5;
        }
        @keyframes uwcDrift {
            from { transform: translate3d(0, 0, 0); }
            to { transform: translate3d(-52px, 26px, 0); }
        }
        .uwc-glow {
            position: absolute;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            filter: blur(90px);
            opacity: .5;
            animation: uwcGlowFloat 9s ease-in-out infinite alternate;
        }
        .uwc-glow.g1 { background: #38bdf8; top: -160px; right: -120px; }
        .uwc-glow.g2 { background: #1e40af; bottom: -200px; left: -140px; animation-delay: -4s; }
        @keyframes uwcGlowFloat {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(-40px, 40px) scale(1.12); }
        }

        /* route map */
        .uwc-route-dash {
            stroke-dasharray: 7 9;
            animation: uwcDashMove 1.4s linear infinite;
        }
        @keyframes uwcDashMove {
            to { stroke-dashoffset: -32; }
        }
        .uwc-hub { transform-box: fill-box; transform-origin: center; animation: uwcPing 2.2s ease-out infinite; }
        .uwc-hub.h2 { animation-delay: .7s; }
        .uwc-hub.h3 { animation-delay: 1.4s; }
        @keyframes uwcPing {
            0% { opacity: .9; }
            70% { opacity: .25; }
            100% { opacity: .9; }
        }

        /* floating stat chips */
        @keyframes uwcFloat {
            0%, 100% { transform: translateY(0) rotate(var(--tilt, 0deg)); }
            50% { transform: translateY(-12px) rotate(var(--tilt, 0deg)); }
        }
        .uwc-chip-float {
            animation: uwcFloat 5s ease-in-out infinite;
            box-shadow: 0 18px 40px rgba(2, 12, 35, .35);
        }
        .uwc-chip-float.f2 { animation-delay: -1.6s; --tilt: 1.5deg; }
        .uwc-chip-float.f3 { animation-delay: -3.2s; --tilt: -1.5deg; }

        /* live feed */
        .uwc-feed-item { animation: uwcFeedIn .5s ease both; }
        @keyframes uwcFeedIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .uwc-live-dot { animation: uwcBlink 1.6s ease-in-out infinite; }
        @keyframes uwcBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: .3; }
        }

        /* lane marquee */
        .uwc-marquee { overflow: hidden; white-space: nowrap; }
        .uwc-marquee-track {
            display: inline-flex;
            gap: 12px;
            padding-right: 12px;
            animation: uwcMarquee 22s linear infinite;
        }
        @keyframes uwcMarquee {
            to { transform: translateX(-50%); }
        }
        .uwc-lane {
            border: 1px solid rgba(255, 255, 255, .22);
            background: rgba(255, 255, 255, .1);
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 12.5px;
        }

        .uwc-glass {
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .18);
            backdrop-filter: blur(6px);
            border-radius: 16px;
        }

        /* ---------- Road + driving trucks ---------- */
        .uwc-road {
            position: relative;
            height: 92px;
            border-radius: 16px;
            background: rgba(4, 18, 44, .55);
            border: 1px solid rgba(255, 255, 255, .16);
            overflow: hidden;
        }
        .uwc-road::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            height: 4px;
            margin-top: -2px;
            background-image: linear-gradient(90deg, rgba(255,255,255,.75) 0 26px, transparent 26px 52px);
            background-size: 52px 4px;
            animation: uwcRoadMove 1s linear infinite;
        }
        @keyframes uwcRoadMove {
            to { background-position-x: -52px; }
        }
        .uwc-truck {
            position: absolute;
            top: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: uwcDrive 9s linear infinite;
        }
        .uwc-truck.t2 { animation-duration: 14s; animation-delay: -7s; top: 46px; opacity: .92; }
        @keyframes uwcDrive {
            from { left: -160px; }
            to { left: 105%; }
        }
        .uwc-truck-body {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 12px;
            padding: 6px 12px 6px 6px;
            animation: uwcBump .5s ease-in-out infinite alternate;
        }
        @keyframes uwcBump {
            from { transform: translateY(0); }
            to { transform: translateY(-2px); }
        }
        .uwc-truck-body i { font-size: 30px; }
        .uwc-wheels { display: flex; gap: 6px; margin-top: 2px; }
        .uwc-wheel {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, .85);
            background: #0a2a5e;
            animation: uwcSpin .6s linear infinite;
        }
        @keyframes uwcSpin {
            to { transform: rotate(360deg); }
        }

        /* ---------- Floating parcels ---------- */
        @keyframes uwcParcelFloat {
            0%, 100% { transform: translateY(0) rotate(-6deg); }
            50% { transform: translateY(-16px) rotate(6deg); }
        }
        .uwc-parcel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .25);
            backdrop-filter: blur(4px);
            box-shadow: 0 14px 30px rgba(2, 12, 35, .35);
            animation: uwcParcelFloat 4.5s ease-in-out infinite;
            z-index: 2;
        }
        .uwc-parcel i { font-size: 26px; }
        .uwc-parcel.p1 { top: 7%; right: 6%; }
        .uwc-parcel.p2 { top: 24%; right: 15%; animation-delay: -1.5s; width: 44px; height: 44px; }
        .uwc-parcel.p3 { bottom: 26%; right: 5%; animation-delay: -3s; width: 40px; height: 40px; }

        /* ---------- Enhanced aeroplane marker ---------- */
        .uwc-plane-halo {
            transform-box: fill-box;
            transform-origin: center;
            animation: uwcPlaneHalo 2s ease-out infinite;
        }
        @keyframes uwcPlaneHalo {
            0% { opacity: .7; transform: scale(.6); }
            70% { opacity: 0; transform: scale(1.6); }
            100% { opacity: 0; transform: scale(1.6); }
        }

        /* ---------- Shipment journey timeline ---------- */
        .uwc-journey { position: relative; }
        .uwc-jstep {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 0;
            opacity: .45;
            transition: opacity .4s ease;
        }
        .uwc-jstep .uwc-jdot {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .25);
            font-size: 16px;
            flex-shrink: 0;
            transition: all .4s ease;
        }
        .uwc-jstep.uwc-active { opacity: 1; }
        .uwc-jstep.uwc-active .uwc-jdot {
            background: var(--uwc-amber);
            border-color: var(--uwc-amber);
            color: #0a2a5e;
            box-shadow: 0 0 0 6px rgba(251, 191, 36, .22), 0 0 22px rgba(251, 191, 36, .55);
        }
        .uwc-jstep.uwc-done { opacity: .95; }
        .uwc-jstep.uwc-done .uwc-jdot {
            background: #34d399;
            border-color: #34d399;
            color: #06281c;
        }
        .uwc-jbar {
            height: 5px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .15);
            overflow: hidden;
        }
        .uwc-jfill {
            height: 100%;
            width: 0%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--uwc-sky), var(--uwc-amber));
            transition: width .8s cubic-bezier(.22, .8, .32, 1);
        }

        /* parallax layers */
        .uwc-px { transition: transform .25s ease-out; will-change: transform; }

        @media (prefers-reduced-motion: reduce) {
            .uwc-anim, .uwc-chip-float, .uwc-marquee-track,
            .uwc-scene::before, .uwc-glow, .uwc-route-dash, .uwc-hub,
            .uwc-signin-btn::after, .uwc-truck, .uwc-truck-body,
            .uwc-wheel, .uwc-parcel, .uwc-road::before,
            .uwc-plane-halo,
            .uwc-login-card::before, .auth-logo img { animation: none !important; }
            .uwc-anim { opacity: 1; }
        }
    </style>

</head>

<body class="account-page bg-white">

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        <div class="overflow-hidden p-3 acc-vh">

            <!-- start row -->
            <div class="row vh-100 w-100 g-0">

                <!-- LEFT : form -->
                <div class="col-lg-5 vh-100 overflow-y-auto overflow-x-hidden uwc-form-side">

                    <!-- start row -->
                    <div class="row position-relative" style="z-index:1;">

                        <div class="col-md-10 mx-auto">
                            <form action="{{ route('admin.login.post') }}" method="POST"
                                class="vh-100 d-flex justify-content-between flex-column p-4 pb-0">
                            @csrf
                                <div class="text-center mb-3 auth-logo uwc-anim uwc-d1">
                                    <img src="{{ asset('assets/img/logo.svg') }}" style="max-width:70%" class="img-fluid" alt="United Worldwide Couriers">
                                </div>
                                <div class="uwc-login-card uwc-anim uwc-d2">
                                    <div class="mb-1">
                                        <span class="uwc-mini-badge mb-2">
                                            <i class="ti ti-shield-lock"></i>Admin Panel
                                        </span>
                                        <h3 class="mb-2">Welcome back</h3>
                                        <p class="mb-0 text-muted">Sign in to run shipments, rates, zones and customers for United Worldwide Couriers.</p>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger mt-3">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(session('success'))
                                        <div class="alert alert-success mt-3">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    @if(session('error'))
                                        <div class="alert alert-danger mt-3">
                                            {{ session('error') }}
                                        </div>
                                    @endif
                                    <div class="mb-3 mt-3 uwc-anim uwc-d3">
                                        <label class="form-label">Email Address</label>
                                        <div class="input-group input-group-flat">
                                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required>
                                            <span class="input-group-text">
                                                <i class="ti ti-mail"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-2 uwc-anim uwc-d4">
                                        <label class="form-label">Password</label>
                                        <div class="input-group input-group-flat pass-group">
                                            <input type="password" class="form-control pass-input" name="password" placeholder="Enter your password" required>
                                            <span class="input-group-text toggle-password">
                                                <i class="ti ti-eye-off"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-1 mb-3 uwc-anim uwc-d5">
                                        <i class="ti ti-lock text-muted"></i>
                                        <small class="text-muted">Restricted access — authorized staff only.</small>
                                    </div>

                                    <div class="mb-2 uwc-anim uwc-d5">
                                        <button type="submit" class="btn btn-primary w-100 uwc-signin-btn">
                                            Sign In<i class="ti ti-arrow-right ms-1"></i>
                                        </button>
                                    </div>

                                    <!-- <div class="d-flex justify-content-center gap-4 mt-3 uwc-anim uwc-d6">
                                        <small class="text-muted d-flex align-items-center gap-1"><i class="ti ti-package text-primary"></i>Express air freight solutions</small>
                                        <small class="text-muted d-flex align-items-center gap-1"><i class="ti ti-plane text-primary"></i>E-commerce logistics solutions</small>
                                        <small class="text-muted d-flex align-items-center gap-1"><i class="ti ti-warehouse text-primary"></i>Warehousing Solutions</small>
                                    </div> -->

                                </div>
                                <div class="text-center py-4 uwc-anim uwc-d7">
                                    <p class="text-dark mb-0">Copyright &copy; <span id="uwcYear"></span> - United Worldwide Couriers</p>
                                </div>
                            </form>
                        </div> <!-- end col -->

                    </div>
                    <!-- end row -->

                </div>

                <!-- RIGHT : animated courier scene -->
                <div class="col-lg-7 d-none d-lg-flex flex-column p-4 uwc-scene vh-100 overflow-y-auto overflow-x-hidden" id="uwcScene"><!-- end col -->
                    <div class="uwc-glow g1"></div>
                    <div class="uwc-glow g2"></div>
                    <div class="uwc-parcel p1 uwc-px" data-depth="26"><i class="ti ti-package text-warning"></i></div>
                    <div class="uwc-parcel p2 uwc-px" data-depth="18"><i class="ti ti-box text-info"></i></div>
                    <div class="uwc-parcel p3 uwc-px" data-depth="34"><i class="ti ti-package-import text-success"></i></div>

                    <div class="position-relative uwc-px my-auto" data-depth="8" style="z-index:1; max-width: 600px; width: 100%; margin: 0 auto;">
                        <div class="uwc-anim uwc-d1">
                            <span class="badge bg-light text-primary mb-3">
                                <span class="uwc-live-dot d-inline-block rounded-circle bg-success me-1" style="width:8px;height:8px;"></span>
                                Live network
                            </span>
                            <h2 class="mb-2 text-white">Every parcel, every mile — under control.</h2>
                            <p class="mb-3 text-white-50">Pickup to delivery, rates to wallets — the whole courier operation, live in one admin tower.</p>
                        </div>

                        <!-- animated route map -->
                        <div class="uwc-glass p-3 mb-2 uwc-anim uwc-d2">
                            <svg viewBox="0 0 560 200" class="w-100" style="height:150px;" role="img" aria-label="Animated delivery route">
                                <defs>
                                    <filter id="uwcPlaneGlow" x="-60%" y="-60%" width="220%" height="220%">
                                        <feGaussianBlur stdDeviation="4" result="b"/>
                                        <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
                                    </filter>
                                </defs>
                                <path d="M30 160 C 140 170, 170 60, 280 80 S 450 150, 530 60" fill="none" stroke="rgba(255,255,255,.35)" stroke-width="2" class="uwc-route-dash"/>
                                <circle cx="30" cy="160" r="7" fill="#fbbf24" class="uwc-hub"/>
                                <circle cx="30" cy="160" r="3" fill="#fff"/>
                                <circle cx="280" cy="80" r="7" fill="#38bdf8" class="uwc-hub h2"/>
                                <circle cx="280" cy="80" r="3" fill="#fff"/>
                                <circle cx="530" cy="60" r="7" fill="#34d399" class="uwc-hub h3"/>
                                <circle cx="530" cy="60" r="3" fill="#fff"/>
                                <text x="18" y="185" fill="rgba(255,255,255,.75)" font-size="12">DEL</text>
                                <text x="268" y="105" fill="rgba(255,255,255,.75)" font-size="12">DXB</text>
                                <text x="516" y="85" fill="rgba(255,255,255,.75)" font-size="12">LHR</text>
                                <g filter="url(#uwcPlaneGlow)">
                                    <circle r="17" fill="rgba(251,191,36,.4)" class="uwc-plane-halo"/>
                                    <circle r="17" fill="#ffffff"/>
                                    <circle r="17" fill="none" stroke="#0b5cd6" stroke-width="3"/>
                                    <circle r="21" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="1.5" stroke-dasharray="4 4"/>
                                    <text text-anchor="middle" dy="8" font-size="22" font-weight="bold" fill="#0a2a5e">&#9992;&#65038;</text>
                                    <animateMotion dur="6s" repeatCount="indefinite" rotate="auto"
                                        path="M30 160 C 140 170, 170 60, 280 80 S 450 150, 530 60"/>
                                </g>
                            </svg>
                            <div class="uwc-marquee mt-1">
                                <div class="uwc-marquee-track" id="uwcLanes"></div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-4 uwc-anim uwc-d3">
                                <div class="uwc-glass uwc-chip-float text-center p-2">
                                    <strong class="d-block fs-5"><span class="uwc-count" data-count="250">0</span>+</strong>
                                    <small class="text-white-50">Countries</small>
                                </div>
                            </div>
                            <div class="col-4 uwc-anim uwc-d4">
                                <div class="uwc-glass uwc-chip-float f2 text-center p-2">
                                    <strong class="d-block fs-5"><span class="uwc-count" data-count="100">0</span>K+</strong>
                                    <small class="text-white-50">Parcels / day</small>
                                </div>
                            </div>
                            <div class="col-4 uwc-anim uwc-d5">
                                <div class="uwc-glass uwc-chip-float f3 text-center p-2">
                                    <strong class="d-block fs-5"><span class="uwc-count" data-count="99">0</span>%</strong>
                                    <small class="text-white-50">On-time</small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <!-- shipment journey timeline -->
                            <div class="col-6 uwc-anim uwc-d7">
                                <div class="uwc-glass p-3 h-100">
                                    <small class="fw-bold text-uppercase d-block mb-1" style="letter-spacing:.08em;">Shipment journey</small>
                                    <div class="uwc-jbar mb-2"><div class="uwc-jfill" id="uwcJourneyFill"></div></div>
                                    <div class="uwc-journey" id="uwcJourney">
                                        <div class="uwc-jstep" data-step="0">
                                            <span class="uwc-jdot"><i class="ti ti-package"></i></span>
                                            <small><strong>Picked up</strong><br><span class="text-white-50">AWB scanned</span></small>
                                        </div>
                                        <div class="uwc-jstep" data-step="1">
                                            <span class="uwc-jdot"><i class="ti ti-plane-departure"></i></span>
                                            <small><strong>In transit</strong><br><span class="text-white-50">Linehaul moving</span></small>
                                        </div>
                                        <div class="uwc-jstep" data-step="2">
                                            <span class="uwc-jdot"><i class="ti ti-truck-delivery"></i></span>
                                            <small><strong>Out for delivery</strong><br><span class="text-white-50">Rider assigned</span></small>
                                        </div>
                                        <div class="uwc-jstep" data-step="3">
                                            <span class="uwc-jdot"><i class="ti ti-circle-check"></i></span>
                                            <small><strong>Delivered</strong><br><span class="text-white-50">Signed receipt</span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- live activity feed -->
                            <div class="col-6 uwc-anim uwc-d8">
                                <div class="uwc-glass p-3 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="uwc-live-dot d-inline-block rounded-circle bg-success" style="width:8px;height:8px;"></span>
                                        <small class="fw-bold text-uppercase" style="letter-spacing:.08em;">Live activity</small>
                                    </div>
                                    <div id="uwcFeed" class="d-flex flex-column gap-2" style="min-height: 104px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- end row -->

        </div>

    </div>
    <!-- End Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        (function () {
            document.getElementById('uwcYear').textContent = new Date().getFullYear();

            // Service lanes marquee (duplicated for a seamless loop)
            var lanes = ['DEL \u2192 DXB', 'DXB \u2192 LHR', 'BOM \u2192 SIN', 'SIN \u2192 SYD', 'DEL \u2192 JFK', 'DXB \u2192 FRA', 'LHR \u2192 YYZ', 'BOM \u2192 NBO'];
            var track = document.getElementById('uwcLanes');
            if (track) {
                var html = lanes.map(function (l) { return '<span class="uwc-lane">\u2708\uFE0E ' + l + '</span>'; }).join('');
                track.innerHTML = html + html;
            }

            // Animated counters
            function animateCount(el) {
                var target = parseInt(el.getAttribute('data-count'), 10) || 0;
                var start = null, dur = 1400;
                function step(ts) {
                    if (!start) start = ts;
                    var p = Math.min((ts - start) / dur, 1);
                    el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }
            document.querySelectorAll('.uwc-count').forEach(animateCount);

            // Rotating live activity feed
            var feedEvents = [
                ['ti-plane-departure', 'AWB 884102 \u00b7 Delhi \u2192 Dubai \u00b7 departed hub'],
                ['ti-package', 'AWB 884103 \u00b7 2.4 kg parcel scanned at Mumbai gateway'],
                ['ti-truck-delivery', 'AWB 884104 \u00b7 out for delivery in London'],
                ['ti-circle-check', 'AWB 884105 \u00b7 delivered \u00b7 signed by consignee'],
                ['ti-file-spreadsheet', 'Rate card v42 published for 12 countries'],
                ['ti-wallet', 'Wallet topped up \u00b7 exporter account credited']
            ];
            var feedIcons = { 0: 'text-info', 1: 'text-warning', 2: 'text-primary', 3: 'text-success', 4: 'text-info', 5: 'text-warning' };
            var feed = document.getElementById('uwcFeed');
            var fi = 0;
            function pushFeed() {
                if (!feed) return;
                var item = feedEvents[fi % feedEvents.length];
                var div = document.createElement('div');
                div.className = 'uwc-feed-item d-flex align-items-center gap-2 small';
                div.innerHTML = '<i class="ti ' + item[0] + ' ' + (feedIcons[fi % feedEvents.length] || '') + '"></i><span>' + item[1] + '</span><span class="ms-auto text-white-50">now</span>';
                feed.prepend(div);
                while (feed.children.length > 3) feed.removeChild(feed.lastChild);
                fi++;
            }
            pushFeed(); pushFeed();
            setInterval(pushFeed, 3200);

            // Shipment journey — cycles Pickup > Transit > Out for delivery > Delivered
            var steps = Array.prototype.slice.call(document.querySelectorAll('#uwcJourney .uwc-jstep'));
            var fill = document.getElementById('uwcJourneyFill');
            var ji = 0;
            function renderJourney() {
                var pos = ji % (steps.length + 1);
                steps.forEach(function (s, idx) {
                    if (pos === steps.length) {
                        s.classList.add('uwc-done');
                    } else if (idx < pos) {
                        s.classList.add('uwc-done');
                    } else if (idx === pos) {
                        s.classList.add('uwc-active');
                    }
                });
                if (fill) fill.style.width = (pos / steps.length * 100) + '%';
                ji++;
            }
            if (steps.length) {
                renderJourney();
                setInterval(renderJourney, 2200);
            }

            // Mouse parallax on the scene layers
            var scene = document.getElementById('uwcScene');
            if (scene && window.matchMedia('(prefers-reduced-motion: no-preference)').matches) {
                var layers = Array.prototype.slice.call(scene.querySelectorAll('.uwc-px'));
                scene.addEventListener('mousemove', function (e) {
                    var r = scene.getBoundingClientRect();
                    var x = (e.clientX - r.left) / r.width - 0.5;
                    var y = (e.clientY - r.top) / r.height - 0.5;
                    layers.forEach(function (l) {
                        var d = parseFloat(l.getAttribute('data-depth')) || 10;
                        l.style.transform = 'translate(' + (-x * d) + 'px,' + (-y * d) + 'px)';
                    });
                });
                scene.addEventListener('mouseleave', function () {
                    layers.forEach(function (l) { l.style.transform = ''; });
                });
            }
        })();
    </script>
</body>

</html>
