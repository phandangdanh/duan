<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\Interfaces\AuditLogServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class RoleService implements RoleServiceInterface
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private AuditLogServiceInterface $auditLogService
    ) {}

    public function getAllRoles(): Collection
    {
        return $this->roleRepository->all();
    }

    public function getRoleById(int $id): ?Role
    {
        return $this->roleRepository->find($id);
    }

    public function getRoleBySlug(string $slug): ?Role
    {
        return $this->roleRepository->findBySlug($slug);
    }

    public function createRole(array $data): Role
    {
        $role = $this->roleRepository->create($data);
        
        // Log role creation
        $this->auditLogService->log('role_created', auth()->id(), Role::class, $role->id, null, $role->toArray());
        
        return $role;
    }

    public function updateRole(int $id, array $data): ?Role
    {
        $role = $this->roleRepository->find($id);
        
        if (!$role) {
            return null;
        }

        $oldValues = $role->toArray();
        $updatedRole = $this->roleRepository->update($id, $data);
        
        if ($updatedRole) {
            // Log role update
            $this->auditLogService->log('role_updated', auth()->id(), Role::class, $role->id, $oldValues, $updatedRole->toArray());
        }
        
        return $updatedRole;
    }

    public function deleteRole(int $id): bool
    {
        $role = $this->roleRepository->find($id);
        
        if (!$role) {
            return false;
        }

        $oldValues = $role->toArray();
        $success = $this->roleRepository->delete($id);
        
        if ($success) {
            // Log role deletion
            $this->auditLogService->log('role_deleted', auth()->id(), Role::class, $role->id, $oldValues, null);
        }
        
        return $success;
    }

    public function assignPermissionsToRole(int $roleId, array $permissionIds): bool
    {
        $role = $this->roleRepository->find($roleId);
        
        if (!$role) {
            return false;
        }

        $success = $this->roleRepository->assignPermissions($roleId, $permissionIds);
        
        if ($success) {
            // Log permission assignment
            $this->auditLogService->log('role_permissions_assigned', auth()->id(), Role::class, $roleId, null, ['permission_ids' => $permissionIds]);
        }
        
        return $success;
    }

    public function revokePermissionsFromRole(int $roleId, array $permissionIds): bool
    {
        $role = $this->roleRepository->find($roleId);
        
        if (!$role) {
            return false;
        }

        $success = $this->roleRepository->revokePermissions($roleId, $permissionIds);
        
        if ($success) {
            // Log permission revocation
            $this->auditLogService->log('role_permissions_revoked', auth()->id(), Role::class, $roleId, null, ['permission_ids' => $permissionIds]);
        }
        
        return $success;
    }

    public function getRolePermissions(int $roleId): Collection
    {
        return $this->roleRepository->getRolePermissions($roleId);
    }
}
