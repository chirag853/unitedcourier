<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - KYC Pending</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Streamline your business with our advanced CRM template. Easily integrate and customize to manage sales, support, and customer interactions efficiently. Perfect for any business size">
    <meta name="keywords" content="Advanced CRM template, customer relationship management, business CRM, sales optimization, customer support software, CRM integration, customizable CRM, business tools, enterprise CRM solutions">
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

    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .table td,
        .table th {
            vertical-align: middle;
            white-space: normal;
            word-wrap: break-word;
        }
        .org-cell {
            max-width: 200px;
        }
        .badge-pending {
            background-color: #fff3e0;
            color: #e65100;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
        }
        .btn-approve {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #2e7d32;
            font-size: 13px;
            padding: 4px 16px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .btn-approve:hover {
            background-color: #2e7d32;
            color: #fff;
        }
        .btn-reject {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #c62828;
            font-size: 13px;
            padding: 4px 16px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .btn-reject:hover {
            background-color: #c62828;
            color: #fff;
        }
        .action-cell {
            min-width: 280px;
        }
        .btn-view-kyc {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #1565c0;
            font-size: 13px;
            padding: 4px 12px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .btn-view-kyc:hover {
            background-color: #1565c0;
            color: #fff;
        }
        .btn-recharge {
            background-color: #fff8e1;
            color: #f57f17;
            border: 1px solid #f57f17;
            font-size: 13px;
            padding: 4px 12px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .btn-recharge:hover {
            background-color: #f57f17;
            color: #fff;
        }
        /* View KYC Modal document styling */
        #viewKycModal .modal-body {
            max-height: 75vh;
            overflow-y: auto;
        }
        .kyc-doc-wrapper {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 32px;
            color: #1e293b;
            line-height: 1.7;
            font-size: 14px;
        }
        .kyc-doc-wrapper h1 {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
            color: #0f172a;
        }
        .kyc-doc-wrapper h2 {
            font-size: 17px;
            font-weight: 700;
            margin-top: 22px;
            margin-bottom: 8px;
            color: #1e3a8a;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .kyc-doc-wrapper h3 {
            font-size: 15px;
            font-weight: 700;
            margin-top: 16px;
            color: #334155;
        }
        .kyc-doc-wrapper p {
            margin-bottom: 10px;
            text-align: justify;
        }
        .kyc-doc-wrapper .subhead-company {
            display: block;
            text-align: center;
            font-size: 13px;
            margin-bottom: 18px;
            color: #475569;
        }
        .kyc-doc-wrapper .underline-title {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 4px;
        }
        .kyc-doc-wrapper .glossary-item {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }
        .kyc-doc-wrapper .glossary-term {
            font-weight: 700;
            color: #1e3a8a;
            min-width: 180px;
        }
        .kyc-doc-wrapper ul {
            padding-left: 22px;
            margin-bottom: 10px;
        }
        .kyc-doc-wrapper ul li {
            margin-bottom: 4px;
        }
        .kyc-doc-wrapper .consolidated-note {
            background: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 12px 16px;
            border-radius: 6px;
            margin-top: 18px;
        }
        .kyc-doc-wrapper .signature-preview {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        .kyc-doc-wrapper .signature-preview img {
            max-height: 90px;
            max-width: 280px;
            object-fit: contain;
        }
        .kyc-doc-wrapper .signature-placeholder {
            min-height: 80px;
            min-width: 220px;
            max-width: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 12px;
            background: #fff;
            color: #94a3b8;
            font-size: 13px;
        }
        .kyc-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .kyc-info-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
        }
        .kyc-info-item .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 2px;
        }
        .kyc-info-item .value {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            word-break: break-word;
        }
        .status-verified {
            color: #16a34a;
            font-weight: 600;
        }
        .status-pending {
            color: #d97706;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            margin-left: 6px;
            font-size: 12px;
            white-space: nowrap;
        }
        .kyc-info-item .value .status-badge {
            vertical-align: middle;
        }
        #viewKycModal .modal-body {
            max-height: 75vh;
            overflow-y: auto;
        }
        .kyc-doc-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 24px 28px;
            color: #1e293b;
            line-height: 1.7;
            font-size: 14px;
        }
        .kyc-doc-wrapper h1 {
            font-size: 1.6rem;
            text-align: center;
            margin-bottom: 0.25rem;
        }
        .kyc-doc-wrapper h2 {
            font-size: 1.15rem;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            color: #1e3a8a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .kyc-doc-wrapper h3 {
            font-size: 1rem;
            margin-top: 1rem;
            margin-bottom: 0.4rem;
            color: #334155;
        }
        .kyc-doc-wrapper p {
            margin-bottom: 0.75rem;
            text-align: justify;
        }
        .kyc-doc-wrapper .subhead-company {
            display: block;
            text-align: center;
            margin-bottom: 1rem;
            color: #475569;
        }
        .kyc-doc-wrapper .underline-title {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 4px;
        }
        .kyc-doc-wrapper .glossary-item {
            margin-bottom: 0.5rem;
        }
        .kyc-doc-wrapper .glossary-term {
            font-weight: 700;
            color: #1e3a8a;
        }
        .kyc-doc-wrapper ul {
            padding-left: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .kyc-doc-wrapper .consolidated-note {
            background: #f1f5f9;
            border-left: 4px solid #1e3a8a;
            padding: 12px 16px;
            border-radius: 6px;
            margin-top: 1rem;
        }
        .kyc-doc-wrapper .signature-preview {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        .kyc-doc-wrapper .signature-placeholder {
            min-height: 80px;
            min-width: 220px;
            max-width: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 12px;
            background: #fff;
        }
    </style>
</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        @include('admin.partials.header')

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

        @include('admin.partials.sidebar')

        <!-- ========================
            Start Page Content
        ========================= -->
         
        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content pb-0">

                <!-- Page Header -->
                <!-- <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">Pending Customer (KYC)</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>                 -->
                <!-- End Page Header -->

                <!-- KYC Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Pending Customer KYC</h5>
                                <p class="card-text">Review and manage pending KYC submissions from customers</p>
                            </div>
                            <div class="card-body">
                                @if ($message = Session::get('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="ti ti-circle-check me-2"></i>
                                        {{ $message }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-hover" id="kycPendingTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Customer Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Organization</th>
                                                <th>GST Number</th>
                                                <th>Submitted At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kycDetails as $key => $kyc)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <strong>{{ $kyc->customer->first_name ?? '' }} {{ $kyc->customer->last_name ?? '' }}</strong>
                                                </td>
                                                <td><a href="mailto:{{ $kyc->customer->email ?? '' }}">{{ $kyc->customer->email ?? '—' }}</a></td>
                                                <td>{{ $kyc->customer->phone_number ?? '—' }}</td>
                                                <td class="org-cell">{{ $kyc->organization_name ?? '—' }}</td>
                                                <td>{{ $kyc->gst_number ?? '—' }}</td>
                                                <td>
                                                    <span class="badge-pending">
                                                        {{ $kyc->created_at->format('d M Y, h:i A') }}
                                                    </span>
                                                </td>
                                                <td class="action-cell">
                                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                                        <button type="button" class="btn-view-kyc"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewKycModal"
                                                            data-kyc-id="{{ $kyc->id }}"
                                                            data-customer-name="{{ $kyc->customer->first_name ?? '' }} {{ $kyc->customer->last_name ?? '' }}"
                                                            data-customer-email="{{ $kyc->customer->email ?? '—' }}"
                                                            data-customer-phone="{{ $kyc->customer->phone_number ?? '—' }}"
                                                            data-organization="{{ $kyc->organization_name ?? '—' }}"
                                                            data-gst="{{ $kyc->gst_number ?? '—' }}"
                                                            data-gst-verified="{{ $kyc->gst_verified ? '1' : '0' }}"
                                                            data-aadhar="{{ $kyc->aadhar_number ?? '—' }}"
                                                            data-aadhar-verified="{{ $kyc->aadhar_verified ? '1' : '0' }}"
                                                            data-signatory="{{ $kyc->authorized_signatory ?? '—' }}"
                                                            data-signature="{{ $kyc->signature ?? '' }}"
                                                            data-otp-verified="{{ $kyc->otp_verified ? '1' : '0' }}"
                                                            data-terms-accepted="{{ $kyc->terms_accepted ? '1' : '0' }}"
                                                            data-submitted="{{ $kyc->created_at ? $kyc->created_at->format('d M Y, h:i A') : '—' }}"
                                                            title="View KYC Details">
                                                            <i class="ti ti-eye me-1"></i>View KYC
                                                        </button>
                                                        <button type="button" class="btn-recharge"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rechargeWalletModal"
                                                            data-customer-id="{{ $kyc->customer->id ?? '' }}"
                                                            data-customer-name="{{ $kyc->customer->first_name ?? '' }} {{ $kyc->customer->last_name ?? '' }}"
                                                            title="Recharge Wallet">
                                                            <i class="ti ti-wallet me-1"></i>Recharge
                                                        </button>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <form action="{{ route('admin.kyc-pending.approve', $kyc->id) }}" method="POST" class="d-inline approve-form">
                                                            @csrf
                                                            <button type="submit" class="btn-approve" title="Approve KYC">
                                                                <i class="ti ti-circle-check me-1"></i>Approve
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.kyc-pending.reject', $kyc->id) }}" method="POST" class="d-inline reject-form">
                                                            @csrf
                                                            <button type="submit" class="btn-reject" title="Reject KYC">
                                                                <i class="ti ti-circle-x me-1"></i>Reject
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End Content -->


        </div>
        <!-- End Page Wrapper -->

    </div>
    <!-- End Wrapper -->

    <!-- View KYC Modal -->
    <div class="modal fade" id="viewKycModal" tabindex="-1" aria-labelledby="viewKycModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewKycModalLabel">
                        <i class="ti ti-id-badge-2 me-2"></i>KYC Details — <span id="vk-modal-title-name"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- KYC Information Grid -->
                    <div class="kyc-info-grid">
                        <div class="kyc-info-item">
                            <span class="label">Customer Name</span>
                            <span class="value" id="vk-customer-name">—</span>
                        </div>
                        <div class="kyc-info-item">
                            <span class="label">Email</span>
                            <span class="value" id="vk-customer-email">—</span>
                        </div>
                        <div class="kyc-info-item">
                            <span class="label">Phone</span>
                            <span class="value" id="vk-customer-phone">—</span>
                        </div>
                        <div class="kyc-info-item">
                            <span class="label">Organization</span>
                            <span class="value" id="vk-organization">—</span>
                        </div>
                        <div class="kyc-info-item">
                            <span class="label">GST Number</span>
                            <span class="value">
                                <span id="vk-gst">—</span>
                                <span class="status-badge" id="vk-gst-status"></span>
                            </span>
                        </div>
                        <div class="kyc-info-item">
                            <span class="label">Aadhar Number</span>
                            <span class="value">
                                <span id="vk-aadhar">—</span>
                                <span class="status-badge" id="vk-aadhar-status"></span>
                            </span>
                        </div>
                        <div class="kyc-info-item">
                            <span class="label">Authorized Signatory</span>
                            <span class="value" id="vk-signatory">—</span>
                        </div>
                        <div class="kyc-info-item">
                            <span class="label">OTP Verified</span>
                            <span class="value"><span class="status-badge" id="vk-otp-status"></span></span>
                        </div>
                        <div class="kyc-info-item">
                            <span class="label">Terms Accepted</span>
                            <span class="value"><span class="status-badge" id="vk-terms-status"></span></span>
                        </div>
                        <div class="kyc-info-item">
                            <span class="label">Submitted On</span>
                            <span class="value" id="vk-submitted">—</span>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Terms & Conditions Document -->
                    <h4 class="mb-3"><i class="ti ti-file-text me-2"></i>Terms & Conditions Document</h4>
                    <div class="kyc-doc-wrapper">
                        @include('customer.partials.terms-document')
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recharge Wallet Modal -->
    <div class="modal fade" id="rechargeWalletModal" tabindex="-1" aria-labelledby="rechargeWalletModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rechargeWalletModalLabel">
                        <i class="ti ti-wallet me-2"></i>Recharge Wallet
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rechargeWalletForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">Recharge wallet for: <strong id="rw-customer-name">—</strong></p>
                        <div class="mb-3">
                            <label for="rechargeAmount" class="form-label">Amount (₹)</label>
                            <input type="number" class="form-control" id="rechargeAmount" name="amount" min="1" step="0.01" placeholder="Enter amount" required>
                            <div class="form-text">Enter the amount to add to the customer's wallet.</div>
                        </div>
                        <div class="alert alert-info py-2 small mb-0" id="rw-result" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="rw-submit-btn">
                            <i class="ti ti-plus me-1"></i>Recharge
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('assets/plugins/slimscroll/slimscroll.min.js') }}" type="text/javascript"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Daterangepicker JS -->
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('#kycPendingTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25,
                language: {
                    search: "Search KYC:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    emptyTable: "No pending KYC submissions found. All submissions have been reviewed.",
                },
                columnDefs: [
                    { orderable: false, targets: 7 }
                ]
            });

            // SweetAlert2 confirmation for Approve
            $('.approve-form').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: 'Approve KYC?',
                    text: "Are you sure you want to approve this KYC submission?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2e7d32',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Approve',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // SweetAlert2 confirmation for Reject
            $('.reject-form').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: 'Reject KYC?',
                    text: "Are you sure you want to reject this KYC submission?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#c62828',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Reject',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // ===== View KYC Modal — populate from data attributes =====
            $('#viewKycModal').on('shown.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);

                var custName = button.attr('data-customer-name') || '—';
                modal.find('#vk-modal-title-name').text(custName);
                modal.find('#vk-customer-name').text(custName);
                modal.find('#vk-customer-email').text(button.attr('data-customer-email') || '—');
                modal.find('#vk-customer-phone').text(button.attr('data-customer-phone') || '—');
                modal.find('#vk-organization').text(button.attr('data-organization') || '—');
                modal.find('#vk-gst').text(button.attr('data-gst') || '—');
                modal.find('#vk-aadhar').text(button.attr('data-aadhar') || '—');
                modal.find('#vk-signatory').text(button.attr('data-signatory') || '—');
                modal.find('#vk-submitted').text(button.attr('data-submitted') || '—');

                function statusBadge(verified, yesText, noText) {
                    return verified
                        ? '<span class="status-verified"><i class="ti ti-circle-check"></i> ' + yesText + '</span>'
                        : '<span class="status-pending"><i class="ti ti-clock"></i> ' + noText + '</span>';
                }
                modal.find('#vk-gst-status').html(statusBadge(button.attr('data-gst-verified') === '1', 'Verified', 'Pending'));
                modal.find('#vk-aadhar-status').html(statusBadge(button.attr('data-aadhar-verified') === '1', 'Verified', 'Pending'));
                modal.find('#vk-otp-status').html(statusBadge(button.attr('data-otp-verified') === '1', 'Verified', 'Pending'));
                modal.find('#vk-terms-status').html(statusBadge(button.attr('data-terms-accepted') === '1', 'Accepted', 'Not Accepted'));

                // Signature preview (base64 data URI)
                var signature = button.attr('data-signature');
                var img = modal.find('#billSignatureImg');
                var placeholder = modal.find('#billSignaturePlaceholder');
                if (signature && signature.length > 10) {
                    img.attr('src', signature).css('display', 'block');
                    placeholder.hide();
                } else {
                    img.hide();
                    placeholder.show();
                }
            });

            // ===== Recharge Wallet Modal — set customer info =====
            var rechargeCustomerId = null;
            var rechargeUrlTemplate = '{{ route("admin.customer.recharge-wallet", ["id" => "__ID__"]) }}';
            $('#rechargeWalletModal').on('shown.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                rechargeCustomerId = button.attr('data-customer-id');
                modal.find('#rw-customer-name').text(button.attr('data-customer-name') || '—');
                modal.find('#rechargeAmount').val('');
                modal.find('#rw-result').hide().removeClass('alert-success alert-danger').addClass('alert-info').text('');
                modal.find('#rw-submit-btn').prop('disabled', false).html('<i class="ti ti-plus me-1"></i>Recharge');
            });

            // ===== Recharge Wallet Form Submission (AJAX) =====
            $('#rechargeWalletForm').on('submit', function(e) {
                e.preventDefault();
                var amount = $('#rechargeAmount').val();
                var resultDiv = $('#rw-result');
                var submitBtn = $('#rw-submit-btn');

                if (!amount || parseFloat(amount) < 1) {
                    resultDiv.removeClass('alert-info alert-success').addClass('alert-danger')
                        .text('Please enter a valid amount (minimum ₹1).').show();
                    return;
                }

                if (!rechargeCustomerId) {
                    resultDiv.removeClass('alert-info alert-success').addClass('alert-danger')
                        .text('Customer ID not found. Please close and try again.').show();
                    return;
                }

                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Processing...');
                resultDiv.hide();

                var url = rechargeUrlTemplate.replace('__ID__', rechargeCustomerId);
                var token = $('meta[name="csrf-token"]').attr('content');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ amount: amount })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        var html = '<i class="ti ti-circle-check me-1"></i>' + data.message;
                        if (data.new_balance !== undefined) {
                            html += '<br><strong>New Balance: ₹' + parseFloat(data.new_balance).toFixed(2) + '</strong>';
                        }
                        resultDiv.removeClass('alert-info alert-danger').addClass('alert-success').html(html).show();
                        submitBtn.prop('disabled', true).html('<i class="ti ti-check me-1"></i>Done');
                        $('#rechargeAmount').val('');
                    } else {
                        resultDiv.removeClass('alert-info alert-success').addClass('alert-danger')
                            .text(data.message || 'An error occurred. Please try again.').show();
                        submitBtn.prop('disabled', false).html('<i class="ti ti-plus me-1"></i>Recharge');
                    }
                })
                .catch(function(error) {
                    resultDiv.removeClass('alert-info alert-success').addClass('alert-danger')
                        .text('Network error: ' + error.message).show();
                    submitBtn.prop('disabled', false).html('<i class="ti ti-plus me-1"></i>Recharge');
                });
            });
        });
    </script>

</body>
</html>