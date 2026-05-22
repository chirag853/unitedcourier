@include('customer.partials.header')

<div class="hero-gradient-container" style="margin-top: 70px;">
    <!-- Floating Elements -->
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;"></div>
    <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;">
    </div>

    <div class="container">
        <div class="row align-items-center justify-content-center g-5">

            <!-- Left Content -->
            <div class="col-lg-7 animate__animated animate__fadeInLeft d-none d-lg-block">
                <h1 class="hero-title mb-1">
                    Deliver<span class="moving-gradient-text">Beyond Boundaries.</span>
                </h1>

                <p class="mt-3 text-muted">
                    Get on board in minutes and ship straight with a global shipping network covering 220+ countries, as we've got the network to back you up.
                </p>

                <!-- Global Network Image Integration -->
                <img src="{{ asset('website_images/global-network.webp') }}" alt="Global Network"
                    class="hero-illustration">

                <div class="row g-3 mb-5">
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="text-success fs-4"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 14px;">Profile Done? You're Live</div>
                                <div class="text-muted" style="font-size: 14px;">No waiting rooms. No approval delays. Finish your profile and start shipping the same day.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="text-primary fs-4"><i class="fas fa-bolt"></i></div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 14px;">Dedicated Customer Support</div>
                                <div class="text-muted" style="font-size: 14px;">Find our logistics experts at your disposal to assist you at every stage.</div>
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

                    <form id="registrationForm" action="{{ route('customer.register.store') }}" method="POST">
                        @csrf
                        <!-- Name Row -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">First Name</label>
                                <div class="input-group-custom">
                                    <input type="text" name="first_name" class="form-control input-custom" placeholder="Rahul" required>
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Last Name</label>
                                <div class="input-group-custom">
                                    <input type="text" name="last_name" class="form-control input-custom" placeholder="Kumar" required>
                                    <i class="fas fa-user-tag"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Email Row -->
                        <div class="mb-4">
                            <label class="form-label-custom">Email Address</label>
                            <div class="input-group-custom">
                                <input type="email" name="email" class="form-control input-custom"
                                    placeholder="rahul@unitedcouriers.biz" required>
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>

                        <!-- Phone Number Row -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Phone Number</label>
                                <div class="input-group-custom">
                                    <input type="tel" name="phone_number" class="form-control input-custom" placeholder="99******"
                                        pattern="[0-9]{10}" maxlength="10" required>
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Alternate Phone Number</label>
                                <div class="input-group-custom">
                                    <input type="tel" name="alternate_phone_number" class="form-control input-custom" placeholder="99******"
                                        pattern="[0-9]{10}" maxlength="10">
                                    <i class="fas fa-phone-slash"></i>
                                </div>
                                <small class="text-muted">Optional</small>
                            </div>
                        </div>

                        <!-- Password Row -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Create Password</label>
                                <div class="input-group-custom">
                                    <input type="password" name="password" class="form-control input-custom" placeholder="********"
                                        required>
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Confirm Password</label>
                                <div class="input-group-custom">
                                    <input type="password" name="confirm_password" class="form-control input-custom" placeholder="********"
                                        required>
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Aadhar Row with OTP integration -->
                        <div class="mb-4">
                            <label class="form-label-custom">Aadhar Number</label>
                            <div class="input-group-custom">
                                <input type="text" name="aadhar_number" id="aadharInput" class="form-control input-custom"
                                    placeholder="12-Digit Number" maxlength="12" required>
                                <i class="fas fa-id-card"></i>
                                <button type="button" class="btn-otp" onclick="sendOTP()">Get Aadhar OTP</button>
                            </div>
                            <div id="otpStatus" class="otp-sent-text"><i class="fas fa-check-circle"></i> OTP sent to
                                registered mobile number</div>
                        </div>

                        <!-- OTP Input (Conditional) -->
                        <div class="mb-4 animate__animated animate__fadeIn" id="otpContainer" style="display: none;">
                            <label class="form-label-custom">Enter OTP</label>
                            <div class="input-group-custom">
                                <input type="text" name="otp" class="form-control input-custom" placeholder="6-Digit Code"
                                    maxlength="6">
                                <i class="fas fa-key"></i>
                            </div>
                        </div>

                        <!-- Business Category -->
                        <div class="mb-3">
                            <div class="input-group-custom">
                                <select name="business_category" class="form-select input-custom">
                                    <option value="" selected disabled>Select your business category</option>
                                    @foreach($businessCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-briefcase"></i>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="termsCheck" id="termsCheck" required>
                            <label class="form-check-label small text-muted" for="termsCheck">
                                I agree to the <a href="#" class="text-primary text-decoration-none fw-bold">Terms of
                                    Service</a> and <a href="#"
                                    class="text-primary text-decoration-none fw-bold">Privacy Policy</a>.
                            </label>
                        </div>

                        <!-- Submit Button -->
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
    const formData = new FormData(this);
    
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Creating Account...';
    btn.style.opacity = '0.7';
    btn.style.pointerEvents = 'none';

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Success!';
            btn.style.backgroundColor = '#10b981';
            btn.classList.remove('moving-gradient-bg');
            btn.style.opacity = '1';
            
            // Redirect to login page after success
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
        } else {
            // Show validation errors
            btn.innerHTML = 'Create My Account';
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
            
            // Display error message
            let errorMessage = data.message;
            if (data.errors) {
                errorMessage = Object.values(data.errors).flat().join('<br>');
            }
            
            // Show error notification
            showNotification(errorMessage, 'error');
        }
    })
    .catch(error => {
        btn.innerHTML = 'Create My Account';
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
        showNotification('Registration failed. Please try again.', 'error');
    });
});

// Helper function to show notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} position-fixed top-0 end-0 m-3`;
    notification.style.zIndex = '9999';
    notification.innerHTML = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>

@include('customer.partials.footer')