@include('website_include.header')
<style>
            /* Right side container */
            #resultBox {
                
                border-radius: 20px;
                padding: 25px;
                color: #fff;

                background: linear-gradient(135deg, #0b1a2b, #0f2b5c);
                position: relative;
                overflow: hidden;
            }

            /* subtle dotted pattern */
            #resultBox::before {
                content: "";
                position: absolute;
                inset: 0;
                background-image: radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
                background-size: 18px 18px;
                opacity: 0.3;
            }

            /* content above pattern */
            #resultBox > * {
                position: relative;
                z-index: 2;
            }

            /* heading */
            #resultBox h5 {
                font-size: 13px;
                letter-spacing: 1px;
                color: #ffb020;
                margin-bottom: 10px;
            }

            /* big weight */
            #finalWeight {
                font-size: 60px;
                font-weight: 700;
                margin: 0;
            }

            #finalWeight span {
                font-size: 18px;
                opacity: 0.7;
            }

            /* divider line */
            #resultBox hr {
                border-color: rgb(255 255 255 / 60%);
            }

            /* info rows */
            #resultBox p {
                margin: 8px 0;
                color: #7488a7;
                background: #ffffff14;
                padding: 8px 12px;
                font-size: 14px;
                border-radius: 9px;
            }

            /* highlight box (like 1.68 kg badge) */
            #volWeight {
                background: #1e66ff;
                padding: 4px 10px;
                border-radius: 6px;
                color: #fff;
                font-weight: 600;
            }

            /* small cards inside */
            .result-item {
                background: rgba(255,255,255,0.05);
                padding: 12px 15px;
                border-radius: 10px;
                margin-bottom: 10px;
            }

            /* mobile spacing */
            @media (max-width: 768px) {
                #resultBox {
                    margin-top: 20px;
                }
            }


          .track-cta.light {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 40px 50px;
                border-radius: 20px;

                background: transparent linear-gradient(255deg, #ffc46554, #5338ff26);
                color: #111;

                box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            }

            /* LEFT */
            .track-left {
                max-width: 600px;
            }

            .live-badge {
                display: inline-block;
                background: rgba(34,197,94,0.1);
                color: #16a34a;
                padding: 6px 14px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 600;
                margin-bottom: 15px;
            }

            .track-left h2 {
                font-size: 40px;
                font-weight: 700;
                margin: 0 0 15px;
                color: #0f172a;
            }

            .track-left p {
                color: #475569;
                line-height: 1.6;
                font-size: 15px;
            }

            /* BUTTON */
            .track-btn {
                background: #111827;
                color: #fff;
                border: none;
                padding: 14px 24px;
                border-radius: 12px;
                font-weight: 600;
                cursor: pointer;
                transition: 0.3s;
            }

            .track-btn:hover {
                background: #1f2937;
                transform: translateY(-2px);
            }

            /* RESPONSIVE */
            @media (max-width: 768px) {
                .track-cta.light {
                    flex-direction: column;
                    text-align: center;
                    padding: 40px 20px;
                }

                .track-left h2 {
                    font-size: 26px;
                }

                .track-right {
                    margin-top: 20px;
                }
            }


        </style>




<!-- Hero section -->
<header class="hero-gradient">
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
                    {{ $heroData->data['badge_text'] ?? 'Free Tool · Instant Results' }}
                </div>
                <h1 class="hero-title mb-4">
                    <!-- Volumetric <span class="moving-gradient-text">{!! $heroData->data_title ?? 'Weight Calculator' !!}</span> -->
                    {!! $heroData->data_title ?? 'Weight Calculator' !!}

                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ $heroData->data_description ?? 'Enter your package dimensions to instantly calculate dimensional weight and understand how carriers determine your chargeable weight.' }}
                </p>

                <!-- <a href="{{ $heroData->data['button_url'] ?? '#' }}" class="book-btn-service"><i class="fas fa-calculator"></i> &nbsp; {{ $heroData->data_button_text ?? 'Calculate Now' }}</a> -->

            </div>

            <!-- Right Image -->
            <div class="col-md-6 text-center">
                <div class="hero-graphic">
                    <div class="">
                        <img src="{{ asset('/website_images/image.png') }}" class="img-fluid"
                            style="max-width: 100%; height: auto;">
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>




<!-- Calculator section -->

<section class="py-5 bg-white">
    <div class="container">
        <div class="row form-shadow ">
            <div class="col-md-8">
                <div class="mx-auto">
                    <div class="mb-4">
                        <h3 class="h4-title">Enter Package <span class="gradient-text">Dimensions</span></h3>
                    </div>

                    <form>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Length (cm)</label>
                                <div class="input-group-custom">
                                    <input type="number" class="form-control input-custom" placeholder="eg: 30">
                                    <i class="fa-solid fa-ruler-combined"></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">width (cm)</label>
                                <div class="input-group-custom">
                                    <input type="number" class="form-control input-custom" placeholder="eg: 20">
                                    <i class="fa-solid fa-ruler-horizontal"></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Height (cm)</label>
                                <div class="input-group-custom">
                                    <input type="number" class="form-control input-custom" placeholder="eg: 15">
                                    <i class="fa-solid fa-ruler-vertical"></i>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Divisor</label>
                                <div style="display: block;" class="input-group-custom">
                                    @if($calculator && isset($calculator->data['divisor_options']))
                                        @foreach($calculator->data['divisor_options'] as $option)
                                        <label class="m-1"
                                            style="width: {{ $option['width'] ?? '145px' }}; background: #2662eb21; padding: 4px 8px; border-radius: 37px;">
                                            <input type="radio" name="divisor" value="{{ $option['value'] }}"> {{ $option['text'] }}
                                        </label>
                                        @endforeach
                                    @else
                                        <label class="m-1"
                                            style="width: 105px; background: #2662eb21; padding: 4px 8px; border-radius: 37px;"><input
                                                type="radio" name="divisor" value="5000"> 5000 Air</label>
                                        <label class="m-1"
                                            style="width: 145px; background: #2662eb21; padding: 4px 8px; border-radius: 37px;"><input
                                                type="radio" name="divisor" value="400"> 400 Express</label>
                                        <label class="m-1"
                                            style="width: 135px; background: #2662eb21; padding: 4px 8px; border-radius: 37px;"><input
                                                type="radio" name="divisor" value="6000"> 6000 Sea</label>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button type="button" style="width: 240px;"
                            class="btn moving-gradient-bg btn-primary-custom m-2">
                            Calculate Weight
                        </button>


                        <button type="button" style="width: 150px; color: #525252; border: 1px solid #525252;"
                            class="btn btn-primary-custom m-2">
                            Reset
                        </button>
                    </form>
                    <p style="background:#f3f3f3; padding: 10px 15px; margin-top: 20px; font-weight: 600; border-radius: 20px; width: fit-content;" >Formula:(L × W × H) ÷ 5000</p>

                </div>
            </div>


            <div class="col-md-4" id="resultBox">
                <div>
                    <h5>VOLUMETRIC WEIGHT</h5>
                    <h1 id="finalWeight">2 kg</h1>
                    <hr syle="color=#fff">
                    <p id="divisorText">Divisor 5000 used (standard air freight).</p>

                    <div>
                        <p>Volume (L × W × H) : 30 cm <span id="volume"></span></p>
                        <p>Divisor used: 15 cm <span id="divisorUsed"></span></p>
                        <p>Volumetric Weight: <span id="volWeight">2 kg</span></p>
                    </div>

                    <div class="mt-4">
                        <h6>Medium Package</h6>
                        <p>Mid-size package — check actual weight; carriers charge the higher of the two.</p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>





<!-- Features cards section -->

<section class="features-section" style="padding:40px 0;">
    <div class="container">
        
        <div class="row justify-content-center mb-3">
            <div class="col-lg-10 text-center">
                <h2 class="about-title">{{ $featuresHeader->data_title ?? 'Understanding volumetric weight' }}</h2>
                
                <p class="about-desc text-center">
                    {{ $featuresHeader->data_description}}
                </p>
            </div>
        </div>

        <div class="row g-4">
            @foreach($features as $feature)
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon fi-blue"><i class="{{ $feature->data_icon ?? 'fa-solid fa-ruler' }}"></i></div>
                        <h5>{{ $feature->data_title ?? 'Feature Title' }}</h5>
                        <p>{{ $feature->data_description ?? 'Feature description' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>




<div class="container my-5" style="display:none">
    <div class="row">
        <div class="col-12">

            <section class="track-cta light">
                <div class="track-left">
                    <span class="live-badge">{{ $trackCta->data['live_badge'] ?? '● LIVE TRACKING' }}</span>

                    <h2>{{ $trackCta->data_title ?? 'Track any shipment in real-time' }}</h2>

                    <p>
                        {{ $trackCta->data_description ?? 'Get live updates across all major carriers — end-to-end visibility from pickup to delivery for every order you export worldwide.' }}
                    </p>
                </div>

                <div class="track-right">
                    <button class="track-btn">
                        {{ $trackCta->data_button_text ?? 'Track Shipment →' }}
                    </button>
                </div>
            </section>

        </div>
    </div>
</div>




<!-- testimonial -->
<section class="testimonial-section" style="display:none">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-3">
            <div class="col-lg-8 text-center">
                <div class="google-badge">
                    <a href="{{ $testimonialsHeader->data['badge_url'] ?? '#' }}">
                        <img src="{{ asset($testimonialsHeader->data['badge_image'] ?? 'public/website_images/google-review.png') }}" alt="{{ $testimonialsHeader->data['badge_alt'] ?? 'Google' }}">
                    </a>
                </div>
                <h2 class="about-title">{{ $testimonialsHeader->data_title ?? 'Trusted by the Brands You Trust' }}</h2>

                <p class="about-desc text-center">
                    {{ $testimonialsHeader->data_description ?? 'Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record speaks for itself. From on-time delivery and zero-compromise on quality to customer satisfaction, United Worldwide Couriers is completely reliable and trustworthy.' }}
                </p>
            </div>
        </div>

        <div class="slider-wrapper">
            <div class="slider-track">
                @foreach($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="stars">{{ str_repeat('★', $testimonial->rating ?? 5) }}</div>
                    <p class="testimonial-text">"{!! $testimonial->content !!}"</p>
                    <div class="user-info">
                        <img src="{{ asset($testimonial->customer_image ?? 'public/website_images/review-1.png') }}" class="img-fluid">
                        <h6>{{ $testimonial->customer_name ?? 'Customer Name' }}</h6>
                    </div>
                </div>
                @endforeach

                <!-- Duplicate for seamless loop -->
                @foreach($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="stars">{{ str_repeat('★', $testimonial->rating ?? 5) }}</div>
                    <p class="testimonial-text">"{!! $testimonial->content !!}"</p>
                    <div class="user-info">
                        <img src="{{ asset($testimonial->customer_image ?? 'public/website_images/review-1.png') }}" class="img-fluid">
                        <h6>{{ $testimonial->customer_name ?? 'Customer Name' }}</h6>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</section>



<!-- FAQ Section -->
<section class="faq-section" style="display:none">
    <div class="container">
        <div class="faq-header">
            <span class="heading-badge">Common Questions</span>
            <h2 class="about-title">Frequently Asked Questions</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
               @include('website_include.faq-support-form')
           </div>


            <div class="col-lg-8">
                <div class="accordion" id="logisticsFaq" style="height: 70vh; overflow-y: auto;">
                    @foreach($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq{{ $index + 1 }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq{{ $index + 1 }}" class="accordion-collapse {{ $index === 0 ? 'show' : 'collapse' }}" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                {!! $faq->answer !!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>






<!-- MOUSE HOVER GREDIENT EFFECT ON MISSION AND VISSION SCRIPS -->

<script>
// Track mouse position for card glow effect
document.querySelectorAll('.purpose-card').forEach(card => {
    card.addEventListener('mousemove', e => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const glow = card.querySelector('.card-glow');
        glow.style.left = `${x - 200}px`;
        glow.style.top = `${y - 200}px`;
    });
});

// Intersection Observer for reveal animations
const observer3 = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
        }
    });
}, {
    threshold: 0.2
});

document.querySelectorAll('.reveal').forEach(el => observer3.observe(el));
</script>


<!-- TIMELINE JOURNEY SCRIPT -->

<script>
// Highlighting active timeline items on scroll
const observerOptions = {
    threshold: 0.4,
    rootMargin: "0px 0px -10% 0px"
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
        }
    });
}, observerOptions);

document.querySelectorAll('.timeline-item').forEach(item => observer.observe(item));
</script>

<!-- VOLUMETRIC CALCULATOR SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lengthInput = document.querySelector('input[placeholder="eg: 30"]');
    const widthInput = document.querySelector('input[placeholder="eg: 20"]');
    const heightInput = document.querySelector('input[placeholder="eg: 15"]');
    const calculateBtn = document.querySelector('.btn.moving-gradient-bg');
    const resetBtn = document.querySelector('.btn:not(.moving-gradient-bg)');
    const finalWeightEl = document.getElementById('finalWeight');
    const volumeEl = document.getElementById('volume');
    const divisorUsedEl = document.getElementById('divisorUsed');
    const volWeightEl = document.getElementById('volWeight');
    const divisorTextEl = document.getElementById('divisorText');

    // Set default divisor to 5000 (Air)
    document.querySelector('input[value="5000"]').checked = true;

    function calculateVolumetricWeight() {
        const length = parseFloat(lengthInput.value) || 0;
        const width = parseFloat(widthInput.value) || 0;
        const height = parseFloat(heightInput.value) || 0;
        
        if (length <= 0 || width <= 0 || height <= 0) {
            alert('Please enter valid dimensions for all fields.');
            return;
        }

        const selectedDivisor = document.querySelector('input[name="divisor"]:checked');
        const divisor = parseFloat(selectedDivisor.value) || 5000;
        
        const volume = length * width * height;
        const volumetricWeight = volume / divisor;
        
        // Update display
        finalWeightEl.innerHTML = volumetricWeight.toFixed(2) + ' <span>kg</span>';
        volumeEl.textContent = volume.toFixed(0) + ' cm³';
        divisorUsedEl.textContent = divisor;
        volWeightEl.textContent = volumetricWeight.toFixed(2) + ' kg';
        
        // Update divisor description
        let divisorDescription = '';
        switch(divisor) {
            case 5000:
                divisorDescription = 'Divisor 5000 used (standard air freight).';
                break;
            case 400:
                divisorDescription = 'Divisor 400 used (express courier).';
                break;
            case 6000:
                divisorDescription = 'Divisor 6000 used (sea freight).';
                break;
            default:
                divisorDescription = `Divisor ${divisor} used.`;
        }
        divisorTextEl.textContent = divisorDescription;

        // Add animation to result box
        const resultBox = document.getElementById('resultBox');
        resultBox.style.animation = 'none';
        setTimeout(() => {
            resultBox.style.animation = 'fadeIn 0.5s ease-in';
        }, 10);
    }

    function resetCalculator() {
        lengthInput.value = '';
        widthInput.value = '';
        heightInput.value = '';
        document.querySelector('input[value="5000"]').checked = true;
        
        finalWeightEl.innerHTML = '2 <span>kg</span>';
        volumeEl.textContent = '';
        divisorUsedEl.textContent = '';
        volWeightEl.textContent = '2 kg';
        divisorTextEl.textContent = 'Divisor 5000 used (standard air freight).';
    }

    // Event listeners
    if (calculateBtn) {
        calculateBtn.addEventListener('click', calculateVolumetricWeight);
    }
    
    if (resetBtn) {
        resetBtn.addEventListener('click', resetCalculator);
    }

    // Add enter key support for inputs
    [lengthInput, widthInput, heightInput].forEach(input => {
        if (input) {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    calculateVolumetricWeight();
                }
            });
        }
    });
});

// Add fade in animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0.5; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);
</script>

@include('website_include.footer')