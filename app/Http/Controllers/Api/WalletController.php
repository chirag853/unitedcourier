<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Recharge a customer wallet by customer_code (no auth).
     *
     * Request JSON (3 only):
     *   - customer_code: required, must exist in customers.customer_code
     *   - recharge_type: required string (e.g. cash, upi, bank)
     *   - amount: required numeric, min 1
     *
     * Effect: wallet balance incremented + a credit WalletTransaction
     * row (reason=recharge) is recorded.
     */
    public function wallet_recharge(Request $request)
    {
        $validated = $request->validate([
            'customer_code' => 'required|string|exists:customers,customer_code',
            'recharge_type' => 'required|string|max:50',
            'amount'        => 'required|numeric|min:1',
        ]);

        $customer = Customer::where('customer_code', $validated['customer_code'])->first();
        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found for this customer_code.',
            ], 404);
        }

        $amount = round((float) $validated['amount'], 2);

        $wallet = Wallet::firstOrCreate(
            ['customer_id' => $customer->id],
            ['balance' => 0]
        );

        $transaction = null;
        DB::transaction(function () use ($wallet, $customer, $validated, $amount, &$transaction) {
            $wallet->increment('balance', $amount);
            $wallet->refresh();

            $transaction = WalletTransaction::create([
                'customer_id'   => $customer->id,
                'type'          => 'credit',
                'reason'        => 'recharge',
                'recharge_type' => $validated['recharge_type'],
                'user_id'       => null,
                'user_type'     => 'api',
                'amount'        => $amount,
                'balance_after' => $wallet->balance,
                'reference'     => 'API-' . now()->format('ymdHis') . '-' . $customer->id,
                'description'   => 'Wallet recharge of ₹' . number_format($amount, 2) . ' via API (' . $validated['recharge_type'] . ')',
            ]);
        });

        $wallet->refresh();

        return response()->json([
            'success'         => true,
            'message'         => 'Wallet recharged successfully.',
            'customer_code'   => $customer->customer_code,
            'amount_credited' => $amount,
            'new_balance'     => (float) $wallet->balance,
            'transaction_id'  => $transaction ? $transaction->transaction_id : null,
        ]);
    }
}
