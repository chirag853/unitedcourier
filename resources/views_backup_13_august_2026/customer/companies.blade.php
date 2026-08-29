<!DOCTYPE html>
<html lang="en">



<head>

	<!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Companies | CRMS - Advanced Bootstrap 5 Admin Template for Customer Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	
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
                            <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x fs-22"></i></button>
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
                        <h4 class="mb-1">Companies<span class="badge badge-soft-primary ms-2">125</span></h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="index-2.html">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Companies</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-light px-2 shadow" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
                            <div class="dropdown-menu  dropdown-menu-end">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-pdf me-1"></i>Export as
                                            PDF</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-xls me-1"></i>Export as
                                            Excel </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>                
				<!-- End Page Header -->

                <!-- table header -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="btn btn-outline-light shadow px-2" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter me-2"></i>Filter<i class="ti ti-chevron-down ms-2"></i></a>
                            <div class="filter-dropdown-menu dropdown-menu dropdown-menu-lg p-0">
                                <div class="filter-header d-flex align-items-center justify-content-between border-bottom">
                                    <h6 class="mb-0"><i class="ti ti-filter me-1"></i>Filter</h6>
                                    <button type="button" class="btn-close close-filter-btn" data-bs-dismiss="dropdown-menu" aria-label="Close"></button>
                                </div>
                                <div class="filter-set-view p-3">                                            
                                    <div class="accordion" id="accordionExample">
                                        <div class="filter-set-content">
                                            <div class="filter-set-content-head">
                                                <a href="#" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">Owner</a>
                                            </div>
                                            <div class="filter-set-contents accordion-collapse collapse show" id="collapseTwo" data-bs-parent="#accordionExample">
                                                <div class="filter-content-list bg-light rounded border p-2 shadow mt-2">
                                                    <div class="mb-2">
                                                        <div class="input-icon-start input-icon position-relative">
                                                            <span class="input-icon-addon fs-12">
                                                                <i class="ti ti-search"></i>
                                                            </span>
                                                            <input type="text" class="form-control form-control-md" placeholder="Search">
                                                        </div>
                                                    </div>
                                                    <ul class="mb-0">
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xs rounded-circle me-2"><img src="{{ asset('assets/img/users/user-06.jpg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>Elizabeth Morgan
                                                            </label>
                                                        </li>
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xs rounded-circle me-2"><img src="{{ asset('assets/img/users/user-40.jpg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>Katherine Brooks
                                                            </label>
                                                        </li>
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xs rounded-circle me-2"><img src="{{ asset('assets/img/users/user-05.jpg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>Sophia Lopez
                                                            </label>
                                                        </li>
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xs rounded-circle me-2"><img src="{{ asset('assets/img/users/user-10.jpg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>John Michael
                                                            </label>
                                                        </li>
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xs rounded-circle me-2"><img src="{{ asset('assets/img/users/user-15.jpg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>Natalie Brooks
                                                            </label>
                                                        </li>
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xs rounded-circle me-2"><img src="{{ asset('assets/img/users/user-01.jpg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>William Turner
                                                            </label>
                                                        </li>
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xs rounded-circle me-2"><img src="{{ asset('assets/img/users/user-13.jpg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>Ava Martinez
                                                            </label>
                                                        </li>
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xs rounded-circle me-2"><img src="{{ asset('assets/img/users/user-12.jpg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>Nathan Reed
                                                            </label>
                                                        </li>
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xs rounded-circle me-2"><img src="{{ asset('assets/img/users/user-03.jpg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>Lily Anderson
                                                            </label>
                                                        </li>
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xs rounded-circle me-2"><img src="{{ asset('assets/img/users/user-18.jpg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>Ryan Coleman
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="link-primary text-decoration-underline p-2 d-flex">Load More</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="filter-set-content">
                                            <div class="filter-set-content-head">
                                                <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Tags</a>
                                            </div>
                                            <div class="filter-set-contents accordion-collapse collapse" id="collapseThree" data-bs-parent="#accordionExample">
                                                <div class="filter-content-list bg-light rounded border p-2 shadow mt-2">
                                                    <ul>
                                                        <li>
                                                                <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                Collab
                                                            </label>
                                                        </li>
                                                            <li>
                                                                <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                Promotion
                                                            </label>
                                                        </li>
                                                            <li>
                                                                <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                VIP
                                                            </label>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="filter-set-content">
                                            <div class="filter-set-content-head">
                                                <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">Location</a>
                                            </div>
                                            <div class="filter-set-contents accordion-collapse collapse" id="collapseFive" data-bs-parent="#accordionExample">
                                                <div class="filter-content-list bg-light rounded border p-2 shadow mt-2">
                                                    <div class="mb-1">
                                                        <div class="input-icon-start input-icon position-relative">
                                                            <span class="input-icon-addon fs-12">
                                                                <i class="ti ti-search"></i>
                                                            </span>
                                                            <input type="text" class="form-control form-control-md" placeholder="Search">
                                                        </div>
                                                    </div>
                                                    <ul class="mb-0">
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xss rounded-circle me-1"><img src="{{ asset('assets/img/flags/us.svg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>USA
                                                            </label>
                                                        </li>
                                                            <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xss rounded-circle me-1"><img src="{{ asset('assets/img/flags/ae.svg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>UAE
                                                            </label>
                                                        </li>
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xss rounded-circle me-1"><img src="{{ asset('assets/img/flags/de.svg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>Germany
                                                            </label>
                                                        </li>
                                                        <li class="mb-1">
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                <span class="avatar avatar-xss rounded-circle me-1"><img src="{{ asset('assets/img/flags/fr.svg') }}" class="flex-shrink-0 rounded-circle" alt="img"></span>France
                                                            </label>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>                                             
                                        <div class="filter-set-content">
                                            <div class="filter-set-content-head">
                                                <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">Rating</a>
                                            </div>
                                            <div class="filter-set-contents accordion-collapse collapse" id="collapseOne" data-bs-parent="#accordionExample">
                                                <div class="filter-content-list bg-light rounded border p-2 shadow mt-2">
                                                    <ul>
                                                        <li>
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                    <span class="rating">
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <span class="ms-1">5.0</span>
                                                                </span>
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                    <span class="rating">
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled"></i>
                                                                    <span class="ms-1">4.0</span>
                                                                </span>
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                    <span class="rating">
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled"></i>
                                                                    <i class="ti ti-star-filled"></i>
                                                                    <span class="ms-1">3.0</span>
                                                                </span>
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                    <span class="rating">
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled"></i>
                                                                    <i class="ti ti-star-filled"></i>
                                                                    <i class="ti ti-star-filled"></i>
                                                                    <span class="ms-1">2.0</span>
                                                                </span>
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                    <span class="rating">
                                                                    <i class="ti ti-star-filled text-warning"></i>
                                                                    <i class="ti ti-star-filled"></i>
                                                                    <i class="ti ti-star-filled"></i>
                                                                    <i class="ti ti-star-filled"></i>
                                                                    <i class="ti ti-star-filled"></i>
                                                                    <span class="ms-1">1.0</span>
                                                                </span>
                                                            </label>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>   
                                        <div class="filter-set-content">
                                            <div class="filter-set-content-head">
                                                <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#Status" aria-expanded="false" aria-controls="Status">Status</a>
                                            </div>
                                            <div class="filter-set-contents accordion-collapse collapse" id="Status" data-bs-parent="#accordionExample">
                                                <div class="filter-content-list bg-light rounded border p-2 shadow mt-2">
                                                    <ul>
                                                        <li>
                                                                <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                Active
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label class="dropdown-item px-2 d-flex align-items-center">
                                                                <input class="form-check-input m-0 me-1" type="checkbox">
                                                                Inactive
                                                            </label>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>                                             
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="javascript:void(0);" class="btn btn-outline-light w-100">Reset</a>
                                        <a href="companies-list.html" class="btn btn-primary w-100">Filter</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="input-icon input-icon-start position-relative">
                            <span class="input-icon-addon text-dark"><i class="ti ti-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search">
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">                               
                        <div class="d-flex align-items-center shadow p-1 rounded border view-icons bg-white">
                            <a href="companies-list.html" class="btn btn-sm p-1 border-0 fs-14"><i class="ti ti-list-tree"></i></a>
                            <a href="companies.html" class="flex-shrink-0 btn btn-sm p-1 border-0 ms-1 fs-14 active"><i class="ti ti-grid-dots"></i></a>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus-filled me-1"></i>Add Company</a>
                    </div>
                </div>
                <!-- table header -->
                
                <!-- Company Grid -->
                <div class="row">

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-01.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html"
                                                    class="fw-medium">NovaWave LLC</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>4.2
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="43312c21263137302c2d03263b222e332f266d202c2e">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 875455453</p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>Germany</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-01.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-02.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html" class="fw-medium">BlueSky
                                                    Industries</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>5.0
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="afdcc7ceddc0c1efcad7cec2dfc3ca81ccc0c2">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 989757485</p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>USA</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-02.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-03.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html" class="fw-medium">Summit
                                                    Peak</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>4.5
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="39535c4a4a505a58080a795e54585055175a5654">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 89316-83167
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>India</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-03.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-04.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html" class="fw-medium">Summit
                                                    Peak</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>4.5
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="4f252a3c3c262c2e7e7c0f28222e2623612c2022">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 89316-83167
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>India</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-04.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-05.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html"
                                                    class="fw-medium">RiverStone Ventur</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>4.7
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item " href="#"
                                                data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f89b998a9794ac9097cbb89f95999194d69b9795">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 84295-01629
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>China</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-06.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-06.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html" class="fw-medium">Bright
                                                    Bridge Grp</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>5.0
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item " href="#"
                                                data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="ed898c9a8380889f8e858cad8a808c8481c38e8280">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 79253-01692
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>Martin Lewis</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-07.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-07.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html"
                                                    class="fw-medium">CoastalStar Co.</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>3.1
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item " href="#"
                                                data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="d4a6b5b7bcb1b894b3b9b5bdb8fab7bbb9">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 52804-89153
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>Indonesia
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-08.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-08.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html"
                                                    class="fw-medium">HarborView</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>5.0
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item " href="#"
                                                data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="d6bcb9b8b3babab396b1bbb7bfbaf8b5b9bb">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 60364-91683
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>Cuba</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-09.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-09.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html" class="fw-medium">Golden
                                                    Gate Ltd</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>2.7
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item " href="#"
                                                data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="ea8085848b9e828b84aa8d878b8386c4898587">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 69023-95179
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>Isreal</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-10.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-10.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html" class="fw-medium">Redwood
                                                    Inc</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>3.0
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item " href="#"
                                                data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="3456465b5b5f745359555d581a575b59">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 49815-90142
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>Colombia
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-11.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-03.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html"
                                                    class="fw-medium">SilverHawk</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>3.0
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item " href="#"
                                                data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="76001703111e1718474436130e171b061a135815191b">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 546555455</p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>Canada</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-12.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <div class="card border shadow">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <a href="company-details.html"
                                            class="avatar border rounded-circle flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/img/icons/company-icon-04.svg') }}"
                                                class="w-auto h-auto" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14"><a href="company-details.html"
                                                    class="fw-medium">SummitPeak</a></h6>
                                            <div class="set-star text-default">
                                                <i class="ti ti-star-filled me-1 text-warning"></i>3.0
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon btn btn-icon btn-sm btn-outline-light shadow" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item " href="#"
                                                data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_edit"><i
                                                    class="ti ti-edit text-blue"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete_contact"><i
                                                    class="ti ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href="company-details.html"><i
                                                    class="ti ti-eye text-blue-light"></i> Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <div class="d-flex flex-column mb-0">
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-mail text-dark me-1"></i><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="a8c2cddbdbc1cbc9999be8cdd0c9c5d8c4cd86cbc7c5">[email&#160;protected]</a>
                                        </p>
                                        <p class="text-default d-inline-flex align-items-center mb-2"><i
                                                class="ti ti-phone text-dark me-1"></i>+1 454478787</p>
                                        <p class="text-default d-inline-flex align-items-center"><i
                                                class="ti ti-map-pin-pin text-dark me-1"></i>India</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge badge-tag badge-soft-success me-2">Collab</span>
                                        <span class="badge badge-tag badge-soft-warning">Rated</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center grid-social-links">
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-mail fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-phone-check fs-14"></i></a>
                                        <a href="#"
                                            class="avatar avatar-xs text-dark rounded-circle me-1"><i
                                                class="ti ti-message-circle-share fs-14"></i></a>
                                        <a href="#" class="avatar avatar-xs text-dark rounded-circle"><i
                                                class="ti ti-brand-facebook fs-14"></i></a>
                                    </div>
                                    <div>
                                        <span class="avatar avatar-xs border-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-13.jpg') }}"
                                                class="rounded-circle" alt="img">
                                        </span>
                                    </div>                                    
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- /Company Grid -->

                <div class="load-btn text-center">
                    <a href="javascript:void(0);" class="btn btn-primary"><i class="ti ti-loader me-1"></i> Load More</a>
                </div>

            </div>
            <!-- End Content -->            

            <!-- Start Footer -->
            <footer class="footer d-block d-md-flex justify-content-between text-md-start text-center">
               <p class="mb-md-0 mb-1">Copyright &copy; <script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script type="986b4ccfc065355012d48840-text/javascript">document.write(new Date().getFullYear())</script> <a href="javascript:void(0);" class="link-primary text-decoration-underline">CRMS</a></p>
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

    <!-- Add offcanvas -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
        <div class="offcanvas-header border-bottom">
            <h5 class="mb-0">Add New Company</h5>
            <button type="button"
                class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
                data-bs-dismiss="offcanvas" aria-label="Close">
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="https://crms.dreamstechnologies.com/html/template/companies-list.html">
                <div class="accordion accordion-bordered" id="main_accordion">
                    <!-- Basic Info -->
                    <div class="accordion-item rounded mb-3">
                        <div class="accordion-header">
                            <a href="#"
                                class="accordion-button accordion-custom-button rounded"
                                data-bs-toggle="collapse" data-bs-target="#basic">
                                <span class="avatar avatar-md rounded me-1"><i
                                class="ti ti-user-plus"></i></span>
                                Basic Info
                            </a>
                        </div>
                        <div class="accordion-collapse collapse show" id="basic" data-bs-parent="#main_accordion">
                            <div class="accordion-body border-top">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-xxl border border-dashed me-3 flex-shrink-0">
                                                <div class="position-relative d-flex align-items-center">
                                                    <i class="ti ti-photo text-dark fs-16"></i>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex flex-column align-items-start">
                                                <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                                    <i class="ti ti-file-broken me-1"></i>Upload file
                                                    <input type="file" class="form-control image-sign" multiple="">
                                                </div>
                                                <span>JPG, GIF or PNG. Max size of 800K</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Company Name<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="form-label">Email <span
                                                        class="text-danger ms-1">*</span></label>
                                                <div class="form-check form-switch mb-1">                                                                                                     
                                                    <label class="form-check-label d-flex align-items-center gap-2">
                                                        <span>Email Opt Out</span>   
                                                        <input class="form-check-input form-check-input-sm switchCheckDefault ms-auto" type="checkbox" role="switch" checked>     
                                                    </label>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Phone 1</label>
                                            <input type="text" class="form-control phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Phone 2</label>
                                            <input type="text" class="form-control phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Fax</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Website</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>  
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label class="form-label">Reviews </label>
                                            <div class="input-group w-auto input-group-flat">													
                                                <input type="text" class="form-control">
                                                <span class="input-group-text"><i class="ti ti-star"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Owner</label>
                                            <select class="select2" data-toggle="select2">
                                                <option>Select</option>
                                                <option>Hendry Milner</option>
                                                <option>Guilory Berggren</option>
                                                <option>Jami Carlile</option>
                                                <option>Theresa Nelson</option>
                                                <option>Smith Cooper</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tags </label>
                                            <input class="input-tags form-control border-0 h-100" data-choices data-choices-limit="infinite" data-choices-removeItem type="text" value="Collab, VIP">
                                            <span class="fs-13">Enter value separated by comma</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <label class="form-label">Deals</label>
                                                <a href="#" class="label-add link-primary mb-1" data-bs-toggle="offcanvas"
                                                    data-bs-target="#offcanvas_add_2"><i
                                                        class="ti ti-plus me-1"></i>Add New</a>
                                            </div>
                                            <select class="select2" data-toggle="select2">
                                                <option>Select</option>
                                                <option>Collins</option>
                                                <option>Konopelski</option>
                                                <option>Adams</option>
                                                <option>Schumm</option>
                                                <option>Wisozk</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Source <span
                                                    class="text-danger">*</span></label>
                                            <select class="select2" data-toggle="select2">
                                                <option>Select</option>
                                                <option>Phone Calls</option>
                                                <option>Social Media</option>
                                                <option>Referral Sites</option>
                                                <option>Web Analytics</option>
                                                <option>Previous Purchases</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Industry <span class="text-danger">*</span></label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>Retail Industry</option>
                                                <option>Banking</option>
                                                <option>Hotels</option>
                                                <option>Financial Services</option>
                                                <option>Insurance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Contacts <span class="text-danger">*</span></label>
                                            <select class="multiple-img" multiple="multiple" data-toggle=" multiple">
                                                <option data-image="assets/img/profiles/avatar-19.jpg" selected>Darlee Robertson
                                                </option>
                                                <option data-image="assets/img/users/user-01.jpg">Sharon Roy</option>
                                                <option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis</option>
                                                <option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise</option>
                                                <option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Currency <span class="text-danger">*</span></label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>Dollar</option>
                                                <option>Euro</option>
                                                <option>Pound</option>
                                                <option>Rupee</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Language <span class="text-danger">*</span></label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>English</option>
                                                <option>Arabic</option>
                                                <option>French</option>
                                                <option>German</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-0">
                                            <label class="form-label">Description <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Basic Info -->

                    <!-- Address Info -->
                    <div class="accordion-item border-top rounded mb-3">
                        <div class="accordion-header">
                            <a href="#"
                                class="accordion-button accordion-custom-button rounded"
                                data-bs-toggle="collapse" data-bs-target="#address">
                                <span class="avatar avatar-md rounded me-1"><i
                                        class="ti ti-map-pin-cog"></i></span>
                                Address Info
                            </a>
                        </div>
                        <div class="accordion-collapse collapse" id="address" data-bs-parent="#main_accordion">
                            <div class="accordion-body border-top">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Street Address </label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Country</label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>USA</option>
                                                <option>Canada</option>
                                                <option>Germany</option>
                                                <option>France</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">State / Province </label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>California</option>
                                                <option>New York</option>
                                                <option>Texas</option>
                                                <option>Florida</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 mb-md-0">
                                            <label class="form-label">City </label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>Los Angeles</option>
                                                <option>San Diego</option>
                                                <option>Fresno</option>
                                                <option>San Francisco</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-0">
                                            <label class="form-label">Zipcode </label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Address Info -->

                    <!-- Social Profile -->
                    <div class="accordion-item border-top rounded mb-3">
                        <div class="accordion-header">
                            <a href="#"
                                class="accordion-button accordion-custom-button rounded"
                                data-bs-toggle="collapse" data-bs-target="#social">
                                <span class="avatar avatar-md rounded me-1"><i
                                        class="ti ti-social"></i></span>
                                Social Profile
                            </a>
                        </div>
                        <div class="accordion-collapse collapse" id="social" data-bs-parent="#main_accordion">
                            <div class="accordion-body border-top">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Facebook</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Skype </label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Linkedin </label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Twitter</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 mb-md-0">
                                            <label class="form-label">Whatsapp</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-0">
                                            <label class="form-label">Instagram</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Social Profile -->

                    <!-- Access -->
                    <div class="accordion-item border-top rounded mb-3">
                        <div class="accordion-header">
                            <a href="#"
                                class="accordion-button accordion-custom-button rounded"
                                data-bs-toggle="collapse" data-bs-target="#access-info">
                                <span class="avatar avatar-md rounded me-1"><i
                                        class="ti ti-accessible"></i></span>
                                Access
                            </a>
                        </div>
                        <div class="accordion-collapse collapse" id="access-info" data-bs-parent="#main_accordion">
                            <div class="accordion-body border-top">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-0">
                                            <label class="form-label">Visibility</label>
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="form-check">
                                                    <input type="radio" id="customRadio1" name="customRadio" class="form-check-input">
                                                    <label class="form-check-label" for="customRadio1">Public</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio" id="customRadio2" name="customRadio" class="form-check-input">
                                                    <label class="form-check-label" for="customRadio2">Private</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio" id="customRadio3" name="customRadio" class="form-check-input" checked>
                                                    <label class="form-check-label" for="customRadio3">Select Pepole</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Access -->
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                    <button type="button" class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#create_success">Create New</button>
                </div>
            </form>
        </div>
    </div>
    <!-- /Add offcanvas -->

    <!-- edit offcanvas -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
        <div class="offcanvas-header border-bottom">
            <h5 class="mb-0">Edit Company</h5>
            <button type="button"
                class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
                data-bs-dismiss="offcanvas" aria-label="Close">
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="https://crms.dreamstechnologies.com/html/template/companies-list.html">
                <div class="accordion accordion-bordered" id="main_accordion2">
                    <!-- Basic Info -->
                    <div class="accordion-item rounded mb-3">
                        <div class="accordion-header">
                            <a href="#"
                                class="accordion-button accordion-custom-button rounded"
                                data-bs-toggle="collapse" data-bs-target="#basic2">
                                <span class="avatar avatar-md rounded me-1"><i
                                class="ti ti-user-plus"></i></span>
                                Basic Info
                            </a>
                        </div>
                        <div class="accordion-collapse collapse show" id="basic2" data-bs-parent="#main_accordion2">
                            <div class="accordion-body border-top">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-xxl border border-dashed me-3 flex-shrink-0">
                                                <div class="position-relative d-flex align-items-center">
                                                    <i class="ti ti-photo text-dark fs-16"></i>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex flex-column align-items-start">
                                                <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                                    <i class="ti ti-file-broken me-1"></i>Upload file
                                                    <input type="file" class="form-control image-sign" multiple="">
                                                </div>
                                                <span>JPG, GIF or PNG. Max size of 800K</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Company Name<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="form-label">Email <span
                                                        class="text-danger ms-1">*</span></label>
                                                <div class="form-check form-switch mb-1">                                                                                                     
                                                    <label class="form-check-label d-flex align-items-center gap-2">
                                                        <span>Email Opt Out</span>   
                                                        <input class="form-check-input form-check-input-sm switchCheckDefault ms-auto" type="checkbox" role="switch" checked>     
                                                    </label>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Phone 1</label>
                                            <input type="text" class="form-control phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Phone 2</label>
                                            <input type="text" class="form-control phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Fax</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Website</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>  
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label class="form-label">Reviews </label>
                                            <div class="input-group w-auto input-group-flat">													
                                                <input type="text" class="form-control">
                                                <span class="input-group-text"><i class="ti ti-star"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Owner</label>
                                            <select class="select2" data-toggle="select2">
                                                <option>Select</option>
                                                <option>Hendry Milner</option>
                                                <option>Guilory Berggren</option>
                                                <option>Jami Carlile</option>
                                                <option>Theresa Nelson</option>
                                                <option>Smith Cooper</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tags </label>
                                            <input class="input-tags form-control border-0 h-100" data-choices data-choices-limit="infinite" data-choices-removeItem type="text" value="Collab, VIP">
                                            <span class="fs-13">Enter value separated by comma</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <label class="form-label">Deals</label>
                                                <a href="#" class="label-add link-primary mb-1" data-bs-toggle="offcanvas"
                                                    data-bs-target="#offcanvas_add_2"><i
                                                        class="ti ti-plus me-1"></i>Add New</a>
                                            </div>
                                            <select class="select2" data-toggle="select2">
                                                <option>Select</option>
                                                <option>Collins</option>
                                                <option>Konopelski</option>
                                                <option>Adams</option>
                                                <option>Schumm</option>
                                                <option>Wisozk</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Source <span
                                                    class="text-danger">*</span></label>
                                            <select class="select2" data-toggle="select2">
                                                <option>Select</option>
                                                <option>Phone Calls</option>
                                                <option>Social Media</option>
                                                <option>Referral Sites</option>
                                                <option>Web Analytics</option>
                                                <option>Previous Purchases</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Industry <span class="text-danger">*</span></label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>Retail Industry</option>
                                                <option>Banking</option>
                                                <option>Hotels</option>
                                                <option>Financial Services</option>
                                                <option>Insurance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Contacts <span class="text-danger">*</span></label>
                                            <select class="multiple-img" multiple="multiple" data-toggle=" multiple">
                                                <option data-image="assets/img/profiles/avatar-19.jpg" selected>Darlee Robertson
                                                </option>
                                                <option data-image="assets/img/users/user-01.jpg">Sharon Roy</option>
                                                <option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis</option>
                                                <option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise</option>
                                                <option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Currency <span class="text-danger">*</span></label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>Dollar</option>
                                                <option>Euro</option>
                                                <option>Pound</option>
                                                <option>Rupee</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Language <span class="text-danger">*</span></label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>English</option>
                                                <option>Arabic</option>
                                                <option>French</option>
                                                <option>German</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-0">
                                            <label class="form-label">Description <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Basic Info -->

                    <!-- Address Info -->
                    <div class="accordion-item border-top rounded mb-3">
                        <div class="accordion-header">
                            <a href="#"
                                class="accordion-button accordion-custom-button rounded"
                                data-bs-toggle="collapse" data-bs-target="#address2">
                                <span class="avatar avatar-md rounded me-1"><i
                                        class="ti ti-map-pin-cog"></i></span>
                                Address Info
                            </a>
                        </div>
                        <div class="accordion-collapse collapse" id="address2" data-bs-parent="#main_accordion2">
                            <div class="accordion-body border-top">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Street Address </label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Country</label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>USA</option>
                                                <option>Canada</option>
                                                <option>Germany</option>
                                                <option>France</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">State / Province </label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>California</option>
                                                <option>New York</option>
                                                <option>Texas</option>
                                                <option>Florida</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 mb-md-0">
                                            <label class="form-label">City </label>
                                            <select class="select">
                                                <option>Select</option>
                                                <option>Los Angeles</option>
                                                <option>San Diego</option>
                                                <option>Fresno</option>
                                                <option>San Francisco</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-0">
                                            <label class="form-label">Zipcode </label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Address Info -->

                    <!-- Social Profile -->
                    <div class="accordion-item border-top rounded mb-3">
                        <div class="accordion-header">
                            <a href="#"
                                class="accordion-button accordion-custom-button rounded"
                                data-bs-toggle="collapse" data-bs-target="#social2">
                                <span class="avatar avatar-md rounded me-1"><i
                                        class="ti ti-social"></i></span>
                                Social Profile
                            </a>
                        </div>
                        <div class="accordion-collapse collapse" id="social2" data-bs-parent="#main_accordion2">
                            <div class="accordion-body border-top">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Facebook</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Skype </label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Linkedin </label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Twitter</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 mb-md-0">
                                            <label class="form-label">Whatsapp</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-0">
                                            <label class="form-label">Instagram</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Social Profile -->

                    <!-- Access -->
                    <div class="accordion-item border-top rounded mb-3">
                        <div class="accordion-header">
                            <a href="#"
                                class="accordion-button accordion-custom-button rounded"
                                data-bs-toggle="collapse" data-bs-target="#access-info2">
                                <span class="avatar avatar-md rounded me-1"><i
                                        class="ti ti-accessible"></i></span>
                                Access
                            </a>
                        </div>
                        <div class="accordion-collapse collapse" id="access-info2" data-bs-parent="#main_accordion2">
                            <div class="accordion-body border-top">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-0">
                                            <label class="form-label">Visibility</label>
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="form-check">
                                                    <input type="radio" id="customRadio4" name="customRadio" class="form-check-input">
                                                    <label class="form-check-label" for="customRadio4">Public</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio" id="customRadio5" name="customRadio" class="form-check-input">
                                                    <label class="form-check-label" for="customRadio5">Private</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio" id="customRadio6" name="customRadio" class="form-check-input" checked>
                                                    <label class="form-check-label" for="customRadio6">Select Pepole</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Access -->
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                    <button type="button" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <!-- /edit offcanvas -->

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
            <form action="https://crms.dreamstechnologies.com/html/template/companies-list.html">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Deal Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control">
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
                                <option data-image="assets/img/profiles/avatar-19.jpg" selected>Darlee Robertson
                                </option>
                                <option data-image="assets/img/users/user-01.jpg">Sharon Roy</option>
                                <option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis</option>
                                <option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise</option>
                                <option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Project <span class="text-danger">*</span></label>
                            <input class="input-tags form-control border-0 h-100" data-choices data-choices-limit="infinite" data-choices-removeItem type="text" value="Devops Design, MargrateDesign">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <div class="input-group w-auto input-group-flat">
                                <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d M, Y">
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
                                <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d M, Y">
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
                                <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d M, Y">
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
                            <input class="input-tags form-control border-0 h-100" data-choices data-choices-limit="infinite" data-choices-removeItem type="text" value="Collab, Rated">
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
                        <span class="avatar avatar-xl badge-soft-success border-0 text-success rounded-circle"><i class="ti ti-building-community fs-24"></i></span>
                    </div>
                    <h5 class="mb-1">Company Created Successfully!!!</h5>
                    <p class="mb-3">View the details of company, created</p>
                    <div class="d-flex justify-content-center">
                        <a href="#" class="btn btn-light position-relative z-1 me-2 w-100" data-bs-dismiss="modal">Cancel</a>
                        <a href="company-details.html" class="btn btn-primary position-relative z-1 w-100">View Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- success modal -->

    <!-- delete modal -->
    <div class="modal fade" id="delete_contact">
        <div class="modal-dialog modal-dialog-centered modal-sm rounded-0">
            <div class="modal-content rounded-0">
                <div class="modal-body p-4 text-center position-relative">
                    <div class="mb-3 position-relative z-1">
                        <span class="avatar avatar-xl badge-soft-danger border-0 text-danger rounded-circle"><i class="ti ti-trash fs-24"></i></span>
                    </div>
                    <h5 class="mb-1">Delete Confirmation</h5>
                    <p class="mb-3">Are you sure you want to remove company you selected.</p>
                    <div class="d-flex justify-content-center">
                        <a href="#" class="btn btn-light position-relative z-1 me-2 w-100" data-bs-dismiss="modal">Cancel</a>
                        <a href="#" class="btn btn-primary position-relative z-1 w-100" data-bs-dismiss="modal">Yes, Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- delete modal -->


    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>  

    <!-- Daterangepikcer JS -->
	<script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
	<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Choices Js -->	
    <script src="{{ asset('assets/plugins/choices.js/public/assets/scripts/choices.min.js') }}" type="text/javascript"></script>

    <!-- Mobile Input -->
    <script src="{{ asset('assets/plugins/intltelinput/js/intlTelInput.js') }}" type="text/javascript"></script>

    <!-- Quill JS -->
    <script src="{{ asset('assets/plugins/quill/quill.min.js') }}" type="text/javascript"></script>

	<!-- Simplebar JS -->
	<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Main JS -->
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

<script src="../../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="986b4ccfc065355012d48840-|49" defer></script><script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"rayId":"967b3188f8ed22a8","version":"2025.7.0","serverTiming":{"name":{"cfExtPri":true,"cfEdge":true,"cfOrigin":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"token":"3ca157e612a14eccbb30cf6db6691c29","b":1}' crossorigin="anonymous"></script>
</body>



</html>