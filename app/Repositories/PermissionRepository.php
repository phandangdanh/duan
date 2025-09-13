<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function all(): Collection
    {
        return Permission::where('is_active', true)->get();
    }

    public function find(int $id): ?Permission
    {
        return Permission::find($id);
    }

    public function findBySlug(string $slug): ?Permission
    {
        return Permission::where('slug', $slug)->first();
    }

    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    public function update(int $id, array $data): ?Permission
    {
        $permission = $this->find($id);
        
        if ($permission) {
            $permission->update($data);
        }

        return $permission;
    }

    public function delete(int $id): bool
    {
        $permission = $this->find($id);
        
        if ($permission) {
            return $permission->update(['is_active' => false]);
        }

        return false;
    }

    public function getByModule(string $module): Collection
    {
        return Permission::where('module', $module)
            ->where('is_active', true)
            ->get();
    }

    public function syncUserPermissions(int $userId, array $permissionIds): bool
    {
        $user = \App\Models\User::find($userId);
        
        if ($user) {
            $user->permissions()->sync($permissionIds);
            return true;
        }

        return false;
    }
}
