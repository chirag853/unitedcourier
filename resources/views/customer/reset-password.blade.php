@include('website_include.header')

    <div class="hero-gradient-container" style="margin-top: 70px;">
        <!-- Floating Elements -->
        <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;"></div>
        <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;"></div>

        <div class="container">
            <div class="row align-items-center justify-content-center g-5">

                <!-- Reset Password Form -->
                <div class="col-lg-5 col-md-10 animate__animated animate__fadeInRight">
                    <div class="form-shadow mx-auto">

                        <div class="mb-4">
                            <h3 class="h4-title">Set <span class="gradient-text">New Password</span></h3>
                            <p class="text-muted small">Choose a new password for your account.</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('customer.password.update') }}">
                            @csrf

                            <!-- Token -->
                            <input type="hidden" name="token" value="{{ $token }}">

                            <!-- Email -->
                            <div class="mb-4">
                                <label class="form-label-custom">Email Address</label>
                                <div class="input-group-custom">
                                    <input type="email" name="email" class="form-control input-custom @error('email') is-invalid @enderror" placeholder="you@example.com" value="{{ $email ?? old('email') }}" required autocomplete="email">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label class="form-label-custom">New Password</label>
                                <div class="input-group-custom password-input-group">
                                    <input type="password" name="password" class="form-control input-custom @error('password') is-invalid @enderror" placeholder="Enter new password" required autocomplete="new-password">
                                    <i class="fas fa-lock"></i>
                                    <button type="button" class="password-toggle" aria-label="Show password"><i class="fas fa-eye"></i></button>
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label class="form-label-custom">Confirm Password</label>
                                <div class="input-group-custom password-input-group">
                                    <input type="password" name="password_confirmation" class="form-control input-custom" placeholder="Re-enter new password" required autocomplete="new-password">
                                    <i class="fas fa-lock"></i>
                                    <button type="button" class="password-toggle" aria-label="Show password"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>

                            <button type="submit" class="btn moving-gradient-bg btn-primary-custom">
                                <i class="fas fa-key me-2"></i> Reset Password
                            </button>
                        </form>

                        <div class="auth-footer-links">
                            Remember your password? <a href="{{ route('login') }}">Back to Login</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@include('website_include.footer')

<script>
    document.querySelectorAll('.password-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = this.parentElement.querySelector('input');
            var icon = this.querySelector('i');
            var visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            icon.classList.toggle('fa-eye', visible);
            icon.classList.toggle('fa-eye-slash', !visible);
            this.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
        });
    });
</script>
