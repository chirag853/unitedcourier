<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - KYC Approved</title>
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
        .badge-approved {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
        }
        .login-id-cell {
            font-family: monospace;
            font-size: 13px;
            color: #1d4ed8;
        }
        .login-id-cell .copy-btn {
            cursor: pointer;
            color: #64748b;
            margin-left: 6px;
            font-size: 12px;
        }
        .login-id-cell .copy-btn:hover {
            color: #1d4ed8;
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
        /* Shipment access toggle (independent from account status) */
        .btn-toggle-shipment {
            font-size: 13px;
            padding: 4px 12px;
            border-radius: 4px;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-toggle-shipment.is-enabled {
            background-color: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #1d4ed8;
        }
        .btn-toggle-shipment.is-enabled:hover {
            background-color: #1d4ed8;
            color: #fff;
        }
        .btn-toggle-shipment.is-disabled {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #b45309;
        }
        .btn-toggle-shipment.is-disabled:hover {
            background-color: #b45309;
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
        .action-cell {
            min-width: 200px;
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

                <!-- KYC Approved Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-1">Approved Customer KYC</h5>
                                    <p class="card-text mb-0">View all approved KYC submissions from customers</p>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.kyc-export', ['status' => 'approved']) }}" class="btn-export" title="Export approved KYC records to Excel">
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
                                    <table class="table table-hover" id="kycApprovedTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Customer Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>KYC Type</th>
                                                <th>Organization</th>
                                                <th>GST Number</th>
                                                <th>Login ID</th>
                                                <th>Password</th>
                                                <th>Approved At</th>
                                                <th>Account Status</th>
                                                <th>Shipment Access</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($approvedKycDetails as $key => $kyc)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <strong>{{ $kyc->customer->first_name ?? '' }} {{ $kyc->customer->last_name ?? '' }}</strong>
                                                </td>
                                                <td><a href="mailto:{{ $kyc->customer->email ?? '' }}">{{ $kyc->customer->email ?? '—' }}</a></td>
                                                <td>{{ $kyc->customer->phone_number ?? '—' }}</td>
                                                <td>
                                                    @if(($kyc->kyc_type ?? 'personal') === 'business')
                                                        <span class="badge bg-info text-white">Business (CSB-V)</span>
                                                    @else
                                                        <span class="badge bg-primary">Personal (CSB-IV)</span>
                                                    @endif
                                                </td>
                                                <td class="org-cell">{{ $kyc->organization_name ?? '—' }}</td>
                                                <td>{{ $kyc->gst_number ?? '—' }}</td>
                                                <td class="login-id-cell">
                                                    {{ $kyc->customer->email ?? '—' }}
                                                    @if($kyc->customer)
                                                        <i class="ti ti-copy copy-btn" title="Copy Login ID" onclick="copyLoginId('{{ $kyc->customer->email }}')"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($kyc->customer)
                                                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="openResetPasswordModal({{ $kyc->customer->id }}, '{{ addslashes($kyc->customer->first_name . ' ' . $kyc->customer->last_name) }}')">
                                                            <i class="ti ti-key me-1"></i> Reset Password
                                                        </button>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge-approved">
                                                        {{ $kyc->updated_at->format('d M Y, h:i A') }}
                                                    </span>
                                                </td>
                                                @php
                                                    $customerId = $kyc->customer->id ?? null;
                                                    $isActive = isset($kyc->customer->status) ? (bool) $kyc->customer->status : true;
                                                @endphp
                                                <td>
                                                    @if($isActive)
                                                        <span class="status-pill active">Active</span>
                                                    @else
                                                        <span class="status-pill inactive">Deactivated</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $canShip = isset($kyc->customer->can_create_shipment) ? (bool) $kyc->customer->can_create_shipment : true;
                                                    @endphp
                                                    @if($canShip)
                                                        <span class="status-pill active">Enabled</span>
                                                    @else
                                                        <span class="status-pill inactive">Disabled</span>
                                                    @endif
                                                </td>
                                                <td class="action-cell">
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @if($customerId)
                                                            <a href="{{ route('admin.customer-profile', $customerId) }}" class="btn-profile" title="View customer profile, login credentials & full KYC">
                                                                <i class="ti ti-user me-1"></i>Profile
                                                            </a>
                                                        @endif
                                                        @if($customerId)
                                                            <form action="{{ route('admin.customer.toggle-status', $customerId) }}" method="POST" class="d-inline toggle-status-form">
                                                                @csrf
                                                                @if($isActive)
                                                                    <button type="submit" class="btn-toggle-status is-active" title="Deactivate this customer account (blocks login)">
                                                                        <i class="ti ti-user-off me-1"></i>Deactivate
                                                                    </button>
                                                                @else
                                                                    <button type="submit" class="btn-toggle-status is-inactive" title="Activate this customer account (allows login)">
                                                                        <i class="ti ti-user-check me-1"></i>Activate
                                                                    </button>
                                                                @endif
                                                            </form>
                                                        @endif
                                                        @if($customerId)
                                                            @php
                                                                $canShip = isset($kyc->customer->can_create_shipment) ? (bool) $kyc->customer->can_create_shipment : true;
                                                            @endphp
                                                            <form action="{{ route('admin.customer.toggle-shipment-access', $customerId) }}" method="POST" class="d-inline toggle-shipment-form">
                                                                @csrf
                                                                @if($canShip)
                                                                    <button type="submit" class="btn-toggle-shipment is-enabled" title="Disable shipment creation for this customer">
                                                                        <i class="ti ti-package-off me-1"></i>Disable Shipment
                                                                    </button>
                                                                @else
                                                                    <button type="submit" class="btn-toggle-shipment is-disabled" title="Enable shipment creation for this customer">
                                                                        <i class="ti ti-package me-1"></i>Enable Shipment
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

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="resetPasswordForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="customer_id" id="resetCustomerId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resetPasswordModalLabel">
                            <i class="ti ti-key me-2"></i> Reset Customer Password
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            Set a new password for <strong id="resetCustomerName">this customer</strong>.
                            The customer can use this new password with their Login ID (email) to sign in.
                        </p>
                        <div class="mb-3">
                            <label class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" id="resetPassword" required minlength="6" autocomplete="new-password">
                            <small class="text-muted">Minimum 6 characters.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" id="resetPasswordConfirmation" required minlength="6" autocomplete="new-password">
                            <div class="invalid-feedback" id="passwordMismatchError" style="display:none;">
                                Passwords do not match.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="resetPasswordSubmit">
                            <i class="ti ti-check me-1"></i> Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Reset Password Modal -->

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
        // Initialize DataTable
        $(document).ready(function() {
            $('#kycApprovedTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25,
                language: {
                    search: "Search Approved:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    emptyTable: "No approved KYC submissions found.",
                },
                columnDefs: [
                    { orderable: false, targets: [10, 11, 12] }
                ]
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

            // SweetAlert2 confirmation for Enable/Disable shipment creation
            $('.toggle-shipment-form').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var btn = $(form).find('button[type="submit"]');
                var isDisable = btn.hasClass('is-enabled');
                var title = isDisable ? 'Disable Shipment Creation?' : 'Enable Shipment Creation?';
                var text = isDisable
                    ? "This customer will no longer be able to create new shipments. They will see a warning on the create-shipment page. You can re-enable it later."
                    : "This will allow the customer to create new shipments again.";
                var icon = isDisable ? 'warning' : 'question';
                var confirmColor = isDisable ? '#b45309' : '#1d4ed8';
                var confirmText = isDisable ? 'Yes, Disable' : 'Yes, Enable';
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
        });

        // Copy Login ID (email) to clipboard
        function copyLoginId(email) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(email).then(function() {
                    showToast('Login ID copied: ' + email);
                }).catch(function() {
                    fallbackCopy(email);
                });
            } else {
                fallbackCopy(email);
            }
        }

        function fallbackCopy(text) {
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                showToast('Login ID copied: ' + text);
            } catch (e) {
                showToast('Unable to copy. Please copy manually.');
            }
            document.body.removeChild(textarea);
        }

        // Simple toast notification
        function showToast(message) {
            var toast = document.createElement('div');
            toast.textContent = message;
            toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#1d4ed8;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;font-weight:600;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,0.2);opacity:0;transition:opacity 0.3s ease;';
            document.body.appendChild(toast);
            requestAnimationFrame(function() { toast.style.opacity = '1'; });
            setTimeout(function() {
                toast.style.opacity = '0';
                setTimeout(function() { document.body.removeChild(toast); }, 300);
            }, 2500);
        }

        // Open Reset Password Modal
        function openResetPasswordModal(customerId, customerName) {
            document.getElementById('resetCustomerId').value = customerId;
            document.getElementById('resetCustomerName').textContent = customerName || 'this customer';
            document.getElementById('resetPassword').value = '';
            document.getElementById('resetPasswordConfirmation').value = '';
            document.getElementById('passwordMismatchError').style.display = 'none';
            document.getElementById('resetPasswordForm').action = '{{ route("admin.customer.reset-password", ":id") }}'.replace(':id', customerId);
            var modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            modal.show();
        }

        // Validate password match before submit
        (function() {
            var form = document.getElementById('resetPasswordForm');
            if (!form) return;
            form.addEventListener('submit', function(e) {
                var pwd = document.getElementById('resetPassword').value;
                var confirm = document.getElementById('resetPasswordConfirmation').value;
                var errEl = document.getElementById('passwordMismatchError');
                if (pwd.length < 6) {
                    e.preventDefault();
                    errEl.textContent = 'Password must be at least 6 characters.';
                    errEl.style.display = 'block';
                    return;
                }
                if (pwd !== confirm) {
                    e.preventDefault();
                    errEl.textContent = 'Passwords do not match.';
                    errEl.style.display = 'block';
                    return;
                }
                errEl.style.display = 'none';
            });
        })();
    </script>

</body>
</html>