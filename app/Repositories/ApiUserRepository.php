<?php

namespace App\Repositories;

use App\Models\UserModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiUserRepository
{
    /**
     * Get all users with pagination and filters
     */
    public function getUsers(array $filters = [], int $perPage = 10, bool $returnAll = false): Collection|LengthAwarePaginator
    {
        $query = UserModel::query();
        
        // Apply filters
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['role'])) {
            $query->where('user_catalogue_id', $filters['role']);
        }
        
        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $allowedSorts = ['created_at', 'name', 'email', 'status'];
        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }
        $query->orderBy($sortBy, $sortDir);
        
        if ($returnAll) {
            return $query->get();
        }
        
        return $query->paginate($perPage);
    }

    /**
     * Get user by ID
     */
    public function getUserById(int $id): ?UserModel
    {
        return UserModel::find($id);
    }

    /**
     * Create new user
     */
    public function createUser(array $data): UserModel
    {
        return UserModel::create($data);
    }

    /**
     * Update user
     */
    public function updateUser(int $id, array $data): bool
    {
        $user = UserModel::find($id);
        if (!$user) {
            return false;
        }
        
        return $user->update($data);
    }

    /**
     * Delete user
     */
    public function deleteUser(int $id): bool
    {
        $user = UserModel::find($id);
        if (!$user) {
            return false;
        }
        
        return $user->delete();
    }
}
