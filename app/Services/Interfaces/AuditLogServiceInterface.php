<?php

namespace App\Services\Interfaces;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Collection;

interface AuditLogServiceInterface
{
    public function log(string $action, ?int $userId = null, ?string $modelType = null, ?int $modelId = null, ?array $oldValues = null, ?array $newValues = null, ?string $ipAddress = null, ?string $userAgent = null, ?string $url = null): AuditLog;
    public function getUserLogs(int $userId, int $limit = 50): Collection;
    public function getModelLogs(string $modelType, int $modelId): Collection;
    public function getActionLogs(string $action, int $limit = 50): Collection;
    public function getRecentLogs(int $limit = 100): Collection;
    public function cleanupOldLogs(int $days = 90): int;
}
