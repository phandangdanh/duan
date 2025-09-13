<?php

namespace App\Services\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface UserServiceInterface
{
    public function paginate();
    public function create($payload);
    public function update($id, $request);
    public function delete($id);
    public function toggleStatus($id, $status);
    public function searchUsers(array $filters);
    public function deleteMany(array $ids);
    public function updateStatusMany(array $ids, $status);
    public function updateRoleMany(array $ids, $role);
    public function getAllPaginate();
    public function getUserStats(): array;
    
}
