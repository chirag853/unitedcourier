@include('website_include.header')

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

                        <!-- Phone Number -->
                        <div class="mb-4">
                            <label class="form-label-custom">Phone Number</label>
                            <div class="input-group-custom">
                                <input type="tel" id="mobileInput" name="phone_number" class="form-control input-custom" placeholder="99******"
                                    pattern="[0-9]{10}" maxlength="10" required>
                                <i class="fas fa-phone-alt"></i>
                                <button type="button" class="btn-otp" id="getOtpBtn" onclick="sendRegistrationOtp()" disabled>Get OTP</button>
                            </div>
                            <div id="phoneStatus" class="otp-sent-text" style="display: none;"></div>
                        </div>

                        <!-- OTP Input (Conditional) -->
                        <div class="mb-4 animate__animated animate__fadeIn" id="otpContainer" style="display: none;">
                            <label class="form-label-custom">Enter 6-Digit OTP</label>
                            <div class="input-group-custom">
                                <input type="text" id="otpInput" class="form-control input-custom" placeholder="XXXXXX" maxlength="6">
                                <i class="fas fa-key"></i>
                                <button type="button" class="btn-otp" id="verifyOtpBtn" onclick="verifyRegistrationOtp()">Verify</button>
                            </div>
                            <div id="otpStatus" class="otp-sent-text" style="display: none;"></div>
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

// Registration OTP state
let registrationPhoneVerified = false;
let currentPhoneNumber = '';

// Enable/disable Get OTP button based on phone number input
document.getElementById('mobileInput').addEventListener('input', function() {
    const phone = this.value.trim();
    const getOtpBtn = document.getElementById('getOtpBtn');

    // If phone changes after verification, reset verification state
    if (registrationPhoneVerified && phone !== currentPhoneNumber) {
        registrationPhoneVerified = false;
        document.getElementById('otpContainer').style.display = 'none';
        document.getElementById('phoneStatus').style.display = 'none';
        document.getElementById('otpStatus').style.display = 'none';
        document.getElementById('otpInput').value = '';
    }

    if (phone.length >= 10) {
        getOtpBtn.disabled = false;
    } else {
        getOtpBtn.disabled = true;
    }
});

// Send OTP for registration
function sendRegistrationOtp() {
    const phone = document.getElementById('mobileInput').value.trim();
    const getOtpBtn = document.getElementById('getOtpBtn');
    const phoneStatus = document.getElementById('phoneStatus');

    if (!phone) {
        showNotification('Please enter a phone number', 'error');
        return;
    }

    let cleanPhone = phone.replace(/[\s+()-]/g, '');

    if (cleanPhone.length < 10 || cleanPhone.length > 15) {
        showNotification('Please enter a valid phone number (10-15 digits)', 'error');
        return;
    }

    if (!/^\d+$/.test(cleanPhone)) {
        showNotification('Phone number should contain only digits', 'error');
        return;
    }

    getOtpBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Sending...';
    getOtpBtn.disabled = true;

    const timeoutId = setTimeout(() => {
        getOtpBtn.innerHTML = 'Get OTP';
        getOtpBtn.disabled = false;
        showNotification('Request timeout. Please try again.', 'error');
    }, 10000);

    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const fetchHeaders = {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };
    if (csrfTokenElement) {
        fetchHeaders['X-CSRF-TOKEN'] = csrfTokenElement.getAttribute('content');
    }

    fetch('{{ route("customer.send.registration.otp") }}', {
        method: 'POST',
        headers: fetchHeaders,
        body: JSON.stringify({ phone_number: cleanPhone })
    })
    .then(response => {
        clearTimeout(timeoutId);
        return response.json().then(data => ({ status: response.status, body: data }));
    })
    .then(({ status, body: data }) => {
        if (data.success) {
            currentPhoneNumber = cleanPhone;
            registrationPhoneVerified = false;

            phoneStatus.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981;"></i> OTP sent successfully!';
            phoneStatus.className = 'otp-sent-text';
            phoneStatus.style.display = 'block';
            phoneStatus.style.color = '#10b981';

            // Show OTP input container
            document.getElementById('otpContainer').style.display = 'block';
            document.getElementById('otpInput').value = '';
            document.getElementById('otpStatus').style.display = 'none';

            getOtpBtn.innerHTML = 'Resend OTP';
            getOtpBtn.disabled = false;

            showNotification('✓ OTP sent to your mobile number!', 'success');
        } else {
            // 409 = already registered; treat as blocking error
            phoneStatus.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> ' + (data.message || 'Error sending OTP');
            phoneStatus.className = 'otp-sent-text';
            phoneStatus.style.display = 'block';
            phoneStatus.style.color = '#dc3545';

            getOtpBtn.innerHTML = 'Get OTP';
            getOtpBtn.disabled = false;

            showNotification(data.message || 'Unable to send OTP. Please try again.', 'error');
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.error('Send OTP error:', error);

        phoneStatus.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> Connection error';
        phoneStatus.className = 'otp-sent-text';
        phoneStatus.style.display = 'block';
        phoneStatus.style.color = '#dc3545';

        getOtpBtn.innerHTML = 'Get OTP';
        getOtpBtn.disabled = false;

        showNotification('Server connection error. Please check your internet and try again.', 'error');
    });
}

// Verify OTP for registration
function verifyRegistrationOtp() {
    const phone = document.getElementById('mobileInput').value.trim();
    const otp = document.getElementById('otpInput').value.trim();
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const otpStatus = document.getElementById('otpStatus');

    if (!phone || !otp) {
        showNotification('Please enter phone number and OTP', 'error');
        return;
    }

    if (otp.length < 6) {
        showNotification('Please enter a valid 6-digit OTP', 'error');
        return;
    }

    verifyOtpBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Verifying...';
    verifyOtpBtn.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const headers = {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
    }

    fetch('{{ route("customer.verify.registration.otp") }}', {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({ phone_number: phone, otp: otp })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            registrationPhoneVerified = true;

            otpStatus.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981;"></i> Phone number verified!';
            otpStatus.className = 'otp-sent-text';
            otpStatus.style.display = 'block';
            otpStatus.style.color = '#10b981';

            verifyOtpBtn.innerHTML = '<i class="fas fa-check"></i> Verified';
            verifyOtpBtn.disabled = true;

            showNotification('✓ Phone number verified! You can now complete your registration.', 'success');
        } else {
            otpStatus.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> ' + (data.message || 'Invalid OTP');
            otpStatus.className = 'otp-sent-text';
            otpStatus.style.display = 'block';
            otpStatus.style.color = '#dc3545';

            verifyOtpBtn.innerHTML = 'Verify';
            verifyOtpBtn.disabled = false;

            showNotification(data.message || 'OTP verification failed. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Verify OTP error:', error);
        otpStatus.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> Connection error';
        otpStatus.className = 'otp-sent-text';
        otpStatus.style.display = 'block';
        otpStatus.style.color = '#dc3545';

        verifyOtpBtn.innerHTML = 'Verify';
        verifyOtpBtn.disabled = false;

        showNotification('Verification failed. Please try again.', 'error');
    });
}

// Form Submission
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Require OTP verification before submitting registration
    if (!registrationPhoneVerified) {
        showNotification('Please verify your phone number via OTP before registering.', 'error');
        return;
    }

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
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(data => ({ ok: response.ok, data }));
        }
        // Non-JSON response (e.g. HTML error page) — surface a clear message
        return response.text().then(text => {
            console.error('Non-JSON registration response:', text);
            return { ok: false, data: { success: false, message: 'Server error (non-JSON response). Please check the Laravel log.' } };
        });
    })
    .then(({ ok, data }) => {
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
            
            // Display error message (include actual error detail if provided)
            let errorMessage = data.message || 'Registration failed.';
            if (data.errors) {
                errorMessage = Object.values(data.errors).flat().join('<br>');
            } else if (data.error) {
                errorMessage += '<br><small>' + data.error + '</small>';
            }
            
            // Show error notification
            showNotification(errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Registration fetch error:', error);
        btn.innerHTML = 'Create My Account';
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
        showNotification('Registration failed due to a network error. Please try again.', 'error');
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

@include('website_include.footer')