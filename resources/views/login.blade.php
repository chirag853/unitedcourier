@include('customer.partials.header')

    <div class="hero-gradient-container" style="margin-top: 70px;">
        <!-- Floating Elements -->
        <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;"></div>
        <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;"></div>

        <div class="container">
            <div class="row align-items-center justify-content-center g-5">

                <!-- Left Content -->
                <div class="col-lg-7 animate__animated animate__fadeInLeft d-none d-lg-block">
                    <h1 class="hero-title mb-1">
                        Welcome to the <span class="moving-gradient-text">Global Network.</span>
                    </h1>
                    
                    <p class="mt-3 text-muted">
                        Sign in to access your business dashboard and manage your international shipping logistics with ease.
                    </p>
                    
                    <!-- Global Network Image Integration
                    <img style="max-width: 55%; opacity: 0.8;" src="images/login-img.webp" class="hero-illustration"> -->
                    
                    <div class="row g-3 mb-5">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="text-primary fs-4"><i class="fas fa-lock"></i></div>
            <div>
                <div class="fw-bold text-dark" style="font-size: 14px;">Secure Access</div>
                <div class="text-muted" style="font-size: 14px;">
                    Advanced encryption ensures your account and shipment data stay fully protected.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="stat-card">
            <div class="text-success fs-4"><i class="fas fa-bolt"></i></div>
            <div>
                <div class="fw-bold text-dark" style="font-size: 14px;">Real-time Sync</div>
                <div class="text-muted" style="font-size: 14px;">
                    Get instant updates and live tracking for all your shipments anytime.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="stat-card">
            <div class="text-success fs-4"><i class="fa-solid fa-headset"></i></div>
            <div>
                <div class="fw-bold text-dark" style="font-size: 14px;">24/7 Support</div>
                <div class="text-muted" style="font-size: 14px;">
                    Our support team is available round the clock to assist you with any queries.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="stat-card">
            <div class="text-primary fs-4"><i class="fa-solid fa-globe"></i></div>
            <div>
                <div class="fw-bold text-dark" style="font-size: 14px;">Global Shipping</div>
                <div class="text-muted" style="font-size: 14px;">
                    Delivering your shipments reliably across 220+ countries worldwide.
                </div>
            </div>
        </div>
    </div>
</div>

                    

                </div>

                <!-- Login Form -->
                <div class="col-lg-5 col-md-10 animate__animated animate__fadeInRight">
                    <div class="form-shadow mx-auto">
                        
                        <div class="mb-4">
                            <h3 class="h4-title">Login to <span class="gradient-text">Your Business Account</span></h3>
                            <p class="text-muted small">Verify your mobile number to continue</p>
                        </div>

                        <form id="registrationForm">

                            <!-- Mobile Row with OTP -->
                            <div class="mb-4">
                                <label class="form-label-custom">Mobile Number</label>
                                <div class="input-group-custom">
                                    <input type="tel" id="mobileInput" class="form-control input-custom" placeholder="+91 XXXXX XXXXX" required>
                                    <i class="fas fa-phone"></i>
                                    <button type="button" class="btn-otp" onclick="sendOTP()">Get OTP</button>
                                </div>
                                <div id="otpStatus" class="otp-sent-text"><i class="fas fa-check-circle"></i> Verification code sent!</div>
                            </div>

                            <!-- OTP Input (Conditional) -->
                            <div class="mb-4 animate__animated animate__fadeIn" id="otpContainer" style="display: none;">
                                <label class="form-label-custom">Enter 6-Digit OTP</label>
                                <div class="input-group-custom">
                                    <input type="text" class="form-control input-custom" placeholder="XXXXXX" maxlength="6">
                                    <i class="fas fa-key"></i>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label small text-muted" for="rememberMe">
                                    By clicking on Login, I accept the Terms & Conditions and Privacy Policy.
                                </label>
                            </div>

                            <button type="submit" class="btn moving-gradient-bg btn-primary-custom">
                                Login Now
                            </button>
                        </form>

                        <div class="auth-footer-links">
                            New to United Couriers? <a href="register.php">Create an Account</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <script>
        // Initialize Lenis
        const lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smoothWheel: true,
        });

        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        // OTP Simulation
        function sendOTP() {
            const status = document.getElementById('otpStatus');
            const container = document.getElementById('otpContainer');
            const btn = document.querySelector('.btn-otp');
            
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';
            
            setTimeout(() => {
                status.style.display = 'block';
                container.style.display = 'block';
                btn.innerHTML = 'Resend';
            }, 800);
        }

        // Form Submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Authenticating...';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';

            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Success!';
                btn.style.backgroundColor = '#10b981';
                btn.classList.remove('moving-gradient-bg');
                btn.style.opacity = '1';
                // Here you would typically redirect to dashboard
            }, 2000);
        });
    </script>

@include('customer.partials.footer')