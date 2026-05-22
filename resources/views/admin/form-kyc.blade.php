<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Streamline your business with our advanced CRM template. Easily integrate and customize to manage sales, support, and customer interactions efficiently. Perfect for any business size">
	<meta name="keywords" content="Advanced CRM template, customer relationship management, business CRM, sales optimization, customer support software, CRM integration, customizable CRM, business tools, enterprise CRM solutions">
	<meta name="author" content="Dreams Technologies">
	<meta name="robots" content="index, follow">
	
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('js/theme-script.js') }}" type="text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" id="app-style">

</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar Start -->
        <header class="navbar-header">
            <div class="page-container topbar-menu">
                <div class="d-flex align-items-center gap-2">

                    <!-- Logo -->
                    <a href="{{ route('admin.dashboard') }}" class="logo">
                        <span class="logo-light">
                            <span class="logo-lg"><img src="{{ asset('assets/img/logo.svg') }}" alt="logo"></span>
                            <span class="logo-sm"><img src="{{ asset('assets/img/logo-small.svg') }}" alt="small logo"></span>
                        </span>
                        <span class="logo-dark">
                            <span class="logo-lg"><img src="{{ asset('assets/img/logo-white.svg') }}" alt="dark logo"></span>
                        </span>
                    </a>

                    <!-- Sidebar Mobile Button -->
                    <a id="mobile_btn" class="mobile-btn" href="#sidebar">
                        <i class="ti ti-menu-deep fs-24"></i>
                    </a>

                    <button class="sidenav-toggle-btn btn border-0 p-0" id="toggle_btn2"> 
                        <i class="ti ti-arrow-bar-to-right"></i>
                    </button> 
					
                    <!-- Search -->
                    <div class="me-auto d-flex align-items-center header-search d-lg-flex d-none">
                        <div class="input-icon position-relative me-2">
                           <input type="text" class="form-control" placeholder="Search Keyword">
                           <span class="input-icon-addon d-inline-flex p-0 header-search-icon"><i class="ti ti-command"></i></span>
                        </div>
                    </div>

                    <!-- Header Menu -->
                    <ul class="header-menu d-flex align-items-center gap-3">
                        
                        <!-- Theme -->
                        <li class="theme-switch">
                            <a href="javascript:void(0);" class="light-theme" id="light-theme">
                                <i class="ti ti-sun"></i>
                            </a>
                            <a href="javascript:void(0);" class="dark-theme d-none" id="dark-theme">
                                <i class="ti ti-moon"></i>
                            </a>
                        </li>
                        
                        <!-- User Menu -->
                        <li class="dropdown nav-item user-nav">
                            <a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                                <img src="{{ asset('assets/img/users/user-01.jpg') }}" alt="Img" class="avatar avatar-sm rounded-circle">
                                <span class="user-name">Admin User</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="user-info">
                                    <div class="user-name">
                                        <h6>Admin User</h6>
                                        <span class="text-muted">admin@example.com</span>
                                    </div>
                                </div>
                                <a class="dropdown-item" href="profile-settings.html"><i class="ti ti-user-circle me-1"></i> Profile</a>
                                <a class="dropdown-item" href="profile-settings.html"><i class="ti ti-settings me-1"></i> Settings</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('admin.login') }}"><i class="ti ti-logout me-1"></i> Logout</a>
                            </div>
                        </li>
                        
                    </ul>
                </div>
            </div>
        </header>
        <!-- Topbar End -->

        <!-- Sidebar Start -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li>
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="ti ti-layout-dashboard"></i> 
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <i class="ti ti-user-check"></i> 
                                <span>KYC</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
        <!-- Sidebar End -->

        <!-- Main Content Start -->
        <div class="page-wrapper">
            <div class="page-content">
                
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">Know Your Customer (KYC)</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Compliance</a></li>
                                <li class="breadcrumb-item active">KYC Form</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- KYC Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">KYC Verification Form</h4>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <!-- Personal Information -->
                                        <div class="col-md-12">
                                            <h5 class="mb-3">Personal Information</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Full Name *</label>
                                                <input type="text" class="form-control" placeholder="Enter full legal name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Date of Birth *</label>
                                                <input type="date" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Nationality *</label>
                                                <select class="form-control" required>
                                                    <option value="">Select Nationality</option>
                                                    <option value="US">United States</option>
                                                    <option value="UK">United Kingdom</option>
                                                    <option value="CA">Canada</option>
                                                    <option value="AU">Australia</option>
                                                    <option value="IN">India</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Gender *</label>
                                                <select class="form-control" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Contact Information -->
                                        <div class="col-md-12 mt-4">
                                            <h5 class="mb-3">Contact Information</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email Address *</label>
                                                <input type="email" class="form-control" placeholder="Enter email address" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Phone Number *</label>
                                                <input type="tel" class="form-control" placeholder="Enter phone number" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">Residential Address *</label>
                                                <textarea class="form-control" rows="3" placeholder="Enter complete residential address" required></textarea>
                                            </div>
                                        </div>
                                        
                                        <!-- Identification Documents -->
                                        <div class="col-md-12 mt-4">
                                            <h5 class="mb-3">Identification Documents</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Document Type *</label>
                                                <select class="form-control" required>
                                                    <option value="">Select Document Type</option>
                                                    <option value="Passport">Passport</option>
                                                    <option value="Driver License">Driver License</option>
                                                    <option value="National ID">National ID Card</option>
                                                    <option value="Aadhaar">Aadhaar Card</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Document Number *</label>
                                                <input type="text" class="form-control" placeholder="Enter document number" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Upload Document Front *</label>
                                                <input type="file" class="form-control" accept="image/*,.pdf" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Upload Document Back</label>
                                                <input type="file" class="form-control" accept="image/*,.pdf">
                                            </div>
                                        </div>
                                        
                                        <!-- Financial Information -->
                                        <div class="col-md-12 mt-4">
                                            <h5 class="mb-3">Financial Information</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Occupation *</label>
                                                <input type="text" class="form-control" placeholder="Enter occupation" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Annual Income *</label>
                                                <select class="form-control" required>
                                                    <option value="">Select Income Range</option>
                                                    <option value="0-25000">Below $25,000</option>
                                                    <option value="25000-50000">$25,000 - $50,000</option>
                                                    <option value="50000-100000">$50,000 - $100,000</option>
                                                    <option value="100000+">Above $100,000</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">Source of Funds *</label>
                                                <textarea class="form-control" rows="3" placeholder="Describe source of funds" required></textarea>
                                            </div>
                                        </div>
                                        
                                        <!-- Declaration -->
                                        <div class="col-md-12 mt-4">
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="declaration" required>
                                                    <label class="form-check-label" for="declaration">
                                                        I hereby declare that all the information provided is true and accurate. I understand that providing false information may result in legal consequences.
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ti ti-check me-1"></i> Submit KYC Application
                                                </button>
                                                <button type="button" class="btn btn-secondary">
                                                    <i class="ti ti-x me-1"></i> Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">KYC Status</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <div class="mb-3">
                                        <span class="badge bg-warning">Pending Verification</span>
                                    </div>
                                    <p class="text-muted">Your KYC application is being processed. This typically takes 2-3 business days.</p>
                                    <div class="mt-3">
                                        <h6>Required Documents:</h6>
                                        <ul class="list-unstyled text-start">
                                            <li><i class="ti ti-check text-success"></i> Personal Information</li>
                                            <li><i class="ti ti-check text-success"></i> Contact Details</li>
                                            <li><i class="ti ti-x text-danger"></i> ID Document</li>
                                            <li><i class="ti ti-x text-danger"></i> Financial Information</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /KYC Form -->

            </div>
        </div>
        <!-- Main Content End -->

    </div>
    <!-- End Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Main JS -->
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

</body>

</html>
