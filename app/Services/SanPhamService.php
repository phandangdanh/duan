<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\Interfaces\SanPhamServiceInterface;
use App\Repositories\Interfaces\SanPhamRepositoryInterface;
use App\Models\DanhMuc;
use App\Models\SanPham;
use App\Models\SanPhamHinhanh;

class SanPhamService implements SanPhamServiceInterface
{
    public function __construct(private SanPhamRepositoryInterface $repo) {}

    public function create(array $payload): int
    {
        return DB::transaction(function () use ($payload) {
            // Normalize base prices to avoid DECIMAL overflow
            $normalizedBasePrice = $this->normalizePrice($payload['base_price'] ?? null);
            $normalizedBaseSale  = $this->normalizePrice($payload['base_sale_price'] ?? null);

            $productId = $this->repo->createProduct([
                'maSP'       => $payload['maSP'] ?? null,
                'tenSP'      => $payload['tenSP'],
                'id_danhmuc' => $payload['id_danhmuc'],
                'moTa'       => $payload['moTa'] ?? null,
                'trangthai'  => $payload['status'] ?? 1,
                'base_price' => $normalizedBasePrice,
                'base_sale_price' => $normalizedBaseSale,
                'soLuong'    => $payload['soLuong'] ?? 1,
            ]);

            if (!empty($payload['variants'])) {
                $this->repo->insertDetails($productId, $payload['variants']);
            }

            if (!empty($payload['image_main_path'])) {
                $this->repo->saveMainImage($productId, $payload['image_main_path']);
            }
            if (!empty($payload['image_extra_paths'])) {
                $this->repo->saveExtraImages($productId, $payload['image_extra_paths']);
            }

            return $productId;
        });
    }

    public function update(int $id, array $payload): void
    {
        DB::transaction(function () use ($id, $payload) {
            // Kiểm tra sản phẩm có tồn tại không (chỉ sản phẩm chưa xóa)
            $sanpham = SanPham::active()->findOrFail($id);
            
            // Normalize before update
            $normalizedBasePrice = array_key_exists('base_price', $payload) ? $this->normalizePrice($payload['base_price']) : null;
            $normalizedBaseSale  = array_key_exists('base_sale_price', $payload) ? $this->normalizePrice($payload['base_sale_price']) : null;

            DB::table('sanpham')->where('id', $id)->whereNull('deleted_at')->update([
                'maSP'       => $payload['maSP'] ?? null,
                'tenSP'      => $payload['tenSP'],
                'id_danhmuc' => $payload['id_danhmuc'],
                'moTa'       => $payload['moTa'] ?? null,
                'trangthai'  => $payload['status'] ?? DB::raw('trangthai'),
                'base_price' => array_key_exists('base_price', $payload) ? $normalizedBasePrice : DB::raw('base_price'),
                'base_sale_price' => array_key_exists('base_sale_price', $payload) ? $normalizedBaseSale : DB::raw('base_sale_price'),
                'soLuong'    => array_key_exists('soLuong', $payload) ? ($payload['soLuong'] ?? 1) : DB::raw('soLuong'),
            ]);

            if (!empty($payload['variants'])) {
                $this->repo->clearDetails($id);
                $this->repo->insertDetails($id, $payload['variants']);
            }

            if (!empty($payload['image_main_path'])) {
                // Xóa ảnh chính cũ (không chỉ set is_default = 0)
                $oldMainImages = DB::table('sanpham_hinhanh')->where('sanpham_id', $id)->where('is_default', 1)->get();
                foreach ($oldMainImages as $oldImage) {
                    // Xóa file vật lý
                    $filePath = public_path($oldImage->url);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    // Xóa record khỏi database
                    DB::table('sanpham_hinhanh')->where('id', $oldImage->id)->delete();
                }
                // Lưu ảnh chính mới
                $this->repo->saveMainImage($id, $payload['image_main_path']);
            }
            if (!empty($payload['image_extra_paths'])) {
                $this->repo->saveExtraImages($id, $payload['image_extra_paths']);
            }

            // Xử lý xóa ảnh đã đánh dấu
            if (!empty($payload['deleted_images'])) {
                Log::info('SanPhamService update: About to delete images: ' . json_encode($payload['deleted_images']));
                $this->deleteImages($payload['deleted_images']);
                Log::info('SanPhamService update: Image deletion completed');
            } else {
                Log::info('SanPhamService update: No images to delete');
            }
        });
    }

    public function list(array $filters): array
    {
        try {
            $validPerPages = [12, 24, 48, 'all'];
            $perPage = $filters['perpage'] ?? 12;
            
            // Xử lý perpage đúng cách
            if (is_string($perPage) && $perPage === 'all') {
                // Giữ nguyên 'all'
            } elseif (is_numeric($perPage) || is_string($perPage) && is_numeric($perPage)) {
                $perPage = (int) $perPage;
                if (!in_array($perPage, [12, 24, 48], true)) {
                    $perPage = 12;
                }
            } else {
                $perPage = 12;
            }
            


            $result = $this->repo->listWithFilters($filters, $perPage);

            $danhmucs = DanhMuc::orderBy('name')->get();

            // Tính toán thống kê chỉ cho sản phẩm chưa xóa
            // Thống kê theo SẢN PHẨM (bám sát logic lọc trong Repository) – THEO BỘ LỌC HIỆN TẠI
            $baseProducts = DB::table('sanpham')->whereNull('sanpham.deleted_at');

            // Áp các bộ lọc giống repository
            if (!empty($filters['search']) && trim($filters['search']) !== '') {
                $keyword = trim($filters['search']);
                $baseProducts->where(function($q) use ($keyword){
                    $q->where('sanpham.tenSP', 'LIKE', "%{$keyword}%")
                      ->orWhere('sanpham.maSP', 'LIKE', "%{$keyword}%")
                      ->orWhere('sanpham.moTa', 'LIKE', "%{$keyword}%");
                });
            }
            if (!empty($filters['category'])) {
                $baseProducts->where('id_danhmuc', (int) $filters['category']);
            }
            if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null) {
                $baseProducts->where('trangthai', (int) $filters['status']);
            }

            // Đếm theo điều kiện tồn kho, dùng whereExists/notExists giống phần danh sách
            $inStockCount = (clone $baseProducts)
                ->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('chitietsanpham')
                        ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                        ->where('chitietsanpham.soLuong', '>', 0);
                })->count();

            $outOfStockCount = (clone $baseProducts)
                ->where(function ($q) {
                    $q->where(function ($subQ) {
                        $subQ->whereExists(function ($sub) {
                            $sub->select(DB::raw(1))
                                ->from('chitietsanpham')
                                ->whereColumn('sanpham.id', 'chitietsanpham.id_sp');
                        })->whereNotExists(function ($sub) {
                            $sub->select(DB::raw(1))
                                ->from('chitietsanpham')
                                ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                                ->where('chitietsanpham.soLuong', '>', 0);
                        });
                    })->orWhere(function ($subQ) {
                        $subQ->whereNotExists(function ($sub) {
                            $sub->select(DB::raw(1))
                                ->from('chitietsanpham')
                                ->whereColumn('sanpham.id', 'chitietsanpham.id_sp');
                        });
                    });
                })->count();

            // Low stock: tổng tồn 1..9 (ước lượng bằng: có tồn >0 và không có dòng tồn >=10; đơn giản hơn: tổng <10 dùng subquery SUM)
            $productStockTotals = DB::table('sanpham')
                ->whereNull('sanpham.deleted_at')
                ->when(!empty($filters['search']) && trim($filters['search']) !== '', function ($q) use ($filters) {
                    $keyword = trim($filters['search']);
                    $q->where(function($qq) use ($keyword){
                        $qq->where('sanpham.tenSP', 'LIKE', "%{$keyword}%")
                           ->orWhere('sanpham.maSP', 'LIKE', "%{$keyword}%")
                           ->orWhere('sanpham.moTa', 'LIKE', "%{$keyword}%");
                    });
                })
                ->when(!empty($filters['category']), fn($q) => $q->where('id_danhmuc', (int)$filters['category']))
                ->when(isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null, fn($q) => $q->where('trangthai', (int)$filters['status']))
                ->leftJoin('chitietsanpham', 'sanpham.id', '=', 'chitietsanpham.id_sp')
                ->groupBy('sanpham.id')
                ->select('sanpham.id', DB::raw('COALESCE(SUM(chitietsanpham.soLuong),0) as total_stock_per_product'));

            $lowStockCount = DB::query()->fromSub($productStockTotals, 't')
                ->where('t.total_stock_per_product', '>', 0)
                ->where('t.total_stock_per_product', '<', 10)
                ->count();

            $stats = [
                // Các số liệu theo bộ lọc hiện tại
                'total' => (clone $baseProducts)->count(),
                'active' => (clone $baseProducts)->where('trangthai', 1)->count(),
                'inactive' => (clone $baseProducts)->where('trangthai', 0)->count(),
                'avg_price' => DB::table('chitietsanpham')->avg('gia') ?? 0,
                // Tổng số lượng tồn kho (cộng tất cả biến thể)
                'total_stock' => DB::table('chitietsanpham')->sum('soLuong') ?? 0,
                // Số SẢN PHẨM sắp hết hàng (tổng tồn 1..9)
                'low_stock' => $lowStockCount,
                // Số SẢN PHẨM còn hàng (tổng tồn > 0)
                'in_stock' => $inStockCount,
                // Số SẢN PHẨM hết hàng (tổng tồn = 0)
                'out_of_stock' => $outOfStockCount,
            ];

            return [
                'items' => $result['items'] ?? collect([]),
                'pagination' => $result['pagination'] ?? null,
                'danhmucs' => $danhmucs,
                'stats' => $stats,
            ];
        } catch (\Exception $e) {
            Log::error('SanPhamService list error: ' . $e->getMessage());
            
            // Trả về dữ liệu mặc định khi có lỗi
            return [
                'items' => collect([]),
                'pagination' => null,
                'danhmucs' => DanhMuc::orderBy('name')->get(),
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'avg_price' => 0,
                    'total_stock' => 0,
                ]
            ];
        }
    }

    public function getCreateData(): array
    {
        $allCategories = DanhMuc::orderBy('parent_id')->orderBy('name')->get();
        $danhmucs = $this->buildCategoryOptions($allCategories);
        $mausacs = DB::table('mausac')->select('id', 'ten as name')->orderBy('ten')->get();
        $sizes = DB::table('size')->select('id', 'ten as name')->orderBy('ten')->get();
        return compact('danhmucs', 'mausacs', 'sizes');
    }

    public function getEditData(int $id): array
    {
        $sanpham = SanPham::active()->with(['hinhanh' => function($query) {
            $query->whereNull('deleted_at');
        }])->findOrFail($id);
        $allCategories = DanhMuc::orderBy('parent_id')->orderBy('name')->get();
        $danhmucs = $this->buildCategoryOptions($allCategories);
        $mausacs = DB::table('mausac')->select('id', 'ten as name')->orderBy('ten')->get();
        $sizes = DB::table('size')->select('id', 'ten as name')->orderBy('ten')->get();

        $detailRows = DB::table('chitietsanpham')
            ->where('id_sp', $sanpham->id)
            ->whereExists(function ($query) use ($sanpham) {
                $query->select(DB::raw(1))
                      ->from('sanpham')
                      ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                      ->where('sanpham.id', $sanpham->id)
                      ->whereNull('sanpham.deleted_at');
            })
            ->get();
        $variantsMap = [];
        foreach ($detailRows as $row) {
            $variantKey = (string)($row->id_mausac ?? 'null');
            if (!isset($variantsMap[$variantKey])) {
                $variantsMap[$variantKey] = [
                    'ten' => $row->tenSp,
                    'mausac' => $row->id_mausac,
                    'sizes' => [],
                ];
            }
            $variantsMap[$variantKey]['sizes'][] = [
                'size' => $row->id_size,
                'so_luong' => $row->soLuong,
                'gia' => $row->gia,
                'gia_khuyenmai' => $row->gia_khuyenmai,
            ];
        }
        $variants = array_values($variantsMap);

        return compact('sanpham', 'danhmucs', 'mausacs', 'sizes', 'variants');
    }

    /**
     * Build hierarchical category options with prefix for depth
     */
    private function buildCategoryOptions($allCategories, int $parentId = 0, string $prefix = ''): array
    {
        $options = [];
        foreach ($allCategories->where('parent_id', $parentId) as $cat) {
            $options[] = (object) [
                'id' => $cat->id,
                'name' => $prefix . $cat->name,
            ];
            $children = $this->buildCategoryOptions($allCategories, (int)$cat->id, $prefix . '— ');
            $options = array_merge($options, $children);
        }
        return $options;
    }

    public function toggle(int $id): int
    {
        return $this->repo->toggleStatus($id);
    }

    public function bulk(string $action, array $ids): array
    {
        return DB::transaction(function () use ($action, $ids) {
            if ($action === 'activate' || $action === 'status_1') {
                $affected = $this->repo->bulkUpdateStatus($ids, 1);
                $newStatus = 1;
                $updated = [];
                foreach ($ids as $id) { $updated[$id] = $newStatus; }
                return ['updated' => $updated, 'message' => 'Cập nhật trạng thái kinh doanh thành công cho ' . count($ids) . ' sản phẩm!'];
            }
            if ($action === 'deactivate' || $action === 'status_0') {
                $affected = $this->repo->bulkUpdateStatus($ids, 0);
                $newStatus = 0;
                $updated = [];
                foreach ($ids as $id) { $updated[$id] = $newStatus; }
                return ['updated' => $updated, 'message' => 'Cập nhật trạng thái ngừng kinh doanh thành công cho ' . count($ids) . ' sản phẩm!'];
            }
            if ($action === 'delete') {
                $this->repo->bulkDelete($ids);
                $updated = [];
                foreach ($ids as $id) { $updated[$id] = 'deleted'; }
                return ['updated' => $updated, 'message' => 'Xóa thành công ' . count($ids) . ' sản phẩm!'];
            }
            return ['updated' => [], 'message' => 'Hành động không hợp lệ!'];
        });
    }

    public function getStatistics(): array
    {
        return $this->repo->getStatistics();
    }

    public function getById(int $id): SanPham
    {
        return $this->repo->getByIdWithRelations($id);
    }

    /**
     * Xóa các ảnh đã đánh dấu
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            Log::info('deleteImages: No images to delete');
            return;
        }

        Log::info('deleteImages: Deleting images with IDs: ' . implode(', ', $imageIds));

        foreach ($imageIds as $imageId) {
            // Tìm ảnh bao gồm cả đã xóa mềm
            $image = SanPhamHinhanh::withTrashed()->find($imageId);
            if ($image) {
                Log::info("deleteImages: Found image ID {$imageId}, URL: {$image->url}, deleted_at: {$image->deleted_at}");
                
                // Xóa file vật lý
                $filePath = public_path($image->url);
                if (file_exists($filePath)) {
                    unlink($filePath);
                    Log::info("deleteImages: Deleted physical file: {$filePath}");
                } else {
                    Log::warning("deleteImages: Physical file not found: {$filePath}");
                }
                
                // Xóa vĩnh viễn record trong database
                $image->forceDelete();
                Log::info("deleteImages: Force deleted database record for image ID {$imageId}");
            } else {
                Log::warning("deleteImages: Image not found with ID: {$imageId}");
            }
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
        if ($num < 0) { $num = 0.0; }
        // DECIMAL(15,2): max 9,999,999,999,999.99 (13 digits before decimal)
        $max = 9999999999999.99;
        if ($num > $max) { $num = $max; }
        // Round to 2 decimals to match schema
        return round($num, 2);
    }
}


