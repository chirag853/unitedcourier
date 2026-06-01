@include('website_include.partners-logo-section')





<!-- Newletter subscribe -->
     <section class="newsletter-section">
        <!-- <div class="bg-blob blob-1"></div>
        <div class="bg-blob blob-2"></div> -->
        
        <div class="container">
            <div class="newsletter-card">
                <div class="row align-items-center">
                <!-- Text Side -->
                <div class="col-lg-7 newsletter-content">
                    <h3>Stay Ahead in Logistics</h3>
                    <p>Get the latest shipping information, delivery updates, industry trends, and exclusive service benefits straight to your inbox.</p>
                </div>

                <!-- Action Side -->
                <div class="col-lg-5">
                    <form id="subscribeForm">
                        <div class="subscribe-box">
                            <input type="email" id="userEmail" class="form-input" placeholder="hello@example.com"
                                required>
                            <button type="submit" class="moving-gradient-bg btn-modern">Subscribe</button>
                        </div>
                        <div id="subscribeMessage" class="subscribe-message" style="margin-top: 8px; font-size: 14px; display: none;"></div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </section>




<!-- footer starts here -->
<footer class="moving-gradient-bg main-footer">
    <!-- Animated Flight Path Layer -->
    <div class="animation-container">
        <svg width="100%" height="100%" viewBox="0 0 1200 400" preserveAspectRatio="none">
            <!-- Dashed Path -->
            <path class="flight-path" d="M-50,150 C150,50 350,250 550,150 C750,50 950,250 1200,100" />
            
            <!-- Animated Airplane -->
            <g class="airplane-icon">
                <path d="M21,16L21,14L13,9L13,3.5A1.5,1.5 0 0,0 11.5,2A1.5,1.5 0 0,0 10,3.5L10,9L2,14L2,16L10,13.5L10,19L8,20.5L8,22L11.5,21L15,22L15,20.5L13,19L13,13.5L21,16Z" transform="scale(2) rotate(90 12 12)"/>
            </g>
        </svg>
    </div>

    <div class="container">
        <div class="row g-4">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6 pe-md-5">
                <a href="index.php" class="footer-logo">
                    <img src="{{ asset('/website_images/logo-white.png') }}" class="img-fluid"
                        style="max-width: 240px;">
                </a>
                <!-- <p class="footer-desc">United Worldwide Couriers delivers integrated logistics solutions for modern B2B enterprises, e-commerce brands, and growing businesses. Our services cover international Air Express & Freight, pan-India pickup, customs clearance with documentation support.</p> -->

                <div class="social-links">
                    <a href="#" class="social-btn">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-btn">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-btn">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-btn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>

                <!-- <h4 class="mb-2 footer-heading">India Office</h4>
                <h4 class="footer-heading">Overseas Office</h4> -->

            </div>

            <!-- Services -->
            <div class="col-lg-2 col-md-6">
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="track-order.php">Tracking</a></li>
                    <li><a href="contact-us.php">Contact Us</a></li>
                    <li><a href="terms-and-conditions.php">Terms & Conditions</a></li>
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="refund-and-cancellation-policy.php">Cancellation & Refund Policy</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="col-lg-2 col-md-6">
                <h4 class="footer-heading">Our Services</h4>
                <ul class="footer-links">
                    <li><a href="express-air-freight-solutions.php">Air Freight Solution</a></li>
                    <li><a href="ecommerce-logistics-solutions.php">Ecommerce Logistics Solutions</a></li>
                    <li><a href="warehousing-solutions.php">Warehousing Solutions</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-lg-4 col-md-6">
                <h4 class="footer-heading">Reach Us At</h4>
                <div class="contact-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <div>
                        
                        <a href="mailto:info@unitedcouriers.biz"> info@unitedcouriers.biz </a><br>
                        <a href="mailto:csd@unitedcouriers.biz"> csd@unitedcouriers.biz </a>
                    </div>
                </div>
                <div class="contact-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <div>
                        <a href="#"  Building No. 1, Bypass Road,<br>
                        Mahipalpur New Delhi -110037</a>
                    </div>
                </div>
                <div class="contact-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l2.27-2.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    <div>
                       
                       <a href="tel:+91-9999911176"> +91-9999911176 </a>, 
                       <a href="tel:+91-11-46122222"> +91-11-46122222 </a> <br>
                       <a href="tel:+91-11-26161261"> +91-11-26161261 </a>
                    </div>
                </div>
                

            </div>
        </div>

<style>
.domestic-offices-bar{
    background:#ffffff1a;
    color:#fff;
    border-top:1px solid rgba(255,255,255,0.1);
    border-bottom:1px solid rgba(255,255,255,0.1);
    overflow:hidden;
    border-radius:5px;
}

.office-label{
    font-weight:600;
    white-space:nowrap;
    color:#f8c400;
    font-size:15px;
}

.domestic-offices-bar marquee{
    font-size:14px;
    font-weight:400;
    color:#fff;
}
</style>
        <!-- Domestic Offices Marquee Start -->




<div class="container text-center">
  <div class="row g-2">
    <div class="col-6">
      <div class="domestic-offices-bar py-2">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <span class="office-label me-3">
                    <i class="fa-solid fa-location-dot"></i> Domestic Offices:
                </span>

                <marquee behavior="scroll" direction="left" scrollamount="5">
                    Delhi &nbsp; | &nbsp;
                    Jaipur &nbsp; | &nbsp;
                    Bhopal &nbsp; | &nbsp;
                    Surat &nbsp; | &nbsp;
                    Ahmedabad &nbsp; | &nbsp;
                    Chennai &nbsp; | &nbsp;
                    Agra &nbsp; | &nbsp;
                    Moradabad &nbsp; | &nbsp;
                    Mumbai &nbsp; | &nbsp;
                    Kanpur &nbsp; | &nbsp;
                    Lucknow &nbsp; | &nbsp;
                    Udaipur &nbsp; | &nbsp;
                    Meerut &nbsp; | &nbsp;
                    Roorkee &nbsp; | &nbsp;
                    Jalandhar &nbsp; | &nbsp;
                    Ludhiana
                </marquee>
            </div>
        </div>
    </div>
    </div>
    <div class="col-6">
      <div class="domestic-offices-bar py-2">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <span class="office-label me-3">
                    <i class="fa-solid fa-location-dot"></i> Overseas Offices:
                </span>

                <marquee behavior="scroll" direction="left" scrollamount="5">
                    New York (USA) &nbsp; | &nbsp;
                    France &nbsp; | &nbsp;
                    Germany &nbsp; | &nbsp;
                    Hong-Kong &nbsp; | &nbsp;
                    Nepal &nbsp; | &nbsp;
                    United Kingdom &nbsp; | &nbsp;
                </marquee>
            </div>
        </div>
    </div>
    </div>
  </div>
</div>
<!-- Domestic Offices Marquee End -->


        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="copyright">
                © 2026 United Worldwide Couriers. All rights reserved.
            </div>
            <ul class="legal-links">
                <li class="copyright">Designed & Developed By <a href="https://adomantra.com/"> Adomantra Digital India Pvt Ltd</a></li>
            </ul>
        </div>
    </div>
</footer>


@include('website_include.chatbot')



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Handle Mobile/Tablet Click Logic for Mega Menu -->
<script>
    document.querySelectorAll('.dropdown-mobile > .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (window.innerWidth < 992) {
                e.preventDefault();
                const parent = this.parentElement;
                const menu = this.nextElementSibling;
                
                parent.classList.toggle('active-mobile');
                menu.classList.toggle('active');
            }
        });
    });
</script>


<!-- ANIMATION ON SCROLL OR INTREACTION -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const elements = document.querySelectorAll('.animate-on-scroll');
    
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    let el = entry.target;
                    let anim = el.getAttribute('data-anim');
    
                    el.classList.add('show', 'animate__animated', anim);
                    observer.unobserve(el);
                }
            });
        }, {
            threshold: 0.2
        });
    
        elements.forEach(el => observer.observe(el));
    });
</script>

<script>
    document.getElementById('subscribeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var email = document.getElementById('userEmail').value;
    var button = this.querySelector('button[type="submit"]');
    var msgDiv = document.getElementById('subscribeMessage');
    var originalText = button.innerText;
    button.innerText = 'Subscribing...';
    button.disabled = true;
    msgDiv.style.display = 'none';

    fetch('{{ route("subscribe") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email: email })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        msgDiv.style.display = 'block';
        if (data.success) {
            document.getElementById('userEmail').value = '';
            msgDiv.style.color = '#28a745';
            msgDiv.innerText = data.message;
        } else {
            msgDiv.style.color = '#dc3545';
            msgDiv.innerText = data.message;
        }
    })
    .catch(function(error) {
        msgDiv.style.display = 'block';
        msgDiv.style.color = '#dc3545';
        msgDiv.innerText = 'Something went wrong. Please try again.';
    })
    .finally(function() {
        button.innerText = originalText;
        button.disabled = false;
    });
});
</script>

<!-- Stack section script -->
   <script>
        // 1. Initialize Lenis (Smooth Scroll)
        // Adjusting duration and easing for that "slightly slow & smooth" feel
        const lenis = new Lenis({
            duration: 1.5,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            orientation: 'vertical',
            gestureOrientation: 'vertical',
            smoothWheel: true,
            wheelMultiplier: 0.8, // Slightly lower for more control
            smoothTouch: false,
        })

        function raf(time) {
            lenis.raf(time)
            requestAnimationFrame(raf)
        }
        requestAnimationFrame(raf)

        // 2. GSAP & ScrollTrigger
        gsap.registerPlugin(ScrollTrigger);

        // Notify ScrollTrigger when Lenis scrolls
        lenis.on('scroll', ScrollTrigger.update);

        const cards = gsap.utils.toArray(".card-item");
        
        cards.forEach((card, i) => {
            // Pinning the card
            ScrollTrigger.create({
                trigger: card,
                start: "top 13%",
                pin: true,
                pinSpacing: false,
                endTrigger: ".stack-wrapper",
                end: "bottom bottom",
                anticipatePin: 1
            });

            // Smooth scaling effect synced with smooth scroll
            if (i < cards.length - 1) {
                gsap.to(card, {
                    scale: 0.9, 
                    scrollTrigger: {
                        trigger: cards[i + 1],
                        start: "top bottom", 
                        end: "top center",
                        scrub: 2.5 // High scrub for a heavy, smooth momentum transition
                    }
                });
            }
        });

        window.addEventListener('resize', () => {
            ScrollTrigger.refresh();
        });
   </script>


<!-- marketplace, dropshipping, b2b, Cards tab section SCRIPT -->
    <script>
       function srShowTab(index, element) {
            // Nav active class update
            document.querySelectorAll('.sr-demo-nav-item').forEach(item => item.classList.remove('sr-active'));
            element.classList.add('sr-active');

            // Cards logic
            const cards = document.querySelectorAll('.sr-demo-product-card');
            cards.forEach(card => {
                card.classList.remove('sr-active', 'animate__fadeInLeft');
            });

            const targetCard = document.getElementById('sr-card-' + index);
            targetCard.classList.add('sr-active', 'animate__fadeInLeft');
            
            // Scroll nav on mobile
            element.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
            
        }
    </script>


    
<!-- FACTS counter script -->
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

        const observer2 = new IntersectionObserver((entries) => {
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

        document.querySelectorAll('.stat-card').forEach(card => observer2.observe(card));
    </script>


</body>

</html>