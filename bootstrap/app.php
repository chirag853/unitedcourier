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
        ]);

        $middleware->validateCsrfTokens(except: [
            '/customer/create-shipment',
            '/tracking/search',
            // Logout must work even after the session expires while a page is open;
            // otherwise a stale CSRF token returns a 419 "Page Expired" on sign out.
            '/customer/logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
