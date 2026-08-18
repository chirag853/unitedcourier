<?php

namespace App\Http\Middleware;

use App\Support\SystemLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    private array $sensitiveFields = [
        'password',
        'password_confirmation',
        'password_hash',
        'old_password',
        'new_password',
        'new_password_confirmation',
        'otp',
        'remember_token',
        'token',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $this->record($request, $response);
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }

    /**
     * Log every request made by an authenticated admin or customer.
     * Unauthenticated requests (login pages, registration, etc.) are skipped;
     * those flows are logged explicitly by their controllers instead.
     */
    private function record(Request $request, $response): void
    {
        $guard = null;
        $actor = null;

        foreach (['admin', 'customer'] as $candidate) {
            if (auth($candidate)->check()) {
                $guard = $candidate;
                $actor = auth($candidate)->user();
                break;
            }
        }

        if (!$actor) {
            return;
        }

        $route = $request->route();
        $routeName = $route ? $route->getName() : null;
        $method = strtoupper($request->method());

        // Only state-changing requests (create/update/delete) are logged.
        // Page loads (GET/HEAD/OPTIONS) are skipped to avoid noise.
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return;
        }

        $payload = [
            'method' => $method,
            'route'  => $routeName,
            'url'    => $request->fullUrl(),
            'status' => $response ? $response->getStatusCode() : null,
        ];

        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $payload['input'] = $this->sanitize($request->except('_token'));
        }

        SystemLogger::log(
            $guard . '.' . strtolower($method),
            $routeName ?? $request->path(),
            $guard,
            null,
            $payload
        );
    }

    private function sanitize(array $input): array
    {
        $filtered = [];

        foreach ($input as $key => $value) {
            if (in_array($key, $this->sensitiveFields, true)) {
                $filtered[$key] = '***';
                continue;
            }

            $filtered[$key] = is_array($value) ? $this->sanitize($value) : $value;
        }

        return $filtered;
    }
}