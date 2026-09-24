<header class="navbar-header">

@php
    $authAdmin = auth()->guard('admin')->user();
    $adminHomeUrl = $authAdmin && $authAdmin->canAccessDashboard()
        ? route('admin.dashboard')
        : ($authAdmin && $authAdmin->canAccessDeliveryDashboard()
            ? route('admin.delivery-dashboard')
            : route('admin.my-profile'));
@endphp

    <!-- change after ftp -->
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/plugins/tabler-icons/tabler-icons.min.css">


    <div class="page-container topbar-menu">
        <div class="d-flex align-items-center gap-2">

            <!-- Logo -->
            <a href="{{ $adminHomeUrl }}" class="logo">

                <!-- Logo Normal -->
                <span class="logo-light">
                    <span class="logo-lg"><img src="{{ asset('assets/img/logo.svg') }}" alt="logo"></span>
                    <span class="logo-sm"><img src="{{ asset('assets/img/logo_without_text.jpg') }}" alt="small logo"></span>
                </span>

                <!-- Logo Dark -->
                <span class="logo-dark">
                    <span class="logo-lg"><img src="{{ asset('assets/img/logo-white.svg') }}" alt="dark logo"></span>
                </span>
            </a>

            <!-- Sidebar Mobile Button -->
            <a id="mobile_btn" class="mobile-btn" href="#sidebar">
                <i class="ti ti-menu-deep fs-24"></i>
            </a>

            <button class="sidenav-toggle-btn btn border-0 p-0" id="toggle_btn2">
                <i class="ti ti-arrow-bar-to-right"></i>
            </button>

            <!-- Search -->


        </div>

        <div class="d-flex align-items-center">

            <!-- Search for Mobile -->
            <div class="header-item d-flex d-lg-none me-2">
                <button class="topbar-link btn" data-bs-toggle="modal" data-bs-target="#searchModal" type="button">
                    <i class="ti ti-search fs-16"></i>
                </button>
            </div>


            <!-- Minimize -->
            <div class="header-item">
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="btn topbar-link btnFullscreen"><i
                            class="ti ti-maximize"></i></a>
                </div>
            </div>
            <!-- Minimize -->

            <!-- Light/Dark Mode Button -->
            <!-- <div class="header-item d-none d-sm-flex me-2">
                <button class="topbar-link btn topbar-link" id="light-dark-mode" type="button">
                    <i class="ti ti-moon fs-16"></i>
                </button>
            </div> -->

            <!-- pages -->

            <!-- faq -->
            <!-- <div class="header-item d-none d-sm-flex">
                <div class="dropdown me-2">
                    <a href="faq.html" class="btn topbar-link topbar-indigo-link"><i class="ti ti-help-hexagon"></i></a>
                </div>
            </div> -->

            <!-- report -->
            <!-- <div class="header-item d-none d-sm-flex">
                <div class="dropdown me-2">
                    <a href="lead-reports.html" class="btn topbar-link topbar-warning-link"><i
                            class="ti ti-chart-pie"></i></a>
                </div>
            </div> -->

            <div class="header-line"></div>

            <!-- message -->
            

            <!-- Notification Dropdown -->
            <div class="header-item me-2">
                <div class="dropdown">
                    <a href="javascript:void(0);" class="btn topbar-link position-relative"
                        data-bs-toggle="dropdown" data-bs-offset="0,22" aria-haspopup="true" aria-expanded="false"
                        title="Notifications">
                        <i class="ti ti-bell fs-16"></i>
                        <span id="notificationUnreadBadge"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-0" style="width: 360px;">
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <span class="fw-semibold text-dark">Notifications</span>
                            <button type="button" id="markAllNotificationsRead"
                                class="btn btn-link btn-sm text-decoration-none p-0 d-none">Mark all read</button>
                        </div>
                        <div id="notificationList" style="max-height: 380px; overflow-y: auto;"></div>
                        <div class="d-flex align-items-center justify-content-between border-top px-3 py-2">
                            <a href="{{ route('admin.notifications') }}"
                                class="text-decoration-none small">View all notifications</a>
                            <button type="button" id="clearNotificationsBtn"
                                class="btn btn-link btn-sm text-danger text-decoration-none p-0">Clear</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Notification Dropdown -->

            <!-- User Dropdown -->
            <div class="dropdown profile-dropdown d-flex align-items-center justify-content-center">
                <a href="javascript:void(0);" class="topbar-link dropdown-toggle drop-arrow-none position-relative"
                    data-bs-toggle="dropdown" data-bs-offset="0,22" aria-haspopup="false" aria-expanded="false">
                    <img src="{{ asset('assets/img/profiles/avatar-19.jpg') }}" width="38" class="rounded-1 d-flex"
                        alt="user-image">
                    <span class="online text-success"><i
                            class="ti ti-circle-filled d-flex bg-white rounded-circle border border-1 border-white"></i></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-2">

                    <div class="d-flex align-items-center bg-light rounded-3 p-2 mb-2">
                        <img src="{{ asset('assets/img/profiles/avatar-19.jpg') }}" class="rounded-circle" width="42"
                            height="42" alt="Img">
                        <div class="ms-2">
                            <p class="fw-medium text-dark mb-0">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</p>
                            <span class="d-block fs-13">{{ Auth::guard('admin')->user()->designation ?? 'Admin' }}</span>
                        </div>
                    </div>

                    <!-- Item-->
                    <a href="{{ route('admin.my-profile') }}" class="dropdown-item">
                        <i class="ti ti-user-circle me-1 align-middle"></i>
                        <span class="align-middle">Profile Settings</span>
                    </a>

                    <!-- item -->
                   

                    <!-- Item-->
                   

                    <!-- Item-->
                    

                    <!-- Item-->
                    <div class="pt-2 mt-2 border-top">
                        <form id="adminLogoutForm" action="{{ route('admin.logout') }}" method="POST" style="display: inline;"
                            data-csrf-url="{{ route('admin.csrf-token') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger w-100 text-start">
                                <i class="ti ti-logout me-1 fs-17 align-middle"></i>
                                <span class="align-middle">Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;">
    <div id="deliveryNotificationToast" class="toast border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-primary text-white">
            <i class="ti ti-truck-delivery me-2"></i>
            <strong class="me-auto" id="deliveryToastTitle">New Delivery Assigned</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="deliveryToastMessage"></div>
    </div>
</div>

<!-- Clear Notifications Confirm Popup -->
<div class="modal fade" id="clearNotificationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger mb-3"
                    style="width: 56px; height: 56px;">
                    <i class="ti ti-trash fs-24"></i>
                </span>
                <h6 class="mb-1">Clear all notifications?</h6>
                <p class="text-muted small mb-4">All notifications will be permanently removed. This action cannot be undone.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light flex-fill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmClearNotificationsBtn" class="btn btn-danger flex-fill">Yes, Clear</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #notificationList .notification-item {
        overflow: hidden;
        max-height: 220px;
        transition: opacity .3s ease, transform .3s ease, max-height .4s ease,
            padding-top .4s ease, padding-bottom .4s ease;
    }
    #notificationList .notification-item.notification-clearing {
        opacity: 0;
        transform: translateX(50px);
        max-height: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        border-bottom: 0 !important;
    }
</style>

<script>
    const BASE_URL = '{{ url('/') }}';

    document.addEventListener('DOMContentLoaded', function () {
        const logoutForm = document.getElementById('adminLogoutForm');

        if (logoutForm) {
            logoutForm.addEventListener('submit', async function (event) {
                if (logoutForm.dataset.submitting === 'true') {
                    return;
                }

                event.preventDefault();

                try {
                    const response = await fetch(logoutForm.dataset.csrfUrl, {
                        method: 'GET',
                        credentials: 'same-origin',
                        cache: 'no-store',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Unable to refresh the CSRF token.');
                    }

                    const data = await response.json();
                    logoutForm.querySelector('input[name="_token"]').value = data.token;
                    logoutForm.dataset.submitting = 'true';
                    logoutForm.requestSubmit();
                } catch (error) {
                    window.location.reload();
                }
            });
        }

        const dataUrl = @json(route('admin.notifications.data'));
        const readUrlTemplate = @json(route('admin.notifications.read', ['id' => '__ID__']));
        const readAllUrl = @json(route('admin.notifications.read-all'));
        const csrfToken = @json(csrf_token());
        const badge = document.getElementById('notificationUnreadBadge');
        const list = document.getElementById('notificationList');
        const markAllButton = document.getElementById('markAllNotificationsRead');
        let knownNotificationIds = new Set();
        let notificationsInitialized = false;

        function renderNotifications(notifications, unreadCount) {
            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            badge.classList.toggle('d-none', unreadCount === 0);
            markAllButton.classList.toggle('d-none', unreadCount === 0);
            const clearFooterBtn = document.getElementById('clearNotificationsBtn');
            if (clearFooterBtn) {
                clearFooterBtn.classList.toggle('d-none', notifications.length === 0);
            }
            list.replaceChildren();

            if (!notifications.length) {
                const empty = document.createElement('div');
                empty.className = 'py-5 text-center text-muted';
                empty.textContent = 'No notifications yet.';
                list.appendChild(empty);
                return;
            }

            notifications.forEach(function (notification) {
                const item = document.createElement('a');
                item.href = notification.url || '#';
                item.className = 'dropdown-item notification-item py-3 text-wrap border-bottom d-block';
                if (!notification.read) {
                    item.classList.add('bg-light');
                }

                const title = document.createElement('p');
                title.className = 'mb-1 fw-semibold text-dark';
                title.textContent = notification.title;

                const message = document.createElement('p');
                message.className = 'mb-1 text-wrap';
                message.textContent = notification.message;

                const time = document.createElement('span');
                time.className = 'fs-12 text-muted';
                time.textContent = notification.created_at;

                item.append(title, message, time);
                item.addEventListener('click', function (event) {
                    if (notification.read) {
                        return;
                    }

                    event.preventDefault();
                    markAsRead(notification.id).finally(function () {
                        window.location.href = item.href;
                    });
                });
                list.appendChild(item);
            });
        }

        function showNewDelivery(notification) {
            document.getElementById('deliveryToastTitle').textContent = notification.title;
            document.getElementById('deliveryToastMessage').textContent = notification.message;

            if (window.bootstrap && bootstrap.Toast) {
                bootstrap.Toast.getOrCreateInstance(document.getElementById('deliveryNotificationToast'), {
                    delay: 8000
                }).show();
            }

            if ('Notification' in window && Notification.permission === 'granted') {
                const browserNotification = new Notification(notification.title, {
                    body: notification.message,
                    icon: @json(asset('favicon.ico'))
                });
                browserNotification.onclick = function () {
                    window.focus();
                    window.location.href = notification.url;
                };
            }
        }

        async function loadNotifications() {
            try {
                const response = await fetch(dataUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                const incomingIds = new Set(payload.notifications.map(function (notification) {
                    return notification.id;
                }));

                if (notificationsInitialized) {
                    const newest = payload.notifications.find(function (notification) {
                        return !notification.read && !knownNotificationIds.has(notification.id);
                    });
                    if (newest) {
                        showNewDelivery(newest);
                    }
                }

                knownNotificationIds = incomingIds;
                notificationsInitialized = true;
                renderNotifications(payload.notifications, payload.unread_count);
            } catch (error) {
                // Keep the existing UI during temporary network failures.
            }
        }

        async function markAsRead(id) {
            await fetch(readUrlTemplate.replace('__ID__', encodeURIComponent(id)), {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin'
            });
        }

        markAllButton.addEventListener('click', async function () {
            await fetch(readAllUrl, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin'
            });
            await loadNotifications();
        });

        const clearBtn = document.getElementById('clearNotificationsBtn');
        const clearUrl = @json(route('admin.notifications.clear'));
        const clearModalEl = document.getElementById('clearNotificationsModal');
        const confirmClearBtn = document.getElementById('confirmClearNotificationsBtn');
        if (clearBtn && clearModalEl && confirmClearBtn) {
            clearBtn.addEventListener('click', function () {
                const items = list.querySelectorAll('.notification-item');
                if (!items.length || clearBtn.disabled) {
                    return;
                }
                if (window.bootstrap && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(clearModalEl).show();
                }
            });
            confirmClearBtn.addEventListener('click', async function () {
                if (window.bootstrap && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(clearModalEl).hide();
                }
                const items = list.querySelectorAll('.notification-item');
                if (!items.length) {
                    return;
                }
                clearBtn.disabled = true;
                confirmClearBtn.disabled = true;
                // Staggered slide-out animation for every notification.
                items.forEach(function (item, index) {
                    setTimeout(function () {
                        item.classList.add('notification-clearing');
                    }, index * 100);
                });
                const totalDelay = items.length * 100 + 450;
                try {
                    await fetch(clearUrl, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        credentials: 'same-origin'
                    });
                } catch (error) {
                    // List still animates out; next poll will re-sync on failure.
                }
                setTimeout(function () {
                    knownNotificationIds = new Set();
                    renderNotifications([], 0);
                    clearBtn.disabled = false;
                    confirmClearBtn.disabled = false;
                }, totalDelay);
            });
        }

        loadNotifications();
        window.setInterval(loadNotifications, 10000);
    });
</script>

@include('partials.global-alert')