<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class AuditLogRepository implements AuditLogRepositoryInterface
{
    public function log(string $action, ?int $userId = null, ?string $modelType = null, ?int $modelId = null, ?array $oldValues = null, ?array $newValues = null, ?string $ipAddress = null, ?string $userAgent = null, ?string $url = null): AuditLog
    {
        return AuditLog::log($action, $userId, $modelType, $modelId, $oldValues, $newValues, $ipAddress, $userAgent, $url);
    }

    public function getByUser(int $userId, int $limit = 50): Collection
    {
        return AuditLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getByModel(string $modelType, int $modelId): Collection
    {
        return AuditLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByAction(string $action, int $limit = 50): Collection
    {
        return AuditLog::where('action', $action)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecent(int $limit = 100): Collection
    {
        return AuditLog::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function cleanup(int $days = 90): int
    {
        $cutoffDate = Carbon::now()->subDays($days);
        
        return AuditLog::where('created_at', '<', $cutoffDate)->delete();
    }
}
