<?php

namespace App\Repositories\Interfaces;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Collection;

interface AuditLogRepositoryInterface
{
    public function log(string $action, ?int $userId = null, ?string $modelType = null, ?int $modelId = null, ?array $oldValues = null, ?array $newValues = null, ?string $ipAddress = null, ?string $userAgent = null, ?string $url = null): AuditLog;
    public function getByUser(int $userId, int $limit = 50): Collection;
    public function getByModel(string $modelType, int $modelId): Collection;
    public function getByAction(string $action, int $limit = 50): Collection;
    public function getRecent(int $limit = 100): Collection;
    public function cleanup(int $days = 90): int;
}
