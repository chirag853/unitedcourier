<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - KYC Rejected</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <style>
        .table td, .table th { vertical-align: middle; white-space: normal; }
        .org-cell { max-width: 220px; }
        .status-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: #fee2e2;
            color: #b91c1c;
        }
        .btn-profile {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border: 1px solid #6b21a8;
            border-radius: 4px;
            background: #f3e8ff;
            color: #6b21a8;
            text-decoration: none;
        }
        .btn-profile:hover { background: #6b21a8; color: #fff; }
        .btn-export {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border: 1px solid #0369a1;
            border-radius: 4px;
            background: #e0f2fe;
            color: #0369a1;
            text-decoration: none;
        }
        .btn-export:hover { background: #0369a1; color: #fff; }
    </style>
</head>
<body>
    <div class="main-wrapper">
        @include('admin.partials.header')
        @include('admin.partials.sidebar')

        <div class="page-wrapper">
            <div class="content pb-0">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-1">Rejected Customer KYC</h5>
                                    <p class="card-text mb-0">View all KYC submissions rejected by the admin team</p>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.kyc-export', ['status' => 'rejected']) }}" class="btn-export" title="Export rejected KYC records to Excel">
                                        <i class="ti ti-file-spreadsheet me-1"></i>Export Excel
                                    </a>
                                    <a href="{{ route('admin.kyc-export', ['status' => 'all']) }}" class="btn-export" title="Export all KYC records to Excel">
                                        <i class="ti ti-file-export me-1"></i>Export All
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if ($message = Session::get('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="ti ti-circle-check me-2"></i>{{ $message }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-hover" id="kycRejectedTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Customer</th>
                                                <th>KYC Type</th>
                                                <th>Organization</th>
                                                <th>Submitted At</th>
                                                <th>Rejection Remark</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rejectedKycDetails as $key => $kyc)
                                                @php($customerId = $kyc->customer->id ?? null)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <strong>{{ $kyc->customer->first_name ?? '' }} {{ $kyc->customer->last_name ?? '' }}</strong>
                                                        <div class="small"><a href="mailto:{{ $kyc->customer->email ?? '' }}" class="text-decoration-none">{{ $kyc->customer->email ?? '—' }}</a></div>
                                                        <div class="small text-muted">{{ $kyc->customer->phone_number ?? '—' }}</div>
                                                    </td>
                                                    <td>
                                                        @if(($kyc->kyc_type ?? 'personal') === 'business')
                                                            <span class="badge bg-info text-white">Business (CSB-V)</span>
                                                        @else
                                                            <span class="badge bg-primary">Personal (CSB-IV)</span>
                                                        @endif
                                                    </td>
                                                    <td class="org-cell">
                                                        {{ $kyc->organization_name ?? '—' }}
                                                        <div class="small text-muted">{{ $kyc->gst_number ?? '—' }}</div>
                                                    </td>
                                                    <td>{{ optional($kyc->created_at)->format('d M Y, h:i A') ?? '—' }}</td>
                                                    <td class="org-cell">{{ $rejectedRemarks[$kyc->customer_id . ':' . ($kyc->kyc_type ?? 'personal')] ?? '—' }}</td>
                                                    <td><span class="status-pill">Rejected</span></td>
                                                    <td>
                                                        @if($customerId)
                                                            <a href="{{ route('admin.customer-profile', $customerId) }}" class="btn-profile" title="View customer profile and full KYC">
                                                                <i class="ti ti-user me-1"></i>View
                                                            </a>
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

    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#kycRejectedTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25,
                language: {
                    search: 'Search Rejected:',
                    lengthMenu: 'Show _MENU_ entries per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    emptyTable: 'No rejected KYC submissions found.'
                },
                columnDefs: [{ orderable: false, targets: [7] }]
            });
        });
    </script>
</body>
</html>
