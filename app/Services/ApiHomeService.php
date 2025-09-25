<?php

namespace App\Services;

use App\Models\SanPham;
use App\Models\DanhMuc;
use App\Services\SanPhamService;
use App\Services\DanhMucService;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service xử lý logic cho trang chủ
 */
class ApiHomeService
{
    protected $sanPhamService;
    protected $danhMucService;
    protected $statisticsService;

    public function __construct(
        SanPhamService $sanPhamService,
        DanhMucService $danhMucService,
        StatisticsService $statisticsService
    ) {
        $this->sanPhamService = $sanPhamService;
        $this->danhMucService = $danhMucService;
        $this->statisticsService = $statisticsService;
    }

    /**
     * Lấy tất cả dữ liệu trang chủ
     */
    public function getHomePageData()
    {
        try {
            return [
                'featured_products' => $this->getFeaturedProducts(8),
                'sale_products' => $this->getSaleProducts(6),
                'best_selling_products' => $this->getBestSellingProducts(6),
                'featured_categories' => $this->getFeaturedCategories(8),
                'statistics' => $this->getStatistics()
            ];
        } catch (\Exception $e) {
            Log::error('ApiHomeService getHomePageData error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy sản phẩm nổi bật
     * Sản phẩm mới nhất có chi tiết sản phẩm và đang kinh doanh
     */
    public function getFeaturedProducts($limit = 8)
    {
        try {
            $products = SanPham::with([
                'danhmuc:id,name,slug',
                'hinhanh' => function($q) {
                    $q->whereNull('deleted_at')->orderBy('is_default', 'desc');
                },
                'chitietsanpham' => function($q) {
                    $q->whereNull('deleted_at')
                      ->with(['mausac:id,ten,ma_mau', 'size:id,ten']);
                }
            ])
            ->where('trangthai', 1)
            ->whereNull('deleted_at')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('chitietsanpham')
                    ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                    ->whereNull('chitietsanpham.deleted_at');
            })
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

            return $this->formatProducts($products);
        } catch (\Exception $e) {
            Log::error('ApiHomeService getFeaturedProducts error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Lấy sản phẩm khuyến mãi
     * Sản phẩm có base_sale_price < base_price
     */
    public function getSaleProducts($limit = 6)
    {
        try {
            $filters = [
                'status' => 1,
                'sort' => 'gia_asc',
                'perpage' => $limit,
                'on_sale' => true
            ];
            
            $result = $this->sanPhamService->list($filters);
            $products = $result['items'] ?? collect([]);

            return $this->formatProducts($products);
        } catch (\Exception $e) {
            Log::error('ApiHomeService getSaleProducts error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Lấy sản phẩm bán chạy
     * Dựa trên tổng số lượng đã bán trong đơn hàng
     */
    public function getBestSellingProducts($limit = 6)
    {
        try {
            // Lấy sản phẩm bán chạy dựa trên tổng số lượng đã bán
            $bestSellingIds = DB::table('chitietdonhang')
                ->join('chitietsanpham', 'chitietdonhang.id_chitietsanpham', '=', 'chitietsanpham.id')
                ->join('sanpham', 'chitietsanpham.id_sp', '=', 'sanpham.id')
                ->whereNull('sanpham.deleted_at')
                ->where('sanpham.trangthai', 1)
                ->select('sanpham.id', DB::raw('SUM(chitietdonhang.soluong) as total_sold'))
                ->groupBy('sanpham.id')
                ->orderBy('total_sold', 'desc')
                ->limit($limit)
                ->pluck('sanpham.id')
                ->toArray();

            if (empty($bestSellingIds)) {
                // Nếu không có sản phẩm nào đã bán, lấy sản phẩm mới nhất có giá
                $products = SanPham::with([
                    'danhmuc:id,name,slug',
                    'hinhanh' => function($q) {
                        $q->whereNull('deleted_at')->orderBy('is_default', 'desc');
                    },
                    'chitietsanpham' => function($q) {
                        $q->whereNull('deleted_at')
                          ->with(['mausac:id,ten,ma_mau', 'size:id,ten']);
                    }
                ])
                ->where('trangthai', 1)
                ->whereNull('deleted_at')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('chitietsanpham')
                        ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                        ->whereNull('chitietsanpham.deleted_at');
                })
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get();
                
                return $this->formatProducts($products);
            }

            // Lấy thông tin sản phẩm bán chạy với quan hệ đầy đủ
            $products = SanPham::with([
                'danhmuc:id,name,slug',
                'hinhanh' => function($q) {
                    $q->whereNull('deleted_at')->orderBy('is_default', 'desc');
                },
                'chitietsanpham' => function($q) {
                    $q->whereNull('deleted_at')
                      ->with(['mausac:id,ten,ma_mau', 'size:id,ten']);
                }
            ])
            ->where('trangthai', 1)
            ->whereNull('deleted_at')
            ->whereIn('id', $bestSellingIds)
            ->get()
            ->sortBy(function($product) use ($bestSellingIds) {
                return array_search($product->id, $bestSellingIds);
            });

            return $this->formatProducts($products);
        } catch (\Exception $e) {
            Log::error('ApiHomeService getBestSellingProducts error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Lấy danh mục nổi bật
     * Chỉ danh mục gốc có sản phẩm
     */
    public function getFeaturedCategories($limit = 8)
    {
        try {
            // Lấy danh mục gốc (parent_id = 0) và active
            $rootCategories = $this->danhMucService->getActiveCategories()
                ->where('parent_id', 0)
                ->where('status', 'active')
                ->sortBy('sort_order');

            // Lọc chỉ những danh mục có sản phẩm
            $categoriesWithProducts = $rootCategories->filter(function ($category) {
                $productCount = SanPham::where('id_danhmuc', $category->id)
                    ->where('trangthai', 1)
                    ->whereNull('deleted_at')
                    ->count();
                return $productCount > 0;
            });

            // Giới hạn và format dữ liệu
            $categories = $categoriesWithProducts->take($limit)->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'image' => $category->image ? url($category->image) : null,
                    'product_count' => SanPham::where('id_danhmuc', $category->id)
                        ->where('trangthai', 1)
                        ->whereNull('deleted_at')
                        ->count(),
                    'sort_order' => $category->sort_order
                ];
            });

            return $categories;
        } catch (\Exception $e) {
            Log::error('ApiHomeService getFeaturedCategories error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Lấy thống kê tổng quan
     */
    public function getStatistics()
    {
        try {
            return $this->statisticsService->getOverviewStats();
        } catch (\Exception $e) {
            Log::error('ApiHomeService getStatistics error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm sản phẩm nhanh
     */
    public function searchProducts($keyword, $limit = 10)
    {
        try {
            $products = SanPham::with([
                'danhmuc:id,name,slug',
                'hinhanh' => function($q) {
                    $q->whereNull('deleted_at')->orderBy('is_default', 'desc');
                }
            ])
            ->where('trangthai', 1)
            ->whereNull('deleted_at')
            ->where(function($q) use ($keyword) {
                $q->where('tenSP', 'like', "%{$keyword}%")
                  ->orWhere('maSP', 'like', "%{$keyword}%")
                  ->orWhere('moTa', 'like', "%{$keyword}%");
            })
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

            return $this->formatProducts($products);
        } catch (\Exception $e) {
            Log::error('ApiHomeService searchProducts error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Format dữ liệu sản phẩm cho API
     */
    private function formatProducts($products)
    {
        return $products->map(function($product) {
            // Lấy giá tốt nhất từ variants
            $bestPrice = $this->getBestPrice($product);
            
            // Lấy hình ảnh chính (ưu tiên hình ảnh mặc định)
            $mainImage = $product->hinhanh->where('is_default', 1)->first() 
                        ?? $product->hinhanh->first();
            
            // Tính tổng stock
            $totalStock = $product->soLuong + $product->chitietsanpham->sum('soLuong');
            
            return [
                'id' => $product->id,
                'maSP' => $product->maSP,
                'tenSP' => $product->tenSP,
                'slug' => $product->slug ?? str_slug($product->tenSP),
                'moTa' => $product->moTa,
                'trangthai' => (bool) $product->trangthai,
                'base_price' => $product->base_price ? (float) $product->base_price : null,
                'base_sale_price' => $product->base_sale_price ? (float) $product->base_sale_price : null,
                'best_price' => $bestPrice['price'],
                'best_sale_price' => $bestPrice['sale_price'],
                'is_on_sale' => $bestPrice['is_on_sale'],
                'total_stock' => $totalStock,
                'danhmuc' => $product->danhmuc ? [
                    'id' => $product->danhmuc->id,
                    'name' => $product->danhmuc->name,
                    'slug' => $product->danhmuc->slug
                ] : null,
                'main_image' => $mainImage ? [
                    'id' => $mainImage->id,
                    'url' => $mainImage->url ? url($mainImage->url) : null,
                    'alt' => $mainImage->alt ?? $product->tenSP
                ] : null,
                'images' => $product->hinhanh->map(function($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->url ? url($image->url) : null,
                        'alt' => $image->alt ?? '',
                        'is_default' => (bool) $image->is_default
                    ];
                }),
                'variants_count' => $product->chitietsanpham->count(),
                'available_variants' => $product->chitietsanpham->where('soLuong', '>', 0)->count(),
                'created_at' => $product->created_at ? $product->created_at->toISOString() : null,
                'updated_at' => $product->updated_at ? $product->updated_at->toISOString() : null
            ];
        });
    }

    /**
     * Lấy giá tốt nhất từ sản phẩm và variants
     */
    private function getBestPrice($product)
    {
        $bestPrice = $product->base_price ?? 0;
        $bestSalePrice = $product->base_sale_price ?? 0;
        
        // Kiểm tra giá từ variants
        foreach ($product->chitietsanpham as $variant) {
            if ($variant->gia && $variant->gia > 0) {
                if ($bestPrice == 0 || $variant->gia < $bestPrice) {
                    $bestPrice = $variant->gia;
                }
            }
            
            if ($variant->gia_khuyenmai && $variant->gia_khuyenmai > 0) {
                if ($bestSalePrice == 0 || $variant->gia_khuyenmai < $bestSalePrice) {
                    $bestSalePrice = $variant->gia_khuyenmai;
                }
            }
        }
        
        $isOnSale = $bestSalePrice > 0 && $bestSalePrice < $bestPrice;
        
        return [
            'price' => (float) $bestPrice,
            'sale_price' => (float) $bestSalePrice,
            'is_on_sale' => $isOnSale
        ];
    }
}
