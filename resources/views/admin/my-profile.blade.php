<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage your admin profile">
    <meta name="keywords" content="admin, profile, settings, courier, logistics">
    
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .profile-card {
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            overflow: hidden;
        }
        .profile-card-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            padding: 30px 20px;
            text-align: center;
            color: #fff;
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #fff;
            object-fit: cover;
        }
        .profile-info-label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
        }
        .profile-info-value {
            font-size: 15px;
            color: #212529;
            font-weight: 500;
        }
        .form-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #212529;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    @php
        $profileHomeUrl = $admin->canAccessDashboard()
            ? route('admin.dashboard')
            : route('admin.my-profile');
    @endphp
    <!-- Begin Wrapper -->
    <div class="main-wrapper">
        <!-- Header Start -->
        @include('admin.partials.header')
        <!-- Header End -->

        <!-- Mobile Menu Search -->
        <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
            <div class="offcanvas-body">
                <div class="card shadow-none mb-0">
                    <div class="px-3 py-2 d-flex flex-row align-items-center" id="search-top">
                        <i class="ti ti-search fs-22"></i>
                        <input type="search" class="form-control border-0" placeholder="Search">
                        <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x fs-22"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidenav Menu Start -->
        @include('admin.partials.sidebar')
        <!-- Sidenav Menu End -->

        <!-- Page Content -->
        <div class="page-wrapper">
            <div class="content">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-circle-x me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Breadcrumb -->
                <!-- <div class="page-header">
                    <div class="page-title">
                        <h4>My Profile</h4>
                    </div>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ $profileHomeUrl }}">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div> -->

                <!-- Profile Content -->
                <div class="row">
                    <!-- Left Column: Profile Card -->
                    <div class="col-lg-4 col-xl-3">
                        <div class="profile-card mb-4">
                            <div class="profile-card-header">
                                <img src="{{ asset('assets/img/profiles/avatar-19.jpg') }}" class="profile-avatar mb-3" alt="Profile">
                                <h5 class="mb-1">{{ $admin->name }}</h5>
                                <p class="mb-0 fs-13">{{ $admin->designation ?? 'Admin' }}</p>
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-3">
                                    <div class="profile-info-label"><i class="ti ti-mail me-1"></i>Email</div>
                                    <div class="profile-info-value">{{ $admin->email }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="profile-info-label"><i class="ti ti-phone me-1"></i>Mobile</div>
                                    <div class="profile-info-value">{{ $admin->mobile ?? '—' }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="profile-info-label"><i class="ti ti-map-pin me-1"></i>Location</div>
                                    <div class="profile-info-value">
                                        {{ $admin->city ?? '—' }}
                                        @if($admin->state)
                                            , {{ $admin->state }}
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <div class="profile-info-label"><i class="ti ti-user-pin me-1"></i>Role</div>
                                    <div class="profile-info-value">
                                        {{ $admin->type === 'Delivery_person' ? 'Delivery Person' : $admin->type }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Edit Form -->
                    <div class="col-lg-8 col-xl-9">
                        <div class="card mb-0">
                            <div class="card-body">
                                <form action="{{ route('admin.update-profile') }}" method="POST" id="profileForm">
                                    @csrf

                                    <!-- Personal Information -->
                                    <div class="form-section-title">
                                        <i class="ti ti-user me-1"></i>Personal Information
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="mobile">Mobile Number</label>
                                            <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile', $admin->mobile) }}" placeholder="Enter mobile number">
                                            @error('mobile')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="designation">Designation</label>
                                            <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" value="{{ old('designation', $admin->designation) }}" placeholder="Enter designation">
                                            @error('designation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Location Information -->
                                    <div class="form-section-title mt-3">
                                        <i class="ti ti-map-pin me-1"></i>Location Information
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="state">State</label>
                                            <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" name="state" value="{{ old('state', $admin->state) }}" placeholder="Enter state">
                                            @error('state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="city">City</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $admin->city) }}" placeholder="Enter city">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Password Change (Optional) -->
                                    <div class="form-section-title mt-3">
                                        <i class="ti ti-lock me-1"></i>Change Password <small class="text-muted">(leave blank to keep current password)</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="current_password">Current Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Enter current password">
                                                <button type="button" class="btn btn-outline-secondary password-toggle" aria-label="Show password"><i class="ti ti-eye"></i></button>
                                            </div>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="new_password">New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" placeholder="Enter new password" minlength="6">
                                                <button type="button" class="btn btn-outline-secondary password-toggle" aria-label="Show password"><i class="ti ti-eye"></i></button>
                                            </div>
                                            @error('new_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="new_password_confirmation">Confirm New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm new password">
                                                <button type="button" class="btn btn-outline-secondary password-toggle" aria-label="Show password"><i class="ti ti-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit -->
                                    <div class="text-end mt-4">
                                        <a href="{{ $profileHomeUrl }}" class="btn btn-secondary me-2">
                                            <i class="ti ti-arrow-left me-1"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-1"></i>Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function () {
            $('.password-toggle').on('click', function () {
                var input = $(this).siblings('input')[0];
                var icon = $(this).find('i');
                var visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                icon.toggleClass('ti-eye', visible).toggleClass('ti-eye-off', !visible);
                $(this).attr('aria-label', visible ? 'Show password' : 'Hide password');
            });

            // Password validation: if new password is entered, current password must also be entered
            $('#profileForm').on('submit', function (e) {
                var newPassword = $('#new_password').val();
                var currentPassword = $('#current_password').val();
                var confirmPassword = $('#new_password_confirmation').val();

                if (newPassword && !currentPassword) {
                    e.preventDefault();
                    showAlert('Please enter your current password to change the password.', 'warning');
                    $('#current_password').focus();
                    return false;
                }

                if (newPassword && newPassword !== confirmPassword) {
                    e.preventDefault();
                    showAlert('New password and confirmation password do not match.', 'warning');
                    $('#new_password_confirmation').focus();
                    return false;
                }

                if (newPassword && newPassword.length < 6) {
                    e.preventDefault();
                    showAlert('New password must be at least 6 characters long.', 'warning');
                    $('#new_password').focus();
                    return false;
                }
            });
        });
    </script>
</body>
</html>
