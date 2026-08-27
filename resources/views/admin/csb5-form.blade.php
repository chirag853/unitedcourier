<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - CSB5 Forms</title>
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

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />

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
        .customer-name-cell {
            min-width: 180px;
        }
        .badge-csb-v {
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-csb-iv {
            background-color: #ede9fe;
            color: #6d28d9;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-yes {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-no {
            background-color: #ffebee;
            color: #c62828;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .btn-profile {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #1565c0;
            font-size: 13px;
            padding: 4px 12px;
            border-radius: 4px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-profile:hover {
            background-color: #1565c0;
            color: #fff;
        }
        .btn-doc {
            background-color: #fff8e1;
            color: #e65100;
            border: 1px solid #e65100;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 4px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-doc:hover {
            background-color: #e65100;
            color: #fff;
        }
        .doc-cell {
            min-width: 150px;
        }
        .detail-cell {
            font-size: 13px;
        }
        .detail-cell .sub-label {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .csb-summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }
        .csb-summary-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
        }
        .csb-summary-card .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
        }
        .csb-summary-card .stat-label {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        @include('admin.partials.header')

        @include('admin.partials.sidebar')

        <!-- ========================
            Start Page Content
        ========================= -->

        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content pb-0">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">CSB5 Form Submissions</h4>
                        <p class="text-muted mb-0">All CSB-V / CSB-IV form data submitted by customers</p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                    </div>
                </div>
                <!-- End Page Header -->

                @php
                    $totalSubmissions = $csbForms->count();
                    $csbVCount = $csbForms->where('is_csb_v', true)->count();
                    $csbIvCount = $csbForms->where('is_csb_v', false)->count();
                    $lutCount = $csbForms->where('is_lut', true)->count();

                    $docUrl = static function (...$paths) {
                        foreach ($paths as $p) {
                            $path = trim((string) $p);
                            if ($path === '') {
                                continue;
                            }
                            if (filter_var($path, FILTER_VALIDATE_URL)) {
                                return $path;
                            }
                            $path = ltrim(str_replace('\\', '/', $path), '/');
                            $path = preg_replace('#^(?:(?:public|uploads)/)+#i', '', $path) ?? $path;
                            if (is_file(public_path('uploads/' . ltrim($path, '/')))) {
                                return asset('uploads/' . ltrim($path, '/'));
                            }
                        }
                        return null;
                    };
                @endphp

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="csb-summary-cards">
                            <div class="csb-summary-card">
                                <div class="stat-value">{{ $totalSubmissions }}</div>
                                <div class="stat-label">Total CSB Submissions</div>
                            </div>
                            <div class="csb-summary-card">
                                <div class="stat-value" style="color:#0369a1;">{{ $csbVCount }}</div>
                                <div class="stat-label">CSB-V (Export / Business)</div>
                            </div>
                            <div class="csb-summary-card">
                                <div class="stat-value" style="color:#6d28d9;">{{ $csbIvCount }}</div>
                                <div class="stat-label">CSB-IV (Postal / Personal)</div>
                            </div>
                            <div class="csb-summary-card">
                                <div class="stat-value" style="color:#2e7d32;">{{ $lutCount }}</div>
                                <div class="stat-label">With LUT</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CSB5 Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-1">CSB5 Form Details</h5>
                                    <p class="card-text mb-0">Export codes, LUT, banking and billing details submitted via the CSB5 form</p>
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
                                    <table class="table table-hover" id="csb5Table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Customer</th>
                                                <th>CSB Type</th>
                                                <th>IEC / AD Code</th>
                                                <th>LUT</th>
                                                <th>Bank Details</th>
                                                <th>Billing</th>
                                                <th>Documents</th>
                                                <th>Submitted At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($csbForms as $key => $csb)
                                            @php
                                                $customer = $csb->customer;
                                                $customerName = $customer
                                                    ? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''))
                                                    : 'Customer';
                                                $iecUrl = $docUrl($csb->iec_document);
                                                $adCodeUrl = $docUrl($csb->ad_code_document);
                                                $lutUrl = $docUrl($csb->lut_document);
                                                $gstUrl = $docUrl($csb->gst_certificate_document, $csb->gst_document);
                                                $aadharUrl = $docUrl($csb->aadhar_document);
                                                $signatureUrl = $docUrl($csb->signature_document);
                                                $agreementUrl = $docUrl($csb->merchant_agreement);
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td class="customer-name-cell">
                                                    <strong>{{ $customerName !== '' ? $customerName : 'Customer' }}</strong>
                                                    <div class="small">
                                                        <a href="mailto:{{ $customer->email ?? '' }}" class="text-decoration-none">{{ $customer->email ?? '—' }}</a>
                                                    </div>
                                                    <div class="small text-muted">{{ $customer->phone_number ?? '—' }}</div>
                                                </td>
                                                <td>
                                                    @if($csb->is_csb_v)
                                                        <span class="badge-csb-v">CSB-V</span>
                                                    @else
                                                        <span class="badge-csb-iv">CSB-IV</span>
                                                    @endif
                                                </td>
                                                <td class="detail-cell">
                                                    <div class="sub-label">IEC</div>
                                                    <div>{{ $csb->iec_number ?? '—' }}</div>
                                                    <div class="sub-label mt-1">AD Code</div>
                                                    <div>{{ $csb->ad_code ?? '—' }}</div>
                                                </td>
                                                <td class="detail-cell">
                                                    @if($csb->is_lut)
                                                        <span class="badge-yes"><i class="ti ti-circle-check"></i> Yes</span>
                                                    @else
                                                        <span class="badge-no"><i class="ti ti-circle-x"></i> No</span>
                                                    @endif
                                                    @if($csb->lut_number)
                                                        <div class="small mt-1">No: {{ $csb->lut_number }}</div>
                                                    @endif
                                                    @if($csb->lut_bond_year)
                                                        <div class="small text-muted">Year: {{ $csb->lut_bond_year }}</div>
                                                    @endif
                                                    @if($csb->lut_expiry_date)
                                                        <div class="small text-muted">Expiry: {{ $csb->lut_expiry_date->format('d M Y') }}</div>
                                                    @endif
                                                </td>
                                                <td class="detail-cell">
                                                    <div class="sub-label">Category</div>
                                                    <div>{{ ucfirst($csb->bank_type ?? '—') }}</div>
                                                    <div class="sub-label mt-1">Account No</div>
                                                    <div>{{ $csb->bank_account_number ?? '—' }}</div>
                                                </td>
                                                <td class="detail-cell">
                                                    <div class="sub-label">GST</div>
                                                    <div>{{ $csb->billing_gst ?? ($csb->gst_certificate_number ?? '—') }}</div>
                                                    <div class="sub-label mt-1">Contact</div>
                                                    <div>{{ $csb->billing_contact ?? '—' }}</div>
                                                    @if($csb->billing_email)
                                                        <div class="small text-muted">{{ $csb->billing_email }}</div>
                                                    @endif
                                                    @if($csb->billing_address)
                                                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($csb->billing_address, 40) }}</div>
                                                    @endif
                                                </td>
                                                <td class="doc-cell">
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @if($iecUrl)
                                                            <a href="{{ $iecUrl }}" target="_blank" class="btn-doc" title="View IEC Certificate"><i class="ti ti-file-export"></i> IEC</a>
                                                        @endif
                                                        @if($adCodeUrl)
                                                            <a href="{{ $adCodeUrl }}" target="_blank" class="btn-doc" title="View AD Code Document"><i class="ti ti-numbers"></i> AD</a>
                                                        @endif
                                                        @if($lutUrl)
                                                            <a href="{{ $lutUrl }}" target="_blank" class="btn-doc" title="View LUT Document"><i class="ti ti-file-text"></i> LUT</a>
                                                        @endif
                                                        @if($gstUrl)
                                                            <a href="{{ $gstUrl }}" target="_blank" class="btn-doc" title="View GST Certificate"><i class="ti ti-file-invoice"></i> GST</a>
                                                        @endif
                                                        @if($aadharUrl)
                                                            <a href="{{ $aadharUrl }}" target="_blank" class="btn-doc" title="View Aadhaar Document"><i class="ti ti-id"></i> Aadhaar</a>
                                                        @endif
                                                        @if($signatureUrl)
                                                            <a href="{{ $signatureUrl }}" target="_blank" class="btn-doc" title="View Signature"><i class="ti ti-pencil"></i> Sign</a>
                                                        @endif
                                                        @if($agreementUrl)
                                                            <a href="{{ $agreementUrl }}" target="_blank" class="btn-doc" title="View Merchant Agreement"><i class="ti ti-file-signature"></i> Agr</a>
                                                        @endif
                                                        @if(!$iecUrl && !$adCodeUrl && !$lutUrl && !$gstUrl && !$aadharUrl && !$signatureUrl && !$agreementUrl)
                                                            <span class="text-muted small">No documents</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge-csb-iv">
                                                        {{ $csb->created_at?->format('d M Y, h:i A') ?? '—' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($customer)
                                                        <a href="{{ route('admin.customer-profile', $customer->id) }}" class="btn-profile" title="View customer profile & full KYC">
                                                            <i class="ti ti-user"></i> Profile
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-4 text-muted">
                                                    No CSB5 form submissions found yet.
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

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#csb5Table').DataTable({
                order: [[8, 'desc']],
                pageLength: 25,
                language: {
                    search: "Search CSB5:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    emptyTable: "No CSB5 form submissions found.",
                },
                columnDefs: [
                    { orderable: false, targets: [7, 9] }
                ]
            });
        });
    </script>

</body>

</html>
