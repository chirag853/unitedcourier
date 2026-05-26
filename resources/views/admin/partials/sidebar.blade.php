<style>
.submenu-two ul li a.active {
    color: #007bff;
    font-weight: bold;
}

.submenu-two.active>a {
    background-color: #f0f0f0;
}
</style>
<div class="sidebar" id="sidebar">

    <!-- Start Logo -->
    <div class="sidebar-logo">
        <div>
            <!-- Logo Normal -->
            <a href="{{ url('index-2.html') }}" class="logo logo-normal">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
            </a>

            <!-- Logo Small -->
            <a href="{{ url('index-2.html') }}" class="logo-small">
                {{ asset('assets/img/logo-small.svg') }}
                <img src="{{ asset('assets/img/logo-small.svg') }}" alt="Logo">
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
                        <li>
                            <a href="{{ url('/admin/dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                                <i class="ti ti-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
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
                                <li><a href="{{ url('/admin/faq') }}"
                                        class="{{ request()->is('admin/faq') ? 'active' : '' }}">FAQ Management</a></li>
                                <!-- <li><a href="/admin/create-shipment" class="active">Add Shipment</a></li> -->
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"
                                class="{{ request()->is('admin/companies') ? 'active subdrop' : '' }}">
                                <i class="ti ti-dashboard"></i><span>Customer</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ url('/admin/companies') }}"
                                        class="{{ request()->is('admin/companies') ? 'active' : '' }}">View Customer
                                        List</a></li>
                                <!-- <li><a href="/admin/create-shipment" class="active">Add Shipment</a></li> -->
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><i class="ti ti-brand-airtable"></i><span>Shipping</span><span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="#">Create Shipment</a></li>
                                <li><a href="#">Modifiy Shipment</a></li>
                                <li><a href="#">Select Shipment</a></li>
                                <li><a href="#">Shipment Report</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-user-star"></i><span>Manifest</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="#">Create Manifest</a></li>
                                <li><a href="#">Edit Manifest</a></li>
                                <li><a href="#">Dispatch Manifest</a></li>
                                <li><a href="#">Manifest Report</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-layout-grid"></i><span>Account</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="#">Wallet Recharge</a></li>
                                <li><a href="#">Account Ledger</a></li>
                                <li><a href="#">Sale Report</a></li>
                                <li><a href="#">Payment Report</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-report-analytics"></i><span>Reports</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="#">Status Report</a></li>
                                <li><a href="#">Hold Report</a></li>
                                <li><a href="#">Un Manifest Report</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="menu-title"><span>Others</span></li>
                <li>
                    <ul>
                        <li>
                            <a href="#"><i class="ti ti-user-up"></i><span>Get Quote</span></a>
                        </li>
                        <li>
                            <a href="#"><i class="ti ti-building-community"></i><span>Track Shipment</span></a>
                        </li>
                        <li>
                            <a href="#"><i class="ti ti-medal"></i><span>My Profile</span></a>
                        </li>
                        <li>
                            <a href="#"><i class="ti ti-chart-arcs"></i><span>Logout</span></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

</div>