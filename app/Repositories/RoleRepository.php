<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    public function all(): Collection
    {
        return Role::where('is_active', true)->get();
    }

    public function find(int $id): ?Role
    {
        return Role::find($id);
    }

    public function findBySlug(string $slug): ?Role
    {
        return Role::where('slug', $slug)->first();
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(int $id, array $data): ?Role
    {
        $role = $this->find($id);
        
        if ($role) {
            $role->update($data);
        }

        return $role;
    }

    public function delete(int $id): bool
    {
        $role = $this->find($id);
        
        if ($role) {
            return $role->update(['is_active' => false]);
        }

        return false;
    }

    public function assignPermissions(int $roleId, array $permissionIds): bool
    {
        $role = $this->find($roleId);
        
        if ($role) {
            $role->permissions()->syncWithoutDetaching($permissionIds);
            return true;
        }

        return false;
    }

    public function revokePermissions(int $roleId, array $permissionIds): bool
    {
        $role = $this->find($roleId);
        
        if ($role) {
            $role->permissions()->detach($permissionIds);
            return true;
        }

        return false;
    }

    public function getRolePermissions(int $roleId): Collection
    {
        $role = $this->find($roleId);
        
        if ($role) {
            return $role->permissions;
        }

        return collect();
    }
}
