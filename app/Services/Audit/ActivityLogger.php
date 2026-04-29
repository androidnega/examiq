<?php

namespace App\Services\Audit;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogger
{
    public static function log(?User $user, string $action, array $metadata = []): void
    {
        ActivityLog::query()->create([
            'user_id' => $user?->getKey(),
            'action' => $action,
            'ip_address' => request()?->ip(),
            'metadata' => $metadata === [] ? null : $metadata,
            'created_at' => now(),
        ]);
    }
}
