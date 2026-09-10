<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manifest Label - {{ $manifest->manifest_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f3f6;
            display: flex;
            justify-content: center;
            padding: 30px;
            color: #111;
        }

        .labels-wrap {
            width: 100%;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            align-items: center;
        }

        /* ===== Label Card ===== */
        .label {
            width: 460px;
            background: #fff;
            border: 2px solid #444;
            padding: 14px 16px;
            page-break-after: always;
        }

        .label:last-child {
            page-break-after: auto;
        }

        /* Header: company + service */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #444;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header .company {
            width: 50%;
        }

        .header .company .brand {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: .5px;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .header .company .brand .sub {
            color: #555;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .header .service {
            width: 50%;
            text-align: right;
        }

        .header .service .svc-label {
            font-size: 10px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header .service .svc-value {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #b22222;
        }

        /* Barcode */
        .barcode {
            text-align: center;
            margin: 8px 0;
        }

        .barcode img {
            max-width: 100%;
            height: 64px;
        }

        .barcode .barcode-number {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 3px;
            margin-top: 2px;
        }

        /* Address section */
        .address-section {
            border: 1px solid #999;
            padding: 8px 10px;
            margin: 6px 0;
        }

        .address-section .addr-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #b22222;
            margin-bottom: 4px;
        }

        .address-section .addr-line {
            font-size: 13px;
            line-height: 1.45;
        }

        /* Product table */
        .products {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        .products th,
        .products td {
            border: 1px solid #444;
            padding: 5px 7px;
            font-size: 12px;
            text-align: left;
        }

        .products th {
            background: #eef1f5;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .products .num {
            text-align: right;
        }

        .products .total-row td {
            font-weight: 700;
            background: #f6f7f9;
        }

        /* Date + sender */
        .date {
            font-size: 12px;
            font-weight: 600;
            margin: 6px 0 8px;
            text-align: right;
        }

        .sender {
            border-top: 2px solid #444;
            padding-top: 8px;
        }

        .sender .sender-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #b22222;
            margin-bottom: 4px;
        }

        .sender .sender-line {
            font-size: 12px;
            line-height: 1.5;
        }

        /* Print */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .labels-wrap {
                max-width: none;
                gap: 0;
                align-items: center;
            }

            .label {
                width: 460px;
                margin: 0;
                border: 2px solid #444;
                box-shadow: none;
                page-break-after: always;
            }

            .label:last-child {
                page-break-after: auto;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="labels-wrap">
        <div class="no-print" style="text-align:center;margin-bottom:10px;">
            <button onclick="window.print()"
                    style="background:#b22222;color:#fff;border:none;padding:10px 26px;font-size:14px;font-weight:700;border-radius:6px;cursor:pointer;">
                🖨 Print Manifest Label
            </button>
        </div>

        @foreach($labels as $label)
        <div class="label">
            <div class="header">
                <div class="company">
                    <div class="brand">Unite Worldwide<br><span class="sub">Couriers</span></div>
                </div>
                <div class="service">
                    <div class="svc-label">Service</div>
                    <div class="svc-value">{{ $label['service'] }}</div>
                </div>
            </div>

            <div class="barcode">
                <img src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($label['awb_number']) }}&code=Code128&translate-esc=false"
                     alt="Barcode">
                <div class="barcode-number">{{ $label['awb_number'] }}</div>
            </div>

            <div class="address-section">
                <div class="addr-title">Delivery Address</div>
                <div class="addr-line">
                    {{ $label['delivery_company'] }}
                    @if(!empty($label['delivery_address']))
                        <br>{{ $label['delivery_address'] }}
                    @endif
                    @if(!empty($label['delivery_phone']))
                        <br>{{ $label['delivery_phone'] }}
                    @endif
                </div>
            </div>

            <table class="products">
                <thead>
                    <tr>
                        <th style="width:60%;">Product Name</th>
                        <th style="width:10%;">Qty</th>
                        <th style="width:30%;">Total (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($label['items'] as $item)
                        <tr>
                            <td>{{ $item['description'] }}</td>
                            <td class="num">{{ (float) $item['qty'] }} {{ $item['unit_type'] ?? '' }}</td>
                            <td class="num">{{ number_format((float) $item['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="barcode">
                <img src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($label['awb_number']) }}&code=Code128&translate-esc=false"
                     alt="Barcode">
                <div class="barcode-number">{{ $label['awb_number'] }}</div>
            </div>

            <div class="date">{{ $label['date'] }}</div>

            <div class="sender">
                <div class="sender-title">Sender & Return Details</div>
                <div class="sender-line">
                    {{ $label['sender_company'] }}<br>
                    {{ $label['sender_address'] }}<br>
                    {{ $label['sender_phone'] }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
