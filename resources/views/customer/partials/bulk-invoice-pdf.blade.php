<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .top-strip {
            height: 8px;
            background: #1a56db;
        }
        .invoice-wrapper {
            padding: 28px 38px 20px 38px;
        }
        .invoice-header {
            width: 100%;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .brand-centered {
            text-align: center;
        }
        .brand-logo-centered {
            height: 64px;
            margin-bottom: 6px;
        }
        .brand-name {
            font-size: 19px;
            font-weight: 800;
            color: #111827;
            margin: 0;
            line-height: 1.25;
        }
        .brand-tag {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            color: #1a56db;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 20px;
            padding: 2px 10px;
            margin: 6px 0;
        }
        .company-info p {
            margin: 2px 0;
            color: #6b7280;
            font-size: 11px;
            line-height: 1.5;
        }
        .meta-row {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        .meta-row td {
            vertical-align: middle;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-badge {
            display: inline-block;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #fff;
            background: #1a56db;
            border-radius: 8px;
            padding: 6px 22px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .invoice-meta table {
            font-size: 11px;
            margin-left: auto;
            border-collapse: collapse;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }
        .invoice-meta td {
            padding: 5px 10px;
            border-bottom: 1px solid #eef2f7;
        }
        .invoice-meta td:first-child {
            color: #6b7280;
            font-weight: 600;
            text-align: left;
        }
        .invoice-meta td:last-child {
            text-align: right;
            font-weight: 600;
            color: #111827;
        }
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }
        .party-box {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-top: 3px solid #1a56db;
            padding: 12px 15px;
            border-radius: 0 0 8px 8px;
        }
        .party-box.consignee {
            border-top-color: #7c3aed;
        }
        .party-box h4 {
            font-size: 10px;
            text-transform: uppercase;
            color: #1a56db;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }
        .party-box.consignee h4 {
            color: #7c3aed;
        }
        .party-box p {
            margin: 2px 0;
            font-size: 11px;
            line-height: 1.5;
            color: #374151;
        }
        .party-box .name {
            font-weight: 700;
            font-size: 12.5px;
            color: #111827;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        table.items thead th {
            background: #1a56db;
            color: #fff;
            padding: 10px 8px;
            text-align: left;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        table.items tbody td {
            padding: 9px 8px;
            border-bottom: 1px solid #eef2f7;
            font-size: 11px;
            color: #374151;
        }
        table.items tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        table.items tfoot td {
            padding: 8px;
            font-weight: 600;
            border-top: 2px solid #1a56db;
        }
        .totals {
            margin-left: auto;
            width: 300px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 7px 12px;
            font-size: 11px;
            border-bottom: 1px solid #f1f5f9;
        }
        .totals td:first-child {
            color: #6b7280;
        }
        .totals td:last-child {
            font-weight: 600;
            color: #111827;
        }
        .totals .grand-total td {
            background: #1a56db;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
        }
        .awb-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .awb-box .label {
            font-size: 10px;
            color: #92400e;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: .6px;
        }
        .awb-box .value {
            font-size: 16px;
            font-weight: 800;
            color: #78350f;
            letter-spacing: 1px;
        }
        .terms {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 10px;
            font-size: 10px;
            color: #64748b;
            line-height: 1.6;
        }
        .section-title {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #111827;
            margin: 4px 0 12px 0;
            padding-left: 10px;
            border-left: 4px solid #1a56db;
        }
        .box-block {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            margin-bottom: 16px;
        }
        .box-head {
            background: #1a56db;
            color: #fff;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 800;
        }
        .box-head .box-count {
            font-size: 10.5px;
            font-weight: 700;
        }
        .box-dim {
            background: #eff6ff;
            border-bottom: 1px solid #dbeafe;
            color: #1e40af;
            font-size: 10.5px;
            font-weight: 600;
            padding: 7px 14px;
        }
        .box-block table.items {
            border: none;
            margin-bottom: 0;
        }
        .box-subtotal {
            text-align: right;
            font-size: 11.5px;
            font-weight: 800;
            color: #111827;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            padding: 8px 14px;
        }
        .footer {
            margin-top: 24px;
            padding-top: 14px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
            line-height: 1.6;
        }
        .footer strong {
            color: #4b5563;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="top-strip"></div>
    <div class="invoice-wrapper">
        <!-- Header: logo on top, company name + address below it -->
        <div class="invoice-header">
            <div class="brand-centered">
                <img src="{{ public_path('assets/img/logo.png') }}" alt="United Worldwide Couriers Pvt. Ltd." class="brand-logo-centered">
                <div class="company-info">
                    <p class="brand-name">United Worldwide Couriers Pvt. Ltd.</p>
                    <span class="brand-tag">Global Logistics Since 1995</span>
                    <p>Building No. 1, Bypass Road, Mahipalpur, New Delhi - 110037</p>
                    <p>Email: support@unitedcouriers.biz &nbsp;|&nbsp; www.unitedcouriers.biz</p>
                </div>
            </div>
            <table class="meta-row">
                <tr>
                    <td><span class="invoice-badge">Invoice</span></td>
                    <td class="invoice-meta" style="text-align: right;">
                        <table>
                    <tr>
                        <td>Invoice No:</td>
                        <td><strong>{{ $invoice->invoice_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>Date:</td>
                        <td>{{ $invoice->invoice_date }}</td>
                    </tr>
                    <tr>
                        <td>AWB Number:</td>
                        <td><strong>{{ $shipper->awb_number }}</strong></td>
                    </tr>
                    @if($invoice->reference_number)
                    <tr>
                        <td>Reference:</td>
                        <td>{{ $invoice->reference_number }}</td>
                    </tr>
                    @endif
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Parties -->
        <div class="parties">
            <div class="party-box">
                <h4>From (Shipper)</h4>
                <p class="name">{{ $shipper->company_name }}</p>
                <p>{{ $shipper->contact_person }}</p>
                <p>{{ $shipper->address_line1 }}</p>
                @if($shipper->address_line2)<p>{{ $shipper->address_line2 }}</p>@endif
                @if($shipper->address_line3)<p>{{ $shipper->address_line3 }}</p>@endif
                <p>{{ $shipper->city }}, {{ $shipper->state }} - {{ $shipper->pincode }}</p>
                <p>Phone: {{ $shipper->phone_number }}</p>
                @if($shipper->kyc_number)<p>GST: {{ $shipper->kyc_number }}</p>@endif
            </div>
            <div class="party-box consignee">
                <h4>To (Consignee)</h4>
                <p class="name">{{ $consignee->consignee_name }}</p>
                <p>{{ $consignee->contact_person }}</p>
                <p>{{ $consignee->address_line1 }}</p>
                @if($consignee->address_line2)<p>{{ $consignee->address_line2 }}</p>@endif
                @if($consignee->address_line3)<p>{{ $consignee->address_line3 }}</p>@endif
                <p>{{ $consignee->city }}, {{ $consignee->state }} - {{ $consignee->zip_code }}</p>
                <p>Phone: {{ $consignee->phone_number }}</p>
            </div>
        </div>

        <!-- AWB Highlight -->
        <div class="awb-box">
            <div>
                <div class="label">Air Waybill Number</div>
                <div class="value">{{ $shipper->awb_number }}</div>
            </div>
            <div class="text-right">
                <div class="label">Total Chargeable Weight</div>
                <div class="value">{{ number_format($totalWeight, 2) }} kg</div>
            </div>
        </div>

        <!-- Box-wise items with dimensions -->
        <div class="section-title">Shipment Contents — Box Wise</div>
        @php
            $groupedItems = $invoiceItems->groupBy(function ($it) { return (string) ($it->box_no ?: '1'); });
            foreach (($packages ?? collect()) as $pi => $pkg) {
                $bn = (string) ($pi + 1);
                if (! $groupedItems->has($bn)) { $groupedItems[$bn] = collect(); }
            }
            $groupedItems = $groupedItems->sortKeys(SORT_NUMERIC);
            $pkgList = isset($packages) ? $packages->values() : collect();
        @endphp
        @forelse($groupedItems as $boxNo => $boxItems)
            @php
                $pkg = $pkgList->get(((int) $boxNo) - 1);
                $boxTotal = (float) $boxItems->sum('amount');
                $dimParts = [];
                if ($pkg) {
                    if ($pkg->actual_weight_kg) $dimParts[] = 'Wt: ' . $pkg->actual_weight_kg . ' Kg';
                    if ($pkg->length_cm && $pkg->width_cm && $pkg->height_cm) $dimParts[] = 'L×W×H: ' . $pkg->length_cm . '×' . $pkg->width_cm . '×' . $pkg->height_cm . ' cm';
                    if ($pkg->volumetric_weight) $dimParts[] = 'Vol. Wt: ' . $pkg->volumetric_weight . ' Kg';
                    if ($pkg->chargeable_weight) $dimParts[] = 'Chg. Wt: ' . $pkg->chargeable_weight . ' Kg';
                }
                $dimLine = count($dimParts) ? implode('  |  ', $dimParts) : 'Dimensions not available';
            @endphp
            <div class="box-block">
                <div class="box-head">Box {{ $boxNo }} &nbsp;·&nbsp; <span class="box-count">{{ $boxItems->count() }} {{ $boxItems->count() === 1 ? 'item' : 'items' }}</span></div>
                <div class="box-dim">{{ $dimLine }}</div>
                <table class="items">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Description</th>
                            <th class="text-center" style="width: 60px;">Qty</th>
                            <th class="text-right" style="width: 90px;">Unit Rate</th>
                            <th class="text-right" style="width: 100px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($boxItems as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $item->description ?: 'Goods' }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-right">{{ number_format($item->unit_rate, 2) }}</td>
                                <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No items in this box</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="box-subtotal">Box {{ $boxNo }} Subtotal: {{ number_format($boxTotal, 2) }} {{ $invoice->invoice_currency }}</div>
            </div>
        @empty
            <p class="text-center">No items</p>
        @endforelse

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal ({{ $invoice->invoice_currency }}):</td>
                    <td class="text-right">{{ number_format($invoice->invoice_amount, 2) }}</td>
                </tr>
                @if(!empty($rateDetails['price']))
                <tr>
                    <td>Shipping Cost:</td>
                    <td class="text-right">{{ number_format($rateDetails['price'], 2) }}</td>
                </tr>
                @endif
                @if(!empty($rateDetails['fuel_charge']))
                <tr>
                    <td>Fuel Charge:</td>
                    <td class="text-right">{{ number_format($rateDetails['fuel_charge'], 2) }}</td>
                </tr>
                @endif
                @if(!empty($rateDetails['gst_amount']))
                <tr>
                    <td>GST ({{ $rateDetails['gst_percentage'] ?? 0 }}%):</td>
                    <td class="text-right">{{ number_format($rateDetails['gst_amount'], 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td>Grand Total:</td>
                    <td class="text-right">{{ number_format(($rateDetails['total'] ?? 0) + $invoice->invoice_amount, 2) }} {{ $invoice->invoice_currency }}</td>
                </tr>
            </table>
        </div>

        <!-- Terms + Footer with full company name -->
        <div class="terms">
            Goods once sold will not be taken back. All disputes subject to Delhi jurisdiction. This is a computer-generated invoice and does not require a physical signature.
        </div>
        <div class="footer">
            <p>This is a system-generated invoice from bulk upload. Generated on {{ date('d M Y, H:i') }}.</p>
            <p><strong>United Worldwide Couriers Pvt. Ltd.</strong> &nbsp;|&nbsp; Building No. 1, Bypass Road, Mahipalpur, New Delhi - 110037 &nbsp;|&nbsp; support@unitedcouriers.biz</p>
            <p>United Worldwide Couriers Pvt. Ltd. &copy; {{ date('Y') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
