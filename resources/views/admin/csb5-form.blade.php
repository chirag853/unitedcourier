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

                <!-- CSB5 Content Tabs -->
                <ul class="nav nav-tabs mb-3" id="csb5Tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-all-btn" data-bs-toggle="tab" data-bs-target="#tab-all" type="button" role="tab" aria-controls="tab-all" aria-selected="true">
                            All Submissions <span class="badge bg-secondary ms-1">{{ $totalSubmissions }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-pending-btn" data-bs-toggle="tab" data-bs-target="#tab-pending" type="button" role="tab" aria-controls="tab-pending" aria-selected="false">
                            CSB 5 Pending <span class="badge bg-warning ms-1">{{ $csbForms->where('is_csb_v', false)->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="csb5TabsContent">
                <!-- Tab: All Submissions -->
                <div class="tab-pane fade show active" id="tab-all" role="tabpanel" aria-labelledby="tab-all-btn">
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
                                            @foreach($csbForms as $key => $csb)
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
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <!-- Tab: CSB 5 Pending (is_csb_v = 0) -->
                <div class="tab-pane fade" id="tab-pending" role="tabpanel" aria-labelledby="tab-pending-btn">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-1">CSB 5 Pending Details</h5>
                                    <p class="card-text mb-0">Submissions with is_csb_v = 0 (CSB5 under review) — same information, pre-filled as submitted</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="csb5PendingTable">
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
                                            @foreach($csbForms->where('is_csb_v', false) as $key => $csb)
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
                                                        <button type="button" class="btn-profile" title="View full CSB5 details"
                                                            data-bs-toggle="modal" data-bs-target="#csbPendingModal"
                                                            data-approve-url="{{ route('admin.csb5-form.approve', $csb->id) }}"
                                                            data-customer="{{ e($customerName !== '' ? $customerName : 'Customer') }}"
                                                            data-email="{{ e($customer->email ?? '') }}"
                                                            data-phone="{{ e($customer->phone_number ?? '') }}"
                                                            data-csb-type="{{ $csb->is_csb_v ? 'CSB-V' : 'CSB-IV' }}"
                                                            data-gst-number="{{ e($csb->gst_certificate_number ?? '') }}"
                                                            data-gst-url="{{ $gstUrl ?? '' }}"
                                                            data-iec-number="{{ e($csb->iec_number ?? '') }}"
                                                            data-iec-url="{{ $iecUrl ?? '' }}"
                                                            data-ad-code="{{ e($csb->ad_code ?? '') }}"
                                                            data-ad-url="{{ $adCodeUrl ?? '' }}"
                                                            data-is-lut="{{ $csb->is_lut ? 'Yes' : 'No' }}"
                                                            data-lut-number="{{ e($csb->lut_number ?? '') }}"
                                                            data-lut-bond-year="{{ e($csb->lut_bond_year ?? '') }}"
                                                            data-lut-expiry="{{ $csb->lut_expiry_date?->format('d M Y') ?? '' }}"
                                                            data-lut-url="{{ $lutUrl ?? '' }}"
                                                            data-bank-type="{{ e(ucfirst($csb->bank_type ?? '')) }}"
                                                            data-bank-account="{{ e($csb->bank_account_number ?? '') }}"
                                                            data-billing-gst="{{ e($csb->billing_gst ?? ($csb->gst_certificate_number ?? '')) }}"
                                                            data-billing-contact="{{ e($csb->billing_contact ?? '') }}"
                                                            data-billing-email="{{ e($csb->billing_email ?? '') }}"
                                                            data-billing-address="{{ e($csb->billing_address ?? '') }}"
                                                            data-aadhar-url="{{ $aadharUrl ?? '' }}"
                                                            data-signature-url="{{ $signatureUrl ?? '' }}"
                                                            data-agreement-url="{{ $agreementUrl ?? '' }}"
                                                            data-submitted="{{ $csb->created_at?->format('d M Y, h:i A') ?? '' }}">
                                                            <i class="ti ti-user"></i> Profile
                                                        </button>
                                                    @endif
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
                </div><!-- End CSB5 Tabs Content -->

                <!-- CSB 5 Pending Detail Modal -->
                <div class="modal fade" id="csbPendingModal" tabindex="-1" aria-labelledby="csbPendingModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="csbPendingModalLabel">CSB5 Form Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div>
                                        <h6 class="mb-0" id="pm_customer">—</h6>
                                        <div class="small text-muted"><span id="pm_email">—</span> · <span id="pm_phone">—</span></div>
                                    </div>
                                    <span class="badge bg-warning ms-auto" id="pm_csb_type">CSB-IV</span>
                                </div>
                                <hr>
                                <div class="row g-3 detail-cell">
                                    <div class="col-md-6">
                                        <div class="sub-label">GST Certificate Number</div>
                                        <div id="pm_gst_number">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sub-label">Submitted At</div>
                                        <div id="pm_submitted">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sub-label">IEC Number</div>
                                        <div id="pm_iec_number">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sub-label">AD Code</div>
                                        <div id="pm_ad_code">—</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-label">LUT Applicable</div>
                                        <div id="pm_is_lut">—</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-label">LUT Number</div>
                                        <div id="pm_lut_number">—</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="sub-label">LUT Bond Year</div>
                                        <div id="pm_lut_bond_year">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sub-label">LUT Expiry Date</div>
                                        <div id="pm_lut_expiry">—</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="sub-label">Bank Category</div>
                                        <div id="pm_bank_type">—</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="sub-label">Account No</div>
                                        <div id="pm_bank_account">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sub-label">Billing GST</div>
                                        <div id="pm_billing_gst">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sub-label">Billing Contact</div>
                                        <div id="pm_billing_contact">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sub-label">Billing Email</div>
                                        <div id="pm_billing_email">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sub-label">Billing Address</div>
                                        <div id="pm_billing_address">—</div>
                                    </div>
                                </div>
                                <hr>
                                <div class="detail-cell">
                                    <div class="sub-label mb-2">Documents</div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a id="pm_doc_iec" target="_blank" class="btn-doc" title="View IEC Certificate"><i class="ti ti-file-export"></i> IEC</a>
                                        <a id="pm_doc_ad" target="_blank" class="btn-doc" title="View AD Code Document"><i class="ti ti-numbers"></i> AD</a>
                                        <a id="pm_doc_lut" target="_blank" class="btn-doc" title="View LUT Document"><i class="ti ti-file-text"></i> LUT</a>
                                        <a id="pm_doc_gst" target="_blank" class="btn-doc" title="View GST Certificate"><i class="ti ti-file-invoice"></i> GST</a>
                                        <a id="pm_doc_aadhar" target="_blank" class="btn-doc" title="View Aadhaar Document"><i class="ti ti-id"></i> Aadhaar</a>
                                        <a id="pm_doc_sign" target="_blank" class="btn-doc" title="View Signature"><i class="ti ti-pencil"></i> Sign</a>
                                        <a id="pm_doc_agr" target="_blank" class="btn-doc" title="View Merchant Agreement"><i class="ti ti-file-signature"></i> Agr</a>
                                        <span id="pm_no_docs" class="text-muted small">No documents</span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <form id="pm_approve_form" method="POST" action="" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#csbApproveConfirmModal"><i class="ti ti-check"></i> Approve</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CSB5 Approve Confirmation Alert Popup -->
                <div class="modal fade" id="csbApproveConfirmModal" tabindex="-1" aria-labelledby="csbApproveConfirmLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body text-center py-4">
                                <div class="mb-3">
                                    <span class="avatar avatar-lg bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                                        <i class="ti ti-alert-triangle" style="font-size:28px;"></i>
                                    </span>
                                </div>
                                <h6 class="fw-bold mb-1" id="csbApproveConfirmLabel">Approve this CSB5 form?</h6>
                                <p class="text-muted small mb-0">It will move out of pending.</p>
                            </div>
                            <div class="modal-footer justify-content-center border-0 pt-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-success" onclick="document.getElementById('pm_approve_form').submit();"><i class="ti ti-check"></i> Yes, Approve</button>
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
            $('#csb5PendingTable').DataTable({
                order: [[8, 'desc']],
                pageLength: 25,
                language: {
                    search: "Search Pending:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    emptyTable: "No pending CSB5 submissions found.",
                },
                columnDefs: [
                    { orderable: false, targets: [7, 9] }
                ]
            });
            // Hidden tab tables need a column recalculation when shown.
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            });

            // Fill the CSB 5 Pending detail modal from the clicked Profile button.
            var csbPendingModal = document.getElementById('csbPendingModal');
            if (csbPendingModal) {
                csbPendingModal.addEventListener('show.bs.modal', function (event) {
                    var btn = event.relatedTarget;
                    if (!btn || !btn.dataset) return;
                    var d = btn.dataset;
                    var setText = function (id, value) {
                        var el = document.getElementById(id);
                        if (el) el.textContent = (value && String(value).trim() !== '') ? value : '—';
                    };
                    setText('pm_customer', d.customer);
                    setText('pm_email', d.email);
                    setText('pm_phone', d.phone);
                    setText('pm_csb_type', d.csbType);
                    setText('pm_gst_number', d.gstNumber);
                    setText('pm_iec_number', d.iecNumber);
                    setText('pm_ad_code', d.adCode);
                    setText('pm_is_lut', d.isLut);
                    setText('pm_lut_number', d.lutNumber);
                    setText('pm_lut_bond_year', d.lutBondYear);
                    setText('pm_lut_expiry', d.lutExpiry);
                    setText('pm_bank_type', d.bankType);
                    setText('pm_bank_account', d.bankAccount);
                    setText('pm_billing_gst', d.billingGst);
                    setText('pm_billing_contact', d.billingContact);
                    setText('pm_billing_email', d.billingEmail);
                    setText('pm_billing_address', d.billingAddress);
                    setText('pm_submitted', d.submitted);
                    var title = document.getElementById('csbPendingModalLabel');
                    if (title) title.textContent = 'CSB5 Details — ' + (d.customer || 'Customer');
                    var setDoc = function (id, url) {
                        var el = document.getElementById(id);
                        if (!el) return;
                        if (url && String(url).trim() !== '') {
                            el.href = url;
                            el.style.display = 'inline-flex';
                        } else {
                            el.removeAttribute('href');
                            el.style.display = 'none';
                        }
                    };
                    setDoc('pm_doc_iec', d.iecUrl);
                    setDoc('pm_doc_ad', d.adUrl);
                    setDoc('pm_doc_lut', d.lutUrl);
                    setDoc('pm_doc_gst', d.gstUrl);
                    setDoc('pm_doc_aadhar', d.aadharUrl);
                    setDoc('pm_doc_sign', d.signatureUrl);
                    setDoc('pm_doc_agr', d.agreementUrl);
                    var hasDoc = ['iecUrl', 'adUrl', 'lutUrl', 'gstUrl', 'aadharUrl', 'signatureUrl', 'agreementUrl']
                        .some(function (k) { return d[k] && String(d[k]).trim() !== ''; });
                    var noDocs = document.getElementById('pm_no_docs');
                    if (noDocs) noDocs.style.display = hasDoc ? 'none' : 'inline';
                    var approveForm = document.getElementById('pm_approve_form');
                    if (approveForm) approveForm.action = d.approveUrl || '';
                });
            }
            // Keep the pending tab active after approve redirect (#tab-pending).
            if (window.location.hash === '#tab-pending') {
                var pendingTabBtn = document.getElementById('tab-pending-btn');
                if (pendingTabBtn) {
                    if (window.bootstrap && bootstrap.Tab) {
                        bootstrap.Tab.getOrCreateInstance(pendingTabBtn).show();
                    } else {
                        pendingTabBtn.click();
                    }
                }
            }
        });
    </script>

</body>

</html>
