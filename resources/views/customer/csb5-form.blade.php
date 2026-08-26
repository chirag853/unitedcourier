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

                        <form id="csbvForm" action="{{ route('customer.csb5-form.standalone.store') }}" method="POST"
                            enctype="multipart/form-data" novalidate>
                            @csrf
                            {{-- Compatibility field keeps CSB-V fixed for this standalone form. --}}
                            <input type="checkbox" id="csbvToggle" name="is_csb_v" value="1" checked hidden
                                aria-hidden="true" tabindex="-1">

                            @php
                                $savedGstNumber = old('gst_certificate_number', $csbForm->gst_certificate_number ?? ($verifiedGstSource?->gst_number ?? ''));
                                $savedGstBusinessName = old('gst_business_name', $csbForm->gst_business_name ?? ($verifiedGstSource?->organization_name ?? ''));
                            @endphp

                            <div class="section-title-alt">Select Tax Type <span class="text-danger">*</span></div>
                            <p class="text-muted small mb-2">Select GST, LUT, or both. At least one option is required.</p>
                            <div class="d-flex flex-wrap gap-4 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gstType" name="is_gst"
                                        value="1" {{ old('is_gst', $csbForm->is_gst ?? false) ? 'checked' : '' }}
                                        onchange="toggleCsbTaxSections();">
                                    <label class="form-check-label fw-bold" for="gstType">GST</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="lutType" name="is_lut"
                                        value="1" {{ old('is_lut', $csbForm->is_lut ?? false) ? 'checked' : '' }}
                                        onchange="toggleCsbTaxSections();">
                                    <label class="form-check-label fw-bold" for="lutType">LUT (Against Bond or UT)</label>
                                </div>
                            </div>

                            <div id="gstSectionWrapper">
                                <div class="section-title-alt">GST Registration</div>
                                <p class="text-muted small mb-2">
                                    Enter and verify your GSTIN and registered business name through Cashfree.
                                </p>
                                <div class="row g-3 mb-2" id="csbGstSection"
                                data-gst-required="{{ $csbGstRequired ? '1' : '0' }}"
                                data-gst-reusable="0"
                                data-verify-url="{{ route('customer.verify.gst') }}">
                                <div class="col-md-6">
                                    <label class="section-label" for="csbGstNumber">GSTIN</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom" id="csbGstNumber"
                                            name="gst_certificate_number" maxlength="15" inputmode="text"
                                            placeholder="Enter 15-character GSTIN *"
                                            value="{{ $savedGstNumber }}">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="section-label" for="csbGstBusinessName">Registered Business Name</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom" id="csbGstBusinessName"
                                            name="gst_business_name" maxlength="255"
                                            placeholder="Enter registered business name *"
                                            value="{{ $savedGstBusinessName }}">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <input type="hidden" id="csbGstCertPath"
                                    value="{{ $csbForm->gst_certificate_document ?? ($csbForm->gst_document ?? ($verifiedGstSource->gst_certificate_document ?? '')) }}">
                                <div class="col-12">
                                    <label class="section-label" for="csbGstCertificate">GST Certificate PDF</label>
                                    <div class="doc-item compact" id="csbGstDocContainer">
                                        <div class="doc-meta">
                                            <span class="doc-name">GST Certificate (PDF)</span>
                                            <div id="csbGstFileInfo" class="file-status">Selected: <span id="csbGstFileName">file.pdf</span></div>
                                        </div>
                                        <div class="text-end d-flex align-items-center">
                                            <input type="file" id="csbGstCertificate" name="gst_certificate_document"
                                                style="display: none;" accept=".pdf,application/pdf"
                                                onchange="handleDocSelect(this, 'csbGstFileName', 'csbGstFileInfo', 'csbGstRemoveFile', '.csbGstUploadBtn', '#csbGstDocContainer'); setCsb5GstDirty();">
                                            <button type="button" id="csbGstUploadBtn" class="link-alt border-0 bg-transparent csbGstUploadBtn"
                                                onclick="document.getElementById('csbGstCertificate').click();">
                                                <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                            </button>
                                            <span id="csbGstRemoveFile" class="text-danger-alt csbGstRemoveFile" style="display: none;"
                                                onclick="clearDocInput('csbGstCertificate', 'csbGstFileName', 'csbGstFileInfo', 'csbGstRemoveFile', '.csbGstUploadBtn', '#csbGstDocContainer'); setCsb5GstDirty();"><i
                                                    class="fas fa-trash-alt"></i> Remove</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div id="csbGstVerificationStatus" class="small text-muted" role="status">
                                        Click VERIFY GST to validate your details through Cashfree before submission.
                                    </div>
                                    <button type="button" id="verifyCsbGstBtn" class="btn-gradient mt-2">
                                        <i class="fas fa-shield-alt me-1"></i> VERIFY GST
                                    </button>
                                </div>
                                </div>
                            </div>

                            <div class="section-title-alt">Export Codes</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="section-label">AD Code</label>

                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom" placeholder="Enter 14-digit AD Code *"
                                            name="ad_code" required inputmode="numeric" maxlength="14"
                                            pattern="[0-9]{14}" title="AD Code must be exactly 14 digits"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0, 14)"
                                            value="{{ old('ad_code', $csbForm->ad_code ?? '') }}">
                                    <small class="text-muted">AD Code must be exactly 14 numeric digits.</small>

                                        <i class="fas fa-barcode"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="section-label">IEC Number</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom" placeholder="Enter 10-character IEC *"
                                            name="iec_number" id="iecNumber" required maxlength="10"
                                            pattern="[A-Za-z0-9]{10}" title="IEC Number must be exactly 10 letters or digits"
                                            oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(0, 10)"
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
                                            <input type="file" id="adCodeFileInput" name="ad_code_document" required
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
                                            <input type="file" id="iecFileInput" name="iec_document" required
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
                                            required inputmode="numeric" minlength="9" maxlength="18" pattern="[0-9]{9,18}"
                                            title="Bank Account Number must contain 9 to 18 digits"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0, 18)"
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

                            @php
                                $savedLutBondYear = old('lut_bond_year', $csbForm->lut_bond_year ?? '');
                                $savedLutStartYear = preg_match('/^(\d{4})-(\d{2})$/', $savedLutBondYear, $lutBondMatches)
                                    ? $lutBondMatches[1]
                                    : '';
                                $savedLutEndYear = '';
                                if ($savedLutStartYear !== '') {
                                    $savedEndYearSuffix = (int) $lutBondMatches[2];
                                    $savedLutEndYear = (intdiv((int) $savedLutStartYear, 100) * 100) + $savedEndYearSuffix;
                                    if ($savedLutEndYear <= (int) $savedLutStartYear) {
                                        $savedLutEndYear += 100;
                                    }
                                    $savedLutEndYear = (string) $savedLutEndYear;
                                }
                                $currentLutYear = now()->year;
                                $lutStartYears = range($currentLutYear, $currentLutYear + 5);
                            @endphp
                            <div id="lutSectionWrapper">
                                <div class="section-title-alt">LUT Details</div>
                                <div class="row g-3" id="lutDetailsSection">
                                <div class="col-md-6">
                                    <label class="section-label" for="lutNumber">LUT Number</label>
                                    <div class="input-wrapper">
                                        <input type="text" class="input-custom" name="lut_number" id="lutNumber"
                                            maxlength="100" placeholder="Enter LUT number"
                                            value="{{ old('lut_number', $csbForm->lut_number ?? '') }}">
                                        <i class="fas fa-hashtag"></i>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="section-label" for="lutBondStartYear">LUT Bond Start Year</label>
                                    <div class="input-wrapper select-wrapper">
                                        <select class="input-custom" id="lutBondStartYear" name="lut_bond_start_year">
                                            <option value="">Select Start Year</option>
                                            @if ($savedLutStartYear !== '' && !in_array((int) $savedLutStartYear, $lutStartYears, true))
                                                <option value="{{ $savedLutStartYear }}" selected>{{ $savedLutStartYear }}</option>
                                            @endif
                                            @foreach ($lutStartYears as $lutStartYear)
                                                <option value="{{ $lutStartYear }}"
                                                    {{ (string) $lutStartYear === $savedLutStartYear ? 'selected' : '' }}>
                                                    {{ $lutStartYear }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="section-label" for="lutBondEndYear">LUT Bond End Year</label>
                                    <div class="input-wrapper select-wrapper">
                                        <select class="input-custom" id="lutBondEndYear" name="lut_bond_end_year"
                                            data-saved-end-year="{{ $savedLutEndYear }}" disabled>
                                            <option value="">Select Start Year First</option>
                                        </select>
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <input type="hidden" name="lut_bond_year" id="lutBondYear"
                                        value="{{ $savedLutBondYear }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="section-label">LUT Expiry Date</label>
                                    <div class="input-wrapper">
                                        <input type="date" class="input-custom"
                                            placeholder="Select LUT Expiry Date" name="lut_expiry_date"
                                            id="lutExpiryDate"
                                            value="{{ old('lut_expiry_date', $csbForm->lut_expiry_date ?? '') }}">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title-alt">Document List</div>
                            <!-- LUT Document Upload -->
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
                            </div>

                            <div class="section-title-alt">Billing Details</div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="section-label">Billing Address</label>
                                    <div class="input-wrapper">
                                        <textarea class="input-custom" rows="3"
                                            placeholder="Enter Billing Address *" name="billing_address"
                                            id="billingAddress"
                                            style="padding-left: 48px; padding-top: 16px;"
                                            required>{{ old('billing_address', $csbForm->billing_address ?? '') }}</textarea>
                                        <i class="fas fa-map-marker-alt" style="top: 22px;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="section-label">Billing Contact Number</label>
                                    <div class="input-wrapper">
                                        <input type="tel" class="input-custom"
                                            placeholder="Enter 10-digit Contact Number *" name="billing_contact"
                                            maxlength="10" inputmode="numeric" pattern="[6-9][0-9]{9}"
                                            title="Contact number must contain 10 digits and start with 6, 7, 8, or 9"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0, 10); if (/^[0-5]/.test(this.value)) { this.value = ''; }"
                                            value="{{ old('billing_contact', $csbForm->billing_contact ?? '') }}"
                                            required>
                                        <i class="fas fa-phone"></i>
                                    </div>
                                </div>
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
                                            <input type="file" id="businessMerchantAgreementFileInput" name="merchant_agreement" required
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

                <!-- CSB5 Form Custom JS (fully inline so it can never be broken by a stale cache) -->
                <script>
                    // ---- Global helpers used by inline onchange/onclick attributes ----
                    function handleDocSelect(input, nameId, infoId, removeClass, uploadBtnSel, containerSel) {
                        if (input.files && input.files.length > 0) {
                            var file = input.files[0];
                            var nameEl = document.getElementById(nameId);
                            if (nameEl) { nameEl.textContent = file.name; }
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

                    // Show GST info only when the GST checkbox is selected and
                    // LUT info only when the LUT checkbox is selected.
                    function toggleCsbTaxSections() {
                        var gstCheckbox = document.getElementById('gstType');
                        var lutCheckbox = document.getElementById('lutType');
                        var gstOn = !!(gstCheckbox && gstCheckbox.checked);
                        var lutOn = !!(lutCheckbox && lutCheckbox.checked);
                        var gstWrap = document.getElementById('gstSectionWrapper');
                        var lutWrap = document.getElementById('lutSectionWrapper');
                        if (gstWrap) {
                            gstWrap.style.display = gstOn ? '' : 'none';
                            gstWrap.querySelectorAll('input, select, button').forEach(function (el) {
                                el.disabled = !gstOn;
                            });
                        }
                        if (lutWrap) {
                            lutWrap.style.display = lutOn ? '' : 'none';
                            lutWrap.querySelectorAll('input, select, button').forEach(function (el) {
                                el.disabled = !lutOn;
                            });
                        }
                    }
                    toggleCsbTaxSections();

                    function setCsb5GstDirty() {
                        var status = document.getElementById('csbGstVerificationStatus');
                        if (status && document.getElementById('csbGstCertificate')) {
                            status.textContent = 'GST details changed. Verify GST again before submission.';
                            status.className = 'small text-muted';
                        }
                    }

                    // ---- Main wiring ----
                    (function initializeCsb5Form() {
                        if (window.__csb5FormInitialized) return;
                        window.__csb5FormInitialized = true;

                        var form = document.getElementById('csbvForm');

                        // --- LUT document upload widget ---
                        try {
                            var lutUploadBtn = document.getElementById('uploadBtn');
                            var lutFileInput = document.getElementById('lutFileInput');
                            var lutFileInfo = document.getElementById('fileInfo');
                            var lutFileName = document.getElementById('fileNameDisplay');
                            var lutRemoveFile = document.getElementById('removeFile');
                            var lutDocContainer = document.getElementById('lutDocContainer');

                            lutUploadBtn && lutUploadBtn.addEventListener('click', function () { lutFileInput.click(); });
                            lutFileInput && lutFileInput.addEventListener('change', function () {
                                if (lutFileInput.files.length > 0) {
                                    lutFileName.textContent = lutFileInput.files[0].name;
                                    lutFileInfo.style.display = 'block';
                                    lutRemoveFile.style.display = 'inline-block';
                                    lutUploadBtn.style.display = 'none';
                                    lutDocContainer.classList.add('has-file');
                                }
                            });
                            lutRemoveFile && lutRemoveFile.addEventListener('click', function () {
                                lutFileInput.value = '';
                                lutFileInfo.style.display = 'none';
                                lutRemoveFile.style.display = 'none';
                                lutUploadBtn.style.display = 'inline-block';
                                lutDocContainer.classList.remove('has-file');
                            });
                        } catch (e) { console.error('[CSB5] LUT upload init failed:', e); }

                        // --- Linked LUT bond years ---
                        try {
                            var startYearSelect = document.getElementById('lutBondStartYear');
                            var endYearSelect = document.getElementById('lutBondEndYear');
                            var combinedYearInput = document.getElementById('lutBondYear');
                            var expiryDateInput = document.getElementById('lutExpiryDate');

                            function syncLutBondYear() {
                                combinedYearInput.value = startYearSelect.value && endYearSelect.value
                                    ? startYearSelect.value + '-' + endYearSelect.value.slice(-2)
                                    : '';
                            }

                            function populateLutBondEndYear(useSavedYear) {
                                var startYear = parseInt(startYearSelect.value, 10);
                                var savedEndYear = useSavedYear ? endYearSelect.getAttribute('data-saved-end-year') : '';
                                endYearSelect.innerHTML = '';

                                if (!startYear) {
                                    endYearSelect.appendChild(new Option('Select Start Year First', ''));
                                    endYearSelect.disabled = true;
                                    expiryDateInput.removeAttribute('min');
                                    syncLutBondYear();
                                    return;
                                }

                                expiryDateInput.min = String(startYear + 1) + '-01-01';
                                if (expiryDateInput.value && expiryDateInput.value < expiryDateInput.min) {
                                    expiryDateInput.value = '';
                                }

                                endYearSelect.appendChild(new Option('Select End Year', ''));
                                for (var yearOffset = 1; yearOffset <= 5; yearOffset++) {
                                    endYearSelect.appendChild(new Option(String(startYear + yearOffset), String(startYear + yearOffset)));
                                }
                                endYearSelect.disabled = false;
                                endYearSelect.value = savedEndYear && endYearSelect.querySelector('option[value="' + savedEndYear + '"]')
                                    ? savedEndYear
                                    : String(startYear + 1);
                                syncLutBondYear();
                            }

                            startYearSelect.addEventListener('change', function () { populateLutBondEndYear(false); });
                            endYearSelect.addEventListener('change', syncLutBondYear);
                            populateLutBondEndYear(true);
                        } catch (e) { console.error('[CSB5] LUT years init failed:', e); }

                        // --- GST verification (same API as the KYC dashboard) ---
                        var csbGstSection = document.getElementById('csbGstSection');
                        var csbGstNumber = document.getElementById('csbGstNumber');
                        var csbGstBusinessName = document.getElementById('csbGstBusinessName');
                        var csbGstCertificate = document.getElementById('csbGstCertificate');
                        var csbGstVerificationStatus = document.getElementById('csbGstVerificationStatus');
                        var verifyCsbGstBtn = document.getElementById('verifyCsbGstBtn');
                        var csbGstVerified = false;

                        function normalizedGst(value) {
                            return String(value || '').replace(/\s+/g, '').toUpperCase();
                        }

                        function setCsbGstVerificationState(verified, message, success) {
                            csbGstVerified = verified;
                            if (!csbGstVerificationStatus) return;
                            csbGstVerificationStatus.textContent = message;
                            csbGstVerificationStatus.className = success ? 'small text-success' : 'small text-muted';
                        }

                        try {
                            [csbGstNumber, csbGstBusinessName].forEach(function (input) {
                                input && input.addEventListener('input', function () {
                                    if (input === csbGstNumber) input.value = normalizedGst(input.value);
                                    setCsbGstVerificationState(false, 'GST details changed. Verify GST again before submission.', false);
                                });
                            });
                        } catch (e) { console.error('[CSB5] GST input init failed:', e); }

                        verifyCsbGstBtn && verifyCsbGstBtn.addEventListener('click', async function () {
                            console.log('[CSB5] VERIFY GST clicked');
                            var gst = normalizedGst(csbGstNumber && csbGstNumber.value);
                            var businessName = ((csbGstBusinessName && csbGstBusinessName.value) || '').trim();
                            var file = csbGstCertificate && csbGstCertificate.files ? csbGstCertificate.files[0] : null;
                            var storedPath = ((document.getElementById('csbGstCertPath') || {}).value || '').trim();

                            if (!/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(gst)) {
                                showCsb5ValidationError('Enter a valid 15-character GSTIN.', csbGstNumber);
                                return;
                            }
                            if (!businessName) {
                                showCsb5ValidationError('Enter the registered Business Name.', csbGstBusinessName);
                                return;
                            }
                            if (!file && !storedPath) {
                                showCsb5ValidationError('Please upload the GST Certificate PDF.', csbGstCertificate);
                                return;
                            }
                            if (file && !validateCsb5File(form, 'gst_certificate_document', 'the GST Certificate', ['pdf'], 5)) return;

                            // Same payload shape as the KYC "Verify GST" request.
                            var formData = new FormData();
                            formData.append('gst_number', gst);
                            formData.append('business_name', businessName);
                            if (file) {
                                formData.append('gst_certificate_document', file);
                            } else {
                                formData.append('gst_certificate_document_path', storedPath);
                            }

                            verifyCsbGstBtn.disabled = true;
                            verifyCsbGstBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                            setCsbGstVerificationState(false, 'Verifying GST through Cashfree...', false);

                            try {
                                var response = await fetch(csbGstSection.dataset.verifyUrl, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                });
                                var contentType = response.headers.get('content-type');
                                var data = contentType && contentType.includes('application/json')
                                    ? await response.json()
                                    : await response.text().then(function (text) {
                                        console.error('[CSB5] Non-JSON GST response:', text);
                                        throw new Error('Server error (non-JSON response). Please try again.');
                                    });

                                if (!response.ok || !data.success) {
                                    throw new Error(data.message || 'GST verification failed.');
                                }

                                // Success behaviour mirrors the KYC verify flow.
                                csbGstVerified = true;
                                var verifiedBusinessName = (data.business_name || businessName).trim();
                                csbGstNumber.value = normalizedGst(data.gst_number || gst);
                                csbGstBusinessName.value = verifiedBusinessName;

                                if (data.address) {
                                    var billingAddress = document.getElementById('billingAddress');
                                    if (billingAddress) billingAddress.value = data.address;
                                }

                                setCsbGstVerificationState(true, data.message || 'GST number and Business Name verified successfully.', true);
                                verifyCsbGstBtn.innerHTML = '<i class="fas fa-check"></i> Verified';
                                csbGstNumber.readOnly = true;
                                csbGstBusinessName.readOnly = true;
                            } catch (error) {
                                console.error('[CSB5] GST verification error:', error);
                                var message = error && error.message ? error.message : 'GST verification could not be completed.';
                                setCsbGstVerificationState(false, message, false);
                                alert(message);
                                verifyCsbGstBtn.disabled = false;
                                verifyCsbGstBtn.innerHTML = '<i class="fas fa-shield-alt me-1"></i> VERIFY GST';
                            }
                        });

                        // --- Validation helpers ---
                        function showCsb5ValidationError(message, field) {
                            alert(message);
                            if (field) {
                                if (field.type !== 'file') field.focus();
                                var container = field.type === 'file' ? field.closest('.doc-item') : field.closest('.input-wrapper');
                                if (container) container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            return false;
                        }

                        function validateCsb5File(targetForm, name, label, allowedExtensions, maxMb, required) {
                            if (required === undefined) required = true;
                            var input = targetForm.querySelector('[name="' + name + '"]');
                            var fileItem = input && input.files ? input.files[0] : null;
                            if (!fileItem) return required ? showCsb5ValidationError('Please upload ' + label + '.', input) : true;

                            var extension = fileItem.name.indexOf('.') > -1 ? fileItem.name.split('.').pop().toLowerCase() : '';
                            if (allowedExtensions.indexOf(extension) === -1) {
                                return showCsb5ValidationError(label + ' must be a ' + allowedExtensions.join(', ').toUpperCase() + ' file.', input);
                            }
                            var imageOnly = allowedExtensions.every(function (ext) { return ['jpg', 'jpeg', 'png'].indexOf(ext) > -1; });
                            if (imageOnly && ['image/jpeg', 'image/png'].indexOf(fileItem.type) === -1) {
                                return showCsb5ValidationError(label + ' must be a valid JPG, JPEG, or PNG image.', input);
                            }
                            if (fileItem.size > maxMb * 1024 * 1024) {
                                return showCsb5ValidationError(label + ' must not exceed ' + maxMb + ' MB.', input);
                            }
                            return true;
                        }

                        function validateCsb5Form(targetForm) {
                            function fieldValue(name) {
                                var el = targetForm.querySelector('[name="' + name + '"]');
                                return el ? el.value.trim() : '';
                            }
                            var standardDocuments = ['pdf', 'jpg', 'jpeg', 'png'];

                            if (!/^\d{14}$/.test(fieldValue('ad_code'))) return showCsb5ValidationError('AD Code must be exactly 14 numeric digits.', targetForm.querySelector('[name="ad_code"]'));
                            if (!/^[A-Z0-9]{10}$/.test(fieldValue('iec_number').toUpperCase())) return showCsb5ValidationError('IEC Number must be exactly 10 letters or digits.', targetForm.querySelector('[name="iec_number"]'));

                            var gstEnabled = !!(document.getElementById('gstType') || {}).checked;
                            var lutEnabled = !!(document.getElementById('lutType') || {}).checked;
                            if (!gstEnabled && !lutEnabled) return showCsb5ValidationError('Please select GST, LUT, or both before continuing.', document.getElementById('gstType'));

                            if (gstEnabled) {
                                var gstCertInput = targetForm.querySelector('[name="gst_certificate_document"]');
                                var hasGstCertFile = Boolean(gstCertInput && gstCertInput.files && gstCertInput.files[0]);
                                var storedGstCertPath = ((document.getElementById('csbGstCertPath') || {}).value || '').trim();
                                if (!/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(normalizedGst(fieldValue('gst_certificate_number')))) {
                                    return showCsb5ValidationError('Enter a valid 15-character GSTIN.', targetForm.querySelector('[name="gst_certificate_number"]'));
                                }
                                if (!fieldValue('gst_business_name')) return showCsb5ValidationError('Please enter the registered Business Name.', targetForm.querySelector('[name="gst_business_name"]'));
                                if (!hasGstCertFile && !storedGstCertPath) return showCsb5ValidationError('Please upload the GST Certificate PDF.', gstCertInput);
                                if (hasGstCertFile && !validateCsb5File(targetForm, 'gst_certificate_document', 'the GST Certificate', ['pdf'], 5)) return false;
                                if (!csbGstVerified) return showCsb5ValidationError('Verify the GSTIN and Business Name through Cashfree before continuing.', verifyCsbGstBtn);
                            }

                            if (!validateCsb5File(targetForm, 'ad_code_document', 'the AD Code Document', standardDocuments, 5)) return false;
                            if (!validateCsb5File(targetForm, 'iec_document', 'the IEC Document', standardDocuments, 5)) return false;
                            if (!/^\d{9,18}$/.test(fieldValue('bank_account_number'))) return showCsb5ValidationError('Bank Account Number must contain 9 to 18 digits.', targetForm.querySelector('[name="bank_account_number"]'));
                            if (['private', 'government'].indexOf(fieldValue('bank_type')) === -1) return showCsb5ValidationError('Please select a valid Bank Type.', targetForm.querySelector('[name="bank_type"]'));

                            if (lutEnabled) {
                                if (!fieldValue('lut_number')) return showCsb5ValidationError('Please enter the LUT Number.', targetForm.querySelector('[name="lut_number"]'));
                                syncLutBondYear();
                                var startYear = parseInt(fieldValue('lut_bond_start_year'), 10);
                                var endYear = parseInt(fieldValue('lut_bond_end_year'), 10);
                                if (!startYear) return showCsb5ValidationError('Please select the LUT Bond Start Year.', targetForm.querySelector('[name="lut_bond_start_year"]'));
                                if (!endYear) return showCsb5ValidationError('Please select the LUT Bond End Year.', targetForm.querySelector('[name="lut_bond_end_year"]'));
                                if (endYear < startYear + 1 || endYear > startYear + 5) return showCsb5ValidationError('LUT Bond End Year must be within five years after the Start Year.', targetForm.querySelector('[name="lut_bond_end_year"]'));
                                if (!fieldValue('lut_expiry_date')) return showCsb5ValidationError('Please select the LUT Expiry Date.', targetForm.querySelector('[name="lut_expiry_date"]'));
                                if (fieldValue('lut_expiry_date') < (startYear + 1) + '-01-01') return showCsb5ValidationError('LUT Expiry Date must be on or after ' + (startYear + 1) + '-01-01.', targetForm.querySelector('[name="lut_expiry_date"]'));
                                if (!validateCsb5File(targetForm, 'lut_document', 'the LUT Document', ['pdf'], 5)) return false;
                            }

                            if (fieldValue('billing_address').length < 10 || fieldValue('billing_address').length > 1000) return showCsb5ValidationError('Billing Address must contain 10 to 1000 characters.', targetForm.querySelector('[name="billing_address"]'));
                            if (!/^[6-9]\d{9}$/.test(fieldValue('billing_contact'))) return showCsb5ValidationError('Billing Contact Number must contain exactly 10 digits and start with 6, 7, 8, or 9.', targetForm.querySelector('[name="billing_contact"]'));
                            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(fieldValue('billing_email'))) return showCsb5ValidationError('Please enter a valid Billing Email address.', targetForm.querySelector('[name="billing_email"]'));
                            if (!validateCsb5File(targetForm, 'merchant_agreement', 'the signed Merchant Agreement', ['pdf'], 10)) return false;
                            if (!targetForm.querySelector('[name="terms_accepted"]').checked) return showCsb5ValidationError('Please accept the declaration and terms before continuing.', targetForm.querySelector('[name="terms_accepted"]'));
                            return true;
                        }

                        // --- Form Submit ---
                        form.addEventListener('submit', function (e) {
                            e.preventDefault();
                            console.log('[CSB5] Form submit triggered');
                            var btn = form.querySelector('.btn-gradient[type="submit"]') || form.querySelector('.btn-gradient');
                            if (!validateCsb5Form(form)) return;

                            var formData = new FormData(form);
                            formData.set('is_csb_v', '1');
                            formData.set('is_gst', document.getElementById('gstType').checked ? '1' : '0');
                            formData.set('is_lut', document.getElementById('lutType').checked ? '1' : '0');
                            formData.set('lut_verified', '0');

                            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> SUBMITTING...';
                            btn.style.opacity = '0.8';
                            btn.style.pointerEvents = 'none';

                            fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                            })
                                .then(function (response) {
                                    var contentType = response.headers.get('content-type');
                                    return contentType && contentType.includes('application/json')
                                        ? response.json()
                                        : response.text().then(function (text) {
                                            console.error('[CSB5] Non-JSON submit response:', text);
                                            return { success: false, message: 'Server error (non-JSON response). Please try again.' };
                                        });
                                })
                                .then(function (data) {
                                    if (data.success) {
                                        btn.innerHTML = 'SUCCESS';
                                        btn.style.background = '#10b981';
                                        btn.style.opacity = '1';
                                        setTimeout(function () { window.location.href = data.redirect; }, 1000);
                                    } else {
                                        btn.innerHTML = 'CONTINUE';
                                        btn.style.opacity = '1';
                                        btn.style.pointerEvents = 'auto';
                                        if (data.errors) {
                                            var errorMessage = 'Please fix the following errors:\n';
                                            Object.keys(data.errors).forEach(function (key) {
                                                errorMessage += '- ' + data.errors[key][0] + '\n';
                                            });
                                            alert(errorMessage);
                                        } else alert(data.message || 'An error occurred. Please try again.');
                                    }
                                })
                                .catch(function (error) {
                                    console.error('[CSB5] Submit error:', error);
                                    btn.innerHTML = 'CONTINUE';
                                    btn.style.opacity = '1';
                                    btn.style.pointerEvents = 'auto';
                                    alert('An error occurred. Please try again.');
                                });
                        });

                        console.log('[CSB5] Form initialized successfully');
                    })();
                </script>


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