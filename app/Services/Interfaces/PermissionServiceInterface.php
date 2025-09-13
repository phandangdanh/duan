<?php

namespace App\Services\Interfaces;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

interface PermissionServiceInterface
{
    public function getAllPermissions(): Collection;
    public function getPermissionById(int $id): ?Permission;
    public function getPermissionBySlug(string $slug): ?Permission;
    public function createPermission(array $data): Permission;
    public function updatePermission(int $id, array $data): ?Permission;
    public function deletePermission(int $id): bool;
    public function getPermissionsByModule(string $module): Collection;
    public function syncUserPermissions(int $userId, array $permissionIds): bool;
}
