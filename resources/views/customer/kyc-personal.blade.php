<!DOCTYPE html>
<html lang="en">


<!-- Personal KYC (CSB-IV) Wizard -->

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Personal KYC | United Courier Worldwide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Complete your Personal KYC (CSB-IV) onboarding with United Courier Worldwide.">
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/plugins/tabler-icons/tabler-icons.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <!-- Font Awesome -->
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

                <!-- Personal KYC Custom CSS -->
                <link rel="stylesheet" href="{{ asset('css/kyc-personal.css') }}?v={{ filemtime(public_path('css/kyc-personal.css')) ?: 1 }}">

                <!-- card start -->

                <div class="form-wrapper">
                    <div class="kyc-card">
                        <div class="form-header">
                            <h2>Complete <span class="gradient-text">Personal KYC (CSB-IV)</span></h2>
                            <p>Verify your identity to start shipping with United Courier Worldwide.</p>
                        </div>

                        <!-- Wizard Progress Bar -->
                        <div class="wizard-progress">
                            <div class="wizard-steps">
                                <div class="wizard-step active" data-step="1">
                                    <span class="step-number">1</span>
                                    <span class="step-label">Aadhaar</span>
                                </div>
                                <div class="wizard-step" data-step="2">
                                    <span class="step-number">2</span>
                                    <span class="step-label">PAN</span>
                                </div>
                                <div class="wizard-step" data-step="3">
                                    <span class="step-number">3</span>
                                    <span class="step-label">Signature</span>
                                </div>
                                <div class="wizard-step" data-step="4">
                                    <span class="step-number">4</span>
                                    <span class="step-label">Summary</span>
                                </div>
                                <div class="wizard-step" data-step="5">
                                    <span class="step-number">5</span>
                                    <span class="step-label">Billing</span>
                                </div>
                                <div class="wizard-step" data-step="6">
                                    <span class="step-number">6</span>
                                    <span class="step-label">Agreement</span>
                                </div>
                            </div>
                            <div class="wizard-bar">
                                <div class="wizard-bar-fill" id="wizardBarFill" style="width: 16.66%;"></div>
                            </div>
                        </div>

                        <form id="personalKycForm" action="{{ route('customer.kyc.personal.store') }}" method="POST"
                            enctype="multipart/form-data"
                            data-verify-pan-url="{{ route('verify.pan') }}"
                            data-aadhar-verified="{{ (isset($kycDetail) && $kycDetail && $kycDetail->aadhar_verified) ? '1' : '0' }}"
                            data-pan-verified="{{ (isset($kycDetail) && $kycDetail && $kycDetail->pan_verified) ? '1' : '0' }}">
                            @csrf

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <!-- ==================== STEP 1: Aadhaar Verification ==================== -->
                            <div class="wizard-panel active" data-panel="1">
                                <div class="section-title-alt">Aadhaar Verification</div>
                                <p class="step-intro">Enter your 12-digit Aadhaar number and upload both sides of your Aadhaar card.</p>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="section-label">Aadhaar Number</label>
                                        <div class="input-wrapper">
                                            <input type="text" class="input-custom" id="aadharNumber"
                                                placeholder="Enter 12-digit Aadhaar Number *" name="aadhar_number"
                                                maxlength="12" inputmode="numeric"
                                                value="{{ old('aadhar_number', $kycDetail->aadhar_number ?? ($customer->aadhar_number ?? '')) }}"
                                                required>
                                            <i class="fas fa-id-card"></i>
                                        </div>
                                        <button type="button" class="btn-verify mt-1" id="aadharVerifyBtn">
                                            <i class="fas fa-shield-halved me-1"></i> Verify Aadhaar
                                        </button>
                                        <span class="verified-badge ms-2" id="aadharVerifiedBadge" style="display: none;">
                                            <i class="fas fa-circle-check me-1"></i> Verified
                                        </span>
                                    </div>
                                </div>

                                <div class="section-title-alt mt-4">Aadhaar Card Documents</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="section-label">Aadhaar Front</label>
                                        <div class="doc-item compact" id="aadharFrontDocContainer">
                                            <div class="doc-meta">
                                                <div>
                                                    <span class="doc-name">Aadhaar Front</span>
                                                    <div id="aadharFrontFileInfo" class="file-status">Selected: <span
                                                            id="aadharFrontFileNameDisplay">file</span></div>
                                                </div>
                                            </div>
                                            <div class="text-end d-flex align-items-center">
                                                <input type="file" id="aadharFrontFileInput" name="aadhar_front_document"
                                                    style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                                    onchange="handleDocSelect(this, 'aadharFrontFileNameDisplay', 'aadharFrontFileInfo', 'aadharFrontRemoveFile', '.aadharFrontUploadBtn', '#aadharFrontDocContainer');">
                                                <button type="button"
                                                    class="link-alt border-0 bg-transparent aadharFrontUploadBtn"
                                                    onclick="document.getElementById('aadharFrontFileInput').click();">
                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                </button>
                                                <span class="text-danger-alt aadharFrontRemoveFile" style="display: none;"
                                                    onclick="clearDocInput('aadharFrontFileInput', 'aadharFrontFileNameDisplay', 'aadharFrontFileInfo', 'aadharFrontRemoveFile', '.aadharFrontUploadBtn', '#aadharFrontDocContainer');"><i
                                                        class="fas fa-trash-alt"></i> Remove</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="section-label">Aadhaar Back</label>
                                        <div class="doc-item compact" id="aadharBackDocContainer">
                                            <div class="doc-meta">
                                                <div>
                                                    <span class="doc-name">Aadhaar Back</span>
                                                    <div id="aadharBackFileInfo" class="file-status">Selected: <span
                                                            id="aadharBackFileNameDisplay">file</span></div>
                                                </div>
                                            </div>
                                            <div class="text-end d-flex align-items-center">
                                                <input type="file" id="aadharBackFileInput" name="aadhar_back_document"
                                                    style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                                    onchange="handleDocSelect(this, 'aadharBackFileNameDisplay', 'aadharBackFileInfo', 'aadharBackRemoveFile', '.aadharBackUploadBtn', '#aadharBackDocContainer');">
                                                <button type="button"
                                                    class="link-alt border-0 bg-transparent aadharBackUploadBtn"
                                                    onclick="document.getElementById('aadharBackFileInput').click();">
                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                </button>
                                                <span class="text-danger-alt aadharBackRemoveFile" style="display: none;"
                                                    onclick="clearDocInput('aadharBackFileInput', 'aadharBackFileNameDisplay', 'aadharBackFileInfo', 'aadharBackRemoveFile', '.aadharBackUploadBtn', '#aadharBackDocContainer');"><i
                                                        class="fas fa-trash-alt"></i> Remove</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-title-alt mt-4">Aadhaar Address</div>
                                <p class="step-intro">Enter the address as printed on your Aadhaar card. This will be used for billing details.</p>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="section-label">Address (as per Aadhaar)</label>
                                        <div class="input-wrapper">
                                            <textarea class="input-custom" id="aadharAddress"
                                                placeholder="Enter your full address as printed on the Aadhaar card *"
                                                name="aadhar_address" rows="3" maxlength="1000"
                                                required>{{ old('aadhar_address', $kycDetail->aadhar_address ?? '') }}</textarea>
                                            <i class="fas fa-location-dot"></i>
                                        </div>
                                        <small class="text-muted">This address will be auto-filled into your billing details in Step 5.</small>
                                    </div>
                                </div>

                                <div class="wizard-nav">
                                    <div></div>
                                    <button type="button" class="btn-gradient wizard-next" data-next="2">NEXT <i class="fas fa-arrow-right ms-2"></i></button>
                                </div>
                            </div>

                            <!-- ==================== STEP 2: PAN Verification ==================== -->
                            <div class="wizard-panel" data-panel="2">
                                <div class="section-title-alt">PAN Verification</div>
                                <p class="step-intro">Enter your PAN number, holder name, date of birth, and upload the PAN card.</p>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="section-label">PAN Number</label>
                                        <div class="input-wrapper">
                                            <input type="text" class="input-custom" id="panNumber"
                                                placeholder="Enter PAN Number (e.g. ABCDE1234F) *" name="pan_number"
                                                maxlength="10" style="text-transform: uppercase;"
                                                value="{{ old('pan_number', $kycDetail->pan_number ?? ($customer->pan_number ?? '')) }}"
                                                required>
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <button type="button" class="btn-verify mt-1" id="panVerifyBtn">
                                            <i class="fas fa-shield-halved me-1"></i> Verify PAN
                                        </button>
                                        <span class="verified-badge ms-2" id="panVerifiedBadge" style="display: none;">
                                            <i class="fas fa-circle-check me-1"></i> Verified
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="section-label">PAN Holder Name</label>
                                        <div class="input-wrapper">
                                            <input type="text" class="input-custom" id="panHolderName"
                                                placeholder="Enter Name as on PAN *" name="pan_holder_name"
                                                value="{{ old('pan_holder_name', $kycDetail->pan_holder_name ?? '') }}"
                                                required>
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="section-label">Date of Birth (as per PAN)</label>
                                        <div class="input-wrapper">
                                            <input type="date" class="input-custom" id="panDob"
                                                placeholder="Select Date of Birth *" name="pan_dob"
                                                value="{{ old('pan_dob', $kycDetail->pan_dob ?? '') }}"
                                                required>
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="section-label">PAN Document</label>
                                        <div class="doc-item compact" id="panDocContainer">
                                            <div class="doc-meta">
                                                <div>
                                                    <span class="doc-name">PAN Card</span>
                                                    <div id="panFileInfo" class="file-status">Selected: <span
                                                            id="panFileNameDisplay">file</span></div>
                                                </div>
                                            </div>
                                            <div class="text-end d-flex align-items-center">
                                                <input type="file" id="panFileInput" name="pan_document"
                                                    style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                                    onchange="handleDocSelect(this, 'panFileNameDisplay', 'panFileInfo', 'panRemoveFile', '.panUploadBtn', '#panDocContainer');">
                                                <button type="button"
                                                    class="link-alt border-0 bg-transparent panUploadBtn"
                                                    onclick="document.getElementById('panFileInput').click();">
                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                </button>
                                                <span class="text-danger-alt panRemoveFile" style="display: none;"
                                                    onclick="clearDocInput('panFileInput', 'panFileNameDisplay', 'panFileInfo', 'panRemoveFile', '.panUploadBtn', '#panDocContainer');"><i
                                                        class="fas fa-trash-alt"></i> Remove</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="wizard-nav">
                                    <button type="button" class="btn-back wizard-prev" data-prev="1"><i class="fas fa-arrow-left me-2"></i> BACK</button>
                                    <button type="button" class="btn-gradient wizard-next" data-next="3">NEXT <i class="fas fa-arrow-right ms-2"></i></button>
                                </div>
                            </div>

                            <!-- ==================== STEP 3: Signature Upload ==================== -->
                            <div class="wizard-panel" data-panel="3">
                                <div class="section-title-alt">Signature Upload</div>
                                <p class="step-intro">Upload your signature. This will be used on shipping declarations and agreements.</p>

                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="section-label">Your Signature</label>
                                        <div class="doc-item" id="signatureDocContainer">
                                            <div class="doc-meta">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="doc-name">Signature</span>
                                                        <i class="fas fa-info-circle info-circle ms-2"
                                                            title="Upload a clear image of your signature on white background"></i>
                                                    </div>
                                                    <div id="signatureFileInfo" class="file-status">Selected: <span
                                                            id="signatureFileNameDisplay">file</span></div>
                                                </div>
                                            </div>
                                            <div class="text-end d-flex align-items-center">
                                                <input type="file" id="signatureFileInput" name="signature_document"
                                                    style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                                    onchange="handleDocSelect(this, 'signatureFileNameDisplay', 'signatureFileInfo', 'signatureRemoveFile', '.signatureUploadBtn', '#signatureDocContainer');">
                                                <button type="button" id="signatureUploadBtn"
                                                    class="link-alt border-0 bg-transparent signatureUploadBtn"
                                                    onclick="document.getElementById('signatureFileInput').click();">
                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                </button>
                                                <span class="text-danger-alt signatureRemoveFile" style="display: none;"
                                                    onclick="clearDocInput('signatureFileInput', 'signatureFileNameDisplay', 'signatureFileInfo', 'signatureRemoveFile', '.signatureUploadBtn', '#signatureDocContainer');"><i
                                                        class="fas fa-trash-alt"></i> Remove</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="wizard-nav">
                                    <button type="button" class="btn-back wizard-prev" data-prev="2"><i class="fas fa-arrow-left me-2"></i> BACK</button>
                                    <button type="button" class="btn-gradient wizard-next" data-next="4">NEXT <i class="fas fa-arrow-right ms-2"></i></button>
                                </div>
                            </div>

                            <!-- ==================== STEP 4: KYC Summary ==================== -->
                            <div class="wizard-panel" data-panel="4">
                                <div class="section-title-alt">KYC Summary</div>
                                <p class="step-intro">Review your details before proceeding to billing.</p>

                                <div class="summary-grid" id="kycSummaryGrid">
                                    <div class="summary-item">
                                        <span class="summary-label">Aadhaar Number</span>
                                        <span class="summary-value" id="summaryAadhar">—</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Aadhaar Front</span>
                                        <span class="summary-value" id="summaryAadharFront">—</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Aadhaar Back</span>
                                        <span class="summary-value" id="summaryAadharBack">—</span>
                                    </div>
                                    <div class="summary-item summary-item-full">
                                        <span class="summary-label">Aadhaar Address</span>
                                        <span class="summary-value" id="summaryAadharAddress">—</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">PAN Number</span>
                                        <span class="summary-value" id="summaryPan">—</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">PAN Holder Name</span>
                                        <span class="summary-value" id="summaryPanName">—</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Date of Birth</span>
                                        <span class="summary-value" id="summaryDob">—</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">PAN Document</span>
                                        <span class="summary-value" id="summaryPanDoc">—</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Signature</span>
                                        <span class="summary-value" id="summarySignature">—</span>
                                    </div>
                                </div>

                                <div class="wizard-nav">
                                    <button type="button" class="btn-back wizard-prev" data-prev="3"><i class="fas fa-arrow-left me-2"></i> BACK</button>
                                    <button type="button" class="btn-gradient wizard-next" data-next="5">NEXT <i class="fas fa-arrow-right ms-2"></i></button>
                                </div>
                            </div>

                            <!-- ==================== STEP 5: Billing Details ==================== -->
                            <div class="wizard-panel" data-panel="5">
                                <div class="section-title-alt">Billing Details</div>
                                <p class="step-intro">Provide your billing address and contact information. This will be used for invoices.</p>

                                <div class="aadhar-billing-toggle mb-3">
                                    <label class="d-flex align-items-center gap-2 cursor-pointer">
                                        <input type="checkbox" id="useAadharAddress" class="form-check-input">
                                        <span class="section-label mb-0">Use my Aadhaar address as billing address</span>
                                    </label>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="section-label">Billing Address</label>
                                        <div class="input-wrapper">
                                            <textarea class="input-custom" id="billingAddress" rows="3"
                                                placeholder="Enter your full billing address *" name="billing_address"
                                                style="padding-left: 48px; padding-top: 16px;"
                                                required>{{ old('billing_address', $kycDetail->billing_address ?? '') }}</textarea>
                                            <i class="fas fa-map-marker-alt" style="top: 22px;"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="section-label">Billing Contact Number</label>
                                        <div class="input-wrapper">
                                            <input type="tel" class="input-custom" id="billingContact"
                                                placeholder="Enter Contact Number *" name="billing_contact"
                                                maxlength="15" inputmode="numeric"
                                                value="{{ old('billing_contact', $kycDetail->billing_contact ?? '') }}"
                                                required>
                                            <i class="fas fa-phone"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="section-label">Billing Email</label>
                                        <div class="input-wrapper">
                                            <input type="email" class="input-custom" id="billingEmail"
                                                placeholder="Enter Billing Email *" name="billing_email"
                                                value="{{ old('billing_email', $kycDetail->billing_email ?? '') }}"
                                                required>
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="wizard-nav">
                                    <button type="button" class="btn-back wizard-prev" data-prev="4"><i class="fas fa-arrow-left me-2"></i> BACK</button>
                                    <button type="button" class="btn-gradient wizard-next" data-next="6">NEXT <i class="fas fa-arrow-right ms-2"></i></button>
                                </div>
                            </div>

                            <!-- ==================== STEP 6: Merchant Agreement ==================== -->
                            <div class="wizard-panel" data-panel="6">
                                <div class="section-title-alt">Merchant Agreement</div>
                                <p class="step-intro">Upload the signed merchant agreement and accept the terms to complete your KYC.</p>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="section-label">Signed Merchant Agreement</label>
                                        <div class="doc-item" id="merchantAgreementContainer">
                                            <div class="doc-meta">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="doc-name">Merchant Agreement (PDF)</span>
                                                        <i class="fas fa-info-circle info-circle ms-2"
                                                            title="Upload the signed merchant agreement document"></i>
                                                    </div>
                                                    <div id="merchantAgreementFileInfo" class="file-status">Selected: <span
                                                            id="merchantAgreementFileNameDisplay">file</span></div>
                                                </div>
                                            </div>
                                            <div class="text-end d-flex align-items-center">
                                                <input type="file" id="merchantAgreementFileInput" name="merchant_agreement"
                                                    style="display: none;" accept=".pdf"
                                                    onchange="handleDocSelect(this, 'merchantAgreementFileNameDisplay', 'merchantAgreementFileInfo', 'merchantAgreementRemoveFile', '.merchantAgreementUploadBtn', '#merchantAgreementContainer');">
                                                <button type="button"
                                                    class="link-alt border-0 bg-transparent merchantAgreementUploadBtn"
                                                    onclick="document.getElementById('merchantAgreementFileInput').click();">
                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                </button>
                                                <span class="text-danger-alt merchantAgreementRemoveFile" style="display: none;"
                                                    onclick="clearDocInput('merchantAgreementFileInput', 'merchantAgreementFileNameDisplay', 'merchantAgreementFileInfo', 'merchantAgreementRemoveFile', '.merchantAgreementUploadBtn', '#merchantAgreementContainer');"><i
                                                        class="fas fa-trash-alt"></i> Remove</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="agreement-box mt-4">
                                    <div class="form-check d-flex align-items-start mb-3">
                                        <input class="form-check-input mt-1" type="checkbox" id="termsAccepted"
                                            name="terms_accepted" value="1" required
                                            {{ old('terms_accepted') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="termsAccepted">
                                            I, <strong id="agreementCustomerName">{{ $customer->name ?? 'the customer' }}</strong>,
                                            hereby declare that the information provided above is true and correct to the best of my knowledge.
                                            I agree to the terms and conditions of United Courier Worldwide and authorize the use of
                                            my submitted documents for KYC verification purposes.
                                        </label>
                                    </div>
                                </div>

                                <div class="wizard-nav">
                                    <button type="button" class="btn-back wizard-prev" data-prev="5"><i class="fas fa-arrow-left me-2"></i> BACK</button>
                                    <button type="submit" class="btn-gradient" id="submitKycBtn">COMPLETE KYC <i class="fas fa-check-circle ms-2"></i></button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Personal KYC Custom JS -->
                <script>
                    // Inline helpers so the upload widgets work even if external JS is cached
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
                <script src="{{ asset('js/kyc-personal.js') }}?v={{ filemtime(public_path('js/kyc-personal.js')) ?: 1 }}"></script>

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

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>

</html>
