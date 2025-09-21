<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\SanPhamRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\SanPham;

class SanPhamRepository implements SanPhamRepositoryInterface
{
    public function createProduct(array $data): int
    {
        // Đảm bảo sản phẩm mới được tạo với deleted_at = null
        $data['deleted_at'] = null;
        return DB::table('sanpham')->insertGetId($data);
    }

    public function insertDetails(int $productId, array $variants, array $defaults = []): void
    {
        // Kiểm tra sản phẩm có tồn tại không (chỉ sản phẩm chưa xóa)
        $sanpham = SanPham::active()->findOrFail($productId);
        
        $basePrice = $defaults['base_price'] ?? null;
        $baseSale  = $defaults['base_sale_price'] ?? null;

        foreach ($variants as $variant) {
            if (empty($variant['sizes'])) { continue; }
            foreach ($variant['sizes'] as $sizeVariant) {
                $price = isset($sizeVariant['gia']) && $sizeVariant['gia'] !== '' ? $sizeVariant['gia'] : ($basePrice ?? 0);
                $sale  = isset($sizeVariant['gia_khuyenmai']) && $sizeVariant['gia_khuyenmai'] !== '' ? $sizeVariant['gia_khuyenmai'] : ($baseSale ?? 0);
                DB::table('chitietsanpham')->insert([
                    'id_sp'         => $productId,
                    'id_mausac'     => $variant['mausac'] ?? null,
                    'id_size'       => $sizeVariant['size'],
                    'soLuong'       => $sizeVariant['so_luong'] ?? 0,
                    'tenSp'         => $variant['ten'] ?? null,
                    'gia'           => $price,
                    'gia_khuyenmai' => $sale,
                ]);
            }
        }
    }

        public function saveMainImage(int $productId, string $relativeUrl): void
    {
        // Kiểm tra sản phẩm có tồn tại không (chỉ sản phẩm chưa xóa)
        $sanpham = SanPham::active()->findOrFail($productId);
        
        DB::table('sanpham_hinhanh')->insert([
            'sanpham_id' => $productId,
            'url'        => $relativeUrl,
            'is_default' => 1,
            'mota'       => 'Ảnh chính',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function saveExtraImages(int $productId, array $relativeUrls): void
    {
        // Kiểm tra sản phẩm có tồn tại không (chỉ sản phẩm chưa xóa)
        $sanpham = SanPham::active()->findOrFail($productId);
        
        foreach ($relativeUrls as $url) {
            DB::table('sanpham_hinhanh')->insert([
                'sanpham_id' => $productId,
                'url'        => $url,
                'is_default' => 0,
                'mota'       => 'Ảnh phụ',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function clearDetails(int $productId): void
    {
        // Chỉ xóa chi tiết của sản phẩm chưa xóa
        DB::table('chitietsanpham')
            ->where('id_sp', $productId)
            ->whereExists(function ($query) use ($productId) {
                $query->select(DB::raw(1))
                      ->from('sanpham')
                      ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                      ->where('sanpham.id', $productId)
                      ->whereNull('sanpham.deleted_at');
            })
            ->delete();
    }

    public function listWithFilters(array $filters, $perPage): array
    {
        try {
            // Bật log truy vấn để debug khi người dùng báo không có kết quả
            DB::enableQueryLog();

            // Ghi log bộ lọc đầu vào để đối chiếu
            try { Log::info('SanPham search filters', $filters); } catch (\Throwable $e) {}

            // Chỉ lấy sản phẩm CHƯA xóa mềm cho trang danh sách chính
            $query = SanPham::active()->with(['danhmuc', 'hinhanh', 'chitietsanpham']);

            // từ khóa tìm kiếm (accent-insensitive & case-insensitive)
            $hasKeyword = !empty($filters['search']) && trim($filters['search']) !== '';
            $keyword = $hasKeyword ? trim($filters['search']) : '';
            
            if ($hasKeyword) {
                // Hạ chữ về lowercase để tránh phụ thuộc collation (nếu cột dùng *_bin sẽ phân biệt hoa/thường)
                $kwLower = mb_strtolower($keyword, 'UTF-8');

                // Chuẩn hóa ký tự có dấu sang không dấu ở PHÍA TỪ KHÓA (best-effort)
                $vn = 'àáảãạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệìíỉĩịòóỏõọôồốổỗộơờớởỡợùúủũụưừứửữựỳýỷỹỵđÀÁẢÃẠĂẰẮẲẴẶÂẦẤẨẪẬÈÉẺẼẸÊỀẾỂỄỆÌÍỈĨỊÒÓỎÕỌÔỒỐỔỖỘƠỜỚỞỠỢÙÚỦŨỤƯỪỨỬỮỰỲÝỶỸỴĐ';
                $en = 'aaaaaaaaaaaaaaaaaeeeeeeeeeeeiiiiiooooooooooooooooouuuuuuuuuuuyyyyydAAAAAAAAAAAAAAAAAEEEEEEEEEEEIIIIIoooooooooooooooooUUUUUUUUUUYYYYYD';
                $map = [];
                for ($i = 0; $i < mb_strlen($vn, 'UTF-8'); $i++) {
                    $map[mb_substr($vn, $i, 1, 'UTF-8')] = mb_substr($en, $i, 1, 'UTF-8');
                }
                $keywordNoAccent = strtr($keyword, $map);
                $kwNoAccentLower = mb_strtolower($keywordNoAccent, 'UTF-8');

                $query->where(function ($q) use ($kwLower, $kwNoAccentLower) {
                    // So khớp không phân biệt hoa/thường
                    $q->orWhereRaw('LOWER(tenSP) LIKE ?', ["%{$kwLower}%"]) 
                      ->orWhereRaw('LOWER(maSP) LIKE ?', ["%{$kwLower}%"]) 
                      ->orWhereRaw('LOWER(moTa) LIKE ?', ["%{$kwLower}%"]) 
                      // So khớp thêm với từ khóa đã bỏ dấu (để tìm 'ư' ~ 'u')
                      ->orWhereRaw('LOWER(tenSP) LIKE ?', ["%{$kwNoAccentLower}%"]) 
                      ->orWhereRaw('LOWER(maSP) LIKE ?', ["%{$kwNoAccentLower}%"]) 
                      ->orWhereRaw('LOWER(moTa) LIKE ?', ["%{$kwNoAccentLower}%"]);
                });
            }

            // lọc theo danh mục (bỏ qua khi có keyword)
            if (!$hasKeyword && !empty($filters['category']) && $filters['category'] !== '' && $filters['category'] !== null) {
                $query->where('id_danhmuc', (int) $filters['category']);
            }

            // lọc theo trạng thái (bỏ qua khi có keyword)
            if (!$hasKeyword && isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null) {
                $query->where('trangthai', (int) $filters['status']);
            }

            // lọc sản phẩm có hình ảnh
            if (!$hasKeyword && !empty($filters['has_image']) && $filters['has_image']) {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('sanpham_hinhanh')
                        ->whereColumn('sanpham.id', 'sanpham_hinhanh.sanpham_id')
                        ->whereNull('sanpham_hinhanh.deleted_at');
                });
            }

            // lọc sản phẩm đang khuyến mãi (có base_sale_price < base_price)
            if (!$hasKeyword && !empty($filters['on_sale']) && $filters['on_sale']) {
                $query->whereRaw('base_sale_price < base_price AND base_sale_price > 0');
            }

            // lọc theo tồn kho (bỏ qua khi có keyword)
            if (!$hasKeyword && !empty($filters['stock']) && $filters['stock'] !== '' && $filters['stock'] !== null) {
                if ($filters['stock'] === 'in_stock') {
                    // Sản phẩm còn hàng: có ít nhất 1 chi tiết sản phẩm có số lượng > 0
                    $query->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('chitietsanpham')
                            ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                            ->where('chitietsanpham.soLuong', '>', 0)
                            ->whereNull('chitietsanpham.deleted_at');
                    });
                } elseif ($filters['stock'] === 'out_of_stock') {
                    // Sản phẩm hết hàng: có chi tiết sản phẩm nhưng tất cả đều có số lượng <= 0
                    // HOẶC không có chi tiết sản phẩm nào (coi như hết hàng)
                    $query->where(function ($q) {
                        $q->where(function ($subQ) {
                            // Trường hợp 1: Có chi tiết sản phẩm nhưng tất cả đều hết hàng
                            $subQ->whereExists(function ($sub) {
                                $sub->select(DB::raw(1))
                                    ->from('chitietsanpham')
                                    ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                                    ->whereNull('chitietsanpham.deleted_at');
                            })->whereNotExists(function ($sub) {
                                $sub->select(DB::raw(1))
                                    ->from('chitietsanpham')
                                    ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                                    ->where('chitietsanpham.soLuong', '>', 0)
                                    ->whereNull('chitietsanpham.deleted_at');
                            });
                        })->orWhere(function ($subQ) {
                            // Trường hợp 2: Không có chi tiết sản phẩm nào (coi như hết hàng)
                            $subQ->whereNotExists(function ($sub) {
                                $sub->select(DB::raw(1))
                                    ->from('chitietsanpham')
                                    ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                                    ->whereNull('chitietsanpham.deleted_at');
                            });
                        });
                    });
                }
            }

            if (!$hasKeyword && (!empty($filters['gia_min']) || !empty($filters['gia_max']))) {
                $query->whereExists(function ($sub) use ($filters) {
                    $sub->from('chitietsanpham')
                        ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                        ->when(!empty($filters['gia_min']), function ($q) use ($filters) {
                            $q->where('gia', '>=', (float) $filters['gia_min']);
                        })
                        ->when(!empty($filters['gia_max']), function ($q) use ($filters) {
                            $q->where('gia', '<=', (float) $filters['gia_max']);
                        });
                });
            }

            // Khi có keyword: luôn sắp xếp theo id để tránh join làm mất kết quả
            $sort = $hasKeyword ? 'id' : ($filters['sort'] ?? 'id');
            switch ($sort) {
                case 'tenSP':
                    $query->orderBy('tenSP', 'asc');
                    break;
                case 'tenSP_desc':
                    $query->orderBy('tenSP', 'desc');
                    break;
                case 'maSP':
                    $query->orderBy('maSP', 'asc');
                    break;
                case 'gia_asc':
                case 'gia_desc':
                    // Sắp xếp theo giá sẽ được xử lý sau khi load dữ liệu
                    $query->orderByDesc('id');
                    break;
                default:
                    $query->orderByDesc('id');
                    break;
            }

            
            if ($perPage === 'all') {
                // Log SQL trước khi chạy
                try {
                    Log::info('SanPham search (all) SQL', [
                        'sql' => $query->toSql(),
                        'bindings' => $query->getBindings(),
                        'keyword' => $keyword
                    ]);
                } catch (\Throwable $e) {}
                $items = $query->get();
                return ['items' => $items, 'pagination' => null];
            }

            // Đảm bảo perPage là số nguyên
            $perPage = (int) $perPage;
            if ($perPage <= 0) {
                $perPage = 12; // Giá trị mặc định
            }

            try {
                // Kiểm tra nếu cần sắp xếp theo giá
                $needsPriceSorting = in_array($filters['sort'] ?? '', ['gia_asc', 'gia_desc']);
                
                if ($needsPriceSorting) {
                    // Lấy tất cả sản phẩm và sắp xếp theo giá
                    $allItems = $query->get();
                    
                    // Load relations cho tất cả sản phẩm
                    $allItems->load(['danhmuc', 'hinhanh', 'chitietsanpham']);
                    
                    // Sắp xếp theo giá
                    $sortedItems = $allItems->sortBy(function($product) use ($filters) {
                        $basePrice = $product->base_price ?? 0;
                        if ($basePrice > 0) {
                            return $basePrice;
                        }
                        
                        // Fallback về variant prices
                        $variantPrices = $product->chitietsanpham
                            ->map(function($d){
                                $price = $d->gia_khuyenmai && $d->gia_khuyenmai > 0 ? $d->gia_khuyenmai : $d->gia;
                                return is_null($price) ? 0 : (float)$price;
                            })
                            ->filter(function($v){ return $v > 0; });
                            
                        return $variantPrices->min() ?? 0;
                    });
                    
                    // Đảo ngược nếu là giảm dần
                    if (($filters['sort'] ?? '') === 'gia_desc') {
                        $sortedItems = $sortedItems->reverse();
                    }
                    
                    // Tạo paginator thủ công
                    $currentPage = request('page', 1);
                    $offset = ($currentPage - 1) * $perPage;
                    $items = $sortedItems->slice($offset, $perPage);
                    
                    $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                        $items,
                        $sortedItems->count(),
                        $perPage,
                        $currentPage,
                        [
                            'path' => request()->url(),
                            'pageName' => 'page',
                        ]
                    );
                    $paginator->appends(array_filter($filters, fn($v) => $v !== null && $v !== ''));
                } else {
                    // Sắp xếp bình thường, sử dụng paginate
                    $totalMatched = (clone $query)->count();
                    Log::info('SanPham search (paginate) SQL', [
                        'sql' => $query->toSql(),
                        'bindings' => $query->getBindings(),
                        'keyword' => $keyword,
                        'total_matched' => $totalMatched
                    ]);
                    $paginator = $query->paginate($perPage);
                    $paginator->appends(array_filter($filters, fn($v) => $v !== null && $v !== ''));
                }
            } catch (\Throwable $e) {
                Log::error('SanPhamRepository pagination error: ' . $e->getMessage());
                // Fallback: lấy tất cả và tạo paginator thủ công
                $allItems = $query->get();
                $currentPage = request('page', 1);
                $offset = ($currentPage - 1) * $perPage;
                $items = $allItems->slice($offset, $perPage);
                
                $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                    $items,
                    $allItems->count(),
                    $perPage,
                    $currentPage,
                    [
                        'path' => request()->url(),
                        'pageName' => 'page',
                    ]
                );
                $paginator->appends(array_filter($filters, fn($v) => $v !== null && $v !== ''));
            }
            
            return ['items' => $paginator, 'pagination' => $paginator];
            
        } catch (\Exception $e) {
            Log::error('SanPhamRepository listWithFilters error: ' . $e->getMessage());
            
            // Trả về dữ liệu mặc định khi có lỗi
            return [
                'items' => collect([]),
                'pagination' => null
            ];
        }
    }

    public function toggleStatus(int $id): int
    {
        $product = SanPham::active()->findOrFail($id);
        $product->trangthai = $product->trangthai == 1 ? 0 : 1;
        $product->save();
        return (int) $product->trangthai;
    }

    public function bulkUpdateStatus(array $ids, int $status): int
    {
        return SanPham::active()->whereIn('id', $ids)->update(['trangthai' => $status]);
    }

    public function bulkDelete(array $ids): void
    {
        // Lấy sản phẩm chưa xóa với hình ảnh để xóa file
        $sanphams = SanPham::active()->with(['hinhanh'])->whereIn('id', $ids)->get();
        
        foreach ($sanphams as $sanpham) {
            // Xóa hình ảnh
            foreach ($sanpham->hinhanh as $hinhanh) {
                $absolute = public_path($hinhanh->url ?? '');
                if ($absolute && file_exists($absolute)) {
                    @unlink($absolute);
                }
            }
            
            $sanpham->delete();
        }
    }

    public function getByIdWithRelations(int $id): SanPham
    {
        return SanPham::active()->with(['danhmuc', 'hinhanh', 'chitietsanpham.mausac', 'chitietsanpham.size'])->findOrFail($id);
    }

    public function getStatistics(): array
    {
        $stats = [
            'total_products' => SanPham::active()->count(),
            'active_products' => SanPham::kinhDoanh()->count(),
            'inactive_products' => SanPham::ngungKinhDoanh()->count(),
            'avg_price' => DB::table('chitietsanpham')->avg('gia') ?? 0,
            'total_stock' => DB::table('chitietsanpham')->sum('soLuong') ?? 0,
            'low_stock' => DB::table('chitietsanpham')->where('soLuong', '<', 10)->count(),
            'out_of_stock' => DB::table('chitietsanpham')->where('soLuong', 0)->count(),
        ];

        try {
            $products_by_category = DB::table('sanpham')
                ->join('danhmuc', 'sanpham.id_danhmuc', '=', 'danhmuc.id')
                ->whereNull('sanpham.deleted_at') // Chỉ đếm sản phẩm chưa xóa
                ->select('danhmuc.name', DB::raw('count(*) as count'))
                ->groupBy('danhmuc.id', 'danhmuc.name')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            $products_by_category = [];
        }

        $recent_products = SanPham::active()->orderBy('id', 'desc')->limit(5)->get();

        $stats['products_by_category'] = $products_by_category;
        $stats['recent_products'] = $recent_products;
        return $stats;
    }
}


