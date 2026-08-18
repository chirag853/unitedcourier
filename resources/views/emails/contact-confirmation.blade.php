<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Message Received</title>
</head>
<body style="margin:0;padding:24px;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;padding:32px;box-shadow:0 4px 16px rgba(15,23,42,.08);">
        <h2 style="margin-top:0;color:#2563eb;">We received your message</h2>
        <p>Hello {{ mb_convert_case($contact['first_name'], MB_CASE_TITLE, 'UTF-8') }},</p>
        <p>Thank you for contacting United Worldwide Couriers. Our support team has received your request regarding <strong>{{ $contact['service'] }}</strong> and will contact you shortly.</p>
        <h3 style="margin-bottom:8px;">Your Message</h3>
        <div style="padding:16px;background:#f8fafc;border-radius:8px;white-space:pre-wrap;">{{ $contact['message'] }}</div>
        <p>If you need immediate assistance, email us at <a href="mailto:{{ config('mail.support_address') }}">{{ config('mail.support_address') }}</a>.</p>
        <p style="margin-bottom:0;">Regards,<br><strong>United Worldwide Couriers Support</strong></p>
    </div>
</body>
</html>
