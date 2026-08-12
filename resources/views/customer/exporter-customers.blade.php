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
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/plugins/tabler-icons/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
</head>
<body>
<div class="main-wrapper">
    @include('customer.partials.customer_dashboard_header')
    @include('customer.partials.sidebar')

    <div class="page-wrapper">
        <div class="content pb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div>
                    <h4 class="mb-1">Customer Management</h4>
                    <p class="text-muted mb-0">Add and manage your saved customers.</p>
                </div>
                <a href="{{ route('customer.create-shipment') }}" class="btn btn-outline-primary">
                    <i class="ti ti-package-export me-1"></i>Create Shipment
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
                            <h5 class="mb-3">Customer Information</h5>
                            <form action="{{ route('customer.exporter-customers.store') }}" method="POST" enctype="multipart/form-data" id="addCustomerForm" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label" for="businessCategoryId">Customer Type <span class="text-danger">*</span></label>
                                        <select name="business_category_id" id="businessCategoryId" class="form-select @error('business_category_id') is-invalid @enderror" required>
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
                                        @error('business_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label" for="csbType">CSB Type <span class="text-danger">*</span></label>
                                        <select name="csb_type" id="csbType" class="form-select @error('csb_type') is-invalid @enderror" required>
                                            <option value="csb_iv" data-personal-option {{ old('csb_type', 'csb_iv') === 'csb_iv' ? 'selected' : '' }}>CSB IV</option>
                                            <option value="csb_v" {{ old('csb_type') === 'csb_v' ? 'selected' : '' }}>CSB V</option>
                                        </select>
                                        <div class="form-text" id="csbTypeHint">Business customers can use CSB V only.</div>
                                        @error('csb_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12"><hr class="mt-0 mb-4"></div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="companyName">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" id="companyName" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" minlength="2" maxlength="150" pattern="[A-Za-z0-9][A-Za-z0-9 .&()'/-]*" required>
                                        <div class="invalid-feedback">@error('company_name'){{ $message }}@else Enter a valid company name (minimum 2 characters). @enderror</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="contactPerson">Contact Person <span class="text-danger">*</span></label>
                                        <input type="text" id="contactPerson" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person') }}" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                                        <div class="invalid-feedback">@error('contact_person'){{ $message }}@else Enter a valid contact person name. @enderror</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="addressLine1">Address Line 1 <span class="text-danger">*</span></label>
                                        <input type="text" id="addressLine1" name="address_line1" class="form-control @error('address_line1') is-invalid @enderror" value="{{ old('address_line1') }}" minlength="5" maxlength="255" required>
                                        <div class="invalid-feedback">@error('address_line1'){{ $message }}@else Enter an address of at least 5 characters. @enderror</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="addressLine2">Address Line 2</label>
                                        <input type="text" id="addressLine2" name="address_line2" class="form-control @error('address_line2') is-invalid @enderror" value="{{ old('address_line2') }}" maxlength="255">
                                        @error('address_line2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="addressLine3">Address Line 3</label>
                                        <input type="text" id="addressLine3" name="address_line3" class="form-control @error('address_line3') is-invalid @enderror" value="{{ old('address_line3') }}" maxlength="255">
                                        @error('address_line3')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="pincode">Pincode <span class="text-danger">*</span></label>
                                        <input type="text" id="pincode" name="pincode" class="form-control @error('pincode') is-invalid @enderror" value="{{ old('pincode') }}" inputmode="numeric" minlength="6" maxlength="6" pattern="[1-9][0-9]{5}" required>
                                        <div class="invalid-feedback">@error('pincode'){{ $message }}@else Enter a valid 6-digit Indian pincode. @enderror</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="city">City <span class="text-danger">*</span></label>
                                        <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                                        <div class="invalid-feedback">@error('city'){{ $message }}@else Enter a valid city name. @enderror</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="state">State <span class="text-danger">*</span></label>
                                        <input type="text" id="state" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state') }}" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                                        <div class="invalid-feedback">@error('state'){{ $message }}@else Enter a valid state name. @enderror</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="phoneNumber">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" id="phoneNumber" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" inputmode="numeric" minlength="10" maxlength="10" pattern="[6-9][0-9]{9}" required>
                                        <div class="invalid-feedback">@error('phone_number'){{ $message }}@else Enter a valid 10-digit Indian mobile number. @enderror</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="customerEmail">Email <span class="text-danger">*</span></label>
                                        <input type="email" id="customerEmail" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" maxlength="150" autocomplete="email" required>
                                        <div class="invalid-feedback">@error('email'){{ $message }}@else Enter a valid email address. @enderror</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="kycType">KYC Type</label>
                                        <select name="kyc_type" id="kycType" class="form-select @error('kyc_type') is-invalid @enderror">
                                            <option value="Aadhar Card" {{ old('kyc_type', 'Aadhar Card') === 'Aadhar Card' ? 'selected' : '' }}>Aadhar Card</option>
                                        </select>
                                        @error('kyc_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="kycNumber">Aadhaar Number</label>
                                        <div class="input-group">
                                            <input type="text" id="kycNumber" name="kyc_number" class="form-control text-uppercase @error('kyc_number') is-invalid @enderror" value="{{ old('kyc_number') }}" maxlength="12" inputmode="numeric" pattern="[2-9][0-9]{11}" autocomplete="off">
                                            <button type="button" class="btn btn-primary" id="kycVerifyBtn">
                                                <i class="ti ti-shield-check me-1"></i>Verify
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">@error('kyc_number'){{ $message }}@else Enter a valid 12-digit Aadhaar number. @enderror</div>
                                        <small class="text-muted" id="kycHint">Enter the 12-digit Aadhaar number and click Verify.</small>
                                    </div>
                                    <div class="col-md-6 mb-3" id="aadharFrontUploadWrap">
                                        <label class="form-label" for="aadharFrontDocument">Aadhaar Front Image <span class="text-danger">*</span></label>
                                        <input type="file" id="aadharFrontDocument" name="aadhar_front_document" class="form-control @error('aadhar_front_document') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                                        <div class="form-text">Upload the Aadhaar front image (JPG, JPEG or PNG, up to 5 MB)</div>
                                        @error('aadhar_front_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12 mb-3" id="kycVerifyStatusWrap" style="display: none;">
                                        <div id="kycVerifyStatus" class="alert alert-success mb-0"></div>
                                    </div>
                                    <!-- <div class="col-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="emailOptOut" name="email_opt_out" value="1" {{ old('email_opt_out') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="emailOptOut">Email Opt Out</label>
                                        </div>
                                    </div> -->
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
                                <div id="csbVFields" class="{{ old('csb_type', 'csb_iv') === 'csb_v' ? '' : 'd-none' }}" aria-hidden="{{ old('csb_type', 'csb_iv') === 'csb_v' ? 'false' : 'true' }}">
                                    <hr class="my-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                        <h5 class="mb-0">CSB V Information</h5>
                                        <span class="badge bg-light text-dark">Additional details</span>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="isLut" name="is_lut" value="1" {{ old('is_lut') ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="isLut">LUT (Against Bond or UT)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="adCode">AD Code <span class="text-danger">*</span></label>
                                            <input type="text" id="adCode" name="ad_code" class="form-control @error('ad_code') is-invalid @enderror" value="{{ old('ad_code') }}" inputmode="numeric" maxlength="14" pattern="[0-9]{14}" data-csb-v-required>
                                            <div class="invalid-feedback">@error('ad_code'){{ $message }}@else Enter exactly 14 numeric digits. @enderror</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="iecNumber">IEC Number <span class="text-danger">*</span></label>
                                            <input type="text" id="iecNumber" name="iec_number" class="form-control text-uppercase @error('iec_number') is-invalid @enderror" value="{{ old('iec_number') }}" maxlength="10" pattern="[A-Za-z0-9]{10}" data-csb-v-required>
                                            <div class="invalid-feedback">@error('iec_number'){{ $message }}@else Enter exactly 10 letters or digits. @enderror</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="adCodeDocument">AD Code Document <span class="text-danger">*</span></label>
                                            <input type="file" id="adCodeDocument" name="ad_code_document" class="form-control @error('ad_code_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" data-csb-v-required>
                                            <div class="form-text">PDF, JPG, JPEG or PNG, up to 5 MB.</div>
                                            @error('ad_code_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="iecDocument">IEC Document <span class="text-danger">*</span></label>
                                            <input type="file" id="iecDocument" name="iec_document" class="form-control @error('iec_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" data-csb-v-required>
                                            <div class="form-text">PDF, JPG, JPEG or PNG, up to 5 MB.</div>
                                            @error('iec_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="bankAccountNumber">Bank Account Number <span class="text-danger">*</span></label>
                                            <input type="text" id="bankAccountNumber" name="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror" value="{{ old('bank_account_number') }}" inputmode="numeric" minlength="9" maxlength="18" pattern="[0-9]{9,18}" data-csb-v-required>
                                            <div class="invalid-feedback">@error('bank_account_number'){{ $message }}@else Enter 9 to 18 numeric digits. @enderror</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="bankType">Bank Type <span class="text-danger">*</span></label>
                                            <select id="bankType" name="bank_type" class="form-select @error('bank_type') is-invalid @enderror" data-csb-v-required>
                                                <option value="">Select Bank Type</option>
                                                <option value="private" {{ old('bank_type') === 'private' ? 'selected' : '' }}>Private</option>
                                                <option value="government" {{ old('bank_type') === 'government' ? 'selected' : '' }}>Government</option>
                                            </select>
                                            @error('bank_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <div id="lutFields">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 mt-2">
                                            <h6 class="mb-0">LUT Details</h6>
                                            <span class="badge bg-light text-dark">Required when LUT is selected</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label" for="lutBondStartYear">LUT Bond Start Year <span class="text-danger">*</span></label>
                                                <select id="lutBondStartYear" name="lut_bond_start_year" class="form-select">
                                                    <option value="">Select Start Year</option>
                                                    @foreach($lutStartYears as $lutStartYear)
                                                        <option value="{{ $lutStartYear }}" {{ (string) $lutStartYear === (string) $savedLutStartYear ? 'selected' : '' }}>{{ $lutStartYear }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label" for="lutBondEndYear">LUT Bond End Year <span class="text-danger">*</span></label>
                                                <select id="lutBondEndYear" class="form-select" data-saved-end-year="{{ $savedLutEndYear }}" disabled>
                                                    <option value="">Select Start Year First</option>
                                                </select>
                                                <input type="hidden" id="lutBondYear" name="lut_bond_year" value="{{ $savedLutBondYear }}">
                                                @error('lut_bond_year')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label" for="lutExpiryDate">LUT Expiry Date <span class="text-danger">*</span></label>
                                                <input type="date" id="lutExpiryDate" name="lut_expiry_date" class="form-control @error('lut_expiry_date') is-invalid @enderror" value="{{ old('lut_expiry_date') }}" readonly>
                                                <div class="form-text">Automatically set to 31 March of the selected LUT Bond End Year.</div>
                                                @error('lut_expiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <h6 class="mb-3 mt-2">Document List</h6>
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label" for="lutDocument">LUT Document <span class="text-danger">*</span></label>
                                                <input type="file" id="lutDocument" name="lut_document" class="form-control @error('lut_document') is-invalid @enderror" accept=".pdf">
                                                <div class="form-text">Letter of Undertaking in PDF format, up to 5 MB.</div>
                                                @error('lut_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="mb-3 mt-2">Billing Details</h6>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label" for="billingAddress">Billing Address <span class="text-danger">*</span></label>
                                            <textarea id="billingAddress" name="billing_address" class="form-control @error('billing_address') is-invalid @enderror" rows="3" minlength="10" maxlength="1000" data-csb-v-required>{{ old('billing_address') }}</textarea>
                                            @error('billing_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="billingContact">Billing Contact Number <span class="text-danger">*</span></label>
                                            <input type="tel" id="billingContact" name="billing_contact" class="form-control @error('billing_contact') is-invalid @enderror" value="{{ old('billing_contact') }}" inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}" data-csb-v-required>
                                            <div class="invalid-feedback">@error('billing_contact'){{ $message }}@else Enter a valid 10-digit Indian mobile number. @enderror</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="billingEmail">Billing Email <span class="text-danger">*</span></label>
                                            <input type="email" id="billingEmail" name="billing_email" class="form-control @error('billing_email') is-invalid @enderror" value="{{ old('billing_email') }}" maxlength="255" data-csb-v-required>
                                            @error('billing_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <h6 class="mb-3 mt-2">Merchant Agreement</h6>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label" for="merchantAgreement">Signed Merchant Agreement <span class="text-danger">*</span></label>
                                            <input type="file" id="merchantAgreement" name="merchant_agreement" class="form-control @error('merchant_agreement') is-invalid @enderror" accept=".pdf" data-csb-v-required>
                                            <div class="form-text">Signed agreement with company stamp and signature, PDF up to 10 MB.</div>
                                            @error('merchant_agreement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-12 mb-4">
                                            <div class="form-check">
                                                <input type="checkbox" id="termsAccepted" name="terms_accepted" value="1" class="form-check-input @error('terms_accepted') is-invalid @enderror" {{ old('terms_accepted') ? 'checked' : '' }} data-csb-v-required>
                                                <label class="form-check-label" for="termsAccepted">I declare that the information provided is correct and authorize the submitted documents for KYC verification.</label>
                                                @error('terms_accepted')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i>Save Customer
                                </button>
                            </form>
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
                                            <th>KYC Details</th>
                                            <th>Added On</th>
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
                                                <td>
                                                    <div>{{ $savedCustomer->address_line1 }}</div>
                                                    @if($savedCustomer->address_line2 || $savedCustomer->address_line3)
                                                        <div class="small text-muted">{{ collect([$savedCustomer->address_line2, $savedCustomer->address_line3])->filter()->implode(', ') }}</div>
                                                    @endif
                                                    <div class="small text-muted">{{ $savedCustomer->city }}, {{ $savedCustomer->state }} - {{ $savedCustomer->pincode }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $savedCustomer->csb_type === 'csb_v' ? 'bg-primary' : 'bg-secondary' }}">
                                                        {{ $savedCustomer->csb_type === 'csb_v' ? 'CSB V' : 'CSB IV' }}
                                                    </span>
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
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">No customers added yet.</td>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('addCustomerForm');
        const kycType = document.getElementById('kycType');
        const kycNumber = document.getElementById('kycNumber');
        const kycHint = document.getElementById('kycHint');
        const customerType = document.getElementById('businessCategoryId');
        const csbType = document.getElementById('csbType');
        const csbIvOption = csbType.querySelector('[data-personal-option]');
        const csbTypeHint = document.getElementById('csbTypeHint');
        const csbVFields = document.getElementById('csbVFields');
        const isLut = document.getElementById('isLut');
        const lutFields = document.getElementById('lutFields');
        const lutStartYear = document.getElementById('lutBondStartYear');
        const lutEndYear = document.getElementById('lutBondEndYear');
        const lutBondYear = document.getElementById('lutBondYear');
        const lutExpiryDate = document.getElementById('lutExpiryDate');
        const lutDocument = document.getElementById('lutDocument');
        const numericFields = [
            document.getElementById('pincode'),
            document.getElementById('phoneNumber'),
            document.getElementById('adCode'),
            document.getElementById('bankAccountNumber'),
            document.getElementById('billingContact')
        ].filter(Boolean);
        const kycFormats = {
            'Aadhar Card': { pattern: '[2-9][0-9]{11}', length: 12, hint: 'Enter a 12-digit Aadhaar number.' }
        };

        numericFields.forEach(function (field) {
            field.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, Number(this.maxLength));
            });
        });

        document.getElementById('iecNumber')?.addEventListener('input', function () {
            this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(0, 10);
        });

        function updateKycValidation() {
            const format = kycFormats[kycType.value];
            kycNumber.required = false;
            kycNumber.value = kycNumber.value.replace(/\s/g, '');
            if (format) {
                kycNumber.pattern = format.pattern;
                kycNumber.maxLength = format.length;
                kycNumber.placeholder = format.hint.replace('Format: ', 'e.g. ');
                kycHint.textContent = format.hint;
            } else {
                kycNumber.removeAttribute('pattern');
                kycNumber.maxLength = 15;
                kycNumber.placeholder = '';
                kycHint.textContent = 'Select a KYC type to see the required format.';
            }
        }

        function syncLutExpiryDate() {
            if (!lutEndYear.value) {
                lutExpiryDate.value = '';
                return;
            }

            lutExpiryDate.value = `${lutEndYear.value}-03-31`;
        }

        function updateLutYears(restoreSavedYear) {
            const startYear = Number(lutStartYear.value);
            const savedEndYear = restoreSavedYear ? lutEndYear.dataset.savedEndYear : '';
            lutEndYear.innerHTML = '';

            if (!startYear) {
                lutEndYear.appendChild(new Option('Select Start Year First', ''));
                lutEndYear.disabled = true;
                lutBondYear.value = '';
                lutExpiryDate.value = '';
                return;
            }

            lutEndYear.appendChild(new Option('Select End Year', ''));
            for (let offset = 1; offset <= 5; offset += 1) {
                const year = String(startYear + offset);
                lutEndYear.appendChild(new Option(year, year));
            }
            lutEndYear.disabled = false;
            lutEndYear.value = savedEndYear && lutEndYear.querySelector(`option[value="${savedEndYear}"]`)
                ? savedEndYear
                : String(startYear + 1);
            lutBondYear.value = `${startYear}-${lutEndYear.value.slice(-2)}`;
            syncLutExpiryDate();
        }

        function updateLutState() {
            const csbVEnabled = csbType.value === 'csb_v';
            const enabled = csbVEnabled && isLut.checked;
            lutFields.classList.toggle('d-none', !csbVEnabled);
            lutFields.querySelectorAll('input, select').forEach(function (field) {
                field.disabled = !enabled;
            });
            lutStartYear.required = enabled;
            lutEndYear.required = enabled;
            lutExpiryDate.required = enabled;
            lutDocument.required = enabled;
            if (enabled) {
                updateLutYears(true);
            }
        }

        function updateCustomerTypeState() {
            const selectedOption = customerType.options[customerType.selectedIndex];
            const isBusiness = selectedOption?.dataset.userType === 'business';

            csbIvOption.disabled = isBusiness;
            csbIvOption.hidden = isBusiness;
            if (isBusiness) {
                csbType.value = 'csb_v';
            }
            csbTypeHint.textContent = isBusiness
                ? 'Business customers are eligible for CSB V only.'
                : 'Personal customers can select CSB IV or CSB V.';

            updateCsbState();
        }

        function updateCsbState() {
            const enabled = csbType.value === 'csb_v';
            csbVFields.classList.toggle('d-none', !enabled);
            csbVFields.setAttribute('aria-hidden', enabled ? 'false' : 'true');
            csbVFields.querySelectorAll('input, select, textarea').forEach(function (field) {
                field.disabled = !enabled;
            });
            csbVFields.querySelectorAll('[data-csb-v-required]').forEach(function (field) {
                field.required = enabled;
            });
            updateLutState();
        }

        kycType.addEventListener('change', updateKycValidation);
        kycNumber.addEventListener('input', updateKycValidation);
        customerType.addEventListener('change', updateCustomerTypeState);
        csbType.addEventListener('change', updateCsbState);
        isLut.addEventListener('change', updateLutState);
        lutStartYear.addEventListener('change', function () {
            updateLutYears(false);
        });
        lutEndYear.addEventListener('change', function () {
            lutBondYear.value = lutStartYear.value && lutEndYear.value
                ? `${lutStartYear.value}-${lutEndYear.value.slice(-2)}`
                : '';
            syncLutExpiryDate();
        });

        updateKycValidation();
        updateCustomerTypeState();

        // ---------- Cashfree Aadhaar verification (same flow as KYC) ----------
        const kycVerifyBtn = document.getElementById('kycVerifyBtn');
        const aadharFrontDocument = document.getElementById('aadharFrontDocument');
        const kycVerifyStatusWrap = document.getElementById('kycVerifyStatusWrap');
        const kycVerifyStatus = document.getElementById('kycVerifyStatus');
        let aadharVerified = false;

        function showKycVerifyStatus(message, type) {
            kycVerifyStatusWrap.style.display = 'block';
            kycVerifyStatus.className = 'alert mb-0 alert-' + type;
            kycVerifyStatus.innerHTML = '<i class="ti me-1 ' + (type === 'success' ? 'ti-circle-check' : 'ti-alert-triangle') + '"></i>' + message;
        }

        function verifyAadharForCustomer() {
            const aadhar = kycNumber.value.replace(/\s+/g, '');

            if (!aadhar) {
                showKycVerifyStatus('Please enter your Aadhaar number.', 'danger');
                kycNumber.focus();
                return;
            }
            if (!/^[2-9][0-9]{11}$/.test(aadhar)) {
                showKycVerifyStatus('Please enter a valid 12-digit Aadhaar number.', 'danger');
                kycNumber.focus();
                return;
            }
            if (!aadharFrontDocument.files || !aadharFrontDocument.files[0]) {
                showKycVerifyStatus('Please upload the Aadhaar front image before verification.', 'danger');
                aadharFrontDocument.focus();
                return;
            }
            if (!['image/jpeg', 'image/png'].includes(aadharFrontDocument.files[0].type)) {
                showKycVerifyStatus('The Aadhaar front image must be a JPG or PNG file.', 'danger');
                return;
            }
            if (aadharFrontDocument.files[0].size > 5 * 1024 * 1024) {
                showKycVerifyStatus('The Aadhaar front image must not exceed 5 MB.', 'danger');
                return;
            }

            const verifyData = new FormData();
            verifyData.append('aadhar_number', aadhar);
            verifyData.append('aadhar_front_document', aadharFrontDocument.files[0]);

            kycVerifyBtn.disabled = true;
            kycVerifyBtn.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i>Verifying...';

            fetch('{{ route('customer.verify.exporter-customer-aadhar') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: verifyData
                })
                .then(response => response.json().catch(() => ({
                    success: false,
                    message: 'Server error. Please try again.'
                })))
                .then(data => {
                    if (data.success) {
                        aadharVerified = true;
                        showKycVerifyStatus(data.message || 'Aadhaar verified successfully through Cashfree!', 'success');
                        kycVerifyBtn.innerHTML = '<i class="ti ti-circle-check me-1"></i>Verified';
                        kycVerifyBtn.classList.remove('btn-primary');
                        kycVerifyBtn.classList.add('btn-success');
                        kycVerifyBtn.disabled = true;
                        kycNumber.readOnly = true;
                        aadharFrontDocument.disabled = true;
                    } else {
                        showKycVerifyStatus(data.message || 'Aadhaar verification failed.', 'danger');
                        kycVerifyBtn.innerHTML = '<i class="ti ti-shield-check me-1"></i>Verify';
                        kycVerifyBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Aadhaar verify error:', error);
                    showKycVerifyStatus('A network error occurred while verifying your Aadhaar. Please try again.', 'danger');
                    kycVerifyBtn.innerHTML = '<i class="ti ti-shield-check me-1"></i>Verify';
                    kycVerifyBtn.disabled = false;
                });
        }

        kycVerifyBtn.addEventListener('click', verifyAadharForCustomer);

        form.addEventListener('submit', function (event) {
            const requiresAadharVerification = kycNumber.value.trim() !== '' && !aadharVerified;
            if (!form.checkValidity() || requiresAadharVerification) {
                event.preventDefault();
                event.stopPropagation();
                if (requiresAadharVerification) {
                    showKycVerifyStatus('Verify the Aadhaar number through Cashfree before saving the customer.', 'danger');
                    kycNumber.focus();
                }
                form.querySelector(':invalid')?.focus();
            }
            form.classList.add('was-validated');
        });
    });
</script>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>
</body>
</html>
