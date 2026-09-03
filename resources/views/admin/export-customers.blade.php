<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - All Customers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
        /* ============================================================
           Sub Customers page design system
           (same visual language as export-customers-detail page)
        ============================================================ */

        /* ---------- Hero / gradient header ---------- */
        .customer-profile-card {
            position: relative;
            overflow: hidden;
            border: 0;
            border-radius: 18px;
            background: linear-gradient(120deg, #0f2557 0%, #1d4ed8 55%, #2563eb 100%);
            box-shadow: 0 14px 34px -14px rgba(29, 78, 216, 0.55);
        }
        .customer-profile-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 88% 12%, rgba(255, 255, 255, 0.16), transparent 42%),
                radial-gradient(circle at 8% 95%, rgba(255, 255, 255, 0.08), transparent 48%);
            pointer-events: none;
        }
        .hero-avatar {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            border: 2px solid rgba(255, 255, 255, 0.4);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18);
            flex-shrink: 0;
        }
        .customer-name {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }
        .customer-sub {
            color: rgba(255, 255, 255, 0.85);
            font-size: 13.5px;
        }
        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.22);
            padding: 5px 13px;
            border-radius: 999px;
            font-size: 12.5px;
            line-height: 1.4;
        }
        .meta-pill .ti {
            font-size: 15px;
        }
        .meta-pill b {
            font-weight: 600;
        }

        /* ---------- Stat tiles ---------- */
        .stat-card {
            border: 1px solid #eef2f7;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04), 0 10px 24px -18px rgba(16, 24, 40, 0.14);
            padding: 16px 18px;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }
        .stat-icon.blue   { background: #eff6ff; color: #2563eb; }
        .stat-icon.violet { background: #f5f3ff; color: #7c3aed; }
        .stat-icon.sky    { background: #e0f2fe; color: #0284c7; }
        .stat-icon.green  { background: #ecfdf5; color: #059669; }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }
        .stat-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        /* ---------- Data card + table ---------- */
        .data-card {
            border: 1px solid #eef2f7;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04), 0 10px 24px -18px rgba(16, 24, 40, 0.12);
        }
        .data-card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e9eef5;
            border-radius: 18px 18px 0 0 !important;
            padding: 18px 20px;
        }
        #exportCustomersTable {
            width: 100%;
            min-width: 980px;
            max-width: none;
        }
        #exportCustomersTable thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-weight: 700;
            border-bottom: 1px solid #e9eef5;
            padding: 13px 12px;
            vertical-align: middle;
            white-space: nowrap;
        }
        #exportCustomersTable thead th .ti {
            font-size: 15px;
            vertical-align: -2px;
        }
        #exportCustomersTable tbody td {
            padding: 14px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        #exportCustomersTable tbody tr:last-child td {
            border-bottom: 0;
        }
        #exportCustomersTable tbody tr:hover td {
            background: #f8fafc;
        }
        /* preferred column widths */
        #exportCustomersTable th:nth-child(1) { min-width: 70px; }
        #exportCustomersTable th:nth-child(2) { min-width: 330px; }
        #exportCustomersTable th:nth-child(3) { min-width: 160px; }
        #exportCustomersTable th:nth-child(4) { min-width: 170px; }
        #exportCustomersTable th:nth-child(5) { min-width: 140px; }
        #exportCustomersTable th:nth-child(6) { min-width: 130px; }
        #exportCustomersTable tbody td:nth-child(6) { white-space: nowrap; }
        .row-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            padding: 0 7px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
        }

        /* ---------- Customer cell (avatar + name) ---------- */
        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.4px;
            flex-shrink: 0;
            box-shadow: 0 4px 10px -3px rgba(16, 24, 40, 0.35);
        }
        .user-name {
            font-weight: 600;
            color: #0f172a;
            line-height: 1.3;
            text-decoration: none;
        }
        .user-name:hover {
            color: #1d4ed8;
        }
        .user-code {
            font-size: 11.5px;
            color: #64748b;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        }
        .user-contact a {
            color: #64748b;
            text-decoration: none;
            font-size: 12.5px;
        }
        .user-contact a:hover {
            color: #1d4ed8;
        }
        .user-contact .ti {
            font-size: 13px;
            vertical-align: -1px;
            color: #94a3b8;
        }

        /* ---------- Type chip ---------- */
        .type-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 4px 11px;
            border-radius: 999px;
            white-space: nowrap;
        }
        .type-chip.business {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }
        .type-chip.personal {
            background: #f5f3ff;
            color: #7c3aed;
            border: 1px solid #ede9fe;
        }
        .type-chip.none {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        /* ---------- Count link (sub customers) ---------- */
        .count-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #7dd3fc;
            font-size: 12.5px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 20px;
            transition: all 0.2s;
            text-decoration: none;
            min-width: 46px;
            text-align: center;
            white-space: nowrap;
        }
        .count-link:hover {
            background-color: #0369a1;
            color: #fff;
            border-color: #0369a1;
            transform: translateY(-1px);
        }
        .count-zero {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: 12.5px;
            font-weight: 600;
            background-color: #f1f5f9;
            color: #94a3b8;
            border: 1px dashed #cbd5e1;
            padding: 5px 14px;
            border-radius: 20px;
            min-width: 46px;
            text-align: center;
            white-space: nowrap;
        }

        /* ---------- Status pill ---------- */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-pill .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-pill.active {
            background: #dcfce7;
            color: #15803d;
        }
        .status-pill.active .dot {
            background: #22c55e;
        }
        .status-pill.inactive {
            background: #fee2e2;
            color: #b91c1c;
        }
        .status-pill.inactive .dot {
            background: #ef4444;
        }

        /* ---------- View button ---------- */
        .btn-view {
            background-color: #f3e8ff;
            color: #6b21a8;
            border: 1px solid #d8b4fe;
            font-size: 12.5px;
            font-weight: 600;
            padding: 5px 14px;
            border-radius: 10px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-view:hover {
            background-color: #6b21a8;
            color: #fff;
            border-color: #6b21a8;
            transform: translateY(-1px);
        }

        /* ---------- Empty state ---------- */
        .empty-state {
            padding: 50px 20px;
        }
        .empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: #f1f5f9;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
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

                @php
                    // ---- computed summary stats (derived from the loaded collection) ----
                    $totalCustomers     = $customers->count();
                    $activeCustomers    = $customers->where('status', true)->count();
                    $totalSubCustomers  = (int) $customers->sum('exporter_customers_count');
                    $businessCount      = $customers->filter(fn ($c) => strtolower((string) ($c->category_user_type ?? '')) === 'business')->count();
                    $personalCount      = $customers->filter(fn ($c) => strtolower((string) ($c->category_user_type ?? '')) === 'personal')->count();

                    // avatar gradient pool (same palette as the detail page)
                    $avatarGradients = [
                        'linear-gradient(135deg, #6366f1, #8b5cf6)',
                        'linear-gradient(135deg, #0ea5e9, #2563eb)',
                        'linear-gradient(135deg, #10b981, #059669)',
                        'linear-gradient(135deg, #f59e0b, #ea580c)',
                        'linear-gradient(135deg, #ec4899, #a855f7)',
                        'linear-gradient(135deg, #06b6d4, #0d9488)',
                        'linear-gradient(135deg, #f43f5e, #d946ef)',
                    ];
                @endphp

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-circle-check me-2"></i>
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- ============ Hero / gradient header ============ -->
                <!-- <div class="row">
                    <div class="col-12">
                        <div class="card customer-profile-card mb-3">
                            <div class="card-body position-relative" style="z-index:1;">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="hero-avatar"><i class="ti ti-users"></i></span>
                                        <div>
                                            <div class="customer-name">All Customers</div>
                                            <div class="customer-sub mt-1">
                                                Every customer account and how many sub customers each one has
                                            </div>
                                        </div>
                                    </div>
                                    <span class="meta-pill"><i class="ti ti-building"></i><b>{{ $totalCustomers }}</b>&nbsp;Accounts</span>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <span class="meta-pill"><i class="ti ti-user-check"></i><b>{{ $activeCustomers }}</b>&nbsp;Active</span>
                                    <span class="meta-pill"><i class="ti ti-package"></i><b>{{ $totalSubCustomers }}</b>&nbsp;Total Sub Customers</span>
                                    <span class="meta-pill"><i class="ti ti-briefcase"></i><b>{{ $businessCount }}</b>&nbsp;Business</span>
                                    <span class="meta-pill"><i class="ti ti-user"></i><b>{{ $personalCount }}</b>&nbsp;Personal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- ============ Stat tiles ============ -->
                <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <span class="stat-icon blue"><i class="ti ti-building"></i></span>
                            <div>
                                <div class="stat-value">{{ $totalCustomers }}</div>
                                <div class="stat-label">Total Accounts</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <span class="stat-icon green"><i class="ti ti-user-check"></i></span>
                            <div>
                                <div class="stat-value">{{ $activeCustomers }}</div>
                                <div class="stat-label">Active Accounts</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <span class="stat-icon violet"><i class="ti ti-package"></i></span>
                            <div>
                                <div class="stat-value">{{ $totalSubCustomers }}</div>
                                <div class="stat-label">Total Sub Customers</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <span class="stat-icon sky"><i class="ti ti-briefcase"></i></span>
                            <div>
                                <div class="stat-value">{{ $businessCount }}</div>
                                <div class="stat-label">Business Accounts</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ Data card / table ============ -->
                <div class="row">
                    <div class="col-12">
                        <div class="card data-card">
                            <div class="card-header data-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-1">All Accounts</h5>
                                    <p class="card-text mb-0 text-muted">Click on the count pill to open sub customers of an account</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="exportCustomersTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><i class="ti ti-user me-1"></i>Customer</th>
                                                <th><i class="ti ti-tag me-1"></i>User Type</th>
                                                <th><i class="ti ti-package me-1"></i>Sub Customers</th>
                                                <th><i class="ti ti-status-change me-1"></i>Status</th>
                                                <th><i class="ti ti-eye me-1"></i>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customers as $key => $customer)
                                                @php
                                                    $userType    = strtolower((string) ($customer->category_user_type ?? ''));
                                                    $fullName    = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
                                                    $nameWords   = preg_split('/\s+/', $fullName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                                                    $initials    = strtoupper(
                                                        (string) mb_substr($nameWords[0] ?? '?', 0, 1) .
                                                        (string) mb_substr($nameWords[1] ?? ($nameWords[0] ?? ''), 0, 1)
                                                    );
                                                    $avatarIndex = ($key % count($avatarGradients));
                                                    $avatarBg    = $avatarGradients[$avatarIndex];
                                                    $hasSubs     = (int) $customer->exporter_customers_count > 0;
                                                @endphp
                                                <tr>
                                                    <td><span class="row-index">{{ $key + 1 }}</span></td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="user-avatar" style="background: {{ $avatarBg }};">{{ $initials }}</span>
                                                            <div>
                                                                <a class="user-name" href="{{ route('admin.export-customers.detail', $customer->id) }}">{{ $fullName ?: '—' }}</a>
                                                                <div class="user-code mt-1">{{ $customer->customer_code ?? '—' }}</div>
                                                                <div class="user-contact d-flex flex-wrap gap-2 mt-1">
                                                                    @if($customer->email)
                                                                        <a href="mailto:{{ $customer->email }}"><i class="ti ti-mail"></i>  {{ $customer->email }}</a>
                                                                    @endif
                                                                </div>
                                                                <div class="user-contact d-flex flex-wrap gap-2 mt-1">
                                                                    @if($customer->phone_number)
                                                                        <a href="tel:{{ $customer->phone_number }}"><i class="ti ti-phone"></i> {{ $customer->phone_number }}</a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($userType === 'business')
                                                            <span class="type-chip business"><i class="ti ti-briefcase"></i>Business</span>
                                                        @elseif($userType === 'personal')
                                                            <span class="type-chip personal"><i class="ti ti-user"></i>Personal</span>
                                                        @else
                                                            <span class="type-chip none">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($hasSubs)
                                                            <a href="{{ route('admin.export-customers.detail', $customer->id) }}"
                                                                class="count-link"
                                                                title="View {{ $customer->exporter_customers_count }} sub customer(s) of {{ $fullName }}">
                                                                <i class="ti ti-users"></i>{{ $customer->exporter_customers_count }}
                                                            </a>
                                                        @else
                                                            <span class="count-zero"><i class="ti ti-users"></i>0</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($customer->status)
                                                            <span class="status-pill active"><span class="dot"></span>Active</span>
                                                        @else
                                                            <span class="status-pill inactive"><span class="dot"></span>Deactivated</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.customer-profile', $customer->id) }}" target="blank" class="btn-view" title="View sub customers">
                                                            <i class="ti ti-users"></i>View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        <div class="empty-state">
                                                            <div class="empty-icon mb-2"><i class="ti ti-users-off"></i></div>
                                                            <div class="fw-semibold text-secondary">No customers found</div>
                                                            <div class="small">There are no United customer accounts yet.</div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
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
            $('#exportCustomersTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    search: "Search Customers:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    emptyTable: "No customers found.",
                },
                columnDefs: [
                    { orderable: false, targets: [5] }
                ]
            });
        });

    </script>

</body>
</html>
