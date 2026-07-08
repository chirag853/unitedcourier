@include('website_include.header')

    <div class="hero-gradient-container" style="margin-top: 70px;">
        <!-- Floating Elements -->
        <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;"></div>
        <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;"></div>

        <div class="container">
            <div class="row align-items-center justify-content-center g-5">

                <!-- Forgot Password Form -->
                <div class="col-lg-5 col-md-10 animate__animated animate__fadeInRight">
                    <div class="form-shadow mx-auto">

                        <div class="mb-4">
                            <h3 class="h4-title">Reset <span class="gradient-text">Your Password</span></h3>
                            <p class="text-muted small">Enter your registered email to receive a password reset link.</p>
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

                        <form method="POST" action="{{ route('customer.password.email') }}">
                            @csrf

                            <!-- Email -->
                            <div class="mb-4">
                                <label class="form-label-custom">Email Address</label>
                                <div class="input-group-custom">
                                    <input type="email" name="email" class="form-control input-custom @error('email') is-invalid @enderror" placeholder="you@example.com" value="{{ old('email') }}" required autofocus autocomplete="email">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn moving-gradient-bg btn-primary-custom">
                                <i class="fas fa-paper-plane me-2"></i> Send Reset Link
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
