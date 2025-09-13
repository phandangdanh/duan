<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use App\Services\Interfaces\AuditLogServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class AuditLogService implements AuditLogServiceInterface
{
    public function __construct(
        private AuditLogRepositoryInterface $auditLogRepository
    ) {}

    public function log(string $action, ?int $userId = null, ?string $modelType = null, ?int $modelId = null, ?array $oldValues = null, ?array $newValues = null, ?string $ipAddress = null, ?string $userAgent = null, ?string $url = null): AuditLog
    {
        return $this->auditLogRepository->log($action, $userId, $modelType, $modelId, $oldValues, $newValues, $ipAddress, $userAgent, $url);
    }

    public function getUserLogs(int $userId, int $limit = 50): Collection
    {
        return $this->auditLogRepository->getByUser($userId, $limit);
    }

    public function getModelLogs(string $modelType, int $modelId): Collection
    {
        return $this->auditLogRepository->getByModel($modelType, $modelId);
    }

    public function getActionLogs(string $action, int $limit = 50): Collection
    {
        return $this->auditLogRepository->getByAction($action, $limit);
    }

    public function getRecentLogs(int $limit = 100): Collection
    {
        return $this->auditLogRepository->getRecent($limit);
    }

    public function cleanupOldLogs(int $days = 90): int
    {
        return $this->auditLogRepository->cleanup($days);
    }
}
