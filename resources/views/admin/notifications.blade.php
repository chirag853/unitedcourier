<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Notifications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .notification-page-item {
            overflow: hidden;
            max-height: 220px;
            transition: opacity .35s ease, transform .35s ease, max-height .45s ease,
                padding-top .45s ease, padding-bottom .45s ease;
        }
        .notification-page-item.notification-dismiss {
            opacity: 0;
            transform: translateX(60px);
            max-height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border-bottom: 0 !important;
        }
        .mark-read-btn {
            opacity: 1;
        }
        .mark-read-btn:disabled {
            opacity: 1;
        }
    </style>
</head>

<body>
    <!-- Begin Wrapper -->
    <div class="main-wrapper">
        <!-- Header Start -->
        @include('admin.partials.header')
        <!-- Header End -->

        <!-- Sidenav Menu Start -->
        @include('admin.partials.sidebar')
        <!-- Sidenav Menu End -->

        <!-- Page Content -->
        <div class="page-wrapper">
            <div class="content">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h4 class="mb-1"><i class="ti ti-bell me-1"></i>Notifications</h4>
                        <p class="text-muted mb-0 small">All notifications for your account, newest first.</p>
                    </div>
                    @if(auth()->guard('admin')->user()->unreadNotifications()->count() > 0)
                        <button type="button" id="markAllReadPageBtn" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-checks me-1"></i>Mark all as read
                        </button>
                    @endif
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        @forelse($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $isUnread = is_null($notification->read_at);
                            @endphp
                            <a href="{{ $data['url'] ?? '#' }}"
                                data-notification-id="{{ $notification->id }}"
                                data-notification-read="{{ $isUnread ? '0' : '1' }}"
                                class="notification-page-item d-flex gap-3 px-3 py-3 border-bottom text-decoration-none {{ $isUnread ? 'bg-light' : '' }}">
                                <span class="flex-shrink-0">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle {{ $isUnread ? 'bg-primary text-white' : 'bg-light text-muted' }}"
                                        style="width: 42px; height: 42px;">
                                        <i class="ti ti-bell fs-18"></i>
                                    </span>
                                </span>
                                <span class="flex-fill">
                                    <span class="d-flex align-items-center gap-2 flex-wrap">
                                        <strong class="text-dark">{{ $data['title'] ?? 'Notification' }}</strong>
                                        @if($isUnread)
                                            <span class="badge bg-primary">New</span>
                                        @endif
                                    </span>
                                    <span class="d-block text-muted small text-wrap">{{ $data['message'] ?? '' }}</span>
                                    <span class="d-block fs-12 text-muted mt-1">
                                        {{ optional($notification->created_at)->diffForHumans() }}
                                        @if(!empty($data['manifest_number']))
                                            <span class="ms-1">· Manifest {{ $data['manifest_number'] }}</span>
                                        @endif
                                    </span>
                                </span>
                                @if($isUnread)
                                    <span class="flex-shrink-0 align-self-center">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-success mark-read-btn"
                                            data-notification-id="{{ $notification->id }}"
                                            title="Mark as read">
                                            <i class="ti ti-check"></i>
                                        </button>
                                    </span>
                                @endif
                            </a>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="ti ti-bell-off fs-1 d-block mb-2"></i>
                                No notifications yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                @if($notifications->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const readUrlTemplate = @json(route('admin.notifications.read', ['id' => '__ID__']));
            const readAllUrl = @json(route('admin.notifications.read-all'));
            const csrfToken = @json(csrf_token());
            const listContainer = document.querySelector('.card-body.p-0');

            async function markAsRead(id) {
                const response = await fetch(readUrlTemplate.replace('__ID__', encodeURIComponent(id)), {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                });
                if (!response.ok) {
                    throw new Error('Mark as read failed.');
                }
            }

            function decrementHeaderBadge() {
                const badge = document.getElementById('notificationUnreadBadge');
                if (!badge) {
                    return;
                }
                const current = parseInt(badge.textContent, 10) || 0;
                const next = Math.max(0, current - 1);
                badge.textContent = next > 99 ? '99+' : next;
                badge.classList.toggle('d-none', next === 0);
            }

            function refreshAfterDismiss() {
                const remainingUnread = document.querySelectorAll('.notification-page-item[data-notification-read="0"]');
                if (remainingUnread.length === 0) {
                    const markAllBtn = document.getElementById('markAllReadPageBtn');
                    if (markAllBtn) {
                        markAllBtn.classList.add('d-none');
                    }
                }
                if (!document.querySelector('.notification-page-item')) {
                    const empty = document.createElement('div');
                    empty.className = 'text-center py-5 text-muted';
                    empty.innerHTML = '<i class="ti ti-circle-check fs-1 d-block mb-2 text-success"></i>You are all caught up!';
                    listContainer.appendChild(empty);
                }
            }

            function dismissWithAnimation(item) {
                item.classList.add('notification-dismiss');
                setTimeout(function () {
                    item.remove();
                    refreshAfterDismiss();
                }, 450);
            }

            // Per-item tick button: mark read, then slide the notification away.
            document.querySelectorAll('.mark-read-btn').forEach(function (btn) {
                btn.addEventListener('click', async function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (btn.disabled) {
                        return;
                    }
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                    const item = btn.closest('.notification-page-item');
                    try {
                        await markAsRead(btn.dataset.notificationId);
                        item.dataset.notificationRead = '1';
                        decrementHeaderBadge();
                        dismissWithAnimation(item);
                    } catch (error) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ti ti-check"></i>';
                    }
                });
            });

            document.querySelectorAll('.notification-page-item').forEach(function (item) {
                item.addEventListener('click', function (event) {
                    if (item.dataset.notificationRead === '1') {
                        return;
                    }
                    event.preventDefault();
                    const target = item.getAttribute('href') || '#';
                    markAsRead(item.dataset.notificationId).finally(function () {
                        window.location.href = target;
                    });
                });
            });

            const markAllBtn = document.getElementById('markAllReadPageBtn');
            if (markAllBtn) {
                markAllBtn.addEventListener('click', async function () {
                    markAllBtn.disabled = true;
                    await fetch(readAllUrl, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        credentials: 'same-origin'
                    });
                    const badge = document.getElementById('notificationUnreadBadge');
                    if (badge) {
                        badge.textContent = '0';
                        badge.classList.add('d-none');
                    }
                    // Staggered slide-out for every unread notification.
                    document.querySelectorAll('.notification-page-item[data-notification-read="0"]').forEach(function (item, index) {
                        item.dataset.notificationRead = '1';
                        setTimeout(function () {
                            dismissWithAnimation(item);
                        }, index * 120);
                    });
                    markAllBtn.classList.add('d-none');
                    markAllBtn.disabled = false;
                });
            }
        });
    </script>
</body>

</html>
