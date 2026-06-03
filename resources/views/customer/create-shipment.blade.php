<!DOCTYPE html>
<html lang="en">
<!-- Mirrored from crms.dreamstechnologies.com/html/template/contacts.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 31 Jul 2025 06:56:43 GMT -->

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Contacts | CRMS - Advanced Bootstrap 5 Admin Template for Customer Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Streamline your business with our advanced CRM template. Easily integrate and customize to manage sales, support, and customer interactions efficiently. Perfect for any business size">
    <meta name="keywords"
        content="Advanced CRM template, customer relationship management, business CRM, sales optimization, customer support software, CRM integration, customizable CRM, business tools, enterprise CRM solutions">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- Choices CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/choices.js/public/assets/styles/choices.min.css') }}">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <!-- Quill CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/quill/quill.snow.css') }}">
    <!-- Mobile CSS-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/intltelinput/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/intltelinput/css/demo.css') }}">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
</head>

<body>
    <!-- Begin Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar Start -->
        @include('customer.partials.customer_dashboard_header')
        <!-- Topbar End -->
        <!-- Search Modal -->
        <div class="modal fade" id="searchModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-transparent">
                    <div class="card shadow-none mb-0">
                        <div class="px-3 py-2 d-flex flex-row align-items-center" id="search-top">
                            <i class="ti ti-search fs-22"></i>
                            <input type="search" class="form-control border-0" placeholder="Search">
                            <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i
                                    class="ti ti-x fs-22"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sidenav Menu Start -->
        @include('customer.partials.sidebar')
        <!-- Sidenav Menu End -->
        <!-- ========================
            Start Page Content
        ========================= -->
        <div class="page-wrapper">
            <!-- Start Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">Create Shipment</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="index-2.html">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create shipment</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-light px-2 shadow"
                                data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
                            <div class="dropdown-menu  dropdown-menu-end">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item"><i
                                                class="ti ti-file-type-pdf me-1"></i>Export as
                                            PDF</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item"><i
                                                class="ti ti-file-type-xls me-1"></i>Export as
                                            Excel </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow"
                            data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh"
                            data-bs-original-title="Refresh"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow"
                            data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse"
                            data-bs-original-title="Collapse" id="collapse-header"><i
                                class="ti ti-transition-top"></i></a>
                    </div>
                </div>
                <!-- End Page Header -->
                <!-- card start -->
                <div class="card border-0 rounded-0">
                    <!-- <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <div class="input-icon input-icon-start position-relative">
                            <span class="input-icon-addon text-dark"><i class="ti ti-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search">
                        </div>
                        <a href="add-page.html" class="btn btn-primary"><i
                                class="ti ti-square-rounded-plus-filled me-1"></i>Add New Page</a>
                    </div> -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="{{ url('/customer/create-shipment') }}" method="POST">
                                    @csrf
                                    <div class="accordion accordion-bordered" id="main_accordion">
                                        <!-- Basic Info -->
                                        <div class="accordion-item rounded mb-3">
                                            <div class="accordion-header">
                                                <a href="#" class="accordion-button accordion-custom-button rounded"
                                                    data-bs-toggle="collapse" data-bs-target="#basic">
                                                    <span class="avatar avatar-md rounded me-1">1</span>
                                                    Shipper Info (ship from)
                                                </a>
                                            </div>
                                            <div class="accordion-collapse collapse show" id="basic"
                                                data-bs-parent="#main_accordion">
                                                <div class="accordion-body border-top">
                                                    <div class="row">
                                                        <!-- <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">AWB Number <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" name="shipper_phone_number" placeholder="Shipper Phone Number">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Forwarding Number <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" name="shipper_email" placeholder="Email Address">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Reference Number <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" name="shipper_email" placeholder="Email Address">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label class="form-label">Customer ID <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" name="shipper_email" placeholder="Email Address">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" name="shipper_email" placeholder="Email Address">
                                                </div>
                                            </div>
                                            <div class="col-md-4">											
                                                <div class="mb-3">
                                                    <label class="form-label">Origin Hub<span class="text-danger ms-1">*</span></label>
                                                    <select class="select2 select2-hidden-accessible" data-toggle="select2" data-select2-id="10" tabindex="-1" aria-hidden="true">
                                                        <option data-select2-id="11">Select</option>
                                                        <option>Delhi</option>
                                                        <option>Mumbai</option>
                                                        <option>Bangaluru</option>
                                                        <option>Pune</option>
                                                    </select>
                                                </div>
                                            </div>
                                            -->
                                                        <h5 style="margin-bottom: 20px;">Shipment Type</h5>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Delivery Destination<span
                                                                        class="text-danger ms-1">*</span></label>
                                                                <select class="select2 select2-hidden-accessible"
                                                                    name="delivery_destination" data-toggle="select2"
                                                                    data-select2-id="12" tabindex="-1"
                                                                    aria-hidden="true">
                                                                    <option data-select2-id="12" value="">Select</option>
                                                                    <option value="US- United State of America" {{ old('delivery_destination') == 'US- United State of America' ? 'selected' : '' }}>US- United State of America</option>
                                                                    <option value="India" {{ old('delivery_destination') == 'India' ? 'selected' : '' }}>India</option>
                                                                    <option value="UK - United Kingdom" {{ old('delivery_destination') == 'UK - United Kingdom' ? 'selected' : '' }}>UK - United Kingdom</option>
                                                                    <option value="China" {{ old('delivery_destination') == 'China' ? 'selected' : '' }}>China</option>
                                                                    <option value="Russia" {{ old('delivery_destination') == 'Russia' ? 'selected' : '' }}>Russia</option>
                                                                    <option value="Srilanka" {{ old('delivery_destination') == 'Srilanka' ? 'selected' : '' }}>Srilanka</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Origin Type<span
                                                                        class="text-danger ms-1">*</span></label>
                                                                <select class="select2 select2-hidden-accessible"
                                                                    name="origin_type" data-toggle="select2"
                                                                    data-select2-id="13" id="originType" tabindex="-1"
                                                                    aria-hidden="true">
                                                                    <option data-select2-id="13" value="">Select
                                                                    </option>
                                                                    <option value="CSB IV" {{ old('origin_type') == 'CSB IV' ? 'selected' : '' }}>CSB IV </option>
                                                                    <option value="CSB V" {{ old('origin_type') == 'CSB V' ? 'selected' : '' }}>CSB V</option>
                                                                </select>
                                                                <div id="originTypeError" class="text-danger mt-1"
                                                                    style="display: none;">
                                                                    Please enroll for CSB V to create shipments with CSB
                                                                    V origin type. <a
                                                                        href="{{ route('customer.csb5-form') }}"
                                                                        class="text-danger fw-bold">Go to CSB V
                                                                        Onboarding</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4" style="display:none">
                                                            <div class="mb-3">
                                                                <label class="form-label">Shipping Method <span
                                                                        class="text-danger ms-1">*</span></label>
                                                                <select class="select2 select2-hidden-accessible"
                                                                    name="shipping_method" data-toggle="select2"
                                                                    data-select2-id="9" tabindex="-1"
                                                                    aria-hidden="true">
                                                                    <option data-select2-id="11" value="">Select
                                                                    </option>
                                                                    <option value="United My Delivery" {{ old('shipping_method') == 'United My Delivery' ? 'selected' : '' }}>United My
                                                                        Delivery</option>
                                                                    <option value="United Air Premium" {{ old('shipping_method') == 'United Air Premium' ? 'selected' : '' }}>United Air
                                                                        Premium (UPS ,DPD)</option>
                                                                    <option value="United Ground Premium" {{ old('shipping_method') == 'United Ground Premium' ? 'selected' : '' }}>United Ground
                                                                        Premium</option>
                                                                    <option value="United Eco Post" {{ old('shipping_method') == 'United Eco Post' ? 'selected' : '' }}>United Eco Post
                                                                        (USPS)</option>
                                                                    <option value="United Airexpress" {{ old('shipping_method') == 'United Airexpress' ? 'selected' : '' }}>United Airexpress
                                                                        (UPS 2nd Day, UPS Saver)
                                                                    </option>
                                                                    <option value="United Premium Post" {{ old('shipping_method') == 'United Premium Post' ? 'selected' : '' }}>united premium
                                                                        post (USPS Parcll, Royal
                                                                        Mail)
                                                                    </option>
                                                                    <option value="United My Pickup" {{ old('shipping_method') == 'United My Pickup' ? 'selected' : '' }}>United My Pickup
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="sameAsCustomer" name="shipper_same_as_customer" {{ old('shipper_same_as_customer') ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="sameAsCustomer">
                                                                Shipper Details (Same as Customer)
                                                            </label>
                                                        </div>
                                                        <hr>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Comapany Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="shipper_company_names" value="{{ old('shipper_company_names') }}"
                                                                    placeholder="Company Name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Contact Person <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="shipper_contact_person" value="{{ old('shipper_contact_person') }}"
                                                                    placeholder="Contact Person">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Address Line 1 <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="shipper_address_line1" value="{{ old('shipper_address_line1') }}"
                                                                    placeholder="Address Line 1">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Address Line 2 <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="shipper_address_line2" value="{{ old('shipper_address_line2') }}"
                                                                    placeholder="Address Line 2">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Address Line 3 <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="shipper_address_line3" value="{{ old('shipper_address_line3') }}"
                                                                    placeholder="Address Line 3">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Pincode <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="shipper_pincode" value="{{ old('shipper_pincode') }}" placeholder="Pincode">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">City <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="shipper_city" value="{{ old('shipper_city') }}" placeholder="City">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">State <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="shipper_state" value="{{ old('shipper_state') }}" placeholder="State">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Phone Number</label>
                                                                <div class="iti iti--allow-dropdown">
                                                                    <div class="iti__flag-container">
                                                                        <div class="iti__selected-flag" role="combobox"
                                                                            aria-controls="iti-0__country-listbox"
                                                                            aria-owns="iti-0__country-listbox"
                                                                            aria-expanded="false" tabindex="0"
                                                                            title="United States: +1"
                                                                            aria-activedescendant="iti-0__item-us-preferred">
                                                                            <div class="iti__flag iti__us"></div>
                                                                            <div class="iti__arrow"></div>
                                                                        </div>
                                                                        <ul class="iti__country-list iti__hide"
                                                                            id="iti-0__country-listbox" role="listbox"
                                                                            aria-label="List of countries">
                                                                            <li class="iti__country iti__preferred iti__active"
                                                                                tabindex="-1"
                                                                                id="iti-0__item-us-preferred"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="us"
                                                                                aria-selected="true">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__us">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">United
                                                                                    States</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__preferred"
                                                                                tabindex="-1"
                                                                                id="iti-0__item-gb-preferred"
                                                                                role="option" data-dial-code="44"
                                                                                data-country-code="gb"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gb">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">United
                                                                                    Kingdom</span><span
                                                                                    class="iti__dial-code">+44</span>
                                                                            </li>
                                                                            <li class="iti__divider" role="separator"
                                                                                aria-disabled="true"></li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-af"
                                                                                role="option" data-dial-code="93"
                                                                                data-country-code="af"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__af">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Afghanistan
                                                                                    (&#x202B;افغانستان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+93</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-al"
                                                                                role="option" data-dial-code="355"
                                                                                data-country-code="al"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__al">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Albania
                                                                                    (Shqipëri)</span><span
                                                                                    class="iti__dial-code">+355</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-dz"
                                                                                role="option" data-dial-code="213"
                                                                                data-country-code="dz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__dz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Algeria
                                                                                    (&#x202B;الجزائر&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+213</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-as"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="as"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__as">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">American
                                                                                    Samoa</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ad"
                                                                                role="option" data-dial-code="376"
                                                                                data-country-code="ad"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ad">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Andorra</span><span
                                                                                    class="iti__dial-code">+376</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ao"
                                                                                role="option" data-dial-code="244"
                                                                                data-country-code="ao"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ao">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Angola</span><span
                                                                                    class="iti__dial-code">+244</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ai"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="ai"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ai">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Anguilla</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ag"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="ag"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ag">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Antigua
                                                                                    and Barbuda</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ar"
                                                                                role="option" data-dial-code="54"
                                                                                data-country-code="ar"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ar">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Argentina</span><span
                                                                                    class="iti__dial-code">+54</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-am"
                                                                                role="option" data-dial-code="374"
                                                                                data-country-code="am"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__am">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Armenia
                                                                                    (Հայաստան)</span><span
                                                                                    class="iti__dial-code">+374</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-aw"
                                                                                role="option" data-dial-code="297"
                                                                                data-country-code="aw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__aw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Aruba</span><span
                                                                                    class="iti__dial-code">+297</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ac"
                                                                                role="option" data-dial-code="247"
                                                                                data-country-code="ac"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ac">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ascension
                                                                                    Island</span><span
                                                                                    class="iti__dial-code">+247</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-au"
                                                                                role="option" data-dial-code="61"
                                                                                data-country-code="au"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__au">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Australia</span><span
                                                                                    class="iti__dial-code">+61</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-at"
                                                                                role="option" data-dial-code="43"
                                                                                data-country-code="at"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__at">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Austria
                                                                                    (Österreich)</span><span
                                                                                    class="iti__dial-code">+43</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-az"
                                                                                role="option" data-dial-code="994"
                                                                                data-country-code="az"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__az">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Azerbaijan
                                                                                    (Azərbaycan)</span><span
                                                                                    class="iti__dial-code">+994</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bs"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="bs"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bs">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bahamas</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bh"
                                                                                role="option" data-dial-code="973"
                                                                                data-country-code="bh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bahrain
                                                                                    (&#x202B;البحرين&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+973</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bd"
                                                                                role="option" data-dial-code="880"
                                                                                data-country-code="bd"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bd">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bangladesh
                                                                                    (বাংলাদেশ)</span><span
                                                                                    class="iti__dial-code">+880</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bb"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="bb"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bb">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Barbados</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-by"
                                                                                role="option" data-dial-code="375"
                                                                                data-country-code="by"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__by">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Belarus
                                                                                    (Беларусь)</span><span
                                                                                    class="iti__dial-code">+375</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-be"
                                                                                role="option" data-dial-code="32"
                                                                                data-country-code="be"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__be">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Belgium
                                                                                    (België)</span><span
                                                                                    class="iti__dial-code">+32</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bz"
                                                                                role="option" data-dial-code="501"
                                                                                data-country-code="bz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Belize</span><span
                                                                                    class="iti__dial-code">+501</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bj"
                                                                                role="option" data-dial-code="229"
                                                                                data-country-code="bj"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bj">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Benin
                                                                                    (Bénin)</span><span
                                                                                    class="iti__dial-code">+229</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bm"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="bm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bermuda</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bt"
                                                                                role="option" data-dial-code="975"
                                                                                data-country-code="bt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bhutan
                                                                                    (འབྲུག)</span><span
                                                                                    class="iti__dial-code">+975</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bo"
                                                                                role="option" data-dial-code="591"
                                                                                data-country-code="bo"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bo">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bolivia</span><span
                                                                                    class="iti__dial-code">+591</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ba"
                                                                                role="option" data-dial-code="387"
                                                                                data-country-code="ba"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ba">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bosnia and
                                                                                    Herzegovina </span><span
                                                                                    class="iti__dial-code">+387</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bw"
                                                                                role="option" data-dial-code="267"
                                                                                data-country-code="bw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Botswana</span><span
                                                                                    class="iti__dial-code">+267</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-br"
                                                                                role="option" data-dial-code="55"
                                                                                data-country-code="br"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__br">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Brazil
                                                                                    (Brasil)</span><span
                                                                                    class="iti__dial-code">+55</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-io"
                                                                                role="option" data-dial-code="246"
                                                                                data-country-code="io"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__io">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">British
                                                                                    Indian Ocean Territory</span><span
                                                                                    class="iti__dial-code">+246</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-vg"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="vg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__vg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">British
                                                                                    Virgin Islands</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bn"
                                                                                role="option" data-dial-code="673"
                                                                                data-country-code="bn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Brunei</span><span
                                                                                    class="iti__dial-code">+673</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bg"
                                                                                role="option" data-dial-code="359"
                                                                                data-country-code="bg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bulgaria
                                                                                    (България)</span><span
                                                                                    class="iti__dial-code">+359</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bf"
                                                                                role="option" data-dial-code="226"
                                                                                data-country-code="bf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Burkina
                                                                                    Faso</span><span
                                                                                    class="iti__dial-code">+226</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bi"
                                                                                role="option" data-dial-code="257"
                                                                                data-country-code="bi"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bi">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Burundi
                                                                                    (Uburundi)</span><span
                                                                                    class="iti__dial-code">+257</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kh"
                                                                                role="option" data-dial-code="855"
                                                                                data-country-code="kh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cambodia
                                                                                    (កម្ពុជា)</span><span
                                                                                    class="iti__dial-code">+855</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cm"
                                                                                role="option" data-dial-code="237"
                                                                                data-country-code="cm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cameroon
                                                                                    (Cameroun)</span><span
                                                                                    class="iti__dial-code">+237</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ca"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="ca"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ca">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Canada</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cv"
                                                                                role="option" data-dial-code="238"
                                                                                data-country-code="cv"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cv">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cape Verde
                                                                                    (Kabu Verdi)</span><span
                                                                                    class="iti__dial-code">+238</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bq"
                                                                                role="option" data-dial-code="599"
                                                                                data-country-code="bq"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bq">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Caribbean
                                                                                    Netherlands</span><span
                                                                                    class="iti__dial-code">+599</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ky"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="ky"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ky">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cayman
                                                                                    Islands</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cf"
                                                                                role="option" data-dial-code="236"
                                                                                data-country-code="cf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Central
                                                                                    African Republic</span><span
                                                                                    class="iti__dial-code">+236</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-td"
                                                                                role="option" data-dial-code="235"
                                                                                data-country-code="td"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__td">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Chad
                                                                                    (Tchad)</span><span
                                                                                    class="iti__dial-code">+235</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cl"
                                                                                role="option" data-dial-code="56"
                                                                                data-country-code="cl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Chile</span><span
                                                                                    class="iti__dial-code">+56</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cn"
                                                                                role="option" data-dial-code="86"
                                                                                data-country-code="cn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">China
                                                                                    (中国)</span><span
                                                                                    class="iti__dial-code">+86</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cx"
                                                                                role="option" data-dial-code="61"
                                                                                data-country-code="cx"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cx">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Christmas
                                                                                    Island</span><span
                                                                                    class="iti__dial-code">+61</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cc"
                                                                                role="option" data-dial-code="61"
                                                                                data-country-code="cc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cocos
                                                                                    (Keeling) Islands</span><span
                                                                                    class="iti__dial-code">+61</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-co"
                                                                                role="option" data-dial-code="57"
                                                                                data-country-code="co"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__co">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Colombia</span><span
                                                                                    class="iti__dial-code">+57</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-km"
                                                                                role="option" data-dial-code="269"
                                                                                data-country-code="km"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__km">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Comoros
                                                                                    (&#x202B;جزر
                                                                                    القمر&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+269</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cd"
                                                                                role="option" data-dial-code="243"
                                                                                data-country-code="cd"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cd">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Congo
                                                                                    (DRC)</span><span
                                                                                    class="iti__dial-code">+243</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cg"
                                                                                role="option" data-dial-code="242"
                                                                                data-country-code="cg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Congo
                                                                                    (Republic)</span><span
                                                                                    class="iti__dial-code">+242</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ck"
                                                                                role="option" data-dial-code="682"
                                                                                data-country-code="ck"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ck">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cook
                                                                                    Islands</span><span
                                                                                    class="iti__dial-code">+682</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cr"
                                                                                role="option" data-dial-code="506"
                                                                                data-country-code="cr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Costa
                                                                                    Rica</span><span
                                                                                    class="iti__dial-code">+506</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ci"
                                                                                role="option" data-dial-code="225"
                                                                                data-country-code="ci"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ci">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Côte
                                                                                    d’Ivoire</span><span
                                                                                    class="iti__dial-code">+225</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-hr"
                                                                                role="option" data-dial-code="385"
                                                                                data-country-code="hr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__hr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Croatia
                                                                                    (Hrvatska)</span><span
                                                                                    class="iti__dial-code">+385</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cu"
                                                                                role="option" data-dial-code="53"
                                                                                data-country-code="cu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cuba</span><span
                                                                                    class="iti__dial-code">+53</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cw"
                                                                                role="option" data-dial-code="599"
                                                                                data-country-code="cw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Curaçao</span><span
                                                                                    class="iti__dial-code">+599</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cy"
                                                                                role="option" data-dial-code="357"
                                                                                data-country-code="cy"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cy">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cyprus
                                                                                    (Κύπρος)</span><span
                                                                                    class="iti__dial-code">+357</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cz"
                                                                                role="option" data-dial-code="420"
                                                                                data-country-code="cz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Czech
                                                                                    Republic (Česká
                                                                                    republika)</span><span
                                                                                    class="iti__dial-code">+420</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-dk"
                                                                                role="option" data-dial-code="45"
                                                                                data-country-code="dk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__dk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Denmark
                                                                                    (Danmark)</span><span
                                                                                    class="iti__dial-code">+45</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-dj"
                                                                                role="option" data-dial-code="253"
                                                                                data-country-code="dj"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__dj">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Djibouti</span><span
                                                                                    class="iti__dial-code">+253</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-dm"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="dm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__dm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Dominica</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-do"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="do"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__do">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Dominican
                                                                                    Republic (República
                                                                                    Dominicana)</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ec"
                                                                                role="option" data-dial-code="593"
                                                                                data-country-code="ec"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ec">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ecuador</span><span
                                                                                    class="iti__dial-code">+593</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-eg"
                                                                                role="option" data-dial-code="20"
                                                                                data-country-code="eg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__eg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Egypt
                                                                                    (&#x202B;مصر&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+20</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sv"
                                                                                role="option" data-dial-code="503"
                                                                                data-country-code="sv"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sv">
                                                                                    </div>
                                                                                </div><span class="iti__country-name">El
                                                                                    Salvador</span><span
                                                                                    class="iti__dial-code">+503</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gq"
                                                                                role="option" data-dial-code="240"
                                                                                data-country-code="gq"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gq">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Equatorial
                                                                                    Guinea (Guinea
                                                                                    Ecuatorial)</span><span
                                                                                    class="iti__dial-code">+240</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-er"
                                                                                role="option" data-dial-code="291"
                                                                                data-country-code="er"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__er">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Eritrea</span><span
                                                                                    class="iti__dial-code">+291</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ee"
                                                                                role="option" data-dial-code="372"
                                                                                data-country-code="ee"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ee">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Estonia
                                                                                    (Eesti)</span><span
                                                                                    class="iti__dial-code">+372</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sz"
                                                                                role="option" data-dial-code="268"
                                                                                data-country-code="sz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Eswatini</span><span
                                                                                    class="iti__dial-code">+268</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-et"
                                                                                role="option" data-dial-code="251"
                                                                                data-country-code="et"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__et">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ethiopia</span><span
                                                                                    class="iti__dial-code">+251</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fk"
                                                                                role="option" data-dial-code="500"
                                                                                data-country-code="fk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Falkland
                                                                                    Islands (Islas Malvinas)</span><span
                                                                                    class="iti__dial-code">+500</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fo"
                                                                                role="option" data-dial-code="298"
                                                                                data-country-code="fo"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fo">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Faroe
                                                                                    Islands (Føroyar)</span><span
                                                                                    class="iti__dial-code">+298</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fj"
                                                                                role="option" data-dial-code="679"
                                                                                data-country-code="fj"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fj">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Fiji</span><span
                                                                                    class="iti__dial-code">+679</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fi"
                                                                                role="option" data-dial-code="358"
                                                                                data-country-code="fi"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fi">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Finland
                                                                                    (Suomi)</span><span
                                                                                    class="iti__dial-code">+358</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fr"
                                                                                role="option" data-dial-code="33"
                                                                                data-country-code="fr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">France</span><span
                                                                                    class="iti__dial-code">+33</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gf"
                                                                                role="option" data-dial-code="594"
                                                                                data-country-code="gf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">French
                                                                                    Guiana (Guyane
                                                                                    française)</span><span
                                                                                    class="iti__dial-code">+594</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pf"
                                                                                role="option" data-dial-code="689"
                                                                                data-country-code="pf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">French
                                                                                    Polynesia (Polynésie
                                                                                    française)</span><span
                                                                                    class="iti__dial-code">+689</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ga"
                                                                                role="option" data-dial-code="241"
                                                                                data-country-code="ga"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ga">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Gabon</span><span
                                                                                    class="iti__dial-code">+241</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gm"
                                                                                role="option" data-dial-code="220"
                                                                                data-country-code="gm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Gambia</span><span
                                                                                    class="iti__dial-code">+220</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ge"
                                                                                role="option" data-dial-code="995"
                                                                                data-country-code="ge"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ge">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Georgia
                                                                                    (საქართველო)</span><span
                                                                                    class="iti__dial-code">+995</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-de"
                                                                                role="option" data-dial-code="49"
                                                                                data-country-code="de"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__de">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Germany
                                                                                    (Deutschland)</span><span
                                                                                    class="iti__dial-code">+49</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gh"
                                                                                role="option" data-dial-code="233"
                                                                                data-country-code="gh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ghana
                                                                                    (Gaana)</span><span
                                                                                    class="iti__dial-code">+233</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gi"
                                                                                role="option" data-dial-code="350"
                                                                                data-country-code="gi"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gi">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Gibraltar</span><span
                                                                                    class="iti__dial-code">+350</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gr"
                                                                                role="option" data-dial-code="30"
                                                                                data-country-code="gr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Greece
                                                                                    (Ελλάδα)</span><span
                                                                                    class="iti__dial-code">+30</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gl"
                                                                                role="option" data-dial-code="299"
                                                                                data-country-code="gl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Greenland
                                                                                    (Kalaallit Nunaat)</span><span
                                                                                    class="iti__dial-code">+299</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gd"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="gd"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gd">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Grenada</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gp"
                                                                                role="option" data-dial-code="590"
                                                                                data-country-code="gp"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gp">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guadeloupe</span><span
                                                                                    class="iti__dial-code">+590</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gu"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="gu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guam</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gt"
                                                                                role="option" data-dial-code="502"
                                                                                data-country-code="gt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guatemala</span><span
                                                                                    class="iti__dial-code">+502</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gg"
                                                                                role="option" data-dial-code="44"
                                                                                data-country-code="gg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guernsey</span><span
                                                                                    class="iti__dial-code">+44</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gn"
                                                                                role="option" data-dial-code="224"
                                                                                data-country-code="gn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guinea
                                                                                    (Guinée)</span><span
                                                                                    class="iti__dial-code">+224</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gw"
                                                                                role="option" data-dial-code="245"
                                                                                data-country-code="gw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guinea-Bissau
                                                                                    (Guiné Bissau)</span><span
                                                                                    class="iti__dial-code">+245</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gy"
                                                                                role="option" data-dial-code="592"
                                                                                data-country-code="gy"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gy">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guyana</span><span
                                                                                    class="iti__dial-code">+592</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ht"
                                                                                role="option" data-dial-code="509"
                                                                                data-country-code="ht"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ht">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Haiti</span><span
                                                                                    class="iti__dial-code">+509</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-hn"
                                                                                role="option" data-dial-code="504"
                                                                                data-country-code="hn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__hn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Honduras</span><span
                                                                                    class="iti__dial-code">+504</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-hk"
                                                                                role="option" data-dial-code="852"
                                                                                data-country-code="hk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__hk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Hong Kong
                                                                                    (香港)</span><span
                                                                                    class="iti__dial-code">+852</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-hu"
                                                                                role="option" data-dial-code="36"
                                                                                data-country-code="hu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__hu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Hungary
                                                                                    (Magyarország)</span><span
                                                                                    class="iti__dial-code">+36</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-is"
                                                                                role="option" data-dial-code="354"
                                                                                data-country-code="is"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__is">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Iceland
                                                                                    (Ísland)</span><span
                                                                                    class="iti__dial-code">+354</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-in"
                                                                                role="option" data-dial-code="91"
                                                                                data-country-code="in"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__in">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">India
                                                                                    (भारत)</span><span
                                                                                    class="iti__dial-code">+91</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-id"
                                                                                role="option" data-dial-code="62"
                                                                                data-country-code="id"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__id">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Indonesia</span><span
                                                                                    class="iti__dial-code">+62</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ir"
                                                                                role="option" data-dial-code="98"
                                                                                data-country-code="ir"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ir">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Iran
                                                                                    (&#x202B;ایران&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+98</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-iq"
                                                                                role="option" data-dial-code="964"
                                                                                data-country-code="iq"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__iq">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Iraq
                                                                                    (&#x202B;العراق&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+964</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ie"
                                                                                role="option" data-dial-code="353"
                                                                                data-country-code="ie"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ie">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ireland</span><span
                                                                                    class="iti__dial-code">+353</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-im"
                                                                                role="option" data-dial-code="44"
                                                                                data-country-code="im"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__im">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Isle of
                                                                                    Man</span><span
                                                                                    class="iti__dial-code">+44</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-il"
                                                                                role="option" data-dial-code="972"
                                                                                data-country-code="il"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__il">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Israel
                                                                                    (&#x202B;ישראל&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+972</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-it"
                                                                                role="option" data-dial-code="39"
                                                                                data-country-code="it"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__it">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Italy
                                                                                    (Italia)</span><span
                                                                                    class="iti__dial-code">+39</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-jm"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="jm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__jm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Jamaica</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-jp"
                                                                                role="option" data-dial-code="81"
                                                                                data-country-code="jp"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__jp">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Japan
                                                                                    (日本)</span><span
                                                                                    class="iti__dial-code">+81</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-je"
                                                                                role="option" data-dial-code="44"
                                                                                data-country-code="je"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__je">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Jersey</span><span
                                                                                    class="iti__dial-code">+44</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-jo"
                                                                                role="option" data-dial-code="962"
                                                                                data-country-code="jo"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__jo">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Jordan
                                                                                    (&#x202B;الأردن&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+962</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kz"
                                                                                role="option" data-dial-code="7"
                                                                                data-country-code="kz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kazakhstan
                                                                                    (Казахстан)</span><span
                                                                                    class="iti__dial-code">+7</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ke"
                                                                                role="option" data-dial-code="254"
                                                                                data-country-code="ke"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ke">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kenya</span><span
                                                                                    class="iti__dial-code">+254</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ki"
                                                                                role="option" data-dial-code="686"
                                                                                data-country-code="ki"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ki">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kiribati</span><span
                                                                                    class="iti__dial-code">+686</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-xk"
                                                                                role="option" data-dial-code="383"
                                                                                data-country-code="xk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__xk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kosovo</span><span
                                                                                    class="iti__dial-code">+383</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kw"
                                                                                role="option" data-dial-code="965"
                                                                                data-country-code="kw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kuwait
                                                                                    (&#x202B;الكويت&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+965</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kg"
                                                                                role="option" data-dial-code="996"
                                                                                data-country-code="kg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kyrgyzstan
                                                                                    (Кыргызстан)</span><span
                                                                                    class="iti__dial-code">+996</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-la"
                                                                                role="option" data-dial-code="856"
                                                                                data-country-code="la"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__la">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Laos
                                                                                    (ລາວ)</span><span
                                                                                    class="iti__dial-code">+856</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lv"
                                                                                role="option" data-dial-code="371"
                                                                                data-country-code="lv"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lv">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Latvia
                                                                                    (Latvija)</span><span
                                                                                    class="iti__dial-code">+371</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lb"
                                                                                role="option" data-dial-code="961"
                                                                                data-country-code="lb"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lb">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Lebanon
                                                                                    (&#x202B;لبنان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+961</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ls"
                                                                                role="option" data-dial-code="266"
                                                                                data-country-code="ls"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ls">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Lesotho</span><span
                                                                                    class="iti__dial-code">+266</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lr"
                                                                                role="option" data-dial-code="231"
                                                                                data-country-code="lr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Liberia</span><span
                                                                                    class="iti__dial-code">+231</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ly"
                                                                                role="option" data-dial-code="218"
                                                                                data-country-code="ly"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ly">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Libya
                                                                                    (&#x202B;ليبيا&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+218</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-li"
                                                                                role="option" data-dial-code="423"
                                                                                data-country-code="li"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__li">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Liechtenstein</span><span
                                                                                    class="iti__dial-code">+423</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lt"
                                                                                role="option" data-dial-code="370"
                                                                                data-country-code="lt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Lithuania
                                                                                    (Lietuva)</span><span
                                                                                    class="iti__dial-code">+370</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lu"
                                                                                role="option" data-dial-code="352"
                                                                                data-country-code="lu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Luxembourg</span><span
                                                                                    class="iti__dial-code">+352</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mo"
                                                                                role="option" data-dial-code="853"
                                                                                data-country-code="mo"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mo">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Macau
                                                                                    (澳門)</span><span
                                                                                    class="iti__dial-code">+853</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mk"
                                                                                role="option" data-dial-code="389"
                                                                                data-country-code="mk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Macedonia
                                                                                    (FYROM) (Македонија)</span><span
                                                                                    class="iti__dial-code">+389</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mg"
                                                                                role="option" data-dial-code="261"
                                                                                data-country-code="mg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Madagascar
                                                                                    (Madagasikara)</span><span
                                                                                    class="iti__dial-code">+261</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mw"
                                                                                role="option" data-dial-code="265"
                                                                                data-country-code="mw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Malawi</span><span
                                                                                    class="iti__dial-code">+265</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-my"
                                                                                role="option" data-dial-code="60"
                                                                                data-country-code="my"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__my">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Malaysia</span><span
                                                                                    class="iti__dial-code">+60</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mv"
                                                                                role="option" data-dial-code="960"
                                                                                data-country-code="mv"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mv">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Maldives</span><span
                                                                                    class="iti__dial-code">+960</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ml"
                                                                                role="option" data-dial-code="223"
                                                                                data-country-code="ml"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ml">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mali</span><span
                                                                                    class="iti__dial-code">+223</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mt"
                                                                                role="option" data-dial-code="356"
                                                                                data-country-code="mt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Malta</span><span
                                                                                    class="iti__dial-code">+356</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mh"
                                                                                role="option" data-dial-code="692"
                                                                                data-country-code="mh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Marshall
                                                                                    Islands</span><span
                                                                                    class="iti__dial-code">+692</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mq"
                                                                                role="option" data-dial-code="596"
                                                                                data-country-code="mq"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mq">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Martinique</span><span
                                                                                    class="iti__dial-code">+596</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mr"
                                                                                role="option" data-dial-code="222"
                                                                                data-country-code="mr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mauritania
                                                                                    (&#x202B;موريتانيا&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+222</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mu"
                                                                                role="option" data-dial-code="230"
                                                                                data-country-code="mu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mauritius
                                                                                    (Moris)</span><span
                                                                                    class="iti__dial-code">+230</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-yt"
                                                                                role="option" data-dial-code="262"
                                                                                data-country-code="yt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__yt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mayotte</span><span
                                                                                    class="iti__dial-code">+262</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mx"
                                                                                role="option" data-dial-code="52"
                                                                                data-country-code="mx"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mx">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mexico
                                                                                    (México)</span><span
                                                                                    class="iti__dial-code">+52</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fm"
                                                                                role="option" data-dial-code="691"
                                                                                data-country-code="fm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Micronesia</span><span
                                                                                    class="iti__dial-code">+691</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-md"
                                                                                role="option" data-dial-code="373"
                                                                                data-country-code="md"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__md">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Moldova
                                                                                    (Republica Moldova)</span><span
                                                                                    class="iti__dial-code">+373</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mc"
                                                                                role="option" data-dial-code="377"
                                                                                data-country-code="mc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Monaco</span><span
                                                                                    class="iti__dial-code">+377</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mn"
                                                                                role="option" data-dial-code="976"
                                                                                data-country-code="mn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mongolia
                                                                                    (Монгол)</span><span
                                                                                    class="iti__dial-code">+976</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-me"
                                                                                role="option" data-dial-code="382"
                                                                                data-country-code="me"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__me">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Montenegro
                                                                                    (Crna Gora)</span><span
                                                                                    class="iti__dial-code">+382</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ms"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="ms"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ms">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Montserrat</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ma"
                                                                                role="option" data-dial-code="212"
                                                                                data-country-code="ma"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ma">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Morocco
                                                                                    (&#x202B;المغرب&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+212</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mz"
                                                                                role="option" data-dial-code="258"
                                                                                data-country-code="mz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mozambique
                                                                                    (Moçambique)</span><span
                                                                                    class="iti__dial-code">+258</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mm"
                                                                                role="option" data-dial-code="95"
                                                                                data-country-code="mm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Myanmar
                                                                                    (Burma) (မြန်မာ)</span><span
                                                                                    class="iti__dial-code">+95</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-na"
                                                                                role="option" data-dial-code="264"
                                                                                data-country-code="na"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__na">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Namibia
                                                                                    (Namibië)</span><span
                                                                                    class="iti__dial-code">+264</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nr"
                                                                                role="option" data-dial-code="674"
                                                                                data-country-code="nr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Nauru</span><span
                                                                                    class="iti__dial-code">+674</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-np"
                                                                                role="option" data-dial-code="977"
                                                                                data-country-code="np"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__np">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Nepal
                                                                                    (नेपाल)</span><span
                                                                                    class="iti__dial-code">+977</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nl"
                                                                                role="option" data-dial-code="31"
                                                                                data-country-code="nl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Netherlands
                                                                                    (Nederland)</span><span
                                                                                    class="iti__dial-code">+31</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nc"
                                                                                role="option" data-dial-code="687"
                                                                                data-country-code="nc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">New
                                                                                    Caledonia
                                                                                    (Nouvelle-Calédonie)</span><span
                                                                                    class="iti__dial-code">+687</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nz"
                                                                                role="option" data-dial-code="64"
                                                                                data-country-code="nz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">New
                                                                                    Zealand</span><span
                                                                                    class="iti__dial-code">+64</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ni"
                                                                                role="option" data-dial-code="505"
                                                                                data-country-code="ni"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ni">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Nicaragua</span><span
                                                                                    class="iti__dial-code">+505</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ne"
                                                                                role="option" data-dial-code="227"
                                                                                data-country-code="ne"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ne">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Niger
                                                                                    (Nijar)</span><span
                                                                                    class="iti__dial-code">+227</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ng"
                                                                                role="option" data-dial-code="234"
                                                                                data-country-code="ng"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ng">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Nigeria</span><span
                                                                                    class="iti__dial-code">+234</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nu"
                                                                                role="option" data-dial-code="683"
                                                                                data-country-code="nu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Niue</span><span
                                                                                    class="iti__dial-code">+683</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nf"
                                                                                role="option" data-dial-code="672"
                                                                                data-country-code="nf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Norfolk
                                                                                    Island</span><span
                                                                                    class="iti__dial-code">+672</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kp"
                                                                                role="option" data-dial-code="850"
                                                                                data-country-code="kp"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kp">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">North
                                                                                    Korea (조선 민주주의 인민 공화국)</span><span
                                                                                    class="iti__dial-code">+850</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mp"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="mp"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mp">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Northern
                                                                                    Mariana Islands</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-no"
                                                                                role="option" data-dial-code="47"
                                                                                data-country-code="no"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__no">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Norway
                                                                                    (Norge)</span><span
                                                                                    class="iti__dial-code">+47</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-om"
                                                                                role="option" data-dial-code="968"
                                                                                data-country-code="om"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__om">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Oman
                                                                                    (&#x202B;عُمان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+968</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pk"
                                                                                role="option" data-dial-code="92"
                                                                                data-country-code="pk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Pakistan
                                                                                    (&#x202B;پاکستان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+92</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pw"
                                                                                role="option" data-dial-code="680"
                                                                                data-country-code="pw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Palau</span><span
                                                                                    class="iti__dial-code">+680</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ps"
                                                                                role="option" data-dial-code="970"
                                                                                data-country-code="ps"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ps">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Palestine
                                                                                    (&#x202B;فلسطين&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+970</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pa"
                                                                                role="option" data-dial-code="507"
                                                                                data-country-code="pa"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pa">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Panama
                                                                                    (Panamá)</span><span
                                                                                    class="iti__dial-code">+507</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pg"
                                                                                role="option" data-dial-code="675"
                                                                                data-country-code="pg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Papua New
                                                                                    Guinea</span><span
                                                                                    class="iti__dial-code">+675</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-py"
                                                                                role="option" data-dial-code="595"
                                                                                data-country-code="py"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__py">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Paraguay</span><span
                                                                                    class="iti__dial-code">+595</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pe"
                                                                                role="option" data-dial-code="51"
                                                                                data-country-code="pe"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pe">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Peru
                                                                                    (Perú)</span><span
                                                                                    class="iti__dial-code">+51</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ph"
                                                                                role="option" data-dial-code="63"
                                                                                data-country-code="ph"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ph">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Philippines</span><span
                                                                                    class="iti__dial-code">+63</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pl"
                                                                                role="option" data-dial-code="48"
                                                                                data-country-code="pl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Poland
                                                                                    (Polska)</span><span
                                                                                    class="iti__dial-code">+48</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pt"
                                                                                role="option" data-dial-code="351"
                                                                                data-country-code="pt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Portugal</span><span
                                                                                    class="iti__dial-code">+351</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pr"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="pr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Puerto
                                                                                    Rico</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-qa"
                                                                                role="option" data-dial-code="974"
                                                                                data-country-code="qa"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__qa">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Qatar
                                                                                    (&#x202B;قطر&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+974</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-re"
                                                                                role="option" data-dial-code="262"
                                                                                data-country-code="re"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__re">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Réunion
                                                                                    (La Réunion)</span><span
                                                                                    class="iti__dial-code">+262</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ro"
                                                                                role="option" data-dial-code="40"
                                                                                data-country-code="ro"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ro">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Romania
                                                                                    (România)</span><span
                                                                                    class="iti__dial-code">+40</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ru"
                                                                                role="option" data-dial-code="7"
                                                                                data-country-code="ru"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ru">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Russia
                                                                                    (Россия)</span><span
                                                                                    class="iti__dial-code">+7</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-rw"
                                                                                role="option" data-dial-code="250"
                                                                                data-country-code="rw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__rw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Rwanda</span><span
                                                                                    class="iti__dial-code">+250</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bl"
                                                                                role="option" data-dial-code="590"
                                                                                data-country-code="bl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Barthélemy</span><span
                                                                                    class="iti__dial-code">+590</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sh"
                                                                                role="option" data-dial-code="290"
                                                                                data-country-code="sh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Helena</span><span
                                                                                    class="iti__dial-code">+290</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kn"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="kn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Kitts and Nevis</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lc"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="lc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Lucia</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mf"
                                                                                role="option" data-dial-code="590"
                                                                                data-country-code="mf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Martin (Saint-Martin (partie
                                                                                    française))</span><span
                                                                                    class="iti__dial-code">+590</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pm"
                                                                                role="option" data-dial-code="508"
                                                                                data-country-code="pm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Pierre and Miquelon </span><span
                                                                                    class="iti__dial-code">+508</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-vc"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="vc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__vc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Vincent and the
                                                                                    Grenadines</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ws"
                                                                                role="option" data-dial-code="685"
                                                                                data-country-code="ws"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ws">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Samoa</span><span
                                                                                    class="iti__dial-code">+685</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sm"
                                                                                role="option" data-dial-code="378"
                                                                                data-country-code="sm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">San
                                                                                    Marino</span><span
                                                                                    class="iti__dial-code">+378</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-st"
                                                                                role="option" data-dial-code="239"
                                                                                data-country-code="st"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__st">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">São Tomé
                                                                                    and Príncipe (São Tomé e
                                                                                    Príncipe)</span><span
                                                                                    class="iti__dial-code">+239</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sa"
                                                                                role="option" data-dial-code="966"
                                                                                data-country-code="sa"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sa">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saudi
                                                                                    Arabia (&#x202B;المملكة العربية
                                                                                    السعودية&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+966</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sn"
                                                                                role="option" data-dial-code="221"
                                                                                data-country-code="sn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Senegal
                                                                                    (Sénégal)</span><span
                                                                                    class="iti__dial-code">+221</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-rs"
                                                                                role="option" data-dial-code="381"
                                                                                data-country-code="rs"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__rs">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Serbia
                                                                                    (Србија)</span><span
                                                                                    class="iti__dial-code">+381</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sc"
                                                                                role="option" data-dial-code="248"
                                                                                data-country-code="sc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Seychelles</span><span
                                                                                    class="iti__dial-code">+248</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sl"
                                                                                role="option" data-dial-code="232"
                                                                                data-country-code="sl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Sierra
                                                                                    Leone</span><span
                                                                                    class="iti__dial-code">+232</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sg"
                                                                                role="option" data-dial-code="65"
                                                                                data-country-code="sg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Singapore</span><span
                                                                                    class="iti__dial-code">+65</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sx"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="sx"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sx">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Sint
                                                                                    Maarten</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sk"
                                                                                role="option" data-dial-code="421"
                                                                                data-country-code="sk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Slovakia
                                                                                    (Slovensko)</span><span
                                                                                    class="iti__dial-code">+421</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-si"
                                                                                role="option" data-dial-code="386"
                                                                                data-country-code="si"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__si">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Slovenia
                                                                                    (Slovenija)</span><span
                                                                                    class="iti__dial-code">+386</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sb"
                                                                                role="option" data-dial-code="677"
                                                                                data-country-code="sb"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sb">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Solomon
                                                                                    Islands</span><span
                                                                                    class="iti__dial-code">+677</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-so"
                                                                                role="option" data-dial-code="252"
                                                                                data-country-code="so"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__so">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Somalia
                                                                                    (Soomaaliya)</span><span
                                                                                    class="iti__dial-code">+252</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-za"
                                                                                role="option" data-dial-code="27"
                                                                                data-country-code="za"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__za">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">South
                                                                                    Africa</span><span
                                                                                    class="iti__dial-code">+27</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kr"
                                                                                role="option" data-dial-code="82"
                                                                                data-country-code="kr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">South
                                                                                    Korea (대한민국)</span><span
                                                                                    class="iti__dial-code">+82</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ss"
                                                                                role="option" data-dial-code="211"
                                                                                data-country-code="ss"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ss">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">South
                                                                                    Sudan (&#x202B;جنوب
                                                                                    السودان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+211</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-es"
                                                                                role="option" data-dial-code="34"
                                                                                data-country-code="es"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__es">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Spain
                                                                                    (España)</span><span
                                                                                    class="iti__dial-code">+34</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lk"
                                                                                role="option" data-dial-code="94"
                                                                                data-country-code="lk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Sri Lanka
                                                                                    (ශ්&zwj;රී ලංකාව)</span><span
                                                                                    class="iti__dial-code">+94</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sd"
                                                                                role="option" data-dial-code="249"
                                                                                data-country-code="sd"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sd">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Sudan
                                                                                    (&#x202B;السودان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+249</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sr"
                                                                                role="option" data-dial-code="597"
                                                                                data-country-code="sr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Suriname</span><span
                                                                                    class="iti__dial-code">+597</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sj"
                                                                                role="option" data-dial-code="47"
                                                                                data-country-code="sj"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sj">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Svalbard
                                                                                    and Jan Mayen</span><span
                                                                                    class="iti__dial-code">+47</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-se"
                                                                                role="option" data-dial-code="46"
                                                                                data-country-code="se"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__se">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Sweden
                                                                                    (Sverige)</span><span
                                                                                    class="iti__dial-code">+46</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ch"
                                                                                role="option" data-dial-code="41"
                                                                                data-country-code="ch"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ch">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Switzerland
                                                                                    (Schweiz)</span><span
                                                                                    class="iti__dial-code">+41</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sy"
                                                                                role="option" data-dial-code="963"
                                                                                data-country-code="sy"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sy">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Syria
                                                                                    (&#x202B;سوريا&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+963</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tw"
                                                                                role="option" data-dial-code="886"
                                                                                data-country-code="tw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Taiwan
                                                                                    (台灣)</span><span
                                                                                    class="iti__dial-code">+886</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tj"
                                                                                role="option" data-dial-code="992"
                                                                                data-country-code="tj"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tj">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tajikistan</span><span
                                                                                    class="iti__dial-code">+992</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tz"
                                                                                role="option" data-dial-code="255"
                                                                                data-country-code="tz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tanzania</span><span
                                                                                    class="iti__dial-code">+255</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-th"
                                                                                role="option" data-dial-code="66"
                                                                                data-country-code="th"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__th">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Thailand
                                                                                    (ไทย)</span><span
                                                                                    class="iti__dial-code">+66</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tl"
                                                                                role="option" data-dial-code="670"
                                                                                data-country-code="tl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Timor-Leste</span><span
                                                                                    class="iti__dial-code">+670</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tg"
                                                                                role="option" data-dial-code="228"
                                                                                data-country-code="tg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Togo</span><span
                                                                                    class="iti__dial-code">+228</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tk"
                                                                                role="option" data-dial-code="690"
                                                                                data-country-code="tk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tokelau</span><span
                                                                                    class="iti__dial-code">+690</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-to"
                                                                                role="option" data-dial-code="676"
                                                                                data-country-code="to"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__to">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tonga</span><span
                                                                                    class="iti__dial-code">+676</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tt"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="tt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Trinidad
                                                                                    and Tobago</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tn"
                                                                                role="option" data-dial-code="216"
                                                                                data-country-code="tn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tunisia
                                                                                    (&#x202B;تونس&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+216</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tr"
                                                                                role="option" data-dial-code="90"
                                                                                data-country-code="tr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Turkey
                                                                                    (Türkiye)</span><span
                                                                                    class="iti__dial-code">+90</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tm"
                                                                                role="option" data-dial-code="993"
                                                                                data-country-code="tm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Turkmenistan</span><span
                                                                                    class="iti__dial-code">+993</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tc"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="tc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Turks and
                                                                                    Caicos Islands</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tv"
                                                                                role="option" data-dial-code="688"
                                                                                data-country-code="tv"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tv">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tuvalu</span><span
                                                                                    class="iti__dial-code">+688</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-vi"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="vi"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__vi">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">U.S.
                                                                                    Virgin Islands</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ug"
                                                                                role="option" data-dial-code="256"
                                                                                data-country-code="ug"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ug">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Uganda</span><span
                                                                                    class="iti__dial-code">+256</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ua"
                                                                                role="option" data-dial-code="380"
                                                                                data-country-code="ua"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ua">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ukraine
                                                                                    (Україна)</span><span
                                                                                    class="iti__dial-code">+380</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ae"
                                                                                role="option" data-dial-code="971"
                                                                                data-country-code="ae"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ae">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">United
                                                                                    Arab Emirates (&#x202B;الإمارات
                                                                                    العربية
                                                                                    المتحدة&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+971</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gb"
                                                                                role="option" data-dial-code="44"
                                                                                data-country-code="gb"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gb">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">United
                                                                                    Kingdom</span><span
                                                                                    class="iti__dial-code">+44</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-us"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="us"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__us">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">United
                                                                                    States</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-uy"
                                                                                role="option" data-dial-code="598"
                                                                                data-country-code="uy"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__uy">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Uruguay</span><span
                                                                                    class="iti__dial-code">+598</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-uz"
                                                                                role="option" data-dial-code="998"
                                                                                data-country-code="uz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__uz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Uzbekistan
                                                                                    (Oʻzbekiston)</span><span
                                                                                    class="iti__dial-code">+998</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-vu"
                                                                                role="option" data-dial-code="678"
                                                                                data-country-code="vu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__vu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Vanuatu</span><span
                                                                                    class="iti__dial-code">+678</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-va"
                                                                                role="option" data-dial-code="39"
                                                                                data-country-code="va"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__va">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Vatican
                                                                                    City (Città del
                                                                                    Vaticano)</span><span
                                                                                    class="iti__dial-code">+39</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ve"
                                                                                role="option" data-dial-code="58"
                                                                                data-country-code="ve"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ve">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Venezuela</span><span
                                                                                    class="iti__dial-code">+58</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-vn"
                                                                                role="option" data-dial-code="84"
                                                                                data-country-code="vn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__vn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Vietnam
                                                                                    (Việt Nam)</span><span
                                                                                    class="iti__dial-code">+84</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-wf"
                                                                                role="option" data-dial-code="681"
                                                                                data-country-code="wf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__wf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Wallis and
                                                                                    Futuna
                                                                                    (Wallis-et-Futuna)</span><span
                                                                                    class="iti__dial-code">+681</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-eh"
                                                                                role="option" data-dial-code="212"
                                                                                data-country-code="eh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__eh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Western
                                                                                    Sahara (&#x202B;الصحراء
                                                                                    الغربية&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+212</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ye"
                                                                                role="option" data-dial-code="967"
                                                                                data-country-code="ye"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ye">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Yemen
                                                                                    (&#x202B;اليمن&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+967</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-zm"
                                                                                role="option" data-dial-code="260"
                                                                                data-country-code="zm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__zm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Zambia</span><span
                                                                                    class="iti__dial-code">+260</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-zw"
                                                                                role="option" data-dial-code="263"
                                                                                data-country-code="zw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__zw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Zimbabwe</span><span
                                                                                    class="iti__dial-code">+263</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ax"
                                                                                role="option" data-dial-code="358"
                                                                                data-country-code="ax"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ax">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Åland
                                                                                    Islands</span><span
                                                                                    class="iti__dial-code">+358</span>
                                                                            </li>
                                                                        </ul>
                                                                    </div><input type="text" class="form-control phone"
                                                                        name="shipper_phone_number" value="{{ old('shipper_phone_number') }}" autocomplete="off"
                                                                        data-intl-tel-input-id="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <label class="form-label">Email <span
                                                                            class="text-danger">*</span></label>
                                                                    <div class="form-check form-switch mb-1">
                                                                        <label
                                                                            class="form-check-label d-flex align-items-center gap-2">
                                                                            <span>Email Opt Out</span>
                                                                            <input
                                                                                class="form-check-input form-check-input-sm switchCheckDefault ms-auto"
                                                                                type="checkbox" role="switch"
                                                                                checked="">
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <input type="email" class="form-control"
                                                                    name="shipper_emails" value="{{ old('shipper_emails') }}" placeholder="Email Address">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">KYC Type<span
                                                                        class="text-danger ms-1">*</span></label>
                                                                <select class="select2 select2-hidden-accessible"
                                                                    name="shipper_kyc_type" data-toggle="select2"
                                                                    data-select2-id="11" tabindex="-1"
                                                                    aria-hidden="true">
                                                                    <option value="">Select</option>
                                                                    <option value="GST (Normal)" {{ old('shipper_kyc_type') == 'GST (Normal)' ? 'selected' : '' }}>GST (Normal)</option>
                                                                    <option value="Aadhar Card" {{ old('shipper_kyc_type') == 'Aadhar Card' ? 'selected' : '' }}>Aadhar Card</option>
                                                                    <option value="PAN Card" {{ old('shipper_kyc_type') == 'PAN Card' ? 'selected' : '' }}>PAN Card</option>
                                                                    <option value="Passport Number" {{ old('shipper_kyc_type') == 'Passport Number' ? 'selected' : '' }}>Passport Number</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">KYC Number</label>
                                                                <input type="text" class="form-control"
                                                                    name="shipper_kyc_number" value="{{ old('shipper_kyc_number') }}" placeholder="KYC Number">
                                                            </div>
                                                        </div>
                                                        <div class="mt-4 d-flex align-items-center">
                                                            <button type="button" class="btn btn-primary"
                                                                id="nextToConsignee">Next</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Basic Info -->
                                        <!-- Address Info -->
                                        <div class="accordion-item border-top rounded mb-3">
                                            <div class="accordion-header">
                                                <a href="#" class="accordion-button accordion-custom-button rounded"
                                                    data-bs-toggle="collapse" data-bs-target="#address">
                                                    <span class="avatar avatar-md rounded me-1">2</span>
                                                    Consignee Info (ship to)
                                                </a>
                                            </div>
                                            <div class="accordion-collapse collapse" id="address"
                                                data-bs-parent="#main_accordion">
                                                <div class="accordion-body border-top">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Consignee Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="consignee_name" value="{{ old('consignee_name') }}" placeholder="Consignee Name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Contact Person <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="consignee_contact_person" value="{{ old('consignee_contact_person') }}"
                                                                    placeholder="Contact Person">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Address Line 1 <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="consignee_address_line1" value="{{ old('consignee_address_line1') }}"
                                                                    placeholder="Address Line 1">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Address Line 2 <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="consignee_address_line2" value="{{ old('consignee_address_line2') }}"
                                                                    placeholder="Address Line 2">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Address Line 3 </label>
                                                                <input type="text" class="form-control"
                                                                    name="consignee_address_line3" value="{{ old('consignee_address_line3') }}"
                                                                    placeholder="Address Line 3">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">ZIP Code <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="consignee_zip_code" value="{{ old('consignee_zip_code') }}" placeholder="ZIP Code">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">City <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="consignee_city" value="{{ old('consignee_city') }}" placeholder="City">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">State </label>
                                                                <input type="text" class="form-control"
                                                                    name="consignee_state" value="{{ old('consignee_state') }}" placeholder="State">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Phone Number</label>
                                                                <div class="iti iti--allow-dropdown">
                                                                    <div class="iti__flag-container">
                                                                        <div class="iti__selected-flag" role="combobox"
                                                                            aria-controls="iti-0__country-listbox"
                                                                            aria-owns="iti-0__country-listbox"
                                                                            aria-expanded="false" tabindex="0"
                                                                            title="United States: +1"
                                                                            aria-activedescendant="iti-0__item-us-preferred">
                                                                            <div class="iti__flag iti__us"></div>
                                                                            <div class="iti__arrow"></div>
                                                                        </div>
                                                                        <ul class="iti__country-list iti__hide"
                                                                            id="iti-0__country-listbox" role="listbox"
                                                                            aria-label="List of countries">
                                                                            <li class="iti__country iti__preferred iti__active"
                                                                                tabindex="-1"
                                                                                id="iti-0__item-us-preferred"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="us"
                                                                                aria-selected="true">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__us">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">United
                                                                                    States</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__preferred"
                                                                                tabindex="-1"
                                                                                id="iti-0__item-gb-preferred"
                                                                                role="option" data-dial-code="44"
                                                                                data-country-code="gb"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gb">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">United
                                                                                    Kingdom</span><span
                                                                                    class="iti__dial-code">+44</span>
                                                                            </li>
                                                                            <li class="iti__divider" role="separator"
                                                                                aria-disabled="true"></li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-af"
                                                                                role="option" data-dial-code="93"
                                                                                data-country-code="af"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__af">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Afghanistan
                                                                                    (&#x202B;افغانستان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+93</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-al"
                                                                                role="option" data-dial-code="355"
                                                                                data-country-code="al"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__al">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Albania
                                                                                    (Shqipëri)</span><span
                                                                                    class="iti__dial-code">+355</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-dz"
                                                                                role="option" data-dial-code="213"
                                                                                data-country-code="dz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__dz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Algeria
                                                                                    (&#x202B;الجزائر&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+213</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-as"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="as"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__as">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">American
                                                                                    Samoa</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ad"
                                                                                role="option" data-dial-code="376"
                                                                                data-country-code="ad"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ad">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Andorra</span><span
                                                                                    class="iti__dial-code">+376</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ao"
                                                                                role="option" data-dial-code="244"
                                                                                data-country-code="ao"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ao">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Angola</span><span
                                                                                    class="iti__dial-code">+244</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ai"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="ai"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ai">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Anguilla</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ag"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="ag"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ag">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Antigua
                                                                                    and Barbuda</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ar"
                                                                                role="option" data-dial-code="54"
                                                                                data-country-code="ar"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ar">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Argentina</span><span
                                                                                    class="iti__dial-code">+54</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-am"
                                                                                role="option" data-dial-code="374"
                                                                                data-country-code="am"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__am">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Armenia
                                                                                    (Հայաստան)</span><span
                                                                                    class="iti__dial-code">+374</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-aw"
                                                                                role="option" data-dial-code="297"
                                                                                data-country-code="aw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__aw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Aruba</span><span
                                                                                    class="iti__dial-code">+297</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ac"
                                                                                role="option" data-dial-code="247"
                                                                                data-country-code="ac"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ac">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ascension
                                                                                    Island</span><span
                                                                                    class="iti__dial-code">+247</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-au"
                                                                                role="option" data-dial-code="61"
                                                                                data-country-code="au"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__au">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Australia</span><span
                                                                                    class="iti__dial-code">+61</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-at"
                                                                                role="option" data-dial-code="43"
                                                                                data-country-code="at"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__at">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Austria
                                                                                    (Österreich)</span><span
                                                                                    class="iti__dial-code">+43</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-az"
                                                                                role="option" data-dial-code="994"
                                                                                data-country-code="az"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__az">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Azerbaijan
                                                                                    (Azərbaycan)</span><span
                                                                                    class="iti__dial-code">+994</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bs"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="bs"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bs">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bahamas</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bh"
                                                                                role="option" data-dial-code="973"
                                                                                data-country-code="bh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bahrain
                                                                                    (&#x202B;البحرين&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+973</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bd"
                                                                                role="option" data-dial-code="880"
                                                                                data-country-code="bd"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bd">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bangladesh
                                                                                    (বাংলাদেশ)</span><span
                                                                                    class="iti__dial-code">+880</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bb"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="bb"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bb">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Barbados</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-by"
                                                                                role="option" data-dial-code="375"
                                                                                data-country-code="by"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__by">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Belarus
                                                                                    (Беларусь)</span><span
                                                                                    class="iti__dial-code">+375</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-be"
                                                                                role="option" data-dial-code="32"
                                                                                data-country-code="be"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__be">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Belgium
                                                                                    (België)</span><span
                                                                                    class="iti__dial-code">+32</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bz"
                                                                                role="option" data-dial-code="501"
                                                                                data-country-code="bz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Belize</span><span
                                                                                    class="iti__dial-code">+501</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bj"
                                                                                role="option" data-dial-code="229"
                                                                                data-country-code="bj"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bj">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Benin
                                                                                    (Bénin)</span><span
                                                                                    class="iti__dial-code">+229</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bm"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="bm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bermuda</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bt"
                                                                                role="option" data-dial-code="975"
                                                                                data-country-code="bt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bhutan
                                                                                    (འབྲུག)</span><span
                                                                                    class="iti__dial-code">+975</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bo"
                                                                                role="option" data-dial-code="591"
                                                                                data-country-code="bo"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bo">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bolivia</span><span
                                                                                    class="iti__dial-code">+591</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ba"
                                                                                role="option" data-dial-code="387"
                                                                                data-country-code="ba"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ba">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bosnia and
                                                                                    Herzegovina </span><span
                                                                                    class="iti__dial-code">+387</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bw"
                                                                                role="option" data-dial-code="267"
                                                                                data-country-code="bw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Botswana</span><span
                                                                                    class="iti__dial-code">+267</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-br"
                                                                                role="option" data-dial-code="55"
                                                                                data-country-code="br"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__br">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Brazil
                                                                                    (Brasil)</span><span
                                                                                    class="iti__dial-code">+55</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-io"
                                                                                role="option" data-dial-code="246"
                                                                                data-country-code="io"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__io">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">British
                                                                                    Indian Ocean Territory</span><span
                                                                                    class="iti__dial-code">+246</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-vg"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="vg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__vg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">British
                                                                                    Virgin Islands</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bn"
                                                                                role="option" data-dial-code="673"
                                                                                data-country-code="bn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Brunei</span><span
                                                                                    class="iti__dial-code">+673</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bg"
                                                                                role="option" data-dial-code="359"
                                                                                data-country-code="bg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Bulgaria
                                                                                    (България)</span><span
                                                                                    class="iti__dial-code">+359</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bf"
                                                                                role="option" data-dial-code="226"
                                                                                data-country-code="bf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Burkina
                                                                                    Faso</span><span
                                                                                    class="iti__dial-code">+226</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bi"
                                                                                role="option" data-dial-code="257"
                                                                                data-country-code="bi"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bi">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Burundi
                                                                                    (Uburundi)</span><span
                                                                                    class="iti__dial-code">+257</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kh"
                                                                                role="option" data-dial-code="855"
                                                                                data-country-code="kh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cambodia
                                                                                    (កម្ពុជា)</span><span
                                                                                    class="iti__dial-code">+855</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cm"
                                                                                role="option" data-dial-code="237"
                                                                                data-country-code="cm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cameroon
                                                                                    (Cameroun)</span><span
                                                                                    class="iti__dial-code">+237</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ca"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="ca"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ca">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Canada</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cv"
                                                                                role="option" data-dial-code="238"
                                                                                data-country-code="cv"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cv">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cape Verde
                                                                                    (Kabu Verdi)</span><span
                                                                                    class="iti__dial-code">+238</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bq"
                                                                                role="option" data-dial-code="599"
                                                                                data-country-code="bq"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bq">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Caribbean
                                                                                    Netherlands</span><span
                                                                                    class="iti__dial-code">+599</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ky"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="ky"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ky">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cayman
                                                                                    Islands</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cf"
                                                                                role="option" data-dial-code="236"
                                                                                data-country-code="cf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Central
                                                                                    African Republic</span><span
                                                                                    class="iti__dial-code">+236</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-td"
                                                                                role="option" data-dial-code="235"
                                                                                data-country-code="td"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__td">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Chad
                                                                                    (Tchad)</span><span
                                                                                    class="iti__dial-code">+235</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cl"
                                                                                role="option" data-dial-code="56"
                                                                                data-country-code="cl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Chile</span><span
                                                                                    class="iti__dial-code">+56</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cn"
                                                                                role="option" data-dial-code="86"
                                                                                data-country-code="cn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">China
                                                                                    (中国)</span><span
                                                                                    class="iti__dial-code">+86</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cx"
                                                                                role="option" data-dial-code="61"
                                                                                data-country-code="cx"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cx">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Christmas
                                                                                    Island</span><span
                                                                                    class="iti__dial-code">+61</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cc"
                                                                                role="option" data-dial-code="61"
                                                                                data-country-code="cc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cocos
                                                                                    (Keeling) Islands</span><span
                                                                                    class="iti__dial-code">+61</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-co"
                                                                                role="option" data-dial-code="57"
                                                                                data-country-code="co"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__co">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Colombia</span><span
                                                                                    class="iti__dial-code">+57</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-km"
                                                                                role="option" data-dial-code="269"
                                                                                data-country-code="km"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__km">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Comoros
                                                                                    (&#x202B;جزر
                                                                                    القمر&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+269</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cd"
                                                                                role="option" data-dial-code="243"
                                                                                data-country-code="cd"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cd">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Congo
                                                                                    (DRC)</span><span
                                                                                    class="iti__dial-code">+243</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cg"
                                                                                role="option" data-dial-code="242"
                                                                                data-country-code="cg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Congo
                                                                                    (Republic)</span><span
                                                                                    class="iti__dial-code">+242</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ck"
                                                                                role="option" data-dial-code="682"
                                                                                data-country-code="ck"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ck">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cook
                                                                                    Islands</span><span
                                                                                    class="iti__dial-code">+682</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cr"
                                                                                role="option" data-dial-code="506"
                                                                                data-country-code="cr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Costa
                                                                                    Rica</span><span
                                                                                    class="iti__dial-code">+506</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ci"
                                                                                role="option" data-dial-code="225"
                                                                                data-country-code="ci"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ci">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Côte
                                                                                    d’Ivoire</span><span
                                                                                    class="iti__dial-code">+225</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-hr"
                                                                                role="option" data-dial-code="385"
                                                                                data-country-code="hr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__hr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Croatia
                                                                                    (Hrvatska)</span><span
                                                                                    class="iti__dial-code">+385</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cu"
                                                                                role="option" data-dial-code="53"
                                                                                data-country-code="cu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cuba</span><span
                                                                                    class="iti__dial-code">+53</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cw"
                                                                                role="option" data-dial-code="599"
                                                                                data-country-code="cw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Curaçao</span><span
                                                                                    class="iti__dial-code">+599</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cy"
                                                                                role="option" data-dial-code="357"
                                                                                data-country-code="cy"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cy">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Cyprus
                                                                                    (Κύπρος)</span><span
                                                                                    class="iti__dial-code">+357</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-cz"
                                                                                role="option" data-dial-code="420"
                                                                                data-country-code="cz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__cz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Czech
                                                                                    Republic (Česká
                                                                                    republika)</span><span
                                                                                    class="iti__dial-code">+420</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-dk"
                                                                                role="option" data-dial-code="45"
                                                                                data-country-code="dk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__dk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Denmark
                                                                                    (Danmark)</span><span
                                                                                    class="iti__dial-code">+45</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-dj"
                                                                                role="option" data-dial-code="253"
                                                                                data-country-code="dj"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__dj">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Djibouti</span><span
                                                                                    class="iti__dial-code">+253</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-dm"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="dm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__dm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Dominica</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-do"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="do"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__do">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Dominican
                                                                                    Republic (República
                                                                                    Dominicana)</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ec"
                                                                                role="option" data-dial-code="593"
                                                                                data-country-code="ec"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ec">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ecuador</span><span
                                                                                    class="iti__dial-code">+593</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-eg"
                                                                                role="option" data-dial-code="20"
                                                                                data-country-code="eg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__eg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Egypt
                                                                                    (&#x202B;مصر&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+20</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sv"
                                                                                role="option" data-dial-code="503"
                                                                                data-country-code="sv"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sv">
                                                                                    </div>
                                                                                </div><span class="iti__country-name">El
                                                                                    Salvador</span><span
                                                                                    class="iti__dial-code">+503</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gq"
                                                                                role="option" data-dial-code="240"
                                                                                data-country-code="gq"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gq">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Equatorial
                                                                                    Guinea (Guinea
                                                                                    Ecuatorial)</span><span
                                                                                    class="iti__dial-code">+240</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-er"
                                                                                role="option" data-dial-code="291"
                                                                                data-country-code="er"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__er">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Eritrea</span><span
                                                                                    class="iti__dial-code">+291</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ee"
                                                                                role="option" data-dial-code="372"
                                                                                data-country-code="ee"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ee">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Estonia
                                                                                    (Eesti)</span><span
                                                                                    class="iti__dial-code">+372</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sz"
                                                                                role="option" data-dial-code="268"
                                                                                data-country-code="sz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Eswatini</span><span
                                                                                    class="iti__dial-code">+268</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-et"
                                                                                role="option" data-dial-code="251"
                                                                                data-country-code="et"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__et">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ethiopia</span><span
                                                                                    class="iti__dial-code">+251</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fk"
                                                                                role="option" data-dial-code="500"
                                                                                data-country-code="fk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Falkland
                                                                                    Islands (Islas Malvinas)</span><span
                                                                                    class="iti__dial-code">+500</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fo"
                                                                                role="option" data-dial-code="298"
                                                                                data-country-code="fo"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fo">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Faroe
                                                                                    Islands (Føroyar)</span><span
                                                                                    class="iti__dial-code">+298</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fj"
                                                                                role="option" data-dial-code="679"
                                                                                data-country-code="fj"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fj">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Fiji</span><span
                                                                                    class="iti__dial-code">+679</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fi"
                                                                                role="option" data-dial-code="358"
                                                                                data-country-code="fi"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fi">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Finland
                                                                                    (Suomi)</span><span
                                                                                    class="iti__dial-code">+358</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fr"
                                                                                role="option" data-dial-code="33"
                                                                                data-country-code="fr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">France</span><span
                                                                                    class="iti__dial-code">+33</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gf"
                                                                                role="option" data-dial-code="594"
                                                                                data-country-code="gf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">French
                                                                                    Guiana (Guyane
                                                                                    française)</span><span
                                                                                    class="iti__dial-code">+594</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pf"
                                                                                role="option" data-dial-code="689"
                                                                                data-country-code="pf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">French
                                                                                    Polynesia (Polynésie
                                                                                    française)</span><span
                                                                                    class="iti__dial-code">+689</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ga"
                                                                                role="option" data-dial-code="241"
                                                                                data-country-code="ga"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ga">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Gabon</span><span
                                                                                    class="iti__dial-code">+241</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gm"
                                                                                role="option" data-dial-code="220"
                                                                                data-country-code="gm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Gambia</span><span
                                                                                    class="iti__dial-code">+220</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ge"
                                                                                role="option" data-dial-code="995"
                                                                                data-country-code="ge"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ge">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Georgia
                                                                                    (საქართველო)</span><span
                                                                                    class="iti__dial-code">+995</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-de"
                                                                                role="option" data-dial-code="49"
                                                                                data-country-code="de"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__de">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Germany
                                                                                    (Deutschland)</span><span
                                                                                    class="iti__dial-code">+49</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gh"
                                                                                role="option" data-dial-code="233"
                                                                                data-country-code="gh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ghana
                                                                                    (Gaana)</span><span
                                                                                    class="iti__dial-code">+233</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gi"
                                                                                role="option" data-dial-code="350"
                                                                                data-country-code="gi"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gi">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Gibraltar</span><span
                                                                                    class="iti__dial-code">+350</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gr"
                                                                                role="option" data-dial-code="30"
                                                                                data-country-code="gr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Greece
                                                                                    (Ελλάδα)</span><span
                                                                                    class="iti__dial-code">+30</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gl"
                                                                                role="option" data-dial-code="299"
                                                                                data-country-code="gl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Greenland
                                                                                    (Kalaallit Nunaat)</span><span
                                                                                    class="iti__dial-code">+299</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gd"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="gd"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gd">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Grenada</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gp"
                                                                                role="option" data-dial-code="590"
                                                                                data-country-code="gp"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gp">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guadeloupe</span><span
                                                                                    class="iti__dial-code">+590</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gu"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="gu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guam</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gt"
                                                                                role="option" data-dial-code="502"
                                                                                data-country-code="gt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guatemala</span><span
                                                                                    class="iti__dial-code">+502</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gg"
                                                                                role="option" data-dial-code="44"
                                                                                data-country-code="gg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guernsey</span><span
                                                                                    class="iti__dial-code">+44</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gn"
                                                                                role="option" data-dial-code="224"
                                                                                data-country-code="gn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guinea
                                                                                    (Guinée)</span><span
                                                                                    class="iti__dial-code">+224</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gw"
                                                                                role="option" data-dial-code="245"
                                                                                data-country-code="gw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guinea-Bissau
                                                                                    (Guiné Bissau)</span><span
                                                                                    class="iti__dial-code">+245</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gy"
                                                                                role="option" data-dial-code="592"
                                                                                data-country-code="gy"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gy">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Guyana</span><span
                                                                                    class="iti__dial-code">+592</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ht"
                                                                                role="option" data-dial-code="509"
                                                                                data-country-code="ht"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ht">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Haiti</span><span
                                                                                    class="iti__dial-code">+509</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-hn"
                                                                                role="option" data-dial-code="504"
                                                                                data-country-code="hn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__hn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Honduras</span><span
                                                                                    class="iti__dial-code">+504</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-hk"
                                                                                role="option" data-dial-code="852"
                                                                                data-country-code="hk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__hk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Hong Kong
                                                                                    (香港)</span><span
                                                                                    class="iti__dial-code">+852</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-hu"
                                                                                role="option" data-dial-code="36"
                                                                                data-country-code="hu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__hu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Hungary
                                                                                    (Magyarország)</span><span
                                                                                    class="iti__dial-code">+36</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-is"
                                                                                role="option" data-dial-code="354"
                                                                                data-country-code="is"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__is">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Iceland
                                                                                    (Ísland)</span><span
                                                                                    class="iti__dial-code">+354</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-in"
                                                                                role="option" data-dial-code="91"
                                                                                data-country-code="in"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__in">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">India
                                                                                    (भारत)</span><span
                                                                                    class="iti__dial-code">+91</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-id"
                                                                                role="option" data-dial-code="62"
                                                                                data-country-code="id"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__id">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Indonesia</span><span
                                                                                    class="iti__dial-code">+62</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ir"
                                                                                role="option" data-dial-code="98"
                                                                                data-country-code="ir"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ir">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Iran
                                                                                    (&#x202B;ایران&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+98</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-iq"
                                                                                role="option" data-dial-code="964"
                                                                                data-country-code="iq"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__iq">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Iraq
                                                                                    (&#x202B;العراق&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+964</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ie"
                                                                                role="option" data-dial-code="353"
                                                                                data-country-code="ie"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ie">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ireland</span><span
                                                                                    class="iti__dial-code">+353</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-im"
                                                                                role="option" data-dial-code="44"
                                                                                data-country-code="im"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__im">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Isle of
                                                                                    Man</span><span
                                                                                    class="iti__dial-code">+44</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-il"
                                                                                role="option" data-dial-code="972"
                                                                                data-country-code="il"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__il">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Israel
                                                                                    (&#x202B;ישראל&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+972</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-it"
                                                                                role="option" data-dial-code="39"
                                                                                data-country-code="it"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__it">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Italy
                                                                                    (Italia)</span><span
                                                                                    class="iti__dial-code">+39</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-jm"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="jm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__jm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Jamaica</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-jp"
                                                                                role="option" data-dial-code="81"
                                                                                data-country-code="jp"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__jp">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Japan
                                                                                    (日本)</span><span
                                                                                    class="iti__dial-code">+81</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-je"
                                                                                role="option" data-dial-code="44"
                                                                                data-country-code="je"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__je">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Jersey</span><span
                                                                                    class="iti__dial-code">+44</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-jo"
                                                                                role="option" data-dial-code="962"
                                                                                data-country-code="jo"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__jo">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Jordan
                                                                                    (&#x202B;الأردن&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+962</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kz"
                                                                                role="option" data-dial-code="7"
                                                                                data-country-code="kz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kazakhstan
                                                                                    (Казахстан)</span><span
                                                                                    class="iti__dial-code">+7</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ke"
                                                                                role="option" data-dial-code="254"
                                                                                data-country-code="ke"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ke">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kenya</span><span
                                                                                    class="iti__dial-code">+254</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ki"
                                                                                role="option" data-dial-code="686"
                                                                                data-country-code="ki"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ki">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kiribati</span><span
                                                                                    class="iti__dial-code">+686</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-xk"
                                                                                role="option" data-dial-code="383"
                                                                                data-country-code="xk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__xk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kosovo</span><span
                                                                                    class="iti__dial-code">+383</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kw"
                                                                                role="option" data-dial-code="965"
                                                                                data-country-code="kw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kuwait
                                                                                    (&#x202B;الكويت&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+965</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kg"
                                                                                role="option" data-dial-code="996"
                                                                                data-country-code="kg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Kyrgyzstan
                                                                                    (Кыргызстан)</span><span
                                                                                    class="iti__dial-code">+996</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-la"
                                                                                role="option" data-dial-code="856"
                                                                                data-country-code="la"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__la">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Laos
                                                                                    (ລາວ)</span><span
                                                                                    class="iti__dial-code">+856</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lv"
                                                                                role="option" data-dial-code="371"
                                                                                data-country-code="lv"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lv">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Latvia
                                                                                    (Latvija)</span><span
                                                                                    class="iti__dial-code">+371</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lb"
                                                                                role="option" data-dial-code="961"
                                                                                data-country-code="lb"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lb">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Lebanon
                                                                                    (&#x202B;لبنان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+961</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ls"
                                                                                role="option" data-dial-code="266"
                                                                                data-country-code="ls"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ls">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Lesotho</span><span
                                                                                    class="iti__dial-code">+266</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lr"
                                                                                role="option" data-dial-code="231"
                                                                                data-country-code="lr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Liberia</span><span
                                                                                    class="iti__dial-code">+231</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ly"
                                                                                role="option" data-dial-code="218"
                                                                                data-country-code="ly"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ly">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Libya
                                                                                    (&#x202B;ليبيا&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+218</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-li"
                                                                                role="option" data-dial-code="423"
                                                                                data-country-code="li"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__li">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Liechtenstein</span><span
                                                                                    class="iti__dial-code">+423</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lt"
                                                                                role="option" data-dial-code="370"
                                                                                data-country-code="lt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Lithuania
                                                                                    (Lietuva)</span><span
                                                                                    class="iti__dial-code">+370</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lu"
                                                                                role="option" data-dial-code="352"
                                                                                data-country-code="lu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Luxembourg</span><span
                                                                                    class="iti__dial-code">+352</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mo"
                                                                                role="option" data-dial-code="853"
                                                                                data-country-code="mo"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mo">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Macau
                                                                                    (澳門)</span><span
                                                                                    class="iti__dial-code">+853</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mk"
                                                                                role="option" data-dial-code="389"
                                                                                data-country-code="mk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Macedonia
                                                                                    (FYROM) (Македонија)</span><span
                                                                                    class="iti__dial-code">+389</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mg"
                                                                                role="option" data-dial-code="261"
                                                                                data-country-code="mg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Madagascar
                                                                                    (Madagasikara)</span><span
                                                                                    class="iti__dial-code">+261</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mw"
                                                                                role="option" data-dial-code="265"
                                                                                data-country-code="mw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Malawi</span><span
                                                                                    class="iti__dial-code">+265</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-my"
                                                                                role="option" data-dial-code="60"
                                                                                data-country-code="my"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__my">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Malaysia</span><span
                                                                                    class="iti__dial-code">+60</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mv"
                                                                                role="option" data-dial-code="960"
                                                                                data-country-code="mv"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mv">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Maldives</span><span
                                                                                    class="iti__dial-code">+960</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ml"
                                                                                role="option" data-dial-code="223"
                                                                                data-country-code="ml"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ml">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mali</span><span
                                                                                    class="iti__dial-code">+223</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mt"
                                                                                role="option" data-dial-code="356"
                                                                                data-country-code="mt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Malta</span><span
                                                                                    class="iti__dial-code">+356</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mh"
                                                                                role="option" data-dial-code="692"
                                                                                data-country-code="mh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Marshall
                                                                                    Islands</span><span
                                                                                    class="iti__dial-code">+692</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mq"
                                                                                role="option" data-dial-code="596"
                                                                                data-country-code="mq"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mq">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Martinique</span><span
                                                                                    class="iti__dial-code">+596</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mr"
                                                                                role="option" data-dial-code="222"
                                                                                data-country-code="mr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mauritania
                                                                                    (&#x202B;موريتانيا&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+222</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mu"
                                                                                role="option" data-dial-code="230"
                                                                                data-country-code="mu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mauritius
                                                                                    (Moris)</span><span
                                                                                    class="iti__dial-code">+230</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-yt"
                                                                                role="option" data-dial-code="262"
                                                                                data-country-code="yt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__yt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mayotte</span><span
                                                                                    class="iti__dial-code">+262</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mx"
                                                                                role="option" data-dial-code="52"
                                                                                data-country-code="mx"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mx">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mexico
                                                                                    (México)</span><span
                                                                                    class="iti__dial-code">+52</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-fm"
                                                                                role="option" data-dial-code="691"
                                                                                data-country-code="fm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__fm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Micronesia</span><span
                                                                                    class="iti__dial-code">+691</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-md"
                                                                                role="option" data-dial-code="373"
                                                                                data-country-code="md"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__md">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Moldova
                                                                                    (Republica Moldova)</span><span
                                                                                    class="iti__dial-code">+373</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mc"
                                                                                role="option" data-dial-code="377"
                                                                                data-country-code="mc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Monaco</span><span
                                                                                    class="iti__dial-code">+377</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mn"
                                                                                role="option" data-dial-code="976"
                                                                                data-country-code="mn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mongolia
                                                                                    (Монгол)</span><span
                                                                                    class="iti__dial-code">+976</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-me"
                                                                                role="option" data-dial-code="382"
                                                                                data-country-code="me"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__me">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Montenegro
                                                                                    (Crna Gora)</span><span
                                                                                    class="iti__dial-code">+382</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ms"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="ms"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ms">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Montserrat</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ma"
                                                                                role="option" data-dial-code="212"
                                                                                data-country-code="ma"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ma">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Morocco
                                                                                    (&#x202B;المغرب&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+212</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mz"
                                                                                role="option" data-dial-code="258"
                                                                                data-country-code="mz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Mozambique
                                                                                    (Moçambique)</span><span
                                                                                    class="iti__dial-code">+258</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mm"
                                                                                role="option" data-dial-code="95"
                                                                                data-country-code="mm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Myanmar
                                                                                    (Burma) (မြန်မာ)</span><span
                                                                                    class="iti__dial-code">+95</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-na"
                                                                                role="option" data-dial-code="264"
                                                                                data-country-code="na"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__na">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Namibia
                                                                                    (Namibië)</span><span
                                                                                    class="iti__dial-code">+264</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nr"
                                                                                role="option" data-dial-code="674"
                                                                                data-country-code="nr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Nauru</span><span
                                                                                    class="iti__dial-code">+674</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-np"
                                                                                role="option" data-dial-code="977"
                                                                                data-country-code="np"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__np">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Nepal
                                                                                    (नेपाल)</span><span
                                                                                    class="iti__dial-code">+977</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nl"
                                                                                role="option" data-dial-code="31"
                                                                                data-country-code="nl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Netherlands
                                                                                    (Nederland)</span><span
                                                                                    class="iti__dial-code">+31</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nc"
                                                                                role="option" data-dial-code="687"
                                                                                data-country-code="nc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">New
                                                                                    Caledonia
                                                                                    (Nouvelle-Calédonie)</span><span
                                                                                    class="iti__dial-code">+687</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nz"
                                                                                role="option" data-dial-code="64"
                                                                                data-country-code="nz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">New
                                                                                    Zealand</span><span
                                                                                    class="iti__dial-code">+64</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ni"
                                                                                role="option" data-dial-code="505"
                                                                                data-country-code="ni"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ni">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Nicaragua</span><span
                                                                                    class="iti__dial-code">+505</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ne"
                                                                                role="option" data-dial-code="227"
                                                                                data-country-code="ne"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ne">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Niger
                                                                                    (Nijar)</span><span
                                                                                    class="iti__dial-code">+227</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ng"
                                                                                role="option" data-dial-code="234"
                                                                                data-country-code="ng"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ng">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Nigeria</span><span
                                                                                    class="iti__dial-code">+234</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nu"
                                                                                role="option" data-dial-code="683"
                                                                                data-country-code="nu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Niue</span><span
                                                                                    class="iti__dial-code">+683</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-nf"
                                                                                role="option" data-dial-code="672"
                                                                                data-country-code="nf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__nf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Norfolk
                                                                                    Island</span><span
                                                                                    class="iti__dial-code">+672</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kp"
                                                                                role="option" data-dial-code="850"
                                                                                data-country-code="kp"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kp">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">North
                                                                                    Korea (조선 민주주의 인민 공화국)</span><span
                                                                                    class="iti__dial-code">+850</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mp"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="mp"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mp">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Northern
                                                                                    Mariana Islands</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-no"
                                                                                role="option" data-dial-code="47"
                                                                                data-country-code="no"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__no">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Norway
                                                                                    (Norge)</span><span
                                                                                    class="iti__dial-code">+47</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-om"
                                                                                role="option" data-dial-code="968"
                                                                                data-country-code="om"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__om">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Oman
                                                                                    (&#x202B;عُمان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+968</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pk"
                                                                                role="option" data-dial-code="92"
                                                                                data-country-code="pk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Pakistan
                                                                                    (&#x202B;پاکستان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+92</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pw"
                                                                                role="option" data-dial-code="680"
                                                                                data-country-code="pw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Palau</span><span
                                                                                    class="iti__dial-code">+680</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ps"
                                                                                role="option" data-dial-code="970"
                                                                                data-country-code="ps"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ps">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Palestine
                                                                                    (&#x202B;فلسطين&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+970</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pa"
                                                                                role="option" data-dial-code="507"
                                                                                data-country-code="pa"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pa">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Panama
                                                                                    (Panamá)</span><span
                                                                                    class="iti__dial-code">+507</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pg"
                                                                                role="option" data-dial-code="675"
                                                                                data-country-code="pg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Papua New
                                                                                    Guinea</span><span
                                                                                    class="iti__dial-code">+675</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-py"
                                                                                role="option" data-dial-code="595"
                                                                                data-country-code="py"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__py">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Paraguay</span><span
                                                                                    class="iti__dial-code">+595</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pe"
                                                                                role="option" data-dial-code="51"
                                                                                data-country-code="pe"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pe">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Peru
                                                                                    (Perú)</span><span
                                                                                    class="iti__dial-code">+51</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ph"
                                                                                role="option" data-dial-code="63"
                                                                                data-country-code="ph"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ph">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Philippines</span><span
                                                                                    class="iti__dial-code">+63</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pl"
                                                                                role="option" data-dial-code="48"
                                                                                data-country-code="pl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Poland
                                                                                    (Polska)</span><span
                                                                                    class="iti__dial-code">+48</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pt"
                                                                                role="option" data-dial-code="351"
                                                                                data-country-code="pt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Portugal</span><span
                                                                                    class="iti__dial-code">+351</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pr"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="pr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Puerto
                                                                                    Rico</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-qa"
                                                                                role="option" data-dial-code="974"
                                                                                data-country-code="qa"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__qa">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Qatar
                                                                                    (&#x202B;قطر&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+974</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-re"
                                                                                role="option" data-dial-code="262"
                                                                                data-country-code="re"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__re">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Réunion
                                                                                    (La Réunion)</span><span
                                                                                    class="iti__dial-code">+262</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ro"
                                                                                role="option" data-dial-code="40"
                                                                                data-country-code="ro"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ro">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Romania
                                                                                    (România)</span><span
                                                                                    class="iti__dial-code">+40</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ru"
                                                                                role="option" data-dial-code="7"
                                                                                data-country-code="ru"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ru">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Russia
                                                                                    (Россия)</span><span
                                                                                    class="iti__dial-code">+7</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-rw"
                                                                                role="option" data-dial-code="250"
                                                                                data-country-code="rw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__rw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Rwanda</span><span
                                                                                    class="iti__dial-code">+250</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-bl"
                                                                                role="option" data-dial-code="590"
                                                                                data-country-code="bl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__bl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Barthélemy</span><span
                                                                                    class="iti__dial-code">+590</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sh"
                                                                                role="option" data-dial-code="290"
                                                                                data-country-code="sh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Helena</span><span
                                                                                    class="iti__dial-code">+290</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kn"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="kn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Kitts and Nevis</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lc"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="lc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Lucia</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-mf"
                                                                                role="option" data-dial-code="590"
                                                                                data-country-code="mf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__mf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Martin (Saint-Martin (partie
                                                                                    française))</span><span
                                                                                    class="iti__dial-code">+590</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-pm"
                                                                                role="option" data-dial-code="508"
                                                                                data-country-code="pm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__pm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Pierre and Miquelon </span><span
                                                                                    class="iti__dial-code">+50
                                                                                    8</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-vc"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="vc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__vc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saint
                                                                                    Vincent and the
                                                                                    Grenadines</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ws"
                                                                                role="option" data-dial-code="685"
                                                                                data-country-code="ws"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ws">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Samoa</span><span
                                                                                    class="iti__dial-code">+685</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sm"
                                                                                role="option" data-dial-code="378"
                                                                                data-country-code="sm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">San
                                                                                    Marino</span><span
                                                                                    class="iti__dial-code">+378</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-st"
                                                                                role="option" data-dial-code="239"
                                                                                data-country-code="st"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__st">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">São Tomé
                                                                                    and Príncipe (São Tomé e
                                                                                    Príncipe)</span><span
                                                                                    class="iti__dial-code">+239</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sa"
                                                                                role="option" data-dial-code="966"
                                                                                data-country-code="sa"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sa">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Saudi
                                                                                    Arabia (&#x202B;المملكة العربية
                                                                                    السعودية&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+966</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sn"
                                                                                role="option" data-dial-code="221"
                                                                                data-country-code="sn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Senegal
                                                                                    (Sénégal)</span><span
                                                                                    class="iti__dial-code">+221</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-rs"
                                                                                role="option" data-dial-code="381"
                                                                                data-country-code="rs"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__rs">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Serbia
                                                                                    (Србија)</span><span
                                                                                    class="iti__dial-code">+381</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sc"
                                                                                role="option" data-dial-code="248"
                                                                                data-country-code="sc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Seychelles</span><span
                                                                                    class="iti__dial-code">+248</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sl"
                                                                                role="option" data-dial-code="232"
                                                                                data-country-code="sl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Sierra
                                                                                    Leone</span><span
                                                                                    class="iti__dial-code">+232</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sg"
                                                                                role="option" data-dial-code="65"
                                                                                data-country-code="sg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Singapore</span><span
                                                                                    class="iti__dial-code">+65</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sx"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="sx"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sx">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Sint
                                                                                    Maarten</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sk"
                                                                                role="option" data-dial-code="421"
                                                                                data-country-code="sk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Slovakia
                                                                                    (Slovensko)</span><span
                                                                                    class="iti__dial-code">+421</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-si"
                                                                                role="option" data-dial-code="386"
                                                                                data-country-code="si"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__si">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Slovenia
                                                                                    (Slovenija)</span><span
                                                                                    class="iti__dial-code">+386</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sb"
                                                                                role="option" data-dial-code="677"
                                                                                data-country-code="sb"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sb">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Solomon
                                                                                    Islands</span><span
                                                                                    class="iti__dial-code">+677</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-so"
                                                                                role="option" data-dial-code="252"
                                                                                data-country-code="so"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__so">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Somalia
                                                                                    (Soomaaliya)</span><span
                                                                                    class="iti__dial-code">+252</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-za"
                                                                                role="option" data-dial-code="27"
                                                                                data-country-code="za"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__za">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">South
                                                                                    Africa</span><span
                                                                                    class="iti__dial-code">+27</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-kr"
                                                                                role="option" data-dial-code="82"
                                                                                data-country-code="kr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__kr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">South
                                                                                    Korea (대한민국)</span><span
                                                                                    class="iti__dial-code">+82</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ss"
                                                                                role="option" data-dial-code="211"
                                                                                data-country-code="ss"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ss">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">South
                                                                                    Sudan (&#x202B;جنوب
                                                                                    السودان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+211</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-es"
                                                                                role="option" data-dial-code="34"
                                                                                data-country-code="es"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__es">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Spain
                                                                                    (España)</span><span
                                                                                    class="iti__dial-code">+34</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-lk"
                                                                                role="option" data-dial-code="94"
                                                                                data-country-code="lk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__lk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Sri Lanka
                                                                                    (ශ්&zwj;රී ලංකාව)</span><span
                                                                                    class="iti__dial-code">+94</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sd"
                                                                                role="option" data-dial-code="249"
                                                                                data-country-code="sd"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sd">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Sudan
                                                                                    (&#x202B;السودان&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+249</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sr"
                                                                                role="option" data-dial-code="597"
                                                                                data-country-code="sr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Suriname</span><span
                                                                                    class="iti__dial-code">+597</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sj"
                                                                                role="option" data-dial-code="47"
                                                                                data-country-code="sj"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sj">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Svalbard
                                                                                    and Jan Mayen</span><span
                                                                                    class="iti__dial-code">+47</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-se"
                                                                                role="option" data-dial-code="46"
                                                                                data-country-code="se"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__se">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Sweden
                                                                                    (Sverige)</span><span
                                                                                    class="iti__dial-code">+46</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ch"
                                                                                role="option" data-dial-code="41"
                                                                                data-country-code="ch"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ch">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Switzerland
                                                                                    (Schweiz)</span><span
                                                                                    class="iti__dial-code">+41</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-sy"
                                                                                role="option" data-dial-code="963"
                                                                                data-country-code="sy"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__sy">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Syria
                                                                                    (&#x202B;سوريا&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+963</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tw"
                                                                                role="option" data-dial-code="886"
                                                                                data-country-code="tw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Taiwan
                                                                                    (台灣)</span><span
                                                                                    class="iti__dial-code">+886</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tj"
                                                                                role="option" data-dial-code="992"
                                                                                data-country-code="tj"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tj">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tajikistan</span><span
                                                                                    class="iti__dial-code">+992</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tz"
                                                                                role="option" data-dial-code="255"
                                                                                data-country-code="tz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tanzania</span><span
                                                                                    class="iti__dial-code">+255</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-th"
                                                                                role="option" data-dial-code="66"
                                                                                data-country-code="th"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__th">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Thailand
                                                                                    (ไทย)</span><span
                                                                                    class="iti__dial-code">+66</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tl"
                                                                                role="option" data-dial-code="670"
                                                                                data-country-code="tl"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tl">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Timor-Leste</span><span
                                                                                    class="iti__dial-code">+670</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tg"
                                                                                role="option" data-dial-code="228"
                                                                                data-country-code="tg"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tg">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Togo</span><span
                                                                                    class="iti__dial-code">+228</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tk"
                                                                                role="option" data-dial-code="690"
                                                                                data-country-code="tk"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tk">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tokelau</span><span
                                                                                    class="iti__dial-code">+690</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-to"
                                                                                role="option" data-dial-code="676"
                                                                                data-country-code="to"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__to">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tonga</span><span
                                                                                    class="iti__dial-code">+676</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tt"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="tt"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tt">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Trinidad
                                                                                    and Tobago</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tn"
                                                                                role="option" data-dial-code="216"
                                                                                data-country-code="tn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tunisia
                                                                                    (&#x202B;تونس&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+216</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tr"
                                                                                role="option" data-dial-code="90"
                                                                                data-country-code="tr"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tr">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Turkey
                                                                                    (Türkiye)</span><span
                                                                                    class="iti__dial-code">+90</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tm"
                                                                                role="option" data-dial-code="993"
                                                                                data-country-code="tm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Turkmenistan</span><span
                                                                                    class="iti__dial-code">+993</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tc"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="tc"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tc">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Turks and
                                                                                    Caicos Islands</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-tv"
                                                                                role="option" data-dial-code="688"
                                                                                data-country-code="tv"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__tv">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Tuvalu</span><span
                                                                                    class="iti__dial-code">+688</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-vi"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="vi"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__vi">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">U.S.
                                                                                    Virgin Islands</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ug"
                                                                                role="option" data-dial-code="256"
                                                                                data-country-code="ug"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ug">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Uganda</span><span
                                                                                    class="iti__dial-code">+256</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ua"
                                                                                role="option" data-dial-code="380"
                                                                                data-country-code="ua"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ua">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Ukraine
                                                                                    (Україна)</span><span
                                                                                    class="iti__dial-code">+380</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ae"
                                                                                role="option" data-dial-code="971"
                                                                                data-country-code="ae"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ae">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">United
                                                                                    Arab Emirates (&#x202B;الإمارات
                                                                                    العربية
                                                                                    المتحدة&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+971</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-gb"
                                                                                role="option" data-dial-code="44"
                                                                                data-country-code="gb"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__gb">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">United
                                                                                    Kingdom</span><span
                                                                                    class="iti__dial-code">+44</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-us"
                                                                                role="option" data-dial-code="1"
                                                                                data-country-code="us"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__us">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">United
                                                                                    States</span><span
                                                                                    class="iti__dial-code">+1</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-uy"
                                                                                role="option" data-dial-code="598"
                                                                                data-country-code="uy"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__uy">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Uruguay</span><span
                                                                                    class="iti__dial-code">+598</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-uz"
                                                                                role="option" data-dial-code="998"
                                                                                data-country-code="uz"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__uz">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Uzbekistan
                                                                                    (Oʻzbekiston)</span><span
                                                                                    class="iti__dial-code">+998</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-vu"
                                                                                role="option" data-dial-code="678"
                                                                                data-country-code="vu"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__vu">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Vanuatu</span><span
                                                                                    class="iti__dial-code">+678</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-va"
                                                                                role="option" data-dial-code="39"
                                                                                data-country-code="va"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__va">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Vatican
                                                                                    City (Città del
                                                                                    Vaticano)</span><span
                                                                                    class="iti__dial-code">+39</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ve"
                                                                                role="option" data-dial-code="58"
                                                                                data-country-code="ve"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ve">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Venezuela</span><span
                                                                                    class="iti__dial-code">+58</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-vn"
                                                                                role="option" data-dial-code="84"
                                                                                data-country-code="vn"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__vn">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Vietnam
                                                                                    (Việt Nam)</span><span
                                                                                    class="iti__dial-code">+84</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-wf"
                                                                                role="option" data-dial-code="681"
                                                                                data-country-code="wf"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__wf">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Wallis and
                                                                                    Futuna
                                                                                    (Wallis-et-Futuna)</span><span
                                                                                    class="iti__dial-code">+681</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-eh"
                                                                                role="option" data-dial-code="212"
                                                                                data-country-code="eh"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__eh">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Western
                                                                                    Sahara (&#x202B;الصحراء
                                                                                    الغربية&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+212</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ye"
                                                                                role="option" data-dial-code="967"
                                                                                data-country-code="ye"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ye">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Yemen
                                                                                    (&#x202B;اليمن&#x202C;&lrm;)</span><span
                                                                                    class="iti__dial-code">+967</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-zm"
                                                                                role="option" data-dial-code="260"
                                                                                data-country-code="zm"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__zm">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Zambia</span><span
                                                                                    class="iti__dial-code">+260</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-zw"
                                                                                role="option" data-dial-code="263"
                                                                                data-country-code="zw"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__zw">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Zimbabwe</span><span
                                                                                    class="iti__dial-code">+263</span>
                                                                            </li>
                                                                            <li class="iti__country iti__standard"
                                                                                tabindex="-1" id="iti-0__item-ax"
                                                                                role="option" data-dial-code="358"
                                                                                data-country-code="ax"
                                                                                aria-selected="false">
                                                                                <div class="iti__flag-box">
                                                                                    <div class="iti__flag iti__ax">
                                                                                    </div>
                                                                                </div><span
                                                                                    class="iti__country-name">Åland
                                                                                    Islands</span><span
                                                                                    class="iti__dial-code">+358</span>
                                                                            </li>
                                                                        </ul>
                                                                    </div><input type="text" class="form-control phone"
                                                                        name="consignee_phone_number" value="{{ old('consignee_phone_number') }}" autocomplete="off"
                                                                        data-intl-tel-input-id="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <label class="form-label">Email <span
                                                                            class="text-danger">*</span></label>
                                                                    <div class="form-check form-switch mb-1">
                                                                        <label
                                                                            class="form-check-label d-flex align-items-center gap-2">
                                                                            <span>Email Opt Out</span>
                                                                            <input
                                                                                class="form-check-input form-check-input-sm switchCheckDefault ms-auto"
                                                                                type="checkbox" role="switch"
                                                                                checked="">
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <input type="email" class="form-control"
                                                                    name="consignee_email" value="{{ old('consignee_email') }}" placeholder="Email Address">
                                                            </div>
                                                        </div>
                                                        <div class="mt-4 d-flex align-items-center">
                                                            <button type="button" class="btn btn-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#create_success">Save Consignee</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Address Info -->
                                        <style>
                                        .rowContaineraddmore {
                                            position: relative;
                                        }

                                        .delete-row {
                                            position: absolute;
                                            top: -8px;
                                            right: -8px;
                                            background: red;
                                            color: #fff;
                                            border: none;
                                            border-radius: 50%;
                                            width: 26px;
                                            height: 26px;
                                            cursor: pointer;
                                            font-size: 16px;
                                            line-height: 26px;
                                            text-align: center;
                                        }
                                        </style>



                                        <!-- Package Dimension -->
                                        <div class="accordion-item border-top rounded mb-3">
                                            <div class="accordion-header">
                                                <a href="#" class="accordion-button accordion-custom-button rounded"
                                                    data-bs-toggle="collapse" data-bs-target="#social">
                                                    <span class="avatar avatar-md rounded me-1">3</span>
                                                    Package Dimension
                                                </a>
                                            </div>
                                            <div class="accordion-collapse collapse" id="social"
                                                data-bs-parent="#main_accordion">
                                                <div class="accordion-body border-top">
                                                    <div class="row rowContaineraddmore">
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Actual Weight (kg)</label>
                                                                <input type="number" class="form-control"
                                                                    name="packages[0][actual_weight_kg]" value="{{ old('packages.0.actual_weight_kg') }}"
                                                                    placeholder="Actual Weight (kg)">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Length (cm) </label>
                                                                <input type="number" class="form-control"
                                                                    name="packages[0][length_cm]" value="{{ old('packages.0.length_cm') }}" placeholder="Length (cm)">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Width (cm) </label>
                                                                <input type="number" class="form-control"
                                                                    name="packages[0][width_cm]" value="{{ old('packages.0.width_cm') }}" placeholder="Width (cm)">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Height (cm)</label>
                                                                <input type="number" class="form-control"
                                                                    name="packages[0][height_cm]" value="{{ old('packages.0.height_cm') }}" placeholder="Height (cm)">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3 mb-md-0">
                                                                <label class="form-label">Volumetric Weight
                                                                    (L*B*H/5000=VOL
                                                                    WT)</label>
                                                                <input type="text" class="form-control"
                                                                    name="packages[0][volumetric_weight]" value="{{ old('packages.0.volumetric_weight') }}"
                                                                    placeholder="volumetric weight">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-primary mt-3" id="addRowBtn">+
                                                        Add
                                                        More</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Package Dimension -->
                                        
                                        <script>
                                        function reindexPackageRows() {
                                            document.querySelectorAll('.rowContaineraddmore').forEach(function(row, index) {
                                                const fields = [
                                                    'actual_weight_kg',
                                                    'length_cm',
                                                    'width_cm',
                                                    'height_cm',
                                                    'volumetric_weight'
                                                ];

                                                fields.forEach(function(field) {
                                                    const input = row.querySelector('[name$="[' + field + ']"]');
                                                    if (input) {
                                                        input.name = 'packages[' + index + '][' + field + ']';
                                                    }
                                                });
                                            });
                                        }

                                        document.getElementById('addRowBtn').addEventListener('click', function() {
                                            let lastRow = document.querySelector(
                                                '.rowContaineraddmore:last-of-type');
                                            // Clone row
                                            let newRow = lastRow.cloneNode(true);
                                            // Clear inputs
                                            newRow.querySelectorAll('input').forEach(input => input.value = '');
                                            // Remove any existing delete button (safety)
                                            let oldDelete = newRow.querySelector('.delete-row');
                                            if (oldDelete) oldDelete.remove();
                                            // Create delete button ONLY for new rows
                                            let deleteBtn = document.createElement('button');
                                            deleteBtn.innerHTML = '×';
                                            deleteBtn.type = 'button';
                                            deleteBtn.className = 'delete-row';
                                            deleteBtn.onclick = function() {
                                                newRow.remove();
                                                reindexPackageRows();
                                            };
                                            newRow.appendChild(deleteBtn);
                                            // Insert above button
                                            let btn = document.getElementById('addRowBtn');
                                            btn.parentNode.insertBefore(newRow, btn);
                                            reindexPackageRows();
                                        });
                                        </script>
                                        <!-- CSB INFO -->
                                        <div class="accordion-item border-top rounded mb-3" id="csbInfoSection"
                                            style="display: none;">
                                            <div class="accordion-header">
                                                <a href="#" class="accordion-button accordion-custom-button rounded"
                                                    data-bs-toggle="collapse" data-bs-target="#csbinfo">
                                                    <span class="avatar avatar-md rounded me-1"
                                                        id="csbInfoNumber">4</span>
                                                    CSB Information
                                                </a>
                                            </div>
                                            <div class="accordion-collapse collapse" id="csbinfo"
                                                data-bs-parent="#main_accordion">
                                                <div class="accordion-body border-top">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-4">
                                                                <label class="form-label">Ecommerce</label>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <div class="form-check">
                                                                        <input type="radio" id="ecommyes"
                                                                            name="ecommerce" value="Yes" {{ old('ecommerce') == 'Yes' ? 'checked' : '' }}
                                                                            class="form-check-input">
                                                                        <label class="form-check-label"
                                                                            for="ecommyes">Yes</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="ecommno"
                                                                            name="ecommerce" value="No" {{ old('ecommerce') == 'No' ? 'checked' : '' }}
                                                                            class="form-check-input">
                                                                        <label class="form-check-label"
                                                                            for="ecommno">No</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-4">
                                                                <label class="form-label">Scheme</label>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <div class="form-check">
                                                                        <input type="radio" id="schemeyes" name="scheme"
                                                                            value="Yes" {{ old('scheme') == 'Yes' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label"
                                                                            for="schemeyes">Yes</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="schemeno" name="scheme"
                                                                            value="No" {{ old('scheme') == 'No' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label"
                                                                            for="schemeno">No</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-4">
                                                                <label class="form-label">Bond UT/IGST</label>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <div class="form-check">
                                                                        <input type="radio" id="bondut"
                                                                            name="bond_ut_igst" value="Bond UT" {{ old('bond_ut_igst') == 'Bond UT' ? 'checked' : '' }}
                                                                            class="form-check-input">
                                                                        <label class="form-check-label"
                                                                            for="bondut">Bond
                                                                            UT</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="igst"
                                                                            name="bond_ut_igst" value="IGST" {{ old('bond_ut_igst') == 'IGST' ? 'checked' : '' }}
                                                                            class="form-check-input">
                                                                        <label class="form-check-label"
                                                                            for="igst">IGST</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">LUT Number</label>
                                                                <input type="text" class="form-control"
                                                                    name="lut_number" value="{{ old('lut_number') }}" placeholder="LUT Number">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">IEC Code </label>
                                                                <input type="text" class="form-control" name="iec_code" value="{{ old('iec_code') }}"
                                                                    placeholder="IEC Code">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">GST Number </label>
                                                                <input type="text" class="form-control"
                                                                    name="gst_number" value="{{ old('gst_number') }}" placeholder="GST Number">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">AD Code</label>
                                                                <input type="text" class="form-control" name="ad_code" value="{{ old('ad_code') }}"
                                                                    placeholder="AD Code">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3 mb-md-0">
                                                                <label class="form-label">Bank Account Number</label>
                                                                <input type="text" class="form-control"
                                                                    name="bank_account_number" value="{{ old('bank_account_number') }}"
                                                                    placeholder="Bank Account Number">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-0">
                                                                <label class="form-label">Bank IFSC Code</label>
                                                                <input type="text" class="form-control"
                                                                    name="bank_ifsc_code" value="{{ old('bank_ifsc_code') }}" placeholder="Bank IFSC Code">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /CSB INFO -->
                                        <!-- Shipment Invoice -->
                                        <div class="accordion-item border-top rounded mb-3">
                                            <div class="accordion-header">
                                                <a href="#" class="accordion-button accordion-custom-button rounded"
                                                    data-bs-toggle="collapse" data-bs-target="#access-info">
                                                    <span class="avatar avatar-md rounded me-1"
                                                        id="shipmentInvoiceNumber">5</span>
                                                    Shipment Invoice
                                                </a>
                                            </div>
                                            <div class="accordion-collapse collapse" id="access-info"
                                                data-bs-parent="#main_accordion">
                                                <div class="accordion-body border-top">
                                                    <div class="row">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Invoice Number</label>
                                                                    <input type="text" class="form-control"
                                                                        name="invoice_number" value="{{ old('invoice_number') }}"
                                                                        placeholder="Invoice Number">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Invoice Date</label>
                                                                    <input type="date" class="form-control"
                                                                        name="invoice_date" value="{{ old('invoice_date') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Invoice Amount </label>
                                                                    <input type="number" class="form-control"
                                                                        name="invoice_amount" value="{{ old('invoice_amount') }}"
                                                                        placeholder="Invoice Amount">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Incoterms<span
                                                                            class="text-danger ms-1">*</span></label>
                                                                    <select class="select2" data-toggle="select2"
                                                                        name="incoterms">
                                                                        <option value="">Select</option>
                                                                        <option value="DDU" {{ old('incoterms') == 'DDU' ? 'selected' : '' }}>DDU</option>
                                                                        <option value="DDP" {{ old('incoterms') == 'DDP' ? 'selected' : '' }}>DDP</option>
                                                                        <option value="Silver" {{ old('incoterms') == 'Silver' ? 'selected' : '' }}>Silver</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Invoice Currency<span class="text-danger ms-1">*</span></label>
                                                                    <select class="select2" data-toggle="select2"
                                                                        name="invoice_currency">
                                                                        <!-- <option value="">Select</option> -->
                                                                        <option value="INR" {{ old('invoice_currency') == 'INR' ? 'selected' : '' }}>INR</option>
                                                                        <!-- <option value="USD" {{ old('invoice_currency') == 'USD' ? 'selected' : '' }}>USD Dollar</option>
                                                                        <option value="GBP" {{ old('invoice_currency') == 'GBP' ? 'selected' : '' }}>Pound</option>
                                                                        <option value="Dollar" {{ old('invoice_currency') == 'Dollar' ? 'selected' : '' }}>Dollar</option> -->
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Reference Number </label>
                                                                    <input type="text" class="form-control"
                                                                        name="reference_number" value="{{ old('reference_number') }}"
                                                                        placeholder="Reference Number">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <h5 class="mb-3">Shipment Invoice Items</h5>
                                                        <div style="overflow-x:auto;">
                                                            <table
                                                                class="table table-bordered align-middle text-center">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Box No.</th>
                                                                        <th>Description</th>
                                                                        <th>HS Code</th>
                                                                        <th>Unit Type</th>
                                                                        <th>QTY</th>
                                                                        <th>Unit Rates</th>
                                                                        <th>Amount</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="invoiceTable">
                                                                    <!-- ROW -->
                                                                    <tr>
                                                                        <td><input type="text"
                                                                                class="form-control boxNo"
                                                                                name="items[0][box_no]" value="{{ old('items.0.box_no', '1') }}">
                                                                        </td>
                                                                        <td><label class="form-label">Description <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                name="items[0][description]" value="{{ old('items.0.description') }}"
                                                                                placeholder="Description">
                                                                        </td>
                                                                        <td><input type="text" class="form-control"
                                                                                name="items[0][hs_code]" value="{{ old('items.0.hs_code') }}"
                                                                                placeholder="HS Code"></td>
                                                                        <td>
                                                                            <select class="form-control"
                                                                                name="items[0][unit_type]">
                                                                                <option value="">Select</option>
                                                                                <option value="PCS" {{ old('items.0.unit_type') == 'PCS' ? 'selected' : '' }}>PCS</option>
                                                                                <option value="KG" {{ old('items.0.unit_type') == 'KG' ? 'selected' : '' }}>KG</option>
                                                                                <option value="NOS" {{ old('items.0.unit_type') == 'NOS' ? 'selected' : '' }}>NOS</option>
                                                                                <option value="Bottle" {{ old('items.0.unit_type') == 'Bottle' ? 'selected' : '' }}>Bottle</option>
                                                                                <option value="Pair" {{ old('items.0.unit_type') == 'Pair' ? 'selected' : '' }}>Pair</option>
                                                                                <option value="Strip" {{ old('items.0.unit_type') == 'Strip' ? 'selected' : '' }}>Strip</option>
                                                                                <option value="Dozen" {{ old('items.0.unit_type') == 'Dozen' ? 'selected' : '' }}>Dozen</option>
                                                                                <option value="Gross" {{ old('items.0.unit_type') == 'Gross' ? 'selected' : '' }}>Gross</option>
                                                                                <option value="Sets" {{ old('items.0.unit_type') == 'Sets' ? 'selected' : '' }}>Sets</option>
                                                                                <option value="Box" {{ old('items.0.unit_type') == 'Box' ? 'selected' : '' }}>Box</option>
                                                                                <option value="Container" {{ old('items.0.unit_type') == 'Container' ? 'selected' : '' }}>Container
                                                                                </option>
                                                                                <option value="Carats" {{ old('items.0.unit_type') == 'Carats' ? 'selected' : '' }}>Carats</option>
                                                                                <option value="Pairs" {{ old('items.0.unit_type') == 'Pairs' ? 'selected' : '' }}>Pairs</option>
                                                                            </select>
                                                                        </td>
                                                                        <td><input type="number"
                                                                                class="form-control qty"
                                                                                name="items[0][qty]" value="{{ old('items.0.qty') }}"
                                                                                placeholder="Quantity">
                                                                        </td>
                                                                        <td><input type="number"
                                                                                class="form-control rate"
                                                                                name="items[0][unit_rate]" value="{{ old('items.0.unit_rate') }}"
                                                                                placeholder="Unit Rate">
                                                                        </td>
                                                                        <td><input type="text"
                                                                                class="form-control amount" readonly
                                                                                placeholder="Amount">
                                                                        </td>
                                                                        <td></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="add-more mb-3" id="tableaddRowBtn">➕ Add another
                                                        </div>
                                                        <div class="mt-3 d-flex align-items-end gap-3">
                                                            <div>
                                                                <label>Total Amount</label>
                                                                <input type="text" id="totalAmount"
                                                                    class="form-control w-100" readonly>
                                                            </div>
                                                            <button type="button" class="btn btn-primary" id="rateCalculateBtn">
                                                                <i class="ti ti-calculator me-1"></i> Rate Calculate
                                                            </button>
                                                        </div>
                                                        <style>
                                                        .table input,
                                                        .table select {
                                                            min-width: 80px;
                                                        }

                                                        .remove-btn {
                                                            background: red;
                                                            color: #fff;
                                                            border: none;
                                                            border-radius: 50%;
                                                            width: 26px;
                                                            height: 26px;
                                                            font-size: 14px;
                                                            cursor: pointer;
                                                        }

                                                        .add-more {
                                                            color: red;
                                                            cursor: pointer;
                                                            font-weight: 500;
                                                        }

                                                        .add-more:hover {
                                                            text-decoration: underline;
                                                        }
                                                        </style>
                                                        <script>
                                                        // ADD ROW
                                                        document.getElementById('tableaddRowBtn').addEventListener(
                                                            'click',
                                                            function() {
                                                                let table = document.getElementById('invoiceTable');
                                                                let rows = table.querySelectorAll('tr');
                                                                let lastRow = rows[rows.length - 1];
                                                                let newRow = lastRow.cloneNode(true);
                                                                let newIndex = rows.length;
                                                                // clear inputs and select
                                                                newRow.querySelectorAll('input').forEach(input =>
                                                                    input
                                                                    .value = '');
                                                                newRow.querySelector('select').value = '';
                                                                // update input names with new index
                                                                let inputs = newRow.querySelectorAll('input');
                                                                let select = newRow.querySelector('select');
                                                                inputs[0].name = 'items[' + newIndex + '][box_no]';
                                                                inputs[0].value = newIndex + 1;
                                                                inputs[1].name = 'items[' + newIndex +
                                                                    '][description]';
                                                                inputs[2].name = 'items[' + newIndex + '][hs_code]';
                                                                select.name = 'items[' + newIndex + '][unit_type]';
                                                                inputs[3].name = 'items[' + newIndex + '][qty]';
                                                                inputs[4].name = 'items[' + newIndex +
                                                                    '][unit_rate]';
                                                                // remove old button
                                                                let actionCell = newRow.children[7];
                                                                actionCell.innerHTML = '';
                                                                // add delete button
                                                                let btn = document.createElement('button');
                                                                btn.innerHTML = '×';
                                                                btn.className = 'remove-btn';
                                                                btn.onclick = function() {
                                                                    newRow.remove();
                                                                    updateTotal();
                                                                    updateBoxNumbers();
                                                                    updateInputNames();
                                                                };
                                                                actionCell.appendChild(btn);
                                                                table.appendChild(newRow);
                                                                updateInputNames();
                                                            });
                                                        // AUTO CALCULATION
                                                        document.addEventListener('input', function(e) {
                                                            if (e.target.classList.contains('qty') || e.target
                                                                .classList.contains('rate')) {
                                                                let row = e.target.closest('tr');
                                                                let qty = row.querySelector('.qty').value || 0;
                                                                let rate = row.querySelector('.rate').value ||
                                                                    0;
                                                                row.querySelector('.amount').value = qty * rate;
                                                                updateTotal();
                                                            }
                                                        });
                                                        // TOTAL
                                                        function updateTotal() {
                                                            let total = 0;
                                                            document.querySelectorAll('.amount').forEach(input => {
                                                                total += Number(input.value) || 0;
                                                            });
                                                            document.getElementById('totalAmount').value = total;
                                                        }
                                                        // UPDATE BOX NUMBER
                                                        function updateBoxNumbers() {
                                                            document.querySelectorAll('.boxNo').forEach((input,
                                                                index) => {
                                                                input.value = index + 1;
                                                            });
                                                        }
                                                        // UPDATE INPUT NAMES
                                                        function updateInputNames() {
                                                            let rows = document.querySelectorAll('#invoiceTable tr');
                                                            rows.forEach((row, index) => {
                                                                row.querySelector('.boxNo').name = 'items[' +
                                                                    index + '][box_no]';
                                                                row.querySelector('.boxNo').value = index + 1;
                                                                let inputs = row.querySelectorAll('input');
                                                                let select = row.querySelector('select');
                                                                if (inputs[1]) inputs[1].name = 'items[' +
                                                                    index + '][description]';
                                                                if (inputs[2]) inputs[2].name = 'items[' +
                                                                    index + '][hs_code]';
                                                                if (select) select.name = 'items[' + index +
                                                                    '][unit_type]';
                                                                if (inputs[3]) inputs[3].name = 'items[' +
                                                                    index + '][qty]';
                                                                if (inputs[4]) inputs[4].name = 'items[' +
                                                                    index + '][unit_rate]';
                                                            });
                                                        }
                                                        </script>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Rate Calculate -->
                                        <div class="accordion-item border-top rounded mb-3">
                                            <div class="accordion-header">
                                                <a href="#" class="accordion-button accordion-custom-button rounded"
                                                    data-bs-toggle="collapse" data-bs-target="#rate-calc">
                                                    <span class="avatar avatar-md rounded me-1"
                                                        id="rateCalculateNumber">6</span>
                                                    Rate Calculate
                                                </a>
                                            </div>
                                            <div class="accordion-collapse collapse" id="rate-calc"
                                                data-bs-parent="#main_accordion">
                                                <div class="accordion-body border-top">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-4">
                                                                <label class="form-label">DDP</label>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <div class="form-check">
                                                                        <input type="radio" id="united-my-delivery" name="ddp_shipping_method" value="DDP - United My Delivery" {{ old('ddp_shipping_method') == 'DDP - United My Delivery' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label" for="united-my-delivery">United My Delivery</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="united-air-premium" name="ddp_shipping_method" value="DDP - United Air Premium" {{ old('ddp_shipping_method') == 'DDP - United Air Premium' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label" for="united-air-premium">United Air Premium </label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="united-grd-premium" name="ddp_shipping_method" value="DDP - United GRD Premium" {{ old('ddp_shipping_method') == 'DDP - United GRD Premium' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label" for="united-grd-premium">United GRD Premium </label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="united-air-express" name="ddp_shipping_method" value="DDP - United Air Express" {{ old('ddp_shipping_method') == 'DDP - United Air Express' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label" for="united-air-express">United Air Express</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="united-prior-post" name="ddp_shipping_method" value="DDP - United Prior Post" {{ old('ddp_shipping_method') == 'DDP - United Prior Post' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label" for="united-prior-post">United Prior Post</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="united-eco-post" name="ddp_shipping_method" value="DDP - United ECO Post" {{ old('ddp_shipping_method') == 'DDP - United ECO Post' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label" for="united-eco-post">United ECO Post</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="united-my-pickup" name="ddp_shipping_method" value="DDP - United My Pickup" {{ old('ddp_shipping_method') == 'DDP - United My Pickup' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label" for="united-my-pickup">United My Pickup</label>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="mb-4">
                                                                <label class="form-label">DDU</label>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <div class="form-check">
                                                                        <input type="radio" id="united-my-delivery1" name="ddp_shipping_method" value="DDU - United My Delivery" {{ old('ddp_shipping_method') == 'DDU - United My Delivery' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label" for="united-my-delivery1">United My Delivery</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="united-air-premium1" name="ddp_shipping_method" value="DDU - United Air Premium" {{ old('ddp_shipping_method') == 'DDU - United Air Premium' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label" for="united-air-premium1">United Air Premium </label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="united-grd-premium1" name="ddp_shipping_method" value="DDU - United GRD Premium" {{ old('ddp_shipping_method') == 'DDU - United GRD Premium' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label" for="united-grd-premium1">United GRD Premium </label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" id="united-my-pickup1" name="ddp_shipping_method" value="DDU - United My Pickup" {{ old('ddp_shipping_method') == 'DDU - United My Pickup' ? 'checked' : '' }} class="form-check-input">
                                                                        <label class="form-check-label" for="united-my-pickup1">United My Pickup</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- UPS Rate Result -->
                                                    <div class="row mt-3" id="upsRateResult" style="display:none;">
                                                        <div class="col-12">
                                                            <div class="card border">
                                                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">UPS Rate Response</h6>
                                                                    <span class="badge bg-success" id="rateStatusBadge">Success</span>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered mb-0">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th>Service</th>
                                                                                    <th>Zone</th>
                                                                                    <th>Weight</th>
                                                                                    <th>Transportation Charges</th>
                                                                                    <th>Total Charges</th>
                                                                                    <th>Currency</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="upsRateTableBody">
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div id="upsRateError" class="alert alert-danger mt-3 d-none"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- /UPS Rate Result -->
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Rate Calculate -->
                                    </div>
                                    <!-- /Access -->
                                    <div class="mt-4 d-flex align-items-center justify-content-end">
                                        <button type="reset" class="btn btn-light me-2">Reset</button>
                                        <button type="submit" class="btn btn-primary">Create Now</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <div class="tips-card"
                                    style="background: #f3f3f3; padding: 15px; position: sticky; top:80px">
                                    <div class="tips-title" style="text-align: center;">
                                        <h5> Quick Tips</h5>
                                    </div>
                                    <div class="tips-image" style="text-align: center;">
                                        <!-- Replace with your image -->
                                        <img src="{{ asset('assets/images/box.webp') }}" alt="Box"
                                            style="width: 150px;">
                                    </div>
                                    <h6>Dead Weight:</h6>
                                    <p>
                                        Dead weight (or dry weight) refers to the actual weight of
                                        the package in kilograms.
                                    </p>
                                    <h6>Volumetric Weight: ( L x W x H / 5000 )</h6>
                                    <p>
                                        Volumetric Weight (or DIM weight) is calculated based on the
                                        dimensions of the package.
                                    </p>
                                    <p>
                                        The formula for calculating volumetric weight involves
                                        multiplying the length, width, and height of the package and
                                        then dividing by 5000.
                                    </p>
                                    <h6>Additionally:</h6>
                                    <p>
                                        The higher value between volumetric weight and dead weight
                                        will be used for freight rate calculation.
                                    </p>
                                    <p>
                                        Prices are subject to change based on fuel surcharges and
                                        courier company base rates.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- card end -->
            </div>
            <!-- End Content -->
            <!-- Start Footer -->
            <footer class="footer d-block d-md-flex justify-content-between text-md-start text-center">
                <p class="mb-md-0 mb-1">Copyright &copy; <script data-cfasync="false"
                        src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
                    <script type="5d3b6c488f778ded9171c76c-text/javascript">
                    document.write(new Date().getFullYear())
                    </script> <a href="javascript:void(0);" class="link-primary text-decoration-underline">CRMS</a>
                </p>
                <div class="d-flex align-items-center gap-2 footer-links justify-content-center justify-content-md-end">
                    <a href="javascript:void(0);">About</a>
                    <a href="javascript:void(0);">Terms</a>
                    <a href="javascript:void(0);">Contact Us</a>
                </div>
            </footer>
            <!-- End Footer -->
        </div>
        <!-- ========================
			End Page Content
		========================= -->
    </div>
    <!-- End Wrapper -->
    <!-- Add Contact -->
    <!-- /Add Contact -->
    <!-- edit Contact -->
    <!-- /edit Contact -->
    <!-- Add New Deals -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_2">
        <div class="offcanvas-header border-bottom">
            <h5 class="mb-0">Add New Deals</h5>
            <button type="button"
                class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
                data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ url('/customer/create-shipment') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Deal Name <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="shipper_email" placeholder="Email Address">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="form-label">Pipeine <span class="text-danger">*</span></label>
                            </div>
                            <select class="select2" data-toggle="select2">
                                <option>Choose</option>
                                <option>Sales</option>
                                <option>Marketing</option>
                                <option>Calls</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="select2" data-toggle="select2">
                                <option>Choose</option>
                                <option>Open</option>
                                <option>Lost</option>
                                <option>Won</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Deal Value<span class="text-danger"> *</span></label>
                            <input class="form-control" type="text">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Currency <span class="text-danger">*</span></label>
                            <select class="select">
                                <option>Choose</option>
                                <option>Dollar</option>
                                <option>Euro</option>
                                <option>Pound</option>
                                <option>Rupee</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Period <span class="text-danger">*</span></label>
                            <select class="select">
                                <option>Choose</option>
                                <option>Days</option>
                                <option>Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Period Value <span class="text-danger">*</span></label>
                            <input class="form-control" type="text">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Contacts <span class="text-danger">*</span></label>
                            <select class="multiple-img" multiple="multiple" data-toggle=" multiple">
                                <option data-image="{{ asset('assets/img/profiles/avatar-19.jpg') }}" selected>Darlee
                                    Robertson
                                </option>
                                <option data-image="{{ asset('assets/img/users/user-01.jpg') }}">Sharon Roy</option>
                                <option data-image="{{ asset('assets/img/profiles/avatar-21.jpg') }}">Vaughan Lewis
                                </option>
                                <option data-image="{{ asset('assets/img/profiles/avatar-23.jpg') }}">Jessica Louise
                                </option>
                                <option data-image="{{ asset('assets/img/profiles/avatar-16.jpg') }}">Carol Thomas
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Project <span class="text-danger">*</span></label>
                            <input class="input-tags form-control border-0 h-100" data-choices
                                data-choices-limit="infinite" data-choices-removeItem type="text"
                                value="Devops Design, MargrateDesign">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <div class="input-group w-auto input-group-flat">
                                <input type="text" class="form-control" data-provider="flatpickr"
                                    data-date-format="d M, Y">
                                <span class="input-group-text">
                                    <i class="ti ti-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Expected Closing Date <span class="text-danger">*</span></label>
                            <div class="input-group w-auto input-group-flat">
                                <input type="text" class="form-control" data-provider="flatpickr"
                                    data-date-format="d M, Y">
                                <span class="input-group-text">
                                    <i class="ti ti-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Assignee <span class="text-danger">*</span></label>
                            <select class="multiple-img" multiple="multiple">
                                <option data-image="assets/img/profiles/avatar-19.jpg">Darlee Robertson</option>
                                <option data-image="assets/img/profiles/avatar-20.jpg" selected>Sharon Roy</option>
                                <option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis</option>
                                <option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise</option>
                                <option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Follow Up Date <span class="text-danger">*</span></label>
                            <div class="input-group w-auto input-group-flat">
                                <input type="text" class="form-control" data-provider="flatpickr"
                                    data-date-format="d M, Y">
                                <span class="input-group-text">
                                    <i class="ti ti-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Source <span class="text-danger">*</span></label>
                            <select class="select">
                                <option>Select</option>
                                <option>Google</option>
                                <option>Social Media</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tags <span class="text-danger">*</span></label>
                            <input class="input-tags form-control border-0 h-100" data-choices
                                data-choices-limit="infinite" data-choices-removeItem type="text" value="Collab, Rated">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="select">
                                <option>Select</option>
                                <option>High</option>
                                <option>Low</option>
                                <option>Medium</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <div class="editor pages-editor"></div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#create_success_2">Create</button>
                </div>
            </form>
        </div>
    </div>
    <!-- /Add New Deals -->
    <!-- success modal -->
    <div class="modal fade" id="create_success">
        <div class="modal-dialog modal-dialog-centered modal-sm rounded-0">
            <div class="modal-content rounded-0">
                <div class="modal-body p-4 text-center position-relative">
                    <div class="mb-3 position-relative z-1">
                        <span class="avatar avatar-xl badge-soft-success border-0 text-success rounded-circle"><i
                                class="ti ti-user-plus fs-24"></i></span>
                    </div>
                    <h5 class="mb-1">Contact Created Successfully!!!</h5>
                    <p class="mb-3">View the details of contact, created</p>
                    <div class="d-flex justify-content-center">
                        <a href="#" class="btn btn-light position-relative z-1 me-2 w-100"
                            data-bs-dismiss="modal">Cancel</a>
                        <a href="contact-details.html" class="btn btn-primary position-relative z-1 w-100">View
                            Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- success modal -->
    <!-- delete modal -->
    <!-- delete modal -->
    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <!-- Daterangepikcer JS -->
    <script src="{{ asset('assets/js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript">
    </script>
    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <!-- Choices Js -->
    <script src="{{ asset('assets/plugins/choices.js/public/assets/scripts/choices.min.js') }}" type="text/javascript">
    </script>
    <!-- Mobile Input -->
    <script src="{{ asset('assets/plugins/intltelinput/js/intlTelInput.js') }}" type="text/javascript">
    </script>
    <!-- Quill JS -->
    <script src="{{ asset('assets/plugins/quill/quill.min.js') }}" type="text/javascript">
    </script>
    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript">
    </script>
    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript">
    </script>
    <!-- Main JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success') || session('error') || $errors->any())
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messages = @json($errors->all());
            const fallbackMessage = @json(session('error') ?: session('success'));
            const errorHtml = messages.length ?
                '<ul class="text-start mb-0 ps-3">' + messages.map(function(message) {
                    return '<li>' + message + '</li>';
                }).join('') + '</ul>' :
                fallbackMessage;

            Swal.fire({
                icon: @json(session('success') ? 'success' : 'error'),
                title: @json(session('success') ? 'Success!' : 'Unable to create shipment'),
                html: errorHtml,
                confirmButtonColor: @json(session('success') ? '#2563eb' : '#dc3545')
            });
        });
        </script>
    @endif
    <script>
    // Toggle CSB Information section based on Origin Type selection
    document.addEventListener('DOMContentLoaded', function() {
        const originTypeSelect = document.getElementById('originType');
        const csbInfoSection = document.getElementById('csbInfoSection');
        const csbStatus = @json($customer->csb_status ?? null);

        function toggleCsbInfo() {
            const rateCalcNum = document.getElementById('rateCalculateNumber');
            if (originTypeSelect.value === 'CSB V' && csbStatus !== 1) {
                csbInfoSection.style.display = 'block';
                // Order: 1-Shipper, 2-Consignee, 3-Package, 4-CSB, 5-Invoice, 6-Rate Calculate
                document.getElementById('csbInfoNumber').textContent = '4';
                document.getElementById('shipmentInvoiceNumber').textContent = '5';
                if (rateCalcNum) rateCalcNum.textContent = '6';
            } else {
                csbInfoSection.style.display = 'none';
                $('#csbinfo').collapse('hide');
                // Order: 1-Shipper, 2-Consignee, 3-Package, 4-Invoice, 5-Rate Calculate
                document.getElementById('shipmentInvoiceNumber').textContent = '4';
                if (rateCalcNum) rateCalcNum.textContent = '5';
            }
        }
        // Initial check
        toggleCsbInfo();
        // Listen for changes
        originTypeSelect.addEventListener('change', toggleCsbInfo);
        // Also handle Select2 changes if it's initialized
        if (originTypeSelect.classList.contains('select2-hidden-accessible')) {
            $(document).on('change', '#originType', toggleCsbInfo);
        }
    });
    </script>
    <script>
    // Helper to get value from input/select
    function getVal(selector) {
        const el = document.querySelector(selector);
        return el ? el.value.trim() : '';
    }

    // Helper to get value from a field by name within a container element
    function getNestedVal(container, fieldName) {
        const input = container.querySelector('[name$="[' + fieldName + ']"]');
        return input ? input.value.trim() : '';
    }

    // Convert kg to lbs (1 kg = 2.20462 lbs)
    function kgToLbs(kg) {
        return (parseFloat(kg) * 2.20462).toFixed(2);
    }

    // Map delivery destination text to country code
    function getCountryCodeFromDestination(dest) {
        const map = {
            'US- United State of America': 'US',
            'India': 'IN',
            'UK - United Kingdom': 'GB',
            'China': 'CN',
            'Russia': 'RU',
            'Srilanka': 'LK'
        };
        return map[dest] || 'US';
    }

    // Map selected shipping method (from radio buttons in Rate Calculate section) to UPS service code
    function getServiceCodeFromShippingMethod(methodValue) {
        const methodMap = {
            'DDP - United My Delivery': '03',      // UPS Ground
            'DDP - United Air Premium': '01',      // UPS Next Day Air
            'DDP - United GRD Premium': '02',      // UPS 2nd Day Air
            'DDP - United Air Express': '07',      // UPS Worldwide Express
            'DDP - United Prior Post': '11',       // UPS Standard
            'DDP - United ECO Post': '65',         // UPS Saver
            'DDP - United My Pickup': '03',

            
            'DDU - United My Delivery': '03',
            'DDU - United Air Premium': '01',
            'DDU - United GRD Premium': '02',
            'DDU - United My Pickup': '03'
        };
        return methodMap[methodValue] || '65';     // fallback
    }

    // Map selected shipping method to UPS service description
    function getServiceDescriptionFromShippingMethod(methodValue) {
        const descMap = {
            'DDP - United My Delivery': 'Ground',
            'DDP - United Air Premium': 'Next Day Air',
            'DDP - United GRD Premium': '2nd Day Air',
            'DDP - United Air Express': 'Worldwide Express',
            'DDP - United Prior Post': 'Standard',
            'DDP - United ECO Post': 'Saver',
            'DDP - United My Pickup': 'Ground',

            'DDU - United My Delivery': 'Ground',
            'DDU - United Air Premium': 'Next Day Air',
            'DDU - United GRD Premium': '2nd Day Air',
            'DDU - United My Pickup': 'Ground'
        };
        return descMap[methodValue] || 'Ground';
    }

    // Build the UPS Rate payload from the form
    // function buildRatePayload() {
    //     const shipperCompany = getVal('input[name="shipper_company_names"]');
    //     const shipperContact = getVal('input[name="shipper_contact_person"]');
    //     const shipperName = shipperCompany || shipperContact || 'Unknown Shipper';
    //     const shipperPostal = getVal('input[name="shipper_pincode"]');
    //     const shipperCity = getVal('input[name="shipper_city"]');
    //     const shipperState = getVal('input[name="shipper_state"]');
    //     const shipperCountry = 'IN';

    //     const consigneePostal = getVal('input[name="consignee_zip_code"]');
    //     const deliveryDest = getVal('select[name="delivery_destination"]');
    //     const destCountry = getCountryCodeFromDestination(deliveryDest);

    //     let selectedMethod = '';
    //     const radio = document.querySelector('input[name="ddp_shipping_method"]:checked');
    //     if (radio) selectedMethod = radio.value;
    //     const serviceCode = getServiceCodeFromShippingMethod(selectedMethod);

    //     let weightKg = getVal('input[name="packages[0][actual_weight_kg]"]');
    //     if (!weightKg) weightKg = '1';

    //     return {
    //         RateRequest: {
    //             Shipment: {
    //                 Shipper: {
    //                     Name: shipperName,
    //                     ShipperNumber: "1255AK",
    //                     Address: {
    //                         City: shipperCity,
    //                         StateProvinceCode: shipperState,
    //                         PostalCode: shipperPostal,
    //                         CountryCode: shipperCountry
    //                     }
    //                 },
    //                 ShipFrom: {
    //                     Address: {
    //                         PostalCode: shipperPostal,
    //                         CountryCode: shipperCountry
    //                     }
    //                 },
    //                 ShipTo: {
    //                     Address: {
    //                         PostalCode: consigneePostal,
    //                         CountryCode: destCountry
    //                     }
    //                 },
    //                 Service: {
    //                     // Code: serviceCode
    //                     Code: "65"
    //                 },
    //                 Package: {
    //                     PackagingType: {
    //                         Code: "02"
    //                     },
    //                     PackageWeight: {
    //                         UnitOfMeasurement: {
    //                             Code: "KGS"
    //                         },
    //                         Weight: weightKg
    //                     }
    //                 }
    //             }
    //         }
    //     };
    // }


    function buildRatePayload() {
    // Shipper
    const shipperCompany = getVal('input[name="shipper_company_names"]');
    const shipperContact = getVal('input[name="shipper_contact_person"]');
    const shipperName = shipperCompany || shipperContact || 'Unknown Shipper';
    const shipperPhone = getVal('input[name="shipper_phone_number"]');
    const shipperAddressLine1 = getVal('input[name="shipper_address_line1"]');
    const shipperAddressLine2 = getVal('input[name="shipper_address_line2"]');
    const shipperAddressLine3 = getVal('input[name="shipper_address_line3"]');
    const shipperPostal = getVal('input[name="shipper_pincode"]');
    const shipperCity = getVal('input[name="shipper_city"]');
    const shipperState = getVal('input[name="shipper_state"]');
    const shipperCountry = 'IN';

    // Build Shipper AddressLine array
    const shipperAddressLine = [];
    if (shipperAddressLine1) shipperAddressLine.push(shipperAddressLine1);
    if (shipperAddressLine2) shipperAddressLine.push(shipperAddressLine2);
    if (shipperAddressLine3) shipperAddressLine.push(shipperAddressLine3);

    // Consignee
    const consigneeName = getVal('input[name="consignee_name"]');
    const consigneePhone = getVal('input[name="consignee_phone_number"]');
    const consigneeAddressLine1 = getVal('input[name="consignee_address_line1"]');
    const consigneeAddressLine2 = getVal('input[name="consignee_address_line2"]');
    const consigneeAddressLine3 = getVal('input[name="consignee_address_line3"]');
    const consigneePostal = getVal('input[name="consignee_zip_code"]');
    const consigneeCity = getVal('input[name="consignee_city"]');
    const consigneeState = getVal('input[name="consignee_state"]');
    const deliveryDest = getVal('select[name="delivery_destination"]');
    const destCountry = getCountryCodeFromDestination(deliveryDest);

    // Build Consignee AddressLine array
    const consigneeAddressLine = [];
    if (consigneeAddressLine1) consigneeAddressLine.push(consigneeAddressLine1);
    if (consigneeAddressLine2) consigneeAddressLine.push(consigneeAddressLine2);
    if (consigneeAddressLine3) consigneeAddressLine.push(consigneeAddressLine3);

    // Selected shipping method
    const selectedRadio = document.querySelector('input[name="ddp_shipping_method"]:checked, input[name="ddu_shipping_method"]:checked');
    if (!selectedRadio) {
        alert('Please select a shipping method');
        return null;
    }
    const methodValue = selectedRadio.value;
    const serviceCode = getServiceCodeFromShippingMethod(methodValue);

    // Build Package array from ALL package dimension rows
    const packageRows = document.querySelectorAll('.rowContaineraddmore');
    const packages = [];

    packageRows.forEach(function(row, index) {
        const weightKg = getNestedVal(row, 'actual_weight_kg');
        const lengthCm = getNestedVal(row, 'length_cm');
        const widthCm = getNestedVal(row, 'width_cm');
        const heightCm = getNestedVal(row, 'height_cm');

        // Skip empty rows if weight is not provided
        if (!weightKg || isNaN(weightKg) || weightKg <= 0) {
            return;
        }

        const pkg = {
            PackagingType: { Code: "02" }, // Customer Supplied Package
            PackageWeight: {
                UnitOfMeasurement: { Code: "KGS" },
                Weight: weightKg
            }
        };

        // Add dimensions only if all three are provided
        if (lengthCm && widthCm && heightCm) {
            pkg.Dimensions = {
                UnitOfMeasurement: { Code: "CM" },
                Length: lengthCm,
                Width: widthCm,
                Height: heightCm
            };
        }

        packages.push(pkg);
    });

    // Fallback: if no valid package rows, send a single default 1kg package
    if (packages.length === 0) {
        packages.push({
            PackagingType: { Code: "02" },
            PackageWeight: {
                UnitOfMeasurement: { Code: "KGS" },
                Weight: "1"
            }
        });
    }

    // return {
    //     RateRequest: {
    //         Request: {
    //             RequestOption: "Rate"
    //         },
    //         Shipment: {
    //             Shipper: {
    //                 Name: shipperName,
    //                 AttentionName: shipperContact || shipperName,
    //                 ShipperNumber: "1255AK",
    //                 Phone: {
    //                     Number: shipperPhone || ""
    //                 },
    //                 Address: {
    //                     AddressLine: shipperAddressLine.length > 0 ? shipperAddressLine : ["Not Provided"],
    //                     City: shipperCity,
    //                     StateProvinceCode: shipperState,
    //                     PostalCode: shipperPostal,
    //                     CountryCode: shipperCountry
    //                 }
    //             },
    //             ShipFrom: {
    //                 Name: shipperName,
    //                 AttentionName: shipperContact || shipperName,
    //                 Phone: {
    //                     Number: shipperPhone || ""
    //                 },
    //                 Address: {
    //                     AddressLine: shipperAddressLine.length > 0 ? shipperAddressLine : ["Not Provided"],
    //                     City: shipperCity,
    //                     StateProvinceCode: shipperState,
    //                     PostalCode: shipperPostal,
    //                     CountryCode: shipperCountry
    //                 }
    //             },
    //             ShipTo: {
    //                 Name: consigneeName || "Consignee",
    //                 AttentionName: consigneeName || "Consignee",
    //                 Phone: {
    //                     Number: consigneePhone || ""
    //                 },
    //                 Address: {
    //                     AddressLine: consigneeAddressLine.length > 0 ? consigneeAddressLine : ["Not Provided"],
    //                     City: consigneeCity,
    //                     StateProvinceCode: consigneeState,
    //                     PostalCode: consigneePostal,
    //                     CountryCode: destCountry
    //                 }
    //             },
    //             Service: {
    //                 Code: serviceCode
    //             },
    //             Package: packages
    //         }
    //     }
    // };

    return {
    RateRequest: {
        Request: {
            RequestOption: "Rate"
        },

        PickupType: {
            Code: "01"
        },

        CustomerClassification: {
            Code: "01"
        },

        Shipment: {
            Shipper: {
                Name: shipperName,
                AttentionName: shipperContact || shipperName,

                // VALID UPS ACCOUNT
                ShipperNumber: "1255AK",

                Phone: {
                    Number: shipperPhone || ""
                },

                Address: {
                    AddressLine:
                        shipperAddressLine.length > 0
                            ? shipperAddressLine
                            : ["Not Provided"],

                    City: shipperCity,
                    StateProvinceCode: shipperState,
                    PostalCode: shipperPostal,
                    CountryCode: shipperCountry
                }
            },

            ShipFrom: {
                Name: shipperName,
                AttentionName: shipperContact || shipperName,

                Phone: {
                    Number: shipperPhone || ""
                },

                Address: {
                    AddressLine:
                        shipperAddressLine.length > 0
                            ? shipperAddressLine
                            : ["Not Provided"],

                    City: shipperCity,
                    StateProvinceCode: shipperState,
                    PostalCode: shipperPostal,
                    CountryCode: shipperCountry
                }
            },

            ShipTo: {
                Name: consigneeName || "Consignee",
                AttentionName: consigneeName || "Consignee",

                Phone: {
                    Number: consigneePhone || ""
                },

                Address: {
                    AddressLine:
                        consigneeAddressLine.length > 0
                            ? consigneeAddressLine
                            : ["Not Provided"],

                    City: consigneeCity,
                    StateProvinceCode: consigneeState,
                    PostalCode: consigneePostal,
                    CountryCode: destCountry
                }
            },

            Service: {
                Code: serviceCode
            },

            Package: packages
        }
    }
};
}

    // Reusable function to call UPS rate API
    function calculateRate() {
        const resultDiv = document.getElementById('upsRateResult');
        const tableBody = document.getElementById('upsRateTableBody');
        const errorDiv = document.getElementById('upsRateError');
        const btn = document.getElementById('rateCalculateBtn');

        // Show the rate calculate accordion
        $('#rate-calc').collapse('show');

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Calculating...';
        }
        resultDiv.style.display = 'none';
        errorDiv.classList.add('d-none');

        const payload = buildRatePayload();
        if (!payload) {
            // buildRatePayload already shows alert if no method selected
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="ti ti-calculator me-1"></i> Rate Calculate';
            }
            return;
        }
        console.log('Payload sent to UPS:', payload);

        fetch('/customer/ups-rate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || ''
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.rateResponse) {
                const rate = data.rateResponse;
                resultDiv.style.display = 'block';
                document.getElementById('rateStatusBadge').textContent = 'Success';
                document.getElementById('rateStatusBadge').className = 'badge bg-success';

                let rows = '';
                if (Array.isArray(rate.RatedShipment)) {
                    rate.RatedShipment.forEach(ship => {
                        rows += `<tr>
                            <td>${ship.Service?.Code || ''}</td>
                            <td>${ship.Zone || ''}</td>
                            <td>${ship.BillingWeight?.Weight || ''} ${ship.BillingWeight?.UnitOfMeasurement?.Code || ''}</td>
                            <td>${ship.TransportationCharges?.MonetaryValue || ''}</td>
                            <td><strong>${ship.TotalCharges?.MonetaryValue || ''}</strong></td>
                            <td>${ship.TotalCharges?.CurrencyCode || ''}</td>
                        </tr>`;
                    });
                }
                tableBody.innerHTML = rows;
            } else {
                // Clear previous table data and show clean error message
                tableBody.innerHTML = '';
                errorDiv.textContent = data.message || 'Failed to get UPS rate';
                errorDiv.classList.remove('d-none');
                resultDiv.style.display = 'block';
                document.getElementById('rateStatusBadge').textContent = 'Error';
                document.getElementById('rateStatusBadge').className = 'badge bg-danger';
            }
        })
        .catch(err => {
            console.error(err);
            tableBody.innerHTML = '';
            errorDiv.textContent = 'Network error. Please try again.';
            errorDiv.classList.remove('d-none');
            resultDiv.style.display = 'block';
            document.getElementById('rateStatusBadge').textContent = 'Error';
            document.getElementById('rateStatusBadge').className = 'badge bg-danger';
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="ti ti-calculator me-1"></i> Rate Calculate';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Button click handler triggers rate calculation
        const btn = document.getElementById('rateCalculateBtn');
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                calculateRate();
            });
        }

        // Auto-calculate rate when any shipping method radio button changes
        document.querySelectorAll('input[name="ddp_shipping_method"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    calculateRate();
                }
            });
        });
    });

    // Build UPS ShipmentRequest payload for the UPS Ship API
    function buildShipPayload() {
        // Shipper
        const shipperCompany = getVal('input[name="shipper_company_names"]');
        const shipperContact = getVal('input[name="shipper_contact_person"]');
        const shipperName = shipperCompany || shipperContact || 'Unknown Shipper';
        const shipperPhone = getVal('input[name="shipper_phone_number"]');
        const shipperAddressLine1 = getVal('input[name="shipper_address_line1"]');
        const shipperAddressLine2 = getVal('input[name="shipper_address_line2"]');
        const shipperAddressLine3 = getVal('input[name="shipper_address_line3"]');
        const shipperPostal = getVal('input[name="shipper_pincode"]');
        const shipperCity = getVal('input[name="shipper_city"]');
        const shipperState = getVal('input[name="shipper_state"]');
        const deliveryDest = getVal('select[name="delivery_destination"]');
        const shipperCountry = 'IN';

        const shipperAddressLine = [];
        if (shipperAddressLine1) shipperAddressLine.push(shipperAddressLine1);
        if (shipperAddressLine2) shipperAddressLine.push(shipperAddressLine2);
        if (shipperAddressLine3) shipperAddressLine.push(shipperAddressLine3);

        // Consignee
        const consigneeName = getVal('input[name="consignee_name"]');
        const consigneePhone = getVal('input[name="consignee_phone_number"]');
        const consigneeAddressLine1 = getVal('input[name="consignee_address_line1"]');
        const consigneeAddressLine2 = getVal('input[name="consignee_address_line2"]');
        const consigneeAddressLine3 = getVal('input[name="consignee_address_line3"]');
        const consigneePostal = getVal('input[name="consignee_zip_code"]');
        const consigneeCity = getVal('input[name="consignee_city"]');
        const consigneeState = getVal('input[name="consignee_state"]');
        const destCountry = getCountryCodeFromDestination(deliveryDest) || 'US';

        const consigneeAddressLine = [];
        if (consigneeAddressLine1) consigneeAddressLine.push(consigneeAddressLine1);
        if (consigneeAddressLine2) consigneeAddressLine.push(consigneeAddressLine2);
        if (consigneeAddressLine3) consigneeAddressLine.push(consigneeAddressLine3);

        // Selected shipping method
        const selectedRadio = document.querySelector('input[name="ddp_shipping_method"]:checked, input[name="ddu_shipping_method"]:checked');
        const serviceCode = selectedRadio ? getServiceCodeFromShippingMethod(selectedRadio.value) : '03';
        const serviceDescription = selectedRadio ? getServiceDescriptionFromShippingMethod(selectedRadio.value) : 'Ground';

        // Build Packages array from ALL package dimension rows
        const packageRows = document.querySelectorAll('.rowContaineraddmore');
        const packages = [];

        packageRows.forEach(function(row) {
            const weightKg = getNestedVal(row, 'actual_weight_kg');
            const lengthCm = getNestedVal(row, 'length_cm');
            const widthCm = getNestedVal(row, 'width_cm');
            const heightCm = getNestedVal(row, 'height_cm');

            // Skip empty rows if weight is not provided
            if (!weightKg || isNaN(weightKg) || weightKg <= 0) {
                return;
            }

            const pkg = {
                Description: "Documents",
                Packaging: { Code: "02" },
                PackageWeight: {
                    UnitOfMeasurement: { Code: "LBS" },
                    Weight: weightKg
                }
            };

            // Add dimensions only if all three are provided
            if (lengthCm && widthCm && heightCm) {
                pkg.Dimensions = {
                    UnitOfMeasurement: { Code: "IN" },
                    Length: lengthCm,
                    Width: widthCm,
                    Height: heightCm
                };
            } else {
                pkg.Dimensions = {
                    UnitOfMeasurement: { Code: "IN" },
                    Length: "10",
                    Width: "8",
                    Height: "4"
                };
            }

            packages.push(pkg);
        });

        // Fallback to a single default package if no rows were added
        if (packages.length === 0) {
            packages.push({
                Description: "Documents",
                Packaging: { Code: "02" },
                PackageWeight: {
                    UnitOfMeasurement: { Code: "LBS" },
                    Weight: "5"
                },
                Dimensions: {
                    UnitOfMeasurement: { Code: "IN" },
                    Length: "10",
                    Width: "8",
                    Height: "4"
                }
            });
        }

        // Build ShipmentRequest payload matching the exact requested structure
        const payload = {
            ShipmentRequest: {
                Shipment: {
                    Shipper: {
                        Name: shipperName,
                        AttentionName: shipperContact || shipperName,
                        CompanyDisplayableName: shipperName,
                        Phone: { Number: shipperPhone || "" },
                        ShipperNumber: "1255AK",
                        Address: {
                            AddressLine: shipperAddressLine.length > 0 ? shipperAddressLine : ["Shipper Address"],
                            City: shipperCity || "",
                            StateProvinceCode: shipperState || "",
                            PostalCode: shipperPostal || "",
                            // CountryCode: shipperCountry
                            CountryCode: "US"
                        }
                    },
                    // Shipper: {
                    //     Name: "SANDEEP KAPUR",
                    //     AttentionName: "United",
                    //     CompanyDisplayableName: "UWC",
                    //     Phone: { Number: "6466741258" },
                    //     ShipperNumber: "1255AK",
                    //     Address: {
                    //         AddressLine: "218 WEST 37 STREET 6TH FLOOR",
                    //         City: "NEW YORK",
                    //         StateProvinceCode: "NY",
                    //         PostalCode: "10018",
                    //         CountryCode: "US"
                    //     }
                    // },
                    ShipFrom: {
                        Name: shipperName,
                        AttentionName: shipperContact || shipperName,
                        Phone: { Number: shipperPhone},
                        Address: {
                            AddressLine: shipperAddressLine.length > 0 ? shipperAddressLine : ["Shipper Address"],
                            City: shipperCity,
                            StateProvinceCode: shipperState,
                            PostalCode: shipperPostal,
                            CountryCode: "US"
                        }
                    },
                    ShipTo: {
                        Name: consigneeName,
                        AttentionName: consigneeName,
                        Phone: { Number: consigneePhone },
                        Address: {
                            AddressLine: consigneeAddressLine.length > 0 ? consigneeAddressLine : ["Receiver Address"],
                            City: consigneeCity,
                            StateProvinceCode: consigneeState,
                            PostalCode: consigneePostal,
                            CountryCode: destCountry
                        }
                    },
                    PaymentInformation: {
                        ShipmentCharge: {
                            Type: "01",
                            BillShipper: {
                                AccountNumber: "1255AK"
                            }
                        }
                    },
                    Service: {
                        Code: serviceCode,
                        Description: serviceDescription
                    },
                    Package: packages
                },
                LabelSpecification: {
                    LabelImageFormat: { Code: "GIF" }
                }
            }
        };

        return payload;
    }

    // Call UPS Ship API
    function callUpsShipApi(payload) {
        return fetch('/customer/ups-ship', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || ''
            },
            body: JSON.stringify(payload)
        }).then(res => res.json());
    }
    </script>
    <script>
    // Handle Next button click to open Consignee Info accordion
    document.addEventListener('DOMContentLoaded', function() {
        const nextButton = document.getElementById('nextToConsignee');
        if (nextButton) {
            nextButton.addEventListener('click', function(e) {
                e.preventDefault();
                // Close Shipper Info accordion
                $('#basic').collapse('hide');
                // Open Consignee Info accordion
                $('#address').collapse('show');
            });
        }
    });
    </script>
    <script>
    // Handle form submission
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form[action*="create-shipment"]');

        function formatFieldName(field) {
            return field
                .replace(/^items\.\d+\./, 'item ')
                .replace(/_/g, ' ')
                .replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
        }

        function buildErrorHtml(errors) {
            if (!errors) {
                return '';
            }

            const messages = [];
            Object.keys(errors).forEach(function(field) {
                const fieldErrors = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
                fieldErrors.forEach(function(message) {
                    messages.push('<li><strong>' + formatFieldName(field) + ':</strong> ' + message + '</li>');
                });
            });

            return messages.length ? '<ul class="text-start mb-0 ps-3">' + messages.join('') + '</ul>' : '';
        }

        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitButton = form.querySelector('button[type="submit"]');
                if (!submitButton) {
                    return;
                }
                const originalText = submitButton.innerHTML;
                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || document.querySelector('input[name="_token"]')?.value
                    || '';

                // Build UPS Ship API payload and call UPS FIRST
                const shipPayload = buildShipPayload();
                console.log('UPS Ship Payload:', shipPayload);

                callUpsShipApi(shipPayload)
                    .then(upsResult => {
                        if (upsResult.success) {
                            console.log('UPS Ship API success:', upsResult.shipmentResponse);
                            // UPS succeeded - now save to database
                            const formData = new FormData(form);
                            // Append UPS shipment response for tracking storage
                            formData.append('ups_shipment_response', JSON.stringify(upsResult.shipmentResponse));
                            return fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            })
                            .then(response => response.json().then(data => ({
                                ok: response.ok,
                                status: response.status,
                                data
                            })))
                            .then(({ data }) => {
                                if (data.success) {
                                    // Clear localStorage saved form data
                                    if (typeof clearShipmentFormStorage === 'function') {
                                        clearShipmentFormStorage();
                                    }
                                    const trackingNumber = upsResult.shipmentResponse?.ShipmentResults?.PackageResults?.TrackingNumber || '';
                                    let successHtml = '<p>' + data.message + '</p>';
                                    if (trackingNumber) {
                                        successHtml += '<p><strong>UPS Tracking Number:</strong> ' + trackingNumber + '</p>';
                                    }
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Shipment Created!',
                                        html: successHtml,
                                        confirmButtonColor: '#2563eb'
                                    }).then(() => {
                                        // window.location.reload();
                                    });
                                } else {
                                    const errorHtml = buildErrorHtml(data.errors);
                                    Swal.fire({
                                        icon: 'error',
                                        title: data.message || 'Unable to create shipment',
                                        html: errorHtml || 'Please check the form and try again.',
                                        confirmButtonColor: '#dc3545'
                                    });
                                    if (data.errors) {
                                        console.log('Validation errors:', data.errors);
                                    }
                                }
                            });
                        } else {
                            // UPS failed - do NOT save to database
                            console.warn('UPS Ship API failed:', upsResult.message);
                            const upsErrorMsg = upsResult.message || 'Unknown UPS error';
                            const rawResponse = upsResult.rawResponse ? JSON.stringify(upsResult.rawResponse, null, 2) : '';
                            Swal.fire({
                                icon: 'error',
                                title: 'UPS Shipment Failed',
                                html: '<p><strong>Error:</strong> ' + upsErrorMsg + '</p>' +
                                      '<p>Shipment was not saved. Please try again.</p>' +
                                      (rawResponse ? '<pre style="max-height:200px;overflow-y:auto;text-align:left;font-size:11px;">' + rawResponse + '</pre>' : ''),
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(upsError => {
                        console.error('UPS Ship API network error:', upsError);
                        Swal.fire({
                            icon: 'error',
                            title: 'UPS Shipment Failed',
                            text: 'Network error. Shipment was not saved. Please try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    })
                    .finally(() => {
                        // Reset button state
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    });
            });
        });
    });

    // Check CSB status when origin_type changes
    document.addEventListener('DOMContentLoaded', function() {
        const originTypeSelect = document.getElementById('originType');
        const originTypeError = document.getElementById('originTypeError');
        const csbInfoSection = document.getElementById('csbInfoSection');
        const csbStatus = @json($customer->csb_status ?? null);

        if (originTypeSelect && typeof $ !== 'undefined') {
            // Use Select2 change event
            $(originTypeSelect).on('change', function() {
                if ($(this).val() === 'CSB V' && csbStatus === 1) {
                    // Show inline error message
                    if (originTypeError) {
                        originTypeError.style.display = 'block';
                    }
                    if (csbInfoSection) {
                        csbInfoSection.style.display = 'none';
                        $('#csbinfo').collapse('hide');
                    }
                    // Disable all form inputs except origin_type select
                    const form = $(this).closest('form');
                    if (form) {
                        form.find(
                            'input:not([name="origin_type"]), select:not(#originType), textarea, button[type="submit"]'
                        ).prop('disabled', true);
                    }
                    // Reset to CSB IV
                    // $(this).val('CSB IV').trigger('change');
                } else {
                    // Hide error message
                    if (originTypeError) {
                        originTypeError.style.display = 'none';
                    }
                    // Enable all form inputs
                    const form = $(this).closest('form');
                    if (form) {
                        form.find('input, select, textarea, button[type="submit"]').prop('disabled',
                            false);
                    }
                }
            });
        }
    });
    </script>
    <script>
    // ============================================================
    // FORM PERSISTENCE VIA LOCALSTORAGE
    // - Auto-saves all form field values on every change
    // - Restores values automatically on page load (survives F5)
    // - Cleared on successful form submit via AJAX
    // ============================================================
    (function() {
        const STORAGE_KEY = 'create_shipment_form_data';

        // Get all form elements in the main create-shipment form
        function getMainForm() {
            return document.querySelector('form[action*="create-shipment"]');
        }

        // Serialize all form fields (text, select, radio, checkbox, textarea) to a plain object
        function serializeForm() {
            const form = getMainForm();
            if (!form) return {};

            const data = {};
            const formData = new FormData(form);
            for (const [key, value] of formData.entries()) {
                // Handle multiple values with same name (e.g., checkboxes)
                if (data.hasOwnProperty(key)) {
                    if (!Array.isArray(data[key])) {
                        data[key] = [data[key]];
                    }
                    data[key].push(value);
                } else {
                    data[key] = value;
                }
            }

            // Also capture unchecked radio/checkbox state
            const radioGroups = new Set();
            form.querySelectorAll('input[type="radio"]').forEach(radio => {
                radioGroups.add(radio.name);
            });
            radioGroups.forEach(name => {
                const checkedRadio = form.querySelector('input[name="' + CSS.escape(name) +
                    '"]:checked');
                if (checkedRadio) {
                    data[name] = checkedRadio.value;
                }
            });

            // Save checkbox that weren't checked (not in FormData)
            form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                if (!cb.checked && !data.hasOwnProperty(cb.name)) {
                    data[cb.name] = '';
                }
            });

            return data;
        }

        // Save current form state to localStorage
        function saveToStorage() {
            const data = serializeForm();
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            } catch (e) {
                // Storage full or unavailable - silently ignore
            }
        }

        // Restore form values from localStorage
        function restoreFromStorage() {
            let saved;
            try {
                saved = localStorage.getItem(STORAGE_KEY);
            } catch (e) {
                return;
            }
            if (!saved) return;

            let data;
            try {
                data = JSON.parse(saved);
            } catch (e) {
                return;
            }

            const form = getMainForm();
            if (!form) return;

            // Track which fields were restored from storage
            const restoredFields = new Set();

            // Helper: safely escape a string for use inside a CSS attribute selector value
            function escapeAttr(str) {
                return String(str).replace(/\\/g, '\\\\').replace(/"/g, '\\"');
            }

            // We need a map of package row count and item row count from saved data
            let maxPackageIndex = -1;
            let maxItemIndex = -1;
            Object.keys(data).forEach(key => {
                const pkgMatch = key.match(/^packages\[(\d+)\]/);
                if (pkgMatch) {
                    maxPackageIndex = Math.max(maxPackageIndex, parseInt(pkgMatch[1]));
                }
                const itemMatch = key.match(/^items\[(\d+)\]/);
                if (itemMatch) {
                    maxItemIndex = Math.max(maxItemIndex, parseInt(itemMatch[1]));
                }
            });

            // Ensure enough package rows exist
            const packageRows = form.querySelectorAll('.rowContaineraddmore');
            const addPackageBtn = document.getElementById('addRowBtn');
            if (addPackageBtn && maxPackageIndex > 0 &&
                packageRows.length <= maxPackageIndex) {
                const rowsToAdd = maxPackageIndex - packageRows.length + 1;
                for (let i = 0; i < rowsToAdd; i++) {
                    try {
                        addPackageBtn.click();
                    } catch (e) {}
                }
            }

            // Ensure enough invoice item rows exist
            const itemRows = form.querySelectorAll('#invoiceTable tr');
            const addItemBtn = document.getElementById('tableaddRowBtn');
            if (addItemBtn && maxItemIndex > 0 &&
                itemRows.length <= maxItemIndex) {
                const rowsToAdd = maxItemIndex - itemRows.length + 1;
                for (let i = 0; i < rowsToAdd; i++) {
                    try {
                        addItemBtn.click();
                    } catch (e) {}
                }
            }

            // Short delay to let dynamic rows render, then fill values
            setTimeout(function() {
                fillFormValues(data, restoredFields);
                // Dispatch change events for Select2 and other plugins
                setTimeout(function() {
                    restoredFields.forEach(function(key) {
                        const escaped = escapeAttr(key);
                        const el = form.querySelector('[name="' + escaped + '"]');
                        if (el) {
                            el.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                            // Trigger Select2 update
                            if (typeof $ !== 'undefined' && $(el).hasClass(
                                    'select2-hidden-accessible')) {
                                $(el).trigger('change');
                            }
                        }
                    });
                }, 100);
            }, 150);
        }

        function fillFormValues(data, restoredFields) {
            const form = getMainForm();
            if (!form) return;

            // Pre-process: extract delivery_destination and origin_type to set first
            const priorityFields = ['delivery_destination', 'origin_type'];

            // Helper: safely escape a string for use inside a CSS attribute selector value
            function escapeAttr(str) {
                return String(str).replace(/\\/g, '\\\\').replace(/"/g, '\\"');
            }

            // Fill all fields
            Object.keys(data).forEach(function(name) {
                const value = data[name];
                const escaped = escapeAttr(name);

                // For radio buttons: check the matching one
                const radio = form.querySelector('input[type="radio"][name="' + escaped +
                    '"][value="' + escapeAttr(String(value)) + '"]');
                if (radio) {
                    radio.checked = true;
                    restoredFields.add(name);
                    return;
                }

                // For checkboxes
                const checkbox = form.querySelector('input[type="checkbox"][name="' + escaped +
                    '"]');
                if (checkbox) {
                    checkbox.checked = (value === 'on' || value === '1' || value === true ||
                        value === 'true');
                    restoredFields.add(name);
                    return;
                }

                // For select/input/textarea
                const escapedName = escapeAttr(name);
                const el = form.querySelector('[name="' + escapedName + '"]');
                if (el) {
                    if (el.tagName === 'SELECT') {
                        el.value = value;
                        restoredFields.add(name);
                    } else if (el.tagName === 'TEXTAREA' || el.tagName === 'INPUT') {
                        // Set value for text/email/number/date/hidden inputs
                        el.value = value;
                        // Also set 'value' attribute on intl-tel-input parent
                        if (el.classList.contains('phone') && typeof $ !==
                            'undefined') {
                            // Trigger intl-tel-input to re-process
                            el.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                        restoredFields.add(name);
                    }
                }
            });
        }

        // Clear localStorage
        function clearShipmentFormStorage() {
            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch (e) {}
        }

        // Expose clear function globally for the success handler
        window.clearShipmentFormStorage = clearShipmentFormStorage;

        // ---- Attach event listeners ----
        document.addEventListener('DOMContentLoaded', function() {
            const form = getMainForm();
            if (!form) return;

            // Restore saved values on page load (only if no server-side old() values exist)
            restoreFromStorage();

            // Auto-save on every form field change (debounced for performance)
            let saveTimeout;
            form.addEventListener('input', function(e) {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(saveToStorage, 300);
            });
            form.addEventListener('change', function(e) {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(saveToStorage, 300);
            });

            // Also save on Select2 selection (which may not trigger native change)
            if (typeof $ !== 'undefined') {
                $(form).on('select2:select select2:unselect', function() {
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(saveToStorage, 300);
                });
            }

            // Remove saved data when user clicks Reset button
            const resetBtn = form.querySelector('button[type="reset"]');
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    setTimeout(clearShipmentFormStorage, 100);
                });
            }
        });
    })();
    </script>
    <script src="../../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js"
        data-cf-settings="5d3b6c488f778ded9171c76c-|49" defer></script>
    <script defer
        src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015"
        integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ=="
        data-cf-beacon='{"rayId":"967b314f0fc122a8","version":"2025.7.0","serverTiming":{"name":{"cfExtPri":true,"cfEdge":true,"cfOrigin":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"token":"3ca157e612a14eccbb30cf6db6691c29","b":1}'
        crossorigin="anonymous"></script>
</body>
<!-- Mirrored from crms.dreamstechnologies.com/html/template/contacts.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 31 Jul 2025 06:56:53 GMT -->

</html>
