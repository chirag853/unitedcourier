<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Manifest Details | United Courier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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
        :root {
            --md-primary: #2f66f3;
            --md-primary-dark: #1e3f8f;
            --md-ink: #0f172a;
            --md-muted: #64748b;
            --md-border: #e6eaf2;
            --md-danger: #e11d48;
            --md-success: #059669;
            --md-grad: linear-gradient(135deg, #1e3f8f 0%, #2f66f3 55%, #4f8bff 100%);
        }

        .page-wrapper .content {
            padding: 1rem !important;
        }

        /* ===== Hero Banner ===== */
        .md-hero {
            position: relative;
            background: var(--md-grad);
            border-radius: 22px;
            padding: 30px 32px;
            color: #fff;
            overflow: hidden;
            box-shadow: 0 18px 40px -12px rgba(47, 102, 243, .45);
            margin-bottom: 20px;
        }

        .md-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 85% 15%, rgba(255, 255, 255, .14) 0%, transparent 42%),
                radial-gradient(circle at 10% 110%, rgba(255, 255, 255, .10) 0%, transparent 48%);
            pointer-events: none;
        }

        .md-hero::after {
            content: '';
            position: absolute;
            right: -70px;
            top: -70px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            border: 1.5px dashed rgba(255, 255, 255, .25);
            animation: md-float 9s linear infinite;
        }

        @keyframes md-float {
            to { transform: rotate(360deg); }
        }

        .md-hero-row {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .md-hero-left {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .md-hero-icon {
            width: 62px;
            height: 62px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .28);
            display: grid;
            place-items: center;
            font-size: 30px;
            flex-shrink: 0;
            backdrop-filter: blur(4px);
        }

        .md-hero-title {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: rgba(255, 255, 255, .78);
            margin-bottom: 4px;
        }

        .md-hero-code {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: .6px;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .md-hero-code .copy-btn {
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .3);
            color: #fff;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 15px;
            transition: all .2s ease;
            cursor: pointer;
            line-height: 1;
        }

        .md-hero-code .copy-btn:hover {
            background: rgba(255, 255, 255, .3);
        }

        .md-hero-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: rgba(255, 255, 255, .85);
            margin-top: 8px;
            flex-wrap: wrap;
        }

        .md-hero-meta .sep {
            opacity: .5;
        }

        .md-hero-right {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .md-hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 999px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            backdrop-filter: blur(4px);
        }

        .md-hero-chip .chip-value {
            font-weight: 800;
            font-size: 15px;
        }

        .md-btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #fff;
            color: var(--md-primary-dark);
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
            padding: 9px 18px;
            box-shadow: 0 8px 20px -6px rgba(0, 0, 0, .25);
            transition: all .2s ease;
            text-decoration: none;
        }

        .md-btn-ghost:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, .3);
            color: var(--md-primary-dark);
        }

        /* ===== Stat Cards ===== */
        .md-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .md-stat {
            background: #fff;
            border: 1px solid var(--md-border);
            border-radius: 18px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .04);
            transition: all .25s ease;
        }

        .md-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(15, 23, 42, .09);
            border-color: #d4defb;
        }

        .md-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .md-stat-icon.blue { background: #eaf0fe; color: var(--md-primary); }
        .md-stat-icon.green { background: #e7f8f1; color: var(--md-success); }
        .md-stat-icon.amber { background: #fef3e2; color: #d97706; }
        .md-stat-icon.purple { background: #f1eafe; color: #7c3aed; }
        .md-stat-icon.rose { background: #fee9ef; color: var(--md-danger); }

        .md-stat-label {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--md-muted);
            margin-bottom: 3px;
        }

        .md-stat-value {
            font-size: 19px;
            font-weight: 800;
            color: var(--md-ink);
            line-height: 1.2;
        }

        .md-stat-sub {
            font-size: 12px;
            color: var(--md-muted);
            font-weight: 500;
        }

        /* ===== Table Card ===== */
        .md-card {
            background: #fff;
            border: 1px solid var(--md-border);
            border-radius: 20px;
            box-shadow: 0 6px 24px rgba(15, 23, 42, .05);
            overflow: hidden;
        }

        .md-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 18px 22px;
            border-bottom: 1px solid var(--md-border);
            background: linear-gradient(180deg, #fbfcff, #fff);
        }

        .md-card-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--md-ink);
            display: inline-flex;
            align-items: center;
            gap: 9px;
        }

        .md-count-badge {
            background: var(--md-grad);
            color: #fff;
            font-size: 11.5px;
            font-weight: 700;
            border-radius: 999px;
            padding: 3px 11px;
            min-width: 28px;
            text-align: center;
        }

        .md-card-tools {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .md-search {
            position: relative;
        }

        .md-search .ti {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 15px;
            color: var(--md-muted);
        }

        .md-search input {
            border: 1px solid var(--md-border);
            border-radius: 10px;
            padding: 8px 14px 8px 36px;
            font-size: 13px;
            width: 230px;
            background: #f8fafc;
            color: var(--md-ink);
            outline: none;
            transition: all .2s ease;
        }

        .md-search input:focus {
            background: #fff;
            border-color: var(--md-primary);
            box-shadow: 0 0 0 3px rgba(47, 102, 243, .12);
        }

        .md-table-wrap {
            overflow-x: auto;
        }

        .md-table {
            width: 100%;
            border-collapse: collapse;
        }

        .md-table thead th {
            background: #f4f6fb;
            color: #44506a;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .7px;
            padding: 13px 16px;
            border-bottom: 1px solid var(--md-border);
            white-space: nowrap;
        }

        .md-table tbody td {
            padding: 13px 16px;
            border-bottom: 1px solid #eef1f7;
            font-size: 13px;
            color: #1f2a44;
            vertical-align: middle;
        }

        .md-table tbody tr {
            transition: background .15s ease;
        }

        .md-table tbody tr:hover {
            background: #f8faff;
        }

        .md-table tbody tr:last-child td {
            border-bottom: none;
        }

        .md-awb {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #0f172a;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .4px;
            padding: 5px 11px;
            border-radius: 8px;
            font-family: 'Consolas', 'Courier New', monospace;
            white-space: nowrap;
        }

        .md-route {
            display: flex;
            flex-direction: column;
            gap: 2px;
            font-size: 12.5px;
        }

        .md-route .to {
            color: var(--md-muted);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .md-route .to .ti {
            color: var(--md-primary);
            font-size: 13px;
        }

        .md-amount {
            font-weight: 800;
            color: var(--md-primary-dark);
            white-space: nowrap;
        }

        .md-cell-sub {
            display: block;
            font-size: 11.5px;
            color: var(--md-muted);
            font-weight: 500;
            margin-top: 2px;
        }

        .md-customer {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 170px;
        }

        .md-customer .md-customer-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 13px;
        }

        .md-customer .md-cell-sub {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            margin-top: 1px;
        }

        .md-customer .md-cell-sub .ti {
            font-size: 12px;
            color: var(--md-primary);
        }

        .md-order-date {
            white-space: nowrap;
        }

        .md-order-date .md-order-date-main {
            font-weight: 600;
            color: #1e293b;
            font-size: 12.5px;
        }

        .md-package {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 110px;
        }

        .md-package .md-package-weight {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 700;
            color: #0f172a;
            font-size: 13px;
            white-space: nowrap;
        }

        .md-package .md-package-weight .ti {
            font-size: 15px;
            color: var(--md-primary);
        }

        .md-package .md-package-weight .md-cell-sub {
            display: inline;
            font-size: 11px;
            margin-top: 0;
        }

        .md-csb-badge {
            display: inline-flex;
            align-items: center;
            align-self: flex-start;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: .4px;
            padding: 3px 9px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .md-csb-badge.csb5 {
            background: #eef2ff;
            color: #4f46e5;
            border: 1px solid #c7d2fe;
        }

        .md-csb-badge.csb4 {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
        }

        .md-address {
            display: block;
            font-size: 12px;
            line-height: 1.45;
            color: #334155;
            max-width: 300px;
        }

        .md-remove-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1px solid #fecdd3;
            color: var(--md-danger);
            font-size: 12px;
            font-weight: 700;
            border-radius: 9px;
            padding: 6px 13px;
            transition: all .2s ease;
            white-space: nowrap;
            cursor: pointer;
        }

        .md-remove-btn:hover {
            background: var(--md-danger);
            border-color: var(--md-danger);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px -4px rgba(225, 29, 72, .45);
        }

        .md-remove-btn:disabled {
            opacity: .65;
            cursor: not-allowed;
            transform: none;
        }

        .md-close-manifest-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--md-ink);
            border: none;
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            border-radius: 12px;
            padding: 9px 18px;
            box-shadow: 0 8px 20px -6px rgba(0, 0, 0, .3);
            transition: all .2s ease;
            cursor: pointer;
            white-space: nowrap;
        }

        .md-close-manifest-btn:hover {
            background: #0b1120;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, .35);
        }

        .md-close-manifest-btn:disabled {
            opacity: .65;
            cursor: not-allowed;
            transform: none;
        }

        /* ===== Empty State ===== */
        .md-empty {
            text-align: center;
            padding: 56px 20px;
            color: var(--md-muted);
        }

        .md-empty-icon {
            width: 76px;
            height: 76px;
            margin: 0 auto 16px;
            border-radius: 24px;
            background: linear-gradient(135deg, #eef2ff, #f8faff);
            display: grid;
            place-items: center;
            font-size: 34px;
            color: var(--md-primary);
        }

        /* ===== Toast ===== */
        .md-toast-wrap {
            position: fixed;
            top: 22px;
            right: 22px;
            z-index: 1080;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .md-toast {
            pointer-events: auto;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 320px;
            max-width: 400px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 16px 40px -8px rgba(15, 23, 42, .22);
            border: 1px solid var(--md-border);
            padding: 14px 16px;
            animation: md-toast-in .3s ease;
        }

        @keyframes md-toast-in {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .md-toast .toast-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .md-toast.success .toast-icon { background: #e7f8f1; color: var(--md-success); }
        .md-toast.error .toast-icon { background: #fee9ef; color: var(--md-danger); }

        .md-toast .toast-body-custom {
            flex: 1;
            font-size: 13px;
            color: var(--md-ink);
            line-height: 1.45;
            padding-top: 1px;
        }

        .md-toast .toast-body-custom strong {
            display: block;
            font-size: 13.5px;
            margin-bottom: 1px;
        }

        .md-toast .toast-close {
            border: none;
            background: none;
            color: #94a3b8;
            font-size: 16px;
            padding: 2px;
            line-height: 1;
            cursor: pointer;
        }

        .md-toast.out {
            animation: md-toast-out .3s ease forwards;
        }

        @keyframes md-toast-out {
            to { opacity: 0; transform: translateX(30px); }
        }

        /* ===== Confirm Modal ===== */
        .md-modal .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 30px 60px -12px rgba(15, 23, 42, .35);
            overflow: hidden;
        }

        .md-modal-icon {
            width: 72px;
            height: 72px;
            margin: 6px auto 4px;
            border-radius: 22px;
            background: #fee9ef;
            color: var(--md-danger);
            display: grid;
            place-items: center;
            font-size: 34px;
            animation: md-pop .35s ease;
        }

        @keyframes md-pop {
            0% { transform: scale(.6); opacity: 0; }
            60% { transform: scale(1.08); }
            100% { transform: scale(1); opacity: 1; }
        }

        .md-modal .modal-title {
            font-weight: 800;
            color: var(--md-ink);
        }

        .md-modal-body-text {
            font-size: 13.5px;
            color: var(--md-muted);
            line-height: 1.6;
        }

        .md-awb-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: var(--md-ink);
            font-weight: 700;
            font-size: 12.5px;
            border-radius: 8px;
            padding: 5px 11px;
            font-family: 'Consolas', 'Courier New', monospace;
            margin: 0 2px;
        }

        .md-btn-confirm {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--md-danger);
            border: none;
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            border-radius: 10px;
            padding: 9px 18px;
            transition: all .2s ease;
        }

        .md-btn-confirm:hover {
            background: #be123c;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px -5px rgba(225, 29, 72, .5);
        }

        .md-btn-cancel {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 700;
            font-size: 13px;
            border-radius: 10px;
            padding: 9px 18px;
            transition: all .2s ease;
        }

        .md-btn-cancel:hover {
            background: #e2e8f0;
            color: var(--md-ink);
        }

        /* ===== Pickup Date Options (Assign for Pickup modal) ===== */
        .md-pickup-date-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 8px;
        }

        .md-pickup-date-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .md-pickup-date-option {
            flex: 1 1 90px;
            min-width: 90px;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            text-align: center;
            cursor: pointer;
            transition: all .2s ease;
        }

        .md-pickup-date-option:hover {
            border-color: var(--md-primary);
            background: #eaf0fe;
        }

        .md-pickup-date-option.selected {
            border-color: var(--md-primary);
            background: #eaf0fe;
            box-shadow: 0 4px 12px rgba(47, 102, 243, .18);
        }

        .md-pickup-date-option .dp-label {
            display: block;
            font-weight: 700;
            font-size: 12px;
            color: #1e293b;
        }

        .md-pickup-date-option.selected .dp-label {
            color: var(--md-primary);
        }

        .md-pickup-date-option .dp-date {
            display: block;
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ===== View (Eye) Button ===== */
        .md-view-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 36px;
            min-width: 40px;
            padding: 0 12px;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            color: #4f46e5;
            font-weight: 700;
            font-size: 13px;
            border-radius: 10px;
            transition: all .2s ease;
            vertical-align: middle;
            margin-right: 6px;
        }

        .md-view-btn:hover {
            background: #4f46e5;
            border-color: #4f46e5;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, .3);
        }

        .md-view-btn i {
            font-size: 17px;
        }

        /* ===== Shipment Detail Modal ===== */
        .md-modal .modal-content {
            border-radius: 18px;
            border: none;
            overflow: hidden;
        }

        .md-modal .modal-header {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: #fff;
            padding: 16px 20px;
        }

        .md-modal .modal-header .modal-title {
            font-weight: 700;
            font-size: 16px;
        }

        .md-modal .modal-header .btn-close {
            filter: invert(1);
            opacity: .8;
        }

        #mdShipmentDetailModal .modal-body {
            padding: 20px;
            max-height: 68vh;
            overflow-y: auto;
            background: #f8fafc;
        }

        .md-detail-section {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 12px;
        }

        .md-detail-section h6 {
            color: #4f46e5;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .md-detail-row-total {
            background: #eef2ff;
            border-radius: 10px;
            padding: 10px 12px;
            margin: 6px 0;
            border-left: 3px solid #4f46e5;
        }

        .md-detail-row-total .label {
            font-weight: 600;
            color: #4f46e5;
        }

        .md-detail-row-total .value {
            font-weight: 700;
            color: #4f46e5;
        }

        .md-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 4px 0;
            font-size: 13px;
            border-bottom: 1px dashed #eef2f7;
        }

        .md-detail-row:last-child {
            border-bottom: none;
        }

        .md-detail-row .label {
            color: #64748b;
            min-width: 130px;
            flex-shrink: 0;
            font-weight: 500;
        }

        .md-detail-row .value {
            color: #0f172a;
            font-weight: 600;
            text-align: right;
            flex: 1;
            word-break: break-word;
        }

        .md-tracking-box {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #fff;
            padding: 14px 18px;
            border-radius: 14px;
            text-align: center;
            margin-bottom: 14px;
            box-shadow: 0 6px 18px rgba(79, 70, 229, .3);
        }

        .md-tracking-box .tracking-label {
            font-size: 12px;
            opacity: .9;
            margin-bottom: 4px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .md-tracking-box .tracking-value {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .md-route-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }

        .md-route-box .route-point {
            text-align: center;
            flex: 1;
        }

        .md-route-box .route-point .route-city {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .md-route-box .route-point .route-label {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
            letter-spacing: .5px;
        }

        .md-route-box .route-arrow {
            font-size: 20px;
            color: #4f46e5;
            flex-shrink: 0;
        }

        #mdShipmentDetailModal .modal-footer {
            background: #fff;
            border-top: 1px solid #eef2f7;
            padding: 12px 20px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 767.98px) {
            .md-hero {
                padding: 22px 20px;
            }

            .md-hero-code {
                font-size: 20px;
            }

            .md-hero-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
                border-radius: 14px;
            }

            .md-search input {
                width: 100%;
            }

            .md-card-tools {
                width: 100%;
            }

            .md-search {
                flex: 1;
            }
        }
    </style>
</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar Start -->
        @if(!empty($isAdminView))
            @include('admin.partials.header')
        @else
            @include('customer.partials.customer_dashboard_header')
        @endif
        <!-- Topbar End -->

        <!-- Sidenav Menu Start -->
        @if(!empty($isAdminView))
            @include('admin.partials.sidebar')
        @else
            @include('customer.partials.sidebar')
        @endif
        <!-- Sidenav Menu End -->

        <!-- ========================
            Start Page Content
        ========================= -->
        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content pb-0">

                <!-- Hero Banner -->
                <div class="md-hero">
                    <div class="md-hero-row">
                        <div class="md-hero-left">
                            <div class="md-hero-icon">
                                <i class="ti ti-package"></i>
                            </div>
                            <div>
                                <div class="md-hero-title">Manifest Details</div>
                                <div class="md-hero-code">
                                    <span>{{ $manifest->manifest_number ?? 'N/A' }}</span>
                                    <button type="button" class="copy-btn" id="mdCopyManifest" title="Copy Manifest Number">
                                        <i class="ti ti-copy"></i>
                                    </button>
                                </div>
                                <div class="md-hero-meta">
                                    <span><i class="ti ti-calendar me-1"></i>
                                        @if($manifest->manifest_created_at)
                                            {{ \Carbon\Carbon::parse($manifest->manifest_created_at)->format('d-m-Y') }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                    <span class="sep">|</span>
                                    <span><i class="ti ti-clock me-1"></i>
                                        @if($manifest->manifest_created_at)
                                            {{ \Carbon\Carbon::parse($manifest->manifest_created_at)->format('h:i A') }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="md-hero-right">
                            <span class="md-hero-chip">
                                <i class="ti ti-user"></i>
                                <span>{{ $manifest->customer_name ?: 'Customer' }}</span>
                            </span>
                            <span class="md-hero-chip">
                                <i class="ti ti-box"></i>
                                <span><span class="chip-value">{{ $manifest->shipment_count }}</span>&nbsp;Shipments</span>
                            </span>
                            @php
                                $mdManifestStatus = (int) ($manifest->status ?? \App\Models\Manifest::STATUS_OPEN);
                                // When opened from admin (admin/companies "Ready for Pickup"), use the
                                // admin routes so the label/document open for an admin session.
                                $mdLabelRoute = !empty($isAdminView) ? 'admin.manifest-label' : 'customer.manifest-label';
                                $mdDocumentRoute = !empty($isAdminView) ? 'admin.manifest-document' : 'customer.manifest-document';
                            @endphp
                            @if($mdManifestStatus === \App\Models\Manifest::STATUS_OPEN && empty($isAdminView))
                                <button type="button" class="md-close-manifest-btn" id="mdCloseManifestBtn" title="Close this manifest">
                                    <i class="ti ti-lock"></i> Close Manifest
                                </button>
                            @elseif(in_array($mdManifestStatus, [\App\Models\Manifest::STATUS_CLOSE, \App\Models\Manifest::STATUS_PICKUP], true))
                                <a href="{{ route($mdLabelRoute, ['manifestNumber' => $manifest->manifest_number]) }}" target="_blank" class="md-close-manifest-btn" title="Print manifest label" style="background:#0a7d33;">
                                    <i class="ti ti-printer"></i> Manifest Label
                                </a>
                                <a href="{{ route($mdDocumentRoute, ['manifestNumber' => $manifest->manifest_number]) }}" target="_blank" class="md-close-manifest-btn" title="Download manifest document" style="background:#1d4ed8;">
                                    <i class="ti ti-file-text"></i> Manifest Document
                                </a>
                                @if($mdManifestStatus === \App\Models\Manifest::STATUS_CLOSE && empty($isAdminView))
                                    <button type="button" class="md-close-manifest-btn" id="mdAssignPickupBtn" title="Assign this manifest for pickup" style="background:var(--md-primary);">
                                        <i class="ti ti-truck"></i> Assign for Pickup
                                    </button>
                                @endif
                            @endif
                            <a href="javascript:window.close();" class="md-btn-ghost">
                                <i class="ti ti-x"></i> Close
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="md-stats">
                    <div class="md-stat">
                        <div class="md-stat-icon blue"><i class="ti ti-truck"></i></div>
                        <div>
                            <div class="md-stat-label">Shipments</div>
                            <div class="md-stat-value">{{ $manifest->shipment_count }}</div>
                            <div class="md-stat-sub">in this manifest</div>
                        </div>
                    </div>
                    <div class="md-stat">
                        <div class="md-stat-icon green"><i class="ti ti-coin"></i></div>
                        <div>
                            <div class="md-stat-label">Total Value</div>
                            <div class="md-stat-value">{{ number_format($manifest->total_value, 2) }}{{ $manifest->currency ? ' ' . $manifest->currency : '' }}</div>
                            <div class="md-stat-sub">declared shipment value</div>
                        </div>
                    </div>
                    <div class="md-stat">
                        <div class="md-stat-icon amber"><i class="ti ti-calendar-event"></i></div>
                        <div>
                            <div class="md-stat-label">Manifest Date</div>
                            <div class="md-stat-value" style="font-size:15px;">
                                @if($manifest->manifest_created_at)
                                    {{ \Carbon\Carbon::parse($manifest->manifest_created_at)->format('d-m-Y') }}
                                @else
                                    -
                                @endif
                            </div>
                            <div class="md-stat-sub">
                                @if($manifest->manifest_created_at)
                                    {{ \Carbon\Carbon::parse($manifest->manifest_created_at)->format('h:i A') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="md-stat">
                        <div class="md-stat-icon purple"><i class="ti ti-user-circle"></i></div>
                        <div>
                            <div class="md-stat-label">Customer</div>
                            <div class="md-stat-value" style="font-size:15px;">{{ $manifest->customer_name ?: '-' }}</div>
                            <div class="md-stat-sub">manifest owner</div>
                        </div>
                    </div>
                </div>

                <!-- Shipments Table Card -->
                <div class="md-card">
                    <div class="md-card-head">
                        <div class="md-card-title">
                            <i class="ti ti-list-details text-primary"></i>
                            Shipments
                            <span class="md-count-badge">{{ $manifest->shipment_count }}</span>
                        </div>
                        <div class="md-card-tools">
                            <div class="md-search">
                                <i class="ti ti-search"></i>
                                <input type="text" id="mdSearchInput" placeholder="Search customer, AWB, address...">
                            </div>
                        </div>
                    </div>

                    <div class="md-table-wrap">
                        @if($manifest->shipments->isNotEmpty())
                            <table class="md-table" id="mdShipmentsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>HAWB Number</th>
                                        <th>Customer Details</th>
                                        <th>Order Date</th>
                                        <th>Package Details</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($manifest->shipments as $index => $shipment)
                                    <tr>
                                        <td style="font-weight:700;color:#94a3b8;">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="md-awb">{{ $shipment['awb_number'] }}</span>
                                        </td>
                                        <td>
                                            <div class="md-customer">
                                                <span class="md-customer-name">{{ $shipment['customer']['name'] ?: 'N/A' }}</span>
                                                <span class="md-cell-sub"><i class="ti ti-mail"></i> {{ $shipment['customer']['email'] ?: 'N/A' }}</span>
                                                <span class="md-cell-sub"><i class="ti ti-phone"></i> {{ $shipment['customer']['phone'] ?: 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="md-order-date">
                                                <span class="md-order-date-main">{{ $shipment['order_date'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="md-package">
                                                <span class="md-package-weight">
                                                    <i class="ti ti-weight"></i> {{ $shipment['total_weight'] }}
                                                    @if((int) $shipment['package_count'] > 0)
                                                        <span class="md-cell-sub">({{ $shipment['package_count'] }} pkg)</span>
                                                    @endif
                                                </span>
                                                <span class="md-csb-badge {{ $shipment['order_type'] === 'CSB5' ? 'csb5' : 'csb4' }}">
                                                    {{ $shipment['order_type'] }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="md-address">{{ $shipment['address'] }}</span>
                                        </td>
                                        <td>
                                            <button type="button"
                                                    class="md-view-btn"
                                                    title="View shipment details"
                                                    data-shipper-id="{{ $shipment['shipper_id'] }}">
                                                <i class="ti ti-eye"></i>
                                            </button>
                                            @if($mdManifestStatus === \App\Models\Manifest::STATUS_OPEN && empty($isAdminView))
                                            <form method="POST"
                                                  action="{{ route('customer.manifest-remove') }}"
                                                  class="d-inline remove-manifest-form"
                                                  data-awb="{{ $shipment['awb_number'] }}"
                                                  data-index="{{ $index + 1 }}">
                                                @csrf
                                                <input type="hidden" name="shipper_id" value="{{ $shipment['shipper_id'] }}">
                                                <input type="hidden" name="manifest_number" value="{{ $manifest->manifest_number }}">
                                                <button type="submit" class="md-remove-btn" title="Remove from Manifest">
                                                    <i class="ti ti-trash"></i> Remove
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="md-empty">
                                <div class="md-empty-icon">
                                    <i class="ti ti-package-off"></i>
                                </div>
                                <div style="font-weight:700;color:#334155;">No shipments in this manifest</div>
                                <div class="mt-1">This manifest currently has no shipment details.</div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
            <!-- End Content -->

        </div>
        <!-- ========================
            End Page Content
        ========================= -->

    </div>
    <!-- End Wrapper -->

    <!-- Close Manifest Confirmation Modal -->
    <div class="modal fade md-modal" id="mdCloseManifestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="md-modal-icon">
                        <i class="ti ti-lock"></i>
                    </div>
                    <h5 class="modal-title mt-3">Close Manifest?</h5>
                    <div class="md-modal-body-text mt-2">
                        You are about to close manifest
                        <span class="md-awb-chip">{{ $manifest->manifest_number ?? '—' }}</span>.
                        <br>Once closed, shipments in this manifest can no longer be removed or modified.
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="md-btn-cancel" data-bs-dismiss="modal">
                            <i class="ti ti-x"></i> Cancel
                        </button>
                        <button type="button" class="md-btn-confirm" id="mdConfirmCloseManifest" style="background:var(--md-ink);">
                            <i class="ti ti-lock"></i> Yes, Close Manifest
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign for Pickup Confirmation Modal -->
    <div class="modal fade md-modal" id="mdAssignPickupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="md-modal-icon" style="background:#eaf0fe;color:var(--md-primary);">
                        <i class="ti ti-truck"></i>
                    </div>
                    <h5 class="modal-title mt-3">Assign for Pickup?</h5>
                    <div class="md-modal-body-text mt-2">
                        You are about to assign manifest
                        <span class="md-awb-chip">{{ $manifest->manifest_number ?? '—' }}</span>
                        for pickup.
                        <br>All shipments in this manifest will be scheduled for pickup by the courier team.
                    </div>
                    <div class="md-pickup-date-section mt-3">
                        <div class="md-pickup-date-label">Select Pickup Date</div>
                        <div class="md-pickup-date-options" id="mdAssignPickupDates">
                            @foreach($pickupDateOptions ?? [] as $idx => $option)
                                <div class="md-pickup-date-option{{ $idx === 0 ? ' selected' : '' }}"
                                     data-value="{{ $option['value'] }}">
                                    <span class="dp-label">{{ $option['label'] }}</span>
                                    <span class="dp-date">{{ $option['display'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" id="mdAssignPickupDate" value="{{ ($pickupDateOptions[0]['value'] ?? '') }}">
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="md-btn-cancel" data-bs-dismiss="modal">
                            <i class="ti ti-x"></i> Cancel
                        </button>
                        <button type="button" class="md-btn-confirm" id="mdConfirmAssignPickup" style="background:var(--md-primary);">
                            <i class="ti ti-truck"></i> Yes, Assign for Pickup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove Confirmation Modal -->
    <div class="modal fade md-modal" id="mdRemoveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="md-modal-icon">
                        <i class="ti ti-alert-triangle"></i>
                    </div>
                    <h5 class="modal-title mt-3">Remove from Manifest?</h5>
                    <div class="md-modal-body-text mt-2">
                        You are about to remove
                        <span class="md-awb-chip" id="mdModalAwb">—</span>
                        from this manifest.
                        <br>The shipment will be moved back to <strong>Packed</strong> status.
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="md-btn-cancel" data-bs-dismiss="modal">
                            <i class="ti ti-x"></i> Cancel
                        </button>
                        <button type="button" class="md-btn-confirm" id="mdConfirmRemove">
                            <i class="ti ti-trash"></i> Yes, Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipment Detail Modal -->
    <div class="modal fade md-modal" id="mdShipmentDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="ti ti-package me-2"></i>Shipment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tracking Number -->
                    <div class="md-tracking-box" id="mdDetailTrackingBox" style="display:none;">
                        <div class="tracking-label">Tracking Number</div>
                        <div class="tracking-value" id="mdDetailTrackingNumber">-</div>
                    </div>

                    <!-- Ship From → Ship To Route -->
                    <div class="md-route-box" id="mdDetailRouteBox">
                        <div class="route-point">
                            <div class="route-label">SHIP FROM</div>
                            <div class="route-city" id="mdDetailShipFrom">-</div>
                        </div>
                        <div class="route-arrow">
                            <i class="ti ti-arrow-right"></i>
                        </div>
                        <div class="route-point">
                            <div class="route-label">SHIP TO</div>
                            <div class="route-city" id="mdDetailShipTo">-</div>
                        </div>
                    </div>

                    <!-- AWB & Invoice Info -->
                    <div class="md-detail-section">
                        <h6><i class="ti ti-clipboard me-1"></i> AWB & Invoice Info</h6>
                        <div class="md-detail-row">
                            <span class="label">HAWB Number</span>
                            <span class="value" id="mdDetailAwbNumber">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Invoice Number</span>
                            <span class="value" id="mdDetailInvoiceNumber">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Invoice Date</span>
                            <span class="value" id="mdDetailInvoiceDate">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Invoice Amount</span>
                            <span class="value" id="mdDetailInvoiceAmount">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Currency</span>
                            <span class="value" id="mdDetailInvoiceCurrency">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Incoterms</span>
                            <span class="value" id="mdDetailIncoterms">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Reference No.</span>
                            <span class="value" id="mdDetailReferenceNumber">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Manifest No.</span>
                            <span class="value" id="mdDetailManifestNumber">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Order Date</span>
                            <span class="value" id="mdDetailOrderDate">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Status</span>
                            <span class="value" id="mdDetailStatus">-</span>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="md-detail-section">
                        <h6><i class="ti ti-building me-1"></i> Customer Info</h6>
                        <div class="md-detail-row">
                            <span class="label">Name</span>
                            <span class="value" id="mdDetailCustomerName">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Phone</span>
                            <span class="value" id="mdDetailCustomerPhone">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Email</span>
                            <span class="value" id="mdDetailCustomerEmail">-</span>
                        </div>
                    </div>

                    <!-- Shipper Info -->
                    <div class="md-detail-section">
                        <h6><i class="ti ti-user me-1"></i> Shipper Info</h6>
                        <div class="md-detail-row">
                            <span class="label">Company</span>
                            <span class="value" id="mdDetailShipperCompany">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Contact Person</span>
                            <span class="value" id="mdDetailShipperContact">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Phone</span>
                            <span class="value" id="mdDetailShipperPhone">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Email</span>
                            <span class="value" id="mdDetailShipperEmail">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Address</span>
                            <span class="value" id="mdDetailShipperAddress">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">City / State / Pincode</span>
                            <span class="value" id="mdDetailShipperCityStatePin">-</span>
                        </div>
                    </div>

                    <!-- Consignee Info -->
                    <div class="md-detail-section">
                        <h6><i class="ti ti-user-check me-1"></i> Consignee Info</h6>
                        <div class="md-detail-row">
                            <span class="label">Name</span>
                            <span class="value" id="mdDetailConsigneeName">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Contact Person</span>
                            <span class="value" id="mdDetailConsigneeContact">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Phone</span>
                            <span class="value" id="mdDetailConsigneePhone">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Email</span>
                            <span class="value" id="mdDetailConsigneeEmail">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Address</span>
                            <span class="value" id="mdDetailConsigneeAddress">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">City / State / Zip</span>
                            <span class="value" id="mdDetailConsigneeCityStateZip">-</span>
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="md-detail-section" id="mdDetailItemsSection">
                        <h6><i class="ti ti-file-text me-1"></i> Invoice Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Box</th>
                                        <th>Description</th>
                                        <th>HS Code</th>
                                        <th>HTS Code</th>
                                        <th>Unit</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>IGST(%)</th>
                                        <th>IGST</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="mdDetailItemsTable"></tbody>
                            </table>
                        </div>
                        <div class="text-end mt-2">
                            <strong>Total: <span id="mdDetailItemsTotal">0.00</span></strong>
                        </div>
                    </div>

                    <!-- Shipment Details -->
                    <div class="md-detail-section">
                        <h6><i class="ti ti-truck me-1"></i> Shipment Details</h6>
                        <div class="md-detail-row">
                            <span class="label">Destination</span>
                            <span class="value" id="mdDetailDestination">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Origin Type</span>
                            <span class="value" id="mdDetailOriginType">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Shipping Method</span>
                            <span class="value" id="mdDetailShippingMethod">-</span>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="md-detail-section" id="mdDetailPriceBreakdownSection" style="display:none;">
                        <h6><i class="ti ti-receipt-2 me-1"></i> Price Breakdown</h6>
                        <div class="md-detail-row">
                            <span class="label">Base Price</span>
                            <span class="value" id="mdDetailBasePrice">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Fuel Price</span>
                            <span class="value" id="mdDetailFuelPrice">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Surcharge</span>
                            <span class="value" id="mdDetailSurchargePrice">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">GST</span>
                            <span class="value" id="mdDetailGstPrice">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Total</span>
                            <span class="value fw-semibold" id="mdDetailTotalPrice">-</span>
                        </div>
                    </div>

                    <!-- Package Dimensions -->
                    <div class="md-detail-section" id="mdDetailPackagesSection">
                        <h6><i class="ti ti-box me-1"></i> Package Dimensions</h6>
                        <div id="mdDetailPackagesContainer"></div>
                    </div>

                    <!-- Shipping Charges (from shipper_info table) -->
                    <div class="md-detail-section" id="mdDetailChargesSection">
                        <h6><i class="ti ti-currency-rupee me-1"></i> Shipping Charges</h6>
                        <div class="md-detail-row">
                            <span class="label">Base Price</span>
                            <span class="value" id="mdDetailTransportCharges">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Fuel Price</span>
                            <span class="value" id="mdDetailServiceOptionsCharges">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Surcharge</span>
                            <span class="value" id="mdDetailSurchargeCharges">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">GST</span>
                            <span class="value" id="mdDetailGstCharges">-</span>
                        </div>
                        <div class="md-detail-row md-detail-row-total">
                            <span class="label">Total Amount</span>
                            <span class="value" id="mdDetailTotalCharges">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Billing Weight</span>
                            <span class="value" id="mdDetailBillingWeight">-</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="md-toast-wrap" id="mdToastWrap"></div>

    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
    <!-- Main theme JS initializes the sidebar dropdowns. -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script>
        // Shipment detail data for the View modal, keyed by shipper_id.
        var manifestShipmentData = @json($shipmentDetails ?? []);

        $(document).ready(function () {

            // Copy manifest number
            $('#mdCopyManifest').on('click', function () {
                var code = '{{ $manifest->manifest_number ?? '' }}';
                if (!code) return;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(code).then(function () {
                        mdShowToast('success', 'Copied!', 'Manifest number copied to clipboard.');
                    });
                } else {
                    var $tmp = $('<textarea>').val(code).appendTo('body').select();
                    document.execCommand('copy');
                    $tmp.remove();
                    mdShowToast('success', 'Copied!', 'Manifest number copied to clipboard.');
                }
            });

            // Live table search
            $('#mdSearchInput').on('keyup', function () {
                var q = $(this).val().toLowerCase().trim();
                $('#mdShipmentsTable tbody tr').each(function () {
                    var rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.indexOf(q) !== -1);
                });
            });

            // Toast helper
            window.mdShowToast = function (type, title, message) {
                var icon = type === 'success' ? 'ti-circle-check' : 'ti-alert-circle';
                var $toast = $(
                    '<div class="md-toast ' + type + '">' +
                        '<div class="toast-icon"><i class="ti ' + icon + '"></i></div>' +
                        '<div class="toast-body-custom"><strong>' + title + '</strong>' + message + '</div>' +
                        '<button type="button" class="toast-close" aria-label="Close"><i class="ti ti-x"></i></button>' +
                    '</div>'
                );
                $('#mdToastWrap').append($toast);
                $toast.find('.toast-close').on('click', function () { mdDismissToast($toast); });
                setTimeout(function () { mdDismissToast($toast); }, 4000);
            };

            window.mdDismissToast = function ($toast) {
                if (!$toast || !$toast.length) return;
                if ($toast.hasClass('out')) return;
                $toast.addClass('out');
                setTimeout(function () { $toast.remove(); }, 300);
            };

            // Remove-from-manifest flow with confirm modal
            var pendingForm = null;

            $(document).on('click', '.remove-manifest-form button[type="submit"]', function (e) {
                e.preventDefault();
                pendingForm = $(this).closest('.remove-manifest-form');
                var awb = pendingForm.data('awb') || 'this shipment';
                $('#mdModalAwb').text(awb);
                var modalEl = document.getElementById('mdRemoveModal');
                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });

            $('#mdConfirmRemove').on('click', function () {
                if (!pendingForm) return;

                var $form = pendingForm;
                var $btn = $form.find('button[type="submit"]');
                var $confirmBtn = $(this);
                var awb = $form.data('awb') || 'this shipment';

                $confirmBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Removing...');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            var modalEl = document.getElementById('mdRemoveModal');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                            mdShowToast('success', 'Removed!', response.message || awb + ' removed from manifest.');
                            setTimeout(function () { window.location.reload(); }, 1200);
                        } else {
                            mdShowToast('error', 'Unable to remove', response.message || 'Please try again.');
                            $confirmBtn.prop('disabled', false).html('<i class="ti ti-trash"></i> Yes, Remove');
                        }
                    },
                    error: function (xhr) {
                        var message = 'Something went wrong. Please try again.';
                        try {
                            var json = JSON.parse(xhr.responseText);
                            if (json && json.message) {
                                message = json.message;
                            }
                        } catch (err) {}
                        mdShowToast('error', 'Unable to remove', message);
                        $confirmBtn.prop('disabled', false).html('<i class="ti ti-trash"></i> Yes, Remove');
                    }
                });
            });

            // Reset pending form when modal is dismissed without confirming
            $('#mdRemoveModal').on('hidden.bs.modal', function () {
                pendingForm = null;
            });

            // ---- Close manifest flow ----
            $('#mdCloseManifestBtn').on('click', function () {
                var modalEl = document.getElementById('mdCloseManifestModal');
                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });

            $('#mdConfirmCloseManifest').on('click', function () {
                var $confirmBtn = $(this);
                var manifestNumber = '{{ $manifest->manifest_number ?? '' }}';

                if (!manifestNumber) {
                    mdShowToast('error', 'Unable to close', 'Manifest number is missing.');
                    return;
                }

                $confirmBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Closing...');

                $.ajax({
                    url: '{{ url("/customer/manifest/close") }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        manifest_number: manifestNumber
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            var modalEl = document.getElementById('mdCloseManifestModal');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                            mdShowToast('success', 'Closed!', response.message || 'Manifest closed successfully.');
                            setTimeout(function () { window.location.reload(); }, 1200);
                        } else {
                            mdShowToast('error', 'Unable to close', response.message || 'Please try again.');
                            $confirmBtn.prop('disabled', false).html('<i class="ti ti-lock"></i> Yes, Close Manifest');
                        }
                    },
                    error: function (xhr) {
                        var message = 'Something went wrong. Please try again.';
                        try {
                            var json = JSON.parse(xhr.responseText);
                            if (json && json.message) {
                                message = json.message;
                            }
                        } catch (err) {}
                        mdShowToast('error', 'Unable to close', message);
                        $confirmBtn.prop('disabled', false).html('<i class="ti ti-lock"></i> Yes, Close Manifest');
                    }
                });
            });

            $(document).on('click', '.md-pickup-date-option', function () {
                var $opt = $(this);
                $opt.closest('.modal').find('.md-pickup-date-option').removeClass('selected');
                $opt.addClass('selected');
                $opt.closest('.modal').find('input[type="hidden"]').val($opt.data('value'));
            });

            // ---- Assign for Pickup flow ----
            $('#mdAssignPickupBtn').on('click', function () {
                var modalEl = document.getElementById('mdAssignPickupModal');
                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });

            $('#mdConfirmAssignPickup').on('click', function () {
                var $confirmBtn = $(this);
                var manifestNumber = '{{ $manifest->manifest_number ?? '' }}';

                if (!manifestNumber) {
                    mdShowToast('error', 'Unable to assign', 'Manifest number is missing.');
                    return;
                }

                $confirmBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Assigning...');

                $.ajax({
                    url: '{{ url("/customer/manifest/assign-pickup") }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        manifest_number: manifestNumber,
                        pickup_date: $('#mdAssignPickupDate').val()
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            var modalEl = document.getElementById('mdAssignPickupModal');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                            mdShowToast('success', 'Assigned!', response.message || 'Manifest assigned for pickup.');
                            setTimeout(function () { window.location.reload(); }, 1200);
                        } else {
                            mdShowToast('error', 'Unable to assign', response.message || 'Please try again.');
                            $confirmBtn.prop('disabled', false).html('<i class="ti ti-truck"></i> Yes, Assign for Pickup');
                        }
                    },
                    error: function (xhr) {
                        var message = 'Something went wrong. Please try again.';
                        try {
                            var json = JSON.parse(xhr.responseText);
                            if (json && json.message) {
                                message = json.message;
                            }
                        } catch (err) {}
                        mdShowToast('error', 'Unable to assign', message);
                        $confirmBtn.prop('disabled', false).html('<i class="ti ti-truck"></i> Yes, Assign for Pickup');
                    }
                });
            });

            // ---- Shipment detail modal ----
            function mdShowShipmentDetail(shipperId) {
                var data = manifestShipmentData[shipperId];
                if (!data) {
                    mdShowToast('error', 'Not found', 'Shipment details not found.');
                    return;
                }

                // Tracking Number
                var trackingBox = document.getElementById('mdDetailTrackingBox');
                var trackingNum = document.getElementById('mdDetailTrackingNumber');
                if (data.tracking_number) {
                    trackingBox.style.display = 'block';
                    trackingNum.textContent = data.tracking_number;
                } else {
                    trackingBox.style.display = 'none';
                }

                // Ship From → Ship To
                document.getElementById('mdDetailShipFrom').textContent = data.ship_from || '-';
                document.getElementById('mdDetailShipTo').textContent = data.ship_to || '-';

                // AWB & Invoice Info
                document.getElementById('mdDetailAwbNumber').textContent = data.awb_number || '-';
                document.getElementById('mdDetailInvoiceNumber').textContent = data.invoice_number || '-';
                document.getElementById('mdDetailInvoiceDate').textContent = data.invoice_date || '-';
                document.getElementById('mdDetailInvoiceAmount').textContent = data.invoice_amount || '-';
                document.getElementById('mdDetailInvoiceCurrency').textContent = data.invoice_currency || '-';
                document.getElementById('mdDetailIncoterms').textContent = data.incoterms || '-';
                document.getElementById('mdDetailReferenceNumber').textContent = data.reference_number || '-';
                document.getElementById('mdDetailManifestNumber').textContent = data.manifest_number || '-';
                document.getElementById('mdDetailOrderDate').textContent = data.order_date || '-';
                document.getElementById('mdDetailStatus').textContent = data.status || '-';

                // Customer Info
                if (data.customer) {
                    document.getElementById('mdDetailCustomerName').textContent = data.customer.name || '-';
                    document.getElementById('mdDetailCustomerPhone').textContent = data.customer.phone || '-';
                    document.getElementById('mdDetailCustomerEmail').textContent = data.customer.email || '-';
                } else {
                    document.getElementById('mdDetailCustomerName').textContent = '-';
                    document.getElementById('mdDetailCustomerPhone').textContent = '-';
                    document.getElementById('mdDetailCustomerEmail').textContent = '-';
                }

                // Shipper Info
                if (data.shipper) {
                    document.getElementById('mdDetailShipperCompany').textContent = data.shipper.company || '-';
                    document.getElementById('mdDetailShipperContact').textContent = data.shipper.contact || '-';
                    document.getElementById('mdDetailShipperPhone').textContent = data.shipper.phone || '-';
                    document.getElementById('mdDetailShipperEmail').textContent = data.shipper.email || '-';
                    document.getElementById('mdDetailShipperAddress').textContent = data.shipper.address || '-';
                    document.getElementById('mdDetailShipperCityStatePin').textContent = data.shipper.city_state_pin || '-';
                } else {
                    document.getElementById('mdDetailShipperCompany').textContent = '-';
                    document.getElementById('mdDetailShipperContact').textContent = '-';
                    document.getElementById('mdDetailShipperPhone').textContent = '-';
                    document.getElementById('mdDetailShipperEmail').textContent = '-';
                    document.getElementById('mdDetailShipperAddress').textContent = '-';
                    document.getElementById('mdDetailShipperCityStatePin').textContent = '-';
                }

                // Consignee Info
                if (data.consignee) {
                    document.getElementById('mdDetailConsigneeName').textContent = data.consignee.name || '-';
                    document.getElementById('mdDetailConsigneeContact').textContent = data.consignee.contact || '-';
                    document.getElementById('mdDetailConsigneePhone').textContent = data.consignee.phone || '-';
                    document.getElementById('mdDetailConsigneeEmail').textContent = data.consignee.email || '-';
                    document.getElementById('mdDetailConsigneeAddress').textContent = data.consignee.address || '-';
                    document.getElementById('mdDetailConsigneeCityStateZip').textContent = data.consignee.city_state_zip || '-';
                } else {
                    document.getElementById('mdDetailConsigneeName').textContent = '-';
                    document.getElementById('mdDetailConsigneeContact').textContent = '-';
                    document.getElementById('mdDetailConsigneePhone').textContent = '-';
                    document.getElementById('mdDetailConsigneeEmail').textContent = '-';
                    document.getElementById('mdDetailConsigneeAddress').textContent = '-';
                    document.getElementById('mdDetailConsigneeCityStateZip').textContent = '-';
                }

                // Shipment Details
                document.getElementById('mdDetailDestination').textContent = data.destination || '-';
                document.getElementById('mdDetailOriginType').textContent = data.origin_type || '-';
                document.getElementById('mdDetailShippingMethod').textContent = data.shipping_method || '-';

                // Package Dimensions
                var packagesContainer = document.getElementById('mdDetailPackagesContainer');
                var packagesSection = document.getElementById('mdDetailPackagesSection');
                packagesContainer.innerHTML = '';
                if (data.packages && data.packages.length > 0) {
                    packagesSection.style.display = 'block';
                    data.packages.forEach(function (pkg) {
                        var card = document.createElement('div');
                        card.className = 'package-card';
                        card.style.cssText = 'border:1px solid #e2e8f0;border-radius:10px;padding:12px;margin-bottom:8px;background:#fff;';
                        var header = document.createElement('div');
                        header.style.cssText = 'background:#f1f5f9;border-radius:8px 8px 0 0;padding:6px 10px;margin:-12px -12px 8px -12px;font-weight:600;color:#475569;';
                        header.innerHTML = '<span style="font-size:14px;"><i class="ti ti-box me-1"></i> Box #' + pkg.index + '</span>';
                        card.appendChild(header);
                        var row1 = document.createElement('div');
                        row1.className = 'row';
                        row1.innerHTML = '<div class="col-md-3"><strong>Weight:</strong> ' + (pkg.weight || '-') + ' Kg</div>' +
                            '<div class="col-md-3"><strong>Length:</strong> ' + (pkg.length || '-') + ' cm</div>' +
                            '<div class="col-md-3"><strong>Width:</strong> ' + (pkg.width || '-') + ' cm</div>' +
                            '<div class="col-md-3"><strong>Height:</strong> ' + (pkg.height || '-') + ' cm</div>';
                        card.appendChild(row1);
                        var row2 = document.createElement('div');
                        row2.className = 'row mt-1';
                        row2.innerHTML = '<div class="col-md-3"><strong>Volumetric Wt:</strong> ' + (pkg.volumetric || '-') + ' Kg</div>' +
                            '<div class="col-md-3"><strong>Chg. Wt:</strong> ' + (pkg.chargeable || '-') + ' Kg</div>';
                        card.appendChild(row2);
                        packagesContainer.appendChild(card);
                    });
                } else {
                    packagesSection.style.display = 'none';
                }

                // Invoice Items
                var itemsTable = document.getElementById('mdDetailItemsTable');
                var itemsSection = document.getElementById('mdDetailItemsSection');
                itemsTable.innerHTML = '';
                if (data.items && data.items.length > 0) {
                    itemsSection.style.display = 'block';
                    data.items.forEach(function (item) {
                        var row = document.createElement('tr');
                        row.innerHTML = '<td>' + (item.box_no || '-') + '</td>' +
                            '<td>' + (item.description || '-') + '</td>' +
                            '<td>' + (item.hs_code || '-') + '</td>' +
                            '<td>' + (item.hts_code || '-') + '</td>' +
                            '<td>' + (item.unit_type || '-') + '</td>' +
                            '<td>' + (item.qty || '-') + '</td>' +
                            '<td>' + (item.unit_rate || '-') + '</td>' +
                            '<td>' + (item.igst_percentage || '-') + '</td>' +
                            '<td>' + (item.igst_amount || '-') + '</td>' +
                            '<td>' + item.amount + '</td>';
                        itemsTable.appendChild(row);
                    });
                    document.getElementById('mdDetailItemsTotal').textContent = data.items_total;
                } else {
                    itemsSection.style.display = 'none';
                }

                // Shipping Charges (from shipper_info table)
                var chargesSection = document.getElementById('mdDetailChargesSection');
                if (data.charges) {
                    chargesSection.style.display = 'block';
                    var chargeValue = function (v) {
                        return v !== null && v !== undefined && v !== '' ? v : '-';
                    };
                    document.getElementById('mdDetailTransportCharges').textContent = chargeValue(data.charges.transport);
                    document.getElementById('mdDetailServiceOptionsCharges').textContent = chargeValue(data.charges.service_options);
                    document.getElementById('mdDetailSurchargeCharges').textContent = chargeValue(data.charges.surcharge);
                    document.getElementById('mdDetailGstCharges').textContent = chargeValue(data.charges.gst);
                    document.getElementById('mdDetailTotalCharges').textContent = chargeValue(data.charges.total);
                    document.getElementById('mdDetailBillingWeight').textContent = chargeValue(data.charges.billing_weight);
                } else {
                    chargesSection.style.display = 'none';
                }

                // Price Breakdown
                var priceBreakdownSection = document.getElementById('mdDetailPriceBreakdownSection');
                if (data.price_breakdown) {
                    priceBreakdownSection.style.display = 'block';
                    var formatPrice = function (v) {
                        return v !== null && v !== undefined ? (data.invoice_currency || 'INR') + ' ' + parseFloat(v).toFixed(2) : '-';
                    };
                    document.getElementById('mdDetailBasePrice').textContent = formatPrice(data.price_breakdown.base);
                    document.getElementById('mdDetailFuelPrice').textContent = formatPrice(data.price_breakdown.fuel);
                    document.getElementById('mdDetailSurchargePrice').textContent = formatPrice(data.price_breakdown.surcharge);
                    document.getElementById('mdDetailGstPrice').textContent = formatPrice(data.price_breakdown.gst);
                    document.getElementById('mdDetailTotalPrice').textContent = formatPrice(data.price_breakdown.total);
                } else {
                    priceBreakdownSection.style.display = 'none';
                }

                // Show modal
                var modalEl = document.getElementById('mdShipmentDetailModal');
                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }

            // Bind View icon click
            $(document).on('click', '.md-view-btn', function () {
                var shipperId = $(this).data('shipper-id');
                if (shipperId) {
                    mdShowShipmentDetail(shipperId);
                }
            });
        });
    </script>

</body>

</html>
