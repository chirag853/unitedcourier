<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Contact Query</title>
</head>
<body style="margin:0;padding:24px;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;padding:32px;box-shadow:0 4px 16px rgba(15,23,42,.08);">
        <h2 style="margin-top:0;color:#2563eb;">New Contact Us Query</h2>
        <table style="width:100%;border-collapse:collapse;">
            <tr><td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:bold;">Name</td><td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ trim($contact['first_name'] . ' ' . $contact['last_name']) }}</td></tr>
            <tr><td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:bold;">Email</td><td style="padding:10px;border-bottom:1px solid #e5e7eb;"><a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></td></tr>
            <tr><td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:bold;">Phone</td><td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $contact['phone'] }}</td></tr>
            <tr><td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:bold;">Service</td><td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $contact['service'] }}</td></tr>
        </table>
        <h3 style="margin-bottom:8px;">Message</h3>
        <div style="padding:16px;background:#f8fafc;border-radius:8px;white-space:pre-wrap;">{{ $contact['message'] }}</div>
    </div>
</body>
</html>
