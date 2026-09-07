<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks customers whose KYC is pending or under review from visiting any
 * page other than the dashboard until their KYC has been approved.
 *
 * The status is read from the customer's latest KycDetail record
 * (it lives on kyc_details, not on the customers table).
 */
class RedirectPendingKyc
{
    /**
     * URI paths inside the /customer prefix that must never be redirected,
     * otherwise we would create a redirect loop or break a payment callback.
     */
    private const ALLOWED_PATHS = [
        'customer/dashboard',                 // the only page they may visit while pending
        'customer/wallet-recharge/callback',  // payment gateway return URL
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('customer');

        // Only logged-in customers are affected.
        if (! $guard->check()) {
            return $next($request);
        }

        // Form submissions (login, logout, KYC drafts, payments, ...) and
        // JSON/API calls are always allowed through so the user is never
        // locked out and the dashboard can still load its chart data.
        if ($request->isMethod('POST') || $request->expectsJson()) {
            return $next($request);
        }

        // Never redirect the dashboard itself (redirect loop) and keep any
        // other paths that must remain reachable.
        if (in_array($request->path(), self::ALLOWED_PATHS, true)) {
            return $next($request);
        }

        $kycStatus = $guard->user()->kycDetail?->kyc_status;

        if (in_array($kycStatus, ['pending', 'under_review'], true)) {
            return redirect()->route('customer.dashboard');
        }

        return $next($request);
    }
}
