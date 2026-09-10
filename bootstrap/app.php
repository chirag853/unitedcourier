<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            'log.activity' => \App\Http\Middleware\LogActivity::class,
            'redirect.pending.kyc' => \App\Http\Middleware\RedirectPendingKyc::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/customer/create-shipment',
            '/tracking/search',
            // Cashfree webhooks cannot carry a session CSRF token; the request
            // is authenticated via the x-webhook-signature HMAC instead.
            '/payment/webhook/cashfree',
            // Logout must work even after the session expires while a page is open;
            // otherwise a stale CSRF token returns a 419 "Page Expired" on sign out.
            '/customer/logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // A CSRF/session-expiry (419) on an AJAX request must return JSON so the
        // front-end can show a meaningful message and ask the user to reload,
        // instead of the plain HTML "Page Expired" page that breaks fetch().
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your session has expired. Please reload the page and try again.',
                    'errors' => ['session' => ['Your session has expired. Please reload the page and try again.']],
                ], 419);
            }

            return null;
        });
    })->create();
