<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface UserRepositoryInterface
{
    public function getAllPaginate();
    public function create($payload);
    public function findById($id);
    public function find($id);
    public function delete($id);
    public function search(array $filters);
    public function deleteMany(array $ids);
    public function updateStatusMany(array $ids, $status);
    public function updateRoleMany(array $ids, $role);
}
