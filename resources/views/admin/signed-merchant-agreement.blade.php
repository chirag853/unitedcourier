<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Merchant Agreement (Signed)</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 11px; line-height: 1.6; }

        /* Step 6 document styling (mirrors .admin-kyc-view .document-wrapper) */
        .document-wrapper { max-width: 100%; margin: 0 auto; background: #ffffff; border-radius: 20px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08); padding: 2rem 2rem; border: 1px solid #eef2f6; }
        .document-wrapper h1, .document-wrapper h2, .document-wrapper h3, .document-wrapper h4 { font-weight: 600; letter-spacing: -0.02em; margin-top: 1.6em; margin-bottom: 0.5em; color: #1a2e4a; }
        .document-wrapper h1 { font-size: 1.8rem; margin-top: 0; margin-bottom: 0.3rem; border-bottom: 2px solid #eef2f6; padding-bottom: 0.4rem; }
        .document-wrapper h2 { font-size: 1.35rem; border-left: 5px solid #1f3a6b; padding: 0.6rem 1rem 0.6rem 1.2rem; background: #f1f5f9; border-radius: 0 40px 40px 0; margin-top: 2rem; }
        .document-wrapper h3 { font-size: 1.1rem; margin-top: 1.6rem; border-bottom: 1px dashed #dce3ec; padding-bottom: 0.3rem; }
        .document-wrapper h4 { font-size: 1rem; margin-top: 1.4rem; }
        .document-wrapper p { margin: 0.8rem 0; color: #334155; font-size: 14px; line-height: 1.7; }
        .document-wrapper ul, .document-wrapper ol { padding-left: 1.6rem; margin: 0.8rem 0 1rem 0; }
        .document-wrapper li { margin: 0.35rem 0; color: #334155; font-size: 14px; line-height: 1.7; }
        .document-wrapper hr { border: 0; border-top: 2px solid #e2eaf2; margin: 2rem 0; }
        .subhead-company { font-size: 1.15rem; font-weight: 500; color: #1f3a6b; margin-top: -0.1rem; margin-bottom: 1.2rem; display: block; }
        .underline-title { text-decoration: underline; text-underline-offset: 4px; text-decoration-thickness: 2px; text-decoration-color: #b3c9e0; }

        /* Bootstrap helper classes referenced by the terms partial */
        .text-muted { color: #64748b; }
        .small { font-size: 12px; }
        .mb-2 { margin-bottom: 0.5rem; }

        /* DomPDF has no flexbox support — force the Authorized Signature block
           to stay right-aligned exactly like the Step 6 preview on the page. */
        #billSignatureBlock { text-align: right; }
        #billSignatureBlock > p { display: block; }
        #billSignaturePlaceholder { display: inline-block !important; vertical-align: top; }

        /* Admin meta box shown above the agreement */
        .meta { margin: 0 0 1.2rem; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 12px; color: #1e293b; }
        .meta-row { margin: 3px 0; }
        .meta .label { font-weight: bold; display: inline-block; width: 135px; color: #334155; }
    </style>
</head>
<body>
    <div class="meta">
        <div class="meta-row"><span class="label">Customer Code:</span>{{ $customer->customer_code ?? '—' }}</div>
        <div class="meta-row"><span class="label">Merchant Name:</span>{{ trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) ?: '—' }}</div>
        <div class="meta-row"><span class="label">Email:</span>{{ $customer->email ?? '—' }}</div>
        <div class="meta-row"><span class="label">Accepted At:</span>{{ $acceptedAt ? \Illuminate\Support\Carbon::parse($acceptedAt)->format('d M Y, h:i A') : '—' }}</div>
    </div>

    @include('customer.partials.terms-document')
</body>
</html>
