<?php

namespace App\Services\Interfaces;

interface DanhMucServiceInterface
{
    public function paginate();
    public function find($id);
    public function create($request);
    public function update($id, $request);
    public function delete($id);
    public function toggleStatus($id, $status);
    public function searchCategories(array $filters);
    public function getActiveCategories();
    public function getCategoryTree();
    public function updateSortOrder($id, $sortOrder);
    public function deleteMany(array $ids);
    public function updateStatusMany(array $ids, $status);
}
