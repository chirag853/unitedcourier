<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Merchant Agreement (Signed)</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 10px; line-height: 1.45; }
        .cover { text-align: center; margin-bottom: 18px; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        h2 { font-size: 13px; margin: 14px 0 6px; }
        .meta { border: 1px solid #d1d5db; padding: 10px; margin-bottom: 14px; }
        .meta-row { margin: 3px 0; }
        .label { font-weight: bold; display: inline-block; width: 135px; }
        .signature { margin-top: 20px; border-top: 1px solid #9ca3af; padding-top: 10px; width: 230px; }
        .signature img { max-width: 190px; max-height: 75px; display: block; margin-bottom: 8px; }
        .terms { page-break-before: always; }
    </style>
</head>
<body>
    <div class="cover">
        <h1>MERCHANT AGREEMENT (SIGNED)</h1>
        <div>United Worldwide Couriers Pvt Ltd</div>
    </div>

    <div class="meta">
        <div class="meta-row"><span class="label">Customer Code:</span>{{ $customer->customer_code ?? '—' }}</div>
        <div class="meta-row"><span class="label">Merchant Name:</span>{{ trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) ?: '—' }}</div>
        <div class="meta-row"><span class="label">Email:</span>{{ $customer->email ?? '—' }}</div>
        <div class="meta-row"><span class="label">Accepted At:</span>{{ $acceptedAt ? \Illuminate\Support\Carbon::parse($acceptedAt)->format('d M Y, h:i A') : '—' }}</div>
    </div>

    <div class="terms">
        @include('customer.partials.terms-document')
    </div>

    <div class="signature">
        <strong>Electronically signed by merchant</strong>
        <img src="{{ $signatureDataUri }}" alt="Customer signature">
        <div>{{ trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) ?: 'Authorized Signatory' }}</div>
        <div>{{ $acceptedAt ? \Illuminate\Support\Carbon::parse($acceptedAt)->format('d M Y, h:i A') : '—' }}</div>
    </div>
</body>
</html>
