<?php

namespace App\Services;

use App\Repositories\ApiCategoryRepository;
use App\Models\DanhMuc;

class ApiCategoryService
{
    public function __construct(private ApiCategoryRepository $repo)
    {
    }

    public function list(array $filters, int $perPage, bool $returnAll)
    {
        return $this->repo->getCategories($filters, $perPage, $returnAll);
    }

    public function find(int $id): ?DanhMuc
    {
        return $this->repo->find($id);
    }

    public function create(array $data): DanhMuc
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): ?DanhMuc
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}


