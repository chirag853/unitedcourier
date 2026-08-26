<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login to access this page');
        }

        $admin = Auth::guard('admin')->user();

        // Block deactivated users even if they have an active session.
        if ($admin && $admin->status != 1) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')
                ->with('error', 'Your account has been deactivated. Please contact the Super Admin.');
        }

        // The main dashboard is reserved for Admin and Super Admin accounts.
        // Delivery persons use their own scoped dashboard instead.
        $uri = $request->path();
        $routeUri = preg_replace('#^admin/?#', '', $uri);
        if ($admin && in_array($routeUri, ['dashboard', 'dashboard-chart-data'], true) && !$admin->canAccessDashboard()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have permission to access the dashboard.',
                ], 403);
            }
            if ($admin->canAccessDeliveryDashboard()) {
                return redirect()->route('admin.delivery-dashboard');
            }

            return redirect()->route('admin.my-profile')
                ->with('error', 'You do not have permission to access the dashboard.');
        }

        // Delivery pages are exclusively scoped to Delivery_person accounts.
        if ($admin && in_array($routeUri, ['delivery-dashboard', 'delivery-dashboard-chart-data', 'delivery-orders', 'pickup-delivery', 'received-in-hub'], true)) {
            if (!$admin->canAccessDeliveryDashboard()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'You do not have permission to access delivery pages.',
                    ], 403);
                }

                return redirect()->route('admin.my-profile')
                    ->with('error', 'You do not have permission to access delivery pages.');
            }

            return $next($request);
        }

        // Enforce module-wise access control.
        // Super Admin bypasses all checks. Other users must have the
        // relevant module granted in their module_access list.

        if ($admin && !$admin->isSuperAdmin()) {
            // Logout, profile, and notification polling remain available so
            // restricted users can manage their account and receive assignments.
            $alwaysAllowed = [
                'logout',
                'my-profile',
                'my-profile/*',
                'notifications-data',
                'notifications/*',
            ];
            foreach ($alwaysAllowed as $pattern) {
                if (str_ends_with($pattern, '*')) {
                    $prefix = rtrim($pattern, '*');
                    if ($routeUri === $prefix || str_starts_with($routeUri, $prefix)) {
                        return $next($request);
                    }
                } elseif ($pattern === $routeUri) {
                    return $next($request);
                }
            }

            if (!$admin->canAccessRoute($routeUri)) {
                // AJAX / API style requests get a 403 response.
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'You do not have permission to access this module.',
                    ], 403);
                }

                return redirect()->route('admin.my-profile')
                    ->with('error', 'You do not have permission to access this module.');
            }
        }

        return $next($request);
    }
}
