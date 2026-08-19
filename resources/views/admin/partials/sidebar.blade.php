<style>
.submenu-two ul li a.active {
    color: #007bff;
    font-weight: bold;
}

.submenu-two.active>a {
    background-color: #f0f0f0;
}
</style>
@php
    $authAdmin = auth()->guard('admin')->user();
    $adminHomeUrl = $authAdmin && $authAdmin->canAccessDashboard()
        ? route('admin.dashboard')
        : ($authAdmin && $authAdmin->canAccessDeliveryDashboard()
            ? route('admin.delivery-dashboard')
            : route('admin.my-profile'));
@endphp
<div class="sidebar" id="sidebar">

    <!-- Start Logo -->
    <div class="sidebar-logo">
        <div>
            <!-- Logo Normal -->
            <a href="{{ $adminHomeUrl }}" class="logo logo-normal">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" style = "width: 90%">
            </a>

            <!-- Logo Small -->
            <a href="{{ $adminHomeUrl }}" class="logo-small">
                <img src="{{ asset('assets/img/logo_without_text.jpg') }}" alt="Logo" style = "width: 100%">
            </a>

            <!-- Logo Dark -->
            <a href="{{ $adminHomeUrl }}" class="dark-logo">
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
                        @if($authAdmin && $authAdmin->canAccessDashboard())
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                                <i class="ti ti-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        @endif
                        @if($authAdmin && $authAdmin->canAccessDeliveryDashboard())
                        <li>
                            <a href="{{ route('admin.delivery-dashboard') }}" class="{{ request()->is('admin/delivery-dashboard') ? 'active' : '' }}">
                                <i class="ti ti-dashboard"></i>
                                <span>Delivery Dashboard</span>
                            </a>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ request()->is('admin/delivery-orders') ? 'active subdrop' : '' }}">
                                <i class="ti ti-truck-delivery"></i><span>Delivery</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ route('admin.delivery-orders', ['view' => 'pending']) }}"
                                        class="{{ request()->is('admin/delivery-orders') && request('view', 'pending') === 'pending' ? 'active' : '' }}">Pending Delivery</a></li>
                                <li><a href="{{ route('admin.delivery-orders', ['view' => 'process_pickup']) }}"
                                        class="{{ request()->is('admin/delivery-orders') && request('view') === 'process_pickup' ? 'active' : '' }}">Process Pickup</a></li>
                                <li><a href="{{ route('admin.delivery-orders', ['view' => 'completed']) }}"
                                        class="{{ request()->is('admin/delivery-orders') && request('view') === 'completed' ? 'active' : '' }}">Complete Delivery</a></li>
                                <li><a href="{{ route('admin.delivery-orders', ['view' => 'history']) }}"
                                        class="{{ request()->is('admin/delivery-orders') && request('view') === 'history' ? 'active' : '' }}">Delivery History</a></li>
                            </ul>
                        </li>
                        @endif
                        @if($authAdmin && $authAdmin->hasModuleAccess('website'))
                        <li class="submenu">
                            <a href="javascript:void(0);"
                                class="{{ request()->is('admin/change-*') ? 'active subdrop' : '' }}">
                                <i class="ti ti-dashboard"></i><span>WebSite</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ url('/admin/change-home') }}"
                                        class="{{ request()->is('admin/change-home') ? 'active' : '' }}">Home</a></li>
                                <li><a href="{{ url('/admin/change-about-us') }}"
                                        class="{{ request()->is('admin/change-about-us') ? 'active' : '' }}">About
                                        Us</a></li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);"
                                        class="{{ request()->is('admin/change-service') ? 'active' : '' }}">Services<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ url('/admin/change-service') }}"
                                                class="{{ request()->is('admin/change-service') ? 'active' : '' }}">Services
                                                Listing</a></li>
                                        <li><a href="{{ url('/admin/change-warehousing-solutions') }}"
                                        class="{{ request()->is('admin/change-warehousing-solutions') ? 'active' : '' }}">Warehousing
                                        Solutions</a></li>
                                        <li><a href="{{ url('/admin/change-e-commerce-logistics-solutions') }}"
                                        class="{{ request()->is('admin/change-e-commerce-logistics-solutions') ? 'active' : '' }}">E-Commerce
                                        Logistics Solutions</a></li>
                                        <li><a href="{{ url('/admin/change-express-air-freight-solutions') }}"
                                        class="{{ request()->is('admin/change-express-air-freight-solutions') ? 'active' : '' }}">Express
                                        Air Freight Solutions</a></li>
                                    </ul>
                                </li>
                                <li><a href="{{ url('/admin/change-network') }}"
                                        class="{{ request()->is('admin/change-network') ? 'active' : '' }}">Network</a>
                                </li>
                                <li><a href="{{ url('/admin/change-volumetric-calculator') }}"
                                        class="{{ request()->is('admin/change-volumetric-calculator') ? 'active' : '' }}">volumetric-calculator</a>
                                </li>
                                <li><a href="{{ url('/admin/change-terms-and-conditions') }}"
                                        class="{{ request()->is('admin/change-terms-and-conditions') ? 'active' : '' }}">Terms
                                        and Conditions</a></li>
                                <li><a href="{{ url('/admin/change-privacy-policy') }}"
                                        class="{{ request()->is('admin/change-privacy-policy') ? 'active' : '' }}">privacy-policy</a>
                                </li>
                                <li><a href="{{ url('/admin/change-refund-and-cancellation-policy') }}"
                                        class="{{ request()->is('admin/change-refund-and-cancellation-policy') ? 'active' : '' }}">Refund
                                        & Cancellation Policy</a></li>
                                <li><a href="{{ url('/admin/change-contact-page') }}"
                                        class="{{ request()->is('admin/change-contact-page') ? 'active' : '' }}">Contact
                                        Us</a></li>
                                <!-- <li><a href="/admin/change-warehousing-solutions"
                                        class="{{ request()->is('admin/change-warehousing-solutions') ? 'active' : '' }}">Warehousing
                                        Solutions</a></li> -->
                                <!-- <li><a href="/admin/change-e-commerce-logistics-solutions"
                                        class="{{ request()->is('admin/change-e-commerce-logistics-solutions') ? 'active' : '' }}">E-Commerce
                                        Logistics Solutions</a></li> -->
                                <li><a href="{{ url('/admin/change-blog') }}"
                                        class="{{ request()->is('admin/change-blog') ? 'active' : '' }}">Blogs</a></li>
                                <li><a href="{{ url('/admin/change-ebook') }}"
                                        class="{{ request()->is('admin/change-ebook') ? 'active' : '' }}">E-Books</a></li>
                                <li><a href="{{ url('/admin/change-track-order') }}"
                                        class="{{ request()->is('admin/change-track-order') ? 'active' : '' }}">Track Order</a></li>
                                <li><a href="{{ url('/admin/change-webinar') }}"
                                        class="{{ request()->is('admin/change-webinar*') ? 'active' : '' }}">Webinar</a></li>
                                <li><a href="{{ url('/admin/change-currency-calculator') }}"
                                        class="{{ request()->is('admin/change-currency-calculator*') ? 'active' : '' }}">Currency Calculator</a></li>
                                <li><a href="{{ url('/admin/change-world-weather') }}"
                                        class="{{ request()->is('admin/change-world-weather*') ? 'active' : '' }}">World Weather</a></li>
                                <li><a href="{{ url('/admin/change-world-time') }}"
                                        class="{{ request()->is('admin/change-world-time*') ? 'active' : '' }}">World Time</a></li>
                                <li><a href="{{ url('/admin/change-partnership') }}"
                                        class="{{ request()->is('admin/change-partnership*') ? 'active' : '' }}">Partnership</a></li>
                                <li><a href="{{ url('/admin/change-document-download') }}"
                                        class="{{ request()->is('admin/change-document-download*') ? 'active' : '' }}">Document Download</a></li>
                                <li><a href="{{ url('/admin/change-barcode-generator') }}"
                                        class="{{ request()->is('admin/change-barcode-generator*') ? 'active' : '' }}">Barcode Generator</a></li>
                                <li><a href="{{ url('/admin/change-shipping-rate-calculator') }}"
                                        class="{{ request()->is('admin/change-shipping-rate-calculator*') ? 'active' : '' }}">Shipping Rate Calculator</a></li>
                                <li><a href="{{ url('/admin/change-hsn-finder') }}"
                                        class="{{ request()->is('admin/change-hsn-finder*') ? 'active' : '' }}">HSN Finder</a></li>
                                <li><a href="{{ url('/admin/change-common-stats') }}"
                                        class="{{ request()->is('admin/change-common-stats*') ? 'active' : '' }}">Common Stats</a></li>
                                <li><a href="{{ url('/admin/change-partner-logos') }}"
                                        class="{{ request()->is('admin/change-partner-logos*') ? 'active' : '' }}">Partner Logos</a></li>
                                <li><a href="{{ url('/admin/change-subscribers') }}"
                                        class="{{ request()->is('admin/change-subscribers*') ? 'active' : '' }}">Subscribers</a></li>
                                <li><a href="{{ url('/admin/change-faq-queries') }}"
                                        class="{{ request()->is('admin/change-faq-queries*') ? 'active' : '' }}">FAQ Queries</a></li>
                                <li><a href="{{ url('/admin/faq') }}"
                                        class="{{ request()->is('admin/faq') ? 'active' : '' }}">FAQ Management</a></li>
                                <li><a href="{{ url('/admin/testimonials') }}"
                                        class="{{ request()->is('admin/testimonials') ? 'active' : '' }}">Testimonials / Reviews</a></li>
                                <!-- <li><a href="/admin/create-shipment" class="active">Add Shipment</a></li> -->
                            </ul>
                        </li>
                        @endif
                        @if($authAdmin && $authAdmin->hasModuleAccess('customer'))
                        <li class="submenu">
                            <a href="javascript:void(0);"
                                class="{{ request()->is('admin/companies') || request()->is('admin/kyc-pending*') || request()->is('admin/kyc-approved*') || request()->is('admin/kyc-rejected*') || request()->is('admin/customer-profile*') ? 'active subdrop' : '' }}">
                                <i class="ti ti-dashboard"></i><span>Customer</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);"
                                        class="{{ request()->is('admin/companies') ? 'active' : '' }}">Shipment<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ url('/admin/companies') }}"
                                                class="{{ request()->is('admin/companies') ? 'active' : '' }}">View All Shipments</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);"
                                        class="{{ request()->is('admin/kyc-pending*') || request()->is('admin/kyc-approved*') || request()->is('admin/kyc-rejected*') || request()->is('admin/customer-profile*') ? 'active' : '' }}">KYC<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="{{ url('/admin/kyc-pending') }}"
                                                class="{{ request()->is('admin/kyc-pending') ? 'active' : '' }}">Pending Customer</a></li>
                                        <li><a href="{{ url('/admin/kyc-approved') }}"
                                                class="{{ request()->is('admin/kyc-approved') ? 'active' : '' }}">Approved Customer</a></li>
                                        <li><a href="{{ route('admin.kyc-rejected') }}"
                                                class="{{ request()->is('admin/kyc-rejected') ? 'active' : '' }}">Rejected Customer</a></li>
                                        <li><a href="{{ route('admin.kyc-export', ['status' => 'all']) }}"
                                                class="{{ request()->is('admin/kyc-export*') ? 'active' : '' }}"><i class="ti ti-file-spreadsheet me-1"></i>Export KYC (Excel)</a></li>
                                    </ul>
                                </li>
                                <!-- <li class="submenu">
                                    <a href="javascript:void(0);"><i class="ti ti-brand-airtable"></i><span>Shipping</span><span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="#">Create Shipment</a></li>
                                        <li><a href="#">Modifiy Shipment</a></li>
                                        <li><a href="#">Select Shipment</a></li>
                                        <li><a href="#">Shipment Report</a></li>
                                    </ul>
                                </li> -->
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);">Manifest<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="#">Create Manifest</a></li>
                                        <li><a href="#">Edit Manifest</a></li>
                                        <li><a href="#">Dispatch Manifest</a></li>
                                        <li><a href="#">Manifest Report</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);">Account<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="#">Wallet Recharge</a></li>
                                        <li><a href="#">Account Ledger</a></li>
                                        <li><a href="#">Sale Report</a></li>
                                        <li><a href="#">Payment Report</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);">Reports<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="#">Status Report</a></li>
                                        <li><a href="#">Hold Report</a></li>
                                        <li><a href="#">Un Manifest Report</a></li>
                                    </ul>
                                </li>
                                <!-- <li><a href="/admin/create-shipment" class="active">Add Shipment</a></li> -->
                            </ul>
                        </li>
                        @endif
                        @if($authAdmin && $authAdmin->hasModuleAccess('manage_rate'))
                        <li class="submenu">
                            <a href="javascript:void(0);"
                                class="{{ request()->is('admin/manage-rate*') ? 'active subdrop' : '' }}">
                                <i class="ti ti-currency-rupee"></i><span>Manage Rate</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ url('/admin/add-country') }}"
                                        class="{{ request()->is('admin/add-country') ? 'active' : '' }}">Add Country</a></li>
                                <li><a href="{{ url('/admin/add-zone') }}"
                                        class="{{ request()->is('admin/add-zone') ? 'active' : '' }}">Add Zone</a></li>
                                <li><a href="{{ url('/admin/manage-rate') }}"
                                        class="{{ request()->is('admin/manage-rate') ? 'active' : '' }}">View & Edit Rates</a></li>
                            </ul>
                        </li>
                        @endif
                        @if($authAdmin && $authAdmin->hasModuleAccess('services'))
                        <li>
                            <a href="{{ url('/admin/services') }}" class="{{ request()->is('admin/services*') ? 'active' : '' }}">
                                <i class="ti ti-truck"></i><span>Courier Services</span>
                            </a>
                        </li>
                        @endif
                        @if($authAdmin && $authAdmin->hasModuleAccess('admin_management'))
                        <li class="submenu">
                            <a href="javascript:void(0);"
                                class="{{ request()->is('admin/delivery-persons*') || request()->is('admin/create-user*') ? 'active subdrop' : '' }}">
                                <i class="ti ti-user-cog"></i><span>Admin Management</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ url('/admin/delivery-persons') }}"
                                        class="{{ request()->is('admin/delivery-persons') ? 'active' : '' }}">Delivery Person</a></li>
                                <li><a href="{{ url('/admin/create-user') }}"
                                        class="{{ request()->is('admin/create-user') ? 'active' : '' }}">Create User</a></li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                </li>
                <li class="menu-title"><span>Others</span></li>
                <li>
                    <ul>
                        <li>
                            <a href="#"><i class="ti ti-user-up"></i><span>Get Quote</span></a>
                        </li>
                        <li>
                            <a href="{{ url('/tracking') }}" target="_blank"><i class="ti ti-building-community"></i><span>Track Shipment</span></a>
                        </li>
                        @if($authAdmin && $authAdmin->hasModuleAccess('my_profile'))
                        <li>
                            <a href="{{ route('admin.my-profile') }}" class="{{ request()->is('admin/my-profile') ? 'active' : '' }}"><i class="ti ti-medal"></i><span>My Profile</span></a>
                        </li>
                        @endif
                        <li>
                            <a href="#"><i class="ti ti-chart-arcs"></i><span>Logout</span></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

</div>