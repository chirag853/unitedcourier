<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manifest Document - {{ $manifest->manifest_number }}</title>
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

        .doc-wrap {
            width: 100%;
            max-width: 900px;
        }

        .doc {
            background: #fff;
            border: 2px solid #333;
            padding: 24px 30px;
            page-break-after: always;
        }

        .doc:last-child {
            page-break-after: auto;
        }

        /* ===== Header ===== */
        .doc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #333;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .doc-header .brand {
            font-size: 26px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.15;
        }

        .doc-header .brand .sub {
            display: block;
            color: #555;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1.5px;
        }

        .doc-header .doc-title {
            text-align: right;
        }

        .doc-header .doc-title .title-main {
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
            color: #b22222;
            letter-spacing: 1px;
        }

        .doc-header .doc-title .title-sub {
            font-size: 12px;
            color: #555;
            margin-top: 2px;
        }

        /* ===== Manifest number barcode band ===== */
        .barcode-band {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            border: 1px solid #999;
            padding: 10px 14px;
            margin-bottom: 14px;
            background: #fafbfc;
        }

        .barcode-band .band-barcode {
            text-align: center;
        }

        .barcode-band .band-barcode img {
            height: 58px;
        }

        .barcode-band .band-barcode .band-number {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .barcode-band .band-meta {
            text-align: right;
            font-size: 12px;
            line-height: 1.7;
        }

        .barcode-band .band-meta .strong {
            font-weight: 700;
        }

        /* ===== Meta grid ===== */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 14px;
        }

        .meta-box {
            border: 1px solid #999;
            padding: 8px 10px;
        }

        .meta-box .meta-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #666;
        }

        .meta-box .meta-value {
            font-size: 13px;
            font-weight: 700;
            margin-top: 2px;
        }

        /* ===== Shipments table ===== */
        .sec-title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin: 14px 0 6px;
            color: #b22222;
        }

        table.shipments {
            width: 100%;
            border-collapse: collapse;
        }

        table.shipments th,
        table.shipments td {
            border: 1px solid #444;
            padding: 6px 7px;
            font-size: 12px;
            text-align: left;
            vertical-align: top;
        }

        table.shipments th {
            background: #eef1f5;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        table.shipments .num {
            text-align: right;
            white-space: nowrap;
        }

        table.shipments .items-cell {
            font-size: 11px;
            line-height: 1.5;
        }

        table.shipments .total-row td {
            font-weight: 700;
            background: #f6f7f9;
        }

        /* ===== Summary boxes ===== */
        .summary-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 14px;
        }

        .summary-box {
            border: 1px solid #999;
            padding: 10px 12px;
        }

        .summary-box .s-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #666;
        }

        .summary-box .s-value {
            font-size: 16px;
            font-weight: 800;
            margin-top: 2px;
        }

        /* ===== Sender & Return ===== */
        .sender-block {
            border: 1px solid #999;
            padding: 10px 12px;
            margin-top: 14px;
        }

        .sender-block .sender-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #b22222;
            margin-bottom: 4px;
        }

        .sender-block .sender-line {
            font-size: 12px;
            line-height: 1.6;
        }

        .footer-note {
            text-align: center;
            font-size: 11px;
            color: #777;
            margin-top: 14px;
            border-top: 1px dashed #999;
            padding-top: 8px;
        }

        /* ===== Print ===== */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .doc-wrap {
                max-width: none;
            }

            .doc {
                border: 2px solid #333;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="doc-wrap">
        <div class="no-print" style="text-align:center;margin-bottom:10px;">
            <button onclick="window.print()"
                    style="background:#b22222;color:#fff;border:none;padding:10px 26px;font-size:14px;font-weight:700;border-radius:6px;cursor:pointer;">
                🖨 Print Manifest Document
            </button>
        </div>

        <div class="doc">
            <div class="doc-header">
                <div class="brand">Unite Worldwide<br><span class="sub">Couriers</span></div>
                <div class="doc-title">
                    <div class="title-main">Manifest Document</div>
                    <div class="title-sub">Shipping Manifest Summary</div>
                </div>
            </div>

            <div class="barcode-band">
                <div class="band-barcode">
                    <img src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($manifest->manifest_number) }}&code=Code128&translate-esc=false"
                         alt="Manifest Barcode">
                    <div class="band-number">{{ $manifest->manifest_number }}</div>
                </div>
                <div class="band-meta">
                    <div><span class="strong">Status:</span>
                        {{ \App\Models\Manifest::statusLabel((int) $manifest->status) }}</div>
                    <div><span class="strong">Customer:</span> {{ $manifest->customer_name ?: '-' }}</div>
                    <div><span class="strong">Phone:</span> {{ $manifest->customer_phone ?: '-' }}</div>
                </div>
            </div>

            <div class="meta-grid">
                <div class="meta-box">
                    <div class="meta-label">Manifest No.</div>
                    <div class="meta-value">{{ $manifest->manifest_number }}</div>
                </div>
                <div class="meta-box">
                    <div class="meta-label">Manifest Date</div>
                    <div class="meta-value">
                        @if($manifest->manifest_created_at)
                            {{ \Carbon\Carbon::parse($manifest->manifest_created_at)->format('d-m-Y h:i A') }}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="meta-box">
                    <div class="meta-label">Pickup Date</div>
                    <div class="meta-value">
                        @if($manifest->pickup_date)
                            {{ \Carbon\Carbon::parse($manifest->pickup_date)->format('d-m-Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="meta-box">
                    <div class="meta-label">Shipments</div>
                    <div class="meta-value">{{ $manifest->shipment_count }}</div>
                </div>
            </div>

            <div class="sec-title">Shipment Details</div>
            <table class="shipments">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>AWB No.</th>
                        <th>Invoice No.</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Consignee</th>
                        <th>Items</th>
                        <th>Pkgs</th>
                        <th>Weight</th>
                        <th class="num">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($manifest->shipments as $index => $shipment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="white-space:nowrap;">{{ $shipment['awb_number'] }}</td>
                            <td style="white-space:nowrap;">{{ $shipment['invoice_number'] }}</td>
                            <td>{{ $shipment['from'] }}</td>
                            <td>{{ $shipment['to'] }}</td>
                            <td>{{ $shipment['consignee_name'] }}</td>
                            <td class="items-cell">
                                @forelse($shipment['items'] as $item)
                                    <div>{{ $item['description'] }} x {{ $item['qty'] }}</div>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td class="num">{{ $shipment['package_count'] }}</td>
                            <td>{{ $shipment['total_weight'] }}</td>
                            <td class="num">{{ number_format($shipment['amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">No shipments in this manifest.</td>
                        </tr>
                    @endforelse
                    <tr class="total-row">
                        <td colspan="9">Total ({{ $manifest->shipment_count }} shipments)</td>
                        <td class="num">{{ number_format($manifest->total_value, 2) }}{{ $manifest->currency ? ' ' . $manifest->currency : '' }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="summary-row">
                <div class="summary-box">
                    <div class="s-label">Total Shipment Value</div>
                    <div class="s-value">{{ number_format($manifest->total_value, 2) }}{{ $manifest->currency ? ' ' . $manifest->currency : '' }}</div>
                </div>
                <div class="summary-box">
                    <div class="s-label">Total Freight Cost</div>
                    <div class="s-value">{{ number_format($manifest->total_cost, 2) }}</div>
                </div>
            </div>

            <div class="sender-block">
                <div class="sender-title">Sender & Return Details</div>
                <div class="sender-line">
                    UWC COURIERS PVT LTD, Khasra 4/2, Bandh Road, Sultanpur, Delhi - 110086, India<br>
                    Phone: 8130470109
                </div>
            </div>

            <div class="footer-note">
                This is a computer generated manifest document. Generated on
                {{ now('Asia/Kolkata')->format('d-m-Y h:i A') }} (IST).
            </div>
        </div>
    </div>
</body>
</html>
