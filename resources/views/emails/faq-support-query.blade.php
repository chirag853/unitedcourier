<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Website Support Query</title>
</head>
<body style="margin:0;padding:24px;background:#f3f4f6;font-family:Arial,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
        <tr>
            <td style="padding:24px;background:linear-gradient(90deg,#2563eb,#9333ea);color:#ffffff;">
                <h1 style="margin:0;font-size:22px;">New Support Query</h1>
                <p style="margin:8px 0 0;font-size:14px;opacity:.9;">Submitted from the United Worldwide Couriers website</p>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="padding:10px 0;width:130px;color:#6b7280;font-weight:bold;vertical-align:top;">Name</td>
                        <td style="padding:10px 0;">{{ mb_convert_case($query->full_name, MB_CASE_TITLE, 'UTF-8') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;font-weight:bold;vertical-align:top;">Email</td>
                        <td style="padding:10px 0;"><a href="mailto:{{ $query->email }}" style="color:#2563eb;">{{ $query->email }}</a></td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;font-weight:bold;vertical-align:top;">Phone</td>
                        <td style="padding:10px 0;">{{ $query->phone }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;font-weight:bold;vertical-align:top;">Page</td>
                        <td style="padding:10px 0;">{{ $query->page_name ?: 'Website' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;font-weight:bold;vertical-align:top;">Submitted</td>
                        <td style="padding:10px 0;">{{ optional($query->created_at)->format('d M Y, h:i A') }}</td>
                    </tr>
                </table>

                <div style="margin-top:18px;padding:18px;background:#f9fafb;border-left:4px solid #7c3aed;border-radius:8px;">
                    <p style="margin:0 0 8px;color:#6b7280;font-weight:bold;">Message</p>
                    <p style="margin:0;line-height:1.6;white-space:pre-wrap;">{{ $query->message }}</p>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
