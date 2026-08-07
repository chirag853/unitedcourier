@include('website_include.header')
<style>
     .tab-pills {
      display: flex;
      gap: 0.5rem;
      background: #eef3f8;
      padding: 0.4rem;
      border-radius: 60px;
      margin-bottom: 2.2rem;
    }
    .tab-pills .pill {
      flex: 1;
      text-align: center;
      padding: 0.6rem 0.2rem;
      border-radius: 40px;
      font-weight: 600;
      font-size: 0.9rem;
      color: #3d556b;
      background: transparent;
      transition: all 0.2s;
      cursor: pointer;
      border: none;
      letter-spacing: -0.01em;
    }
    .tab-pills .pill i {
      margin-right: 6px;
      font-size: 0.9rem;
    }
    .tab-pills .pill.active {
      background: white;
      color: #0b1e33;
      box-shadow: 0 4px 12px rgba(0, 20, 40, 0.08);
    }
    .tab-pills .pill:not(.active):hover {
      background: rgba(255,255,255,0.3);
    }

    /* OTP login is temporarily hidden from the frontend only. */
    #otp-tab,
    #otp-pane {
      display: none !important;
    }
</style>
<link href="{{ asset('css/uc-login.css') }}" rel="stylesheet">

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
                    Your Shipments Don't Stop. <span class="moving-gradient-text">Neither Do We.</span>
                </h1>

                <p class="mt-3 text-muted">
                    Log in to your United Courier's dashboard and take full control of your worldwide shipping. All your
                    shipments are provided with end-to-end support.
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
                                    Your business account and data will be protected with advanced encryption.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="text-success fs-4"><i class="fas fa-bolt"></i></div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 14px;">Track Everything Live</div>
                                <div class="text-muted" style="font-size: 14px;">
                                    Firms can track shipments, manage orders, generate invoices, and monitor delivery
                                    status from a dedicated dashboard.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="text-success fs-4"><i class="fa-solid fa-headset"></i></div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 14px;">End-to-End Support</div>
                                <div class="text-muted" style="font-size: 14px;">
                                    The team will provide end-to-end support with relevant answers to your queries and
                                    no automated runaround.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="text-primary fs-4"><i class="fa-solid fa-globe"></i></div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 14px;">Live Updates</div>
                                <div class="text-muted" style="font-size: 14px;">
                                    Live tracking updates so you're never left guessing where your parcel is.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

            <!-- Login Form -->
            <div class="col-lg-5 col-md-10 animate__animated animate__fadeInRight">
                <div class="form-shadow mx-auto uc-glass-login">
                    <div class="uc-glass-accent"></div>

                    <div class="mb-4">
                        <h3 class="h4-title">Login to <span class="gradient-text">Your Account</span></h3>
                        <!-- <p class="text-muted small">Choose your preferred login method to continue</p> -->
                    </div>

                    <!-- Segmented Tabs -->
                    <!-- <ul class="nav nav-pills login-tabs" id="loginTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="otp-tab" data-bs-toggle="pill"
                                data-bs-target="#otp-pane" type="button" role="tab" aria-selected="true">
                                <i class="fas fa-mobile-screen"></i> OTP Login
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="pill"
                                data-bs-target="#password-pane" type="button" role="tab" aria-selected="false">
                                <i class="fas fa-envelope"></i> Email / Password
                            </button>
                        </li>
                    </ul> -->

                        <!-- <div class="tab-pills" id="tabPills" role="tablist">
                            <button type="button" class="pill" id="otp-tab" role="tab" aria-selected="false" data-pane="otp-pane"><i class="fas fa-mobile-alt"></i> OTP</button>
                            <button type="button" class="pill active" id="password-tab" role="tab" aria-selected="true" data-pane="password-pane"><i class="fas fa-envelope"></i> Email / Password</button>
                        </div> -->


                    <div class="tab-content" id="loginTabsContent">

                        <!-- OTP Login Pane (hidden from the frontend via CSS) -->
                        <div class="tab-pane fade" id="otp-pane" role="tabpanel" aria-labelledby="otp-tab">
                            <form id="loginForm">

                                <!-- Mobile Row with OTP -->
                                <div class="mb-4">
                                    <label class="form-label-custom">Mobile Number</label>
                                    <div class="input-group-custom">
                                        <input type="tel" id="mobileInput" name="phone_number"
                                            class="form-control input-custom" placeholder="+91 XXXXX XXXXX" required>
                                        <i class="fas fa-phone"></i>
                                        <button type="button" class="btn-otp" id="getOtpBtn"
                                            onclick="checkPhoneNumber()" disabled>Get OTP</button>
                                    </div>
                                    <div id="phoneStatus" class="otp-sent-text" style="display: none;"></div>
                                </div>

                                <!-- OTP Input (Conditional) -->
                                <div class="mb-4 animate__animated animate__fadeIn" id="otpContainer"
                                    style="display: none;">
                                    <label class="form-label-custom">Enter 6-Digit OTP</label>
                                    <div class="input-group-custom">
                                        <input type="text" id="otpInput" class="form-control input-custom"
                                            placeholder="XXXXXX" maxlength="6">
                                        <i class="fas fa-key"></i>
                                    </div>
                                </div>

                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="rememberMe">
                                    <label class="form-check-label small text-muted" for="rememberMe">
                                        By clicking on Login, I accept the Terms & Conditions and Privacy Policy.
                                    </label>
                                </div>

                                <button type="submit" class="btn moving-gradient-bg btn-primary-custom" id="loginBtn"
                                    disabled>
                                    Login Now
                                </button>
                            </form>
                        </div>

                        <!-- Email / Password Pane -->
                        <div class="tab-pane fade show active" id="password-pane" role="tabpanel" aria-labelledby="password-tab">
                            <form id="passwordLoginForm">

                                <div class="mb-4">
                                    <label class="form-label-custom">Email Address</label>
                                    <div class="input-group-custom">
                                        <input type="email" id="emailInput" name="email"
                                            class="form-control input-custom" placeholder="you@example.com" required
                                            autocomplete="email">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label-custom">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" id="passwordInput" name="password" class="input-custom form-control"
                                            placeholder="Enter your password" required autocomplete="current-password">
                                        <button type="button" class="btn btn-outline-secondary" style="background-color:#f8f9fa;border: 1px solid #e9ecef;" id="togglePasswordBtn"
                                            tabindex="-1" aria-label="Show password">
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMePw"
                                            name="remember" value="1">
                                        <label class="form-check-label small text-muted" for="rememberMePw">Remember
                                            Me</label>
                                    </div>
                                    <a href="{{ route('customer.password.request') }}" class="forgot-link">Forgot
                                        Password?</a>
                                </div>

                                <button type="submit" class="btn moving-gradient-bg btn-primary-custom"
                                    id="passwordLoginBtn">
                                    Login Now
                                </button>
                            </form>
                        </div>

                    </div>

                    <div class="auth-footer-links">
                        New to United Couriers? <a href="{{ url('/customer/register') }}">Create an Account</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<script>
// Initialize Lenis (only if not already initialized)
if (typeof window.lenisInstance === 'undefined') {
    window.lenisInstance = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
    });

    function raf(time) {
        window.lenisInstance.raf(time);
        requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);
}

// Custom tab switching (replaces Bootstrap pill plugin to avoid "Illegal invocation" errors)
(function initLoginTabs() {
    const tabButtons = document.querySelectorAll('#tabPills .pill');
    const panes = document.querySelectorAll('#loginTabsContent .tab-pane');

    function activateTab(targetPaneId) {
        // Update button states
        tabButtons.forEach(btn => {
            const isActive = btn.getAttribute('data-pane') === targetPaneId;
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        // Update pane visibility
        panes.forEach(pane => {
            const isTarget = pane.id === targetPaneId;
            if (isTarget) {
                pane.classList.add('show', 'active');
            } else {
                pane.classList.remove('show', 'active');
            }
        });
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetPaneId = this.getAttribute('data-pane');
            if (targetPaneId) {
                activateTab(targetPaneId);
            }
        });
    });
})();

// Phone number validation and OTP functionality
let currentPhoneNumber = '';

// Enable/disable Get OTP button based on phone number input
document.getElementById('mobileInput').addEventListener('input', function() {
    const phone = this.value.trim();
    const getOtpBtn = document.getElementById('getOtpBtn');

    if (phone.length >= 10) {
        getOtpBtn.disabled = false;
        getOtpBtn.classList.add('active-otp');
    } else {
        getOtpBtn.disabled = true;
        getOtpBtn.classList.remove('active-otp');
    }
});

// Check phone number in database and send real OTP via SMS
function checkPhoneNumber() {
    const phone = document.getElementById('mobileInput').value.trim();
    const getOtpBtn = document.getElementById('getOtpBtn');
    const phoneStatus = document.getElementById('phoneStatus');

    // Validate phone number
    if (!phone) {
        showNotification('Please enter a phone number', 'error');
        return;
    }

    // Remove common formatting characters
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
    getOtpBtn.classList.remove('active-otp');

    // Set a timeout for the request (10 seconds)
    const timeoutId = setTimeout(() => {
        getOtpBtn.innerHTML = 'Get OTP';
        getOtpBtn.disabled = false;
        getOtpBtn.classList.add('active-otp');
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

    fetch('{{ route("customer.check.phone") }}', {
            method: 'POST',
            headers: fetchHeaders,
            body: JSON.stringify({
                phone_number: cleanPhone
            })
        })
        .then(async response => {
            clearTimeout(timeoutId);

            const data = await response.json();
            return {
                ok: response.ok,
                data: data
            };
        })
        .then(result => {
            const data = result.data;

            if (data.success) {
                currentPhoneNumber = cleanPhone;

                phoneStatus.innerHTML =
                    '<i class="fas fa-check-circle" style="color: #10b981;"></i> OTP sent successfully!';
                phoneStatus.className = 'otp-sent-text';
                phoneStatus.style.display = 'block';
                phoneStatus.style.color = '#10b981';

                // Show OTP input container
                document.getElementById('otpContainer').style.display = 'block';
                document.getElementById('loginBtn').disabled = false;

                getOtpBtn.innerHTML = 'Resend OTP';
                getOtpBtn.disabled = false;
                getOtpBtn.classList.add('active-otp');

                showNotification('✓ OTP sent to your registered mobile number!', 'success');
            } else {
                phoneStatus.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> ' + (data
                    .message || 'Error sending OTP');
                phoneStatus.className = 'otp-sent-text';
                phoneStatus.style.display = 'block';
                phoneStatus.style.color = '#dc3545';

                getOtpBtn.innerHTML = 'Get OTP';
                getOtpBtn.disabled = false;
                getOtpBtn.classList.add('active-otp');

                showNotification(data.message || 'Unable to send OTP. Please try again.', 'error');
            }
        })
        .catch(error => {
            clearTimeout(timeoutId);

            console.error('Phone check error:', error);

            phoneStatus.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> Connection error';
            phoneStatus.className = 'otp-sent-text';
            phoneStatus.style.display = 'block';
            phoneStatus.style.color = '#dc3545';

            getOtpBtn.innerHTML = 'Get OTP';
            getOtpBtn.disabled = false;
            getOtpBtn.classList.add('active-otp');

            showNotification('Server connection error. Please check your internet and try again.', 'error');
        });
}

// Form submission with server-side OTP verification
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const phone = document.getElementById('mobileInput').value.trim();
    const otp = document.getElementById('otpInput').value.trim();
    const loginBtn = document.getElementById('loginBtn');

    if (!phone || !otp) {
        showNotification('Please enter phone number and OTP', 'error');
        return;
    }

    if (otp.length < 6) {
        showNotification('Please enter a valid 6-digit OTP', 'error');
        return;
    }

    loginBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Verifying...';
    loginBtn.disabled = true;

    // Verify OTP with server
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const headers = {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
    }

    fetch('{{ route("customer.verify.otp") }}', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                phone_number: phone,
                otp: otp
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loginBtn.innerHTML = '<i class="fas fa-check-circle"></i> Success!';
                loginBtn.style.backgroundColor = '#10b981';
                loginBtn.classList.remove('moving-gradient-bg');

                showNotification('Login successful! Redirecting...', 'success');

                // Redirect to dashboard
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                showNotification(data.message, 'error');
                loginBtn.innerHTML = 'Login Now';
                loginBtn.disabled = false;
            }
        })
        .catch(error => {
            showNotification('Verification failed. Please try again.', 'error');
            loginBtn.innerHTML = 'Login Now';
            loginBtn.disabled = false;
        });
});

// Password visibility toggle
document.getElementById('togglePasswordBtn').addEventListener('click', function() {
    const passwordInput = document.getElementById('passwordInput');
    const toggleIcon = document.getElementById('togglePasswordIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
        this.setAttribute('aria-label', 'Hide password');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
        this.setAttribute('aria-label', 'Show password');
    }

    passwordInput.focus();
});

// Restore saved credentials on page load (Remember Me)
(function restoreRememberedCredentials() {
    try {
        const saved = localStorage.getItem('uc_remember_credentials');
        if (saved) {
            const creds = JSON.parse(saved);
            const emailInput = document.getElementById('emailInput');
            const passwordInput = document.getElementById('passwordInput');
            const rememberCheckbox = document.getElementById('rememberMePw');
            if (creds && creds.email && creds.password && emailInput && passwordInput && rememberCheckbox) {
                emailInput.value = creds.email;
                passwordInput.value = creds.password;
                rememberCheckbox.checked = true;
            }
        }
    } catch (e) {
        console.error('Failed to restore saved credentials:', e);
    }
})();

// Email / Password login form submission
document.getElementById('passwordLoginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const email = document.getElementById('emailInput').value.trim();
    const password = document.getElementById('passwordInput').value;
    const remember = document.getElementById('rememberMePw').checked;
    const loginBtn = document.getElementById('passwordLoginBtn');

    if (!email || !password) {
        showNotification('Please enter both email and password', 'error');
        return;
    }

    loginBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Verifying...';
    loginBtn.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const headers = {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
    }

    fetch('{{ route("customer.login.password") }}', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                email: email,
                password: password,
                remember: remember
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loginBtn.innerHTML = '<i class="fas fa-check-circle"></i> Success!';
                loginBtn.style.backgroundColor = '#10b981';
                loginBtn.classList.remove('moving-gradient-bg');

                // Save or clear credentials based on Remember Me
                try {
                    if (remember) {
                        localStorage.setItem('uc_remember_credentials', JSON.stringify({
                            email: email,
                            password: password
                        }));
                    } else {
                        localStorage.removeItem('uc_remember_credentials');
                    }
                } catch (e) {
                    console.error('Failed to save credentials:', e);
                }

                showNotification('Login successful! Redirecting...', 'success');

                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                showNotification(data.message || 'Login failed. Please check your credentials.', 'error');
                loginBtn.innerHTML = 'Login Now';
                loginBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Password login error:', error);
            showNotification('Login failed. Please check your credentials and try again.', 'error');
            loginBtn.innerHTML = 'Login Now';
            loginBtn.disabled = false;
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