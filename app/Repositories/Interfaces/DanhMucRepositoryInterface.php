<?php

namespace App\Repositories\Interfaces;

interface DanhMucRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllPaginate($perpage = 10);
    public function getActiveCategories();
    public function getRootCategories();
    public function getCategoryTree();
    public function searchCategories($keyword, $status = '', $sort = 'name', $perpage = 10);
    public function updateStatus($id, $status);
    public function updateSortOrder($id, $sortOrder);
}
