<?php

namespace App\Services;

use App\Services\Interfaces\DanhMucServiceInterface;
use App\Repositories\DanhMucRepository;
use App\Repositories\Interfaces\DanhMucRepositoryInterface;
use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class DanhMucService implements DanhMucServiceInterface
{
    protected $danhMucRepository;

    public function __construct(DanhMucRepository $danhMucRepository)
    {
        $this->danhMucRepository = $danhMucRepository;
    }

    public function paginate($perpage = 10)
    {
        return $this->danhMucRepository->getAllPaginate($perpage);
    }

    public function find($id)
    {
        return $this->danhMucRepository->find($id);
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            // Xóa session error không liên quan
            clearUserSessionErrors();
            
            $payload = $request->except('_token', 'send');

            // Xử lý parent_id
            if (empty($payload['parent_id']) || $payload['parent_id'] == 0 || $payload['parent_id'] == '') {
                $payload['parent_id'] = 0;
            } else {
                $payload['parent_id'] = (int) $payload['parent_id'];
            }

            // Xử lý sort_order
            if (empty($payload['sort_order'])) {
                $payload['sort_order'] = 0;
            }

            // Xử lý status
            if (empty($payload['status'])) {
                $payload['status'] = 'active';
            }

            // Trong create()
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                try {
                    // Đảm bảo thư mục tồn tại
                    if (!file_exists(public_path('uploads/categories'))) {
                        mkdir(public_path('uploads/categories'), 0777, true);
                    }

                    $file = $request->file('image');
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    // Lưu trực tiếp vào public/uploads/categories
                    $file->move(public_path('uploads/categories'), $filename);

                    // Chỉ lưu relative path
                    $payload['image'] = 'categories/' . $filename;

                    Log::info('Image uploaded successfully: ' . $payload['image']);
                } catch (\Exception $e) {
                    Log::error('Image upload failed: ' . $e->getMessage());
                    throw new \Exception('Lỗi upload ảnh: ' . $e->getMessage());
                }
            }


            // Kiểm tra tên danh mục đã tồn tại chưa
            $existingCategory = $this->danhMucRepository->findByName($payload['name']);
            if ($existingCategory) {
                throw new \Exception('Tên danh mục "' . $payload['name'] . '" đã tồn tại! Vui lòng chọn tên khác.');
            }

            // Tạo danh mục
            $category = $this->danhMucRepository->create($payload);

            if (!$category) {
                throw new \Exception('Không thể tạo danh mục!');
            }

            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category create failed: ' . $e->getMessage());
            Log::error('Payload: ' . json_encode($payload ?? []));
            throw $e;
        }
    }

    public function update($id, $request)
    {
        $category = $this->danhMucRepository->find($id);
        if (!$category) {
            throw new \Exception('Không tìm thấy danh mục!');
        }

        $data = $request->only([
            'name', 'description', 'parent_id', 'sort_order', 'status'
        ]);

        // Xử lý parent_id
        if (empty($data['parent_id']) || $data['parent_id'] == 0 || $data['parent_id'] == '') {
            $data['parent_id'] = 0;
        } else {
            $data['parent_id'] = (int) $data['parent_id'];
        }

        // Kiểm tra tên danh mục đã tồn tại chưa (trừ chính mình)
        if (isset($data['name']) && $data['name'] !== $category->name) {
            $existingCategory = $this->danhMucRepository->findByNameExcludeId($data['name'], $id);
            if ($existingCategory) {
                throw new \Exception('Tên danh mục "' . $data['name'] . '" đã tồn tại! Vui lòng chọn tên khác.');
            }
        }

        // Trong update()
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                // Xóa ảnh cũ nếu có
                if ($category->image && file_exists(public_path('uploads/' . $category->image))) {
                    unlink(public_path('uploads/' . $category->image));
                    Log::info('Old image deleted: ' . $category->image);
                }

                if (!file_exists(public_path('uploads/categories'))) {
                    mkdir(public_path('uploads/categories'), 0777, true);
                }

                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Lưu vào public/uploads/categories
                $file->move(public_path('uploads/categories'), $filename);

                $data['image'] = 'categories/' . $filename;

                Log::info('New image uploaded successfully: ' . $data['image']);
            } catch (\Exception $e) {
                Log::error('Image update failed: ' . $e->getMessage());
                throw new \Exception('Lỗi cập nhật ảnh: ' . $e->getMessage());
            }
        }


        return $this->danhMucRepository->update($id, $data);
    }

    public function delete($id)
    {
        $category = $this->danhMucRepository->find($id);
        if (!$category) return false;

        try {
            // Trong delete()
            if ($category->image && file_exists(public_path('uploads/' . $category->image))) {
                unlink(public_path('uploads/' . $category->image));
                Log::info('Category image deleted: ' . $category->image);
            }


            return $this->danhMucRepository->delete($id);
        } catch (\Exception $e) {
            Log::error('Category delete failed: ' . $e->getMessage());
            throw new \Exception('Lỗi xóa danh mục: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id, $status)
    {
        $category = $this->danhMucRepository->find($id);
        if (!$category) return false;
        
        // Nếu status được truyền vào, sử dụng nó; nếu không thì toggle
        if ($status === null) {
            $category->status = $category->status === 'active' ? 'inactive' : 'active';
        } else {
            $category->status = $status;
        }
        
        $category->save();
        return $category;
    }

    public function searchCategories(array $filters)
    {
        $keyword = $filters['keyword'] ?? '';
        $status = $filters['status'] ?? '';
        $sort = $filters['sort'] ?? 'name';
        $perpage = $filters['perpage'] ?? 10;
        
        return $this->danhMucRepository->searchCategories($keyword, $status, $sort, $perpage);
    }

    public function getActiveCategories()
    {
        return $this->danhMucRepository->getActiveCategories();
    }

    public function getCategoryTree()
    {
        return $this->danhMucRepository->getCategoryTree();
    }

    public function updateSortOrder($id, $sortOrder)
    {
        return $this->danhMucRepository->updateSortOrder($id, $sortOrder);
    }

    public function deleteMany(array $ids)
    {
        DB::beginTransaction();
        try {
            $deletedCount = 0;
            foreach ($ids as $id) {
                $category = $this->danhMucRepository->find($id);
                if ($category) {
                    // Kiểm tra xem có danh mục con không
                    if ($category->hasChildren()) {
                        throw new \Exception('Không thể xóa danh mục "' . $category->name . '" vì có danh mục con');
                    }
                    
                    if ($category->image && file_exists(public_path('uploads/' . $category->image))) {
                        unlink(public_path('uploads/' . $category->image));
                        Log::info('Category image deleted (bulk): ' . $category->image);
                    }
                    
                    
                    $this->danhMucRepository->delete($id);
                    $deletedCount++;
                }
            }
            DB::commit();
            return $deletedCount;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category delete many failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateStatusMany(array $ids, $status)
    {
        DB::beginTransaction();
        try {
            $updatedCount = 0;
            foreach ($ids as $id) {
                $category = $this->danhMucRepository->find($id);
                if ($category) {
                    $this->danhMucRepository->updateStatus($id, $status);
                    $updatedCount++;
                }
            }
            DB::commit();
            return $updatedCount;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category update status many failed: ' . $e->getMessage());
            throw $e;
        }
    }

    // Thống kê danh mục
    public function getDanhMucStats()
    {
        $stats = [
            'total_categories' => DanhMuc::count(),
            'active_categories' => DanhMuc::where('status', 'active')->count(),
            'inactive_categories' => DanhMuc::where('status', 'inactive')->count(),
            'root_categories' => DanhMuc::where('parent_id', 0)->count(),
            'sub_categories' => DanhMuc::where('parent_id', '>', 0)->count(),
            'categories_with_image' => DanhMuc::whereNotNull('image')->where('image', '!=', '')->count(),
            'categories_without_image' => DanhMuc::where(function($query) {
                $query->whereNull('image')->orWhere('image', '');
            })->count(),
        ];

        return $stats;
    }

    // Dữ liệu biểu đồ danh mục
    public function getDanhMucChartData($period = 'month')
    {
        $query = DanhMuc::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_categories')
        );

        switch ($period) {
            case 'week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', now()->subMonth());
                break;
            case 'year':
                $query->where('created_at', '>=', now()->subYear());
                break;
        }

        return $query->groupBy('date')
                    ->orderBy('date')
                    ->get();
    }

    // Top danh mục có nhiều sản phẩm nhất
    public function getTopCategories($limit = 10)
    {
        // Chỉ select các cột cần thiết để tránh ONLY_FULL_GROUP_BY
        return DanhMuc::select(
            'danhmuc.id',
            'danhmuc.name',
            DB::raw('COUNT(sanpham.id) as total_products')
        )
        ->leftJoin('sanpham', 'danhmuc.id', '=', 'sanpham.id_danhmuc')
        ->where('danhmuc.status', 'active')
        ->groupBy('danhmuc.id', 'danhmuc.name')
        ->orderBy('total_products', 'desc')
        ->limit($limit)
        ->get();
    }
}
