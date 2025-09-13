<?php

namespace App\Services;

use App\Models\Permission;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Services\Interfaces\PermissionServiceInterface;
use App\Services\Interfaces\AuditLogServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class PermissionService implements PermissionServiceInterface
{
    public function __construct(
        private PermissionRepositoryInterface $permissionRepository,
        private AuditLogServiceInterface $auditLogService
    ) {}

    public function getAllPermissions(): Collection
    {
        return $this->permissionRepository->all();
    }

    public function getPermissionById(int $id): ?Permission
    {
        return $this->permissionRepository->find($id);
    }

    public function getPermissionBySlug(string $slug): ?Permission
    {
        return $this->permissionRepository->findBySlug($slug);
    }

    public function createPermission(array $data): Permission
    {
        $permission = $this->permissionRepository->create($data);
        
        // Log permission creation
        $this->auditLogService->log('permission_created', auth()->id(), Permission::class, $permission->id, null, $permission->toArray());
        
        return $permission;
    }

    public function updatePermission(int $id, array $data): ?Permission
    {
        $permission = $this->permissionRepository->find($id);
        
        if (!$permission) {
            return null;
        }

        $oldValues = $permission->toArray();
        $updatedPermission = $this->permissionRepository->update($id, $data);
        
        if ($updatedPermission) {
            // Log permission update
            $this->auditLogService->log('permission_updated', auth()->id(), Permission::class, $permission->id, $oldValues, $updatedPermission->toArray());
        }
        
        return $updatedPermission;
    }

    public function deletePermission(int $id): bool
    {
        $permission = $this->permissionRepository->find($id);
        
        if (!$permission) {
            return false;
        }

        $oldValues = $permission->toArray();
        $success = $this->permissionRepository->delete($id);
        
        if ($success) {
            // Log permission deletion
            $this->auditLogService->log('permission_deleted', auth()->id(), Permission::class, $permission->id, $oldValues, null);
        }
        
        return $success;
    }

    public function getPermissionsByModule(string $module): Collection
    {
        return $this->permissionRepository->getByModule($module);
    }

    public function syncUserPermissions(int $userId, array $permissionIds): bool
    {
        $success = $this->permissionRepository->syncUserPermissions($userId, $permissionIds);
        
        if ($success) {
            // Log permission sync
            $this->auditLogService->log('user_permissions_synced', auth()->id(), \App\Models\User::class, $userId, null, ['permission_ids' => $permissionIds]);
        }
        
        return $success;
    }
}
