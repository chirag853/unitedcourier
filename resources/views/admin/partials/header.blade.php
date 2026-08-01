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
            <div class="me-auto d-flex align-items-center header-search d-lg-flex d-none">
                <!-- Search -->
                <div class="input-icon position-relative me-2">
                    <input type="text" class="form-control" placeholder="Search Keyword">
                    <span class="input-icon-addon d-inline-flex p-0 header-search-icon"><i
                            class="ti ti-command"></i></span>
                </div>
                <!-- /Search -->
            </div>

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
            <div class="header-item d-none d-sm-flex me-2">
                <button class="topbar-link btn topbar-link" id="light-dark-mode" type="button">
                    <i class="ti ti-moon fs-16"></i>
                </button>
            </div>

            <!-- pages -->
            <div class="header-item d-none d-sm-flex">
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="btn topbar-link topbar-teal-link" data-bs-toggle="dropdown">
                        <i class="ti ti-layout-grid-add"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-2">

                        <!-- Item-->
                        <a href="contacts.html" class="dropdown-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="d-flex mb-1 fw-semibold text-dark">Contacts</span>
                                    <span class="fs-13">View All the Contacts</span>
                                </div>
                                <i class="ti ti-chevron-right-pipe text-dark"></i>
                            </div>
                        </a>

                        <!-- Item-->
                        <a href="pipeline.html" class="dropdown-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="d-flex mb-1 fw-semibold text-dark">Pipeline</span>
                                    <span class="fs-13">View All the Pipeline</span>
                                </div>
                                <i class="ti ti-chevron-right-pipe text-dark"></i>
                            </div>
                        </a>

                        <!-- Item-->
                        <a href="activities.html" class="dropdown-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="d-flex mb-1 fw-semibold text-dark">Activities</span>
                                    <span class="fs-13">Activities</span>
                                </div>
                                <i class="ti ti-chevron-right-pipe text-dark"></i>
                            </div>
                        </a>

                        <!-- Item-->
                        <a href="analytics.html" class="dropdown-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="d-flex mb-1 fw-semibold text-dark">Analytics</span>
                                    <span class="fs-13">Analytics</span>
                                </div>
                                <i class="ti ti-chevron-right-pipe text-dark"></i>
                            </div>
                        </a>

                    </div>
                </div>
            </div>

            <!-- faq -->
            <div class="header-item d-none d-sm-flex">
                <div class="dropdown me-2">
                    <a href="faq.html" class="btn topbar-link topbar-indigo-link"><i class="ti ti-help-hexagon"></i></a>
                </div>
            </div>

            <!-- report -->
            <div class="header-item d-none d-sm-flex">
                <div class="dropdown me-2">
                    <a href="lead-reports.html" class="btn topbar-link topbar-warning-link"><i
                            class="ti ti-chart-pie"></i></a>
                </div>
            </div>

            <div class="header-line"></div>

            <!-- message -->
            <div class="header-item">
                <div class="dropdown me-2">
                    <a href="chat.html" class="btn topbar-link">
                        <i class="ti ti-message-circle-exclamation"></i>
                        <span class="badge rounded-pill">14</span>
                    </a>
                </div>
            </div>

            <!-- Notification Dropdown -->
            <div class="header-item">
                <div class="dropdown me-2">
                    <button class="topbar-link btn dropdown-toggle drop-arrow-none"
                        data-bs-toggle="dropdown" data-bs-offset="0,24" type="button" aria-haspopup="false"
                        aria-expanded="false" id="notificationDropdownButton">
                        <i class="ti ti-bell-check fs-16 animate-ring"></i>
                        <span class="badge rounded-pill d-none" id="notificationUnreadBadge">0</span>
                    </button>

                    <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg" style="min-height: 260px;">
                        <div class="p-2 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="m-0 fs-16 fw-semibold">Notifications</h6>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none d-none"
                                id="markAllNotificationsRead">Mark all as read</button>
                        </div>

                        <div class="notification-body position-relative z-2 rounded-0 overflow-auto"
                            id="notificationList" style="max-height: 360px;">
                            <div class="py-5 text-center text-muted" id="notificationLoading">
                                <span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>
                                Loading notifications...
                            </div>
                        </div>

                        <div class="p-2 rounded-bottom border-top text-center">
                            <a href="{{ $adminHomeUrl }}"
                                class="text-center text-decoration-underline fs-14 mb-0">
                                View Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

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
                    <div
                        class="form-check form-switch form-check-reverse d-flex align-items-center justify-content-between dropdown-item mb-0">
                        <label class="form-check-label" for="notify"><i class="ti ti-bell"></i>Notifications</label>
                        <input class="form-check-input me-0" type="checkbox" role="switch" id="notify">
                    </div>

                    <!-- Item-->
                    <a href="javascript:void(0);" class="dropdown-item">
                        <i class="ti ti-help-circle me-1 align-middle"></i>
                        <span class="align-middle">Help & Support</span>
                    </a>

                    <!-- Item-->
                    <a href="{{ route('admin.my-profile') }}" class="dropdown-item">
                        <i class="ti ti-settings me-1 align-middle"></i>
                        <span class="align-middle">Settings</span>
                    </a>

                    <!-- Item-->
                    <div class="pt-2 mt-2 border-top">
                        <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
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

<script>
    const BASE_URL = '{{ url('/') }}';

    document.addEventListener('DOMContentLoaded', function () {
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

        loadNotifications();
        window.setInterval(loadNotifications, 10000);
    });
</script>