<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Verification Code</title>
</head>
<body style="margin:0;padding:24px;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;padding:32px;box-shadow:0 4px 16px rgba(15,23,42,.08);">
        <h2 style="margin-top:0;color:#2563eb;">Verify your email address</h2>
        <p>Use the verification code below to continue creating your United Worldwide Couriers account:</p>
        <div style="margin:24px 0;padding:18px;text-align:center;background:#eff6ff;border-left:4px solid #2563eb;border-radius:6px;">
            <span style="font-size:32px;letter-spacing:8px;font-weight:700;color:#1d4ed8;">{{ $otp }}</span>
        </div>
        <p>This code expires in 5 minutes.</p>
        <p style="color:#6b7280;">For your security, do not share this code with anyone. If you did not request this code, you can safely ignore this email.</p>
        <p style="margin-bottom:0;">Regards,<br><strong>United Worldwide Couriers Support</strong></p>
    </div>
</body>
</html>
