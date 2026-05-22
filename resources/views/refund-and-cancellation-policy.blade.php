@include('website_include.header')

<!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>


    <style>
        :root {
            --uwd-rc-primary: #2563eb;
            --uwd-rc-secondary: #9333ea;
            --uwd-rc-bg-light: #f8fafc;
            --uwd-rc-card-bg: rgba(255, 255, 255, 0.8);
            --uwd-rc-glass-border: rgba(255, 255, 255, 0.5);
        }

        #uwd-rc-wrapper {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--uwd-rc-bg-light);
            color: #1e293b;
            line-height: 1.7;
            position: relative;
            padding-top: 60px;
            min-height: 100vh;
        }

        /* Animated Background Blobs */
        .uwd-rc-blob {
            position: fixed;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(147, 51, 234, 0.08) 100%);
            filter: blur(100px);
            border-radius: 50%;
            z-index: 1;
            animation: uwdRcFloat 25s infinite alternate;
        }
        .uwd-rc-blob-1 { top: -15%; left: -10%; }
        .uwd-rc-blob-2 { bottom: -15%; right: -10%; animation-delay: -7s; }

        @keyframes uwdRcFloat {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.1); }
        }

        .uwd-rc-last-updated {
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 20px;
            display: block;
        }

        /* Content Container */
        .uwd-rc-glass-panel {
            background: var(--uwd-rc-card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--uwd-rc-glass-border);
            border-radius: 32px;
            padding: 60px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            margin-bottom: 80px;
            animation: uwdRcFadeInUp 0.8s ease-out;
            position: relative;
            z-index: 2;
        }

        @keyframes uwdRcFadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .uwd-rc-section {
            margin-bottom: 48px;
            scroll-margin-top: 100px;
        }

        .uwd-rc-section h2 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #0f172a;
        }

        .uwd-rc-section h2 i {
            color: var(--uwd-rc-primary);
            width: 28px;
            height: 28px;
        }

        .uwd-rc-section p {
            color: #475569;
            font-size: 1.05rem;
            margin-bottom: 16px;
        }

        .uwd-rc-list-group {
            list-style: none;
            padding-left: 0;
        }

        .uwd-rc-list-group li {
            position: relative;
            padding-left: 32px;
            margin-bottom: 12px;
            color: #475569;
        }

        .uwd-rc-list-group li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--uwd-rc-primary);
            font-weight: bold;
        }

        /* Navigation Sidebar */
        .uwd-rc-sidebar-col {
            position: relative;
        }

        .uwd-rc-sticky-nav {
            position: -webkit-sticky;
            position: sticky;
            top: 100px;
            z-index: 10;
        }

        .uwd-rc-nav-link {
            display: block;
            padding: 12px 20px;
            color: #64748b;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            margin-bottom: 8px;
            font-weight: 500;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
        }

        .uwd-rc-nav-link:hover {
            background: rgba(37, 99, 235, 0.05);
            color: var(--uwd-rc-primary);
        }

        .uwd-rc-nav-link.uwd-rc-active {
            background: white;
            color: var(--uwd-rc-primary);
            border-color: var(--uwd-rc-glass-border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            font-weight: 700;
        }

        /* Footer CTA */
        .uwd-rc-footer-cta {
            background: linear-gradient(135deg, var(--uwd-rc-primary), var(--uwd-rc-secondary));
            border-radius: 24px;
            padding: 40px;
            color: white;
            text-align: center;
            margin-top: 40px;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .uwd-rc-glass-panel { padding: 30px; border-radius: 24px; }
            .uwd-rc-sidebar-col { display: none; }
        }

    </style>



<!-- Hero section -->
<header style="min-height: 0vh;" class="hero-gradient">
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
    </div>
    <div class="floating-blob bg-primary opacity-10"
        style="width: 200px; height: 200px; bottom: 10%; right: -100px;"></div>

    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <!-- Content -->
            <div class="col-md-12 text-center animate__animated animate__fadeInLeft">
                @if($pageMeta)
                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    Effective Date: {{ $pageMeta->effective_date ?? 'October 2025' }}
                </div>
                @endif
                <h1 class="hero-title mb-4">
                    Refund & Cancellation <span class="moving-gradient-text"> Policy</span>
                </h1>
                <p style="max-width: 100%;" class="text-center mb-5 lead">
                    {{ $pageMeta->paragraphs ?? 'Your privacy is our priority. This policy outlines how United Worldwide Couriers collects, uses, and protects your information.' }}
                </p>
            </div>

        </div>
    </div>
</header>


<div id="uwd-rc-wrapper">
    <div class="uwd-rc-blob uwd-rc-blob-1"></div>
    <div class="uwd-rc-blob uwd-rc-blob-2"></div>

    <div class="container">
        <div class="row g-5">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 uwd-rc-sidebar-col">
                <div class="uwd-rc-sticky-nav">
                    <nav id="uwd-rc-nav-list">
                        <a href="#uwd-cancel" class="uwd-rc-nav-link uwd-rc-active">{{ $cancellationPolicy->title ?? 'Cancellation Policy' }}</a>
                        <a href="#uwd-eligibility" class="uwd-rc-nav-link">{{ $refundEligibility->title ?? 'Refund Eligibility' }}</a>
                        <a href="#uwd-process" class="uwd-rc-nav-link">{{ $refundProcess->title ?? 'Refund Process' }}</a>
                        <a href="#uwd-nonrefundable" class="uwd-rc-nav-link">{{ $nonRefundableItems->title ?? 'Non-Refundable Items' }}</a>
                        <a href="#uwd-delays" class="uwd-rc-nav-link">{{ $serviceDelays->title ?? 'Service Delays' }}</a>
                        <a href="#uwd-contact" class="uwd-rc-nav-link">Contact Support</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="uwd-rc-glass-panel">
                    <span class="uwd-rc-last-updated">Effective Date: {{ $pageMeta->effective_date ?? 'October 2023' }}</span>
                    
                    @if($cancellationPolicy)
                    <section id="uwd-cancel" class="uwd-rc-section">
                        <h2><i data-lucide="x-circle"></i> {{ $cancellationPolicy->title ?? 'Cancellation Policy' }}</h2>
                        <p>{!! $cancellationPolicy->paragraphs ?? 'At United Worldwide Couriers, we understand that plans change. Our cancellation rules are as follows:' !!}</p>
                    </section>
                    @endif

                    @if($refundEligibility)
                    <section id="uwd-eligibility" class="uwd-rc-section">
                        <h2><i data-lucide="check-circle-2"></i> {{ $refundEligibility->title ?? 'Refund Eligibility' }}</h2>
                        <p>{!! $refundEligibility->paragraphs ?? 'Refunds are evaluated on a case-by-case basis. You may be eligible for a refund if:' !!}</p>
                    </section>
                    @endif

                    @if($refundProcess)
                    <section id="uwd-process" class="uwd-rc-section">
                        <h2><i data-lucide="refresh-ccw"></i> {{ $refundProcess->title ?? 'Refund Process' }}</h2>
                        <p>{!! $refundProcess->paragraphs ?? 'To request a refund, please follow these steps:' !!}</p>
                    </section>
                    @endif

                    @if($nonRefundableItems)
                    <section id="uwd-nonrefundable" class="uwd-rc-section">
                        <h2><i data-lucide="shield-alert"></i> {{ $nonRefundableItems->title ?? 'Non-Refundable Items' }}</h2>
                        <p>{!! $nonRefundableItems->paragraphs ?? 'Certain fees and charges are strictly non-refundable:' !!}</p>
                    </section>
                    @endif

                    @if($serviceDelays)
                    <section id="uwd-delays" class="uwd-rc-section">
                        <h2><i data-lucide="clock"></i> {{ $serviceDelays->title ?? 'Service Delays' }}</h2>
                        <p>{!! $serviceDelays->paragraphs ?? 'Refunds are not provided for delays caused by circumstances beyond our control (Force Majeure), including but not limited to:' !!}</p>
                    </section>
                    @endif

                    @if($pageMeta)
                    <div class="uwd-rc-footer-cta" id="uwd-contact">
                        <h3>{{ $pageMeta->footer_heading ?? 'Need Assistance?' }}</h3>
                        <p class="mb-0">For refund or cancellation queries, reach out to <a href="mailto:{{ $pageMeta->footer_email ?? 'contact@unitedcourier.com' }}" style="color: white; font-weight: 700;">{{ $pageMeta->footer_email ?? 'contact@unitedcourier.com' }}</a></p>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>



<!-- sticky sidebar items Script -->

<script>
    // Initialize Icons
    lucide.createIcons();

    // Intersection Observer for the Sidebar
    const rcObserverOptions = {
        root: null,
        rootMargin: '-20% 0px -70% 0px',
        threshold: 0
    };

    const rcObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                document.querySelectorAll('.uwd-rc-nav-link').forEach(link => {
                    link.classList.remove('uwd-rc-active');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('uwd-rc-active');
                    }
                });
            }
        });
    }, rcObserverOptions);

    // Observe sections
    document.querySelectorAll('.uwd-rc-section').forEach(section => {
        rcObserver.observe(section);
    });

    // Also observe the footer CTA
    const footerCTA = document.getElementById('uwd-contact');
    if (footerCTA) rcObserver.observe(footerCTA);
</script>


@include('website_include.footer')