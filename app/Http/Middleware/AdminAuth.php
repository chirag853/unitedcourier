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

        // Enforce module-wise access control.
        // Super Admin bypasses all checks. Other users must have the
        // relevant module granted in their module_access list.

        if ($admin && !$admin->isSuperAdmin()) {
            // Resolve the route URI relative to the /admin prefix.
            $uri = $request->path(); // e.g. "admin/dashboard"
            $routeUri = preg_replace('#^admin/?#', '', $uri); // e.g. "dashboard"

            // Always allow access to the dashboard index, logout and profile
            // so a restricted user can still navigate and sign out.
            $alwaysAllowed = ['dashboard', 'logout', 'my-profile', 'my-profile/*'];
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

                return redirect()->route('admin.dashboard')
                    ->with('error', 'You do not have permission to access this module.');
            }
        }

        return $next($request);
    }
}
