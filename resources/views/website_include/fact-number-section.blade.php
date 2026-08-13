<style>
    /* FACTS NUMBER CSS */
    :root {
        --brand-blue: #2563eb;
        --brand-gradient: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        --text-main: #0f172a;
        --text-muted: #64748b;
        --card-border: rgba(0, 0, 0, 0.06);
    }
.stats-wrapper {
    width: 100%;
    padding: 0 10px;
    max-width: 1400px;
    margin: 0 auto;
}

.stats-container {
    display: flex;
    justify-content: center;
    align-items: stretch;
    gap: 15px;
    width: 100%;
    flex-wrap: wrap;
}

.stat-card {
    flex: 0 1 200px;
    text-align: center;
     background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 16px;
        padding: 10px;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: center;
}

    .stat-card:hover {
        border-color: var(--brand-blue);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
        transform: translateY(-4px);
    }

    .stat-number-wrapper {
        font-family: 'Outfit', sans-serif;
        font-size: 2.2rem;
        font-weight: 500;
        color: var(--brand-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 4px;
        letter-spacing: -0.01em;
    }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 400;
        color: var(--text-muted);
        margin: 0;
        letter-spacing: 0.02em;
    }

    @media (max-width: 1200px) {
        .stat-number-wrapper { font-size: 1.8rem; }
        .stat-label { font-size: 0.75rem; }
    }

    @media (max-width: 992px) {
        .stats-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 576px) {
        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<!-- FACTS NUMBER section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Trusted by <span class="gradient-text">Businesses</span> for daily logistics</h2>
        </div>
        <div class="stats-wrapper">
            <div class="stats-container">
                @if($commonStats && $commonStats->count() > 0)
                    @foreach($commonStats as $stat)
                        <div class="stat-card">
                            <div class="stat-number-wrapper">
                                <span class="stat-number" data-target="{{ $stat->target_number }}">{{ $stat->target_number }}</span>{{ $stat->suffix }}
                            </div>
                            <p class="stat-label">{{ $stat->title }}</p>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback hardcoded stats -->
                    <div class="stat-card">
                        <div class="stat-number-wrapper"><span class="stat-number" data-target="150">0</span>+</div>
                        <p class="stat-label">Cities Covered</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number-wrapper"><span class="stat-number" data-target="100">0</span>K+</div>
                        <p class="stat-label">Daily Parcels</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number-wrapper"><span class="stat-number" data-target="5">0</span>K+</div>
                        <p class="stat-label">Delivery Riders</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number-wrapper"><span class="stat-number" data-target="99">0</span>.9%</div>
                        <p class="stat-label">On-time Rate</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number-wrapper"><span class="stat-number" data-target="24">0</span>/7</div>
                        <p class="stat-label">Live Tracking</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number-wrapper"><span class="stat-number" data-target="50">0</span>K+</div>
                        <p class="stat-label">Happy Clients</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<script>
    const animateCounter = (el) => {
        const target = parseInt(el.getAttribute('data-target'));
        const duration = 1500;
        const stepTime = 20;
        const steps = duration / stepTime;
        const increment = target / steps;
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                el.innerText = target;
                clearInterval(timer);
            } else {
                el.innerText = Math.floor(current);
            }
        }, stepTime);
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target.querySelector('.stat-number');
                if (counter && !counter.classList.contains('counted')) {
                    animateCounter(counter);
                    counter.classList.add('counted');
                }
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('.stat-card').forEach(card => observer.observe(card));
</script>