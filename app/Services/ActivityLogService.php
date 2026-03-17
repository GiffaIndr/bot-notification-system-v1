<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogService
{
    public static function log(
        int $groupId,
        string $type,
        string $description,
        array $meta = [],
        string $status = 'success',
        ?int $userId = null
    ): void {
        ActivityLog::create([
            'group_id'    => $groupId,
            'user_id'     => $userId ?? auth()->id(),
            'type'        => $type,
            'description' => $description,
            'meta'        => $meta,
            'status'      => $status,
        ]);
    }
}
