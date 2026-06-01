@include('website_include/header'); ?>

<!-- Hero section -->
<header style="min-height: 50vh;" class="hero-gradient">
        <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
        </div>
        <div class="floating-blob bg-primary opacity-10"
            style="width: 200px; height: 200px; bottom: 10%; right: -100px;"></div>

        <div class="container">
            <div class="row align-items-center g-4 g-lg-5">

                <div class="col-lg-7 animate__animated animate__fadeInLeft">
                    <h1 class="hero-title mb-4">
                        {!! $hero->content['title'] ?? 'Be a Part of the Fastest <br class="d-none d-md-block"> <span class="moving-gradient-text">Growing Ecosystem</span>' !!}
                    </h1>
                    <p class="lead mb-5">
                        {{ $hero->content['description'] ?? 'Maximise your profits by accessing affordable international shipping, and a network that helps you reach customers in every major market.' }}
                    </p>

                    <a href="{{ $hero->content['cta_link'] ?? '#' }}" class="book-btn-service"><i class="fa-solid fa-handshake"></i> &nbsp; {{ $hero->content['cta_text'] ?? 'Join Network' }}</a>

                </div>

                <div class="col-lg-5 animate__animated animate__fadeInRight">
                    <img src="{{ asset($hero->image ?? 'images/partnership.webp') }}" alt="Hero Image" class="rounded-5 img-fluid">
                </div>

            </div>
        </div>
</header>

@if($partnerLogos->count() > 0)
        <div class="logo-slider">
            <div class="logo-track">
                <!-- Original Logos -->
                @foreach($partnerLogos as $logo)
                <div class="logo-item">
                    <img src="{{ $logo->logo_image }}" alt="{{ $logo->alt_text ?? 'Partner Logo' }}">
                </div>
                @endforeach

                <!-- Cloned Logos (for seamless loop) -->
                @foreach($partnerLogos as $logo)
                <div class="logo-item">
                    <img src="{{ $logo->logo_image }}" alt="{{ $logo->alt_text ?? 'Partner Logo' }}">
                </div>
                @endforeach
            </div>
        </div>
@endif

   <header style="background: #fafafa; min-height: 20vh; padding-top: 40px; padding-bottom: 40px;">
        <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
        </div>
        <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;"></div>

        <div class="container">
            <div class="row align-items-center g-4 g-lg-5">

                <div class="col-lg-5 animate__animated animate__fadeInRight">
                    <div class="form-shadow mx-auto">
                        <div class="mb-4">
                            <h3 class="h4-title">{!! $formSection->content['title'] ?? 'Partner with <span class="gradient-text">United Couriers</span>' !!}</h3>
                        </div>

                        <form>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom" placeholder="{{ $formSection->content['first_name_placeholder'] ?? 'First Name' }}">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom" placeholder="{{ $formSection->content['last_name_placeholder'] ?? 'Last Name' }}">
                                        <i class="fas fa-user-tag"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="input-group-custom">
                                        <input type="email" class="form-control input-custom" placeholder="{{ $formSection->content['email_placeholder'] ?? 'Email' }}">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group-custom">
                                        <input type="tel" class="form-control input-custom" placeholder="{{ $formSection->content['phone_placeholder'] ?? 'Phone' }}">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom" placeholder="{{ $formSection->content['company_placeholder'] ?? 'Company Name' }}">
                                        <i class="fa-envelope"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom" placeholder="{{ $formSection->content['message_placeholder'] ?? 'Message' }}">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn moving-gradient-bg btn-primary-custom">
                                {{ $formSection->content['button_text'] ?? 'Become a Partner' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-7 animate__animated animate__fadeInLeft">
                    <h3 class="fs-1 hero-title mb-4">
                        {!! $aboutSection->content['title'] ?? 'Where trust and collaboration <br class="d-none d-md-block"> <span class="moving-gradient-text">create lasting partnerships</span>' !!}
                    </h3>
                    <p class="lead mb-4">
                        {{ $aboutSection->content['description'] ?? 'With us, you\'re joining hands with a brand that values collaboration and long-term success. Together, let\'s create opportunities, expand reach, and build solutions to move businesses forward – across industries.' }}
                    </p>

                    @foreach($features as $feature)
                    <div class="lead d-flex align-items-center gap-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="check-circle" aria-hidden="true" class="lucide lucide-check-circle text-primary"><path d="M21.801 10A10 10 0 1 1 17 3.335"></path><path d="m9 11 3 3L22 4"></path></svg>
                        <span>{{ $feature->title }}</span>
                    </div>
                    @endforeach

                </div>

            </div>
        </div>
</header>            


<!-- About Section -->
<section style="background: #f7faff;" class="about-section text-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="" >
                    <span class="heading-badge animate-on-scroll" data-anim="animate__fadeInRight" style="animation-delay: 0.2s;">{{ $ecosystemSection->content['badge'] ?? 'Our Export Ecosystem Partners' }}</span>
                    <h2 class="about-title">{{ $ecosystemSection->content['title'] ?? 'Powering global commerce through strong, trusted partnerships' }}</h2>
                    <p class="mb-2 about-desc text-center animate-on-scroll" data-anim="animate__fadeInUp" style="animation-delay: 0.5s;">
                        {{ $ecosystemSection->content['description'] ?? 'United Worldwide Couriers works with leading platforms, service providers, institutions, and logistics networks to simplify cross-border trade for Indian exporters and D2C brands. Together, we help businesses sell globally with confidence, speed, and scale.' }}
                    </p>
                </div>
    
            </div>
        </div>
</section>


<style>
    /* Ecosystem Partner Grids (image_85ef1d.png) */
        .uwd-pt-eco-section {
            padding: 30px 0;
            background: #f7faff;
        }

        .uwd-pt-eco-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            height: 100%;
            border: 1px solid var(--uwd-pt-border);
        }

        .uwd-pt-eco-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .uwd-pt-logo-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .uwd-pt-logo-item {
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            min-height: 70px;
            text-align: center;
        }

        .uwd-pt-logo-item span {
            font-weight: 800;
            font-size: 0.9rem;
            color: #94a3b8;
        }

</style>    


<!-- ECOSYSTEM PARTNERS (image_85ef1d.png) -->
    <section class="uwd-pt-eco-section">
        <div class="container">
            
            <div class="row g-4">
                <!-- Worldwide Marketplaces -->
                <div class="col-lg-6">
                    <div class="uwd-pt-eco-card">
                        <h3>{{ $ecosystemSection->content['global_card_title'] ?? 'Worldwide Marketplaces' }}</h3>
                        <div class="uwd-pt-logo-grid">
                            @foreach($ecosystemGlobalCards as $card)
                            <div class="uwd-pt-logo-item"><img src="{{ asset($card->image) }}" class="img-fluid" alt="{{ $card->title ?? 'Marketplace' }}"></div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Our Partners -->
                <div class="col-lg-6">
                    <div class="uwd-pt-eco-card">
                        <h3>{{ $ecosystemSection->content['partner_card_title'] ?? 'Our Partners' }}</h3>
                        <div class="uwd-pt-logo-grid">
                            @foreach($ecosystemPartnerCards as $card)
                            <div class="uwd-pt-logo-item"><img src="{{ asset($card->image) }}" class="img-fluid" alt="{{ $card->title ?? 'Partner' }}"></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>



<!-- FAQ Section -->
 <section class="faq-section">
    <div class="container">
        <div class="faq-header">
            <span class="heading-badge">{{ $faqSection->content['badge'] ?? 'Common Questions' }}</span>
            <h2 class="about-title">{{ $faqSection->content['title'] ?? 'Why Partner with us?' }}</h2>
            <p class="about-desc text-center">{{ $faqSection->content['description'] ?? 'Join India\'s leading logistics brand, where trust and shared success drive lasting partnerships as we empower Indian exporters to go global.' }}</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
               @include('website_include.faq-support-form')
           </div>
            

            <div class="col-lg-8">
              <div class="accordion" id="logisticsFaq" style="height: 70vh; overflow-y: auto;">
                @foreach($faqItems as $faq)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $loop->iteration }}">
                            {{ $faq->question }}
                        </button>
                    </h2>
                    <div id="faq{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#logisticsFaq">
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

@include('website_include/footer'); ?>