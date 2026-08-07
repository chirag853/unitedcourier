<header class="navbar-header">
            @php
                $authCustomer = auth()->guard('customer')->user();
                $walletBalance = $authCustomer && $authCustomer->wallet ? $authCustomer->wallet->balance : 0;
                $customerInitial = $authCustomer ? strtoupper(substr($authCustomer->first_name, 0, 1)) : 'G';
                $customerFullName = $authCustomer ? $authCustomer->first_name . ' ' . $authCustomer->last_name : 'Guest';
            @endphp
            <div class="page-container topbar-menu">
                <div class="d-flex align-items-center gap-2">

                    <!-- Logo -->
                    <a href="{{ route('admin.dashboard') }}" class="logo">

                        <!-- Logo Normal -->
                        <span class="logo-light">
                            <span class="logo-lg"><img src="{{ asset('assets/img/logo.svg') }}" alt="logo"></span>
                            <span class="logo-sm"><img src="{{ asset('assets/img/logo-small.svg') }}"
                                    alt="small logo"></span>
                        </span>

                        <!-- Logo Dark -->
                        <span class="logo-dark">
                            <span class="logo-lg"><img src="{{ asset('assets/img/logo-white.svg') }}"
                                    alt="dark logo"></span>
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
                        <!-- <div class="input-icon position-relative me-2">
                            <input type="text" class="form-control" placeholder="Search Keyword">
                            <span class="input-icon-addon d-inline-flex p-0 header-search-icon"><i
                                    class="ti ti-command"></i></span>
                        </div> -->
                        <!-- /Search -->
                    </div>

                </div>

                <div class="d-flex align-items-center">

                    <!-- Search for Mobile -->
                    <!-- <div class="header-item d-flex d-lg-none me-2">
                        <button class="topbar-link btn" data-bs-toggle="modal" data-bs-target="#searchModal"
                            type="button">
                            <i class="ti ti-search fs-16"></i>
                        </button>
                    </div> -->


                    <!-- Minimize -->
                    <!-- <div class="header-item">
                        <div class="dropdown me-2">
                            <a href="javascript:void(0);" class="btn topbar-link btnFullscreen"><i
                                    class="ti ti-maximize"></i></a>
                        </div>
                    </div> -->
                    <!-- Minimize -->

                    <!-- Light/Dark Mode Button -->
                    <!-- <div class="header-item d-none d-sm-flex me-2">
                        <button class="topbar-link btn topbar-link" id="light-dark-mode" type="button">
                            <i class="ti ti-moon fs-16"></i>
                        </button>
                    </div> -->

                    <!-- pages -->
                    <div class="header-item d-none d-sm-flex">
                        <div class="dropdown me-2 d-none">
                            <a href="javascript:void(0);" class="btn topbar-link topbar-teal-link"
                                data-bs-toggle="dropdown">
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
                    <div class="header-item d-sm-flex">
                        <div class="dropdown me-2 d-none ">
                            <a href="faq.html" class="btn topbar-link topbar-indigo-link"><i
                                    class="ti ti-help-hexagon"></i></a>
                        </div>
                    </div>

                    <!-- report -->
                    <div class="header-item d-sm-flex">
                        <div class="dropdown me-2 d-none ">
                            <a href="lead-reports.html" class="btn topbar-link topbar-warning-link"><i
                                    class="ti ti-chart-pie"></i></a>
                        </div>
                    </div>

                    <!-- Wallet Button -->

                    <div class="ms-3 mb-2 header-wallet-section">
                        <p class="form-label fw-bold mb-1" style="color: #3e2d5e;">Wallet Recharge</p>
                        <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#walletRechargeModal" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%);
                        border-color: #7b1fa2;
                        color: white;
                        box-shadow: 0 2px 6px rgba(156, 39, 176, 0.3); font-weight: 700 !important;">
                        <i class="bi bi-currency-rupee"></i>&nbsp;Recharge
                        </button>
                    </div>

                    <div class="ms-3 mb-2 header-credit-balance-section">
                        <p b-hoqn66jiy3="" class="form-label fw-bold mb-1" style="color: #3e2d5e;">Credit Balance</p>
                        <button b-hoqn66jiy3="" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#modalCenter" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%);
                        border-color: #7b1fa2;
                        color: white;
                        box-shadow: 0 2px 6px rgba(156, 39, 176, 0.3);
                        font-weight: 700 !important;">
                        <i class="bi bi-currency-rupee"></i>&nbsp;{{ number_format($walletBalance, 2) }}
                        </button>
                    </div>

                    

                    <div class="header-line ms-3"></div>

                    <!-- message -->
                    <div class="header-item d-none">
                        <div class="dropdown me-2">
                            <a href="chat.html" class="btn topbar-link">
                                <i class="ti ti-message-circle-exclamation"></i>
                                <span class="badge rounded-pill">14</span>
                            </a>
                        </div>
                    </div>

                    <!-- Notification Dropdown -->
                    <div class="header-item header-notification-section">
                        <div class="dropdown me-2">

                            <button class="topbar-link btn topbar-link dropdown-toggle drop-arrow-none"
                                data-bs-toggle="dropdown" data-bs-offset="0,24" type="button" aria-haspopup="false"
                                aria-expanded="false">
                                <i class="ti ti-bell-check fs-16 animate-ring"></i>
                                <span class="badge rounded-pill">10</span>
                            </button>

                            <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg"
                                style="min-height: 300px;">

                                <div class="p-2 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-0 fs-16 fw-semibold"> Notifications</h6>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notification Body -->
                                <div class="notification-body position-relative z-2 rounded-0" data-simplebar>

                                    <!-- Item-->
                                    <div class="dropdown-item notification-item py-3 text-wrap border-bottom"
                                        id="notification-1">
                                        <div class="d-flex">
                                            <div class="me-2 position-relative flex-shrink-0">
                                                <img src="assets/img/users/user-01.jpg" class="avatar-md rounded-circle"
                                                    alt="Img">
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-medium text-dark">John Doe</p>
                                                <p class="mb-1 text-wrap">
                                                    left 6 comments on <span class="fw-medium text-dark">Isla Nublar
                                                        SOC2 compliance report</span>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fs-12"><i class="ti ti-clock me-1"></i>4 min ago</span>
                                                    <div
                                                        class="notification-action d-flex align-items-center float-end gap-2">
                                                        <a href="javascript:void(0);"
                                                            class="notification-read rounded-circle bg-danger"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Make as Read"
                                                            aria-label="Make as Read"></a>
                                                        <button class="btn rounded-circle p-0"
                                                            data-dismissible="#notification-1">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item-->
                                    <div class="dropdown-item notification-item py-3 text-wrap border-bottom"
                                        id="notification-2">
                                        <div class="d-flex">
                                            <div class="me-2 position-relative flex-shrink-0">
                                                <img src="assets/img/users/user-12.jpg" class="avatar-md rounded-circle"
                                                    alt="Img">
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-medium text-dark">Thomas William</p>
                                                <p class="mb-1 text-wrap">
                                                    “Oh, I finished de-bugging the phones, but the system's compiling
                                                    for eighteen minutes, or twenty...”
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fs-12"><i class="ti ti-clock me-1"></i>8 min ago</span>
                                                    <div
                                                        class="notification-action d-flex align-items-center float-end gap-2">
                                                        <a href="javascript:void(0);"
                                                            class="notification-read rounded-circle bg-danger"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Make as Read"
                                                            aria-label="Make as Read"></a>
                                                        <button class="btn rounded-circle p-0"
                                                            data-dismissible="#notification-2">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item-->
                                    <div class="dropdown-item notification-item py-3 text-wrap border-bottom"
                                        id="notification-3">
                                        <div class="d-flex">
                                            <div class="me-2 position-relative flex-shrink-0">
                                                <img src="assets/img/profiles/avatar-12.jpg"
                                                    class="avatar-md rounded-circle" alt="Img">
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-medium text-dark">Sarah Anderson</p>
                                                <p class="mb-1 text-wrap">
                                                    attached a file to <span class="fw-medium text-dark">Isla Nublar
                                                        SOC2 compliance report</span>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fs-12"><i class="ti ti-clock me-1"></i>15 min
                                                        ago</span>
                                                    <div
                                                        class="notification-action d-flex align-items-center float-end gap-2">
                                                        <a href="javascript:void(0);"
                                                            class="notification-read rounded-circle bg-danger"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Make as Read"
                                                            aria-label="Make as Read"></a>
                                                        <button class="btn rounded-circle p-0"
                                                            data-dismissible="#notification-3">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item-->
                                    <div class="dropdown-item notification-item py-3 text-wrap" id="notification-4">
                                        <div class="d-flex">
                                            <div class="me-2 position-relative flex-shrink-0">
                                                <img src="assets/img/profiles/avatar-08.jpg"
                                                    class="avatar-md rounded-circle" alt="Img">
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-medium text-dark">Ann McClure</p>
                                                <p class="mb-1 text-wrap">
                                                    mentioned you in <span class="fw-medium text-dark">Bug Fix Review -
                                                        Task #432</span>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fs-12"><i class="ti ti-clock me-1"></i>20 min
                                                        ago</span>
                                                    <div
                                                        class="notification-action d-flex align-items-center float-end gap-2">
                                                        <a href="javascript:void(0);"
                                                            class="notification-read rounded-circle bg-danger"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Make as Read"
                                                            aria-label="Make as Read"></a>
                                                        <button class="btn rounded-circle p-0"
                                                            data-dismissible="#notification-4">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- View All-->
                                <div class="p-2 rounded-bottom border-top text-center">
                                    <a href="notifications.html"
                                        class="text-center text-decoration-underline fs-14 mb-0">
                                        View All Notifications
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown profile-dropdown d-flex align-items-center justify-content-center">
                        <a href="javascript:void(0);"
                            class="topbar-link dropdown-toggle drop-arrow-none position-relative"
                            data-bs-toggle="dropdown" data-bs-offset="0,22" aria-haspopup="false" aria-expanded="false">
                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold fs-18" style="width:38px;height:38px;" alt="user-initial">{{ $customerInitial }}</div>
                            <span class="online text-success"><i
                                    class="ti ti-circle-filled d-flex bg-white rounded-circle border border-1 border-white"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-2">

                            <div class="d-flex align-items-center bg-light rounded-3 p-2 mb-2">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold fs-16" style="width:42px;height:42px;">{{ $customerInitial }}</div>
                                <div class="ms-2">
                                    <p class="fw-medium text-dark mb-0">{{ $customerFullName }}</p>
                                    <span class="d-block fs-13">Customer</span>
                                </div>
                            </div>

                            <!-- Item-->
                            <a href="profile-settings.html" class="dropdown-item">
                                <i class="ti ti-user-circle me-1 align-middle"></i>
                                <span class="align-middle">Profile Settings</span>
                            </a>

                            <!-- item -->
                            <div
                                class="form-check form-switch form-check-reverse d-flex align-items-center justify-content-between dropdown-item mb-0">
                                <label class="form-check-label" for="notify"><i
                                        class="ti ti-bell"></i>Notifications</label>
                                <input class="form-check-input me-0" type="checkbox" role="switch" id="notify">
                            </div>

                            <!-- Item-->
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class="ti ti-help-circle me-1 align-middle"></i>
                                <span class="align-middle">Help & Support</span>
                            </a>

                            <!-- Item-->
                            <a href="profile-settings.html" class="dropdown-item">
                                <i class="ti ti-settings me-1 align-middle"></i>
                                <span class="align-middle">Settings</span>
                            </a>

                            <!-- Item-->
                            <div class="pt-2 mt-2 border-top">
                                <form action="{{ route('customer.logout') }}" method="POST" class="mb-0">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="dropdown-item text-danger text-start w-100 border-0 bg-transparent">
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

        <!-- Wallet Recharge Modal -->
        <div class="modal fade" id="walletRechargeModal" tabindex="-1" aria-labelledby="walletRechargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="walletRechargeModalLabel">
                            <i class="ti ti-wallet me-2 text-primary"></i>Recharge Wallet
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <p class="text-muted mb-3 fs-13">Select an amount or enter a custom one:</p>
                        <div class="d-flex flex-wrap gap-2 mb-3" id="prepaidAmounts">
                            <button type="button" class="btn btn-outline-primary prepaid-btn" data-amount="500">₹500</button>
                            <button type="button" class="btn btn-outline-primary prepaid-btn" data-amount="1000">₹1,000</button>
                            <button type="button" class="btn btn-outline-primary prepaid-btn" data-amount="2000">₹2,000</button>
                            <button type="button" class="btn btn-outline-primary prepaid-btn" data-amount="5000">₹5,000</button>
                        </div>
                        <div class="mb-3">
                            <label for="rechargeAmount" class="form-label fw-medium">Enter Amount (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-currency-rupee"></i></span>
                                <input type="number" class="form-control" id="rechargeAmount" placeholder="Enter amount" min="1" step="1">
                            </div>
                            <div class="invalid-feedback" id="rechargeAmountError"></div>
                        </div>
                        <div class="alert alert-light border mb-0 py-2 px-3 d-flex align-items-center" style="border-radius: 10px;">
                            <i class="ti ti-wallet fs-18 me-2 text-success"></i>
                            <div>
                                <span class="fs-12 text-muted">Current Balance</span>
                                <strong class="d-block fs-14 text-dark" id="modalCurrentBalance">₹{{ number_format($walletBalance, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary px-4" id="confirmRechargeBtn" style="border-radius: 8px;">
                            <i class="ti ti-credit-card me-1"></i>Recharge Now
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        (function() {
            var prepaidBtns = document.querySelectorAll('.prepaid-btn');
            var amountInput = document.getElementById('rechargeAmount');
            var confirmBtn = document.getElementById('confirmRechargeBtn');
            var modalCurrentBalance = document.getElementById('modalCurrentBalance');
            var rechargeAmountError = document.getElementById('rechargeAmountError');

            if (!amountInput || !confirmBtn) return;

            prepaidBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    amountInput.value = this.getAttribute('data-amount');
                    prepaidBtns.forEach(function(b) { b.classList.remove('active', 'bg-primary', 'text-white'); });
                    this.classList.add('active', 'bg-primary', 'text-white');
                    if (rechargeAmountError) { rechargeAmountError.textContent = ''; amountInput.classList.remove('is-invalid'); }
                });
            });

            amountInput.addEventListener('input', function() {
                prepaidBtns.forEach(function(b) { b.classList.remove('active', 'bg-primary', 'text-white'); });
                if (rechargeAmountError) { rechargeAmountError.textContent = ''; amountInput.classList.remove('is-invalid'); }
            });

            confirmBtn.addEventListener('click', function() {
                var amount = parseFloat(amountInput.value);
                if (!amount || amount < 1) {
                    if (rechargeAmountError) { rechargeAmountError.textContent = 'Please enter a valid amount (minimum ₹1).'; amountInput.classList.add('is-invalid'); }
                    return;
                }

                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';

                // Use a server-rendered token instead of relying on every page
                // that includes this header to provide a CSRF meta element.
                var token = @json(csrf_token());

                fetch('{{ route("customer.wallet-recharge") }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ amount: amount })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        var formatted = '₹' + parseFloat(data.new_balance).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                        var spans = document.querySelectorAll('#walletBalanceBtn span');
                        spans.forEach(function(el) { el.textContent = formatted; });
                        if (modalCurrentBalance) modalCurrentBalance.textContent = formatted;

                        var alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                        alertDiv.style.cssText = 'min-width:300px;max-width:500px;z-index:9999;';
                        alertDiv.innerHTML = 'Wallet recharged! New balance: ' + formatted;
                        document.body.appendChild(alertDiv);
                        setTimeout(function() { alertDiv.remove(); }, 5000);

                        var modalEl = document.getElementById('walletRechargeModal');
                        if (modalEl) {
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                        amountInput.value = '';
                        prepaidBtns.forEach(function(b) { b.classList.remove('active', 'bg-primary', 'text-white'); });
                    } else {
                        if (rechargeAmountError) { rechargeAmountError.textContent = data.message || 'Recharge failed.'; amountInput.classList.add('is-invalid'); }
                    }
                })
                .catch(function() {
                    if (rechargeAmountError) { rechargeAmountError.textContent = 'Network error. Please try again.'; amountInput.classList.add('is-invalid'); }
                })
                .finally(function() {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<i class="ti ti-credit-card me-1"></i>Recharge Now';
                });
            });
        })();
        </script>