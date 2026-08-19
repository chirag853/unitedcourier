<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wallet Recharge Successful</title>
</head>
<body style="margin:0;padding:24px;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;padding:32px;box-shadow:0 4px 16px rgba(15,23,42,.08);">
        <h2 style="margin-top:0;color:#15803d;">Wallet Recharge Successful</h2>
        <p>Hello {{ mb_convert_case(trim($customer->first_name . ' ' . $customer->last_name), MB_CASE_TITLE, 'UTF-8') }},</p>
        <p>Your wallet has been recharged successfully. Below are the details of your payment:</p>

        @php
            $methodLabels = [
                'upi' => 'UPI',
                'card' => 'Card',
                'netbanking' => 'Net Banking',
                'wallet' => 'Wallet',
                'paylater' => 'Pay Later',
                'prepaid_card' => 'Prepaid Card',
                'credit_card' => 'Credit Card',
                'debit_card' => 'Debit Card',
                'visa' => 'Visa',
                'mastercard' => 'Mastercard',
                'rupay' => 'RuPay',
                'amex' => 'Amex',
            ];
            $rawMethod = strtolower(trim((string) $payment_method));
            $friendlyMethod = $methodLabels[$rawMethod] ?? null;

            if (! $friendlyMethod && $rawMethod !== '') {
                $friendlyParts = [];
                foreach (explode('_', $rawMethod) as $part) {
                    $friendlyParts[] = $methodLabels[$part] ?? ucfirst($part);
                }
                $friendlyMethod = implode(' ', $friendlyParts);
            }
            $friendlyMethod = $friendlyMethod ?: 'Online Payment';
        @endphp

        <div style="padding:20px;background:#f0fdf4;border-left:4px solid #16a34a;border-radius:8px;margin:20px 0;">
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Amount Credited</td>
                    <td style="padding:6px 0;text-align:right;font-weight:bold;font-size:18px;color:#15803d;">₹{{ number_format((float) $amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Payment Mode</td>
                    <td style="padding:6px 0;text-align:right;font-weight:bold;">{{ $friendlyMethod }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Payment Date &amp; Time</td>
                    <td style="padding:6px 0;text-align:right;font-weight:bold;">{{ $payment_time ? $payment_time->format('d M Y, h:i A') : '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Payment Status</td>
                    <td style="padding:6px 0;text-align:right;font-weight:bold;color:#15803d;">{{ ucfirst($status) }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Transaction ID</td>
                    <td style="padding:6px 0;text-align:right;font-weight:bold;">{{ $transaction->transaction_id }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Payment Reference</td>
                    <td style="padding:6px 0;text-align:right;font-weight:bold;">{{ $order->cashfree_order_id }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Wallet Balance After Recharge</td>
                    <td style="padding:6px 0;text-align:right;font-weight:bold;">₹{{ number_format((float) $transaction->balance_after, 2) }}</td>
                </tr>
            </table>
        </div>

        <p>You can view your complete wallet history anytime from your customer dashboard under <strong>Wallet History</strong>.</p>
        <p>If you have any questions about this recharge, email us at <a href="mailto:{{ config('mail.support_address') }}">{{ config('mail.support_address') }}</a>.</p>
        <p style="margin-bottom:0;">Regards,<br><strong>United Worldwide Couriers Support</strong></p>
    </div>
</body>
</html>