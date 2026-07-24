<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Add Country</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
</head>

<body>
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

        <div class="page-wrapper">
            <div class="content pb-0">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">Add Country</h4>
                        <p class="text-muted mb-0">Add a new country (destination) so it can be used when adding zones and rates.</p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ url('/admin/manage-rate') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i>Back to Manage Rate
                        </a>
                        <a href="{{ route('admin.add-zone') }}" class="btn btn-outline-primary">
                            <i class="ti ti-map-pin-plus me-1"></i>Add Zone
                        </a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-alert-circle me-1"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <!-- Add Country Form -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="ti ti-plus me-1"></i>New Country</h6>
                                <form id="addCountryForm" method="POST" action="{{ route('admin.add-country.store') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Select Country <span class="text-muted fw-normal">(quick-fill)</span></label>
                                        <select class="form-select" id="countryPicker" data-placeholder="— Search & select a country to auto-fill below —">
                                            <option value=""></option>
                                            <option value="AF|Afghanistan|AF">Afghanistan (AF)</option>
                                            <option value="AL|Albania|AL">Albania (AL)</option>
                                            <option value="DZ|Algeria|DZ">Algeria (DZ)</option>
                                            <option value="AD|Andorra|AD">Andorra (AD)</option>
                                            <option value="AO|Angola|AO">Angola (AO)</option>
                                            <option value="AG|Antigua and Barbuda|AG">Antigua and Barbuda (AG)</option>
                                            <option value="AR|Argentina|AR">Argentina (AR)</option>
                                            <option value="AM|Armenia|AM">Armenia (AM)</option>
                                            <option value="AU|Australia|AU">Australia (AU)</option>
                                            <option value="AT|Austria|AT">Austria (AT)</option>
                                            <option value="AZ|Azerbaijan|AZ">Azerbaijan (AZ)</option>
                                            <option value="BS|Bahamas|BS">Bahamas (BS)</option>
                                            <option value="BH|Bahrain|BH">Bahrain (BH)</option>
                                            <option value="BD|Bangladesh|BD">Bangladesh (BD)</option>
                                            <option value="BB|Barbados|BB">Barbados (BB)</option>
                                            <option value="BY|Belarus|BY">Belarus (BY)</option>
                                            <option value="BE|Belgium|BE">Belgium (BE)</option>
                                            <option value="BZ|Belize|BZ">Belize (BZ)</option>
                                            <option value="BJ|Benin|BJ">Benin (BJ)</option>
                                            <option value="BT|Bhutan|BT">Bhutan (BT)</option>
                                            <option value="BO|Bolivia|BO">Bolivia (BO)</option>
                                            <option value="BA|Bosnia and Herzegovina|BA">Bosnia and Herzegovina (BA)</option>
                                            <option value="BW|Botswana|BW">Botswana (BW)</option>
                                            <option value="BR|Brazil|BR">Brazil (BR)</option>
                                            <option value="BN|Brunei|BN">Brunei (BN)</option>
                                            <option value="BG|Bulgaria|BG">Bulgaria (BG)</option>
                                            <option value="BF|Burkina Faso|BF">Burkina Faso (BF)</option>
                                            <option value="BI|Burundi|BI">Burundi (BI)</option>
                                            <option value="CV|Cabo Verde|CV">Cabo Verde (CV)</option>
                                            <option value="KH|Cambodia|KH">Cambodia (KH)</option>
                                            <option value="CM|Cameroon|CM">Cameroon (CM)</option>
                                            <option value="CA|Canada|CA">Canada (CA)</option>
                                            <option value="CF|Central African Republic|CF">Central African Republic (CF)</option>
                                            <option value="TD|Chad|TD">Chad (TD)</option>
                                            <option value="CL|Chile|CL">Chile (CL)</option>
                                            <option value="CN|China|CN">China (CN)</option>
                                            <option value="CO|Colombia|CO">Colombia (CO)</option>
                                            <option value="KM|Comoros|KM">Comoros (KM)</option>
                                            <option value="CG|Congo|CG">Congo (CG)</option>
                                            <option value="CR|Costa Rica|CR">Costa Rica (CR)</option>
                                            <option value="HR|Croatia|HR">Croatia (HR)</option>
                                            <option value="CU|Cuba|CU">Cuba (CU)</option>
                                            <option value="CY|Cyprus|CY">Cyprus (CY)</option>
                                            <option value="CZ|Czech Republic (Czechia)|CZ">Czech Republic (Czechia) (CZ)</option>
                                            <option value="CD|Democratic Republic of the Congo|CD">Democratic Republic of the Congo (CD)</option>
                                            <option value="DK|Denmark|DK">Denmark (DK)</option>
                                            <option value="DJ|Djibouti|DJ">Djibouti (DJ)</option>
                                            <option value="DM|Dominica|DM">Dominica (DM)</option>
                                            <option value="DO|Dominican Republic|DO">Dominican Republic (DO)</option>
                                            <option value="EC|Ecuador|EC">Ecuador (EC)</option>
                                            <option value="EG|Egypt|EG">Egypt (EG)</option>
                                            <option value="SV|El Salvador|SV">El Salvador (SV)</option>
                                            <option value="GQ|Equatorial Guinea|GQ">Equatorial Guinea (GQ)</option>
                                            <option value="ER|Eritrea|ER">Eritrea (ER)</option>
                                            <option value="EE|Estonia|EE">Estonia (EE)</option>
                                            <option value="SZ|Eswatini|SZ">Eswatini (SZ)</option>
                                            <option value="ET|Ethiopia|ET">Ethiopia (ET)</option>
                                            <option value="FJ|Fiji|FJ">Fiji (FJ)</option>
                                            <option value="FI|Finland|FI">Finland (FI)</option>
                                            <option value="FR|France|FR">France (FR)</option>
                                            <option value="GA|Gabon|GA">Gabon (GA)</option>
                                            <option value="GM|Gambia|GM">Gambia (GM)</option>
                                            <option value="GE|Georgia|GE">Georgia (GE)</option>
                                            <option value="DE|Germany|DE">Germany (DE)</option>
                                            <option value="GH|Ghana|GH">Ghana (GH)</option>
                                            <option value="GR|Greece|GR">Greece (GR)</option>
                                            <option value="GD|Grenada|GD">Grenada (GD)</option>
                                            <option value="GT|Guatemala|GT">Guatemala (GT)</option>
                                            <option value="GN|Guinea|GN">Guinea (GN)</option>
                                            <option value="GW|Guinea-Bissau|GW">Guinea-Bissau (GW)</option>
                                            <option value="GY|Guyana|GY">Guyana (GY)</option>
                                            <option value="HT|Haiti|HT">Haiti (HT)</option>
                                            <option value="HN|Honduras|HN">Honduras (HN)</option>
                                            <option value="HU|Hungary|HU">Hungary (HU)</option>
                                            <option value="IS|Iceland|IS">Iceland (IS)</option>
                                            <option value="IN|India|IN">India (IN)</option>
                                            <option value="ID|Indonesia|ID">Indonesia (ID)</option>
                                            <option value="IR|Iran|IR">Iran (IR)</option>
                                            <option value="IQ|Iraq|IQ">Iraq (IQ)</option>
                                            <option value="IE|Ireland|IE">Ireland (IE)</option>
                                            <option value="IL|Israel|IL">Israel (IL)</option>
                                            <option value="IT|Italy|IT">Italy (IT)</option>
                                            <option value="CI|Ivory Coast (Côte d'Ivoire)|CI">Ivory Coast (Côte d'Ivoire) (CI)</option>
                                            <option value="JM|Jamaica|JM">Jamaica (JM)</option>
                                            <option value="JP|Japan|JP">Japan (JP)</option>
                                            <option value="JO|Jordan|JO">Jordan (JO)</option>
                                            <option value="KZ|Kazakhstan|KZ">Kazakhstan (KZ)</option>
                                            <option value="KE|Kenya|KE">Kenya (KE)</option>
                                            <option value="KI|Kiribati|KI">Kiribati (KI)</option>
                                            <option value="KW|Kuwait|KW">Kuwait (KW)</option>
                                            <option value="KG|Kyrgyzstan|KG">Kyrgyzstan (KG)</option>
                                            <option value="LA|Laos|LA">Laos (LA)</option>
                                            <option value="LV|Latvia|LV">Latvia (LV)</option>
                                            <option value="LB|Lebanon|LB">Lebanon (LB)</option>
                                            <option value="LS|Lesotho|LS">Lesotho (LS)</option>
                                            <option value="LR|Liberia|LR">Liberia (LR)</option>
                                            <option value="LY|Libya|LY">Libya (LY)</option>
                                            <option value="LI|Liechtenstein|LI">Liechtenstein (LI)</option>
                                            <option value="LT|Lithuania|LT">Lithuania (LT)</option>
                                            <option value="LU|Luxembourg|LU">Luxembourg (LU)</option>
                                            <option value="MG|Madagascar|MG">Madagascar (MG)</option>
                                            <option value="MW|Malawi|MW">Malawi (MW)</option>
                                            <option value="MY|Malaysia|MY">Malaysia (MY)</option>
                                            <option value="MV|Maldives|MV">Maldives (MV)</option>
                                            <option value="ML|Mali|ML">Mali (ML)</option>
                                            <option value="MT|Malta|MT">Malta (MT)</option>
                                            <option value="MH|Marshall Islands|MH">Marshall Islands (MH)</option>
                                            <option value="MR|Mauritania|MR">Mauritania (MR)</option>
                                            <option value="MU|Mauritius|MU">Mauritius (MU)</option>
                                            <option value="MX|Mexico|MX">Mexico (MX)</option>
                                            <option value="FM|Micronesia|FM">Micronesia (FM)</option>
                                            <option value="MD|Moldova|MD">Moldova (MD)</option>
                                            <option value="MC|Monaco|MC">Monaco (MC)</option>
                                            <option value="MN|Mongolia|MN">Mongolia (MN)</option>
                                            <option value="ME|Montenegro|ME">Montenegro (ME)</option>
                                            <option value="MA|Morocco|MA">Morocco (MA)</option>
                                            <option value="MZ|Mozambique|MZ">Mozambique (MZ)</option>
                                            <option value="MM|Myanmar|MM">Myanmar (MM)</option>
                                            <option value="NA|Namibia|NA">Namibia (NA)</option>
                                            <option value="NR|Nauru|NR">Nauru (NR)</option>
                                            <option value="NP|Nepal|NP">Nepal (NP)</option>
                                            <option value="NL|Netherlands|NL">Netherlands (NL)</option>
                                            <option value="NZ|New Zealand|NZ">New Zealand (NZ)</option>
                                            <option value="NI|Nicaragua|NI">Nicaragua (NI)</option>
                                            <option value="NE|Niger|NE">Niger (NE)</option>
                                            <option value="NG|Nigeria|NG">Nigeria (NG)</option>
                                            <option value="KP|North Korea|KP">North Korea (KP)</option>
                                            <option value="MK|North Macedonia|MK">North Macedonia (MK)</option>
                                            <option value="NO|Norway|NO">Norway (NO)</option>
                                            <option value="OM|Oman|OM">Oman (OM)</option>
                                            <option value="PK|Pakistan|PK">Pakistan (PK)</option>
                                            <option value="PW|Palau|PW">Palau (PW)</option>
                                            <option value="PS|Palestine|PS">Palestine (PS)</option>
                                            <option value="PA|Panama|PA">Panama (PA)</option>
                                            <option value="PG|Papua New Guinea|PG">Papua New Guinea (PG)</option>
                                            <option value="PY|Paraguay|PY">Paraguay (PY)</option>
                                            <option value="PE|Peru|PE">Peru (PE)</option>
                                            <option value="PH|Philippines|PH">Philippines (PH)</option>
                                            <option value="PL|Poland|PL">Poland (PL)</option>
                                            <option value="PT|Portugal|PT">Portugal (PT)</option>
                                            <option value="QA|Qatar|QA">Qatar (QA)</option>
                                            <option value="RO|Romania|RO">Romania (RO)</option>
                                            <option value="RU|Russia|RU">Russia (RU)</option>
                                            <option value="RW|Rwanda|RW">Rwanda (RW)</option>
                                            <option value="KN|Saint Kitts and Nevis|KN">Saint Kitts and Nevis (KN)</option>
                                            <option value="LC|Saint Lucia|LC">Saint Lucia (LC)</option>
                                            <option value="VC|Saint Vincent and the Grenadines|VC">Saint Vincent and the Grenadines (VC)</option>
                                            <option value="WS|Samoa|WS">Samoa (WS)</option>
                                            <option value="SM|San Marino|SM">San Marino (SM)</option>
                                            <option value="ST|Sao Tome and Principe|ST">Sao Tome and Principe (ST)</option>
                                            <option value="SA|Saudi Arabia|SA">Saudi Arabia (SA)</option>
                                            <option value="SN|Senegal|SN">Senegal (SN)</option>
                                            <option value="RS|Serbia|RS">Serbia (RS)</option>
                                            <option value="SC|Seychelles|SC">Seychelles (SC)</option>
                                            <option value="SL|Sierra Leone|SL">Sierra Leone (SL)</option>
                                            <option value="SG|Singapore|SG">Singapore (SG)</option>
                                            <option value="SK|Slovakia|SK">Slovakia (SK)</option>
                                            <option value="SI|Slovenia|SI">Slovenia (SI)</option>
                                            <option value="SB|Solomon Islands|SB">Solomon Islands (SB)</option>
                                            <option value="SO|Somalia|SO">Somalia (SO)</option>
                                            <option value="ZA|South Africa|ZA">South Africa (ZA)</option>
                                            <option value="KR|South Korea|KR">South Korea (KR)</option>
                                            <option value="SS|South Sudan|SS">South Sudan (SS)</option>
                                            <option value="ES|Spain|ES">Spain (ES)</option>
                                            <option value="LK|Sri Lanka|LK">Sri Lanka (LK)</option>
                                            <option value="SD|Sudan|SD">Sudan (SD)</option>
                                            <option value="SR|Suriname|SR">Suriname (SR)</option>
                                            <option value="SE|Sweden|SE">Sweden (SE)</option>
                                            <option value="CH|Switzerland|CH">Switzerland (CH)</option>
                                            <option value="SY|Syria|SY">Syria (SY)</option>
                                            <option value="TW|Taiwan|TW">Taiwan (TW)</option>
                                            <option value="TJ|Tajikistan|TJ">Tajikistan (TJ)</option>
                                            <option value="TZ|Tanzania|TZ">Tanzania (TZ)</option>
                                            <option value="TH|Thailand|TH">Thailand (TH)</option>
                                            <option value="TL|Timor-Leste|TL">Timor-Leste (TL)</option>
                                            <option value="TG|Togo|TG">Togo (TG)</option>
                                            <option value="TO|Tonga|TO">Tonga (TO)</option>
                                            <option value="TT|Trinidad and Tobago|TT">Trinidad and Tobago (TT)</option>
                                            <option value="TN|Tunisia|TN">Tunisia (TN)</option>
                                            <option value="TR|Turkey|TR">Turkey (TR)</option>
                                            <option value="TM|Turkmenistan|TM">Turkmenistan (TM)</option>
                                            <option value="TV|Tuvalu|TV">Tuvalu (TV)</option>
                                            <option value="UG|Uganda|UG">Uganda (UG)</option>
                                            <option value="UA|Ukraine|UA">Ukraine (UA)</option>
                                            <option value="AE|United Arab Emirates|AE">United Arab Emirates (AE)</option>
                                            <option value="UK|United Kingdom|UK">United Kingdom (UK)</option>
                                            <option value="US|United States|US">United States (US)</option>
                                            <option value="UY|Uruguay|UY">Uruguay (UY)</option>
                                            <option value="UZ|Uzbekistan|UZ">Uzbekistan (UZ)</option>
                                            <option value="VU|Vanuatu|VU">Vanuatu (VU)</option>
                                            <option value="VA|Vatican City|VA">Vatican City (VA)</option>
                                            <option value="VE|Venezuela|VE">Venezuela (VE)</option>
                                            <option value="VN|Vietnam|VN">Vietnam (VN)</option>
                                            <option value="YE|Yemen|YE">Yemen (YE)</option>
                                            <option value="ZM|Zambia|ZM">Zambia (ZM)</option>
                                            <option value="ZW|Zimbabwe|ZW">Zimbabwe (ZW)</option>
                                        </select>
                                        <small class="text-muted">Pick a country to auto-fill the fields below. You can still edit them before saving.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Country Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Germany" required>
                                        <small class="text-muted">The full country name. A short code is auto-generated if left blank.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Short Code</label>
                                        <input type="text" class="form-control" id="code" name="code" placeholder="e.g. DE" maxlength="10">
                                        <small class="text-muted">Optional. Must be unique. Auto-derived from the name if blank.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">ISO Country Code</label>
                                        <input type="text" class="form-control" id="country_code" name="country_code" placeholder="e.g. DE" maxlength="5">
                                        <small class="text-muted">Optional. ISO 3166-1 alpha-2 code.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Add Service <span class="text-muted fw-normal">(optional)</span></label>
                                        <select class="form-select" id="service_ids" name="service_ids[]" multiple="multiple" data-placeholder="— Select services to add for this country —">
                                            @foreach($courierServices as $service)
                                                <option value="{{ $service->id }}">
                                                    {{ $service->method ?? ('Service #' . $service->id) }}
                                                    @if(!empty($service->service_code)) [{{ $service->service_code }}] @endif
                                                    @if(!empty($service->country)) ({{ $service->country }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Pick one or more existing courier services to make available for this country. Each selected service is cloned with this country's code — the original service is left untouched.</small>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                        <label class="form-check-label fw-bold" for="is_active">Active</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="ti ti-device-floppy me-1"></i>Add Country
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Countries List -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="ti ti-list me-1"></i>Existing Countries ({{ $destinations->count() }})</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Code</th>
                                                <th>ISO</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($destinations as $i => $dest)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $dest->name }}</td>
                                                    <td><span class="badge bg-light text-dark">{{ $dest->code }}</span></td>
                                                    <td>{{ $dest->country_code ?: '—' }}</td>
                                                    <td>
                                                        @if($dest->is_active)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
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
        </div>

    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
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
            // Initialise the searchable "Select Country" dropdown. Each
            // option value is "ISO|CountryName|ISO" so selecting one can
            // auto-fill the Country Name, Short Code and ISO Country Code
            // fields below.
            $('#countryPicker').select2({
                width: '100%',
                placeholder: $('#countryPicker').data('placeholder'),
                allowClear: true
            });

            // When a country is picked, split the option value and fill the
            // three text fields. The admin can still edit them afterwards.
            $('#countryPicker').on('select2:select', function() {
                var parts = (this.value || '').split('|');
                if (parts.length === 3) {
                    $('#country_code').val(parts[0]);
                    $('#name').val(parts[1]);
                    $('#code').val(parts[2]);
                }
            });

            // When the selection is cleared, leave the fields as-is so the
            // admin's edits are not wiped out unexpectedly.

            // Initialise the multi-select "Add Service" dropdown as a
            // searchable Select2 control so the admin can pick one or more
            // existing courier services to clone for the new country.
            $('#service_ids').select2({
                width: '100%',
                placeholder: $('#service_ids').data('placeholder'),
                allowClear: true,
                closeOnSelect: false
            });

            $('#addCountryForm').on('submit', function() {
                $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Adding...');
            });
        });
    </script>

</body>
</html>
