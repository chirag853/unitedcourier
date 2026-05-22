<?php include 'website_include/header.php'; ?>

    <div class="hero-gradient-container" style="margin-top: 70px;">
        <!-- Floating Elements -->
        <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;"></div>
        <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;"></div>

        <div class="container">
            <div class="row align-items-center justify-content-center g-5">

                <!-- Left Content -->
                <div class="col-lg-7 animate__animated animate__fadeInLeft d-none d-lg-block">
                    <h1 class="hero-title mb-1">
                        Join the <span class="moving-gradient-text">Global Network.</span>
                    </h1>
                    
                    <p class="mt-3 text-muted">
                        Set up your account in minutes and start shipping to over 220+ countries with our advanced logistics infrastructure.
                    </p>
                    
                    <!-- Global Network Image Integration -->
                    <img src="https://uat.adomantra.com/united-courier/images/global-network.webp" class="hero-illustration">
                    
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="stat-card">
                                <div class="text-success fs-4"><i class="fas fa-check-circle"></i></div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 14px;">Secure Verification</div>
                                    <div class="text-muted" style="font-size: 14px;">We verify your identity safe with Aadhaar-based authentication.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card">
                                <div class="text-primary fs-4"><i class="fas fa-bolt"></i></div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 14px;">Instant Activation</div>
                                    <div class="text-muted" style="font-size: 14px;">Just complete your profile to activate your account instantly.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Registration Form -->
                <div class="col-lg-5 col-md-10 animate__animated animate__fadeInRight">
                    <div class="form-shadow mx-auto">
                        
                        <div class="mb-4">
                            <h3 class="h4-title">Create <span class="gradient-text">Business Account</span></h3>
                            <p class="text-muted small">Fill in your details to get started</p>
                        </div>

                        <form id="registrationForm">
                            <!-- Name Row -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label-custom">First Name</label>
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom" placeholder="Rahul" required>
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Last Name</label>
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom" placeholder="Kumar" required>
                                        <i class="fas fa-user-tag"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Email Row -->
                            <div class="mb-4">
                                <label class="form-label-custom">Email Address</label>
                                <div class="input-group-custom">
                                    <input type="email" class="form-control input-custom" placeholder="rahul@unitedcouriers.biz" required>
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>

                            <!-- Name Row -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Create Password</label>
                                    <div class="input-group-custom">
                                        <input type="password" class="form-control input-custom" placeholder="********" required>
                                        <i class="fa-solid fa-lock"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Confirm Password</label>
                                    <div class="input-group-custom">
                                        <input type="password" class="form-control input-custom" placeholder="********" required>
                                        <i class="fa-solid fa-lock"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Aadhar Row with OTP integration -->
                            <div class="mb-4">
                                <label class="form-label-custom">Aadhar Number</label>
                                <div class="input-group-custom">
                                    <input type="text" id="aadharInput" class="form-control input-custom" placeholder="12-Digit Number" maxlength="12" required>
                                    <i class="fas fa-id-card"></i>
                                    <button type="button" class="btn-otp" onclick="sendOTP()">Get Aadhar  OTP</button>
                                </div>
                                <div id="otpStatus" class="otp-sent-text"><i class="fas fa-check-circle"></i> OTP sent to registered mobile number</div>
                            </div>

                            <!-- OTP Input (Conditional) -->
                            <div class="mb-4 animate__animated animate__fadeIn" id="otpContainer" style="display: none;">
                                <label class="form-label-custom">Enter OTP</label>
                                <div class="input-group-custom">
                                    <input type="text" class="form-control input-custom" placeholder="6-Digit Code" maxlength="6">
                                    <i class="fas fa-key"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="input-group-custom">
                                    <select class="form-select input-custom">
                                        <option selected="" disabled="">Select your business category</option>
                                        <option>B2B</option>
                                        <option>D2C</option>
                                        <option>Courier or Aggregator</option>
                                        <option>eCommerce</option>
                                        <option>Exporter</option>
                                        <option>Personal</option>
                                    </select>
                                    <i class="fas fa-briefcase"></i>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                <label class="form-check-label small text-muted" for="termsCheck">
                                    I agree to the <a href="#" class="text-primary text-decoration-none fw-bold">Terms of Service</a> and <a href="#" class="text-primary text-decoration-none fw-bold">Privacy Policy</a>.
                                </label>
                            </div>

                            <button type="submit" class="btn moving-gradient-bg btn-primary-custom">
                                Create My Account
                            </button>
                        </form>

                        <div class="auth-footer-links">
                            Already have an account? <a href="login.php">Sign In</a>
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

        // OTP Simulation tied to Aadhar Field
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
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Verifying Identity...';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';

            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Success!';
                btn.style.backgroundColor = '#10b981';
                btn.classList.remove('moving-gradient-bg');
                btn.style.opacity = '1';
            }, 2000);
        });
    </script>

<?php include 'website_include/footer.php'; ?>