@include('website_include.header')



<style>
:root {
    --uwd-primary: #2563eb;
    --uwd-primary-dark: #1d4ed8;
    --uwd-text-main: #0f172a;
    --uwd-text-muted: #64748b;
    --uwd-bg: #ffffff;
    --uwd-card-border: #e2e8f0;
}


.contact-container {
    max-width: 1140px;
    margin: 0 auto;
    padding: 0 15px;
}

.contact-wrapper {
    background: #fff;
    border-radius: 30px;
    box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    border: 1px solid var(--uwd-card-border);
}

/* Left Side: Info Panel */
.contact-info-panel {
    background: linear-gradient(to right, #2563eb, #9333ea);
    color: white;
    padding: 50px;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.info-item {
    display: flex;
    gap: 20px;
    margin-bottom: 35px;
}

.info-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.info-content h5 {
    font-family: 'Outfit', sans-serif;
    margin-bottom: 5px;
    font-size: 1.1rem;
}

.info-content p,
.info-content a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    margin-bottom: 0;
    font-size: 0.95rem;
}

.social-links {
    margin-top: auto;
    display: flex;
    gap: 15px;
    padding-top: 30px;
}

.social-link {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: 0.3s;
}

.social-link:hover {
    background: white;
    color: var(--uwd-primary);
    transform: translateY(-3px);
}

/* Right Side: Form Panel */
.contact-form-panel {
    padding: 50px;
}

.form-label {
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--uwd-text-main);
    margin-bottom: 8px;
}

.form-control,
.form-select {
    padding: 12px 18px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    font-size: 0.95rem;
    background-color: #f8fafc;
}

.form-control:focus {
    background-color: #fff;
    border-color: var(--uwd-primary);
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

.btn-send {
    background: var(--uwd-primary);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 12px;
    font-weight: 700;
    width: 100%;
    transition: 0.3s;
    margin-top: 10px;
}

.btn-send:hover {
    background: var(--uwd-primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
}

/* Map Section Styling */
.map-section {
    width: 100%;
    height: 400px;
    border-top: 1px solid var(--uwd-card-border);
    border-bottom: 1px solid var(--uwd-card-border);
    filter: grayscale(10%) contrast(90%);
}

#form-success {
    display: none;
    text-align: center;
    padding: 40px 0;
}

@media (max-width: 991px) {

    .contact-info-panel,
    .contact-form-panel {
        padding: 40px 30px;
    }

    .map-section {
        height: 300px;
    }
}
</style>



<!-- Hero section -->
<header style="min-height: 20vh; padding-top: 130px; padding-bottom: 30px;" class="hero-gradient">
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
    </div>
    <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;">
    </div>

    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <div class="col-lg-12 text-center animate__animated animate__fadeInLeft">
                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    Contact Us
                </div>
                <h1 class="hero-title mb-4">
                    {!! $pageMeta->title ?? 'Landed Here <span class="moving-gradient-text"> With a Question?</span>' !!}
                </h1>
                <p style="max-width: 100%;" class="text-center mb-5 lead">
                    {{ $pageMeta->paragraphs ?? 'If you need to know more about our services, or would like to contact someone who can help you with your request, please fill up the form below, and we will contact you as soon as possible.' }}
                </p>

            </div>

        </div>
    </div>
</header>


<div class="contact-container my-5">
    <div class="contact-wrapper">
        <div class="row g-0">
            <!-- Contact Information -->
            <div class="col-lg-5">
                <div class="contact-info-panel">
                    <h3 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif;">Get in Touch</h3>
                    <p class="mb-5" style="color: rgba(255,255,255,0.7)">
                        {{ $contactInfo->paragraphs ?? 'Have a query about your shipment? Our support team is available 24/7 to assist you.' }}
                    </p>

                    <div class="info-item">
                        <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                        <div class="info-content">
                            <h5>Call Us</h5>
                            @if($contactInfo->phone_numbers && count($contactInfo->phone_numbers) > 0)
                                @foreach($contactInfo->phone_numbers as $phone)
                                <p><a href="tel:{{ $phone }}">{{ $phone }}</a></p>
                                @endforeach
                            @else
                                <p><a href="tel:+919999911176">+91-9999911176</a></p>
                                <p><a href="tel:+911146122222">+91-11-46122222</a></p>
                                <p><a href="tel:+911126161261">+91-11-26161261</a></p>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                        <div class="info-content">
                            <h5>Email Us</h5>
                            @if($contactInfo->email_addresses && count($contactInfo->email_addresses) > 0)
                                @foreach($contactInfo->email_addresses as $email)
                                <p><a href="mailto:{{ $email }}">{{ $email }}</a></p>
                                @endforeach
                            @else
                                <p><a href="mailto:info@unitedcouriers.biz">info@unitedcouriers.biz</a></p>
                                <p><a href="mailto:csd@unitedcouriers.biz">csd@unitedcouriers.biz</a></p>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div class="info-content">
                            <h5>Head Office</h5>
                            <p>{{ $contactInfo->address ?? 'Building No. 1, Bypass Road, <br>Mahipalpur New Delhi -110037' }}</p>
                        </div>
                    </div>

                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" class="social-link"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-form-panel">
                    <div id="contact-form-ui">
                        <h4 class="fw-bold mb-4" style="font-family: 'Outfit', sans-serif;">Send us a message</h4>
                        <form id="main-contact-form" action="{{ route('contact-us.submit') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" placeholder="John" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" placeholder="Doe" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" placeholder="john@company.com" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="+91 XXXX XXX XXX" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Service Required</label>
                                <select name="service" class="form-select" required>
                                    <option value="" selected disabled>Select a service</option>
                                    <option>International Shipping</option>
                                    <option>E-commerce Fulfillment</option>
                                    <option>Warehousing</option>
                                    <option>Customs Support</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Your Message</label>
                                <textarea name="message" class="form-control" rows="4" placeholder="How can we help?" required></textarea>
                            </div>
                            <div id="contact-form-error" class="alert alert-danger mb-3" style="display: none;"></div>
                            <button type="submit" class="btn-send">Send Message</button>
                        </form>
                    </div>

                    <!-- Success State -->
                    <div id="form-success">
                        <div class="mb-4">
                            <i class="fa-solid fa-circle-check text-success" style="font-size: 60px;"></i>
                        </div>
                        <h2 class="fw-bold">Sent Successfully</h2>
                        <p class="text-muted">Thank you for your message. Our team will get back to you shortly.</p>
                        <button class="btn btn-outline-primary px-4 rounded-pill mt-3" onclick="location.reload()">Send
                            another message</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-section">
            <iframe
                src="{{ $contactInfo->map_embed_url ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d224026.25888083235!2d77.00429638410489!3d28.677370779651767!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d1b3400f3d737%3A0x746d7d6e610c0779!2sUnited%20worldwide%20courier%20pvt%20ltd!5e0!3m2!1sen!2sin!4v1778586557834!5m2!1sen!2sin' }}"
                width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>

<script>
document.getElementById('main-contact-form').addEventListener('submit', function (event) {
    event.preventDefault();

    const form = this;
    const button = form.querySelector('button[type="submit"]');
    const errorBox = document.getElementById('contact-form-error');
    const originalButtonText = button.innerHTML;

    errorBox.style.display = 'none';
    button.disabled = true;
    button.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-2"></i>Sending...';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new FormData(form)
    })
    .then(function (response) {
        return response.json().then(function (data) {
            return { ok: response.ok, data: data };
        });
    })
    .then(function (result) {
        if (!result.ok || !result.data.success) {
            let message = result.data.message || 'Unable to send your message. Please try again.';

            if (result.data.errors) {
                message = Object.values(result.data.errors).flat().join('<br>');
            }

            throw new Error(message);
        }

        form.reset();
        document.getElementById('contact-form-ui').style.display = 'none';
        document.getElementById('form-success').style.display = 'block';
    })
    .catch(function (error) {
        errorBox.innerHTML = error.message;
        errorBox.style.display = 'block';
    })
    .finally(function () {
        button.disabled = false;
        button.innerHTML = originalButtonText;
    });
});
</script>

@include('website_include.footer')