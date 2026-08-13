<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Successful</title>
</head>
<body style="margin:0;padding:24px;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;padding:32px;box-shadow:0 4px 16px rgba(15,23,42,.08);">
        <h2 style="margin-top:0;color:#2563eb;">Welcome to United Worldwide Couriers</h2>
        <p>Hello {{ $customer->first_name }},</p>
        <p>Your business account has been created successfully.</p>
        <div style="padding:16px;background:#eff6ff;border-left:4px solid #2563eb;border-radius:6px;margin:20px 0;">
            <strong>Customer Code:</strong> {{ $customer->customer_code }}<br>
            <strong>Registered Email:</strong> {{ $customer->email }}
        </div>
        <p>You can now sign in using your registered email address and password.</p>
        <p>If you did not create this account, contact us immediately at <a href="mailto:{{ config('mail.support_address') }}">{{ config('mail.support_address') }}</a>.</p>
        <p style="margin-bottom:0;">Regards,<br><strong>United Worldwide Couriers Support</strong></p>
    </div>
</body>
</html>
