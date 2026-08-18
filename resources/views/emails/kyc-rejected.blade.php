<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KYC Rejected</title>
</head>
<body style="margin:0;padding:24px;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;padding:32px;box-shadow:0 4px 16px rgba(15,23,42,.08);">
        <h2 style="margin-top:0;color:#b91c1c;">Your KYC application needs attention</h2>
        <p>Hello {{ mb_convert_case($customer->first_name, MB_CASE_TITLE, 'UTF-8') }},</p>
        <p>Your KYC application has been reviewed and <strong>rejected</strong>. Please review the reason below, correct the details, and re-submit your KYC.</p>
        <div style="padding:16px;background:#fef2f2;border-left:4px solid #dc2626;border-radius:6px;margin:20px 0;">
            <strong>Customer Code:</strong> {{ $customer->customer_code }}<br>
            <strong>Status:</strong> Rejected
        </div>
        <div style="padding:16px;background:#fff7ed;border:1px solid #fed7aa;border-radius:6px;margin:20px 0;">
            <strong style="display:block;margin-bottom:6px;">Reason for rejection:</strong>
            <span style="white-space:pre-wrap;">{{ $remark }}</span>
        </div>
        <p>You can sign in to your dashboard, correct the details shown on the KYC form, and submit again for review.</p>
        <p>If you need assistance, email us at <a href="mailto:{{ config('mail.support_address') }}">{{ config('mail.support_address') }}</a>.</p>
        <p style="margin-bottom:0;">Regards,<br><strong>United Worldwide Couriers Support</strong></p>
    </div>
</body>
</html>
