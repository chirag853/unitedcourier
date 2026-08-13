<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Create User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create admin users with module-wise access for United Courier">
    <meta name="keywords" content="user management, access control, courier, logistics">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .status-badge-active {
            background-color: #d4edda;
            color: #155724;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-badge-inactive {
            background-color: #f8d7da;
            color: #721c24;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .type-badge-super {
            background-color: #cce5ff;
            color: #004085;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .type-badge-admin {
            background-color: #fff3cd;
            color: #856404;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .table-actions .btn-icon {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 12px;
        }
        .dataTables_wrapper .dataTables_info {
            margin-top: 8px;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 8px;
        }
        .module-access-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            background: #fafbfc;
        }
        .module-access-item {
            display: flex;
            align-items: center;
            padding: 8px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }
        .module-access-item:hover {
            background-color: #f0f6ff;
        }
        .module-access-item input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .module-access-item .module-icon {
            margin-right: 8px;
            font-size: 18px;
            color: #007bff;
        }
        .module-access-item .module-label {
            font-weight: 500;
            color: #333;
        }
        .access-pill {
            display: inline-block;
            background: #e7f1ff;
            color: #0056b3;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 11px;
            margin: 2px;
        }
        .access-pill.full {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>

<body>
    <!-- Begin Wrapper -->
    <div class="main-wrapper">
        <!-- Header Start -->
        @include('admin.partials.header')
        <!-- Header End -->

        <!-- Mobile Menu Search -->
        <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
            <div class="offcanvas-body">
                <div class="card shadow-none mb-0">
                    <div class="px-3 py-2 d-flex flex-row align-items-center" id="search-top">
                        <i class="ti ti-search fs-22"></i>
                        <input type="search" class="form-control border-0" placeholder="Search">
                        <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x fs-22"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidenav Menu Start -->
        @include('admin.partials.sidebar')
        <!-- Sidenav Menu End -->

        <!-- Page Content -->
        <div class="page-wrapper">
            <div class="content">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">Create User <span class="badge badge-soft-primary ms-2">{{ count($users) }}</span></h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Admin Management</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create User</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="ti ti-user-plus me-1"></i>Add User
                        </button>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="usersTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Designation</th>
                                        <th>Type</th>
                                        <th>Module Access</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-title bg-primary rounded-circle">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                                <span class="fw-medium">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $user->email ?? '—' }}</td>
                                        <td>{{ $user->mobile ?? '—' }}</td>
                                        <td>{{ $user->designation ?? '—' }}</td>
                                        <td>
                                            @if($user->isSuperAdmin())
                                                <span class="type-badge-super">Super Admin</span>
                                            @else
                                                <span class="type-badge-admin">Admin</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->isSuperAdmin())
                                                <span class="access-pill full">Full Access</span>
                                            @else
                                                @php
                                                    $granted = $user->getAccessModules();
                                                @endphp
                                                @if(!empty($granted))
                                                    @foreach($granted as $mod)
                                                        <span class="access-pill">{{ $modules[$mod]['label'] ?? $mod }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No access</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->status == 1)
                                                <span class="status-badge-active">Active</span>
                                            @else
                                                <span class="status-badge-inactive">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}</td>
                                        <td class="text-center table-actions">
                                            @if(!$user->isSuperAdmin())
                                            <button type="button" class="btn btn-sm btn-icon btn-soft-primary edit-user-btn"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-mobile="{{ $user->mobile }}"
                                                data-designation="{{ $user->designation }}"
                                                data-state="{{ $user->state }}"
                                                data-city="{{ $user->city }}"
                                                data-status="{{ $user->status }}"
                                                data-type="{{ $user->type }}"
                                                data-modules='@json($user->getAccessModules())'
                                                data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <a href="{{ route('admin.create-user.delete', $user->id) }}"
                                                class="btn btn-sm btn-icon btn-soft-danger delete-user-btn"
                                                title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this user?');">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                            @else
                                            <span class="text-muted"><i class="ti ti-lock"></i></span>
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

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addUserForm" method="POST" action="{{ route('admin.create-user.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">
                            <i class="ti ti-user-plus me-2"></i>Add User
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required placeholder="Enter full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required placeholder="Enter email address">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile</label>
                                <input type="text" class="form-control" name="mobile" placeholder="Enter mobile number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" required placeholder="Enter password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Designation</label>
                                <input type="text" class="form-control" name="designation" placeholder="e.g. Accounts Manager">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name="state" placeholder="Enter state">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" placeholder="Enter city">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">User Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="type" id="add_type">
                                    <option value="Admin" selected>Admin</option>
                                    <option value="Super Admin">Super Admin</option>
                                </select>
                                <small class="text-muted">Super Admin gets full access to all modules.</small>
                            </div>
                            <div class="col-12" id="addModuleAccessWrap">
                                <label class="form-label fw-semibold">
                                    <i class="ti ti-shield-lock me-1"></i>Module Access
                                    <small class="text-muted d-block">Select the modules this user can access. Leave unchecked for no access.</small>
                                </label>
                                <div class="module-access-card">
                                    <div class="row g-2">
                                        @foreach($modules as $key => $module)
                                        <div class="col-md-6">
                                            <label class="module-access-item">
                                                <input type="checkbox" name="module_access[]" value="{{ $key }}">
                                                <i class="ti {{ $module['icon'] }} module-icon"></i>
                                                <span class="module-label">{{ $module['label'] }}</span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editUserForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">
                            <i class="ti ti-user-edit me-2"></i>Edit User
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="edit_name" required placeholder="Enter full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="edit_email" required placeholder="Enter email address">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile</label>
                                <input type="text" class="form-control" name="mobile" id="edit_mobile" placeholder="Enter mobile number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password <small class="text-muted">(leave blank to keep current)</small></label>
                                <input type="password" class="form-control" name="password" id="edit_password" placeholder="Enter new password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Designation</label>
                                <input type="text" class="form-control" name="designation" id="edit_designation" placeholder="e.g. Accounts Manager">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name="state" id="edit_state" placeholder="Enter state">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" id="edit_city" placeholder="Enter city">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" id="edit_status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">User Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="type" id="edit_type">
                                    <option value="Admin" selected>Admin</option>
                                    <option value="Super Admin">Super Admin</option>
                                </select>
                                <small class="text-muted">Super Admin gets full access to all modules.</small>
                            </div>
                            <div class="col-12" id="editModuleAccessWrap">
                                <label class="form-label fw-semibold">
                                    <i class="ti ti-shield-lock me-1"></i>Module Access
                                    <small class="text-muted d-block">Select the modules this user can access. Leave unchecked for no access.</small>
                                </label>
                                <div class="module-access-card">
                                    <div class="row g-2" id="editModulesContainer">
                                        @foreach($modules as $key => $module)
                                        <div class="col-md-6">
                                            <label class="module-access-item">
                                                <input type="checkbox" name="module_access[]" value="{{ $key }}" class="edit-module-checkbox" data-module="{{ $key }}">
                                                <i class="ti {{ $module['icon'] }} module-icon"></i>
                                                <span class="module-label">{{ $module['label'] }}</span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                }
            });

            // Edit User - populate modal
            $(document).on('click', '.edit-user-btn', function () {
                var id = $(this).data('id');
                var updateUrl = '{{ url("/admin/create-user") }}/' + id;

                $('#editUserForm').attr('action', updateUrl);
                $('#edit_name').val($(this).data('name'));
                $('#edit_email').val($(this).data('email'));
                $('#edit_mobile').val($(this).data('mobile'));
                $('#edit_password').val('');
                $('#edit_designation').val($(this).data('designation'));
                $('#edit_state').val($(this).data('state'));
                $('#edit_city').val($(this).data('city'));
                $('#edit_status').val($(this).data('status'));
                $('#edit_type').val($(this).data('type')).trigger('change');

                // Reset all module checkboxes
                $('.edit-module-checkbox').prop('checked', false);

                // Check the modules this user has access to
                var modules = $(this).data('modules');
                if (Array.isArray(modules)) {
                    modules.forEach(function (mod) {
                        $('.edit-module-checkbox[data-module="' + mod + '"]').prop('checked', true);
                    });
                }
            });

            // Toggle module access visibility based on user type
            function toggleModuleAccess($select, $wrap) {
                if ($select.val() === 'Super Admin') {
                    $wrap.slideUp(150);
                } else {
                    $wrap.slideDown(150);
                }
            }

            $('#add_type').on('change', function () {
                toggleModuleAccess($(this), $('#addModuleAccessWrap'));
            });

            $('#edit_type').on('change', function () {
                toggleModuleAccess($(this), $('#editModuleAccessWrap'));
            });
        });
    </script>
</body>
</html>
