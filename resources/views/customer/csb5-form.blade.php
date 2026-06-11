<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from crms.dreamstechnologies.com/html/template/dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 31 Jul 2025 06:57:23 GMT -->

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard | CRMS - Advanced Bootstrap 5 Admin Template for Customer Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <script src="{{ asset('assets/js/theme-script.js') }}"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/css/dataTables.bootstrap5.min.css') }}">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">

    <!-- Tabler Icon CSS -->
    <!-- <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}"> -->
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/plugins/tabler-icons/tabler-icons.min.css">


    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
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
            <div class="content pb-0">


                <!-- CSB5 Form Custom CSS -->
                <link rel="stylesheet" href="{{ asset('css/csb5-form.css') }}">

                <!-- card start -->


                <div class="form-wrapper">
                    <div class="kyc-card">
                        <div class="form-header">
                            <h2>Complete <span class="gradient-text">CSB V Onboarding</span></h2>
                            <p>Provide details for the digital agreement.</p>
                        </div>

                        <form id="csbvForm" action="{{ route('customer.csb5-form.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <!-- Tax Choice -->
                            <div class="mb-4">
                                <div class="form-check d-flex align-items-center mb-3">
                                    <input class="form-check-input" type="checkbox" checked id="csbvToggle"
                                        name="is_csb_v" value="1">
                                    <label class="form-check-label fw-bold" for="csbvToggle">
                                        CSB V <span class="text-muted fw-normal ms-2" style="font-size: 13px;">(For Non
                                            Gifts and Non Samples)</span>
                                    </label>
                                </div>
                            </div>

                            <div class="section-title-alt">Tell us about your Tax type?</div>
                            <div class="d-flex gap-4 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked id="gstType" name="is_gst"
                                        value="1">
                                    <label class="form-check-label fw-bold" for="gstType">GST</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="lutType" name="is_lut"
                                        value="1">
                                    <label class="form-check-label fw-bold" for="lutType">LUT (Against Bond or
                                        UT)</label>
                                </div>
                            </div>

                            <div class="section-title-alt">Export Codes</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="section-label">AD Code</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom" placeholder="Enter AD Code *"
                                            name="ad_code" required>
                                        <i class="fas fa-barcode"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="section-label">IEC Number</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom" placeholder="Enter IEC *"
                                            name="iec_number" required>
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title-alt">Bank Details</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="section-label">Bank Account Number</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom"
                                            placeholder="Enter Bank Account Number *" name="bank_account_number"
                                            required>
                                        <i class="fas fa-university"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title-alt">Document List</div>
                            <!-- Document Upload Item -->
                            <div class="doc-item" id="lutDocContainer">
                                <div class="doc-meta">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <span class="doc-name">LUT</span>
                                            <i class="fas fa-info-circle info-circle ms-2"
                                                title="Letter of Undertaking"></i>
                                        </div>
                                        <div id="fileInfo" class="file-status">Selected: <span
                                                id="fileNameDisplay">file.pdf</span></div>
                                    </div>
                                </div>
                                <div class="text-end d-flex align-items-center">
                                    <input type="file" id="lutFileInput" name="lut_document" style="display: none;"
                                        accept=".pdf">
                                    <button type="button" id="uploadBtn" class="link-alt border-0 bg-transparent">
                                        <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                    </button>
                                    <span id="removeFile" class="text-danger-alt" style="display: none;"><i
                                            class="fas fa-trash-alt"></i> Remove</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center justify-content-between mt-5">
                                <!-- <button type="button" class="btn-back">BACK</button> -->
                                <div class="d-flex align-items-center gap-4">
                                    <a href="#" class="link-alt">Continue with CSBIV</a>
                                    <button type="submit" class="btn-gradient">CONTINUE</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- CSB5 Form Custom JS -->
                <script src="{{ asset('js/csb5-form.js') }}"></script>


            </div>
            <!-- End Content -->

            <!-- Start Footer -->
            <footer class="footer d-block d-md-flex justify-content-between text-md-start text-center">
                <p class="mb-md-0 mb-1">Copyright &copy; 2026 <a href="javascript:void(0);" class="">United Courier
                        worldwide</a></p>
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


    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Daterangepikcer JS -->
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <!-- Apexchart JS -->
    <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('assets/plugins/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/peity/chart-data.js') }}"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>

<!-- Mirrored from crms.dreamstechnologies.com/html/template/dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 31 Jul 2025 06:57:26 GMT -->

</html>