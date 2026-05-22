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
                    {{ $hero->title ?? 'World Weather' }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $hero->content['title'] ?? 'Global <span class="moving-gradient-text">Weather Updates</span>'
                    !!}
                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ $hero->description ?? 'Check current weather conditions across different countries and cities instantly with accurate and real-time updates.' }}
                </p>

            </div>

            <!-- Right Image -->
            <div class="col-md-6 text-center">
                <div class="hero-graphic">
                    <img src="{{ asset($hero->image ?? 'public/website_images/weather.webp') }}" class="img-fluid"
                        style="width:80%; border-radius:20px">
                </div>
            </div>
        </div>
</header>



<section class="py-5 bg-white">
    <div class="container text-center">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h2 class="section-title mb-2">{{ $weatherHeader->title ?? '🌦️ World Weather' }}</h2>

            <!-- Temperature Toggle -->
            <div>
                <button class="btn btn-sm btn-primary" onclick="setUnit('C')">°C</button>
                <button class="btn btn-sm btn-outline-primary" onclick="setUnit('F')">°F</button>
            </div>
        </div>

        <div class="row g-4">

            @forelse($weatherCities as $city)
            <div class="col-lg-3 col-md-6">
                <div class="feat-card {{ $city->content['color_class'] ?? 'feat-blue' }}">
                    <h5>{{ $city->content['emoji'] ?? '' }} {{ $city->title }}</h5>
                    <div class="weather-temp" style="font-size: 32px; font-weight: 700;" data-city="{{ $city->title }}" data-lat="{{ $city->content['lat'] ?? '' }}"
                        data-lon="{{ $city->content['lon'] ?? '' }}">--</div>
                    <p class="text-muted condition-text">{{ $city->content['condition'] ?? '' }}</p>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p class="text-muted">Weather data not available.</p>
            </div>
            @endforelse

        </div>

    </div>
</section>



<section style="background:#fff;" class="features-section">
    <div class="container">
        <!-- Section Header -->
        <div class="row justify-content-center mb-3">
            <div class="col-lg-12 text-center">
                <h2 class="about-title">{{ $featuresHeader->title ?? 'Importance of World Weather Tracking' }}</h2>

                <p class="about-desc text-center">
                    {{ $featuresHeader->description ?? 'People use world weather tools to stay informed about climate conditions across different regions. Whether for travel, business planning, or daily activities, knowing the weather in another location is essential. A world weather tool helps users easily check and compare weather conditions, ensuring better preparation and decision-making globally.' }}
                </p>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="row g-4">

            @forelse($featureCards as $feature)
            @if($feature->item_key == 'feature_card_1')
            <div class="col-lg-3 col-md-6">
                <div class="feat-card {{ $feature->content['color_class'] ?? 'feat-blue' }}">
                    <div class="feat-icon-box">
                        <i class="fas {{ $feature->content['icon'] ?? 'fa-cloud-sun' }}"></i>
                    </div>
                    <h3 class="feat-title">{{ $feature->title }}</h3>
                    <p class="feat-text">{{ $feature->description }}</p>
                </div>
            </div>
            @elseif($feature->item_key == 'feature_card_2')
            <div class="col-lg-3 col-md-6">
                <div class="feat-card {{ $feature->content['color_class'] ?? 'feat-purple' }}">
                    <div class="feat-icon-box">
                        <i class="fas {{ $feature->content['icon'] ?? 'fa-globe' }}"></i>
                    </div>
                    <h3 class="feat-title">{{ $feature->title }}</h3>
                    <p class="feat-text">{{ $feature->description }}</p>
                </div>
            </div>
            @elseif($feature->item_key == 'feature_card_3')
            <div class="col-lg-3 col-md-6">
                <div class="feat-card {{ $feature->content['color_class'] ?? 'feat-green' }}">
                    <div class="feat-icon-box">
                        <i class="fas {{ $feature->content['icon'] ?? 'fa-sync-alt' }}"></i>
                    </div>
                    <h3 class="feat-title">{{ $feature->title }}</h3>
                    <p class="feat-text">{{ $feature->description }}</p>
                </div>
            </div>
            @elseif($feature->item_key == 'feature_card_4')
            <div class="col-lg-3 col-md-6">
                <div class="feat-card {{ $feature->content['color_class'] ?? 'feat-orange' }}">
                    <div class="feat-icon-box">
                        <i class="fas {{ $feature->content['icon'] ?? 'fa-umbrella' }}"></i>
                    </div>
                    <h3 class="feat-title">{{ $feature->title }}</h3>
                    <p class="feat-text">{{ $feature->description }}</p>
                </div>
            </div>
            @endif
            @empty
            <div class="col-12 text-center">
                <p class="text-muted">Feature information not available.</p>
            </div>
            @endforelse

        </div>
    </div>
</section>



<script>
let unit = "C";

// Weather condition mapping from WMO weather codes (Open-Meteo)
// Reference: https://open-meteo.com/en/docs
const weatherCodeMap = {
    0: 'Clear',
    1: 'Mainly Clear',
    2: 'Partly Cloudy',
    3: 'Overcast',
    45: 'Foggy',
    48: 'Depositing Rime Fog',
    51: 'Light Drizzle',
    53: 'Moderate Drizzle',
    55: 'Dense Drizzle',
    56: 'Light Freezing Drizzle',
    57: 'Dense Freezing Drizzle',
    61: 'Slight Rain',
    63: 'Moderate Rain',
    65: 'Heavy Rain',
    66: 'Light Freezing Rain',
    67: 'Heavy Freezing Rain',
    71: 'Slight Snow',
    73: 'Moderate Snow',
    75: 'Heavy Snow',
    77: 'Snow Grains',
    80: 'Slight Rain Showers',
    81: 'Moderate Rain Showers',
    82: 'Violent Rain Showers',
    85: 'Slight Snow Showers',
    86: 'Heavy Snow Showers',
    95: 'Thunderstorm',
    96: 'Thunderstorm with Slight Hail',
    99: 'Thunderstorm with Heavy Hail',
};

function getCondition(code) {
    return weatherCodeMap[code] || 'Unknown';
}

function convertTemp(temp) {
    return unit === "C" ? Math.round(temp) + "°C" : (Math.round(temp * 9 / 5) + 32) + "°F";
}

function fetchWeather() {
    document.querySelectorAll(".weather-temp").forEach(el => {
        const lat = el.getAttribute("data-lat");
        const lon = el.getAttribute("data-lon");
        const conditionEl = el.closest('.feat-card')?.querySelector('.condition-text');

        if (!lat || !lon) {
            el.innerHTML = '--';
            return;
        }

        el.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        const url =
            `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`;

        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('Failed to fetch');
                return res.json();
            })
            .then(data => {
                const temp = data.current_weather.temperature;
                const code = data.current_weather.weathercode;
                const condition = getCondition(code);

                el.innerHTML = convertTemp(temp);

                if (conditionEl) {
                    conditionEl.textContent = condition;
                }
            })
            .catch(() => {
                el.innerHTML = '--';
            });
    });
}

function updateWeather() {
    document.querySelectorAll(".weather-temp").forEach(el => {
        const raw = el.getAttribute("data-raw-temp");
        if (raw) {
            el.innerHTML = convertTemp(parseFloat(raw));
        }
        el.style.fontSize = "32px";
        el.style.fontWeight = "700";
    });
}

function setUnit(u) {
    unit = u;

    // Toggle button styles
    document.querySelectorAll("button").forEach(btn => {
        btn.classList.remove("btn-primary");
        btn.classList.add("btn-outline-primary");
    });

    event.target.classList.add("btn-primary");

    // Re-fetch with new unit display
    fetchWeather();
}

// Initial load - fetch live weather
fetchWeather();

// Auto-refresh every 10 minutes
setInterval(fetchWeather, 600000);
</script>



@include('website_include.footer')