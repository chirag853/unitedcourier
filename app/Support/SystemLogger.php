<?php

namespace App\Support;

use App\Models\SystemLog;

class SystemLogger
{
    /**
     * Write an entry to the system_logs table.
     *
     * The actor (admin/customer) is detected automatically from the active
     * auth guards, and the request IP is recorded. Failures never break the
     * caller's flow; the error is only reported to the application log.
     */
    public static function log(
        string $action,
        ?string $heading = null,
        ?string $entityType = null,
        $oldValue = null,
        $newValue = null
    ): void {
        try {
            SystemLog::create([
                'action'      => $action,
                'heading'     => $heading,
                'entity_type' => $entityType,
                'old_value'   => $oldValue,
                'new_value'   => $newValue,
                'user_id'     => self::actorId(),
                'ip_address'  => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
            \Log::error('System log write failed: ' . $e->getMessage());
        }
    }

    private static function actorId(): ?int
    {
        foreach (['admin', 'customer', 'web'] as $guard) {
            $user = auth($guard)->user();

            if ($user) {
                return (int) $user->getAuthIdentifier();
            }
        }

        return null;
    }
}