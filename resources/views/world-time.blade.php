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
    overflow: hidden;
}

.feat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
}

/* Card Color Variations */
.feat-blue {
    background-color: #f0f7ff;
}

.feat-purple {
    background-color: #faf5ff;
}

.feat-green {
    background-color: #f0fdf4;
}

.feat-orange {
    background-color: #fffaf0;
}

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
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.03);
    transition: all 0.3s ease;
}

.feat-card:hover .feat-icon-box {
    transform: scale(1.1) rotate(-5deg);
}

/* Icon Colors */
.feat-blue .feat-icon-box {
    color: #2563eb;
}

.feat-purple .feat-icon-box {
    color: #8b5cf6;
}

.feat-green .feat-icon-box {
    color: #16a34a;
}

.feat-orange .feat-icon-box {
    color: #f59e0b;
}

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
    .section-title {
        font-size: 34px;
    }

    .feat-card {
        padding: 30px;
    }
}
</style>




<!-- Hero section -->
<header style="min-height: 70vh; padding-top: 140px; padding-bottom: 50px;" class="hero-gradient">
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
                    {{ $hero->title ?? 'World Time' }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $hero->content['title'] ?? 'Global <span class="moving-gradient-text">Time Zones</span>' !!}
                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ $hero->description ?? 'Check current time across different countries and time zones instantly with accurate and real-time updates.' }}
                </p>

            </div>

            <!-- Right Image -->
            @if($hero && $hero->image)
            <div class="col-md-6 text-center">
                <div class="hero-graphic">
                    <img src="{{ asset($hero->image) }}" class="img-fluid" style="width:50%;">
                </div>
            </div>
            @endif
        </div>
</header>


<!-- Live time and clock start -->

<section class="py-5 bg-white">
    <div class="container text-center">

        @if($timeHeader)
        <div class="feature-badge">
            <i class="fas {{ $timeHeader->content['badge_icon'] ?? 'fa-clock' }}"></i>
            {{ $timeHeader->content['badge_text'] ?? 'Live World Time' }}
        </div>

        <h2 class="section-title mb-3">
            {!! $timeHeader->title ?? 'Current Time Around the <span>World</span>' !!}
        </h2>

        <p class="section-desc mx-auto mb-5">
            {{ $timeHeader->description ?? 'Track real-time clocks across major global cities and stay synchronized worldwide.' }}
        </p>
        @else
        <div class="feature-badge">
            <i class="fas fa-clock"></i>
            Live World Time
        </div>

        <h2 class="section-title mb-3">
            Current Time Around the <span>World</span>
        </h2>

        <p class="section-desc mx-auto mb-5">
            Track real-time clocks across major global cities and stay synchronized worldwide.
        </p>
        @endif

        <div class="row g-4 justify-content-center">

            @forelse($timeCities as $city)
            <div class="col-lg-3 col-md-4 col-6">
                <div class="feat-card {{ $city->content['color_class'] ?? 'feat-blue' }}">
                    <h5>{{ $city->content['emoji'] ?? '' }} {{ $city->title }}</h5>
                    <div id="{{ $city->content['clock_id'] ?? '' }}" class="fw-bold fs-4"
                        data-timezone="{{ $city->content['timezone'] ?? '' }}">--</div>
                    <small class="text-muted">{{ $city->content['timezone_abbr'] ?? '' }}</small>
                </div>
            </div>
            @empty
            <!-- Default fallback clocks -->
            <div class="col-lg-3 col-md-4 col-6">
                <div class="feat-card feat-blue">
                    <h5>🇮🇳 Delhi</h5>
                    <div id="clock-delhi" class="fw-bold fs-4" data-timezone="Asia/Kolkata">--</div>
                    <small class="text-muted">IST</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <div class="feat-card feat-purple">
                    <h5>🇺🇸 New York</h5>
                    <div id="clock-ny" class="fw-bold fs-4" data-timezone="America/New_York">--</div>
                    <small class="text-muted">EST</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <div class="feat-card feat-green">
                    <h5>🇬🇧 London</h5>
                    <div id="clock-london" class="fw-bold fs-4" data-timezone="Europe/London">--</div>
                    <small class="text-muted">GMT</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <div class="feat-card feat-orange">
                    <h5>🇦🇪 Dubai</h5>
                    <div id="clock-dubai" class="fw-bold fs-4" data-timezone="Asia/Dubai">--</div>
                    <small class="text-muted">GST</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <div class="feat-card feat-blue">
                    <h5>🇯🇵 Tokyo</h5>
                    <div id="clock-tokyo" class="fw-bold fs-4" data-timezone="Asia/Tokyo">--</div>
                    <small class="text-muted">JST</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <div class="feat-card feat-purple">
                    <h5>🇦🇺 Sydney</h5>
                    <div id="clock-sydney" class="fw-bold fs-4" data-timezone="Australia/Sydney">--</div>
                    <small class="text-muted">AEST</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <div class="feat-card feat-green">
                    <h5>🇫🇷 Paris</h5>
                    <div id="clock-paris" class="fw-bold fs-4" data-timezone="Europe/Paris">--</div>
                    <small class="text-muted">CET</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <div class="feat-card feat-orange">
                    <h5>🇸🇬 Singapore</h5>
                    <div id="clock-singapore" class="fw-bold fs-4" data-timezone="Asia/Singapore">--</div>
                    <small class="text-muted">SGT</small>
                </div>
            </div>
            @endforelse

        </div>

    </div>
</section>

<!-- Live time and clock ends -->


<section style="background:#fff;" class="features-section">
    <div class="container">
        <!-- Section Header -->
        @if($featuresHeader)
        <div class="row justify-content-center mb-3">
            <div class="col-lg-12 text-center">
                <h2 class="about-title">{{ $featuresHeader->title ?? 'Importance of World Time Tracking' }}</h2>
                <p class="about-desc text-center">
                    {{ $featuresHeader->description ?? 'People use world time tools to stay synchronized across different countries and regions.' }}
                </p>
            </div>
        </div>
        @else
        <div class="row justify-content-center mb-3">
            <div class="col-lg-12 text-center">
                <h2 class="about-title">Importance of World Time Tracking</h2>
                <p class="about-desc text-center">People use world time tools to stay synchronized across different
                    countries and regions.</p>
            </div>
        </div>
        @endif

        <!-- Features Grid -->
        <div class="row g-4">

            @forelse($featureCards as $feature)
            <div class="col-lg-3 col-md-6">
                <div class="feat-card {{ $feature->content['color_class'] ?? 'feat-blue' }}">
                    <div class="feat-icon-box">
                        <i class="fas {{ $feature->content['icon'] ?? 'fa-clock' }}"></i>
                    </div>
                    <h3 class="feat-title">{{ $feature->title }}</h3>
                    <p class="feat-text">{{ $feature->description }}</p>
                </div>
            </div>
            @empty
            <!-- Default fallback features -->
            <div class="col-lg-3 col-md-6">
                <div class="feat-card feat-blue">
                    <div class="feat-icon-box">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="feat-title">Real-time Global Time</h3>
                    <p class="feat-text">Get accurate and up-to-date current time from any country or city around the
                        world instantly.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feat-card feat-purple">
                    <div class="feat-icon-box">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3 class="feat-title">Worldwide Coverage</h3>
                    <p class="feat-text">Access time zones from all over the globe and stay connected no matter where
                        you are.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feat-card feat-green">
                    <div class="feat-icon-box">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h3 class="feat-title">Instant Time Convert</h3>
                    <p class="feat-text">Easily convert time between different time zones for meetings, travel, or
                        events.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feat-card feat-orange">
                    <div class="feat-icon-box">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <h3 class="feat-title">Smart Time Planning</h3>
                    <p class="feat-text">Plan your schedule efficiently by comparing time zones and avoiding confusion
                        across regions.</p>
                </div>
            </div>
            @endforelse

        </div>
    </div>
</section>




<!-- world time script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let clockData = {};
    let browserActive = true;

    function pad(n) {
        return n.toString().padStart(2, '0');
    }

    // ---- BROWSER-BASED FALLBACK (always visible, updates every second) ----
    function showBrowserClocks() {
        document.querySelectorAll("[id^='clock-']").forEach(el => {
            const tz = el.getAttribute('data-timezone');
            if (!tz) return;
            try {
                el.innerHTML = new Date().toLocaleTimeString('en-US', {
                    timeZone: tz,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                });
            } catch(e) {
                el.innerHTML = '--:--:--';
            }
        });
    }

    // Show browser time immediately (guaranteed to work)
    showBrowserClocks();
    setInterval(showBrowserClocks, 1000);

    // ---- API-BASED ACCURATE TIME (overrides browser when data arrives) ----
    function fetchAPITimes() {
        document.querySelectorAll("[id^='clock-']").forEach(el => {
            const tz = el.getAttribute('data-timezone');
            if (!tz) return;

            const url = `https://worldtimeapi.org/api/timezone/${tz}`;

            fetch(url)
                .then(res => {
                    if (!res.ok) throw new Error('World time service is unavailable. Please try again later.');
                    return res.json();
                })
                .then(data => {
                    // Parse UTC datetime + offset for accurate local time calculation
                    const apiDate = new Date(data.utc_datetime);
                    // Extract offset in minutes from "+05:30" or "-05:00"
                    const parts = data.utc_offset.match(/([+-])(\d{2}):(\d{2})/);
                    const offsetMin = parts ?
                        (parseInt(parts[2]) * 60 + parseInt(parts[3])) * (parts[1] === '-' ? -1 : 1) :
                        0;

                    clockData[el.id] = {
                        baseUtcMs: apiDate.getTime(),
                        offsetMinutes: offsetMin,
                        baseTimeMs: Date.now()
                    };
                    // Update this clock to use API-driven tick
                    tickAPIClock(el.id);
                })
                .catch(err => {
                    // Browser fallback already handles this timezone
                    console.log('API failed for ' + tz + ': ' + err.message);
                });
        });
    }

    function tickAPIClock(id) {
        const el = document.getElementById(id);
        if (!el || !clockData[id]) return;

        const data = clockData[id];
        const elapsed = Date.now() - data.baseTimeMs;
        // Local time = UTC + offset + elapsed since fetch
        const localMs = data.baseUtcMs + elapsed + (data.offsetMinutes * 60 * 1000);
        const d = new Date(localMs);

        const hours = d.getUTCHours();
        const minutes = d.getUTCMinutes();
        const seconds = d.getUTCSeconds();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const h12 = hours % 12 || 12;

        el.innerHTML = `${pad(h12)}:${pad(minutes)}:${pad(seconds)} ${ampm}`;
    }

    // Tick API-based clocks every second
    setInterval(() => {
        for (let id in clockData) {
            tickAPIClock(id);
        }
    }, 1000);

    // Initial API fetch
    fetchAPITimes();

    // Re-fetch API every 2 minutes to stay in sync
    setInterval(fetchAPITimes, 120000);
});
</script>

@include('website_include.footer')