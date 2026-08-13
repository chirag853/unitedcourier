<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Query Received</title>
</head>
<body style="margin:0;padding:24px;background:#f3f4f6;font-family:Arial,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
        <tr>
            <td style="padding:28px;background:#2563eb;color:#ffffff;text-align:center;">
                <h1 style="margin:0;font-size:24px;">Thank You for Contacting Us</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:28px;">
                <p style="margin:0 0 16px;font-size:16px;">Dear {{ $query->full_name }},</p>
                <p style="margin:0 0 16px;line-height:1.7;color:#374151;">
                    We have received your support query. Our team will review your request and contact you shortly.
                </p>

                <div style="margin:22px 0;padding:18px;background:#f9fafb;border-left:4px solid #7c3aed;border-radius:8px;">
                    <p style="margin:0 0 8px;color:#6b7280;font-weight:bold;">Your message</p>
                    <p style="margin:0;line-height:1.6;white-space:pre-wrap;">{{ $query->message }}</p>
                </div>

                <p style="margin:0 0 8px;line-height:1.7;color:#374151;">
                    For additional information, reply directly to this email or contact us at
                    <a href="mailto:{{ config('mail.support_address') }}" style="color:#2563eb;">{{ config('mail.support_address') }}</a>.
                </p>

                <p style="margin:24px 0 0;line-height:1.6;">
                    Regards,<br>
                    <strong>United Worldwide Couriers Support Team</strong>
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
