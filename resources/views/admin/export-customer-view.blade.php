<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - {{ $exporterCustomer->company_name ?: 'Exporter Customer' }}</title>
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
        /* ---------------------------------------------------------------
           Profile header (gradient banner)
        --------------------------------------------------------------- */
        .view-profile-card {
            position: relative;
            overflow: hidden;
            border: 0;
            border-radius: 18px;
            background: linear-gradient(120deg, #0f2557 0%, #1d4ed8 55%, #2563eb 100%);
            box-shadow: 0 14px 34px -14px rgba(29, 78, 216, 0.55);
        }
        .view-profile-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 88% 12%, rgba(255, 255, 255, 0.16), transparent 42%),
                radial-gradient(circle at 8% 95%, rgba(255, 255, 255, 0.08), transparent 48%);
            pointer-events: none;
        }
        .view-avatar {
            width: 84px;
            height: 84px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.18);
            border: 2px solid rgba(255, 255, 255, 0.4);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18);
            flex-shrink: 0;
        }
        .view-name {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }
        .view-sub {
            color: rgba(255, 255, 255, 0.85);
            font-size: 13.5px;
        }
        .view-pill {
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
        .view-pill .ti { font-size: 15px; }
        .view-pill b { font-weight: 600; }
        .view-back {
            background: rgba(255, 255, 255, 0.94);
            color: #1d4ed8;
            border: 0;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 999px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }
        .view-back:hover {
            background: #fff;
            color: #1e3a8a;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
        }

        /* ---------------------------------------------------------------
           Detail cards
        --------------------------------------------------------------- */
        .detail-card {
            border: 1px solid #eef2f7;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04), 0 10px 24px -18px rgba(16, 24, 40, 0.12);
            height: 100%;
        }
        .detail-card .card-head {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 14px 18px;
            border-bottom: 1px solid #eef2f7;
        }
        .detail-card .card-head .head-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            background: #eff6ff;
            color: #2563eb;
            flex-shrink: 0;
        }
        .detail-card .card-head .head-title {
            font-weight: 700;
            color: #0f172a;
            font-size: 14px;
        }
        .detail-card .card-body { padding: 16px 18px; }

        .kv-row {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            padding: 7px 0;
            border-bottom: 1px dashed #eef2f7;
        }
        .kv-row:last-child { border-bottom: 0; }
        .kv-row .k {
            color: #64748b;
            font-size: 12.5px;
            font-weight: 500;
            flex-shrink: 0;
        }
        .kv-row .v {
            color: #0f172a;
            font-size: 13px;
            font-weight: 600;
            text-align: right;
            overflow-wrap: anywhere;
            word-break: break-word;
            min-width: 0;
        }
        .kv-row .v.mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 12px;
        }
        .kv-row .v .muted { color: #94a3b8; font-weight: 400; }
        .kv-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.2px;
            padding: 4px 12px;
            border-radius: 999px;
            line-height: 1.5;
        }
        .kv-chip.blue   { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
        .kv-chip.green  { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
        .kv-chip.amber  { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .kv-chip.gray   { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
        .kv-chip.violet { background: #f5f3ff; color: #6d28d9; border: 1px solid #ede9fe; }

        /* Address blocks */
        .addr-box {
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-left: 3px solid #93c5fd;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 10px;
        }
        .addr-box:last-child { margin-bottom: 0; }
        .addr-box.is-primary { border-left-color: #2563eb; background: #f8fbff; }
        .addr-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 2px 10px;
            border-radius: 999px;
            margin-bottom: 7px;
        }
        .addr-tag.muted { background: #e2e8f0; color: #64748b; border-color: #cbd5e1; }
        .addr-line1 { font-weight: 600; color: #0f172a; font-size: 13.5px; }
        .addr-sub { color: #64748b; font-size: 12.5px; line-height: 1.5; }

        /* ---------------------------------------------------------------
           Documents grid
        --------------------------------------------------------------- */
        .doc-card {
            border: 1px solid #eef2f7;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04), 0 10px 24px -18px rgba(16, 24, 40, 0.12);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: box-shadow .15s ease, transform .15s ease;
        }
        .doc-card:hover { box-shadow: 0 14px 30px -18px rgba(16, 24, 40, 0.2); transform: translateY(-2px); }
        .doc-preview {
            height: 150px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-bottom: 1px solid #eef2f7;
            position: relative;
        }
        .doc-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .doc-preview .doc-big-icon { font-size: 52px; color: #94a3b8; }
        .doc-preview .doc-big-icon.pdf { color: #ef4444; }
        .doc-preview .doc-big-icon.img { color: #2563eb; }
        .doc-body { padding: 12px 14px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .doc-name-label {
            font-weight: 700;
            color: #0f172a;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .doc-file {
            font-size: 11px;
            color: #64748b;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            overflow-wrap: anywhere;
            word-break: break-all;
            background: #f8fafc;
            border: 1px solid #e9eef5;
            border-radius: 6px;
            padding: 4px 8px;
        }
        .doc-actions { margin-top: auto; display: flex; gap: 7px; }
        .doc-actions .btn { flex: 1; font-size: 12.5px; }
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
                    $ec = $exporterCustomer;

                    $companyName = (string) ($ec->company_name ?? '');
                    $companyWords = preg_split('/\s+/', trim($companyName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
                    $companyInitials = strtoupper(
                        (string) mb_substr($companyWords[0] ?? '', 0, 1) .
                        (string) mb_substr($companyWords[1] ?? ($companyWords[0] ?? ''), 0, 1)
                    );
                    if ($companyInitials === '') {
                        $companyInitials = '?';
                    }
                    $avatarGradients = [
                        'linear-gradient(135deg, #6366f1, #8b5cf6)',
                        'linear-gradient(135deg, #0ea5e9, #2563eb)',
                        'linear-gradient(135deg, #10b981, #059669)',
                        'linear-gradient(135deg, #f59e0b, #ea580c)',
                        'linear-gradient(135deg, #ec4899, #a855f7)',
                        'linear-gradient(135deg, #06b6d4, #0d9488)',
                        'linear-gradient(135deg, #f43f5e, #d946ef)',
                    ];
                    $companyGradient = $avatarGradients[
                        abs(crc32($companyName !== '' ? $companyName : (string) $ec->id)) % count($avatarGradients)
                    ];

                    // KYC type badge
                    $kycRaw = strtolower(trim((string) ($ec->kyc_type ?? '')));
                    if (str_contains($kycRaw, 'aadhar')) {
                        $kycBadge = ['label' => 'Aadhaar Card', 'icon' => 'ti-id-badge', 'class' => 'blue'];
                    } elseif (str_contains($kycRaw, 'pan')) {
                        $kycBadge = ['label' => 'PAN Card', 'icon' => 'ti-file-text', 'class' => 'amber'];
                    } elseif (str_contains($kycRaw, 'gst')) {
                        $kycBadge = ['label' => 'GST', 'icon' => 'ti-building', 'class' => 'violet'];
                    } elseif (! empty($ec->gst_certificate_number)) {
                        $kycBadge = ['label' => 'GST', 'icon' => 'ti-building', 'class' => 'violet'];
                    } elseif (! empty($ec->pan_number)) {
                        $kycBadge = ['label' => 'PAN Card', 'icon' => 'ti-file-text', 'class' => 'amber'];
                    } else {
                        $kycBadge = null;
                    }

                    // Resolve a stored document path into a public URL
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

                    $docName = static function ($url) {
                        return $url ? basename((string) parse_url($url, PHP_URL_PATH)) : null;
                    };

                    $isImageDoc = static function ($url) {
                        return (bool) preg_match('/\.(jpe?g|png|gif|webp)$/i', (string) $url);
                    };

                    $documents = [
                        'aadhar_front' => ['label' => 'Aadhaar (Front)', 'icon' => 'ti-id-badge', 'url' => $docUrl($ec->aadhar_front_document)],
                        'aadhar_back' => ['label' => 'Aadhaar (Back)', 'icon' => 'ti-id-badge', 'url' => $docUrl($ec->aadhar_back_document)],
                        'pan' => ['label' => 'PAN Card', 'icon' => 'ti-file-text', 'url' => $docUrl($ec->pan_document)],
                        'gst' => ['label' => 'GST Certificate', 'icon' => 'ti-building', 'url' => $docUrl($ec->gst_certificate_document)],
                        'iec' => ['label' => 'IEC Certificate', 'icon' => 'ti-file-certificate', 'url' => $docUrl($ec->iec_document)],
                        'ad_code' => ['label' => 'AD Code Document', 'icon' => 'ti-file-text', 'url' => $docUrl($ec->ad_code_document)],
                        'lut' => ['label' => 'LUT Document', 'icon' => 'ti-file-text', 'url' => $docUrl($ec->lut_document)],
                        'merchant_agreement' => ['label' => 'Merchant Agreement', 'icon' => 'ti-file-type-pdf', 'url' => $docUrl($ec->merchant_agreement)],
                    ];
                    $uploadedDocuments = array_filter($documents, fn ($d) => ! empty($d['url']));

                    $displayAddresses = $ec->displayAddresses();

                    $parentBackUrl = $parentCustomer
                        ? route('admin.export-customers.detail', $parentCustomer->id)
                        : route('admin.export-customers');
                @endphp

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-circle-check me-2"></i>
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- ============ Profile / gradient header ============ -->
                <div class="row">
                    <div class="col-12">
                        <div class="card view-profile-card mb-3">
                            <div class="card-body position-relative" style="z-index:1;">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="view-avatar" style="background: {{ $companyGradient }};">{{ $companyInitials }}</span>
                                        <div>
                                            <div class="view-name">{{ $companyName !== '' ? $companyName : '—' }}</div>
                                            <div class="view-sub mt-1">
                                                @if($ec->contact_person)
                                                    <i class="ti ti-user me-1"></i>{{ $ec->contact_person }}
                                                @else
                                                    Exporter customer profile
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ $parentBackUrl }}" class="view-back" title="Back to exporter customers">
                                            <i class="ti ti-arrow-left"></i>Back
                                        </a>
                                        <a href="{{ $parentBackUrl }}" class="view-back" title="Back to all exporter customers list">
                                            <i class="ti ti-list"></i>All
                                        </a>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    @if($ec->businessCategory)
                                        <span class="view-pill"><i class="ti ti-tag"></i>{{ $ec->businessCategory->category_name ?? 'Exporter' }}</span>
                                    @endif
                                    @if($ec->ad_code)
                                        <span class="view-pill"><i class="ti ti-hash"></i><b>AD Code: {{ $ec->ad_code }}</b></span>
                                    @endif
                                    @if($ec->kyc_number || $kycBadge)
                                        <span class="view-pill"><i class="ti {{ $kycBadge['icon'] ?? 'ti-shield-check' }}"></i>
                                            @if($kycBadge)
                                                {{ $kycBadge['label'] }}
                                            @else
                                                KYC
                                            @endif
                                            @if($ec->kyc_number)
                                                <b>({{ $ec->kyc_number }})</b>
                                            @endif
                                        </span>
                                    @endif
                                    @if($ec->email)
                                        <span class="view-pill"><i class="ti ti-mail"></i>{{ $ec->email }}</span>
                                    @endif
                                    @if($ec->phone_number)
                                        <span class="view-pill"><i class="ti ti-phone"></i>{{ $ec->phone_number }}</span>
                                    @endif
                                    @if($ec->created_at)
                                        <span class="view-pill"><i class="ti ti-calendar"></i>Added {{ $ec->created_at->format('d M Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ Main detail grid ============ -->
                <div class="row g-3 mb-3">
                    <!-- Company / Basic -->
                    <div class="col-lg-6">
                        <div class="detail-card">
                            <div class="card-head">
                                <span class="head-icon"><i class="ti ti-building"></i></span>
                                <span class="head-title">Company Details</span>
                            </div>
                            <div class="card-body">
                                <div class="kv-row">
                                    <span class="k">Company Name</span>
                                    <span class="v">{{ $ec->company_name ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">Contact Person</span>
                                    <span class="v">{{ $ec->contact_person ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">Category</span>
                                    <span class="v">{{ $ec->businessCategory->category_name ?? '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">CSB Type</span>
                                    <span class="v">{{ $ec->csb_type ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">Parent Account</span>
                                    <span class="v">
                                        @if($parentCustomer)
                                            {{ $parentCustomer->first_name }} {{ $parentCustomer->last_name }}
                                            <span class="muted">(#{{ $parentCustomer->id }})</span>
                                        @else
                                            <span class="muted">—</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="col-lg-6">
                        <div class="detail-card">
                            <div class="card-head">
                                <span class="head-icon"><i class="ti ti-phone"></i></span>
                                <span class="head-title">Contact Information</span>
                            </div>
                            <div class="card-body">
                                <div class="kv-row">
                                    <span class="k">Phone</span>
                                    <span class="v">
                                        @if($ec->phone_number)
                                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $ec->phone_number) }}" class="text-decoration-none">{{ $ec->phone_number }}</a>
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">Email</span>
                                    <span class="v">
                                        @if($ec->email)
                                            <a href="mailto:{{ $ec->email }}" class="text-decoration-none">{{ $ec->email }}</a>
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">Email Opt-out</span>
                                    <span class="v">
                                        @if($ec->email_opt_out)
                                            <span class="kv-chip amber"><i class="ti ti-mail-off"></i>Opted out</span>
                                        @else
                                            <span class="kv-chip green"><i class="ti ti-mail"></i>Active</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">Terms Accepted</span>
                                    <span class="v">
                                        @if($ec->terms_accepted)
                                            <span class="kv-chip green"><i class="ti ti-circle-check"></i>Accepted</span>
                                        @else
                                            <span class="kv-chip gray"><i class="ti ti-clock"></i>Not accepted</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">Agreement Accepted</span>
                                    <span class="v">
                                        @if($ec->merchant_agreement_accepted_at)
                                            {{ $ec->merchant_agreement_accepted_at->format('d M Y, h:i A') }}
                                        @else
                                            <span class="muted">—</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KYC / Compliance -->
                    <div class="col-lg-6">
                        <div class="detail-card">
                            <div class="card-head">
                                <span class="head-icon"><i class="ti ti-shield-check"></i></span>
                                <span class="head-title">KYC & Compliance</span>
                            </div>
                            <div class="card-body">
                                <div class="kv-row">
                                    <span class="k">KYC Document</span>
                                    <span class="v">
                                        @if($kycBadge)
                                            <span class="kv-chip {{ $kycBadge['class'] }}"><i class="ti {{ $kycBadge['icon'] }}"></i>{{ $kycBadge['label'] }}</span>
                                        @else
                                            <span class="muted">Not set</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">KYC Number</span>
                                    <span class="v mono">{{ $ec->kyc_number ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">PAN Number</span>
                                    <span class="v mono">{{ $ec->pan_number ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">PAN Holder</span>
                                    <span class="v">{{ $ec->pan_holder_name ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">PAN Date of Birth</span>
                                    <span class="v">{{ $ec->pan_dob ? $ec->pan_dob->format('d M Y') : '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">GST Number</span>
                                    <span class="v mono">{{ $ec->gst_certificate_number ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">GST Business Name</span>
                                    <span class="v">{{ $ec->gst_business_name ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">IEC Number</span>
                                    <span class="v mono">{{ $ec->iec_number ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">Tax Options</span>
                                    <span class="v">
                                        @if($ec->is_gst)
                                            <span class="kv-chip violet"><i class="ti ti-building"></i>GST</span>
                                        @endif
                                        @if($ec->is_lut)
                                            <span class="kv-chip blue"><i class="ti ti-file-text"></i>LUT</span>
                                        @endif
                                        @if(! $ec->is_gst && ! $ec->is_lut)
                                            <span class="muted">—</span>
                                        @endif
                                    </span>
                                </div>
                                @if($ec->is_lut)
                                    <div class="kv-row">
                                        <span class="k">LUT Bond Year</span>
                                        <span class="v">{{ $ec->lut_bond_year ?: '—' }}</span>
                                    </div>
                                    <div class="kv-row">
                                        <span class="k">LUT Expiry</span>
                                        <span class="v">{{ $ec->lut_expiry_date ? $ec->lut_expiry_date->format('d M Y') : '—' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Bank + Billing -->
                    <div class="col-lg-6">
                        <div class="detail-card mb-3">
                            <div class="card-head">
                                <span class="head-icon"><i class="ti ti-building-bank"></i></span>
                                <span class="head-title">Bank Details</span>
                            </div>
                            <div class="card-body">
                                <div class="kv-row">
                                    <span class="k">Bank Type</span>
                                    <span class="v">{{ $ec->bank_type ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">Account Number</span>
                                    <span class="v mono">{{ $ec->bank_account_number ?: '—' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="detail-card">
                            <div class="card-head">
                                <span class="head-icon"><i class="ti ti-receipt"></i></span>
                                <span class="head-title">Billing Details</span>
                            </div>
                            <div class="card-body">
                                <div class="kv-row">
                                    <span class="k">Billing Contact</span>
                                    <span class="v">{{ $ec->billing_contact ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">Billing Email</span>
                                    <span class="v">{{ $ec->billing_email ?: '—' }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="k">Billing Address</span>
                                    <span class="v">{{ $ec->billing_address ?: '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Addresses -->
                    <div class="col-lg-12">
                        <div class="detail-card">
                            <div class="card-head">
                                <span class="head-icon"><i class="ti ti-map-pin"></i></span>
                                <span class="head-title">Saved Addresses ({{ count($displayAddresses) }})</span>
                            </div>
                            <div class="card-body">
                                @forelse($displayAddresses as $addrIndex => $displayAddress)
                                    @php
                                        $isPrimary = ! empty($displayAddress['is_primary']);
                                        $extraLines = collect([$displayAddress['address_line2'], $displayAddress['address_line3']])->filter();
                                        $cityFull = trim(trim((string) $displayAddress['city'] . ', ' . (string) $displayAddress['state'], ', '));
                                        if (! empty($displayAddress['pincode'])) {
                                            $cityFull = $cityFull !== '' ? $cityFull . ' - ' . (string) $displayAddress['pincode'] : (string) $displayAddress['pincode'];
                                        }
                                    @endphp
                                    <div class="addr-box {{ $isPrimary ? 'is-primary' : '' }}">
                                        <div class="addr-tag {{ $isPrimary ? '' : 'muted' }}">
                                            <i class="ti {{ $isPrimary ? 'ti-star' : 'ti-building-warehouse' }}"></i>
                                            {{ $isPrimary ? 'Primary Address' : 'Address ' . $addrIndex }}
                                        </div>
                                        <div class="addr-line1">{{ $displayAddress['address_line1'] ?: '—' }}</div>
                                        @if($extraLines->isNotEmpty())
                                            <div class="addr-sub">{{ $extraLines->implode(', ') }}</div>
                                        @endif
                                        <div class="addr-sub">{{ $cityFull ?: '—' }}</div>
                                    </div>
                                @empty
                                    <div class="text-muted small py-2">No address details were provided for this exporter customer.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="col-lg-12">
                        <div class="detail-card">
                            <div class="card-head">
                                <span class="head-icon"><i class="ti ti-files"></i></span>
                                <span class="head-title">Documents ({{ count($uploadedDocuments) }} uploaded)</span>
                            </div>
                            <div class="card-body">
                                @if(count($uploadedDocuments) === 0)
                                    <div class="text-center text-muted py-4">
                                        <i class="ti ti-file-off" style="font-size:42px;color:#cbd5e1;"></i>
                                        <div class="fw-semibold mt-2">No documents uploaded</div>
                                        <div class="small">This exporter customer has not uploaded any documents yet.</div>
                                    </div>
                                @else
                                    <div class="row g-3">
                                        @foreach($documents as $docKey => $document)
                                            @if(! empty($document['url']))
                                                @php
                                                    $docFile = $docName($document['url']);
                                                    $docImage = $isImageDoc($document['url']);
                                                @endphp
                                                <div class="col-sm-6 col-md-4 col-xl-3">
                                                    <div class="doc-card">
                                                        <div class="doc-preview">
                                                            @if($docImage)
                                                                <img src="{{ $document['url'] }}" alt="{{ $document['label'] }}" loading="lazy">
                                                            @else
                                                                <i class="ti {{ $document['icon'] }} doc-big-icon {{ str_contains(strtolower((string) $docFile), '.pdf') ? 'pdf' : '' }}"></i>
                                                            @endif
                                                        </div>
                                                        <div class="doc-body">
                                                            <div class="doc-name-label">
                                                                <i class="ti {{ $document['icon'] }}"></i>{{ $document['label'] }}
                                                            </div>
                                                            @if($docFile)
                                                                <div class="doc-file" title="{{ $docFile }}">{{ $docFile }}</div>
                                                            @endif
                                                            <div class="doc-actions">
                                                                <a href="{{ $document['url'] }}"
                                                                   class="btn btn-sm btn-outline-primary document-preview-link"
                                                                   data-doc-label="{{ $document['label'] }}"
                                                                   title="Preview {{ $document['label'] }}">
                                                                    <i class="ti ti-eye"></i> View
                                                                </a>
                                                                <a href="{{ $document['url'] }}" target="_blank" rel="noopener"
                                                                   class="btn btn-sm btn-outline-success" title="Download {{ $document['label'] }}">
                                                                    <i class="ti ti-download"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
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

    <!-- Document Preview Modal -->
    <div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-labelledby="documentPreviewTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentPreviewTitle"><i class="ti ti-file-search me-2"></i>Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="height:min(78vh, 900px);min-height:420px;">
                    <iframe id="documentPreviewFrame" title="Document preview" loading="lazy" style="width:100%;height:100%;border:0;"></iframe>
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

    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var documentPreviewElement = document.getElementById('documentPreviewModal');
            var documentPreviewFrame = document.getElementById('documentPreviewFrame');
            var documentPreviewTitle = document.getElementById('documentPreviewTitle');
            var documentPreviewModal = bootstrap.Modal.getOrCreateInstance(documentPreviewElement);

            document.querySelectorAll('.document-preview-link').forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    documentPreviewTitle.textContent = link.dataset.docLabel || 'Document Preview';
                    documentPreviewFrame.src = link.href;
                    documentPreviewModal.show();
                });
            });

            documentPreviewElement.addEventListener('hidden.bs.modal', function() {
                documentPreviewFrame.removeAttribute('src');
                documentPreviewTitle.textContent = 'Document Preview';
            });
        });
    </script>

</body>
</html>
