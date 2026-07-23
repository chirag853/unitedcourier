@include('website_include.header')


<style>
.cke_notification_message{
    display: none !important;
}

.newtowk-detail-section {
    background-color: #f7f7ff;
}

.section-container {
    padding: 50px;
    0px;
}

.section-header {
    font-size: 1.75rem;
    margin-bottom: 30px;
    position: relative;
    display: inline-block;
    padding-bottom: 10px;
}

.section-header::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(to right, #2563eb, #9333ea);
    border-radius: 2px;
}

.office-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    height: 100%;
    background: #fff;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
}

.office-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
}

.office-header {
    padding: 12px 16px;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.025em;
}

.contact-table {
    width: 100%;
    margin-bottom: 0;
    font-size: 0.8rem;
}

.contact-table td {
    padding: 10px 12px;
    vertical-align: top;
    border-bottom: 1px solid #f8fafc;
}

.contact-table td:first-child {
    width: 90px;
    font-weight: 600;
    color: var(--text-muted);
    background-color: #fcfcfd;
    border-right: 1px solid #f1f5f9;
}

.address-text {
    line-height: 1.5;
    color: var(--text-dark);
    font-weight: 500;
    text-transform: uppercase;
}

.email-link,
.tel-link {
    color: var(--brand-blue-main);
    text-decoration: none;
    font-weight: 500;
}

.email-link:hover,
.tel-link:hover {
    text-decoration: underline;
}
</style>

<!-- Hero section -->
<header style="min-height: 0vh;" class="hero-gradient">
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
    </div>
    <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;">
    </div>

    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <!-- Content -->
            <div class="col-md-12 text-center animate__animated animate__fadeInLeft">
                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    Global Presence
                </div>
                <h1 class="hero-title mb-4">
                    Our Global <span class="moving-gradient-text"> Network.</span>
                </h1>
                <p style="max-width: 100%;" class="text-center mb-5 lead">
                    Since 1994, United Worldwide Couriers has built a logistics network that reaches every corner of the world. We've been connecting businesses to global markets through strategic hubs, trusted carrier partnerships, and three decades of on-ground expertise.
                </p>
            </div>

        </div>
    </div>
</header>

<section class="newtowk-detail-section">
    <div class="container section-container">


        <h2 class="section-header gradient-text">India Offices</h2>

        <div class="row g-4 mb-5">
            @forelse ($indiaOffices as $office)
            <div class="col-md-6 col-lg-4">
                <div class="office-card">
                    <div class="office-header moving-gradient-bg">{{ $office->name }}</div>
                    <table class="contact-table">
                        <tr>
                            <td>Address</td>
                            <td class="address-text">{{ $office->address }}</td>
                        </tr>
                        @if($office->telephone)
                        <tr>
                            <td>Tel</td>
                            <td><a href="tel:{{ $office->telephone }}" class="tel-link">{{ $office->telephone }}</a>
                            </td>
                        </tr>
                        @endif
                        @if($office->mobile)
                        <tr>
                            <td>Mob</td>
                            <td><a href="tel:{{ $office->mobile }}" class="tel-link">{{ $office->mobile }}</a></td>
                        </tr>
                        @endif
                        @if($office->fax)
                        <tr>
                            <td>Fax</td>
                            <td>{{ $office->fax }}</td>
                        </tr>
                        @endif
                        @if($office->email)
                        <tr>
                            <td>Email</td>
                            <td><a href="mailto:{{ $office->email }}" class="email-link">{{ $office->email }}</a></td>
                        </tr>
                        @endif
                        @if($office->contact_person)
                        <tr>
                            <td>Contact</td>
                            <td>{{ $office->contact_person }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-center text-muted">No India offices available at the moment.</p>
            </div>
            @endforelse
        </div>

        <h2 class="section-header gradient-text">Overseas Offices</h2>

        <div class="row g-4">
            @forelse ($overseasOffices as $office)
            <div class="col-md-6 col-lg-4">
                <div class="office-card">
                    <div class="office-header moving-gradient-bg">{{ $office->name }}</div>
                    <table class="contact-table">
                        <tr>
                            <td>Address</td>
                            <td class="address-text">{{ $office->address }}</td>
                        </tr>
                        @if($office->telephone)
                        <tr>
                            <td>Tel</td>
                            <td><a href="tel:{{ $office->telephone }}" class="tel-link">{{ $office->telephone }}</a>
                            </td>
                        </tr>
                        @endif
                        @if($office->mobile)
                        <tr>
                            <td>Mob</td>
                            <td><a href="tel:{{ $office->mobile }}" class="tel-link">{{ $office->mobile }}</a></td>
                        </tr>
                        @endif
                        @if($office->fax)
                        <tr>
                            <td>Fax</td>
                            <td>{{ $office->fax }}</td>
                        </tr>
                        @endif
                        @if($office->email)
                        <tr>
                            <td>Email</td>
                            <td><a href="mailto:{{ $office->email }}" class="email-link">{{ $office->email }}</a></td>
                        </tr>
                        @endif
                        @if($office->contact_person)
                        <tr>
                            <td>Contact</td>
                            <td>{{ $office->contact_person }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-center text-muted">No overseas offices available at the moment.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>






<!-- testimonial -->
<section class="testimonial-section">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-3">
            <div class="col-lg-8 text-center">
                <div class="google-badge">
                    <a href="#">
                        <img src="{{ asset('website_images/google-review.png') }}" alt="Google">
                    </a>
                </div>
                <h2 class="about-title">Trusted by Businesses. Rated by Customers</h2>

                <p class="about-desc text-center">
                    For over 30 years, United Worldwide Couriers has supported businesses and individuals with secure, timely, and dependable logistics solutions. Our clients trust us for consistent service, transparent communication, careful handling, and smooth delivery experiences across domestic and international shipments.
                </p>
            </div>
        </div>

        <div class="slider-wrapper">
            <div class="slider-track">
                @forelse($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="stars">{{ str_repeat('★', $testimonial->rating ?? 5) }}</div>
                    <p class="testimonial-text">"{!! $testimonial->content !!}"</p>
                    <div class="user-info">
                        <img src="{{ asset($testimonial->customer_image ?? 'public/website_images/review-1.png') }}"
                            class="img-fluid">
                        <h6>{{ $testimonial->customer_name }}</h6>
                    </div>
                </div>
                @endforeach

                <!-- Duplicate for seamless loop -->
                @forelse($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="stars">{{ str_repeat('★', $testimonial->rating ?? 5) }}</div>
                    <p class="testimonial-text">"{!! $testimonial->content !!}"</p>
                    <div class="user-info">
                        <img src="{{ asset($testimonial->customer_image ?? 'public/website_images/review-1.png') }}"
                            class="img-fluid">
                        <h6>{{ $testimonial->customer_name }}</h6>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>



<!-- FAQ Section -->
<section class="faq-section">
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
                    @forelse($faqs as $index => $faq)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq{{ $loop->iteration }}">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="faq{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#logisticsFaq">
                                <div class="accordion-body">
                                    {!! $faq->answer !!}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <p class="text-muted">No FAQs available at the moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>



@include('website_include.footer')