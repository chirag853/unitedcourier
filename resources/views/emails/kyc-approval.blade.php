<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KYC Approved</title>
</head>
<body style="margin:0;padding:24px;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;padding:32px;box-shadow:0 4px 16px rgba(15,23,42,.08);">
        <h2 style="margin-top:0;color:#15803d;">Your KYC has been approved</h2>
        <p>Hello {{ $customer->first_name }},</p>
        <p>Your KYC application has been reviewed and approved successfully.</p>
        <div style="padding:16px;background:#f0fdf4;border-left:4px solid #16a34a;border-radius:6px;margin:20px 0;">
            <strong>KYC Reference:</strong> #{{ $kyc->id }}<br>
            <strong>Customer Code:</strong> {{ $customer->customer_code }}<br>
            <strong>Status:</strong> Approved
        </div>
        <p>Shipment creation is now enabled for your account. You can sign in and continue using your customer dashboard.</p>
        <p>If you need assistance, email us at <a href="mailto:{{ config('mail.support_address') }}">{{ config('mail.support_address') }}</a>.</p>
        <p style="margin-bottom:0;">Regards,<br><strong>United Worldwide Couriers Support</strong></p>
    </div>
</body>
</html>
