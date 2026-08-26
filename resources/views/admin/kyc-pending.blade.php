<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - KYC Pending</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
        .action-cell .d-flex {
            display: inline-flex;
            box-sizing: border-box;
            align-items: center;
            justify-content: start;
            gap: 4px;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        .action-cell .d-flex form {
            display: contents;
        }
        .btn-approve,
        .btn-reject,
        .btn-profile,
        .btn-toggle-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            min-width: 32px;
            min-height: 32px;
            padding: 0 !important;
            line-height: 1;
            flex: 0 0 auto;
        }
        .action-cell .d-flex .ti {
            font-size: 18px;
            line-height: 1;
        }
        .btn-export {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #0369a1;
            font-size: 13px;
            padding: 6px 16px;
            border-radius: 4px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-export:hover {
            background-color: #0369a1;
            color: #fff;
        }
        .btn-profile {
            background-color: #f3e8ff;
            color: #6b21a8;
            border: 1px solid #6b21a8;
            font-size: 13px;
            padding: 4px 12px;
            border-radius: 4px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-profile:hover {
            background-color: #6b21a8;
            color: #fff;
        }
        .btn-toggle-status {
            font-size: 13px;
            padding: 4px 12px;
            border-radius: 4px;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-toggle-status.is-active {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #b91c1c;
        }
        .btn-toggle-status.is-active:hover {
            background-color: #b91c1c;
            color: #fff;
        }
        .btn-toggle-status.is-inactive {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #15803d;
        }
        .btn-toggle-status.is-inactive:hover {
            background-color: #15803d;
            color: #fff;
        }
        .status-pill {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-pill.active {
            background: #dcfce7;
            color: #15803d;
        }
        .status-pill.inactive {
            background: #fee2e2;
            color: #b91c1c;
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
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-1">Pending Customer KYC</h5>
                                    <p class="card-text mb-0">Review and manage pending KYC submissions from customers</p>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.kyc-export', ['status' => 'pending']) }}" class="btn-export" title="Export pending KYC records to Excel">
                                        <i class="ti ti-file-spreadsheet me-1"></i>Export Excel
                                    </a>
                                    <a href="{{ route('admin.kyc-export', ['status' => 'all']) }}" class="btn-export" title="Export all KYC records to Excel">
                                        <i class="ti ti-file-export me-1"></i>Export All
                                    </a>
                                </div>
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
                                                <th>Customer</th>
                                                <th>KYC Type</th>
                                                <th>Organization</th>
                                                <th>Submitted At</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kycDetails as $key => $kyc)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td class="customer-name-cell">
                                                    <strong>{{ trim(($kyc->customer->first_name ?? '') . ' ' . ($kyc->customer->last_name ?? '')) ?: ($kyc->customer->name ?? 'Customer') }}</strong>
                                                    <div class="small">
                                                        <a href="mailto:{{ $kyc->customer->email ?? '' }}" class="text-decoration-none">{{ $kyc->customer->email ?? '—' }}</a>
                                                    </div>
                                                    <div class="small text-muted">{{ $kyc->customer->phone_number ?? '—' }}</div>
                                                </td>
                                                <td>
                                                    @if(($kyc->kyc_type ?? 'personal') === 'business')
                                                        <span class="badge bg-info text-white">Business (CSB-V)</span>
                                                    @else
                                                        <span class="badge bg-primary">Personal (CSB-IV)</span>
                                                    @endif
                                                </td>
                                                <td class="org-cell">
                                                    {{ $kyc->organization_name ?? '—' }}
                                                    <div class="small text-muted">{{ $kyc->gst_number ?? '—' }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge-pending">
                                                        {{ $kyc->created_at->format('d M Y, h:i A') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $isActive = isset($kyc->customer->status) ? (bool) $kyc->customer->status : true;
                                                    @endphp
                                                    @if($isActive)
                                                        <span class="status-pill active">Active</span>
                                                    @else
                                                        <span class="status-pill inactive">Deactivated</span>
                                                    @endif
                                                </td>
                                                <td class="action-cell">
                                                    <div class="d-flex flex-nowrap gap-1">
                                                        @php
                                                            $customerId = $kyc->customer->id ?? null;
                                                            $isActive = isset($kyc->customer->status) ? (bool) $kyc->customer->status : true;
                                                        @endphp
                                                        @if($customerId)
                                                            <a href="{{ route('admin.customer-profile', $customerId) }}" class="btn-profile" title="View customer profile, login credentials & full KYC">
                                                                <i class="ti ti-user"></i>
                                                            </a>
                                                        @endif
                                                        <form action="{{ route('admin.kyc-pending.approve', $kyc->id) }}" method="POST" class="d-inline approve-form">
                                                            @csrf
                                                            <button type="submit" class="btn-approve" title="Approve KYC">
                                                                <i class="ti ti-circle-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.kyc-pending.reject', $kyc->id) }}" method="POST" class="d-inline reject-form">
                                                            @csrf
                                                            <input type="hidden" name="reject_remark" class="reject-remark-input" value="">
                                                            <button type="submit" class="btn-reject" title="Reject KYC">
                                                                <i class="ti ti-circle-x"></i>
                                                            </button>
                                                        </form>
                                                        @if($customerId)
                                                            <form action="{{ route('admin.customer.toggle-status', $customerId) }}" method="POST" class="d-inline toggle-status-form">
                                                                @csrf
                                                                @if($isActive)
                                                                    <button type="submit" class="btn-toggle-status is-active" title="Deactivate this customer account (blocks login)">
                                                                        <i class="ti ti-user-off"></i>
                                                                    </button>
                                                                @else
                                                                    <button type="submit" class="btn-toggle-status is-inactive" title="Activate this customer account (allows login)">
                                                                        <i class="ti ti-user-check"></i>
                                                                    </button>
                                                                @endif
                                                            </form>
                                                        @endif
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

<!-- Reject KYC Modal -->
    <div class="modal fade" id="rejectKycModal" tabindex="-1" aria-labelledby="rejectKycModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectKycModalLabel">
                        <i class="ti ti-alert-triangle me-2 text-danger"></i>Reject KYC
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        Are you sure you want to reject KYC for <strong id="rk-customer-name">—</strong>?
                    </p>
                    <div class="mb-2">
                        <label for="rejectRemarkInput" class="form-label">Remark <span class="text-danger">*</span></label>
                        <textarea id="rejectRemarkInput" class="form-control" rows="3" maxlength="1000" required
                            placeholder="Enter the reason for rejection..."></textarea>
                        <div class="text-danger small mt-1 reject-remark-error d-none">Please enter a remark before rejecting.</div>
                        @if ($errors->has('reject_remark'))
                            <div class="text-danger small mt-1">{{ $errors->first('reject_remark') }}</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRejectBtn">
                        <i class="ti ti-circle-x me-1"></i>Reject
                    </button>
                </div>
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
                    { orderable: false, targets: [5, 6] }
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

            // SweetAlert2 confirmation for Activate/Deactivate account
            $('.toggle-status-form').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var btn = $(form).find('button[type="submit"]');
                var isDeactivate = btn.hasClass('is-active');
                var title = isDeactivate ? 'Deactivate Account?' : 'Activate Account?';
                var text = isDeactivate
                    ? "This customer will no longer be able to log in. You can reactivate the account later."
                    : "This will allow the customer to log in again.";
                var icon = isDeactivate ? 'warning' : 'question';
                var confirmColor = isDeactivate ? '#b91c1c' : '#15803d';
                var confirmText = isDeactivate ? 'Yes, Deactivate' : 'Yes, Activate';
                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // ===== Reject KYC with remark: open the remark modal, then submit on confirm =====
            var rejectFormToSubmit = null;
            $('.reject-form').on('submit', function(e) {
                e.preventDefault();
                rejectFormToSubmit = this;
                $('#rejectRemarkInput').val('');
                $('#rk-customer-name').text($(this).closest('tr').find('.customer-name-cell strong').text().trim() || 'Customer');
                $('#rejectKycModal').modal('show');
            });

            $('#confirmRejectBtn').on('click', function() {
                if (!rejectFormToSubmit) return;
                var form = rejectFormToSubmit;
                var remark = $('#rejectRemarkInput').val().trim();
                if (!remark) {
                    $('#rejectRemarkInput').addClass('is-invalid');
                    $('.reject-remark-error').removeClass('d-none');
                    return;
                }
                $('#rejectRemarkInput').removeClass('is-invalid');
                $('.reject-remark-error').addClass('d-none');
                $(form).find('.reject-remark-input').val(remark);
                $('#rejectKycModal').modal('hide');
                rejectFormToSubmit = null;
                form.submit();
            });
        });
    </script>