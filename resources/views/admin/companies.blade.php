<!DOCTYPE html>
<html lang="en">

<head>

	<!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - View Customer List</title>
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

    <!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Choices CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/choices.js/public/assets/styles/choices.min.css') }}">

    <!-- Select2 CSS -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Quill CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/quill/quill.snow.css') }}">

    <!-- Mobile CSS-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/intltelinput/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/intltelinput/css/demo.css') }}">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .status-active {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-assigned {
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-dispatched {
            background-color: #cffafe;
            color: #0e7490;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-ready-to-dispatch {
            background-color: #fef3c7;
            color: #92400e;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .btn-ready-to-dispatch {
            background-color: #f59e0b;
            color: #fff;
            border: none;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .btn-ready-to-dispatch:hover {
            background-color: #d97706;
        }
        .btn-dispute {
            background-color: #e5484d;
            color: #fff;
            border: none;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .btn-dispute:hover {
            background-color: #c81e2b;
            color: #fff;
        }
        /* ===== Draft-style columns (same as view-all-shipments?status=draft) ===== */
        .hawb-sub-info {
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px dashed #dee2e6;
            display: flex;
            flex-direction: column;
            gap: 3px;
            font-size: 11px;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .hawb-sub-row {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            line-height: 1.35;
        }
        .hawb-sub-label {
            color: #6b7280;
            flex-shrink: 0;
            font-weight: 600;
            min-width: 32px;
        }
        .hawb-sub-value {
            color: #1f2937;
            min-width: 0;
        }
        .receiver-details-stack {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 12px;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .receiver-details-stack .receiver-name {
            font-weight: 600;
            color: #1f2937;
            line-height: 1.3;
        }
        .receiver-details-stack .receiver-line {
            color: #1f2937;
            line-height: 1.35;
        }
        .package-details-card {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 12px;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .package-details-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            line-height: 1.35;
        }
        .package-details-label {
            color: #6b7280;
            flex-shrink: 0;
        }
        .package-details-value {
            font-weight: 600;
            color: #1f2937;
            text-align: right;
            min-width: 0;
        }
        .customer-name-link {
            color: #0d6efd;
            cursor: pointer;
            text-decoration: none;
        }
        .customer-name-link:hover {
            text-decoration: underline;
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
        /* ===== Table scroll & alignment fixes ===== */
        .table-scroll-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            padding:15px;
        }
        .table-scroll-wrap .dataTables_wrapper {
            padding: 0;
        }
        .table-scroll-wrap .dataTables_scroll {
            width: 100% !important;
        }
        .table-scroll-wrap .dataTables_scrollHeadInner,
        .table-scroll-wrap .dataTables_scrollHeadInner table.dataTable {
            width: 100% !important;
            margin: 0 !important;
        }
        .table-scroll-wrap .dataTables_scrollBody {
            overflow: auto !important;
        }
        .table-scroll-wrap table.dataTable {
            width: 100% !important;
            margin: 0 !important;
            border-collapse: separate !important;
            border-spacing: 0;
        }
        .table-scroll-wrap table.dataTable thead th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
            vertical-align: middle;
            padding: 12px 14px;
            border-bottom: 2px solid #e2e8f0;
        }
        .table-scroll-wrap table.dataTable tbody td {
            vertical-align: middle;
            padding: 10px 14px;
            font-size: 13px;
            white-space: nowrap;
        }
        .table-scroll-wrap table.dataTable tbody td.from-to-cell {
            white-space: normal;
            min-width: 280px;
        }
        .table-scroll-wrap table.dataTable tbody td.consignee-cell {
            white-space: normal;
            min-width: 220px;
        }
        .table-scroll-wrap table.dataTable tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }
        .table-scroll-wrap table.dataTable tbody tr:hover {
            background-color: #f1f5f9;
        }
        .table-scroll-wrap .dataTables_wrapper .dataTables_length,
        .table-scroll-wrap .dataTables_wrapper .dataTables_filter {
            margin-bottom: 0;
            padding: 12px 16px 4px;
        }
        .table-scroll-wrap .dataTables_wrapper .dataTables_info,
        .table-scroll-wrap .dataTables_wrapper .dataTables_paginate {
            margin-top: 0;
            padding: 8px 16px 12px;
        }
        /* Tab card styles */
        .tab-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .tab-card .nav-tabs {
            border-bottom: 2px solid #e5e7eb;
            padding: 0 16px;
            background: #f9fafb;
        }
        .tab-card .nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            padding: 14px 24px;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.2s ease;
            position: relative;
        }
        .tab-card .nav-tabs .nav-link:hover {
            color: #374151;
            background: transparent;
        }
        .tab-card .nav-tabs .nav-link.active {
            color: #4f46e5;
            background: transparent;
            border-bottom: 3px solid #4f46e5;
        }
        .tab-card .nav-tabs .nav-link .badge {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 10px;
            margin-left: 6px;
        }
        .tab-card .tab-content {
            padding: 0;
        }
        .tab-card .tab-pane {
            padding: 0;
        }
        .tab-card .card-body {
            padding: 16px;
        }
        /* Print label button style */
        .btn-print-label {
            background-color: #0e7490;
            color: white;
            border: none;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-print-label:hover {
            background-color: #155e75;
            color: white;
        }
        /* Shipment From/To route display (matches customer view-all-shipments) */
        .shipment-route-line {
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            height: 2px;
            transform: translateY(-50%);
            background: repeating-linear-gradient(
                90deg,
                #9ca3af 0 7px,
                transparent 7px 12px
            );
            background-size: 12px 2px;
            animation: shipment-route-flow .6s linear infinite;
        }
        .shipment-route-plane {
            position: absolute;
            left: 50%;
            z-index: 1;
            padding: 0 4px;
            line-height: 1;
            background: #fff;
            transform: translateX(-50%);
        }
        @keyframes shipment-route-flow {
            to {
                background-position: 12px 0;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .shipment-route-line {
                animation: none;
            }
        }
        /* ===== Dispute Charge Modal — premium UI ===== */
        #disputeChargeModal .modal-content.dispute-modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(153, 27, 27, 0.25), 0 8px 24px rgba(15, 23, 42, 0.12);
        }
        .dispute-modal-header {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 22px 22px 20px;
            color: #fff;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 55%, #7f1d1d 100%);
            overflow: hidden;
        }
        .dispute-modal-header::before {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            right: -60px;
            top: -80px;
            background: radial-gradient(circle, rgba(255,255,255,0.22) 0%, rgba(255,255,255,0) 70%);
            pointer-events: none;
        }
        .dispute-modal-header::after {
            content: "";
            position: absolute;
            width: 140px;
            height: 140px;
            left: -40px;
            bottom: -70px;
            background: radial-gradient(circle, rgba(255,255,255,0.14) 0%, rgba(255,255,255,0) 70%);
            pointer-events: none;
        }
        .dispute-icon-wrap {
            position: relative;
            z-index: 1;
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(6px);
            font-size: 24px;
        }
        .dispute-header-text {
            position: relative;
            z-index: 1;
            flex: 1;
            min-width: 0;
        }
        .dispute-header-text h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.1px;
            line-height: 1.25;
        }
        .dispute-header-text p {
            margin: 4px 0 0;
            font-size: 12.5px;
            line-height: 1.45;
            color: rgba(255, 255, 255, 0.85);
        }
        .dispute-modal-header .btn-close {
            position: relative;
            z-index: 2;
            filter: invert(1);
            opacity: 0.85;
            background-color: rgba(255,255,255,0.15);
            border-radius: 8px;
            padding: 8px;
            background-size: 12px;
        }
        .dispute-modal-header .btn-close:hover {
            opacity: 1;
            background-color: rgba(255,255,255,0.28);
        }
        .dispute-modal-body {
            padding: 20px 22px 18px;
            background: #fbfbfc;
        }
        .dispute-awb-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid #f1d5d5;
            box-shadow: 0 1px 2px rgba(220, 38, 38, 0.06);
        }
        .dispute-awb-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .dispute-awb-ic {
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #fef2f2;
            color: #dc2626;
            font-size: 19px;
            border: 1px solid #fecaca;
        }
        .dispute-awb-label {
            display: block;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #9ca3af;
            line-height: 1;
        }
        .dispute-awb-value {
            display: block;
            margin-top: 4px;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.4px;
            color: #111827;
            font-variant-numeric: tabular-nums;
        }
        .dispute-live-pill {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 5px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }
        .dispute-live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.18);
            animation: dispute-pulse 1.6s ease-in-out infinite;
        }
        @keyframes dispute-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(0.8); opacity: 0.7; }
        }
        .dispute-field {
            margin-top: 16px;
        }
        .dispute-field-head {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        .dispute-step-num {
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #111827;
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .dispute-step-num.step-2 {
            background: #dc2626;
        }
        .dispute-field-head label {
            margin: 0;
            font-size: 13.5px;
            font-weight: 700;
            color: #1f2937;
        }
        .dispute-field-head .req {
            color: #dc2626;
        }
        .dispute-select-wrap {
            position: relative;
        }
        .dispute-select-wrap > i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 17px;
            pointer-events: none;
            z-index: 2;
        }
        #disputeChargeModal .form-select.dispute-select {
            padding-left: 38px;
            padding-top: 11px;
            padding-bottom: 11px;
            border-radius: 12px;
            border: 1.5px solid #e5e7eb;
            font-size: 13.5px;
            font-weight: 600;
            color: #1f2937;
            background-color: #fff;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
            appearance: none;
        }
        #disputeChargeModal .form-select.dispute-select:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12);
            outline: none;
        }
        .dispute-hint {
            margin-top: 6px;
            font-size: 11.5px;
            color: #9ca3af;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .dispute-hint i {
            font-size: 14px;
        }
        .dispute-summary {
            margin-top: 16px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
        }
        .dispute-summary-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            border-bottom: 1px solid #e5e7eb;
        }
        .dispute-summary-head .s-ic {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #dc2626;
            color: #fff;
            font-size: 17px;
            flex-shrink: 0;
        }
        .dispute-summary-head strong {
            display: block;
            font-size: 13.5px;
            font-weight: 800;
            color: #111827;
            line-height: 1.2;
        }
        .dispute-summary-head small {
            display: block;
            font-size: 11.5px;
            color: #6b7280;
            font-weight: 500;
        }
        .dispute-summary-head .s-badge {
            margin-left: auto;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #047857;
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            padding: 4px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }
        .dispute-summary-grid {
            padding: 6px 16px 12px;
        }
        .dispute-kv {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            padding: 9px 0;
            border-bottom: 1px dashed #eef0f3;
            font-size: 12.5px;
        }
        .dispute-kv:last-child {
            border-bottom: none;
        }
        .dispute-kv .k {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #6b7280;
            font-weight: 600;
            flex-shrink: 0;
        }
        .dispute-kv .k i {
            font-size: 15px;
            color: #9ca3af;
        }
        .dispute-kv .v {
            text-align: right;
            color: #111827;
            font-weight: 700;
            min-width: 0;
            overflow-wrap: anywhere;
        }
        .dispute-value-band {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 0 16px 14px;
            padding: 12px 14px;
            border-radius: 12px;
            background: linear-gradient(135deg, #fef2f2 0%, #fff7ed 100%);
            border: 1px solid #fecaca;
        }
        .dispute-value-band .vb-label {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.7px;
            text-transform: uppercase;
            color: #991b1b;
        }
        .dispute-value-band .vb-sub {
            font-size: 11.5px;
            color: #b45309;
            margin-top: 2px;
            font-weight: 600;
        }
        .dispute-value-band .vb-amount {
            font-size: 20px;
            font-weight: 900;
            color: #b91c1c;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        .dispute-empty {
            margin-top: 16px;
            border-radius: 14px;
            border: 1.5px dashed #d1d5db;
            background: #f9fafb;
            padding: 22px 16px;
            text-align: center;
        }
        .dispute-empty .e-ic {
            width: 44px;
            height: 44px;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #fff;
            border: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 22px;
        }
        .dispute-empty strong {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #4b5563;
        }
        .dispute-empty span {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            color: #9ca3af;
        }
        .dispute-loading {
            margin-top: 16px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #fff;
            padding: 18px 16px;
            text-align: center;
        }
        .dispute-loading .spinner-border {
            width: 28px;
            height: 28px;
            color: #dc2626;
        }
        .dispute-loading p {
            margin: 10px 0 0;
            font-size: 12.5px;
            font-weight: 600;
            color: #6b7280;
        }
        .dispute-note {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            align-items: flex-start;
            font-size: 11.5px;
            line-height: 1.5;
            color: #92400e;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 9px 12px;
        }
        .dispute-note i {
            font-size: 15px;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .dispute-modal-footer {
            display: flex;
            gap: 10px;
            padding: 14px 22px 18px;
            background: #fbfbfc;
            border-top: 1px solid #f1f2f4;
        }
        .dispute-btn-close {
            flex: 1;
            border-radius: 12px;
            padding: 11px;
            font-size: 13.5px;
            font-weight: 700;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            color: #374151;
            transition: all 0.18s ease;
        }
        .dispute-btn-close:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
            color: #111827;
        }
        .dispute-btn-apply {
            flex: 1.4;
            border: none;
            border-radius: 12px;
            padding: 11px;
            font-size: 13.5px;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
        }
        .dispute-btn-apply:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(220, 38, 38, 0.38);
            filter: brightness(1.03);
            color: #fff;
        }
        .dispute-btn-apply:disabled {
            background: #e5e7eb;
            color: #9ca3af;
            box-shadow: none;
            cursor: not-allowed;
        }
        #disputeChargeModal .form-control.dispute-input {
            padding-left: 38px;
            padding-top: 11px;
            padding-bottom: 11px;
            border-radius: 12px;
            border: 1.5px solid #e5e7eb;
            font-size: 13.5px;
            font-weight: 700;
            color: #1f2937;
            background-color: #fff;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }
        #disputeChargeModal .form-control.dispute-input:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12);
            outline: none;
        }
        #disputeChargeModal .modal-dialog {
            max-width: 520px;
        }
        /* Condition list — full text wrap, no truncation */
        .dispute-cond-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 220px;
            overflow-y: auto;
            padding: 2px;
        }
        .dispute-cond-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            width: 100%;
            text-align: left;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
        }
        .dispute-cond-item:hover {
            border-color: #fca5a5;
            background: #fef2f2;
        }
        .dispute-cond-item.selected {
            border-color: #dc2626;
            background: #fef2f2;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
        }
        .dispute-cond-radio {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 2px;
            border-radius: 50%;
            border: 2px solid #d1d5db;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: border-color 0.15s ease;
        }
        .dispute-cond-item.selected .dispute-cond-radio {
            border-color: #dc2626;
        }
        .dispute-cond-item.selected .dispute-cond-radio::after {
            content: "";
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #dc2626;
        }
        .dispute-cond-text {
            flex: 1;
            min-width: 0;
            font-size: 12.5px;
            font-weight: 600;
            line-height: 1.5;
            color: #1f2937;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .dispute-cond-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 6px;
        }
        .dispute-cond-meta span {
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #4b5563;
            white-space: nowrap;
        }
        .dispute-cond-meta span.val {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }
    </style>

</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar Start -->
            @include('admin.partials.header')
        <!-- Topbar End -->

        <!-- Search Modal -->
        <div class="modal fade" id="searchModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-transparent">
                    <div class="card shadow-none mb-0">
                        <div class="px-3 py-2 d-flex flex-row align-items-center" id="search-top">
                            <i class="ti ti-search fs-22"></i>
                            <input type="search" class="form-control border-0" placeholder="Search">
                            <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x fs-22"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidenav Menu Start -->
            @include('admin.partials.sidebar')
        <!-- Sidenav Menu End -->

        <!-- ========================
			Start Page Content
		========================= -->
         
        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content">

                <!-- Page Header -->
                <!-- <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">View Customer List</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Customer List</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                    </div>
                </div>                 -->
				<!-- End Page Header -->

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Three-Tab Card: Manifested | Assigned for Pickup | Print Label -->
                <div class="tab-card">
                    <ul class="nav nav-tabs" id="shipmentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="manifested-tab" data-bs-toggle="tab" data-bs-target="#manifestedPane" type="button" role="tab" aria-controls="manifestedPane" aria-selected="true">
                                <i class="ti ti-package me-1"></i> Manifested
                                <span class="badge bg-primary">{{ count($manifestGroups) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="readyforpickup-tab" data-bs-toggle="tab" data-bs-target="#readyforpickupPane" type="button" role="tab" aria-controls="readyforpickupPane" aria-selected="false">
                                <i class="ti ti-calendar-event me-1"></i> Ready for Pickup
                                <span class="badge" style="background:#6f42c1;color:#fff;">{{ count($readyForPickupShipments) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assignedPane" type="button" role="tab" aria-controls="assignedPane" aria-selected="false">
                                <i class="ti ti-truck-delivery me-1"></i> Assigned for Pickup
                                <span class="badge" style="background:#6366f1;color:#fff;">{{ count($assignedForPickupShipments) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="printlabel-tab" data-bs-toggle="tab" data-bs-target="#printlabelPane" type="button" role="tab" aria-controls="printlabelPane" aria-selected="false">
                                <i class="ti ti-printer me-1"></i> Print Label
                                <span class="badge" style="background:#06b6d4;color:#fff;">{{ count($printLabelShipments) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="readytodispatch-tab" data-bs-toggle="tab" data-bs-target="#readytodispatchPane" type="button" role="tab" aria-controls="readytodispatchPane" aria-selected="false">
                                <i class="ti ti-truck me-1"></i> Ready to Dispatch
                                <span class="badge" style="background:#f59e0b;color:#fff;">{{ count($readyToDispatchShipments) }}</span>
                            </button>
                        </li>
                        <div class="gap-2 d-flex align-items-center justify-content-end flex-wrap">
                            <a href="javascript:void(0);" 
                            class="btn btn-icon btn-outline-light shadow" 
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            aria-label="Refresh" 
                            data-bs-original-title="Refresh" 
                            onclick="location.reload();">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                    </ul>
                    <div class="tab-content" id="shipmentTabContent">

                        <!-- ===== TAB 1: Manifested ===== -->
                        <div class="tab-pane fade show active" id="manifestedPane" role="tabpanel" aria-labelledby="manifested-tab">
                            <div class="card-body">
                                <div class="table-scroll-wrap">
                                    <table id="manifestedTable" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Manifest Code</th>
                                                <th>Order Date</th>
                                                <th>Shipments</th>
                                                <th>Total Value</th>
                                                <th>Cost</th>
                                                <th>Shipments Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($manifestGroups as $index => $manifest)
                                            <tr class="manifest-group-row">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-dark" style="font-size:12px;">{{ $manifest->manifest_number ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    {{ $manifest->manifest_created_at ? \Carbon\Carbon::parse($manifest->manifest_created_at)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $manifest->shipment_count }}</span>
                                                </td>
                                                <td style="font-weight:600;color:#0f172a;">
                                                    {{ number_format($manifest->total_value, 2) }}
                                                </td>
                                                <td style="color:#dc2626;">
                                                    {{ number_format($manifest->total_cost, 2) }}
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary btn-icon manifest-toggle" data-index="{{ $index }}" title="View Shipments">
                                                        <i class="ti ti-chevron-down"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Hidden templates for expandable shipment details (used by DataTables child rows) -->
                                @foreach($manifestGroups as $index => $manifest)
                                <template id="manifest-child-{{ $index }}">
                                    <div class="p-3">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>HAWB Number</th>
                                                    <th>From / To</th>
                                                    <th>Customer Name</th>
                                                    <th>Consignee</th>
                                                    <th>Invoice No.</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($manifest->shipments as $shipment)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-dark">{{ $shipment['awb_number'] }}</span>
                                                    </td>
                                                    <td style="font-size:12px;white-space:normal;">
                                                        <div>{{ $shipment['from'] }}</div>
                                                        <div class="text-muted"><i class="ti ti-arrow-right"></i> {{ $shipment['to'] }}</div>
                                                    </td>
                                                    <td>{{ $shipment['customer_name'] }}</td>
                                                    <td>{{ $shipment['consignee_name'] }}</td>
                                                    <td>{{ $shipment['invoice_number'] }}</td>
                                                    <td>{{ $shipment['amount_formatted'] }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                                @endforeach
                            </div>
                        </div>

                        <!-- ===== TAB 2: Ready for Pickup =====
                             Grouped by manifest number (one row per manifest, shipments
                             collapsed). "View" opens the manifest detail page in a new tab.
                             Each collapsed child keeps the Assign Pickup icon. -->
                        <div class="tab-pane fade" id="readyforpickupPane" role="tabpanel" aria-labelledby="readyforpickup-tab">
                            <div class="card-body">
                                <div class="table-scroll-wrap">
                                    <table id="readyforpickupTable" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Manifest Code</th>
                                                <th>Order Date</th>
                                                <th>Shipments</th>
                                                <th>Total Value</th>
                                                <th>Pickup Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($readyForPickupManifestGroups as $index => $manifest)
                                            <tr class="manifest-group-row">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-dark" style="font-size:12px;">{{ $manifest->manifest_number ?? 'N/A' }}</span>
                                                </td>
                                                <td style="white-space:nowrap;">
                                                    @if($manifest->manifest_created_at)
                                                        <div>{{ \Carbon\Carbon::parse($manifest->manifest_created_at)->format('d-m-Y') }}</div>
                                                        <div class="text-muted" style="font-size:11px;">{{ \Carbon\Carbon::parse($manifest->manifest_created_at)->format('h:i A') }}</div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $manifest->shipment_count }}</span>
                                                </td>
                                                <td style="font-weight:600;color:#0f172a;">
                                                    {{ number_format($manifest->total_value, 2) }}
                                                </td>
                                                <td style="font-weight:600;color:#0f172a;">
                                                    {{ $manifest->pickup_date ? \Carbon\Carbon::parse($manifest->pickup_date)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td class="table-actions">
                                                    @php
                                                        $bulkAwbs = collect($manifest->shipments ?? [])->pluck('awb_number')->filter()->values()->all();
                                                        $bulkAwbCsv = implode(', ', $bulkAwbs);
                                                    @endphp
                                                    @if(!empty($manifest->shipments))
                                                    <button class="btn btn-sm btn-outline-primary btn-icon" title="Assign Pickup (whole manifest: {{ $manifest->shipment_count }} shipment(s))"
                                                            onclick="openAssignBulkDelivery('{{ addslashes($manifest->manifest_number ?? '') }}', {{ (int) $manifest->shipment_count }}, '{{ addslashes($bulkAwbCsv) }}')">
                                                        <i class="ti ti-truck-delivery"></i>
                                                    </button>
                                                    @endif
                                                    <a href="{{ route('admin.manifest-detail', ['manifestNumber' => $manifest->manifest_number]) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-secondary btn-icon"
                                                       title="View Manifest Details">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    <!-- <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary btn-icon manifest-rfp-toggle" data-index="{{ $index }}" title="View Shipments">
                                                        <i class="ti ti-chevron-down"></i>
                                                    </a> -->
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Hidden templates for expandable shipment details (DataTables child rows) -->
                                @foreach($readyForPickupManifestGroups as $index => $manifest)
                                <template id="manifest-rfp-child-{{ $index }}">
                                    <div class="p-3">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>HAWB Number</th>
                                                    <th>From / To</th>
                                                    <th>Customer Name</th>
                                                    <th>Consignee</th>
                                                    <th>Invoice No.</th>
                                                    <th>Amount</th>
                                                    <th>Pickup Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($manifest->shipments as $shipment)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-dark">{{ $shipment['awb_number'] }}</span>
                                                    </td>
                                                    <td style="font-size:12px;white-space:normal;">
                                                        <div>{{ $shipment['from'] }}</div>
                                                        <div class="text-muted"><i class="ti ti-arrow-right"></i> {{ $shipment['to'] }}</div>
                                                    </td>
                                                    <td>{{ $shipment['customer_name'] }}</td>
                                                    <td>{{ $shipment['consignee_name'] }}</td>
                                                    <td>{{ $shipment['invoice_number'] }}</td>
                                                    <td>{{ $shipment['amount_formatted'] }}</td>
                                                    <td>
                                                        @if(!empty($shipment['pickup_date']))
                                                            <span class="badge" style="background:#6f42c1;color:#fff;white-space:nowrap;">
                                                                <i class="ti ti-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($shipment['pickup_date'])->format('d-m-Y') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td class="table-actions">
                                                        <button class="btn btn-sm btn-outline-primary btn-icon" title="Assign Pickup"
                                                                onclick="openAssignDelivery({{ $shipment['id'] }}, '{{ $shipment['delivery_type'] ?? '' }}', {{ $shipment['assigned_delivery_person'] ?? 'null' }}, '{{ $shipment['awb_number'] }}')">
                                                            <i class="ti ti-truck-delivery"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                                @endforeach
                            </div>
                        </div>

                        <!-- ===== TAB 3: Assigned for Pickup =====
                             Grouped by manifest number (one row per manifest, same
                             layout as the Ready for Pickup tab). Action keeps the
                             existing Receive Shipment button. -->
                        <div class="tab-pane fade" id="assignedPane" role="tabpanel" aria-labelledby="assigned-tab">
                            <div class="card-body">
                                <div class="table-scroll-wrap">
                                    <table id="assignedTable" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Manifest Code</th>
                                                <th>Order Date</th>
                                                <th>Shipments</th>
                                                <th>Total Value</th>
                                                <th>Pickup Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($assignedForPickupManifestGroups as $index => $manifest)
                                            <tr class="manifest-group-row">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-dark" style="font-size:12px;">{{ $manifest->manifest_number ?? 'N/A' }}</span>
                                                </td>
                                                <td style="white-space:nowrap;">
                                                    @if($manifest->manifest_created_at)
                                                        <div>{{ \Carbon\Carbon::parse($manifest->manifest_created_at)->format('d-m-Y') }}</div>
                                                        <div class="text-muted" style="font-size:11px;">{{ \Carbon\Carbon::parse($manifest->manifest_created_at)->format('h:i A') }}</div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $manifest->shipment_count }}</span>
                                                </td>
                                                <td style="font-weight:600;color:#0f172a;">
                                                    {{ number_format($manifest->total_value, 2) }}
                                                </td>
                                                <td style="font-weight:600;color:#0f172a;">
                                                    {{ $manifest->pickup_date ? \Carbon\Carbon::parse($manifest->pickup_date)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td class="table-actions">
                                                    @if(!empty($manifest->shipments))
                                                    <button class="btn btn-sm btn-outline-success btn-icon" title="Receive Shipment (whole manifest: {{ $manifest->shipment_count }} shipment(s))" onclick="openReceiveBulkShipment('{{ addslashes($manifest->manifest_number ?? '') }}', {{ (int) $manifest->shipment_count }})">
                                                        <i class="ti ti-package"></i>
                                                    </button>
                                                    @endif
                                                    @if(!empty($manifest->manifest_number))
                                                    <a href="{{ route('admin.manifest-detail', ['manifestNumber' => $manifest->manifest_number]) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-secondary btn-icon"
                                                       title="View Manifest Details">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Hidden templates for expandable shipment details (DataTables child rows) -->
                                @foreach($assignedForPickupManifestGroups as $index => $manifest)
                                <template id="manifest-asg-child-{{ $index }}">
                                    <div class="p-3">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>HAWB Number</th>
                                                    <th>From / To</th>
                                                    <th>Customer Name</th>
                                                    <th>Consignee</th>
                                                    <th>Invoice No.</th>
                                                    <th>Amount</th>
                                                    <th>Pickup Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($manifest->shipments as $shipment)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-dark">{{ $shipment['awb_number'] }}</span>
                                                    </td>
                                                    <td style="font-size:12px;white-space:normal;">
                                                        <div>{{ $shipment['from'] }}</div>
                                                        <div class="text-muted"><i class="ti ti-arrow-right"></i> {{ $shipment['to'] }}</div>
                                                    </td>
                                                    <td>{{ $shipment['customer_name'] }}</td>
                                                    <td>{{ $shipment['consignee_name'] }}</td>
                                                    <td>{{ $shipment['invoice_number'] }}</td>
                                                    <td>{{ $shipment['amount_formatted'] }}</td>
                                                    <td>
                                                        @if(!empty($shipment['pickup_date']))
                                                            <span class="badge" style="background:#6366f1;color:#fff;white-space:nowrap;">
                                                                <i class="ti ti-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($shipment['pickup_date'])->format('d-m-Y') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td class="table-actions">
                                                        <button class="btn btn-sm btn-outline-success btn-icon" title="Receive Shipment"
                                                                onclick="openReceiveShipment({{ $shipment['id'] }}, '{{ $manifest->manifest_number ?? '' }}')">
                                                            <i class="ti ti-package"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                                @endforeach
                            </div>
                        </div>

                        <!-- ===== TAB 3: Print Label ===== -->
                        <div class="tab-pane fade" id="printlabelPane" role="tabpanel" aria-labelledby="printlabel-tab">
                            <div class="card-body">
                                <div class="table-scroll-wrap">
                                    <table id="printlabelTable" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>HAWB Number</th>
                                                <th>Order Date</th>
                                                <th>Receiver Details</th>
                                                <th>Package Details</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($printLabelShipments as $index => $shipment)
                                            @php
                                                $plPkgs = $packagesByShipper->get($shipment->shipper_id, collect());
                                                $plBillable = 0.0; $plDead = 0.0; $plVol = 0.0;
                                                foreach ($plPkgs as $plPkg) {
                                                    if ($plPkg->chargeable_weight !== null && $plPkg->chargeable_weight !== '') { $plBillable += (float) $plPkg->chargeable_weight; }
                                                    if ($plPkg->actual_weight_kg !== null && $plPkg->actual_weight_kg !== '') { $plDead += (float) $plPkg->actual_weight_kg; }
                                                    if ($plPkg->volumetric_weight !== null && $plPkg->volumetric_weight !== '') { $plVol += (float) $plPkg->volumetric_weight; }
                                                }
                                                $plStatus = $shipment->shipper_status ?? 'dispatched';
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-dark">{{ $shipment->awb_number ?? 'N/A' }}</span>
                                                    <div class="hawb-sub-info">
                                                        <div class="hawb-sub-row">
                                                            <span class="hawb-sub-label">Destination:</span>
                                                            <span class="hawb-sub-value">{{ $shipment->consignee_destination ?: '-' }}{{ $shipment->consignee_zip ? ' · '.$shipment->consignee_zip : '' }}</span>
                                                        </div>
                                                        <div class="hawb-sub-row">
                                                            <span class="hawb-sub-label">Reference number:</span>
                                                            <span class="hawb-sub-value">{{ $shipment->reference_number ?: '-' }}</span>
                                                        </div>
                                                        <div class="hawb-sub-row">
                                                            <span class="hawb-sub-label">Invoice number:</span>
                                                            <span class="hawb-sub-value">{{ $shipment->invoice_number ?: '-' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>{{ \Carbon\Carbon::parse($shipment->created_at)->format('d M Y') }}</div>
                                                    <div class="text-muted">{{ \Carbon\Carbon::parse($shipment->created_at)->format('h:i A') }}</div>
                                                </td>
                                                <td>
                                                    <div class="receiver-details-stack">
                                                        <div class="receiver-name">{{ $shipment->consignee_name ?: ($shipment->consignee_contact ?: '-') }}</div>
                                                        @if($shipment->consignee_email)
                                                            <div class="receiver-line">{{ $shipment->consignee_email }}</div>
                                                        @endif
                                                        @if($shipment->consignee_phone)
                                                            <div class="receiver-line">{{ $shipment->consignee_phone }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if(count($plPkgs) > 0)
                                                        <div class="package-details-card">
                                                            @if(count($plPkgs) > 1)
                                                                <div class="package-details-title">Total ({{ count($plPkgs) }} pkgs)</div>
                                                            @endif
                                                            <div class="package-details-row">
                                                                <span class="package-details-label">Billable Wt.</span>
                                                                <span class="package-details-value">{{ $plBillable > 0 ? number_format($plBillable, 2).' kg' : '-' }}</span>
                                                            </div>
                                                            <div class="package-details-row">
                                                                <span class="package-details-label">Dead Wt.</span>
                                                                <span class="package-details-value">{{ $plDead > 0 ? number_format($plDead, 2).' kg' : '-' }}</span>
                                                            </div>
                                                            <div class="package-details-row">
                                                                <span class="package-details-label">Vol. Wt.</span>
                                                                <span class="package-details-value">{{ $plVol > 0 ? number_format($plVol, 2).' kg' : '-' }}</span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($plStatus === 'received')
                                                        <span class="badge bg-info">Received</span>
                                                    @else
                                                        <span class="badge bg-dark">Dispatched</span>
                                                    @endif
                                                </td>
                                                <td class="table-actions">
                                                    <button class="btn-print-label" onclick="printLabel({{ $shipment->id }})">
                                                        <i class="ti ti-printer me-1"></i> Print
                                                    </button>
                                                    <button class="btn-ready-to-dispatch ms-1" onclick="markReadyToDispatch({{ $shipment->id }})">
                                                        <i class="ti ti-truck me-1"></i> Ready to Dispatch
                                                    </button>
                                                    <div class="mt-1">
                                                        <button class="btn-dispute" onclick="openDisputeModal({{ $shipment->id }}, '{{ $shipment->awb_number ?? '' }}', 'weighing at first scan')">
                                                            <i class="ti ti-alert-triangle me-1"></i> Dispute
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ===== TAB 4: Ready to Dispatch (same table structure as Print Label) ===== -->
                        <div class="tab-pane fade" id="readytodispatchPane" role="tabpanel" aria-labelledby="readytodispatch-tab">
                            <div class="card-body">
                                <div class="table-scroll-wrap">
                                    <table id="readytodispatchTable" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>HAWB Number</th>
                                                <th>Order Date</th>
                                                <th>Receiver Details</th>
                                                <th>Package Details</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($readyToDispatchShipments as $index => $shipment)
                                            @php
                                                $rtdPkgs = $packagesByShipper->get($shipment->shipper_id, collect());
                                                $rtdBillable = 0.0; $rtdDead = 0.0; $rtdVol = 0.0;
                                                foreach ($rtdPkgs as $rtdPkg) {
                                                    if ($rtdPkg->chargeable_weight !== null && $rtdPkg->chargeable_weight !== '') { $rtdBillable += (float) $rtdPkg->chargeable_weight; }
                                                    if ($rtdPkg->actual_weight_kg !== null && $rtdPkg->actual_weight_kg !== '') { $rtdDead += (float) $rtdPkg->actual_weight_kg; }
                                                    if ($rtdPkg->volumetric_weight !== null && $rtdPkg->volumetric_weight !== '') { $rtdVol += (float) $rtdPkg->volumetric_weight; }
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-dark">{{ $shipment->awb_number ?? 'N/A' }}</span>
                                                    <div class="hawb-sub-info">
                                                        <div class="hawb-sub-row">
                                                            <span class="hawb-sub-label">Destination:</span>
                                                            <span class="hawb-sub-value">{{ $shipment->consignee_destination ?: '-' }}{{ $shipment->consignee_zip ? ' · '.$shipment->consignee_zip : '' }}</span>
                                                        </div>
                                                        <div class="hawb-sub-row">
                                                            <span class="hawb-sub-label">Reference number:</span>
                                                            <span class="hawb-sub-value">{{ $shipment->reference_number ?: '-' }}</span>
                                                        </div>
                                                        <div class="hawb-sub-row">
                                                            <span class="hawb-sub-label">Invoice number:</span>
                                                            <span class="hawb-sub-value">{{ $shipment->invoice_number ?: '-' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>{{ \Carbon\Carbon::parse($shipment->created_at)->format('d M Y') }}</div>
                                                    <div class="text-muted">{{ \Carbon\Carbon::parse($shipment->created_at)->format('h:i A') }}</div>
                                                </td>
                                                <td>
                                                    <div class="receiver-details-stack">
                                                        <div class="receiver-name">{{ $shipment->consignee_name ?: ($shipment->consignee_contact ?: '-') }}</div>
                                                        @if($shipment->consignee_email)
                                                            <div class="receiver-line">{{ $shipment->consignee_email }}</div>
                                                        @endif
                                                        @if($shipment->consignee_phone)
                                                            <div class="receiver-line">{{ $shipment->consignee_phone }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if(count($rtdPkgs) > 0)
                                                        <div class="package-details-card">
                                                            @if(count($rtdPkgs) > 1)
                                                                <div class="package-details-title">Total ({{ count($rtdPkgs) }} pkgs)</div>
                                                            @endif
                                                            <div class="package-details-row">
                                                                <span class="package-details-label">Billable Wt.</span>
                                                                <span class="package-details-value">{{ $rtdBillable > 0 ? number_format($rtdBillable, 2).' kg' : '-' }}</span>
                                                            </div>
                                                            <div class="package-details-row">
                                                                <span class="package-details-label">Dead Wt.</span>
                                                                <span class="package-details-value">{{ $rtdDead > 0 ? number_format($rtdDead, 2).' kg' : '-' }}</span>
                                                            </div>
                                                            <div class="package-details-row">
                                                                <span class="package-details-label">Vol. Wt.</span>
                                                                <span class="package-details-value">{{ $rtdVol > 0 ? number_format($rtdVol, 2).' kg' : '-' }}</span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="status-ready-to-dispatch">Ready to Dispatch</span>
                                                </td>
                                                <td class="table-actions">
                                                    <button class="btn-print-label" onclick="printLabel({{ $shipment->id }})">
                                                        <i class="ti ti-printer me-1"></i> Print
                                                    </button>
                                                    <div class="mt-1">
                                                        <button class="btn-dispute" onclick="openDisputeModal({{ $shipment->id }}, '{{ $shipment->awb_number ?? '' }}', 'After Dispatched')">
                                                            <i class="ti ti-alert-triangle me-1"></i> Dispute
                                                        </button>
                                                    </div>
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
                <!-- End Three-Tab Card -->

            </div>
            <!-- End Content -->

        </div>
        <!-- End Page Wrapper -->

    </div>
    <!-- End Main Wrapper -->

    <!-- Assign Delivery Modal -->
    <div class="modal fade" id="assignDeliveryModal" tabindex="-1" aria-labelledby="assignDeliveryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignDeliveryModalLabel">
                        <i class="ti ti-truck-delivery me-1"></i> Assign Pickup
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignDeliveryForm">
                    <input type="hidden" name="shipment_id" id="assign_shipment_id" value="">
                    <input type="hidden" name="assign_mode" id="assign_mode" value="single">
                    <input type="hidden" name="manifest_number" id="assign_manifest_number" value="">
                    <div class="modal-body">
                        <div id="assignDeliveryAlert" class="alert d-none"></div>

                        <div class="mb-3" id="assignSingleInfo">
                            <label class="form-label fw-semibold">HAWB Number</label>
                            <p class="mb-0" id="assign_awb_display">-</p>
                        </div>

                        <div class="mb-3 d-none" id="assignBulkInfo">
                            <label class="form-label fw-semibold">Manifest (whole manifest will be assigned)</label>
                            <p class="mb-1"><span class="badge bg-dark" id="assign_manifest_display">-</span>
                                <span class="badge bg-primary ms-1" id="assign_bulk_count"></span>
                            </p>
                            <small class="text-muted d-block">AWBs:</small>
                            <p class="mb-0 small" id="assign_bulk_awbs" style="word-break:break-word;">-</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pickup Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="delivery_ddu" value="DDU">
                                    <label class="form-check-label" for="delivery_ddu">
                                        <strong>Delhivery</strong><br>
                                        <small class="text-muted">Pickup Duty Unpaid</small>
                                    </label>
                                </div>
                                <!-- <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="delivery_ddp" value="DDP">
                                    <label class="form-check-label" for="delivery_ddp">
                                        <strong>Shiprocket</strong><br>
                                        <small class="text-muted">Pickup Duty Paid</small>
                                    </label>
                                </div> -->
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="delivery_self" value="Self">
                                    <label class="form-check-label" for="delivery_self">
                                        <strong>Self</strong><br>
                                        <small class="text-muted">Assign Pickup Person</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="deliveryPersonSection" style="display: none;">
                            <label class="form-label fw-semibold">Select Pickup Person <span class="text-danger">*</span></label>
                            <select class="form-select" name="delivery_person_id" id="delivery_person_id">
                                <option value="">-- Select Pickup Person --</option>
                                @foreach($deliveryPersons as $person)
                                    <option value="{{ $person->id }}">{{ $person->name }} @if($person->mobile) ({{ $person->mobile }}) @endif</option>
                                @endforeach
                            </select>
                            @if($deliveryPersons->isEmpty())
                                <div class="text-warning mt-1">
                                    <small><i class="ti ti-alert-triangle"></i> No pickup persons found. Please add them in the admin users section.</small>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="assignDeliveryBtn">
                            <i class="ti ti-device-floppy me-1"></i> Save Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Print Label Modal -->
    <div class="modal fade" id="printLabelModal" tabindex="-1" aria-labelledby="printLabelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printLabelModalLabel">
                        <i class="ti ti-printer me-1"></i> Print Shipping Label
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="printLabelContent">
                    <!-- Loading state -->
                    <div id="printLabelLoading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Generating label PDF...</p>
                    </div>
                    <!-- Error state -->
                    <div id="printLabelError" class="text-center py-4 d-none">
                        <i class="ti ti-alert-circle fs-24 text-danger d-block mb-2"></i>
                        <p class="text-danger" id="printLabelErrorMsg">Failed to generate label.</p>
                    </div>
                    <!-- PDF iframe -->
                    <iframe id="printLabelPdfFrame" style="width:100%;height:500px;border:none;display:none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary d-none" id="printLabelPrintBtn" onclick="triggerPdfPrint()">
                        <i class="ti ti-printer me-1"></i> Print Label
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Receive Shipment Modal -->
    <div class="modal fade" id="receiveShipmentModal" tabindex="-1" aria-labelledby="receiveShipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiveShipmentModalLabel">
                        <i class="ti ti-package me-1"></i> Receive Shipment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="receiveShipmentForm">
                    <input type="hidden" name="shipment_id" id="receive_shipment_id" value="">
                    <input type="hidden" name="receive_mode" id="receive_mode" value="single">
                    <input type="hidden" name="manifest_number" id="receive_manifest_number" value="">
                    <div class="modal-body">
                        <div id="receiveShipmentAlert" class="alert d-none"></div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Manifest Number</label>
                            <p class="mb-0"><span id="receive_manifest_display">-</span>
                                <span class="badge bg-primary ms-1 d-none" id="receive_bulk_count"></span>
                            </p>
                            <small class="text-muted d-none" id="receive_bulk_note">Whole manifest will be updated together.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Has this shipment been received? <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="received" id="receive_yes" value="yes">
                                    <label class="form-check-label" for="receive_yes">
                                        <strong>Yes</strong><br>
                                        <small class="text-muted">Shipment has been received</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="received" id="receive_no" value="no">
                                    <label class="form-check-label" for="receive_no">
                                        <strong>No</strong><br>
                                        <small class="text-muted">Shipment not received / On Hold</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="receiveShipmentBtn">
                            <i class="ti ti-check me-1"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Dispute Charge Modal — redesigned premium UI -->
    <div class="modal fade" id="disputeChargeModal" tabindex="-1" aria-labelledby="disputeChargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content dispute-modal-content">
                <div class="dispute-modal-header">
                    <div class="dispute-icon-wrap">
                        <i class="ti ti-alert-triangle"></i>
                    </div>
                    <div class="dispute-header-text">
                        <h5 id="disputeChargeModalLabel" style="color:white">Apply Dispute Charge</h5>
                        <p>Select a charge type to instantly preview the applicable surcharge.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body dispute-modal-body">
                    <!-- Shipment strip -->
                    <div class="dispute-awb-card">
                        <div class="dispute-awb-left">
                            <div class="dispute-awb-ic">
                                <i class="ti ti-barcode"></i>
                            </div>
                            <div style="min-width:0;">
                                <span class="dispute-awb-label">HAWB Number</span>
                                <span class="dispute-awb-value" id="dispute_awb_display">-</span>
                            </div>
                        </div>
                        <span class="dispute-live-pill">
                            <span class="dispute-live-dot"></span> Weighing Scan
                        </span>
                    </div>

                    <!-- Step 1 -->
                    <div class="dispute-field">
                        <div class="dispute-field-head">
                            <span class="dispute-step-num">1</span>
                            <label for="dispute_charge_type">Choose Charge Type <span class="req">*</span></label>
                        </div>
                        <div class="dispute-select-wrap">
                            <i class="ti ti-receipt-tax"></i>
                            <select class="form-select dispute-select" id="dispute_charge_type">
                                <option value="">Loading...</option>
                            </select>
                        </div>
                        <div class="dispute-hint">
                            <i class="ti ti-info-circle"></i>
                            <span id="dispute_type_hint">Charges load based on the shipment's destination &amp; service</span>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="dispute-field d-none" id="dispute_condition_wrap">
                        <div class="dispute-field-head">
                            <span class="dispute-step-num step-2">2</span>
                            <label>Select Condition <span class="req">*</span></label>
                        </div>
                        <div id="dispute_condition_list" class="dispute-cond-list"></div>
                        <input type="hidden" id="dispute_condition_id" value="">
                    </div>

                    <!-- Step 3 : boxes (shown only for flat/box calculation) -->
                    <div class="dispute-field d-none" id="dispute_boxes_wrap">
                        <div class="dispute-field-head">
                            <span class="dispute-step-num step-2">3</span>
                            <label for="dispute_boxes_count">Enter number of boxes <span class="req">*</span></label>
                        </div>
                        <div class="dispute-select-wrap">
                            <i class="ti ti-package"></i>
                            <input type="number" class="form-control dispute-input" id="dispute_boxes_count"
                                min="1" step="1" placeholder="e.g. 2">
                        </div>
                        <div class="dispute-hint">
                            <i class="ti ti-info-circle"></i>
                            <span>Calculation <b>flat/box</b> hai — total = per-box rate &times; boxes</span>
                        </div>
                    </div>

                    <!-- Custom Amount (shown for Weight disputes or Custom-valued rules) -->
                    <div class="dispute-field d-none" id="dispute_custom_wrap">
                        <div class="dispute-field-head">
                            <span class="dispute-step-num step-2">3</span>
                            <label for="dispute_custom_amount">Enter custom amount <span class="req">*</span></label>
                        </div>
                        <div class="dispute-select-wrap">
                            <i class="ti ti-currency-rupee"></i>
                            <input type="number" class="form-control dispute-input" id="dispute_custom_amount"
                                min="1" step="0.01" placeholder="e.g. 500">
                        </div>
                        <div class="dispute-hint">
                            <i class="ti ti-info-circle"></i>
                            <span>This <b>amount</b> will be charged instead of the rule rate (GST applies separately as per the rule)</span>
                        </div>
                    </div>

                    <!-- Summary / states -->
                    <div id="dispute_charge_detail" class="mb-0"></div>

                    <div class="dispute-note">
                        <i class="ti ti-bulb"></i>
                        <span>This is a preview of the applicable charge. The final billed amount, including applicable GST, will be reflected on the invoice.</span>
                    </div>
                </div>
                <div class="dispute-modal-footer">
                    <button type="button" class="dispute-btn-close" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="dispute-btn-apply" id="dispute_apply_btn" disabled>
                        <i class="ti ti-check"></i> Apply Charge
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>  

    <!-- Daterangepikcer JS -->
	<script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
	<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Choices Js -->	
    <script src="{{ asset('assets/plugins/choices.js/public/assets/scripts/choices.min.js') }}" type="text/javascript"></script>

    <!-- Mobile Input -->
    <script src="{{ asset('assets/plugins/intltelinput/js/intlTelInput.js') }}" type="text/javascript"></script>

    <!-- Quill JS -->
    <script src="{{ asset('assets/plugins/quill/quill.min.js') }}" type="text/javascript"></script>

	<!-- Simplebar JS -->
	<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

    <script data-cfasync="false">
        $(document).ready(function() {
            // Initialize DataTables for each tab
            const manifestedDt = $('#manifestedTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                scrollX: true,
                scrollY: '60vh',
                scrollCollapse: true,
                columnDefs: [
                    { orderable: false, targets: 6 }
                ],
                language: {
                    emptyTable: "No manifested shipments found",
                    info: "Showing _START_ to _END_ of _TOTAL_ manifests",
                    infoEmpty: "Showing 0 to 0 of 0 manifests",
                    infoFiltered: "(filtered from _MAX_ total manifests)",
                    lengthMenu: "Show _MENU_ manifests",
                    search: "Search:",
                    zeroRecords: "No matching manifests found"
                }
            });

            // Expand / collapse manifest shipment details (DataTables child rows)
            $('#manifestedTable tbody').on('click', '.manifest-toggle', function (e) {
                e.stopPropagation();
                const tr = $(this).closest('tr');
                const index = $(this).data('index');
                const row = manifestedDt.row(tr);
                const icon = $(this).find('i');

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    icon.removeClass('ti-chevron-up').addClass('ti-chevron-down');
                } else {
                    const template = document.getElementById('manifest-child-' + index);
                    const content = template ? template.innerHTML : '<div class="p-3">No details available.</div>';
                    row.child(content).show();
                    tr.addClass('shown');
                    icon.removeClass('ti-chevron-down').addClass('ti-chevron-up');
                }
            });

            const readyforpickupDt = $('#readyforpickupTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                scrollX: true,
                scrollY: '60vh',
                scrollCollapse: true,
                columnDefs: [
                    { orderable: false, targets: 6 },
                    { defaultContent: '-', targets: '_all' }
                ],
                language: {
                    emptyTable: "No shipments ready for pickup",
                    info: "Showing _START_ to _END_ of _TOTAL_ manifests",
                    infoEmpty: "Showing 0 to 0 of 0 manifests",
                    infoFiltered: "(filtered from _MAX_ total manifests)",
                    lengthMenu: "Show _MENU_ manifests",
                    search: "Search:",
                    zeroRecords: "No matching manifests found"
                }
            });

            // Expand / collapse ready-for-pickup manifest shipment details (DataTables child rows)
            $('#readyforpickupTable tbody').on('click', '.manifest-rfp-toggle', function (e) {
                e.stopPropagation();
                const tr = $(this).closest('tr');
                const index = $(this).data('index');
                const row = readyforpickupDt.row(tr);
                const icon = $(this).find('i');

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    icon.removeClass('ti-chevron-up').addClass('ti-chevron-down');
                } else {
                    const template = document.getElementById('manifest-rfp-child-' + index);
                    const content = template ? template.innerHTML : '<div class="p-3">No details available.</div>';
                    row.child(content).show();
                    tr.addClass('shown');
                    icon.removeClass('ti-chevron-down').addClass('ti-chevron-up');
                }
            });

            const assignedDt = $('#assignedTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                scrollX: true,
                scrollY: '60vh',
                scrollCollapse: true,
                columnDefs: [
                    { orderable: false, targets: 6 },
                    { defaultContent: '-', targets: '_all' }
                ],
                language: {
                    emptyTable: "No assigned for pickup manifests found",
                    info: "Showing _START_ to _END_ of _TOTAL_ manifests",
                    infoEmpty: "Showing 0 to 0 of 0 manifests",
                    infoFiltered: "(filtered from _MAX_ total manifests)",
                    lengthMenu: "Show _MENU_ manifests",
                    search: "Search:",
                    zeroRecords: "No matching manifests found"
                }
            });

            // Expand / collapse assigned-for-pickup manifest shipment details (DataTables child rows)
            $('#assignedTable tbody').on('click', '.manifest-asg-toggle', function (e) {
                e.stopPropagation();
                const tr = $(this).closest('tr');
                const index = $(this).data('index');
                const row = assignedDt.row(tr);
                const icon = $(this).find('i');

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    icon.removeClass('ti-chevron-up').addClass('ti-chevron-down');
                } else {
                    const template = document.getElementById('manifest-asg-child-' + index);
                    const content = template ? template.innerHTML : '<div class="p-3">No details available.</div>';
                    row.child(content).show();
                    tr.addClass('shown');
                    icon.removeClass('ti-chevron-down').addClass('ti-chevron-up');
                }
            });

            $('#printlabelTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                scrollX: true,
                scrollY: '60vh',
                scrollCollapse: true,
                language: {
                    emptyTable: "No dispatched shipments available for label printing",
                    info: "Showing _START_ to _END_ of _TOTAL_ shipments",
                    infoEmpty: "Showing 0 to 0 of 0 shipments",
                    infoFiltered: "(filtered from _MAX_ total shipments)",
                    lengthMenu: "Show _MENU_ shipments",
                    search: "Search:",
                    zeroRecords: "No matching shipments found"
                }
            });

            $('#readytodispatchTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                scrollX: true,
                scrollY: '60vh',
                scrollCollapse: true,
                language: {
                    emptyTable: "No shipments ready to dispatch",
                    info: "Showing _START_ to _END_ of _TOTAL_ shipments",
                    infoEmpty: "Showing 0 to 0 of 0 shipments",
                    infoFiltered: "(filtered from _MAX_ total shipments)",
                    lengthMenu: "Show _MENU_ shipments",
                    search: "Search:",
                    zeroRecords: "No matching shipments found"
                }
            });

            // Reinitialize DataTable when switching tabs (to fix layout issues)
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const targetId = $(e.target).attr('data-bs-target');
                const tableId = targetId === '#manifestedPane' ? 'manifestedTable'
                              : targetId === '#readyforpickupPane' ? 'readyforpickupTable'
                              : targetId === '#assignedPane' ? 'assignedTable'
                              : targetId === '#printlabelPane' ? 'printlabelTable'
                              : 'readytodispatchTable';
                const dt = $(`#${tableId}`).DataTable();
                dt.columns.adjust().draw();
            });

            // ===== Assign Delivery Modal Logic =====

            /**
             * Show/hide delivery person section when delivery type radio changes
             */
            $('input[name="delivery_type"]').on('change', function() {
                if ($(this).val() === 'Self') {
                    $('#deliveryPersonSection').slideDown(200);
                } else {
                    $('#deliveryPersonSection').slideUp(200);
                    $('#delivery_person_id').val('');
                }
            });

            /**
             * Handle form submission via AJAX
             */
            $('#assignDeliveryForm').on('submit', function(e) {
                e.preventDefault();

                // Basic client-side validation
                const deliveryType = $('input[name="delivery_type"]:checked').val();
                if (!deliveryType) {
                    showAssignDeliveryAlert('Please select a delivery type.', 'danger');
                    return;
                }
                if (deliveryType === 'Self' && !$('#delivery_person_id').val()) {
                    showAssignDeliveryAlert('Please select a delivery person for Self delivery.', 'danger');
                    return;
                }

                const $btn = $('#assignDeliveryBtn');
                // Show contextual loading text based on delivery type
                if (deliveryType === 'DDU') {
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Creating Delhivery Pickup...');
                } else {
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
                }

                $.ajax({
                    url: $('#assign_mode').val() === 'bulk'
                        ? '{{ route("admin.assign-delivery-bulk") }}'
                        : '{{ route("admin.assign-delivery") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            let alertMsg = response.message;

                            // Bulk assign: list Delhivery-failed orders (if any) under the message.
                            if (response.failed && response.failed.length > 0) {
                                alertMsg += '<br><small class="text-danger">Delhivery failed for: ' +
                                    response.failed.map(function (f) {
                                        return (f.awb || f.order || '-') + (f.remarks ? ' (' + f.remarks + ')' : '');
                                    }).join(', ') + '</small>';
                            }

                            // If Delhivery API was called, show additional details
                            if (response.delhivery) {
                                const delhivery = response.delhivery;
                                if (delhivery.success) {
                                    // Show waybill/awb info from Delhivery if available
                                    let waybillInfo = '';
                                    const delhiveryData = delhivery.data || {};
                                    // Extract waybill from rmk array or packages array
                                    if (delhiveryData.rmk && delhiveryData.rmk.length > 0) {
                                        waybillInfo = '<br><strong>Delhivery Waybill: ' + delhiveryData.rmk[0] + '</strong>';
                                    } 
                                    else if (delhiveryData.packages && delhiveryData.packages.length > 0) {
                                        waybillInfo = '<br><strong>Delhivery Waybill: ' + (delhiveryData.packages[0].waybill || '') + '</strong>';
                                    }
                                    alertMsg += waybillInfo;
                                    showAssignDeliveryAlert(alertMsg, 'success');
                                } else {
                                    // Delhivery API call failed but assignment was saved
                                    let failMsg = alertMsg;
                                    if (delhivery.message) {
                                        failMsg += '<br><small class="text-muted">Delhivery Error: ' + delhivery.message + '</small>';
                                    }
                                    // Show per-package error details if available
                                    const delhiveryData = delhivery.data || {};
                                    if (delhiveryData.packages && delhiveryData.packages.length > 0) {
                                        const pkg = delhiveryData.packages[0];
                                        if (pkg.status === 'Fail') {
                                            failMsg += '<br><small class="text-danger">Package Status: Failed</small>';
                                        }
                                        if (pkg.remarks && pkg.remarks.length > 0) {
                                            const remarks = pkg.remarks.filter(r => r && r.trim() !== '');
                                            if (remarks.length > 0) {
                                                failMsg += '<br><small class="text-danger">Reason: ' + remarks.join(', ') + '</small>';
                                            }
                                        }
                                    }
                                    if (delhiveryData.rmk) {
                                        const rmkText = Array.isArray(delhiveryData.rmk) ? delhiveryData.rmk.join(', ') : delhiveryData.rmk;
                                        if (rmkText) {
                                            failMsg += '<br><small class="text-muted">Delhivery Remark: ' + rmkText + '</small>';
                                        }
                                    }
                                    showAssignDeliveryAlert(failMsg, 'warning');
                                }
                            } else {
                                // Bulk with partial Delhivery failures -> warning, else success.
                                const alertType = (response.failed && response.failed.length > 0) ? 'warning' : 'success';
                                showAssignDeliveryAlert(alertMsg, alertType);
                            }

                            $btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
                            // Reload page after a short delay to reflect changes (shipment moves to Assigned tab)
                            setTimeout(function() {
                                // location.reload();
                            }, 2500);
                        } else {
                            showAssignDeliveryAlert(response.message || 'Something went wrong.', 'danger');
                            $btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            }
                        }
                        showAssignDeliveryAlert(msg, 'danger');
                        $btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
                    }
                });
            });

            /**
             * Reset modal form when it is hidden
             */
            $('#assignDeliveryModal').on('hidden.bs.modal', function() {
                $('#assignDeliveryAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');
                $('input[name="delivery_type"]').prop('checked', false);
                $('#delivery_person_id').val('');
                $('#deliveryPersonSection').hide();
                $('#assign_mode').val('single');
                $('#assign_shipment_id').val('');
                $('#assign_manifest_number').val('');
                $('#assignSingleInfo').removeClass('d-none');
                $('#assignBulkInfo').addClass('d-none');
                $('#assignDeliveryBtn').prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
            });

            // ===== Receive Shipment Modal Logic =====

            /**
             * Handle receive shipment form submission via AJAX
             */
            $('#receiveShipmentForm').on('submit', function(e) {
                e.preventDefault();

                const received = $('input[name="received"]:checked').val();
                if (!received) {
                    showReceiveShipmentAlert('Please select Yes or No.', 'danger');
                    return;
                }

                const $btn = $('#receiveShipmentBtn');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Submitting...');

                $.ajax({
                    url: $('#receive_mode').val() === 'bulk'
                        ? '{{ route("admin.receive-shipment-bulk") }}'
                        : '{{ route("admin.receive-shipment") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showReceiveShipmentAlert(response.message, 'success');
                            $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Submit');
                            // Reload page after a short delay to reflect changes
                            setTimeout(function() {
                                location.reload();
                            }, 2500);
                        } else {
                            showReceiveShipmentAlert(response.message || 'Something went wrong.', 'danger');
                            $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Submit');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            }
                        }
                        showReceiveShipmentAlert(msg, 'danger');
                        $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Submit');
                    }
                });
            });

            /**
             * Reset receive shipment modal when it is hidden
             */
            $('#receiveShipmentModal').on('hidden.bs.modal', function() {
                $('#receiveShipmentAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');
                $('input[name="received"]').prop('checked', false);
                $('#receive_mode').val('single');
                $('#receive_shipment_id').val('');
                $('#receive_manifest_number').val('');
                $('#receive_bulk_count').addClass('d-none').text('');
                $('#receive_bulk_note').addClass('d-none');
                $('#receiveShipmentBtn').prop('disabled', false).html('<i class="ti ti-check me-1"></i> Submit');
            });

            /**
             * Reset Print Label modal when it is hidden - revoke blob URL to free memory
             */
            $('#printLabelModal').on('hidden.bs.modal', function() {
                // Revoke the blob URL to free memory
                if (window._labelPdfBlobUrl) {
                    URL.revokeObjectURL(window._labelPdfBlobUrl);
                    window._labelPdfBlobUrl = null;
                }
                // Reset modal states
                $('#printLabelLoading').addClass('d-none');
                $('#printLabelError').addClass('d-none');
                $('#printLabelPdfFrame').css('display', 'none').attr('src', '');
                $('#printLabelPrintBtn').addClass('d-none');
            });

            /**
             * Helper to show alerts inside the assign delivery modal
             */
            function showAssignDeliveryAlert(message, type) {
                const $alert = $('#assignDeliveryAlert');
                $alert.removeClass('d-none alert-success alert-danger alert-warning').addClass('alert-' + type).html(message);
            }

            /**
             * Helper to show alerts inside the receive shipment modal
             */
            function showReceiveShipmentAlert(message, type) {
                const $alert = $('#receiveShipmentAlert');
                $alert.removeClass('d-none alert-success alert-danger').addClass('alert-' + type).html(message);
            }

        });

        /**
         * Open the Assign Delivery modal and pre-populate with current values.
         * @param {number} shipmentId
         * @param {string} currentType - Current delivery type (DDU, DDP, Self, or empty)
         * @param {number|null} currentPersonId - Current assigned delivery person ID
         * @param {string} awbNumber - AWB number for display
         */
        function openAssignDelivery(shipmentId, currentType, currentPersonId, awbNumber) {
            // Single-shipment mode
            $('#assign_mode').val('single');
            $('#assign_shipment_id').val(shipmentId);
            $('#assign_manifest_number').val('');
            $('#assignSingleInfo').removeClass('d-none');
            $('#assignBulkInfo').addClass('d-none');

            // Display AWB number
            $('#assign_awb_display').text(awbNumber || '-');

            // Pre-select the current delivery type radio
            $('input[name="delivery_type"]').prop('checked', false);
            if (currentType) {
                $('input[name="delivery_type"][value="' + currentType + '"]').prop('checked', true);
            }

            // Show/hide delivery person section based on current delivery type
            if (currentType === 'Self') {
                $('#deliveryPersonSection').show();
            } else {
                $('#deliveryPersonSection').hide();
            }

            // Pre-select the delivery person
            if (currentPersonId && currentPersonId !== 'null') {
                $('#delivery_person_id').val(currentPersonId);
            } else {
                $('#delivery_person_id').val('');
            }

            // Reset alert
            $('#assignDeliveryAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');

            // Open the modal
            $('#assignDeliveryModal').modal('show');
        }

        /**
         * Open the Assign Delivery modal in BULK mode for a whole manifest.
         * All Ready-for-Pickup shipments of the manifest are assigned together.
         * @param {string} manifestNumber
         * @param {number} shipmentCount
         * @param {string} awbCsv - comma-separated AWB numbers for display
         */
        function openAssignBulkDelivery(manifestNumber, shipmentCount, awbCsv) {
            // Bulk-manifest mode
            $('#assign_mode').val('bulk');
            $('#assign_shipment_id').val('');
            $('#assign_manifest_number').val(manifestNumber);
            $('#assignSingleInfo').addClass('d-none');
            $('#assignBulkInfo').removeClass('d-none');

            // Display manifest summary
            $('#assign_manifest_display').text(manifestNumber || '-');
            $('#assign_bulk_count').text((shipmentCount || 0) + ' shipment(s)');
            $('#assign_bulk_awbs').text(awbCsv || '-');

            // Fresh pickup-type selection for the bulk assignment
            $('input[name="delivery_type"]').prop('checked', false);
            $('#deliveryPersonSection').hide();
            $('#delivery_person_id').val('');

            // Reset alert
            $('#assignDeliveryAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');

            // Open the modal
            $('#assignDeliveryModal').modal('show');
        }

        /**
         * Open the Receive Shipment modal.
         * @param {number} shipmentId
         * @param {string} manifestNumber - Manifest number for display
         */
        function openReceiveShipment(shipmentId, manifestNumber) {
            // Single-shipment mode
            $('#receive_mode').val('single');
            // Set shipment ID
            $('#receive_shipment_id').val(shipmentId);
            $('#receive_manifest_number').val('');

            // Display Manifest number
            $('#receive_manifest_display').text(manifestNumber || '-');
            $('#receive_bulk_count').addClass('d-none').text('');
            $('#receive_bulk_note').addClass('d-none');

            // Reset radio buttons
            $('input[name="received"]').prop('checked', false);

            // Reset alert
            $('#receiveShipmentAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');

            // Open the modal
            $('#receiveShipmentModal').modal('show');
        }

        /**
         * Open the Receive Shipment modal in BULK mode for a whole manifest.
         * Yes/No applies to ALL Assigned-for-Pickup shipments of the manifest together.
         * @param {string} manifestNumber
         * @param {number} shipmentCount
         */
        function openReceiveBulkShipment(manifestNumber, shipmentCount) {
            // Bulk-manifest mode
            $('#receive_mode').val('bulk');
            $('#receive_shipment_id').val('');
            $('#receive_manifest_number').val(manifestNumber);

            // Display manifest summary
            $('#receive_manifest_display').text(manifestNumber || '-');
            $('#receive_bulk_count').removeClass('d-none').text((shipmentCount || 0) + ' shipment(s)');
            $('#receive_bulk_note').removeClass('d-none');

            // Reset radio buttons
            $('input[name="received"]').prop('checked', false);

            // Reset alert
            $('#receiveShipmentAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');

            // Open the modal
            $('#receiveShipmentModal').modal('show');
        }

        /**
         * Open the Dispute Charge modal and load the dropdown.
         * The dropdown lists all dispute_surcharge_charges rows
         * with place_of_apply 'weighing at first scan' or 'After Dispatched'.
         * @param {number} shipmentId
         * @param {string} awbNumber - AWB number for display
         * @param {string} [place] - If provided (e.g. 'After Dispatched'), only rules for that stage are loaded.
         */
        let disputeChargesCache = [];
        let disputeCurrentShipment = null;
        let disputePlaceFilter = null;
        const escDispute = function (v) { return $('<div>').text(v ?? '-').html(); };

        function disputeSetDetail(html) {
            $('#dispute_charge_detail').html(html || '');
        }

        function disputeEmptyState(title, sub, icon) {
            return '<div class="dispute-empty">' +
                '<div class="e-ic"><i class="ti ' + (icon || 'ti-receipt-off') + '"></i></div>' +
                '<strong>' + escDispute(title) + '</strong>' +
                '<span>' + escDispute(sub) + '</span></div>';
        }

        function openDisputeModal(shipmentId, awbNumber, place) {
            disputeCurrentShipment = shipmentId;
            // Ready to Dispatch se khule toh sirf 'After Dispatched' rules dikhenge.
            disputePlaceFilter = place || null;
            // Display AWB number
            $('#dispute_awb_display').text(awbNumber || '-');

            // Reset dropdowns + detail
            const $typeSelect = $('#dispute_charge_type');
            const $condWrap = $('#dispute_condition_wrap');
            $('#dispute_condition_id').val('');
            $('#dispute_condition_list').html('');
            $('#dispute_boxes_count').val('');
            $('#dispute_boxes_wrap').addClass('d-none');
            $('#dispute_custom_amount').val('');
            $('#dispute_custom_wrap').addClass('d-none');
            $typeSelect.html('<option value="">Loading charges...</option>');
            $condWrap.addClass('d-none');
            $('#dispute_apply_btn').prop('disabled', true);
            $('#dispute_type_hint').text('Loading charges based on the shipment destination & service...');
            disputeSetDetail(
                '<div class="dispute-loading">' +
                '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>' +
                '<p>Available dispute charges load ho rahe hain...</p></div>'
            );

            // Open the modal
            $('#disputeChargeModal').modal('show');

            const renderTypes = function (charges) {
                disputeChargesCache = charges || [];
                if (!disputeChargesCache.length) {
                    $typeSelect.html('<option value="">No dispute charges found</option>');
                    $('#dispute_type_hint').text('No charge rule matched for this shipment');
                    disputeSetDetail(disputeEmptyState(
                        'No charges available',
                        'No dispute rule found for this shipment destination / service.',
                        'ti-search-off'
                    ));
                    return;
                }
                const seen = {};
                let html = '<option value="">-- Select charge type --</option>';
                disputeChargesCache.forEach(function (c) {
                    const t = c.additional_charges || '';
                    if (t === '' || seen[t]) return;
                    seen[t] = true;
                    const count = disputeChargesCache.filter(function (x) { return (x.additional_charges || '') === t; }).length;
                    html += '<option value="' + escDispute(t) + '">' + escDispute(t) + ' (' + count + ' rule' + (count > 1 ? 's' : '') + ')</option>';
                });
                $typeSelect.html(html);
                $('#dispute_type_hint').text(disputeChargesCache.length + ' Select a charge type to view the applicable rules.');
                disputeSetDetail(disputeEmptyState(
                    'Charge Preview',
                    'Select a charge type in Step 1, then choose the applicable condition in Step 2 to view the surcharge preview.',
                    'ti-file-description'
                ));
            };

            $.ajax({
                url: '{{ route("admin.dispute-charges-list") }}',
                type: 'GET',
                data: (function () {
                    const d = { shipment_id: shipmentId };
                    if (disputePlaceFilter) { d.place_of_apply = disputePlaceFilter; }
                    return d;
                })(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        renderTypes(response.charges);
                    } else {
                        $typeSelect.html('<option value="">Failed to load</option>');
                        disputeSetDetail(disputeEmptyState('Load failed', response.message || 'Something went wrong.', 'ti-alert-circle'));
                        showAlert(response.message || 'Something went wrong.', 'error');
                    }
                },
                error: function () {
                    $typeSelect.html('<option value="">Failed to load</option>');
                    disputeSetDetail(disputeEmptyState('Load failed', 'Network issue — please retry.', 'ti-wifi-off'));
                    showAlert('Could not load dispute charges. Please try again.', 'error');
                }
            });
        }

        const isBoxCalc = function (calc) { return /box/i.test(calc || ''); };
        const isWeightDispute = function (type) { return /weight/i.test(type || ''); };
        // Show the custom amount field when the charge type is Weight OR the rule values contain "Custom".
        const isCustomAmount = function (type, values) { return isWeightDispute(type) || /custom/i.test(values || ''); };
        const parseDisputeRate = function (values) {
            const m = String(values || '').replace(/,/g, '').match(/(\d+(?:\.\d+)?)/);
            return m ? parseFloat(m[1]) : NaN;
        };

        // Charge Type select karne par uski saari conditions card list me (full text, no cut).
        $(document).on('change', '#dispute_charge_type', function () {
            const selectedType = $(this).val();
            const $condWrap = $('#dispute_condition_wrap');
            const $condList = $('#dispute_condition_list');
            $('#dispute_condition_id').val('');
            $('#dispute_boxes_count').val('');
            $('#dispute_boxes_wrap').addClass('d-none');
            $('#dispute_custom_amount').val('');
            $('#dispute_custom_wrap').addClass('d-none');
            $('#dispute_apply_btn').prop('disabled', true);
            if (!selectedType) {
                $condList.html('');
                $condWrap.addClass('d-none');
                disputeSetDetail(disputeEmptyState(
                    'Charge preview will appear here',
                    'Select a charge type in Step 1, then choose the applicable condition in Step 2.',
                    'ti-file-description'
                ));
                return;
            }
            const matches = disputeChargesCache.filter(function (c) { return (c.additional_charges || '') === selectedType; });
            let html = '';
            matches.forEach(function (c) {
                const condTxt = (c.conditions || '').trim();
                html += '<button type="button" class="dispute-cond-item" data-id="' + c.id + '">' +
                    '<span class="dispute-cond-radio"></span>' +
                    '<span class="dispute-cond-text">' + (condTxt ? escDispute(condTxt) : '') +
                        '<span class="dispute-cond-meta">' +
                            '<span>' + escDispute(c.destination || '-') + '</span>' +
                            '<span>' + escDispute(c.service_id || '-') + '</span>' +
                            '<span class="val">' + escDispute(c.values || '-') + '</span>' +
                        '</span>' +
                    '</span>' +
                '</button>';
            });
            $condList.html(html);
            $condWrap.removeClass('d-none').hide().slideDown(180);
            // Weight dispute ho toh custom amount field dikhao.
            if (isWeightDispute(selectedType)) {
                $('#dispute_custom_wrap').removeClass('d-none').hide().slideDown(180);
            }
            $('#dispute_type_hint').text(matches.length + ' conditions available — select the condition');
            disputeSetDetail(disputeEmptyState(
                'Now select a condition',
                '"' + selectedType + '" — choose one of its ' + matches.length + ' rules.',
                'ti-list-check'
            ));
        });

        // Condition card click -> select + summary dikhao.
        $(document).on('click', '.dispute-cond-item', function () {
            const id = $(this).data('id');
            $('.dispute-cond-item').removeClass('selected');
            $(this).addClass('selected');
            $('#dispute_condition_id').val(id);
            $('#dispute_condition_id').trigger('change');
        });

        // Summary render — boxes ke saath total bhi dikhata hai.
        function renderDisputeSummary() {
            const selectedId = $('#dispute_condition_id').val();
            const $detail = $('#dispute_charge_detail');
            const $apply = $('#dispute_apply_btn');
            const found = disputeChargesCache.find(function (c) { return String(c.id) === String(selectedId); });
            if (!found) {
                $detail.html(disputeEmptyState('Now select a condition', 'Choose a condition to view the summary.', 'ti-list-check'));
                $apply.prop('disabled', true);
                return;
            }
            const gstPct = parseFloat(found.gst_percentage) || 0;
            const gstTxt = (found.gst_percentage !== undefined && found.gst_percentage !== null && String(found.gst_percentage) !== '')
                ? String(found.gst_percentage) + '%'
                : '-';
            const calcTxt = found.calculation_type || '-';
            const boxMode = isBoxCalc(calcTxt);
            const boxesRaw = $('#dispute_boxes_count').val();
            const boxes = parseInt(boxesRaw, 10);
            const boxesValid = boxMode ? (Number.isInteger(boxes) && boxes >= 1) : true;
            // For Weight disputes the custom amount replaces the rule rate.
            const weightMode = isCustomAmount($('#dispute_charge_type').val(), found.values);
            const customAmt = parseFloat($('#dispute_custom_amount').val());
            const customValid = !weightMode || (!isNaN(customAmt) && customAmt > 0);
            const ruleRate = parseDisputeRate(found.values);
            const rate = (weightMode && customValid) ? customAmt : (weightMode ? NaN : ruleRate);
            const curr = /^\s*\$/.test(String(found.values || '')) ? '$' : 'Rs ';
            const fmtMoney = function (n) {
                if (isNaN(n)) return '-';
                const rounded = Math.round(n * 100) / 100;
                return curr + (Number.isInteger(rounded)
                    ? rounded.toLocaleString('en-IN')
                    : rounded.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            };

            let boxRow = '';
            let extraRows = '';
            let bandSub = 'GST ' + gstTxt + ' applicable';
            let bandAmount = found.values || '-';
            let customRow = '';
            if (weightMode) {
                customRow = '<div class="dispute-kv"><span class="k"><i class="ti ti-currency-rupee"></i>Custom Amount</span><span class="v">' + (customValid ? fmtMoney(customAmt) : '—') + '</span></div>';
            }

            if (boxMode) {
                const boxesLabel = boxesValid ? String(boxes) + (boxes === 1 ? ' box' : ' boxes') : '—';
                boxRow = '<div class="dispute-kv"><span class="k"><i class="ti ti-package"></i>Boxes</span><span class="v">' + escDispute(boxesLabel) + '</span></div>';
                if (boxesValid && !isNaN(rate)) {
                    const base = rate * boxes;
                    const gstAmt = base * gstPct / 100;
                    const totalIncl = base + gstAmt;
                    bandSub = fmtMoney(rate) + ' × ' + boxes + ' + ' + escDispute(gstTxt) + ' GST incl.';
                    bandAmount = fmtMoney(totalIncl);
                    extraRows =
                        '<div class="dispute-kv"><span class="k"><i class="ti ti-wallet"></i>Base (' + boxes + ' × ' + fmtMoney(rate) + ')</span><span class="v">' + fmtMoney(base) + '</span></div>' +
                        '<div class="dispute-kv"><span class="k"><i class="ti ti-percent"></i>GST (' + escDispute(gstTxt) + ')</span><span class="v">+' + fmtMoney(gstAmt) + '</span></div>';
                } else if (boxesValid && isNaN(rate)) {
                    bandSub = boxes + ' boxes &nbsp;•&nbsp; GST ' + escDispute(gstTxt) + ' included on billing (custom rate)';
                    bandAmount = escDispute(found.values);
                } else if (!isNaN(rate)) {
                    const perBoxIncl = rate * (1 + gstPct / 100);
                    bandSub = fmtMoney(rate) + ' + ' + escDispute(gstTxt) + ' GST = ' + fmtMoney(perBoxIncl) + ' /box incl.';
                    bandAmount = fmtMoney(perBoxIncl) + ' /box';
                } else {
                    bandSub = 'Enter the number of boxes to see the GST-inclusive total here';
                    bandAmount = escDispute(found.values) + ' /box';
                }
            } else if (!isNaN(rate)) {
                // flat (per-shipment) — GST included total
                const totalIncl = rate * (1 + gstPct / 100);
                const gstAmt = totalIncl - rate;
                bandSub = fmtMoney(rate) + ' + ' + escDispute(gstTxt) + ' GST incl.';
                bandAmount = fmtMoney(totalIncl);
                if (gstPct > 0) {
                    extraRows =
                        '<div class="dispute-kv"><span class="k"><i class="ti ti-wallet"></i>Base</span><span class="v">' + fmtMoney(rate) + '</span></div>' +
                        '<div class="dispute-kv"><span class="k"><i class="ti ti-percent"></i>GST (' + escDispute(gstTxt) + ')</span><span class="v">+' + fmtMoney(gstAmt) + '</span></div>';
                }
            }

            // Until a valid custom amount is entered for a Weight dispute, prompt for it.
            if (weightMode && !customValid) {
                bandSub = 'Enter a custom amount to see the GST-inclusive total here';
                bandAmount = '—';
                extraRows = '';
            }
            const readyLabel = !boxesValid ? 'Need boxes' : (!customValid ? 'Need amount' : 'Ready');
            // Hide the Condition row when conditions are null.
            const condTxt = (found.conditions || '').trim();
            const condRow = condTxt
                ? '<div class="dispute-kv"><span class="k"><i class="ti ti-file-text"></i>Condition</span><span class="v">' + escDispute(condTxt) + '</span></div>'
                : '';

            $detail.html(
                '<div class="dispute-summary">' +
                    '<div class="dispute-summary-head">' +
                        '<div class="s-ic"><i class="ti ti-receipt"></i></div>' +
                        '<div><strong>' + escDispute(found.additional_charges) + '</strong>' +
                        '<small>Rule #' + escDispute(found.id) + ' &bull; ' + escDispute(found.place_of_apply || 'weighing at first scan') + '</small></div>' +
                        '<span class="s-badge">' + readyLabel + '</span>' +
                    '</div>' +
                    '<div class="dispute-summary-grid">' +
                        condRow +
                        '<div class="dispute-kv"><span class="k"><i class="ti ti-map-pin"></i>Destination</span><span class="v">' + escDispute(found.destination) + '</span></div>' +
                        '<div class="dispute-kv"><span class="k"><i class="ti ti-truck"></i>Service</span><span class="v">' + escDispute(found.service_id) + '</span></div>' +
                        '<div class="dispute-kv"><span class="k"><i class="ti ti-calculator"></i>Calculation</span><span class="v">' + escDispute(calcTxt) + '</span></div>' +
                        boxRow +
                        customRow +
                        '<div class="dispute-kv"><span class="k"><i class="ti ti-percent"></i>GST</span><span class="v">' + escDispute(gstTxt) + '</span></div>' +
                        extraRows +
                    '</div>' +
                    '<div class="dispute-value-band">' +
                        '<div><div class="vb-label">' + (boxMode ? 'Total Charge (incl. GST)' : 'Charge Value (incl. GST)') + '</div><div class="vb-sub">' + bandSub + '</div></div>' +
                        '<div class="vb-amount">' + bandAmount + '</div>' +
                    '</div>' +
                '</div>'
            ).hide().fadeIn(200);
            $apply.prop('disabled', !(boxesValid && customValid));
        }

        // Condition select karne par boxes field (flat/box) + summary dikhao.
        $(document).on('change', '#dispute_condition_id', function () {
            const selectedId = $(this).val();
            const found = disputeChargesCache.find(function (c) { return String(c.id) === String(selectedId); });
            if (!found) {
                $('#dispute_boxes_wrap').addClass('d-none');
                $('#dispute_custom_amount').val('');
                $('#dispute_custom_wrap').addClass('d-none');
                renderDisputeSummary();
                return;
            }
            if (isBoxCalc(found.calculation_type)) {
                $('#dispute_boxes_count').val('');
                $('#dispute_boxes_wrap').removeClass('d-none').hide().slideDown(180);
            } else {
                $('#dispute_boxes_count').val('');
                $('#dispute_boxes_wrap').addClass('d-none');
            }
            // Show the field for custom-amount rules (Weight type or "Custom" in values).
            if (isCustomAmount($('#dispute_charge_type').val(), found.values)) {
                $('#dispute_custom_wrap').removeClass('d-none').hide().slideDown(180);
            } else {
                $('#dispute_custom_amount').val('');
                $('#dispute_custom_wrap').addClass('d-none');
            }
            renderDisputeSummary();
        });

        // Boxes type karne par total live update.
        $(document).on('input', '#dispute_boxes_count', function () {
            let v = parseInt($(this).val(), 10);
            if ($(this).val() !== '' && (isNaN(v) || v < 1)) {
                $(this).val(1);
            }
            renderDisputeSummary();
        });

        // Custom amount type karne par total live update.
        $(document).on('input', '#dispute_custom_amount', function () {
            let v = parseFloat($(this).val());
            if ($(this).val() !== '' && (isNaN(v) || v <= 0)) {
                $(this).val('');
            }
            renderDisputeSummary();
        });

        // Small safe notifier (the global showAlert is not available on every page).
        function disputeNotify(msg, type) {
            try {
                if (typeof showAlert === 'function') { showAlert(msg, type || 'success'); return; }
            } catch (e) {}
            if (window.Swal) {
                Swal.fire({ icon: type === 'error' ? 'error' : 'success', title: msg, timer: 2600, showConfirmButton: false });
            } else { alert(msg); }
        }

        // Apply button — save to backend, then it appears on the Dispute Orders page.
        $(document).on('click', '#dispute_apply_btn', function () {
            const condId = $('#dispute_condition_id').val();
            const type = $('#dispute_charge_type').val();
            if (!type || !condId) return;
            const found = disputeChargesCache.find(function (c) { return String(c.id) === String(condId); });
            let boxesTxt = '';
            let boxesVal = null;
            // Custom amount is mandatory for Weight disputes.
            const weightMode = isCustomAmount(type, found ? found.values : '');
            let customVal = null;
            if (weightMode) {
                const c = parseFloat($('#dispute_custom_amount').val());
                if (isNaN(c) || c <= 0) {
                    disputeNotify('Please enter custom amount for this dispute.', 'error');
                    $('#dispute_custom_amount').focus();
                    return;
                }
                customVal = Math.round(c * 100) / 100;
            }
            if (found && isBoxCalc(found.calculation_type)) {
                const b = parseInt($('#dispute_boxes_count').val(), 10);
                if (!Number.isInteger(b) || b < 1) {
                    disputeNotify('Please enter number of boxes (min 1).', 'error');
                    $('#dispute_boxes_count').focus();
                    return;
                }
                boxesVal = b;
                const rate = (weightMode && customVal !== null) ? customVal : parseDisputeRate(found.values);
                const gstP = parseFloat(found.gst_percentage) || 0;
                const curr = /^\s*\$/.test(String(found.values || '')) ? '$' : 'Rs ';
                const fmtA = function (n) {
                    const r = Math.round(n * 100) / 100;
                    return curr + (Number.isInteger(r) ? r.toLocaleString('en-IN') : r.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                };
                boxesTxt = ' × ' + b + ' boxes';
                if (!isNaN(rate)) {
                    boxesTxt += ' = ' + fmtA(rate * b * (1 + gstP / 100)) + ' incl. GST';
                }
            } else if (found) {
                const rate0 = (weightMode && customVal !== null) ? customVal : parseDisputeRate(found.values);
                const gstP0 = parseFloat(found.gst_percentage) || 0;
                if (!isNaN(rate0) && gstP0 > 0) {
                    const curr0 = /^\s*\$/.test(String(found.values || '')) ? '$' : 'Rs ';
                    const tot0 = rate0 * (1 + gstP0 / 100);
                    const r0 = Math.round(tot0 * 100) / 100;
                    boxesTxt = ' = ' + curr0 + (Number.isInteger(r0) ? r0.toLocaleString('en-IN') : r0.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })) + ' incl. GST';
                }
            }
            let label = found ? (found.additional_charges + ' — ' + found.values + boxesTxt) : type;
            if (found && weightMode && customVal !== null) {
                const currLbl = /^\s*\$/.test(String(found.values || '')) ? '$' : 'Rs ';
                label = found.additional_charges + ' — Custom ' + currLbl + customVal.toLocaleString('en-IN') + boxesTxt;
            }
            const awb = $('#dispute_awb_display').text();

            const doSave = function () {
                const $btn = $('#dispute_apply_btn');
                const orig = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Applying...');
                $.ajax({
                    url: '{{ route("admin.apply-dispute-charge") }}',
                    type: 'POST',
                    data: {
                        shipment_id: disputeCurrentShipment,
                        dispute_charge_id: condId,
                        boxes: boxesVal,
                        custom_amount: customVal
                    },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        $btn.prop('disabled', false).html(orig);
                        if (response && response.success) {
                            $('#disputeChargeModal').modal('hide');
                            const viewUrl = '{{ route("admin.dispute-orders") }}';
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dispute charge applied!',
                                    html: '<b>' + escDispute(label) + '</b><br><span style="font-size:13px;color:#6b7280;">HAWB: ' + escDispute(awb) + '</span><br><span class="badge mt-2" style="background:#f59e0b;color:#fff;">Shipment marked as Ready to Dispatch</span>',
                                    showCancelButton: true,
                                    confirmButtonText: 'View Dispute Orders',
                                    cancelButtonText: 'Stay here',
                                    confirmButtonColor: '#dc2626',
                                    reverseButtons: true
                                }).then(function (r) {
                                    if (r.isConfirmed) { window.location.href = viewUrl; }
                                });
                            } else {
                                disputeNotify('Dispute charge applied! (HAWB ' + awb + ')', 'success');
                            }
                        } else {
                            disputeNotify((response && response.message) || 'Could not apply charge.', 'error');
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(orig);
                        let msg = 'Could not apply charge. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        disputeNotify(msg, 'error');
                    }
                });
            };

            if (window.Swal) {
                Swal.fire({
                    title: 'Apply this charge?',
                    html: '<b>' + escDispute(label) + '</b><br><span style="font-size:13px;color:#6b7280;">HAWB: ' + escDispute(awb) + '</span>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, apply it',
                    cancelButtonText: 'Review again',
                    confirmButtonColor: '#dc2626',
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed) { doSave(); }
                });
            } else if (confirm('Apply this charge? ' + label)) {
                doSave();
            }
        });

        /**
         * Print shipping label - fetches base64 PDF from server and displays in modal.
         * @param {number} shipmentId - The shipment_invoice ID
         */
        function printLabel(shipmentId) {
            // Reset modal states
            $('#printLabelLoading').removeClass('d-none');
            $('#printLabelError').addClass('d-none');
            $('#printLabelPdfFrame').css('display', 'none');
            $('#printLabelPrintBtn').addClass('d-none');

            // Open modal
            $('#printLabelModal').modal('show');

            // AJAX call to generate label PDF
            $.ajax({
                // url: '/admin/generate-label',
                url: '{{ route("admin.generate-label") }}',
                type: 'POST',
                data: { shipment_id: shipmentId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.pdf_base64) {
                        // Convert base64 to PDF blob
                        const binaryString = atob(response.pdf_base64);
                        const bytes = new Uint8Array(binaryString.length);
                        for (let i = 0; i < binaryString.length; i++) {
                            bytes[i] = binaryString.charCodeAt(i);
                        }
                        const pdfBlob = new Blob([bytes], { type: 'application/pdf' });
                        const blobUrl = URL.createObjectURL(pdfBlob);

                        // Hide loading, show iframe with PDF
                        $('#printLabelLoading').addClass('d-none');
                        $('#printLabelPdfFrame').attr('src', blobUrl).css('display', 'block');
                        $('#printLabelPrintBtn').removeClass('d-none');

                        // Store blob URL for printing
                        window._labelPdfBlobUrl = blobUrl;
                    } else {
                        // Show error
                        $('#printLabelLoading').addClass('d-none');
                        $('#printLabelError').removeClass('d-none');
                        $('#printLabelErrorMsg').text(response.message || 'Failed to generate label.');
                    }
                },
                error: function(xhr) {
                    // Show error
                    $('#printLabelLoading').addClass('d-none');
                    $('#printLabelError').removeClass('d-none');
                    let errorMsg = 'Failed to generate label. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#printLabelErrorMsg').text(errorMsg);
                }
            });
        }

        /**
         * Trigger browser print for the PDF label.
         * Opens the PDF blob URL in a new window and triggers print.
         */
        function triggerPdfPrint() {
            if (window._labelPdfBlobUrl) {
                const printWindow = window.open(window._labelPdfBlobUrl, '_blank');
                if (printWindow) {
                    printWindow.onload = function() {
                        setTimeout(function() {
                            printWindow.print();
                        }, 500);
                    };
                } else {
                    showAlert('Please allow popups to print the label.', 'warning');
                }
            }
        }

        /**
         * Mark a shipment as Ready to Dispatch.
         * Creates a tracking record with status 'ready_to_dispatch' and moves
         * the shipment from Print Label tab to Ready to Dispatch tab.
         * @param {number} shipmentId - The shipment_invoice ID
         */
        function markReadyToDispatch(shipmentId) {
            const $btn = $(event.currentTarget);

            const askConfirm = function () {
                if (window.Swal) {
                    Swal.fire({
                        title: 'Mark as Ready to Dispatch?',
                        text: 'Are you sure you want to mark this shipment as Ready to Dispatch?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, mark it',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#405189',
                        reverseButtons: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            doMarkReady($btn, shipmentId);
                        }
                    });
                } else if (confirm('Are you sure you want to mark this shipment as Ready to Dispatch?')) {
                    doMarkReady($btn, shipmentId);
                }
            };

            if (window.Swal) {
                askConfirm();
            } else {
                // SweetAlert2 on-demand load (global alerts wala CDN); fail ho to native confirm.
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                s.onload = askConfirm;
                s.onerror = askConfirm;
                document.head.appendChild(s);
            }
        }

        function doMarkReady($btn, shipmentId) {
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

            $.ajax({
                url: '{{ route("admin.ready-to-dispatch") }}',
                type: 'POST',
                data: { shipment_id: shipmentId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success', function() {
                            // Reload page to reflect changes (shipment moves to Ready to Dispatch tab)
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        });
                    } else {
                        showAlert(response.message || 'Something went wrong.', 'error');
                        $btn.prop('disabled', false).html('<i class="ti ti-truck me-1"></i> Ready to Dispatch');
                    }
                },
                error: function(xhr) {
                    let msg = 'An error occurred. Please try again.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                    }
                    showAlert(msg, 'error');
                    $btn.prop('disabled', false).html('<i class="ti ti-truck me-1"></i> Ready to Dispatch');
                }
            });
        }
    </script>

</body>

</html>
