@php
    $sidebarCustomer = auth()->guard('customer')->user();
    $sidebarBizCat = $sidebarCustomer?->businessCategory;
    $sidebarUserType = $sidebarBizCat?->user_type ?: 'Personal';
    $sidebarSavedCustomerCategories = ['courier-or-aggregator', 'courier or aggregator'];
    $sidebarCanManageSavedCustomers = $sidebarBizCat && (
        in_array(strtolower(trim((string) $sidebarBizCat->category_slug)), $sidebarSavedCustomerCategories, true)
        || in_array(strtolower(trim((string) $sidebarBizCat->category_name)), $sidebarSavedCustomerCategories, true)
    );
@endphp
<div class="sidebar" id="sidebar">

            <!-- Start Logo -->
            <div class="sidebar-logo">
                <div>
            <!-- Logo Normal -->
            <a href="{{ url('index-2.html') }}" class="logo logo-normal">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" style = "width: 90%">
            </a>

            <!-- Logo Small -->
            <a href="{{ url('index-2.html') }}" class="logo-small">
                <img src="{{ asset('assets/img/logo_without_text.jpg') }}" alt="Logo" style = "width: 100%">
            </a>

            <!-- Logo Dark -->
            <a href="{{ url('index-2.html') }}" class="dark-logo">
                <img src="{{ asset('assets/img/logo-white.svg') }}" alt="Logo">
            </a>
        </div>
                <button class="sidenav-toggle-btn btn border-0 p-0 active" id="toggle_btn">
                    <i class="ti ti-arrow-bar-to-left"></i>
                </button>

                <!-- Sidebar Menu Close -->
                <button class="sidebar-close">
                    <i class="ti ti-x align-middle"></i>
                </button>
            </div>
            <!-- End Logo -->

            <!-- Sidenav Menu -->
            <div class="sidebar-inner" data-simplebar>
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title"><span>Main Menu</span></li>
                        <li>
                            <ul>
                                <!-- <li class="submenu">
                                    <a href="javascript:void(0);" class="active subdrop">
                                        <i class="ti ti-dashboard"></i><span>Dashboard</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="/customer/dashboard" class="active">Admin Dashboard</a></li>
                                    </ul>
                                </li> -->


                                <li>
                                    <a href="{{ url('/customer/dashboard') }}" class="{{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                                        <i class="ti ti-dashboard"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>


                                <li class="submenu">
                                    <a href="javascript:void(0);" class="{{ request()->routeIs('customer.exporter-customers*', 'customer.create-shipment*', 'customer.bulk-upload*', 'customer.view-all-shipments') ? 'active subdrop' : '' }}">
                                        <i class="ti ti-dashboard"></i><span>Orders</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <!-- <li><a href="/customer/companies" class="active">View Customer List</a></li> -->
                                        @if($sidebarCanManageSavedCustomers)
                                            <li><a href="{{ route('customer.exporter-customers') }}" class="{{ request()->routeIs('customer.exporter-customers*') ? 'active' : '' }}">Add Customer</a></li>
                                        @endif
                                        <li><a href="{{ url('/customer/create-shipment') }}" class="{{ request()->routeIs('customer.create-shipment*') ? 'active' : '' }}">Create Order</a></li>
                                        <li><a href="{{ url('/customer/bulk-upload') }}" class="{{ request()->routeIs('customer.bulk-upload*') ? 'active' : '' }}">Bulk Upload</a></li>
                                        <li><a href="{{ url('/customer/view-all-shipments') }}" class="{{ request()->routeIs('customer.view-all-shipments') ? 'active' : '' }}">View All Order</a></li>
                                    </ul>
                                </li>
                                <!-- <li class="submenu">
                                    <a href="javascript:void(0);"><i
                                            class="ti ti-brand-airtable"></i><span>Shipping</span><span
                                            class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="{{ url('#') }}">Create Shipment</a></li>
                                        <li><a href="{{ url('#') }}">Modifiy Shipment</a></li>
                                        <li><a href="{{ url('#') }}">Select Shipment</a></li>
                                        <li><a href="{{ url('#') }}">Shipment Report</a></li>
                                    </ul>
                                </li> -->
                                <!-- <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="ti ti-user-star"></i><span>Manifest</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="{{ url('#') }}">Create Manifest</a></li>
                                        <li><a href="{{ url('#') }}">Edit Manifest</a></li>
                                        <li><a href="{{ url('#') }}">Dispatch Manifest</a></li>
                                        <li><a href="{{ url('#') }}">Manifests Report</a></li>
                                    </ul>
                                </li> -->
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="{{ request()->routeIs('customer.transaction-history', 'customer.wallet-history') ? 'active subdrop' : '' }}">
                                        <i class="ti ti-layout-grid"></i><span>Account</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <!-- <li><a href="{{ url('#') }}">Wallet Recharge</a></li> -->
                                        <!-- <li><a href="{{ url('#') }}">Account Ledger</a></li> -->
                                        <!-- <li><a href="{{ url('#') }}">Sale Report</a></li> -->
                                        <li><a href="{{ route('customer.transaction-history') }}" class="{{ request()->routeIs('customer.transaction-history') ? 'active' : '' }}">Transaction History</a></li>
                                        <li><a href="{{ route('customer.wallet-history') }}" class="{{ request()->routeIs('customer.wallet-history') ? 'active' : '' }}">Wallet History</a></li>
                                    </ul>
                                </li>

                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="ti ti-report-analytics"></i><span>Reports</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="{{ url('#') }}">Status Report</a></li>
                                        <li><a href="{{ url('#') }}">Hold Report</a></li>
                                        <li><a href="{{ url('#') }}">Un Manifest Report</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-title"><span>Others</span></li>
                        <li>
                            <ul>
                                <li>
                                    <a href="{{ url('shipping-rate-calculator') }}" target="_blank"><i class="ti ti-user-up"></i><span>Get Quote</span></a>
                                </li>
                                <li>
                                    <a href="{{ url('tracking') }}" target="_blank"><i class="ti ti-building-community"></i><span>Track Shipment</span></a>
                                </li>
                                <li>
                                    <a href="{{ route('customer.kyc.summary') }}" class="{{ request()->routeIs('customer.kyc.*') ? 'active' : '' }}"><i class="ti ti-shield-check"></i><span>{{ $sidebarUserType === 'Business' ? 'Business KYC' : 'Personal KYC' }}</span></a>
                                </li>
                                <li>
                                    <a href="{{ route('customer.my-profile') }}" class="{{ request()->routeIs('customer.my-profile') ? 'active' : '' }}"><i class="ti ti-medal"></i><span>My Profile</span></a>
                                </li>
                                <!-- <li>
                                    <form action="{{ route('customer.logout') }}" method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" class="sidebar-logout border-0 bg-transparent">
                                            <i class="ti ti-logout"></i><span>Logout</span>
                                        </button>
                                    </form>
                                </li> -->
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

        </div>