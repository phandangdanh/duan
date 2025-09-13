<?php

namespace App\Repositories;

use App\Models\SanPham;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ApiProductRepository
{
    /**
     * Get all products with pagination and filters
     */
    public function getProducts(array $filters = [], int $perPage = 10, bool $returnAll = false): Collection|LengthAwarePaginator
    {
        $query = SanPham::active()
            ->with(['danhmuc', 'hinhanh' => function($q) {
                $q->whereNull('deleted_at');
            }, 'chitietsanpham' => function($q) {
                $q->whereNull('deleted_at')
                  ->with(['mausac', 'size']);
            }]);
        
        // Apply filters
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('tenSP', 'like', "%{$keyword}%")
                  ->orWhere('maSP', 'like', "%{$keyword}%")
                  ->orWhere('moTa', 'like', "%{$keyword}%");
            });
        }
        
        if (isset($filters['status'])) {
            $query->where('trangthai', $filters['status']);
        }
        
        if (isset($filters['category'])) {
            $query->where('id_danhmuc', $filters['category']);
        }
        
        // Price filters
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $query->whereExists(function ($subQuery) use ($filters) {
                $subQuery->select(DB::raw(1))
                    ->from('chitietsanpham')
                    ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                    ->whereNull('chitietsanpham.deleted_at');
                
                if (isset($filters['min_price'])) {
                    $subQuery->where('chitietsanpham.gia', '>=', $filters['min_price']);
                }
                
                if (isset($filters['max_price'])) {
                    $subQuery->where('chitietsanpham.gia', '<=', $filters['max_price']);
                }
            });
        }
        
        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $allowedSorts = ['created_at', 'tenSP', 'maSP', 'base_price', 'trangthai'];
        
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
     * Get product by ID
     */
    public function getProductById(int $id): ?SanPham
    {
        return SanPham::active()
            ->with(['danhmuc', 'hinhanh' => function($q) {
                $q->whereNull('deleted_at');
            }, 'chitietsanpham' => function($q) {
                $q->whereNull('deleted_at')
                  ->with(['mausac', 'size']);
            }, 'binhluan' => function($q) {
                $q->whereNull('deleted_at')
                  ->orderBy('created_at', 'desc');
            }])
            ->find($id);
    }

    /**
     * Create new product
     */
    public function createProduct(array $data): SanPham
    {
        return SanPham::create([
            'maSP' => $data['maSP'] ?? null,
            'tenSP' => $data['tenSP'],
            'id_danhmuc' => $data['id_danhmuc'],
            'moTa' => $data['moTa'] ?? null,
            'trangthai' => $data['trangthai'] ?? 1,
            'base_price' => $data['base_price'] ?? null,
            'base_sale_price' => $data['base_sale_price'] ?? null,
        ]);
    }

    /**
     * Update product
     */
    public function updateProduct(int $id, array $data): bool
    {
        $product = SanPham::active()->find($id);
        if (!$product) {
            return false;
        }
        
        return $product->update([
            'maSP' => $data['maSP'] ?? $product->maSP,
            'tenSP' => $data['tenSP'],
            'id_danhmuc' => $data['id_danhmuc'],
            'moTa' => $data['moTa'] ?? $product->moTa,
            'trangthai' => $data['trangthai'] ?? $product->trangthai,
            'base_price' => $data['base_price'] ?? $product->base_price,
            'base_sale_price' => $data['base_sale_price'] ?? $product->base_sale_price,
        ]);
    }

    /**
     * Delete product (soft delete)
     */
    public function deleteProduct(int $id): bool
    {
        $product = SanPham::active()->find($id);
        if (!$product) {
            return false;
        }
        
        return $product->delete();
    }

    /**
     * Restore a soft-deleted product
     */
    public function restoreProduct(int $id): bool
    {
        $product = SanPham::withTrashed()->find($id);
        if (!$product || $product->deleted_at === null) {
            return false;
        }
        return (bool) $product->restore();
    }

    /**
     * Permanently delete a product
     */
    public function forceDeleteProduct(int $id): bool
    {
        $product = SanPham::withTrashed()->find($id);
        if (!$product) {
            return false;
        }
        return (bool) $product->forceDelete();
    }

    /**
     * Create product variants
     */
    public function createProductVariants(int $productId, array $variants): void
    {
        foreach ($variants as $variant) {
            if (!empty($variant['sizes'])) {
                foreach ($variant['sizes'] as $size) {
                    DB::table('chitietsanpham')->insert([
                        'id_sp' => $productId,
                        'tenSp' => $variant['ten'],
                        'id_mausac' => $variant['mausac'],
                        'id_size' => $size['size'],
                        'soLuong' => $size['so_luong'],
                        'gia' => $size['gia'],
                        'gia_khuyenmai' => $size['gia_khuyenmai'] ?? null,
                    ]);
                }
            }
        }
    }

    /**
     * Update product variants
     */
    public function updateProductVariants(int $productId, array $variants): void
    {
        // Delete existing variants
        DB::table('chitietsanpham')
            ->where('id_sp', $productId)
            ->update(['deleted_at' => now()]);
        
        // Create new variants
        $this->createProductVariants($productId, $variants);
    }
}
