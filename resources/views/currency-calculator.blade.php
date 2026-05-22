@include('website_include.header')

    <style>
        :root {
            --brand-blue: #2563eb;
            --brand-purple: #9333ea;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
        }

        .features-section {
            padding: 50px 0;
            background-color: #fff !important;
        }

        /* --- Header Styling --- */
        .feature-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(37, 99, 235, 0.08);
            color: var(--brand-blue);
            padding: 8px 18px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 44px;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .section-title span {
            background: linear-gradient(90deg, var(--brand-blue), var(--brand-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-desc {
            color: var(--text-muted);
            font-size: 17px;
            max-width: 600px;
            line-height: 1.6;
        }

        /* --- Feature Card Styling --- */
        .feat-card {
            padding: 30px;
            border-radius: 32px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            border: 1px solid transparent;
            position: relative;
            position: relative;
            overflow: hidden;
        }

        .feat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
        }

        /* Card Color Variations */
        .feat-blue { background-color: #f0f7ff; }
        .feat-purple { background-color: #faf5ff; }
        .feat-green { background-color: #f0fdf4; }
        .feat-orange { background-color: #fffaf0; }

        .feat-icon-box {
            width: 64px;
            height: 64px;
            background: #ffffff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }

        .feat-card:hover .feat-icon-box {
            transform: scale(1.1) rotate(-5deg);
        }

        /* Icon Colors */
        .feat-blue .feat-icon-box { color: #2563eb; }
        .feat-blue .feat-icon-box { color: #2563eb; }
        .feat-purple .feat-icon-box { color: #8b5cf6; }
        .feat-green .feat-icon-box { color: #16a34a; }
        .feat-orange .feat-icon-box { color: #f59e0b; }

        .feat-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 14px;
        }

        .feat-text {
            color: var(--text-muted);
            font-size: 16px;
            line-height: 1.7;
            text-align: left;
            margin: 0;
        }

        @media (max-width: 991px) {
            .section-title { font-size: 34px; }
            .feat-card { padding: 30px; }
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

                    @if($hero && $hero->title)
                    <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                        <span class="static-dot"></span>
                        {{ $hero->title }}
                    </div>
                    @endif

                    @if($hero && $hero->content && isset($hero->content['title']))
                    <h1 class="hero-title mb-4">
                        {!! $hero->content['title'] !!}
                    </h1>
                    @endif

                    @if($hero && $hero->description)
                    <p style="max-width: 100%;" class="mb-5 lead">
                        {{ $hero->description }}
                    </p>
                    @endif

                </div>

                <!-- Right Image -->
                @if($hero && $hero->image)
                <div class="col-md-6 text-center">
                    <div class="hero-graphic">
                        <img src="{{ asset($hero->image) }}" class="img-fluid" style="width:80%;">
                    </div>
                </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section style="background:#fff;" class="features-section">
        <div class="container">
            <!-- Section Header -->
            @if($featuresHeader)
            <div class="row justify-content-center mb-3">
                <div class="col-lg-12 text-center">
                    <h2 class="about-title">{{ $featuresHeader->title }}</h2>
                    @if($featuresHeader->description)
                    <p class="about-desc text-center">{!! nl2br(e($featuresHeader->description)) !!}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Features Grid -->
            @if($featureCards && $featureCards->count() > 0)
            <div class="row g-4">
                @foreach($featureCards as $card)
                <div class="col-lg-3 col-md-6">
                    <div class="feat-card {{ $card->content['color_class'] ?? 'feat-blue' }}">
                        @if(isset($card->content['icon']))
                        <div class="feat-icon-box">
                            <i class="fas {{ $card->content['icon'] }}"></i>
                        </div>
                        @endif
                        @if($card->title)
                        <h3 class="feat-title">{{ $card->title }}</h3>
                        @endif
                        @if($card->description)
                        <p class="feat-text">{{ $card->description }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

@include('website_include.footer')