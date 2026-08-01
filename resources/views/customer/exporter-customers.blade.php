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
                            <form action="{{ route('customer.exporter-customers.store') }}" method="POST" id="addCustomerForm" novalidate>
                                @csrf
                                <div class="row">
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
                                            <option value="">Select</option>
                                            @foreach(['GST (Normal)', 'Aadhar Card', 'PAN Card', 'Passport Number'] as $kycType)
                                                <option value="{{ $kycType }}" {{ old('kyc_type') === $kycType ? 'selected' : '' }}>{{ $kycType }}</option>
                                            @endforeach
                                        </select>
                                        @error('kyc_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="kycNumber">KYC Number</label>
                                        <input type="text" id="kycNumber" name="kyc_number" class="form-control text-uppercase @error('kyc_number') is-invalid @enderror" value="{{ old('kyc_number') }}" maxlength="15" autocomplete="off">
                                        <div class="invalid-feedback">@error('kyc_number'){{ $message }}@else Enter a valid KYC number. @enderror</div>
                                        <small class="text-muted" id="kycHint">Select a KYC type to see the required format.</small>
                                    </div>
                                    <!-- <div class="col-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="emailOptOut" name="email_opt_out" value="1" {{ old('email_opt_out') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="emailOptOut">Email Opt Out</label>
                                        </div>
                                    </div> -->
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
                                            <th>Company</th>
                                            <th>Contact Person</th>
                                            <th>Contact Details</th>
                                            <th>Address</th>
                                            <th>KYC Details</th>
                                            <th>Added On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($exporterCustomers as $savedCustomer)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
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
                                                <td colspan="7" class="text-center text-muted py-4">No customers added yet.</td>
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
        const numericFields = [document.getElementById('pincode'), document.getElementById('phoneNumber')];
        const kycFormats = {
            'GST (Normal)': { pattern: '[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]', length: 15, hint: 'Format: 22AAAAA0000A1Z5' },
            'Aadhar Card': { pattern: '[2-9][0-9]{11}', length: 12, hint: 'Enter a 12-digit Aadhaar number.' },
            'PAN Card': { pattern: '[A-Z]{5}[0-9]{4}[A-Z]', length: 10, hint: 'Format: AAAAA0000A' },
            'Passport Number': { pattern: '[A-Z][0-9]{7}', length: 8, hint: 'Format: A1234567' }
        };

        numericFields.forEach(function (field) {
            field.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, Number(this.maxLength));
            });
        });

        function updateKycValidation() {
            const format = kycFormats[kycType.value];
            kycNumber.required = Boolean(format);
            kycNumber.value = kycNumber.value.toUpperCase().replace(/\s/g, '');
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

        kycType.addEventListener('change', updateKycValidation);
        kycNumber.addEventListener('input', updateKycValidation);
        updateKycValidation();

        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
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
