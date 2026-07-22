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
            $('#addCountryForm').on('submit', function() {
                $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Adding...');
            });
        });
    </script>

</body>
</html>
