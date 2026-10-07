<?php

namespace App\Http\Middleware;

use App\Models\CustomerApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCustomerApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = (string) $request->header('Authorization', '');
        if (! str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Missing Bearer token. Use Authorization: Bearer <token>.',
            ], 401);
        }

        $plain = trim(substr($header, 7));
        if ($plain === '') {
            return response()->json(['success' => false, 'message' => 'Empty Bearer token.'], 401);
        }

        $hash = hash('sha256', $plain);
        $record = CustomerApiToken::where('token', $hash)->first();

        if (! $record || $record->isExpired()) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired API token.'], 401);
        }

        $customer = $record->customer;
        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found for this token.'], 401);
        }

        if (isset($customer->status) && ! $customer->status) {
            return response()->json(['success' => false, 'message' => 'Customer account is deactivated.'], 403);
        }

        // Make the customer available to the existing web manifest flow
        // (CustomerController::manifestShipment uses this guard).
        auth()->guard('customer')->setUser($customer);
        $request->setUserResolver(fn () => $customer);
        $request->attributes->set('api_customer', $customer);
        $request->attributes->set('api_token_record', $record);

        $record->forceFill(['last_used_at' => now()])->saveQuietly();

        return $next($request);
    }
}
