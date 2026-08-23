<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Manage Surcharges</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .surcharge-form {
            display: inline-block;
            margin: 0;
        }
        .surcharge-form .form-control, .surcharge-form .form-select {
            min-width: 120px;
        }
        .surcharge-price-input {
            width: 110px !important;
        }
        .surcharge-name-input {
            width: 240px !important;
        }
        .surcharge-code-input {
            width: 110px !important;
        }
    </style>
</head>

<body>

    <div class="main-wrapper">

        @include('admin.partials.header')
        @include('admin.partials.sidebar')

        <div class="page-wrapper">
            <div class="content pb-0">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">Manage Surcharges</h4>
                        <p class="text-muted-sm mb-0">Add, edit prices and delete surcharges. Selected surcharges are added to courier rate totals.</p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="{{ route('admin.manage-rate') }}" class="btn btn-outline-primary">
                            <i class="ti ti-arrow-left me-1"></i>Manage Rates
                        </a>
                    </div>
                </div>

                <!-- Flash Messages -->
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

                <!-- Add Surcharge Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="ti ti-plus me-1"></i>Add New Surcharge</h6>
                        <form method="POST" action="{{ route('admin.manage-surcharges.store') }}" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-semibold mb-1">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. AWB CHARGES" required>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold mb-1">Code</label>
                                <input type="text" name="code" class="form-control" placeholder="e.g. AWBC">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label fw-semibold mb-1">Price (₹)</label>
                                <input type="number" name="price" step="0.01" min="0" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-12 col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-device-floppy me-1"></i>Add
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Surcharges Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="surchargesTable" class="table table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Price (₹)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($surcharges as $sur)
                                    <tr id="surcharge-row-{{ $sur->id }}">
                                        <form method="POST" action="{{ route('admin.manage-surcharges.update', $sur->id) }}" class="surcharge-form">
                                            @csrf
                                            <td>{{ $sur->id }}</td>
                                            <td>
                                                <input type="text" name="name" value="{{ $sur->name }}" class="form-control form-control-sm surcharge-name-input" required>
                                            </td>
                                            <td>
                                                <input type="text" name="code" value="{{ $sur->code }}" class="form-control form-control-sm surcharge-code-input">
                                            </td>
                                            <td>
                                                <input type="number" name="price" step="0.01" min="0" value="{{ number_format($sur->price, 2, '.', '') }}" class="form-control form-control-sm surcharge-price-input" required>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <button type="submit" class="btn btn-sm btn-light" title="Save">
                                                        <i class="ti ti-device-floppy"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete-surcharge" data-id="{{ $sur->id }}" title="Delete">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </form>
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

    <!-- Delete Surcharge Form (hidden, submitted via JS) -->
    <form id="deleteSurchargeForm" method="POST" action="" class="d-none">
        @csrf
    </form>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.min.js"></script>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function () {
            if ($.fn.DataTable.isDataTable('#surchargesTable')) {
                $('#surchargesTable').DataTable().clear().destroy();
            }
            $('#surchargesTable').DataTable({
                destroy: true,
                order: [[0, 'asc']],
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: [4] }
                ]
            });

            $(document).on('click', '.btn-delete-surcharge', function () {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this surcharge?',
                    text: 'This will remove the surcharge from all rates that use it.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $('#deleteSurchargeForm').attr('action', '{{ url("/admin/manage-surcharges/delete") }}/' + id);
                        $('#deleteSurchargeForm').submit();
                    }
                });
            });
        });
    </script>

</body>

</html>