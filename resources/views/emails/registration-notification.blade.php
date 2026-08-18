<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Customer Registration</title>
</head>
<body style="margin:0;padding:24px;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;padding:32px;box-shadow:0 4px 16px rgba(15,23,42,.08);">
        <h2 style="margin-top:0;color:#2563eb;">New Customer Registration</h2>
        <p>A new customer has successfully registered on the website.</p>
        <table style="width:100%;border-collapse:collapse;">
            <tr><td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:bold;">Customer Code</td><td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $customer->customer_code }}</td></tr>
            <tr><td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:bold;">Name</td><td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ mb_convert_case(trim($customer->first_name . ' ' . $customer->last_name), MB_CASE_TITLE, 'UTF-8') }}</td></tr>
            <tr><td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:bold;">Email</td><td style="padding:10px;border-bottom:1px solid #e5e7eb;"><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></td></tr>
            <tr><td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:bold;">Phone</td><td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $customer->phone_number }}</td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Registered At</td><td style="padding:10px;">{{ optional($customer->created_at)->format('d M Y, h:i A') }}</td></tr>
        </table>
    </div>
</body>
</html>
