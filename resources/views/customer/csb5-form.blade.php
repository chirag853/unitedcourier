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
                <link rel="stylesheet" href="{{ asset('css/csb5-form.css') }}?v={{ filemtime(public_path('css/csb5-form.css')) ?: 1 }}">

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
                                            name="ad_code" required
                                            value="{{ old('ad_code', $csbForm->ad_code ?? '') }}">
                                        <i class="fas fa-barcode"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="section-label">IEC Number</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom" placeholder="Enter IEC *"
                                            name="iec_number" id="iecNumber" required
                                            value="{{ old('iec_number', $csbForm->iec_number ?? '') }}">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="section-label">AD Code Document</label>
                                    <div class="doc-item compact" id="adCodeDocContainer">
                                        <div class="doc-meta">
                                            <div>
                                                <span class="doc-name">AD Code File</span>
                                                <div id="adCodeFileInfo" class="file-status">Selected: <span
                                                        id="adCodeFileNameDisplay">file</span></div>
                                            </div>
                                        </div>
                                        <div class="text-end d-flex align-items-center">
                                            <input type="file" id="adCodeFileInput" name="ad_code_document"
                                                style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                                onchange="handleDocSelect(this, 'adCodeFileNameDisplay', 'adCodeFileInfo', 'adCodeRemoveFile', '.adCodeUploadBtn', '#adCodeDocContainer');">
                                            <button type="button"
                                                class="link-alt border-0 bg-transparent adCodeUploadBtn"
                                                onclick="document.getElementById('adCodeFileInput').click();">
                                                <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                            </button>
                                            <span class="text-danger-alt adCodeRemoveFile" style="display: none;"
                                                onclick="clearDocInput('adCodeFileInput', 'adCodeFileNameDisplay', 'adCodeFileInfo', 'adCodeRemoveFile', '.adCodeUploadBtn', '#adCodeDocContainer');"><i
                                                    class="fas fa-trash-alt"></i> Remove</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title-alt">GST Details</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="section-label">GST Certificate Number</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom"
                                            placeholder="Enter GST Certificate Number *" name="gst_certificate_number"
                                            id="gstCertificateNumber" required
                                            value="{{ old('gst_certificate_number', $csbForm->gst_certificate_number ?? '') }}">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="section-label">GST Document</label>
                                    <div class="doc-item compact" id="gstDocContainer">
                                        <div class="doc-meta">
                                            <div>
                                                <span class="doc-name">GST File</span>
                                                <div id="gstFileInfo" class="file-status">Selected: <span
                                                        id="gstFileNameDisplay">file.pdf</span></div>
                                            </div>
                                        </div>
                                        <div class="text-end d-flex align-items-center">
                                            <input type="file" id="gstFileInput" name="gst_document"
                                                style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                                onchange="handleDocSelect(this, 'gstFileNameDisplay', 'gstFileInfo', 'gstRemoveFile', '.gstUploadBtn', '#gstDocContainer');">
                                            <button type="button" class="link-alt border-0 bg-transparent gstUploadBtn"
                                                onclick="document.getElementById('gstFileInput').click();">
                                                <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                            </button>
                                            <span class="text-danger-alt gstRemoveFile" style="display: none;"
                                                onclick="clearDocInput('gstFileInput', 'gstFileNameDisplay', 'gstFileInfo', 'gstRemoveFile', '.gstUploadBtn', '#gstDocContainer');"><i
                                                    class="fas fa-trash-alt"></i> Remove</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="section-label">GST Certificate</label>
                                    <div class="doc-item compact" id="gstCertDocContainer">
                                        <div class="doc-meta">
                                            <div>
                                                <span class="doc-name">GST Certificate</span>
                                                <div id="gstCertFileInfo" class="file-status">Selected: <span
                                                        id="gstCertFileNameDisplay">file.pdf</span></div>
                                            </div>
                                        </div>
                                        <div class="text-end d-flex align-items-center">
                                            <input type="file" id="gstCertFileInput" name="gst_certificate_document"
                                                style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                                onchange="handleDocSelect(this, 'gstCertFileNameDisplay', 'gstCertFileInfo', 'gstCertRemoveFile', '.gstCertUploadBtn', '#gstCertDocContainer');">
                                            <button type="button"
                                                class="link-alt border-0 bg-transparent gstCertUploadBtn"
                                                onclick="document.getElementById('gstCertFileInput').click();">
                                                <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                            </button>
                                            <span class="text-danger-alt gstCertRemoveFile" style="display: none;"
                                                onclick="clearDocInput('gstCertFileInput', 'gstCertFileNameDisplay', 'gstCertFileInfo', 'gstCertRemoveFile', '.gstCertUploadBtn', '#gstCertDocContainer');"><i
                                                    class="fas fa-trash-alt"></i> Remove</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="section-label">IEC Document</label>
                                    <div class="doc-item compact" id="iecDocContainer">
                                        <div class="doc-meta">
                                            <div>
                                                <span class="doc-name">IEC File</span>
                                                <div id="iecFileInfo" class="file-status">Selected: <span
                                                        id="iecFileNameDisplay">file.pdf</span></div>
                                            </div>
                                        </div>
                                        <div class="text-end d-flex align-items-center">
                                            <input type="file" id="iecFileInput" name="iec_document"
                                                style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                                onchange="handleDocSelect(this, 'iecFileNameDisplay', 'iecFileInfo', 'iecRemoveFile', '.iecUploadBtn', '#iecDocContainer');">
                                            <button type="button"
                                                class="link-alt border-0 bg-transparent iecUploadBtn"
                                                onclick="document.getElementById('iecFileInput').click();">
                                                <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                            </button>
                                            <span class="text-danger-alt iecRemoveFile" style="display: none;"
                                                onclick="clearDocInput('iecFileInput', 'iecFileNameDisplay', 'iecFileInfo', 'iecRemoveFile', '.iecUploadBtn', '#iecDocContainer');"><i
                                                    class="fas fa-trash-alt"></i> Remove</span>
                                        </div>
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
                                            required
                                            value="{{ old('bank_account_number', $csbForm->bank_account_number ?? '') }}">
                                        <i class="fas fa-university"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="section-label">Bank Type</label>
                                    <div class="input-wrapper select-wrapper">
                                        <select class="input-custom" name="bank_type" id="bankType" required>
                                            <option value="" disabled {{ empty(old('bank_type', $csbForm->bank_type ?? '')) ? 'selected' : '' }}>Select Bank Type *</option>
                                            <option value="private" {{ (old('bank_type', $csbForm->bank_type ?? '') === 'private') ? 'selected' : '' }}>Private</option>
                                            <option value="government" {{ (old('bank_type', $csbForm->bank_type ?? '') === 'government') ? 'selected' : '' }}>Government</option>
                                        </select>
                                        <i class="fas fa-landmark"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title-alt">LUT Details</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="section-label">LUT Expiry Date</label>
                                    <div class="input-wrapper">
                                        <input type="date" class="input-custom"
                                            placeholder="Select LUT Expiry Date" name="lut_expiry_date"
                                            id="lutExpiryDate"
                                            value="{{ old('lut_expiry_date', $csbForm->lut_expiry_date ?? '') }}">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="section-label">LUT Bond Year</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom"
                                            placeholder="Enter LUT Bond Year (e.g. 2026-27)" name="lut_bond_year"
                                            id="lutBondYear" maxlength="10"
                                            value="{{ old('lut_bond_year', $csbForm->lut_bond_year ?? '') }}">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title-alt">Aadhaar Verification</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="section-label">Aadhaar Number</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom"
                                            placeholder="Enter 12-digit Aadhaar Number *" name="aadhar_number"
                                            id="businessAadharNumber" maxlength="12" inputmode="numeric"
                                            value="{{ old('aadhar_number', $csbForm->aadhar_number ?? ($customer->aadhar_number ?? '')) }}"
                                            required>
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <button type="button" class="btn-verify mt-1" id="businessAadharVerifyBtn">
                                        <i class="fas fa-shield-halved me-1"></i> Verify Aadhaar
                                    </button>
                                    <span class="verified-badge ms-2" id="businessAadharVerifiedBadge" style="display: none;">
                                        <i class="fas fa-circle-check me-1"></i> Verified
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <label class="section-label">Aadhaar Document</label>
                                    <div class="doc-item compact" id="businessAadharDocContainer">
                                        <div class="doc-meta">
                                            <div>
                                                <span class="doc-name">Aadhaar Card</span>
                                                <div id="businessAadharFileInfo" class="file-status">Selected: <span
                                                        id="businessAadharFileNameDisplay">file</span></div>
                                            </div>
                                        </div>
                                        <div class="text-end d-flex align-items-center">
                                            <input type="file" id="businessAadharFileInput" name="aadhar_document"
                                                style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                                onchange="handleDocSelect(this, 'businessAadharFileNameDisplay', 'businessAadharFileInfo', 'businessAadharRemoveFile', '.businessAadharUploadBtn', '#businessAadharDocContainer');">
                                            <button type="button"
                                                class="link-alt border-0 bg-transparent businessAadharUploadBtn"
                                                onclick="document.getElementById('businessAadharFileInput').click();">
                                                <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                            </button>
                                            <span class="text-danger-alt businessAadharRemoveFile" style="display: none;"
                                                onclick="clearDocInput('businessAadharFileInput', 'businessAadharFileNameDisplay', 'businessAadharFileInfo', 'businessAadharRemoveFile', '.businessAadharUploadBtn', '#businessAadharDocContainer');"><i
                                                    class="fas fa-trash-alt"></i> Remove</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title-alt">Authorized Signature</div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="section-label">Signature (with Company Stamp)</label>
                                    <div class="doc-item" id="businessSignatureDocContainer">
                                        <div class="doc-meta">
                                            <div>
                                                <div class="d-flex align-items-center">
                                                    <span class="doc-name">Authorized Signature</span>
                                                    <i class="fas fa-info-circle info-circle ms-2"
                                                        title="Upload authorized signature with company stamp"></i>
                                                </div>
                                                <div id="businessSignatureFileInfo" class="file-status">Selected: <span
                                                        id="businessSignatureFileNameDisplay">file</span></div>
                                            </div>
                                        </div>
                                        <div class="text-end d-flex align-items-center">
                                            <input type="file" id="businessSignatureFileInput" name="signature_document"
                                                style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                                onchange="handleDocSelect(this, 'businessSignatureFileNameDisplay', 'businessSignatureFileInfo', 'businessSignatureRemoveFile', '.businessSignatureUploadBtn', '#businessSignatureDocContainer');">
                                            <button type="button"
                                                class="link-alt border-0 bg-transparent businessSignatureUploadBtn"
                                                onclick="document.getElementById('businessSignatureFileInput').click();">
                                                <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                            </button>
                                            <span class="text-danger-alt businessSignatureRemoveFile" style="display: none;"
                                                onclick="clearDocInput('businessSignatureFileInput', 'businessSignatureFileNameDisplay', 'businessSignatureFileInfo', 'businessSignatureRemoveFile', '.businessSignatureUploadBtn', '#businessSignatureDocContainer');"><i
                                                    class="fas fa-trash-alt"></i> Remove</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title-alt">Document List</div>
                            <!-- LUT Document Upload + Verify -->
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
                                    <button type="button" id="lutVerifyBtn" class="btn-verify ms-2" disabled>
                                        <i class="fas fa-shield-halved me-1"></i> Verify
                                    </button>
                                    <span id="lutVerifiedBadge" class="verified-badge" style="display: none;">
                                        <i class="fas fa-circle-check me-1"></i> Verified
                                    </span>
                                    <input type="hidden" name="lut_verified" id="lutVerifiedInput" value="0">
                                </div>
                            </div>

                            <div class="section-title-alt">Billing Details</div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="section-label">Billing Address (from GST)</label>
                                    <div class="input-wrapper">
                                        <textarea class="input-custom" rows="3"
                                            placeholder="Enter Billing Address *" name="billing_address"
                                            style="padding-left: 48px; padding-top: 16px;"
                                            required>{{ old('billing_address', $csbForm->billing_address ?? '') }}</textarea>
                                        <i class="fas fa-map-marker-alt" style="top: 22px;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="section-label">Billing GST</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom"
                                            placeholder="Enter Billing GST Number" name="billing_gst" maxlength="15"
                                            style="text-transform: uppercase;"
                                            value="{{ old('billing_gst', $csbForm->billing_gst ?? '') }}">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="section-label">Billing Contact Number</label>
                                    <div class="input-wrapper">
                                        <input type="tel" class="input-custom"
                                            placeholder="Enter Contact Number *" name="billing_contact"
                                            maxlength="20" inputmode="numeric"
                                            value="{{ old('billing_contact', $csbForm->billing_contact ?? '') }}"
                                            required>
                                        <i class="fas fa-phone"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="section-label">Billing Email</label>
                                    <div class="input-wrapper">
                                        <input type="email" class="input-custom"
                                            placeholder="Enter Billing Email *" name="billing_email"
                                            value="{{ old('billing_email', $csbForm->billing_email ?? '') }}"
                                            required>
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title-alt">Merchant Agreement</div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="section-label">Signed Merchant Agreement (with Company Stamp & Signature)</label>
                                    <div class="doc-item" id="businessMerchantAgreementContainer">
                                        <div class="doc-meta">
                                            <div>
                                                <div class="d-flex align-items-center">
                                                    <span class="doc-name">Merchant Agreement (PDF)</span>
                                                    <i class="fas fa-info-circle info-circle ms-2"
                                                        title="Upload the signed merchant agreement with company stamp and authorized signature"></i>
                                                </div>
                                                <div id="businessMerchantAgreementFileInfo" class="file-status">Selected: <span
                                                        id="businessMerchantAgreementFileNameDisplay">file</span></div>
                                            </div>
                                        </div>
                                        <div class="text-end d-flex align-items-center">
                                            <input type="file" id="businessMerchantAgreementFileInput" name="merchant_agreement"
                                                style="display: none;" accept=".pdf"
                                                onchange="handleDocSelect(this, 'businessMerchantAgreementFileNameDisplay', 'businessMerchantAgreementFileInfo', 'businessMerchantAgreementRemoveFile', '.businessMerchantAgreementUploadBtn', '#businessMerchantAgreementContainer');">
                                            <button type="button"
                                                class="link-alt border-0 bg-transparent businessMerchantAgreementUploadBtn"
                                                onclick="document.getElementById('businessMerchantAgreementFileInput').click();">
                                                <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                            </button>
                                            <span class="text-danger-alt businessMerchantAgreementRemoveFile" style="display: none;"
                                                onclick="clearDocInput('businessMerchantAgreementFileInput', 'businessMerchantAgreementFileNameDisplay', 'businessMerchantAgreementFileInfo', 'businessMerchantAgreementRemoveFile', '.businessMerchantAgreementUploadBtn', '#businessMerchantAgreementContainer');"><i
                                                    class="fas fa-trash-alt"></i> Remove</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="agreement-box mt-4">
                                <div class="form-check d-flex align-items-start mb-3">
                                    <input class="form-check-input mt-1" type="checkbox" id="businessTermsAccepted"
                                        name="terms_accepted" value="1" required
                                        {{ old('terms_accepted') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="businessTermsAccepted">
                                        I, <strong>{{ $customer->name ?? 'the authorized signatory' }}</strong>,
                                        on behalf of the company, hereby declare that the information provided above is true
                                        and correct to the best of my knowledge. I agree to the terms and conditions of
                                        United Courier Worldwide and authorize the use of the submitted documents for KYC
                                        verification purposes.
                                    </label>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center justify-content-between mt-5">
                                <!-- <button type="button" class="btn-back">BACK</button> -->
                                <div class="d-flex align-items-center gap-4">
                                    <a href="{{ route('customer.kyc.personal') }}" class="link-alt">Continue with CSBIV</a>
                                    <button type="submit" class="btn-gradient">CONTINUE</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- CSB5 Form Custom JS -->
                <script>
                    // Inline helpers so the new upload widgets work even if the
                    // external csb5-form.js is served from a stale cache.
                    function handleDocSelect(input, nameId, infoId, removeClass, uploadBtnSel, containerSel) {
                        if (input.files && input.files.length > 0) {
                            var name = input.files[0].name;
                            var nameEl = document.getElementById(nameId);
                            if (nameEl) { nameEl.textContent = name; }
                            var infoEl = document.getElementById(infoId);
                            if (infoEl) { infoEl.style.display = 'block'; }
                            var removeEl = document.querySelector('.' + removeClass);
                            if (removeEl) { removeEl.style.display = 'inline-block'; }
                            var uploadBtn = document.querySelector(uploadBtnSel);
                            if (uploadBtn) { uploadBtn.style.display = 'none'; }
                            var container = document.querySelector(containerSel);
                            if (container) { container.classList.add('has-file'); }
                        }
                    }
                    function clearDocInput(inputId, nameId, infoId, removeClass, uploadBtnSel, containerSel) {
                        var input = document.getElementById(inputId);
                        if (input) { input.value = ''; }
                        var infoEl = document.getElementById(infoId);
                        if (infoEl) { infoEl.style.display = 'none'; }
                        var removeEl = document.querySelector('.' + removeClass);
                        if (removeEl) { removeEl.style.display = 'none'; }
                        var uploadBtn = document.querySelector(uploadBtnSel);
                        if (uploadBtn) { uploadBtn.style.display = 'inline-block'; }
                        var container = document.querySelector(containerSel);
                        if (container) { container.classList.remove('has-file'); }
                    }
                </script>
                <script src="{{ asset('js/csb5-form.js') }}?v={{ filemtime(public_path('js/csb5-form.js')) ?: 1 }}"></script>


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