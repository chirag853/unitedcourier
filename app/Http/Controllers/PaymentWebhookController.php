<?php

namespace App\Http\Controllers;

use App\Models\PaymentOrder;
use App\Services\CashfreePaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle Cashfree payment webhooks (PAYMENT_SUCCESS_WEBHOOK,
     * ORDER_PAID_WEBHOOK, PAYMENT_FAILED_WEBHOOK, ...).
     *
     * The wallet is credited only after the signature is verified and the
     * payment status is confirmed as SUCCESS. Crediting is idempotent.
     */
    public function handleCashfree(Request $request, CashfreePaymentService $service): JsonResponse
    {
        $rawBody = $request->getContent();
        $signature = $request->header('x-webhook-signature');

        if (! $service->verifyWebhookSignature($rawBody, $signature)) {
            Log::warning('Cashfree webhook rejected: invalid signature.', [
                'url' => $request->fullUrl(),
            ]);

            return response()->json(['status' => 'invalid_signature'], 401);
        }

        $payload = json_decode($rawBody, true);

        if (! is_array($payload)) {
            Log::warning('Cashfree webhook rejected: payload is not valid JSON.');

            return response()->json(['status' => 'invalid_payload'], 400);
        }

        $eventType = strtoupper((string) ($payload['type'] ?? ''));
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        // v3 webhooks nest the order under data.order; keep a v2 fallback.
        $orderId = (string) ($data['order']['order_id'] ?? $data['order_id'] ?? '');

        Log::info('Cashfree webhook received.', [
            'type' => $eventType,
            'order_id' => $orderId,
        ]);

        $order = $orderId !== '' ? PaymentOrder::where('cashfree_order_id', $orderId)->first() : null;

        if (! $order) {
            Log::warning('Cashfree webhook: order not found.', [
                'type' => $eventType,
                'order_id' => $orderId,
            ]);

            return response()->json(['status' => 'order_not_found']);
        }

        $payment = is_array($data['payment'] ?? null) ? $data['payment'] : [];
        $paymentStatus = strtoupper((string) ($payment['payment_status'] ?? ''));

        if (in_array($eventType, ['PAYMENT_SUCCESS_WEBHOOK', 'ORDER_PAID_WEBHOOK'], true)) {
            if ($paymentStatus === 'SUCCESS') {
                $orderAmount = $data['order']['order_amount'] ?? null;

                if (is_numeric($orderAmount) && abs((float) $orderAmount - (float) $order->order_amount) > 0.01) {
                    Log::warning('Cashfree webhook: amount mismatch for order.', [
                        'order_id' => $orderId,
                        'webhook_amount' => $orderAmount,
                        'order_amount' => $order->order_amount,
                    ]);
                }

                $result = $service->creditPaymentOrder($order, [
                    'cf_payment_id' => (string) ($payment['cf_payment_id'] ?? ''),
                    'payment_method' => CashfreePaymentService::formatPaymentMethod($payment),
                    'payment_time' => $payment['payment_time'] ?? null,
                ]);

                Log::info('Cashfree webhook: wallet credited.', [
                    'order_id' => $orderId,
                    'already_credited' => $result['already_credited'],
                ]);
            } else {
                Log::warning('Cashfree webhook: success event with unexpected payment status.', [
                    'type' => $eventType,
                    'order_id' => $orderId,
                    'payment_status' => $paymentStatus,
                ]);
            }
        }

        if (in_array($eventType, ['PAYMENT_FAILED_WEBHOOK', 'PAYMENT_USER_TERMINATED_WEBHOOK'], true)) {
            if (in_array($paymentStatus, ['FAILED', 'CANCELLED', 'USER_TERMINATED'], true) && $order->status === 'pending') {
                $order->update(['status' => 'failed']);

                Log::info('Cashfree webhook: order marked failed.', [
                    'order_id' => $orderId,
                    'payment_status' => $paymentStatus,
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
