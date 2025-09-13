<?php

namespace App\Services;

use App\Repositories\ApiProductRepository;
use App\Models\SanPham;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ApiProductService
{
    protected $apiProductRepository;

    public function __construct(ApiProductRepository $apiProductRepository)
    {
        $this->apiProductRepository = $apiProductRepository;
    }

    /**
     * Get products with pagination and filters
     */
    public function getProducts(array $filters = [], int $perPage = 10, bool $returnAll = false): array
    {
        $result = $this->apiProductRepository->getProducts($filters, $perPage, $returnAll);
        
        if ($returnAll) {
            return [
                'data' => $result,
                'pagination' => null,
            ];
        }
        
        $currentPage = $result->currentPage();
        $lastPage = $result->lastPage();
        $pages = $lastPage > 0 ? range(1, $lastPage) : [];
        
        return [
            'data' => $result->items(),
            'pagination' => [
                'current_page' => $currentPage,
                'per_page' => $result->perPage(),
                'total' => $result->total(),
                'last_page' => $lastPage,
                'prev_page' => $currentPage > 1 ? $currentPage - 1 : null,
                'next_page' => $currentPage < $lastPage ? $currentPage + 1 : null,
                'prev_url' => $currentPage > 1 ? $result->url($currentPage - 1) : null,
                'next_url' => $currentPage < $lastPage ? $result->url($currentPage + 1) : null,
                'first_url' => $lastPage > 0 ? $result->url(1) : null,
                'last_url' => $lastPage > 0 ? $result->url($lastPage) : null,
                'pages' => $pages,
                'path' => $result->path(),
            ],
        ];
    }

    /**
     * Get product by ID
     */
    public function getProductById(int $id): ?SanPham
    {
        return $this->apiProductRepository->getProductById($id);
    }

    /**
     * Create new product
     */
    public function createProduct(array $data): SanPham
    {
        return DB::transaction(function () use ($data) {
            // Normalize prices
            $data['base_price'] = $this->normalizePrice($data['base_price'] ?? null);
            $data['base_sale_price'] = $this->normalizePrice($data['base_sale_price'] ?? null);
            
            // Set default status if not provided
            $data['trangthai'] = $data['trangthai'] ?? 1;
            
            $product = $this->apiProductRepository->createProduct($data);
            
            // Handle variants if provided
            if (!empty($data['variants'])) {
                $this->apiProductRepository->createProductVariants($product->id, $data['variants']);
            }
            
            return $this->apiProductRepository->getProductById($product->id);
        });
    }

    /**
     * Update product
     */
    public function updateProduct(int $id, array $data): ?SanPham
    {
        return DB::transaction(function () use ($id, $data) {
            // Normalize prices if provided
            if (array_key_exists('base_price', $data)) {
                $data['base_price'] = $this->normalizePrice($data['base_price']);
            }
            if (array_key_exists('base_sale_price', $data)) {
                $data['base_sale_price'] = $this->normalizePrice($data['base_sale_price']);
            }
            
            $updated = $this->apiProductRepository->updateProduct($id, $data);
            if (!$updated) {
                return null;
            }
            
            // Handle variants if provided
            if (!empty($data['variants'])) {
                $this->apiProductRepository->updateProductVariants($id, $data['variants']);
            }
            
            return $this->apiProductRepository->getProductById($id);
        });
    }

    /**
     * Delete product
     */
    public function deleteProduct(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            // Check if product exists and is not deleted
            $product = SanPham::active()->find($id);
            if (!$product) {
                return false;
            }
            
            // Soft delete related data first
            $this->softDeleteProductRelatedData($id);
            
            // Soft delete the product
            return $this->apiProductRepository->deleteProduct($id);
        });
    }

    /**
     * Restore a soft-deleted product
     */
    public function restoreProduct(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $restored = $this->apiProductRepository->restoreProduct($id);
            if (!$restored) {
                return false;
            }

            // Restore related data that was soft deleted together with product
            try {
                // Restore product images
                DB::table('sanpham_hinhanh')
                    ->where('sanpham_id', $id)
                    ->update(['deleted_at' => null]);

                // Restore product variants/details
                DB::table('chitietsanpham')
                    ->where('id_sp', $id)
                    ->update(['deleted_at' => null]);

                // Restore comments
                DB::table('binhluan')
                    ->where('id_sp', $id)
                    ->update(['deleted_at' => null]);
            } catch (\Exception $e) {
                Log::error("Error restoring related data for product {$id}: " . $e->getMessage());
                throw $e;
            }

            return true;
        });
    }

    /**
     * Permanently delete a product (hard delete)
     */
    public function forceDeleteProduct(int $id)
    {
        return DB::transaction(function () use ($id) {
            // Check referencing order details to avoid breaking historical data
            $detailIds = DB::table('chitietsanpham')
                ->where('id_sp', $id)
                ->pluck('id')
                ->toArray();

            if (!empty($detailIds)) {
                $orderDetailCount = DB::table('chitietdonhang')
                    ->whereIn('id_chitietsanpham', $detailIds)
                    ->count();
                if ($orderDetailCount > 0) {
                    // Cannot hard delete when order details exist
                    return [
                        'ok' => false,
                        'blocked_by_orders' => true,
                        'order_detail_count' => $orderDetailCount,
                    ];
                }
            }

            // Delete related data first
            DB::table('danhgia')->whereIn('id_chitietdonhang', function ($q) use ($detailIds) {
                $q->select('id')->from('chitietdonhang')->whereIn('id_chitietsanpham', $detailIds);
            })->delete();
            DB::table('binhluan')->where('id_sp', $id)->delete();
            DB::table('sanpham_hinhanh')->where('sanpham_id', $id)->delete();
            if (Schema::hasTable('sanpham_mausac_images')) {
                DB::table('sanpham_mausac_images')->where('sanpham_id', $id)->delete();
            }
            DB::table('chitietsanpham')->where('id_sp', $id)->delete();

            $deleted = $this->apiProductRepository->forceDeleteProduct($id);
            if (!$deleted) {
                return false; // not found
            }
            return ['ok' => true];
        });
    }

    /**
     * Soft delete product related data
     */
    private function softDeleteProductRelatedData(int $productId): void
    {
        try {
            // Get all product detail IDs
            $chiTietSanPhamIds = DB::table('chitietsanpham')
                ->where('id_sp', $productId)
                ->pluck('id')
                ->toArray();
            
            if (!empty($chiTietSanPhamIds)) {
                // Get order detail IDs that reference these product details
                $chitietDonHangIds = DB::table('chitietdonhang')
                    ->whereIn('id_chitietsanpham', $chiTietSanPhamIds)
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($chitietDonHangIds)) {
                    // Soft delete ratings
                    DB::table('danhgia')
                        ->whereIn('id_chitietdonhang', $chitietDonHangIds)
                        ->update(['deleted_at' => now()]);
                    
                    // Soft delete order details
                    DB::table('chitietdonhang')
                        ->whereIn('id_chitietsanpham', $chiTietSanPhamIds)
                        ->update(['deleted_at' => now()]);
                }
                
                // Soft delete product details
                DB::table('chitietsanpham')
                    ->where('id_sp', $productId)
                    ->update(['deleted_at' => now()]);
            }
            
            // Soft delete comments
            DB::table('binhluan')
                ->where('id_sp', $productId)
                ->update(['deleted_at' => now()]);
            
            // Soft delete product images
            DB::table('sanpham_hinhanh')
                ->where('sanpham_id', $productId)
                ->update(['deleted_at' => now()]);
            
        } catch (\Exception $e) {
            Log::error("Error soft deleting related data for product {$productId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Normalize price input: remove separators, cast to float, clamp to DECIMAL(15,2) range
     */
    private function normalizePrice($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        // Convert strings like "1,234,567.89" or "1 234 567" to numeric
        if (is_string($value)) {
            $value = str_replace([',', ' '], ['', ''], $value);
        }
        
        if (!is_numeric($value)) {
            return null;
        }
        
        $num = (float) $value;
        if ($num < 0) { 
            $num = 0.0; 
        }
        
        // DECIMAL(15,2): max 9,999,999,999,999.99 (13 digits before decimal)
        $max = 9999999999999.99;
        if ($num > $max) { 
            $num = $max; 
        }
        
        // Round to 2 decimals to match schema
        return round($num, 2);
    }
}
