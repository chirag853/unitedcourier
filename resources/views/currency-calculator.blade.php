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
<!-- Currency Converter Section -->
<section id="currency_converter_section" class="py-5">
    <div class="container">
        <div class="row currency-shadow">

            <!-- Left: Form -->
            <div class="col-md-7 currency-form-panel">
                <div class="mb-4">
                    <h3 class="h4-title">Enter Conversion <span class="gradient-text">Details</span></h3>
                </div>

                <form id="currencyForm">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Amount</label>
                            <input type="number" id="cc_amount" class="form-control input-custom" value="1000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">From</label>
                            <select id="cc_from" class="form-control input-custom">
                                <option value="INR">INR ₹ · Indian Rupee</option>
                                <option value="USD">USD $ · United States</option>
                                <option value="EUR">EUR € · Euro</option>
                                <option value="GBP">GBP £ · British Pound</option>
                                <option value="AED">AED · UAE Dirham</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">To</label>
                            <select id="cc_to" class="form-control input-custom">
                                <option value="USD" selected>USD $ · United States</option>
                                <option value="INR">INR ₹ · Indian Rupee</option>
                                <option value="EUR">EUR € · Euro</option>
                                <option value="GBP">GBP £ · British Pound</option>
                                <option value="AED">AED · UAE Dirham</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Source currency</label>
                            <div id="cc_source_display" class="currency-display-box">INR ₹ · Indian Rupee</div>
                        </div>
                        <div class="col-md-2 text-center">
                            <button type="button" id="cc_swap" class="btn-swap"><i class="fa-solid fa-right-left"></i></button>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Target currency</label>
                            <div id="cc_target_display" class="currency-display-box">USD $ · United States Dollar</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Popular:</label>
                        <button type="button" class="btn-pill cc-popular" data-from="USD" data-to="INR">USD → INR</button>
                        <button type="button" class="btn-pill cc-popular" data-from="EUR" data-to="INR">EUR → INR</button>
                        <button type="button" class="btn-pill cc-popular" data-from="GBP" data-to="INR">GBP → INR</button>
                        <button type="button" class="btn-pill cc-popular" data-from="AED" data-to="INR">AED → INR</button>
                        <button type="button" class="btn-pill cc-popular active" data-from="INR" data-to="USD">INR → USD</button>
                    </div>

 <button type="button" id="cc_convert" style="width: 240px;" class="btn moving-gradient-bg btn-primary-custom m-2">
                        Convert Currency
                    </button>
                    <button type="button" id="cc_reset" style="width: 150px;" class="btn m-2 btn-outline-reset">
                        Reset
                    </button>
                </form>

                <p class="cc-formula-note">
                    <i class="fa-solid fa-circle-info"></i> Formula: <strong>Amount × Exchange Rate</strong>
                </p>
            </div>

            <!-- Right: Result -->
            <div class="col-md-5 currency-result-panel">
                <div>
                    <p class="cc-label"><span class="static-dot"></span> CONVERTED VALUE</p>
                    <p class="cc-equals" id="cc_equals_line">₹1,000.00 equals</p>
                    <h1 id="cc_result">$10.49</h1>
                    <hr>

                    <div class="cc-info-box">
                        <p class="cc-info-label">EXCHANGE RATE</p>
                        <p class="cc-info-value" id="cc_rate">1 INR = 0.01049 USD</p>
                    </div>
                    <div class="cc-info-box">
                        <p class="cc-info-label">REVERSE RATE</p>
                        <p class="cc-info-value" id="cc_reverse_rate">1 USD = 95.328885 INR</p>
                    </div>
                    <div class="cc-info-box">
                        <p class="cc-info-label">RATE DATE</p>
                        <p class="cc-info-value" id="cc_rate_date">-</p>
                    </div>

                    <p class="cc-status" id="cc_status"></p>
                    <p class="cc-disclaimer">
                        Final realization may vary due to bank markup, settlement timing, payment gateway fees, and marketplace payout terms.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
    .currency-shadow {
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.12);
    }
    .currency-form-panel {
        background: #fff;
        padding: 45px;
    }
    .currency-result-panel {
        background: linear-gradient(160deg, #0f172a, var(--brand-blue));
        color: #fff;
        padding: 45px;
    }
    .currency-display-box {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-weight: 700;
        color: var(--text-dark);
        background: var(--bg-light);
    }
    .btn-swap {
        width: 42px; height: 42px;
        border-radius: 50%;
        border: 1px solid #dbeafe;
        background: #eff6ff;
        color: var(--brand-blue);
    }
    .btn-pill {
        display: inline-block;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: var(--text-dark);
        padding: 6px 16px;
        border-radius: 100px;
        font-weight: 700;
        font-size: 13px;
        margin: 4px 4px 0 0;
    }
    .btn-pill.active, .btn-pill.cc-active {
        background: var(--brand-blue);
        color: #fff;
        border-color: var(--brand-blue);
    }
  .btn-primary-custom, .btn-outline-reset {
        border-radius: 12px;
        padding: 14px 28px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 14px;
    }
    .btn-outline-reset {
        border: 1px solid #cbd5e1;
        color: var(--text-dark);
        background: #fff;
    }
    .btn-outline-reset:hover {
        border-color: #94a3b8;
        background: #f8fafc;
    }
    .cc-formula-note {
        background: #f3f3f3;
        padding: 10px 15px;
        margin-top: 20px;
        font-weight: 600;
        border-radius: 20px;
        width: fit-content;
    }
    .cc-label { color: #fb923c; font-weight: 800; font-size: 13px; letter-spacing: 1px; }
    .cc-equals { color: #cbd5e1; margin-bottom: 4px; }
    .currency-result-panel h1 { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 44px; }
    .currency-result-panel hr { border-color: rgba(255,255,255,0.15); }
    .cc-info-box { background: rgba(255,255,255,0.06); border-radius: 14px; padding: 12px 16px; margin-bottom: 12px; }
    .cc-info-label { color: #94a3b8; font-size: 11px; font-weight: 700; letter-spacing: 1px; margin-bottom: 4px; }
    .cc-info-value { font-weight: 800; margin: 0; }
    .cc-status { color: #4ade80; font-weight: 600; margin-top: 10px; }
    .cc-disclaimer { color: #94a3b8; font-size: 13px; line-height: 1.6; }
</style>

<script>
(function () {
    const CC_ACCESS_KEY = '979ec4da644188e9862b5a5625e3f73c';
    const CC_API_URL = 'https://api.exchangeratesapi.io/v1/latest?access_key=' + CC_ACCESS_KEY;

    const fromSelect = document.getElementById('cc_from');
    const toSelect = document.getElementById('cc_to');
    const amountInput = document.getElementById('cc_amount');
    const sourceDisplay = document.getElementById('cc_source_display');
    const targetDisplay = document.getElementById('cc_target_display');
    const statusEl = document.getElementById('cc_status');

    const currencyNames = {
        INR: 'INR ₹ · Indian Rupee',
        USD: 'USD $ · United States Dollar',
        EUR: 'EUR € · Euro',
        GBP: 'GBP £ · British Pound',
        AED: 'AED · UAE Dirham'
    };
    function updateDisplays() {
        sourceDisplay.textContent = currencyNames[fromSelect.value];
        targetDisplay.textContent = currencyNames[toSelect.value];
    }

    fromSelect.addEventListener('change', updateDisplays);
    toSelect.addEventListener('change', updateDisplays);
    updateDisplays(); // set initial values on load

    let ratesCache = null;

    async function getRates() {
        if (ratesCache) return ratesCache;
        statusEl.textContent = 'Fetching live rates...';
        statusEl.style.color = '#93c5fd';
        try {
            const res = await fetch(CC_API_URL);
            const data = await res.json();
            if (!data.success) throw new Error(data.error?.info || 'API error');
            ratesCache = data; // { rates: {...}, date: 'YYYY-MM-DD', base: 'EUR' }
            return ratesCache;
        } catch (err) {
            statusEl.textContent = 'Could not fetch live rates: ' + err.message;
            statusEl.style.color = '#f87171';
            throw err;
        }
    }

    function crossRate(rates, from, to) {
        // rates are all relative to EUR base
        const eurToFrom = rates[from];
        const eurToTo = rates[to];
        return eurToTo / eurToFrom;
    }

    async function convert() {
        const from = fromSelect.value;
        const to = toSelect.value;
        const amount = parseFloat(amountInput.value) || 0;

 updateDisplays();

        try {
            const data = await getRates();
            const rate = crossRate(data.rates, from, to);
            const converted = amount * rate;
            const reverseRate = 1 / rate;
 function toDMY(dateStr) {
        const [y, m, d] = dateStr.split('-');
        return `${d}-${m}-${y}`;
    }
            document.getElementById('cc_equals_line').textContent =
                new Intl.NumberFormat('en-US', { style: 'currency', currency: from }).format(amount) + ' equals';
            document.getElementById('cc_result').textContent =
                new Intl.NumberFormat('en-US', { style: 'currency', currency: to }).format(converted);
            document.getElementById('cc_rate').textContent = `1 ${from} = ${rate.toFixed(5)} ${to}`;
            document.getElementById('cc_reverse_rate').textContent = `1 ${to} = ${reverseRate.toFixed(6)} ${from}`;
            // document.getElementById('cc_rate_date').textContent = data.date;
            document.getElementById('cc_rate_date').textContent = toDMY(data.date);

            statusEl.textContent = 'Indicative exchange rate fetched successfully.';
            statusEl.style.color = '#4ade80';
        } catch (err) {
            // status already set inside getRates()
        }
    }

    document.getElementById('cc_convert').addEventListener('click', convert);

    document.getElementById('cc_swap').addEventListener('click', function () {
        const tmp = fromSelect.value;
        fromSelect.value = toSelect.value;
        toSelect.value = tmp;
        convert();
    });

    document.getElementById('cc_reset').addEventListener('click', function () {
        amountInput.value = 1000;
        fromSelect.value = 'INR';
        toSelect.value = 'USD';
        document.querySelectorAll('.cc-popular').forEach(b => b.classList.remove('active'));
        document.querySelector('.cc-popular[data-from="INR"][data-to="USD"]').classList.add('active');
        convert();
    });

    document.querySelectorAll('.cc-popular').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.cc-popular').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            fromSelect.value = this.dataset.from;
            toSelect.value = this.dataset.to;
            convert();
        });
    });

    // Initial load
    convert();
})();
</script>
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