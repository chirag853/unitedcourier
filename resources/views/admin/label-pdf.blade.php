<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Shipping Label - {{ $shipment->awb_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; }
        .label-container {
            width: 100%;
            max-width: 280mm;
            padding: 8mm;
            border: 2px solid #000;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 6mm;
            margin-bottom: 6mm;
        }
        .header h2 {
            font-size: 16px;
            color: #4f46e5;
            margin-bottom: 2px;
        }
        .header p {
            font-size: 10px;
            color: #666;
        }
        .awb-section {
            text-align: center;
            margin-bottom: 6mm;
            padding: 4mm;
            border: 1px solid #000;
            background: #f8f8f8;
        }
        .awb-section .awb-label {
            font-size: 9px;
            color: #888;
            margin-bottom: 2px;
        }
        .awb-section .awb-number {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 3px;
        }
        .address-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6mm;
        }
        .address-box {
            flex: 1;
            padding: 4mm;
            border: 1px solid #000;
        }
        .address-box.from {
            margin-right: 3mm;
        }
        .address-box.to {
            margin-left: 3mm;
            border-left: 2px dashed #000;
        }
        .address-box .label {
            font-size: 9px;
            color: #888;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .address-box .name {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .address-box .detail {
            font-size: 11px;
            line-height: 1.4;
        }
        .info-section {
            border-top: 1px dashed #000;
            padding-top: 4mm;
            margin-bottom: 6mm;
            display: flex;
            justify-content: space-between;
        }
        .info-item {
            flex: 1;
        }
        .info-item .info-label {
            font-size: 9px;
            color: #888;
        }
        .info-item .info-value {
            font-size: 12px;
            font-weight: bold;
        }
        .footer-barcode {
            text-align: center;
            border-top: 2px dashed #000;
            padding-top: 6mm;
        }
        .footer-barcode .awb-big {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 4px;
        }
        .footer-barcode .date-printed {
            font-size: 9px;
            color: #888;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="label-container">
        <!-- Header -->
        <div class="header">
            <h2>UNITED WORLD COURIER</h2>
            <p>Shipping Label</p>
        </div>

        <!-- AWB Number Section -->
        <div class="awb-section">
            <div class="awb-label">AIR WAYBILL NUMBER</div>
            <div class="awb-number">{{ $shipment->awb_number }}</div>
        </div>

        <!-- From / To Address Section -->
        <div class="address-section">
            <div class="address-box from">
                <div class="label">FROM</div>
                <div class="name">{{ $shipment->shipper_company ?? 'N/A' }}</div>
                <div class="detail">
                    {{ $shipment->shipper_contact ?? '' }}<br>
                    {{ $shipment->shipper_address_line1 ?? '' }}
                    @if($shipment->shipper_address_line2) {{ ', ' . $shipment->shipper_address_line2 }} @endif
                    @if($shipment->shipper_address_line3) {{ ', ' . $shipment->shipper_address_line3 }} @endif<br>
                    {{ $shipment->shipper_city ?? '' }}{{ $shipment->shipper_state ? ', ' . $shipment->shipper_state : '' }}{{ $shipment->shipper_pincode ? ' - ' . $shipment->shipper_pincode : '' }}<br>
                    {{ $shipment->shipper_phone ?? '' }}
                </div>
            </div>
            <div class="address-box to">
                <div class="label">TO</div>
                <div class="name">{{ $shipment->consignee_name ?? 'N/A' }}</div>
                <div class="detail">
                    {{ $shipment->consignee_address_line1 ?? '' }}
                    @if($shipment->consignee_address_line2) {{ ', ' . $shipment->consignee_address_line2 }} @endif
                    @if($shipment->consignee_address_line3) {{ ', ' . $shipment->consignee_address_line3 }} @endif<br>
                    {{ $shipment->consignee_city ?? '' }}{{ $shipment->consignee_state ? ', ' . $shipment->consignee_state : '' }}{{ $shipment->consignee_zip_code ? ' - ' . $shipment->consignee_zip_code : '' }}<br>
                    {{ $shipment->consignee_phone ?? '' }}
                </div>
            </div>
        </div>

        <!-- Invoice Info Section -->
        <div class="info-section">
            <div class="info-item">
                <div class="info-label">INVOICE NO.</div>
                <div class="info-value">{{ $shipment->invoice_number ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">AMOUNT</div>
                <div class="info-value">{{ $shipment->invoice_currency ?? 'INR' }} {{ $shipment->invoice_amount ? number_format($shipment->invoice_amount, 2) : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">DATE</div>
                <div class="info-value">{{ $shipment->invoice_date ?? now()->format('d-m-Y') }}</div>
            </div>
        </div>

        <!-- Footer: Large AWB Number -->
        <div class="footer-barcode">
            <div class="awb-big">{{ $shipment->awb_number }}</div>
            <div class="date-printed">Printed: {{ now()->format('d-m-Y H:i') }}</div>
        </div>
    </div>
</body>
</html>
