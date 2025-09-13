<?php

namespace App\Services\Interfaces;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleServiceInterface
{
    public function getAllRoles(): Collection;
    public function getRoleById(int $id): ?Role;
    public function getRoleBySlug(string $slug): ?Role;
    public function createRole(array $data): Role;
    public function updateRole(int $id, array $data): ?Role;
    public function deleteRole(int $id): bool;
    public function assignPermissionsToRole(int $roleId, array $permissionIds): bool;
    public function revokePermissionsFromRole(int $roleId, array $permissionIds): bool;
    public function getRolePermissions(int $roleId): Collection;
}
