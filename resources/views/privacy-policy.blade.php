@include('website_include.header')
<!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
<style>
        :root {
            --uwd-pp-primary: #2563eb;
            --uwd-pp-secondary: #9333ea;
            --uwd-pp-bg-light: #f8fafc;
            --uwd-pp-card-bg: rgba(255, 255, 255, 0.8);
            --uwd-pp-glass-border: rgba(255, 255, 255, 0.5);
        }

        #uwd-pp-wrapper {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--uwd-pp-bg-light);
            color: #1e293b;
            line-height: 1.7;
            position: relative;
            padding-top: 60px;
            min-height: 100vh;
        }

        /* Animated Background Blobs */
        .uwd-pp-blob {
            position: fixed;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(147, 51, 234, 0.08) 100%);
            filter: blur(100px);
            border-radius: 50%;
            z-index: 1;
            animation: uwdPpFloat 25s infinite alternate;
        }
        .uwd-pp-blob-1 { top: -15%; left: -10%; }
        .uwd-pp-blob-2 { bottom: -15%; right: -10%; animation-delay: -7s; }

        @keyframes uwdPpFloat {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.1); }
        }

        .uwd-pp-last-updated {
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 20px;
            display: block;
        }

        /* Content Container */
        .uwd-pp-glass-panel {
            background: var(--uwd-pp-card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--uwd-pp-glass-border);
            border-radius: 32px;
            padding: 60px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            margin-bottom: 80px;
            animation: uwdPpFadeInUp 0.8s ease-out;
            position: relative;
            z-index: 2;
        }

        @keyframes uwdPpFadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .uwd-pp-section {
            margin-bottom: 48px;
            scroll-margin-top: 100px; /* Ensures space above section when clicking nav links */
        }

        .uwd-pp-section h2 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #0f172a;
        }

        .uwd-pp-section h2 i {
            color: var(--uwd-pp-primary);
            width: 28px;
            height: 28px;
        }

        .uwd-pp-section p {
            color: #475569;
            font-size: 1.05rem;
            margin-bottom: 16px;
        }

        .uwd-pp-list-group {
            list-style: none;
            padding-left: 0;
        }

        .uwd-pp-list-group li {
            position: relative;
            padding-left: 32px;
            margin-bottom: 12px;
            color: #475569;
        }

        .uwd-pp-list-group li::before {
            content: '→';
            position: absolute;
            left: 0;
            color: var(--uwd-pp-primary);
            font-weight: bold;
        }

        /* Navigation Sidebar */
        .uwd-pp-sidebar-col {
            position: relative;
        }

        .uwd-pp-sticky-nav {
            position: -webkit-sticky;
            position: sticky;
            top: 120px;
            z-index: 10;
        }

        .uwd-pp-nav-link {
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

        .uwd-pp-nav-link:hover {
            background: rgba(37, 99, 235, 0.05);
            color: var(--uwd-pp-primary);
        }

        .uwd-pp-nav-link.uwd-pp-active {
            background: white;
            color: var(--uwd-pp-primary);
            border-color: var(--uwd-pp-glass-border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            font-weight: 700;
        }

        /* Footer CTA */
        .uwd-pp-footer-cta {
            background: linear-gradient(135deg, var(--uwd-pp-primary), var(--uwd-pp-secondary));
            border-radius: 24px;
            padding: 40px;
            color: white;
            text-align: center;
            margin-top: 40px;
        }

        .uwd-pp-btn-white {
            background: white;
            color: var(--uwd-pp-primary);
            border-radius: 12px;
            padding: 12px 32px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }

        .uwd-pp-btn-white:hover {
            transform: scale(1.05);
            color: var(--uwd-pp-secondary);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .uwd-pp-glass-panel { padding: 30px; border-radius: 24px; }
            .uwd-pp-sidebar-col { display: none; }
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
                    Our <span class="moving-gradient-text"> Privacy Policy</span>
                </h1>
                <p style="max-width: 100%;" class="text-center mb-5 lead">
                    {{ $pageMeta->paragraphs ?? 'Your privacy is our priority. This policy outlines how United Worldwide Couriers collects, uses, and protects your information.' }}
                </p>
            </div>

        </div>
    </div>
</header>


<div id="uwd-pp-wrapper">
    <div class="uwd-pp-blob uwd-pp-blob-1"></div>
    <div class="uwd-pp-blob uwd-pp-blob-2"></div>

    <div class="container">
        <div class="row g-5">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 uwd-pp-sidebar-col">
                <div class="uwd-pp-sticky-nav">
                    <nav id="uwd-pp-nav-list">
                        <a href="#uwd-col" class="uwd-pp-nav-link uwd-pp-active">{{ $dataCollection->title ?? 'Data Collection' }}</a>
                        <a href="#uwd-use" class="uwd-pp-nav-link">{{ $dataUsage->title ?? 'Usage of Info' }}</a>
                        @if($dataSharing)
                        <a href="#uwd-sha" class="uwd-pp-nav-link">{{ $dataSharing->title ?? 'Data Sharing' }}</a>
                        @endif
                        <a href="#uwd-sec" class="uwd-pp-nav-link">{{ $dataSecurity->title ?? 'Security Measures' }}</a>
                        <a href="#uwd-cok" class="uwd-pp-nav-link">{{ $cookiesPolicy->title ?? 'Cookies Policy' }}</a>
                        <a href="#uwd-rig" class="uwd-pp-nav-link">{{ $userRights->title ?? 'Your Rights' }}</a>
                        @if($policyUpdates)
                        <a href="#uwd-upd" class="uwd-pp-nav-link">{{ $policyUpdates->title ?? 'Policy Updates' }}</a>
                        @endif
                        <a href="#uwd-con" class="uwd-pp-nav-link">Contact Support</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="uwd-pp-glass-panel">
                    <span class="uwd-pp-last-updated">Last Updated: {{ $pageMeta->effective_date ?? 'October 2023' }}</span>
                    
                    @if($dataCollection)
                    <section id="uwd-col" class="uwd-pp-section">
                        <h2><i data-lucide="database"></i> {{ $dataCollection->title ?? 'Information Collection' }}</h2>
                        <p>{!! $dataCollection->paragraphs ?? 'When you use our worldwide courier services, we collect information that allows us to provide efficient logistics solutions. This includes:' !!}</p>
                    </section>
                    @endif

                    @if($dataUsage)
                    <section id="uwd-use" class="uwd-pp-section">
                        <h2><i data-lucide="activity"></i> {{ $dataUsage->title ?? 'How We Use Information' }}</h2>
                        <p>{!! $dataUsage->paragraphs ?? 'The information we collect is used primarily to fulfill your delivery requests and improve our global network efficiency.' !!}</p>
                    </section>
                    @endif

                    @if($dataSecurity)
                    <section id="uwd-sec" class="uwd-pp-section">
                        <h2><i data-lucide="shield-check"></i> {{ $dataSecurity->title ?? 'Security & Protection' }}</h2>
                        <p>{!! $dataSecurity->paragraphs ?? 'We implement enterprise-grade security protocols to safeguard your sensitive data from unauthorized access or disclosure.' !!}</p>
                    </section>
                    @endif

                    @if($cookiesPolicy)
                    <section id="uwd-cok" class="uwd-pp-section">
                        <h2><i data-lucide="cookie"></i> {{ $cookiesPolicy->title ?? 'Cookies & Tracking' }}</h2>
                        <p>{!! $cookiesPolicy->paragraphs ?? 'Our digital platforms use "cookies" to recognize your preferences and provide a personalized experience. You can choose to disable cookies through your browser settings, though some website features may not function optimally as a result.' !!}</p>
                    </section>
                    @endif

                    @if($userRights)
                    <section id="uwd-rig" class="uwd-pp-section">
                        <h2><i data-lucide="user-check"></i> {{ $userRights->title ?? 'Your Legal Rights' }}</h2>
                        <p>{!! $userRights->paragraphs ?? 'Depending on your jurisdiction (including GDPR or local Indian laws), you have the right to:' !!}</p>
                    </section>
                    @endif

                    @if($dataSharing)
                    <section id="uwd-sha" class="uwd-pp-section">
                        <h2><i data-lucide="share-2"></i> {{ $dataSharing->title ?? 'Data Sharing' }}</h2>
                        <p>{!! $dataSharing->paragraphs ?? 'We may share your information with trusted third parties to facilitate our services.' !!}</p>
                    </section>
                    @endif

                    @if($policyUpdates)
                    <section id="uwd-upd" class="uwd-pp-section">
                        <h2><i data-lucide="refresh-cw"></i> {{ $policyUpdates->title ?? 'Policy Updates' }}</h2>
                        <p>{!! $policyUpdates->paragraphs ?? 'We may update this privacy policy from time to time.' !!}</p>
                    </section>
                    @endif

                    @if($pageMeta)
                    <div class="uwd-pp-footer-cta" id="uwd-con">
                        <h3>{{ $pageMeta->footer_heading ?? 'Have Questions?' }}</h3>
                        <p class="mb-4">If you have any concerns regarding this Privacy Policy or how your data is handled, our compliance team is here to help.</p>
                        <a href="mailto:{{ $pageMeta->footer_email ?? 'contact@unitedcourier.com' }}" class="uwd-pp-btn-white">Contact Privacy Team</a>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>





<!-- sticky sidebar items Script -->

<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    /**
     * ADVANCED SCROLLSPY USING INTERSECTION OBSERVER
     * This is much more robust than scroll listeners and 
     * prevents conflicts with other scroll events on your page.
     */
    const observerOptions = {
        root: null,
        rootMargin: '-20% 0px -70% 0px', // Detects when section is in top part of screen
        threshold: 0
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                
                // Update active class on nav links
                document.querySelectorAll('.uwd-pp-nav-link').forEach(link => {
                    link.classList.remove('uwd-pp-active');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('uwd-pp-active');
                    }
                });
            }
        });
    }, observerOptions);

    // Observe all policy sections
    document.querySelectorAll('.uwd-pp-section').forEach(section => {
        observer.observe(section);
    });

    // Special case for the footer CTA section link
    const contactSection = document.getElementById('uwd-con');
    if (contactSection) observer.observe(contactSection);

</script>


@include('website_include.footer')