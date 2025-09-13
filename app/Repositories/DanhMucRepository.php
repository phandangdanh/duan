<?php

namespace App\Repositories;

use App\Models\DanhMuc;
use App\Repositories\Interfaces\DanhMucRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DanhMucRepository extends BaseRepository implements DanhMucRepositoryInterface
{
    protected $model;

    public function __construct(DanhMuc $model)
    {
        $this->model = $model;
    }

    public function getAllPaginate($perpage = 10)
    {
        return $this->model->with('parent')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($perpage);
    }

    public function getActiveCategories()
    {
        return $this->model->active()
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    public function getRootCategories()
    {
        return $this->model->root()
            ->active()
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    public function getCategoryTree()
    {
        return $this->model->with('allChildren')
            ->root()
            ->active()
            ->orderBy('sort_order', 'asc')
            ->get();
    }
    

    public function searchCategories($keyword, $status = '', $sort = 'name', $perpage = 10)
{
    $query = $this->model->with('parent');

    // Tìm kiếm theo keyword
    if (!empty($keyword)) {
        $query->where(function($q) use ($keyword) {
            // Tìm trong chính danh mục
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhere('slug', 'like', "%{$keyword}%");

            // Nếu có danh mục cha thì tìm thêm theo tên cha
            $q->orWhereHas('parent', function($parent) use ($keyword) {
                $parent->where('name', 'like', "%{$keyword}%")
                       ->orWhere('slug', 'like', "%{$keyword}%")
                       ->orWhere('description', 'like', "%{$keyword}%");
            });
        });
    }

    // Lọc theo status
    if (!empty($status)) {
        $query->where('status', $status);
    }

    // Sắp xếp
    switch ($sort) {
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        case 'created_at':
            $query->orderBy('created_at', 'desc');
            break;
        case 'sort_order':
            $query->orderBy('sort_order', 'asc');
            break;
        case 'name':
        default:
            $query->orderBy('created_at', 'desc');
            break;
    }

    return $query->paginate($perpage);
}

    


    public function updateStatus($id, $status)
    {
        $category = $this->model->find($id);
        if ($category) {
            $category->status = $status;
            $category->save();
            return $category;
        }
        return false;
    }

    public function updateSortOrder($id, $sortOrder)
    {
        $category = $this->model->find($id);
        if ($category) {
            $category->sort_order = $sortOrder;
            $category->save();
            return $category;
        }
        return false;
    }

    public function create($data)
    {
        DB::beginTransaction();
        try {
            // Xóa session error không liên quan
            clearUserSessionErrors();
            
            // Xử lý dữ liệu trước khi tạo
            $data = array_filter($data, function($value) {
                return $value !== null && $value !== '';
            });

            // Đảm bảo các trường bắt buộc
            if (empty($data['name'])) {
                throw new \Exception('Tên danh mục không được để trống!');
            }

            // Xử lý slug nếu không có
            if (empty($data['slug'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
            }

            // Xử lý parent_id
            if (empty($data['parent_id']) || $data['parent_id'] == 0 || $data['parent_id'] == '') {
                $data['parent_id'] = 0;
            } else {
                $data['parent_id'] = (int) $data['parent_id'];
            }

            // Xử lý sort_order
            if (empty($data['sort_order'])) {
                $data['sort_order'] = 0;
            }

            // Xử lý status
            if (empty($data['status'])) {
                $data['status'] = 'active';
            }

            // Xử lý image nếu có
            if (isset($data['image']) && !empty($data['image'])) {
                // Đảm bảo đường dẫn ảnh hợp lệ
                if (!str_starts_with($data['image'], 'categories/')) {
                    $data['image'] = 'categories/' . basename($data['image']);
                }
            }

            $category = $this->model->create($data);
            
            if (!$category) {
                throw new \Exception('Không thể tạo danh mục trong database!');
            }

            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('DanhMucRepository create error: ' . $e->getMessage());
            Log::error('Data: ' . json_encode($data));
            throw $e;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $category = $this->model->find($id);
            if (!$category) {
                return false;
            }
            $category->update($data);
            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $category = $this->model->find($id);
            if (!$category) {
                return false;
            }
            
            // Kiểm tra xem có danh mục con không
            if ($category->hasChildren()) {
                throw new \Exception('Không thể xóa danh mục có danh mục con');
            }
            
            $category->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function findByName($name)
    {
        return $this->model->where('name', $name)->first();
    }

    public function findByNameExcludeId($name, $excludeId)
    {
        return $this->model->where('name', $name)
                          ->where('id', '!=', $excludeId)
                          ->first();
    }
}
