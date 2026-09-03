<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Customer | United Courier</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <style>
        .page-wrapper .content{
            padding:0.5rem;
        }
        .saved-address-item {
            padding-left: .5rem;
            border-left: 2px solid #e9ecef;
        }
        .saved-address-item + .saved-address-item {
            margin-top: .5rem;
        }
        .saved-address-badge {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    @include('customer.partials.customer_dashboard_header')
    @include('customer.partials.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div>
                    <!-- <h4 class="mb-1">Customer Management</h4> -->
                    <!-- <p class="text-muted mb-0">Add and manage your saved customers.</p> -->
                </div>
                <a href="{{ route('customer.create-shipment') }}" class="btn btn-outline-primary">
                    <i class="ti ti-package-export me-1"></i>Create Order
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card rounded-0 border-0">
                <div class="card-header p-0 border-bottom">
                    <ul class="nav nav-tabs nav-tabs-bottom border-0 px-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ session('success') ? '' : 'active' }}" id="add-customer-tab" data-bs-toggle="tab" data-bs-target="#add-customer-pane" type="button" role="tab" aria-controls="add-customer-pane" aria-selected="{{ session('success') ? 'false' : 'true' }}">
                                <i class="ti ti-user-plus me-1"></i>Add Customer
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ session('success') ? 'active' : '' }}" id="view-customers-tab" data-bs-toggle="tab" data-bs-target="#view-customers-pane" type="button" role="tab" aria-controls="view-customers-pane" aria-selected="{{ session('success') ? 'true' : 'false' }}">
                                <i class="ti ti-users me-1"></i>View All Customers
                                <span class="badge bg-primary ms-1">{{ $exporterCustomers->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade {{ session('success') ? '' : 'show active' }}" id="add-customer-pane" role="tabpanel" aria-labelledby="add-customer-tab" tabindex="0">

                            <!-- Exporter Customer Wizard Custom CSS -->
                            <link rel="stylesheet" href="{{ asset('css/exporter-customers.css') }}?v={{ filemtime(public_path('css/exporter-customers.css')) ?: 1 }}">

                            <div class="form-wrapper">
                                <div class="kyc-card">
                                    <div class="form-header">
                                        <h2>Add <span class="gradient-text">Customer</span></h2>
                                        <p>Register a new customer for your shipping account in a few simple steps.</p>
                                    </div>

                                    <!-- Wizard Progress Bar -->
                                    <div class="wizard-progress">
                                        <div class="wizard-steps">
                                            <div class="wizard-step active" data-step="1">
                                                <span class="step-number">1</span>
                                                <span class="step-label">Details</span>
                                            </div>
                                            <div class="wizard-step" data-step="2">
                                                <span class="step-number">2</span>
                                                <span class="step-label">KYC Document</span>
                                            </div>
                                            <div class="wizard-step" data-step="3">
                                                <span class="step-number">3</span>
                                                <span class="step-label">Basic Info</span>
                                            </div>
                                            <div class="wizard-step" data-step="4">
                                                <span class="step-number">4</span>
                                                <span class="step-label">CSB5 Info</span>
                                            </div>
                                        </div>
                                        <div class="wizard-bar">
                                            <div class="wizard-bar-fill" id="wizardBarFill" style="width: 25%;"></div>
                                        </div>
                                    </div>

                                    <form id="exporterCustomerForm"
                                          action="{{ route('customer.exporter-customers.store') }}"
                                          method="POST" enctype="multipart/form-data" novalidate
                                          data-verify-aadhar-url="{{ route('customer.verify.exporter-customer-aadhar') }}"
                                          data-verify-gst-url="{{ route('customer.verify.gst') }}"
                                          data-verify-pan-url="{{ route('customer.verify.exporter-customer-pan') }}">
                                        @csrf

                                        <!-- ==================== STEP 1: User Type + KYC Type + CSB Type ==================== -->
                                        <div class="wizard-panel active" data-panel="1">
                                            <div class="section-title-alt"><i class="fas fa-sliders"></i> Customer Configuration <span class="step-chip">Step 1 of 4</span></div>
                                            <p class="step-intro">Select the customer type and CSB type. The KYC type is set automatically from the customer type - Aadhar Card for Individual; for Business you can choose between PAN Card and GST (Normal). Selecting CSB V adds an extra step to collect the CSB V details.</p>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="section-label">User Type <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <select name="business_category_id" id="businessCategoryId" class="input-custom select-custom" required>
                                                            <option value="">Select Customer Type</option>
                                                            @foreach($groupedBusinessCategories as $groupName => $categories)
                                                                <optgroup label="{{ $groupName }}">
                                                                    @foreach($categories as $category)
                                                                        <option value="{{ $category->id }}" data-user-type="{{ strtolower($category->user_type ?: $category->parent_group) }}" {{ (string) old('business_category_id') === (string) $category->id ? 'selected' : '' }}>
                                                                            {{ $category->category_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <small class="text-muted">Select the type of customer you are adding.</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="section-label">CSB Type <span class="" style="color:gray">(Optional)</span></label>
                                                    <div class="csb-type-box">
                                                        <label class="csb-checkbox">
                                                            <input type="checkbox" id="csbTypeCheck" {{ old('csb_type') === 'csb_v' ? 'checked' : '' }}>
                                                            <span class="csb-checkbox-box"><i class="fas fa-check"></i></span>
                                                            <span class="csb-checkbox-label">CSB V <small>Enable CSB V fields</small></span>
                                                        </label>
                                                        <input type="hidden" name="csb_type" id="csbType" value="{{ old('csb_type', 'csb_iv') }}">
                                                    </div>
                                                    <small class="text-muted" id="csbTypeHint">Personal customers can select CSB IV or CSB V.</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="section-label">KYC Type <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <select name="kyc_type" id="kycType" class="input-custom select-custom" required>
                                                            <option value="">Select KYC Type</option>
                                                            <option value="Aadhar Card" {{ old('kyc_type', 'Aadhar Card') === 'Aadhar Card' ? 'selected' : '' }}>Aadhar Card</option>
                                                            <option value="PAN Card" {{ old('kyc_type') === 'PAN Card' ? 'selected' : '' }}>PAN Card</option>
                                                            <option value="GST (Normal)" {{ old('kyc_type') === 'GST (Normal)' ? 'selected' : '' }}>GST (Normal)</option>
                                                        </select>
                                                        <i class="fas fa-id-card"></i>
                                                    </div>
                                                    <small class="text-muted">KYC type is set automatically from the customer type - Aadhar Card for Individual; PAN Card or GST (Normal) for Business. For Business, Step 2 always requires PAN + GST verification regardless of the KYC Type chosen here.</small>
                                                </div>
                                            </div>

                                            @php
                                                $savedLutBondYear = old('lut_bond_year', '');
                                                $savedLutStartYear = preg_match('/^(\d{4})-(\d{2})$/', $savedLutBondYear, $savedLutMatches)
                                                    ? $savedLutMatches[1]
                                                    : '';
                                                $savedLutEndYear = '';
                                                if ($savedLutStartYear !== '') {
                                                    $savedLutEndYear = (intdiv((int) $savedLutStartYear, 100) * 100) + (int) $savedLutMatches[2];
                                                    if ($savedLutEndYear <= (int) $savedLutStartYear) {
                                                        $savedLutEndYear += 100;
                                                    }
                                                }
                                                $lutStartYears = range(now()->year, now()->year + 5);
                                            @endphp

                                            <div class="wizard-nav">
                                                <div></div>
                                                <button type="button" class="btn-gradient wizard-next" data-next="2">NEXT <i class="fas fa-arrow-right ms-2"></i></button>
                                            </div>
                                        </div>

                                        <!-- ==================== STEP 2: KYC Document ==================== -->
                                        <div class="wizard-panel" data-panel="2">
                                            <div class="section-title-alt"><i class="fas fa-shield-halved"></i> KYC Document Verification <span class="step-chip">Step 2 of 4</span></div>
                                            <p class="step-intro">Verify the required KYC documents below. The sections shown depend on the customer type selected in Step 1. Upload the required document images and verify each section.</p>

                                            <!-- Aadhar section (mandatory for Individual customers, optional for Business customers) -->
                                            <div id="aadharKycSection">
                                                <div class="sub-section-header">
                                                    <div class="sub-section-icon"><i class="fas fa-id-card"></i></div>
                                                    <div>
                                                        <h6 class="sub-section-title mb-0">Aadhaar Verification</h6>
                                                        <!-- <small class="sub-section-desc">Verify Aadhaar details through Cashfree OCR</small> -->
                                                    </div>
                                                    <span class="badge-sub badge-kyc-optional" id="aadharRequirementBadge" style="display: none;">Optional</span>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">Aadhaar Number</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom" id="aadharNumber" placeholder="Enter 12-digit Aadhaar Number *" name="aadhar_number" maxlength="12" inputmode="numeric" value="{{ old('aadhar_number') }}" required>
                                                            <i class="fas fa-id-card"></i>
                                                        </div>
                                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                                            <button type="button" class="btn-verify mt-1" id="aadharVerifyBtn">
                                                                <i class="fas fa-shield-halved me-1"></i> Verify Aadhaar
                                                            </button>
                                                            <span class="verified-badge" id="aadharVerifiedBadge" style="display: none;">
                                                                <i class="fas fa-circle-check me-1"></i> Verified
                                                            </span>
                                                        </div>
                                                        <div id="aadharVerifyStatus" class="kyc-alert" style="display: none;"></div>
                                                    </div>
                                                </div>

                                                <!-- <div class="sub-section-header mt-4">
                                                    <div class="sub-section-icon"><i class="fas fa-images"></i></div>
                                                    <div>
                                                        <h6 class="sub-section-title mb-0">Aadhaar Card Documents</h6>
                                                        <small class="sub-section-desc">Upload clear, JPG / PNG photos of the front and back of your Aadhaar card</small>
                                                    </div>
                                                    <span class="badge-sub">JPG / PNG</span>
                                                </div> -->
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">Aadhaar Front</label>
                                                        <div class="doc-item compact" id="aadharFrontDocContainer">
                                                            <div class="doc-meta">
                                                                <div class="doc-file-icon"><i class="fas fa-id-card"></i></div>
                                                                <div>
                                                                    <span class="doc-name">Aadhaar Front</span>
                                                                    <div id="aadharFrontFileInfo" class="file-status">Selected: <span id="aadharFrontFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="aadharFrontFileInput" name="aadhar_front_document" style="display: none;" accept=".jpg,.jpeg,.png" required
                                                                    onchange="handleDocSelect(this, 'aadharFrontFileNameDisplay', 'aadharFrontFileInfo', 'aadharFrontRemoveFile', '.aadharFrontUploadBtn', '#aadharFrontDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent aadharFrontUploadBtn" onclick="document.getElementById('aadharFrontFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt aadharFrontRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('aadharFrontFileInput', 'aadharFrontFileNameDisplay', 'aadharFrontFileInfo', 'aadharFrontRemoveFile', '.aadharFrontUploadBtn', '#aadharFrontDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">JPG, JPEG or PNG, up to 5 MB.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Aadhaar Back</label>
                                                        <div class="doc-item compact" id="aadharBackDocContainer">
                                                            <div class="doc-meta">
                                                                <div class="doc-file-icon"><i class="fas fa-id-card"></i></div>
                                                                <div>
                                                                    <span class="doc-name">Aadhaar Back</span>
                                                                    <div id="aadharBackFileInfo" class="file-status">Selected: <span id="aadharBackFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="aadharBackFileInput" name="aadhar_back_document" style="display: none;" accept=".jpg,.jpeg,.png" required
                                                                    onchange="handleDocSelect(this, 'aadharBackFileNameDisplay', 'aadharBackFileInfo', 'aadharBackRemoveFile', '.aadharBackUploadBtn', '#aadharBackDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent aadharBackUploadBtn" onclick="document.getElementById('aadharBackFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt aadharBackRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('aadharBackFileInput', 'aadharBackFileNameDisplay', 'aadharBackFileInfo', 'aadharBackRemoveFile', '.aadharBackUploadBtn', '#aadharBackDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">JPG, JPEG or PNG, up to 5 MB.</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- PAN section (mandatory for both Individual and Business customers) -->
                                            <div id="panKycSection" class="d-none">
                                                <div class="sub-section-header mt-4">
                                                    <div class="sub-section-icon"><i class="fas fa-credit-card"></i></div>
                                                    <div>
                                                        <h6 class="sub-section-title mb-0">PAN Verification</h6>
                                                        <small class="sub-section-desc">Verify PAN details</small>
                                                    </div>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">PAN Number</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom text-uppercase" id="panNumber" placeholder="Enter 10-character PAN Number *" name="pan_number" maxlength="10" style="text-transform: uppercase;" value="{{ old('pan_number') }}" required>
                                                            <i class="fas fa-credit-card"></i>
                                                        </div>
                                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                                            <button type="button" class="btn-verify mt-1" id="panVerifyBtn">
                                                                <i class="fas fa-shield-halved me-1"></i> Verify PAN
                                                            </button>
                                                            <span class="verified-badge" id="panVerifiedBadge" style="display: none;">
                                                                <i class="fas fa-circle-check me-1"></i> Verified
                                                            </span>
                                                        </div>
                                                        <div id="panVerifyStatus" class="kyc-alert" style="display: none;"></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Name as on PAN</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom" id="panHolderName" placeholder="Enter name as on PAN *" name="pan_holder_name" maxlength="255" value="{{ old('pan_holder_name') }}" required>
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">Date of Birth</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom" id="panDob" placeholder="DD/MM/YYYY" name="pan_dob" autocomplete="bday" readonly value="{{ old('pan_dob') }}" required>
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </div>
                                                        <small class="text-muted">Date of birth as per the PAN record.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">PAN Card Document</label>
                                                        <div class="doc-item compact" id="panDocContainer">
                                                            <div class="doc-meta">
                                                                <div class="doc-file-icon"><i class="fas fa-credit-card"></i></div>
                                                                <div>
                                                                    <span class="doc-name">PAN Card</span>
                                                                    <div id="panFileInfo" class="file-status">Selected: <span id="panFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="panFileInput" name="pan_document" style="display: none;" accept=".jpg,.jpeg,.png" required
                                                                    onchange="handleDocSelect(this, 'panFileNameDisplay', 'panFileInfo', 'panRemoveFile', '.panUploadBtn', '#panDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent panUploadBtn" onclick="document.getElementById('panFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt panRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('panFileInput', 'panFileNameDisplay', 'panFileInfo', 'panRemoveFile', '.panUploadBtn', '#panDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">JPG, JPEG or PNG, up to 5 MB.</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- GST section (shown when customer type = Business) -->
                                            <div id="gstKycSection" class="d-none">
                                                <div class="sub-section-header">
                                                    <div class="sub-section-icon"><i class="fas fa-receipt"></i></div>
                                                    <div>
                                                        <h6 class="sub-section-title mb-0">GST Verification</h6>
                                                        <small class="sub-section-desc">Verify GSTIN and registered business name </small>
                                                    </div>
                                                    <!-- <span class="badge-sub">Cashfree</span> -->
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">GSTIN</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom text-uppercase" id="gstKycNumber" placeholder="Enter 15-character GSTIN *" name="gst_kyc_number" maxlength="15" value="{{ old('gst_kyc_number') }}" required>
                                                            <i class="fas fa-receipt"></i>
                                                        </div>
                                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                                            <button type="button" class="btn-verify mt-1" id="gstKycVerifyBtn">
                                                                <i class="fas fa-shield-halved me-1"></i> Verify GST
                                                            </button>
                                                            <span class="verified-badge" id="gstKycVerifiedBadge" style="display: none;">
                                                                <i class="fas fa-circle-check me-1"></i> Verified
                                                            </span>
                                                        </div>
                                                        <div id="gstKycVerifyStatus" class="kyc-alert" style="display: none;"></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Registered Business Name</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom" id="gstKycBusinessName" placeholder="Enter registered business name *" name="gst_kyc_business_name" maxlength="255" value="{{ old('gst_kyc_business_name') }}" required>
                                                            <i class="fas fa-building"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">GST Certificate (PDF)</label>
                                                        <div class="doc-item compact" id="gstKycDocContainer">
                                                            <div class="doc-meta">
                                                                <div class="doc-file-icon"><i class="fas fa-file-pdf"></i></div>
                                                                <div>
                                                                    <span class="doc-name">GST Certificate</span>
                                                                    <div id="gstKycFileInfo" class="file-status">Selected: <span id="gstKycFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="gstKycFileInput" name="gst_certificate_document" style="display: none;" accept=".pdf" required
                                                                    onchange="handleDocSelect(this, 'gstKycFileNameDisplay', 'gstKycFileInfo', 'gstKycRemoveFile', '.gstKycUploadBtn', '#gstKycDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent gstKycUploadBtn" onclick="document.getElementById('gstKycFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt gstKycRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('gstKycFileInput', 'gstKycFileNameDisplay', 'gstKycFileInfo', 'gstKycRemoveFile', '.gstKycUploadBtn', '#gstKycDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">GST certificate in PDF format, up to 5 MB.</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-nav">
                                                <button type="button" class="btn-back wizard-prev" data-prev="1"><i class="fas fa-arrow-left me-2"></i> BACK</button>
                                                <button type="button" class="btn-gradient wizard-next" data-next="3">NEXT <i class="fas fa-arrow-right ms-2"></i></button>
                                            </div>
                                        </div>

                                        <!-- ==================== STEP 3: Basic Info ==================== -->
                                        <div class="wizard-panel" data-panel="3">
                                            <div class="section-title-alt"><i class="fas fa-user-pen"></i> Basic Information <span class="step-chip">Step 3 of 4</span></div>
                                            <p class="step-intro">Provide the customer's basic information. Fields verified in Step 2 are auto-filled for you.</p>

                                            <div class="row g-3">
                                                <div class="col-md-6" id="companyNameWrapper">
                                                    <label class="section-label">Company Name <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="companyName" name="company_name" class="input-custom" value="{{ old('company_name') }}" minlength="2" maxlength="150" pattern="[A-Za-z0-9][A-Za-z0-9 .&()'/-]*" required>
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                    <small class="text-muted">Minimum 2 characters.</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="section-label">Contact Person <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="contactPerson" name="contact_person" class="input-custom" value="{{ old('contact_person') }}" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <small class="text-muted" id="contactPersonHint">Auto-filled from the verified KYC document.</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">Address Line 1 <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="addressLine1" name="address_line1" class="input-custom" value="{{ old('address_line1') }}" minlength="5" maxlength="255" required>
                                                        <i class="fas fa-location-dot"></i>
                                                    </div>
                                                    <small class="text-muted">Minimum 5 characters.</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">Address Line 2</label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="addressLine2" name="address_line2" class="input-custom" value="{{ old('address_line2') }}" maxlength="255">
                                                        <i class="fas fa-map"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">Address Line 3</label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="addressLine3" name="address_line3" class="input-custom" value="{{ old('address_line3') }}" maxlength="255">
                                                        <i class="fas fa-map-pin"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">Pincode <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="pincode" name="pincode" class="input-custom" value="{{ old('pincode') }}" inputmode="numeric" minlength="6" maxlength="6" pattern="[1-9][0-9]{5}" required>
                                                        <i class="fas fa-hashtag"></i>
                                                    </div>
                                                    <small class="text-muted">Enter a valid 6-digit Indian pincode.</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">City <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="city" name="city" class="input-custom" value="{{ old('city') }}" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                                                        <i class="fas fa-city"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">State <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="state" name="state" class="input-custom" value="{{ old('state') }}" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                                                        <i class="fas fa-flag"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="section-label">Phone Number <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="tel" id="phoneNumber" name="phone_number" class="input-custom" value="{{ old('phone_number') }}" inputmode="numeric" minlength="10" maxlength="10" pattern="[6-9][0-9]{9}" required>
                                                        <i class="fas fa-phone"></i>
                                                    </div>
                                                    <small class="text-muted">Enter a valid 10-digit Indian mobile number.</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="section-label">Email <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="email" id="customerEmail" name="email" class="input-custom" value="{{ old('email') }}" maxlength="150" autocomplete="email" required>
                                                        <i class="fas fa-envelope"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-nav">
                                                <button type="button" class="btn-back wizard-prev" data-prev="2"><i class="fas fa-arrow-left me-2"></i> BACK</button>
                                                <button type="button" class="btn-gradient" id="step3ActionBtn">NEXT <i class="fas fa-arrow-right ms-2"></i></button>
                                            </div>
                                        </div>

                                        <!-- ==================== STEP 4: CSB5 Info (CSB V only) ==================== -->
                                        <div class="wizard-panel" data-panel="4">
                                            <div class="section-title-alt"><i class="fas fa-briefcase"></i> CSB V Information <span class="step-chip">Step 4 of 4</span></div>
                                            <p class="step-intro">Provide the additional CSB V details required for your business account.</p>

                                            <div id="csbVFields">
                                                @php
                                                    // Reuse the account's Cashfree-verified GST (session-based). When a
                                                    // valid verification is present, GST is auto-selected and the GSTIN +
                                                    // registered business name are prefilled read-only so the customer can
                                                    // save without re-verifying. The GST certificate PDF is still required
                                                    // because every saved customer stores its own copy.
                                                    $reuseGst = $verifiedGstReusable ?? false;
                                                    $prefilledGstNumber = $reuseGst ? ($verifiedGstNumber ?? '') : old('gst_certificate_number');
                                                    $prefilledGstBusinessName = $reuseGst ? ($verifiedGstBusinessName ?? '') : old('gst_business_name');
                                                @endphp
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <div class="d-flex flex-wrap gap-4 align-items-center">
                                                            <div class="form-check mb-0">
                                                                <input type="checkbox" class="form-check-input" id="isGst" name="is_gst" value="1" {{ (old('is_gst') || $reuseGst) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold" for="isGst">GST</label>
                                                            </div>
                                                            <div class="form-check mb-0">
                                                                <input type="checkbox" class="form-check-input" id="isLut" name="is_lut" value="1" {{ old('is_lut') ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold" for="isLut">LUT (Against Bond or UT)</label>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Select GST, LUT, or both. At least one option is required.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">AD Code <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <input type="text" id="adCode" name="ad_code" class="input-custom" value="{{ old('ad_code') }}" inputmode="numeric" maxlength="14" pattern="[0-9]{7}|[0-9]{14}" data-csb-v-required>
                                                            <i class="fas fa-hashtag"></i>
                                                        </div>
                                                        <small class="text-muted">Enter exactly 7 or 14 numeric digits.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">IEC Number <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <input type="text" id="iecNumber" name="iec_number" class="input-custom text-uppercase" value="{{ old('iec_number') }}" maxlength="10" pattern="[A-Za-z0-9]{10}" data-csb-v-required>
                                                            <i class="fas fa-file-invoice"></i>
                                                        </div>
                                                        <small class="text-muted">Enter exactly 10 letters or digits.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">AD Code Document <span class="text-danger">*</span></label>
                                                        <div class="doc-item compact" id="adCodeDocContainer">
                                                            <div class="doc-meta">
                                                                <div>
                                                                    <span class="doc-name">AD Code Document</span>
                                                                    <div id="adCodeFileInfo" class="file-status">Selected: <span id="adCodeFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="adCodeFileInput" name="ad_code_document" style="display: none;" accept=".pdf,.jpg,.jpeg,.png" data-csb-v-required
                                                                    onchange="handleDocSelect(this, 'adCodeFileNameDisplay', 'adCodeFileInfo', 'adCodeRemoveFile', '.adCodeUploadBtn', '#adCodeDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent adCodeUploadBtn" onclick="document.getElementById('adCodeFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt adCodeRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('adCodeFileInput', 'adCodeFileNameDisplay', 'adCodeFileInfo', 'adCodeRemoveFile', '.adCodeUploadBtn', '#adCodeDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">PDF, JPG, JPEG or PNG, up to 5 MB.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">IEC Document <span class="text-danger">*</span></label>
                                                        <div class="doc-item compact" id="iecDocContainer">
                                                            <div class="doc-meta">
                                                                <div>
                                                                    <span class="doc-name">IEC Document</span>
                                                                    <div id="iecFileInfo" class="file-status">Selected: <span id="iecFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="iecFileInput" name="iec_document" style="display: none;" accept=".pdf,.jpg,.jpeg,.png" data-csb-v-required
                                                                    onchange="handleDocSelect(this, 'iecFileNameDisplay', 'iecFileInfo', 'iecRemoveFile', '.iecUploadBtn', '#iecDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent iecUploadBtn" onclick="document.getElementById('iecFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt iecRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('iecFileInput', 'iecFileNameDisplay', 'iecFileInfo', 'iecRemoveFile', '.iecUploadBtn', '#iecDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">PDF, JPG, JPEG or PNG, up to 5 MB.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Bank Account Number <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <input type="text" id="bankAccountNumber" name="bank_account_number" class="input-custom" value="{{ old('bank_account_number') }}" inputmode="numeric" minlength="9" maxlength="18" pattern="[0-9]{9,18}" data-csb-v-required>
                                                            <i class="fas fa-building-columns"></i>
                                                        </div>
                                                        <small class="text-muted">Enter 9 to 18 numeric digits.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Bank Type <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <select name="bank_type" id="bankType" class="input-custom select-custom" data-csb-v-required>
                                                                <option value="">Select Bank Type</option>
                                                                <option value="private" {{ old('bank_type') === 'private' ? 'selected' : '' }}>Private</option>
                                                                <option value="government" {{ old('bank_type') === 'government' ? 'selected' : '' }}>Government</option>
                                                            </select>
                                                            <i class="fas fa-university"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="lutFields">
                                                    <div class="sub-section-header mt-4">
                                                        <div class="sub-section-icon"><i class="fas fa-file-signature"></i></div>
                                                        <div>
                                                            <h6 class="sub-section-title mb-0">LUT Details</h6>
                                                            <small class="sub-section-desc">Letter of Undertaking bond details</small>
                                                        </div>
                                                        <span class="badge-sub">Required when LUT is selected</span>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="section-label">LUT Bond Start Year <span class="text-danger">*</span></label>
                                                            <div class="input-wrapper">
                                                                <select id="lutBondStartYear" name="lut_bond_start_year" class="input-custom select-custom">
                                                                    <option value="">Select Start Year</option>
                                                                    @foreach($lutStartYears as $lutStartYear)
                                                                        <option value="{{ $lutStartYear }}" {{ (string) $lutStartYear === (string) $savedLutStartYear ? 'selected' : '' }}>{{ $lutStartYear }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <i class="fas fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="section-label">LUT Bond End Year <span class="text-danger">*</span></label>
                                                            <div class="input-wrapper">
                                                                <select id="lutBondEndYear" class="input-custom select-custom" data-saved-end-year="{{ $savedLutEndYear }}" disabled>
                                                                    <option value="">Select Start Year First</option>
                                                                </select>
                                                                <i class="fas fa-calendar-check"></i>
                                                            </div>
                                                            <input type="hidden" id="lutBondYear" name="lut_bond_year" value="{{ $savedLutBondYear }}">
                                                            @error('lut_bond_year')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="section-label">LUT Expiry Date <span class="text-danger">*</span></label>
                                                            <div class="input-wrapper">
                                                                <input type="date" id="lutExpiryDate" name="lut_expiry_date" class="input-custom" value="{{ old('lut_expiry_date') }}" readonly>
                                                                <i class="fas fa-calendar-xmark"></i>
                                                            </div>
                                                            <small class="text-muted">Automatically set to 31 March of the selected LUT Bond End Year.</small>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="section-label">LUT Document <span class="text-danger">*</span></label>
                                                            <div class="doc-item compact" id="lutDocContainer">
                                                                <div class="doc-meta">
                                                                    <div>
                                                                        <span class="doc-name">LUT Document (PDF)</span>
                                                                        <div id="lutFileInfo" class="file-status">Selected: <span id="lutFileNameDisplay">file</span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="text-end d-flex align-items-center">
                                                                    <input type="file" id="lutFileInput" name="lut_document" style="display: none;" accept=".pdf"
                                                                        onchange="handleDocSelect(this, 'lutFileNameDisplay', 'lutFileInfo', 'lutRemoveFile', '.lutUploadBtn', '#lutDocContainer');">
                                                                    <button type="button" class="link-alt border-0 bg-transparent lutUploadBtn" onclick="document.getElementById('lutFileInput').click();">
                                                                        <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                    </button>
                                                                    <span class="text-danger-alt lutRemoveFile" style="display: none;"
                                                                        onclick="clearDocInput('lutFileInput', 'lutFileNameDisplay', 'lutFileInfo', 'lutRemoveFile', '.lutUploadBtn', '#lutDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">Letter of Undertaking in PDF format, up to 5 MB.</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="gstFields" data-gst-reusable="{{ $reuseGst ? '1' : '0' }}">
                                                    <div class="sub-section-header mt-4">
                                                        <div class="sub-section-icon"><i class="fas fa-receipt"></i></div>
                                                        <div>
                                                            <h6 class="sub-section-title mb-0">GST Registration</h6>
                                                            <small class="sub-section-desc">Enter and verify your GSTIN and registered business name through Cashfree</small>
                                                        </div>
                                                        <span class="badge-sub">Required when GST is selected</span>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="section-label">GSTIN <span class="text-danger">*</span></label>
                                                            <div class="input-wrapper">
                                                                <input type="text" id="gstNumber" name="gst_certificate_number" class="input-custom text-uppercase" value="{{ $prefilledGstNumber }}" maxlength="15" placeholder="Enter 15-character GSTIN *" {{ $reuseGst ? 'readonly' : '' }}>
                                                                <i class="fas fa-receipt"></i>
                                                            </div>
                                                            <small class="text-muted">Enter exactly 15 characters (e.g., 22AAAAA0000A1Z5).</small>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="section-label">Registered Business Name <span class="text-danger">*</span></label>
                                                            <div class="input-wrapper">
                                                                <input type="text" id="gstBusinessName" name="gst_business_name" class="input-custom" value="{{ $prefilledGstBusinessName }}" maxlength="255" placeholder="Enter registered business name *" {{ $reuseGst ? 'readonly' : '' }}>
                                                                <i class="fas fa-building"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="section-label">GST Certificate (PDF) <span class="text-danger">*</span></label>
                                                            <div class="doc-item compact" id="gstDocContainer">
                                                                <div class="doc-meta">
                                                                    <div>
                                                                        <span class="doc-name">GST Certificate (PDF)</span>
                                                                        <div id="gstFileInfo" class="file-status">Selected: <span id="gstFileNameDisplay">file</span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="text-end d-flex align-items-center">
                                                                    <input type="file" id="gstFileInput" name="gst_certificate_document" style="display: none;" accept=".pdf"
                                                                        onchange="handleDocSelect(this, 'gstFileNameDisplay', 'gstFileInfo', 'gstRemoveFile', '.gstUploadBtn', '#gstDocContainer');">
                                                                    <button type="button" class="link-alt border-0 bg-transparent gstUploadBtn" onclick="document.getElementById('gstFileInput').click();">
                                                                        <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                    </button>
                                                                    <span class="text-danger-alt gstRemoveFile" style="display: none;"
                                                                        onclick="clearDocInput('gstFileInput', 'gstFileNameDisplay', 'gstFileInfo', 'gstRemoveFile', '.gstUploadBtn', '#gstDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">GST certificate in PDF format, up to 5 MB.</small>
                                                        </div>
                                                        <div class="col-12">
                                                            <div id="gstVerifyStatus" class="small text-muted" role="status">Click VERIFY GST to validate your details through Cashfree before submission.</div>
                                                            <div class="d-flex align-items-center flex-wrap gap-2 mt-2">
                                                                <button type="button" id="verifyGstBtn" class="btn-gradient">
                                                                    <i class="fas fa-shield-alt me-1"></i> VERIFY GST
                                                                </button>
                                                                <span class="verified-badge" id="gstVerifiedBadge" style="display: none;">
                                                                    <i class="fas fa-circle-check me-1"></i> Verified
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sub-section-header mt-4">
                                                    <div class="sub-section-icon"><i class="fas fa-receipt"></i></div>
                                                    <div>
                                                        <h6 class="sub-section-title mb-0">Billing Details</h6>
                                                        <small class="sub-section-desc">Address and contact for billing</small>
                                                    </div>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="section-label">Billing Address <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <textarea id="billingAddress" name="billing_address" class="input-custom" rows="3" minlength="10" maxlength="1000" data-csb-v-required placeholder="Enter billing address *" style="padding-top: 16px;">{{ old('billing_address') }}</textarea>
                                                            <i class="fas fa-location-dot" style="top: 22px;"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Billing Contact Number <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <input type="tel" id="billingContact" name="billing_contact" class="input-custom" value="{{ old('billing_contact') }}" inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}" data-csb-v-required>
                                                            <i class="fas fa-phone"></i>
                                                        </div>
                                                        <small class="text-muted">Enter a valid 10-digit Indian mobile number.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Billing Email <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <input type="email" id="billingEmail" name="billing_email" class="input-custom" value="{{ old('billing_email') }}" maxlength="255" data-csb-v-required>
                                                            <i class="fas fa-envelope"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-nav">
                                                <button type="button" class="btn-back wizard-prev" data-prev="3"><i class="fas fa-arrow-left me-2"></i> BACK</button>
                                                <button type="submit" class="btn-gradient" id="submitCustomerBtn">SAVE CUSTOMER <i class="fas fa-check-circle ms-2"></i></button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade {{ session('success') ? 'show active' : '' }}" id="view-customers-pane" role="tabpanel" aria-labelledby="view-customers-tab" tabindex="0">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <h5 class="mb-0">All Customers</h5>
                                <span class="badge bg-light text-dark">Total: {{ $exporterCustomers->count() }}</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Customer Type</th>
                                            <th>Company</th>
                                            <th>Contact Person</th>
                                            <th>Contact Details</th>
                                            <th>Address</th>
                                            <th>CSB Type</th>
                                            <th>GST / LUT</th>
                                            <th>KYC Details</th>
                                            <th>Added On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($exporterCustomers as $savedCustomer)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $savedCustomer->businessCategory?->category_name ?: 'Not specified' }}</td>
                                                <td class="fw-semibold">{{ $savedCustomer->company_name }}</td>
                                                <td>{{ $savedCustomer->contact_person }}</td>
                                                <td>
                                                    <div>{{ $savedCustomer->phone_number }}</div>
                                                    <div class="small text-muted">{{ $savedCustomer->email }}</div>
                                                </td>
                                                <td id="address-cell-{{ $savedCustomer->id }}">
                                                    @php($displayAddresses = $savedCustomer->displayAddresses())
                                                    @forelse($displayAddresses as $displayAddress)
                                                        <div class="saved-address-item{{ $loop->first ? '' : ' mt-1' }}">
                                                            @if(! empty($displayAddress['is_primary']))
                                                                <span class="badge bg-primary-subtle text-primary border me-1 saved-address-badge">Primary</span>
                                                            @endif
                                                            <span class="fw-semibold">{{ $displayAddress['address_line1'] }}</span>
                                                            @php($extraAddressLines = collect([$displayAddress['address_line2'], $displayAddress['address_line3']])->filter())
                                                            @if($extraAddressLines->isNotEmpty())
                                                                <div class="small text-muted">{{ $extraAddressLines->implode(', ') }}</div>
                                                            @endif
                                                            <div class="small text-muted">{{ $displayAddress['city'] }}, {{ $displayAddress['state'] }} - {{ $displayAddress['pincode'] }}</div>
                                                        </div>
                                                    @empty
                                                        <div class="small text-muted">No address provided.</div>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    <span class="badge {{ $savedCustomer->csb_type === 'csb_v' ? 'bg-primary' : 'bg-secondary' }}">
                                                        {{ $savedCustomer->csb_type === 'csb_v' ? 'CSB V' : 'CSB IV' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($savedCustomer->csb_type === 'csb_v')
                                                        @if($savedCustomer->is_gst)
                                                            <span class="badge bg-success">GST</span>
                                                        @endif
                                                        @if($savedCustomer->is_lut)
                                                            <span class="badge bg-warning text-dark">LUT</span>
                                                        @endif
                                                        @if(! $savedCustomer->is_gst && ! $savedCustomer->is_lut)
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($savedCustomer->kyc_type || $savedCustomer->kyc_number)
                                                        <div>{{ $savedCustomer->kyc_type ?: 'Not specified' }}</div>
                                                        <div class="small text-muted">{{ $savedCustomer->kyc_number ?: '-' }}</div>
                                                    @else
                                                        <span class="text-muted">Not provided</span>
                                                    @endif
                                                </td>
                                                <td>{{ $savedCustomer->created_at?->format('d M Y') }}</td>
                                                <td>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary add-address-btn"
                                                            data-customer-id="{{ $savedCustomer->id }}"
                                                            data-company="{{ $savedCustomer->company_name }}"
                                                            title="Save Address">
                                                        <i class="fas fa-location-dot me-1"></i> Save Address
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center text-muted py-4">No customers added yet.</td>
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
    </div>
</div>

<!-- Save Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addAddressForm" method="POST"
                  action="{{ route('customer.exporter-customers.address', '__CUSTOMER_ID__') }}"
                  data-action-template="{{ route('customer.exporter-customers.address', '__CUSTOMER_ID__') }}">
                @csrf
                <input type="hidden" id="addressCustomerId" name="customer_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAddressModalLabel"><i class="fas fa-location-dot me-2 text-primary"></i>Save Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="section-label">Address Line 1 <span class="text-danger">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" id="modalAddressLine1" name="address_line1" class="input-custom" minlength="5" maxlength="255" required>
                            <i class="fas fa-location-dot"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="section-label">Address Line 2</label>
                                <div class="input-wrapper">
                                    <input type="text" id="modalAddressLine2" name="address_line2" class="input-custom" maxlength="255">
                                    <i class="fas fa-map"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="section-label">Address Line 3</label>
                                <div class="input-wrapper">
                                    <input type="text" id="modalAddressLine3" name="address_line3" class="input-custom" maxlength="255">
                                    <i class="fas fa-map-pin"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="section-label">Pincode <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <input type="text" id="modalAddressPincode" name="pincode" class="input-custom" inputmode="numeric" minlength="6" maxlength="6" pattern="[1-9][0-9]{5}" required>
                                    <i class="fas fa-hashtag"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="section-label">City <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <input type="text" id="modalAddressCity" name="city" class="input-custom" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                                    <i class="fas fa-city"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="section-label">State <span class="text-danger">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" id="modalAddressState" name="state" class="input-custom" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                            <i class="fas fa-flag"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addAddressSubmitBtn"><i class="fas fa-location-dot me-1"></i> Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inline helpers so the upload widgets work even if external JS is cached -->
<script>
    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value)
            .replace(/&/g, '&')
            .replace(/</g, '<')
            .replace(/>/g, '>')
            .replace(/"/g, '"')
            .replace(/'/g, ''');
    }
    function showAlert(message, type) {
        if (typeof window !== 'undefined' && window.showAlertGlobal) {
            window.showAlertGlobal(message, type);
        }
    }
    function handleDocSelect(input, nameId, infoId, removeClass, uploadBtnSel, containerSel) {
        if (input.files && input.files.length > 0) {
            var file = input.files[0];
            var imageOnlyFields = ['aadhar_front_document', 'aadhar_back_document', 'pan_document'];
            var extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
            if (imageOnlyFields.indexOf(input.name) !== -1 &&
                (['jpg', 'jpeg', 'png'].indexOf(extension) === -1 ||
                    ['image/jpeg', 'image/png'].indexOf(file.type) === -1 ||
                    file.size > 5 * 1024 * 1024)) {
                input.value = '';
                showAlert('Uploaded documents must be JPG, JPEG, or PNG images up to 5 MB.', 'warning');
                return;
            }
            var name = file.name;
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
            var field = document.getElementById(input.id);
            if (field) { field.classList.remove('input-invalid'); }
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

<script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var panDob = document.getElementById('panDob');
        if (panDob && typeof flatpickr !== 'undefined') {
            flatpickr(panDob, {
                dateFormat: 'd/m/Y',
                maxDate: 'today'
            });
        }
    });
</script>

<script>
    // KYC Type (Step 1) must reflect the selected customer type:
    //   Individual => ONLY "Aadhar Card".
    //   Business   => "PAN Card" and "GST (Normal)" (GST stays the default).
    // Selecting one of the two Business options does not change Step 2 - a
    // Business customer still verifies PAN + GST before saving; the dropdown
    // only records which document is the customer's primary KYC type.
    // Kept inline (in addition to exporter-customers.js) so it can never be
    // skipped by an externally cached JS file or an unrelated load error.
    (function () {
        function desiredOptions(isBusiness) {
            if (isBusiness) {
                return [
                    { value: 'PAN Card', label: 'PAN Card', selected: false },
                    { value: 'GST (Normal)', label: 'GST (Normal)', selected: true }
                ];
            }
            return [
                { value: 'Aadhar Card', label: 'Aadhar Card', selected: true }
            ];
        }

        function sameOptionSet(kycType, desiredValues) {
            if (!kycType.options || kycType.options.length !== desiredValues.length) {
                return false;
            }
            for (var i = 0; i < kycType.options.length; i++) {
                if (kycType.options[i].value !== desiredValues[i]) {
                    return false;
                }
            }
            return true;
        }

        function syncKycTypeFromCustomerType() {
            var customerType = document.getElementById('businessCategoryId');
            var kycType = document.getElementById('kycType');
            if (!customerType || !kycType) {
                return;
            }
            // No customer type chosen yet - keep the server-rendered options
            // (including any old() preselection after a validation error).
            if (!customerType.value) {
                return;
            }
            var option = customerType.options[customerType.selectedIndex];
            var isBusiness = !!(option && option.value && option.dataset.userType === 'business');
            var desired = desiredOptions(isBusiness);
            var desiredValues = [];
            var defaultOption = null;
            for (var i = 0; i < desired.length; i++) {
                desiredValues.push(desired[i].value);
                if (desired[i].selected) {
                    defaultOption = desired[i];
                }
            }

            var previousValue = kycType.value;

            // Rebuild only when the current option set differs, so this stays
            // idempotent alongside the identical logic in exporter-customers.js.
            if (sameOptionSet(kycType, desiredValues)) {
                if (previousValue && desiredValues.indexOf(previousValue) !== -1) {
                    return;
                }
                if (defaultOption) {
                    kycType.value = defaultOption.value;
                }
                return;
            }

            kycType.innerHTML = '';
            var preserveSelection = previousValue && desiredValues.indexOf(previousValue) !== -1;
            for (var j = 0; j < desired.length; j++) {
                var newOption = document.createElement('option');
                newOption.value = desired[j].value;
                newOption.textContent = desired[j].label;
                newOption.selected = preserveSelection ? desired[j].value === previousValue : !!desired[j].selected;
                kycType.appendChild(newOption);
            }
            if (kycType.value === '' && defaultOption) {
                kycType.value = defaultOption.value;
            }
        }

        function init() {
            var customerType = document.getElementById('businessCategoryId');
            if (!customerType) {
                return;
            }
            customerType.addEventListener('change', syncKycTypeFromCustomerType);
            if (customerType.value) {
                syncKycTypeFromCustomerType();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>

<script src="{{ asset('js/exporter-customers.js') }}?v={{ filemtime(public_path('js/exporter-customers.js')) ?: 1 }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('addAddressModal');
        var form = document.getElementById('addAddressForm');
        if (!modalEl || !form) {
            return;
        }

        // Open the popup with empty fields. Each "Save Address" click appends a
        // brand-new address to the customer, so nothing is pre-filled anymore.
        document.querySelectorAll('.add-address-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-customer-id');
                document.getElementById('addressCustomerId').value = id || '';

                var template = form.getAttribute('data-action-template') || '';
                form.setAttribute('action', template.replace('__CUSTOMER_ID__', id || ''));

                var fields = ['modalAddressLine1', 'modalAddressLine2', 'modalAddressLine3',
                    'modalAddressPincode', 'modalAddressCity', 'modalAddressState'];
                fields.forEach(function (fieldId) {
                    var el = document.getElementById(fieldId);
                    if (el) { el.value = ''; }
                });

                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });
        });

        // Submit via AJAX, then rebuild the row's Address cell with the full
        // list of addresses (primary + any extra ones that were appended).
        function renderAddressList(cell, addresses) {
            if (!cell || !addresses || !addresses.length) {
                return;
            }
            var html = '';
            addresses.forEach(function (addr, index) {
                var lines23 = [addr.address_line2, addr.address_line3]
                    .filter(function (p) { return p && p.trim(); })
                    .join(', ');
                html += '<div class="saved-address-item' + (index > 0 ? ' mt-1' : '') + '">';
                if (index === 0) {
                    html += '<span class="badge bg-primary-subtle text-primary border me-1 saved-address-badge">Primary</span>';
                }
                html += '<span class="fw-semibold">' + escapeHtml(addr.address_line1) + '</span>';
                if (lines23) {
                    html += '<div class="small text-muted">' + escapeHtml(lines23) + '</div>';
                }
                html += '<div class="small text-muted">' +
                    escapeHtml((addr.city || '') + ', ' + (addr.state || '') + ' - ' + (addr.pincode || '')) +
                    '</div></div>';
            });
            cell.innerHTML = html;
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var submitBtn = document.getElementById('addAddressSubmitBtn');
            var originalHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
            }

            var formData = new FormData(form);
            var token = document.querySelector('meta[name="csrf-token"]');
            var csrf = token ? token.getAttribute('content') : '';

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(function (res) {
                    return res.json().then(function (data) {
                        return { ok: res.ok, data: data };
                    });
                })
                .then(function (result) {
                    var data = result.data || {};
                    if (result.ok && data.success) {
                        var id = document.getElementById('addressCustomerId').value;
                        var cell = document.getElementById('address-cell-' + id);
                        if (cell) {
                            renderAddressList(cell, data.addresses || []);
                        }

                        showAlert(data.message || 'Address saved successfully!', 'success');

                        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.hide();
                    } else {
                        var msg = data.message || 'Failed to save address. Please check your details.';
                        if (data.errors) {
                            msg = Object.values(data.errors).flat().join(' ');
                        }
                        showAlert(msg, 'error');
                    }
                })
                .catch(function () {
                    showAlert('A network error occurred. Please try again.', 'error');
                })
                .finally(function () {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                    }
                });
        });
    });
</script>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>
</body>
</html>
