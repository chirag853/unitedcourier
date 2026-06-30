<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .invoice-wrapper {
            padding: 30px 40px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #2d8eff;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .company-info h1 {
            font-size: 22px;
            color: #2d8eff;
            margin: 0 0 5px 0;
        }
        .company-info p {
            margin: 2px 0;
            color: #666;
            font-size: 11px;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-meta h2 {
            font-size: 18px;
            margin: 0 0 8px 0;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .invoice-meta table {
            font-size: 11px;
            margin-left: auto;
        }
        .invoice-meta td {
            padding: 2px 8px;
        }
        .invoice-meta td:first-child {
            color: #888;
            font-weight: 600;
        }
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 20px;
        }
        .party-box {
            flex: 1;
            background: #f8f9fa;
            border-left: 3px solid #2d8eff;
            padding: 12px 15px;
            border-radius: 0 6px 6px 0;
        }
        .party-box h4 {
            font-size: 11px;
            text-transform: uppercase;
            color: #2d8eff;
            margin: 0 0 8px 0;
            letter-spacing: 0.5px;
        }
        .party-box p {
            margin: 2px 0;
            font-size: 11px;
            line-height: 1.5;
        }
        .party-box .name {
            font-weight: 700;
            font-size: 12px;
            color: #333;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table.items thead th {
            background: #2d8eff;
            color: #fff;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.items tbody td {
            padding: 8px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
        }
        table.items tbody tr:nth-child(even) {
            background: #fafbfc;
        }
        table.items tfoot td {
            padding: 8px;
            font-weight: 600;
            border-top: 2px solid #2d8eff;
        }
        .totals {
            margin-left: auto;
            width: 300px;
            margin-bottom: 25px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 6px 10px;
            font-size: 11px;
        }
        .totals td:first-child {
            color: #666;
        }
        .totals .grand-total td {
            background: #2d8eff;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            border-radius: 4px;
        }
        .awb-box {
            background: #fff3cd;
            border: 1px solid #ffe69c;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .awb-box .label {
            font-size: 11px;
            color: #997404;
            text-transform: uppercase;
            font-weight: 600;
        }
        .awb-box .value {
            font-size: 16px;
            font-weight: 700;
            color: #997404;
            letter-spacing: 1px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #999;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>United Courier</h1>
                <p>Providing seamless global logistics solutions since 1995</p>
                <p>support@unitedcourier.com</p>
            </div>
            <div class="invoice-meta">
                <h2>Invoice</h2>
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
            </div>
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
            <div class="party-box">
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

        <!-- Items -->
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
                @foreach($invoiceItems as $item)
                    <tr>
                        <td>{{ $item->box_no }}</td>
                        <td>{{ $item->description ?: 'Goods' }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-right">{{ number_format($item->unit_rate, 2) }}</td>
                        <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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

        <!-- Footer -->
        <div class="footer">
            <p>This is a system-generated invoice from bulk upload. Generated on {{ date('d M Y, H:i') }}.</p>
            <p>United Courier &copy; {{ date('Y') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
