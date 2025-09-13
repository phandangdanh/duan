<?php

namespace App\Repositories\Interfaces;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

interface PermissionRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Permission;
    public function findBySlug(string $slug): ?Permission;
    public function create(array $data): Permission;
    public function update(int $id, array $data): ?Permission;
    public function delete(int $id): bool;
    public function getByModule(string $module): Collection;
    public function syncUserPermissions(int $userId, array $permissionIds): bool;
}
