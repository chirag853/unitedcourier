@include('website_include.header')

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        :root {
            --uwd-tc-primary: #2563eb;
            --uwd-tc-secondary: #9333ea;
            --uwd-tc-bg-light: #f8fafc;
            --uwd-tc-card-bg: rgba(255, 255, 255, 0.8);
            --uwd-tc-glass-border: rgba(255, 255, 255, 0.5);
        }
        

        #uwd-tc-wrapper {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--uwd-tc-bg-light);
            color: #1e293b;
            line-height: 1.7;
            position: relative;
            padding-top: 60px;
            min-height: 100vh;
        }

        /* Animated Background Blobs */
        .uwd-tc-blob {
            position: fixed;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(147, 51, 234, 0.08) 100%);
            filter: blur(100px);
            border-radius: 50%;
            z-index: 1;
            animation: uwdTcFloat 25s infinite alternate;
        }
        .uwd-tc-blob-1 { top: -15%; left: -10%; }
        .uwd-tc-blob-2 { bottom: -15%; right: -10%; animation-delay: -7s; }

        @keyframes uwdTcFloat {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.1); }
        }

        .uwd-tc-last-updated {
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 20px;
            display: block;
        }

        /* Content Container */
        .uwd-tc-glass-panel {
            background: var(--uwd-tc-card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--uwd-tc-glass-border);
            border-radius: 32px;
            padding: 60px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            margin-bottom: 80px;
            animation: uwdTcFadeInUp 0.8s ease-out;
            position: relative;
            z-index: 2;
        }

        @keyframes uwdTcFadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .uwd-tc-section {
            margin-bottom: 48px;
            scroll-margin-top: 100px;
        }

        .uwd-tc-section h2 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #0f172a;
        }

        .uwd-tc-section h2 i {
            color: var(--uwd-tc-primary);
            width: 28px;
            height: 28px;
        }

        .uwd-tc-section p {
            color: #475569;
            font-size: 1.05rem;
            margin-bottom: 16px;
        }

        .uwd-tc-list-group {
            list-style: none;
            padding-left: 0;
        }

        .uwd-tc-list-group li {
            position: relative;
            padding-left: 32px;
            margin-bottom: 12px;
            color: #475569;
        }

        .uwd-tc-list-group li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--uwd-tc-primary);
            font-weight: bold;
        }

        /* Navigation Sidebar */
        .uwd-tc-sidebar-col {
            position: relative;
        }

        .uwd-tc-sticky-nav {
            position: -webkit-sticky;
            position: sticky;
            top: 100px;
            z-index: 10;
        }

        .uwd-tc-nav-link {
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

        .uwd-tc-nav-link:hover {
            background: rgba(37, 99, 235, 0.05);
            color: var(--uwd-tc-primary);
        }

        .uwd-tc-nav-link.uwd-tc-active {
            background: white;
            color: var(--uwd-tc-primary);
            border-color: var(--uwd-tc-glass-border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            font-weight: 700;
        }

        /* Footer CTA */
        .uwd-tc-footer-cta {
            background: linear-gradient(135deg, var(--uwd-tc-primary), var(--uwd-tc-secondary));
            border-radius: 24px;
            padding: 40px;
            color: white;
            text-align: center;
            margin-top: 40px;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .uwd-tc-glass-panel { padding: 30px; border-radius: 24px; }
            .uwd-tc-sidebar-col { display: none; }
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
                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    Effective Date: {{ $pageMeta->effective_date ?? 'October 2025' }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $pageMeta->title ?? 'Our <span class="moving-gradient-text">Terms and Conditions</span>' !!}
                </h1>
                <p style="max-width: 100%;" class="text-center mb-5 lead">
                    {{ $pageMeta->paragraphs ?? 'By using our website or services, you agree to follow our terms. Please read them carefully before proceeding.' }}
                </p>
            </div>

        </div>
    </div>
</header>


<div id="uwd-tc-wrapper">
    <div class="uwd-tc-blob uwd-tc-blob-1"></div>
    <div class="uwd-tc-blob uwd-tc-blob-2"></div>

    <div class="container">
        <div class="row g-5">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 uwd-tc-sidebar-col">
                <div class="uwd-tc-sticky-nav">
                    <nav id="uwd-tc-nav-list">
                        @forelse($sections as $index => $section)
                            <a href="#section-{{ $section->section_key }}" class="uwd-tc-nav-link {{ $loop->first ? 'uwd-tc-active' : '' }}">
                                {{ $section->title ?? Str::title(str_replace('_', ' ', $section->section_key)) }}
                            </a>
                        @empty
                            <a href="#" class="uwd-tc-nav-link">No sections available</a>
                        @endforelse
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="uwd-tc-glass-panel">
                    <span class="uwd-tc-last-updated">Effective Date: {{ $pageMeta->effective_date ?? 'October 2025' }}</span>
                    
                    @forelse($sections as $section)
                        <section id="section-{{ $section->section_key }}" class="uwd-tc-section">
                            <h2><i data-lucide="file-text"></i> {{ $section->title ?? Str::title(str_replace('_', ' ', $section->section_key)) }}</h2>
                            <p>{!! $section->paragraphs ?? '' !!}</p>
                        </section>
                    @empty
                        <section class="uwd-tc-section">
                            <h2><i data-lucide="file-text"></i> Terms and Conditions</h2>
                            <p>No content available at this time.</p>
                        </section>
                    @endforelse

                    <div class="uwd-tc-footer-cta">
                        <h3>{{ $pageMeta->footer_heading ?? 'Questions about our Terms?' }}</h3>
                        <p class="mb-0">Contact our legal department at <a href="mailto:{{ $pageMeta->footer_email ?? 'contact@unitedcourier.com' }}" style="color: white; font-weight: 700;">{{ $pageMeta->footer_email ?? 'contact@unitedcourier.com' }}</a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<!-- sticky sidebar items Script -->

<script>
    lucide.createIcons();

    const tcObserverOptions = {
        root: null,
        rootMargin: '-20% 0px -70% 0px',
        threshold: 0
    };

    const tcObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                document.querySelectorAll('.uwd-tc-nav-link').forEach(link => {
                    link.classList.remove('uwd-tc-active');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('uwd-tc-active');
                    }
                });
            }
        });
    }, tcObserverOptions);

    document.querySelectorAll('.uwd-tc-section').forEach(section => {
        tcObserver.observe(section);
    });
</script>


@include('website_include.footer')