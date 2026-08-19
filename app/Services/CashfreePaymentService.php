<?php

namespace App\Services;

use App\Models\PaymentOrder;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CashfreePaymentService
{
    /**
     * Credit the wallet for a paid payment order.
     *
     * The operation is idempotent: a payment order is credited exactly once.
     * The row is locked within a transaction so concurrent callbacks/webhooks
     * cannot double-credit the wallet.
     *
     * @param  array<string, mixed>  $paymentDetails
     * @return array{success: bool, already_credited: bool}
     */
    public function creditPaymentOrder(PaymentOrder $order, array $paymentDetails = []): array
    {
        return DB::transaction(function () use ($order, $paymentDetails) {
            $locked = PaymentOrder::where('id', $order->id)->lockForUpdate()->first();

            if (! $locked || $locked->status === 'paid') {
                return ['success' => false, 'already_credited' => true];
            }

            $wallet = Wallet::where('customer_id', $locked->customer_id)->first();

            if (! $wallet) {
                throw new \RuntimeException('Wallet not found for customer #' . $locked->customer_id);
            }

            $wallet->increment('balance', $locked->order_amount);
            $wallet->refresh();

            $transaction = WalletTransaction::where('reference', $locked->cashfree_order_id)
                ->where('type', 'credit')
                ->latest('id')
                ->first();

            $transactionData = [
                'customer_id' => $locked->customer_id,
                'type' => 'credit',
                'reason' => 'recharge',
                'recharge_type' => $locked->recharge_type ?: 'credit',
                'user_id' => $locked->customer_id,
                'user_type' => 'customer',
                'amount' => $locked->order_amount,
                'balance_after' => $wallet->balance,
                'payment_method' => $paymentDetails['payment_method'] ?? ($transaction?->payment_method ?? 'initiate'),
                'payment_status' => 'success',
                'payment_session_id' => $transaction?->payment_session_id ?? $locked->payment_session_id,
                'reference' => $locked->cashfree_order_id,
                'description' => 'Wallet recharge of ₹' . number_format($locked->order_amount, 2)
                    . ' (Payment ref: ' . $locked->cashfree_order_id . ')',
            ];

            if ($transaction) {
                // Update the "initiate" row created when the order was placed
                // so the history shows the final payment method and balance.
                $transaction->update($transactionData);
            } else {
                $transaction = WalletTransaction::create($transactionData);
            }

            $locked->update([
                'status' => 'paid',
                'cf_payment_id' => $paymentDetails['cf_payment_id'] ?? null,
                'payment_method' => $paymentDetails['payment_method'] ?? null,
                'payment_time' => $paymentDetails['payment_time'] ?? null,
                'verified_at' => now(),
            ]);

            $this->sendRechargeEmail($locked, $transaction, $paymentDetails);

            return ['success' => true, 'already_credited' => false];
        });
    }

    /**
     * Verify the HMAC-SHA256 signature Cashfree sends in the
     * `x-webhook-signature` header of webhook requests.
     */
    public function verifyWebhookSignature(string $rawBody, ?string $signature): bool
    {
        $secret = (string) config('services.cashfree.pg.webhook_secret', '');

        if ($secret === '' || $signature === null || $signature === '') {
            return false;
        }

        $computed = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($computed, $signature);
    }

    /**
     * Extract a readable payment method label from Cashfree payment data.
     *
     * Returns values such as 'credit_card_visa', 'debit_card_mastercard',
     * 'upi', 'netbanking' or 'wallet' so the wallet history and emails show
     * exactly how the customer paid.
     *
     * @param  array<mixed>|string  $payment  Raw Cashfree payment / payment_method data.
     */
    public static function formatPaymentMethod($payment): string
    {
        $method = $payment;

        if (is_array($payment)) {
            $method = $payment['payment_method'] ?? $payment;
        }

        if (! is_array($method)) {
            return strtolower(trim((string) $method));
        }

        $base = strtolower(trim((string) (($method['method'] ?? '') ?: ($method['channel'] ?? ''))));

        if ($base === '') {
            return '';
        }

        if ($base === 'card') {
            $cardType = strtolower(trim((string) ($method['card']['card_type'] ?? '')));
            $scheme = strtolower(trim((string) (($method['card']['card_scheme'] ?? '') ?: ($method['card']['card_brand'] ?? ''))));

            if (str_contains($cardType, 'credit')) {
                $type = 'credit_card';
            } elseif (str_contains($cardType, 'debit')) {
                $type = 'debit_card';
            } else {
                $type = 'card';
            }

            if ($type !== 'card' && $scheme !== '' && $scheme !== 'other') {
                return $type . '_' . $scheme;
            }

            return $type;
        }

        return $base;
    }

    /**
     * Send a wallet recharge confirmation email to the customer, with an
     * internal BCC copy to the finance mailbox. Includes the payment method,
     * date/time, amount and status so the customer knows exactly how the
     * payment was made.
     *
     * @param  array<string, mixed>  $paymentDetails
     */
    private function sendRechargeEmail(PaymentOrder $order, WalletTransaction $transaction, array $paymentDetails = []): void
    {
        try {
            $customer = $order->customer;

            if (! $customer || ! $customer->email) {
                return;
            }

            $paymentTime = $paymentDetails['payment_time'] ?? null;
            $paymentTime = $paymentTime !== null && $paymentTime !== ''
                ? Carbon::parse($paymentTime)
                : $transaction->created_at;

            Mail::send('emails.wallet-recharge', [
                'customer' => $customer,
                'order' => $order,
                'transaction' => $transaction,
                'amount' => $order->order_amount,
                'payment_method' => (string) ($paymentDetails['payment_method'] ?? $transaction->payment_method ?? ''),
                'payment_time' => $paymentTime,
                'status' => 'success',
            ], function ($mail) use ($customer, $order) {
                $mail->to(
                    $customer->email,
                    trim($customer->first_name . ' ' . $customer->last_name)
                )
                    ->bcc('sidhantk@unitedcouriers.biz')
                    ->replyTo(config('mail.support_address'), config('mail.from.name'))
                    ->subject('Wallet Recharge Successful - ₹' . number_format($order->order_amount, 2) . ' - United Worldwide Couriers');
            });
        } catch (\Throwable $mailException) {
            report($mailException);
            Log::error('Wallet recharge email error for customer ' . $order->customer_id . ': ' . $mailException->getMessage());
        }
    }
}
