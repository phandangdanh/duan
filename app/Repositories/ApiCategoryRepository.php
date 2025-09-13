<?php

namespace App\Repositories;

use App\Models\DanhMuc;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiCategoryRepository
{
    public function getCategories(array $filters = [], int $perPage = 10, bool $returnAll = false): Collection|LengthAwarePaginator
    {
        $query = DanhMuc::query();

        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        if ($returnAll) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?DanhMuc
    {
        return DanhMuc::find($id);
    }

    public function create(array $data): DanhMuc
    {
        return DanhMuc::create($data);
    }

    public function update(int $id, array $data): ?DanhMuc
    {
        $record = DanhMuc::find($id);
        if (!$record) return null;
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        $record = DanhMuc::find($id);
        return $record ? (bool) $record->delete() : false;
    }
}


