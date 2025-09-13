<?php

namespace App\Repositories\Interfaces;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Role;
    public function findBySlug(string $slug): ?Role;
    public function create(array $data): Role;
    public function update(int $id, array $data): ?Role;
    public function delete(int $id): bool;
    public function assignPermissions(int $roleId, array $permissionIds): bool;
    public function revokePermissions(int $roleId, array $permissionIds): bool;
    public function getRolePermissions(int $roleId): Collection;
}
