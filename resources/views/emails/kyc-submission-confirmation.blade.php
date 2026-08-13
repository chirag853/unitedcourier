<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KYC Application Received</title>
</head>
<body style="margin:0;padding:24px;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;padding:32px;box-shadow:0 4px 16px rgba(15,23,42,.08);">
        <h2 style="margin-top:0;color:#2563eb;">Your KYC application has been received</h2>
        <p>Hello {{ $customer->first_name }},</p>
        <p>We have received your KYC application. Our team will review the submitted information and documents.</p>
        <div style="padding:16px;background:#eff6ff;border-left:4px solid #2563eb;border-radius:6px;margin:20px 0;">
            <strong>KYC Reference:</strong> #{{ $kyc->id }}<br>
            <strong>Customer Code:</strong> {{ $customer->customer_code }}<br>
            <strong>Status:</strong> Under Review
        </div>
        <p>We will email you at this registered address when your KYC is approved.</p>
        <p>If you need assistance, email us at <a href="mailto:{{ config('mail.support_address') }}">{{ config('mail.support_address') }}</a>.</p>
        <p style="margin-bottom:0;">Regards,<br><strong>United Worldwide Couriers Support</strong></p>
    </div>
</body>
</html>
