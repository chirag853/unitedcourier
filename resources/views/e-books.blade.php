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
    background-image: radial-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px);
    background-size: 18px 18px;
    opacity: 0.3;
}

/* content above pattern */
#resultBox>* {
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
    background: rgba(255, 255, 255, 0.05);
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

    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}

/* LEFT */
.track-left {
    max-width: 600px;
}

.live-badge {
    display: inline-block;
    background: rgba(34, 197, 94, 0.1);
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
@php
$heroBadgeText = $heroContent->content['badge_text'] ?? 'Read our ebooks';
$heroTitle = $heroContent->content['title'] ?? 'eBooks for <span class="moving-gradient-text">Exporters</span>';
$heroSubtitle = $heroContent->content['subtitle'] ?? 'Must-read guides, handpicked for their popularity among global
exporters';
$heroImage = $heroContent->content['image'] ?? 'images/e-books.webp';
@endphp
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
                    {{ $heroBadgeText }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $heroTitle !!}
                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ $heroSubtitle }}
                </p>

            </div>

            <!-- Right Image -->
            <div class="col-md-6 text-center">
                <div class="hero-graphic">
                    <div class="">
                        <img src="{{ asset( $heroImage) }}" class="img-fluid" style="width:70%">
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>







<style>
.uwd-section-container {
    max-width: 1200px;
    margin: auto;
    padding: 40px 20px;
}

.uwd-feature-block {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
    padding: 50px;
    border-radius: 20px;
    margin-bottom: 40px;
    background: #eef1ff;
}

/* Alternate background colors */
.uwd-feature-block:nth-child(2) {
    background: #fffaec;
}

.uwd-feature-block:nth-child(3) {
    background: #e4edf5;
}

.uwd-feature-text {
    flex: 1;
}

.uwd-feature-text h2 {
    font-size: 28px;
    margin-bottom: 15px;
    font-weight: 700;
}

.uwd-feature-text p {
    font-size: 15px;
    color: #555;
    margin-bottom: 20px;
    max-width: 450px;
}

.uwd-btn {
    display: inline-block;
    background: #6c4ce3;
    color: #fff;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 14px;
    text-decoration: none;
    transition: 0.3s;
}

.uwd-btn:hover {
    background: #5a3bd1;
}

.uwd-feature-image {
    flex: 1;
    display: flex;
    justify-content: center;
}

.uwd-feature-image img {
    width: 220px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Reverse layout */
.reverse {
    flex-direction: row-reverse;
}

/* 📱 Responsive */
@media (max-width: 768px) {
    .uwd-feature-block {
        flex-direction: column;
        text-align: center;
        padding: 30px 20px;
    }

    .reverse {
        flex-direction: column;
    }

    .uwd-feature-text p {
        max-width: 100%;
    }

    .uwd-feature-image img {
        width: 180px;
    }
}


#save {
    background: #6c4ce3;
    color: #fff;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 14px;
    text-decoration: none;
    transition: 0.3s;
}
</style>




<div class="uwd-section-container">

    @php
    $sectionTitle = $sectionHeader->content['title'] ?? 'Grow your knowledge to grow your business';
    $sectionDescription = $sectionHeader->content['description'] ?? 'EGet your hands on our eBooks and learn about
    everything required to grow your business. Be it marketing, sales, logistics, or social media. Access the A to Z
    guides for guaranteed business growth.. Join our growing network of satisfied clients who depend on us for easy,
    secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record
    speaks for itself.';
    @endphp

    <div class="col-lg-12 text-center">
        <h2 class="about-title">{{ $sectionTitle }}</h2>
        <p class="about-desc text-center">
            {{ $sectionDescription }}
        </p>
    </div>

    @forelse($ebooks as $ebook)
    <div class="uwd-feature-block @if($loop->index % 2 == 1) reverse @endif">
        @if($loop->index % 2 == 0)
        <div class="uwd-feature-image">
            @if($ebook->image)
            <img src="{{ asset( $ebook->image) }}" alt="{{ $ebook->title }}">
            @else
            <img src="{{ asset('public/website_images/book-1.webp') }}" alt="{{ $ebook->title }}">
            @endif
        </div>
        @endif
        <div class="uwd-feature-text">
            <h2>{{ $ebook->title }}</h2>
            <p>{{ $ebook->description }}</p>
            <a href="javascript:void(0)" onclick="openPdfModal('{{ $ebook->link ? asset($ebook->link) : '' }}')"
                class="uwd-btn">Read more</a>
        </div>
        @if($loop->index % 2 == 1)
        <div class="uwd-feature-image">
            @if($ebook->image)
            <img src="{{ asset( $ebook->image) }}" alt="{{ $ebook->title }}">
            @else
            <img src="{{ asset('public/website_images/book-1.webp') }}" alt="{{ $ebook->title }}">
            @endif
        </div>
        @endif
    </div>
    @empty
    <div class="text-center py-5">
        <p class="text-muted">No e-books available at the moment.</p>
    </div>
    @endforelse

</div>




<!-- FAQ Section -->
@php
$faqBadge = $faqHeader->content['badge'] ?? 'Common Questions';
$faqTitle = $faqHeader->content['title'] ?? 'Frequently Asked Questions';
$faqSidebarImage = $faqHeader->content['sidebar_image'] ??
'https://i.pinimg.com/originals/f6/5d/46/f65d4681649d85bc91c86872a1775919.gif';
$faqSidebarTitle = $faqHeader->content['sidebar_title'] ?? 'Need personalized help?';
$faqSidebarDescription = $faqHeader->content['sidebar_description'] ?? 'Our logistics experts are available 24/7 to
assist your requirements.';
$faqContactBoxTitle = $faqHeader->content['contact_box_title'] ?? 'Contact Us';
$faqContactBoxDescription = $faqHeader->content['contact_box_description'] ?? 'For urgent inquiries regarding your
current shipment status.';
$faqContactButtonText = $faqHeader->content['contact_button_text'] ?? 'Message Support';
@endphp
<section class="faq-section">
    <div class="container">
        <div class="faq-header">
            <span class="heading-badge">{{ $faqBadge }}</span>
            <h2 class="about-title">{{ $faqTitle }}</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
               @include('website_include.faq-support-form')
           </div>


            <div class="col-lg-8">
                <div class="accordion" id="logisticsFaq" style="height: 70vh; overflow-y: auto;">
                    @forelse($faqs as $faq)
                    @php
                    $faqId = 'faq' . $loop->iteration;
                    @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#{{ $faqId }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="{{ $faqId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                            data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                {!! $faq->answer !!}
                            </div>
                        </div>
                    </div>
                    @empty
                    <!-- Fallback static FAQ items -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq1">
                                How do I get started?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                To connect with our team, you have to register yourself, get a quote, and schedule your
                                first pickup. Thereafter, the team will guide you through every step of the process.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq2">
                                How does United Worldwide Couriers meet your shipping and logistics needs?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                To provide the best freight management solutions, we work with broad strategies,
                                technologies, and services to simplify the planning, storage, and movement of goods.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq3">
                                What packaging standards should we follow for shipping?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#logisticsFaq">
                            <div class="accordion-body">
                                We utilize study and secure packaging for small to large packages to protect your goods
                                during transit. In case of fragile items, they will be cushioned enough and clearly
                                labelled as "Fragile". In addition, we also provide a packaging and labelling guide for
                                all new on boarders for no confusion and faster results.
                            </div>
                        </div>
                    </div>
                    @endforelse
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

<script>
function openPdfModal(pdfUrl) {
    if (!pdfUrl) {
        showAlert('No PDF available for this e-book.', 'warning');
        return;
    }
    const iframe = document.getElementById('pdfViewer');
    // + '#toolbar=0&navpanes=0&scrollbar=0';
    iframe.src = pdfUrl + '#toolbar=1`';
    const modal = new bootstrap.Modal(document.getElementById('pdfModal'));
    modal.show();
}

// Clear iframe src when modal is hidden so it stops loading
document.addEventListener('DOMContentLoaded', function() {
    const pdfModal = document.getElementById('pdfModal');
    pdfModal.addEventListener('hidden.bs.modal', function() {
        document.getElementById('pdfViewer').src = '';
    });
});
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



<!-- PDF Preview Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">E-Book PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <iframe id="pdfViewer" src="" style="width:100%;height:100%;border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

@include('website_include.footer')