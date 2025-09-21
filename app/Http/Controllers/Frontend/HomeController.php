<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\SanPhamService;
use App\Services\DanhMucService;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Hiển thị trang chủ
     */
    public function index()
    {
        try {
            // Lấy danh mục nổi bật (chỉ danh mục gốc có sản phẩm, giới hạn 8 danh mục)
            $categories = $this->getFeaturedCategories();
            
            // Lấy sản phẩm nổi bật (sản phẩm mới, bán chạy)
            $featuredProducts = $this->getFeaturedProducts();
            
            // Lấy sản phẩm khuyến mãi
            $saleProducts = $this->getSaleProducts();
            
            // Lấy sản phẩm bán chạy
            $bestSellingProducts = $this->getBestSellingProducts();
            
            // Lấy thống kê tổng quan
            $stats = $this->statisticsService->getOverviewStats();

            return view('fontend.home.trangchu', compact(
                'categories',
                'featuredProducts', 
                'saleProducts',
                'bestSellingProducts',
                'stats'
            ));
        } catch (\Exception $e) {
            \Log::error('HomeController index error: ' . $e->getMessage());
            
            // Trả về view với dữ liệu mặc định khi có lỗi
            return view('fontend.home.trangchu', [
                'categories' => collect([]),
                'featuredProducts' => collect([]),
                'saleProducts' => collect([]),
                'bestSellingProducts' => collect([]),
                'stats' => []
            ]);
        }
    }

    /**
     * Lấy sản phẩm nổi bật
     * 
     * Sản phẩm nổi bật được lấy theo tiêu chí:
     * 1. Sản phẩm đang kinh doanh (trangthai = 1)
     * 2. Sản phẩm chưa bị xóa mềm (deleted_at IS NULL)
     * 3. Sản phẩm có chi tiết sản phẩm (biến thể) - đảm bảo có giá
     * 4. Sắp xếp theo ID giảm dần (sản phẩm mới nhất)
     * 5. Giới hạn 8 sản phẩm
     */
    private function getFeaturedProducts()
    {
        try {
            // Lấy trực tiếp từ model để đảm bảo load đúng quan hệ
            $products = \App\Models\SanPham::with(['danhmuc', 'hinhanh', 'chitietsanpham'])
                ->where('trangthai', 1)  // Chỉ sản phẩm đang kinh doanh
                ->whereNull('deleted_at')  // Chưa bị xóa mềm
                ->whereExists(function ($query) {
                    // Phải có chi tiết sản phẩm (biến thể) để đảm bảo có giá
                    $query->select(\DB::raw(1))
                        ->from('chitietsanpham')
                        ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                        ->whereNull('chitietsanpham.deleted_at');
                })
                ->orderBy('id', 'desc')  // Sản phẩm mới nhất
                ->limit(8)  // Giới hạn 8 sản phẩm
                ->get();
            
            // Debug: Log dữ liệu sản phẩm
            if ($products->count() > 0) {
                $firstProduct = $products->first();
                \Log::info('Featured Product Debug:', [
                    'id' => $firstProduct->id,
                    'tenSP' => $firstProduct->tenSP,
                    'base_price' => $firstProduct->base_price,
                    'base_sale_price' => $firstProduct->base_sale_price,
                    'hinhanh_count' => $firstProduct->hinhanh->count(),
                    'chitietsanpham_count' => $firstProduct->chitietsanpham->count(),
                    'first_image_url' => $firstProduct->hinhanh->first()?->url,
                    'first_variant_price' => $firstProduct->chitietsanpham->first()?->gia,
                ]);
            }
            
            return $products;
        } catch (\Exception $e) {
            \Log::error('HomeController getFeaturedProducts error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Lấy sản phẩm khuyến mãi (có base_sale_price < base_price)
     */
    private function getSaleProducts()
    {
        $filters = [
            'status' => 1,
            'sort' => 'gia_asc', // Sắp xếp theo giá tăng dần (rẻ nhất trước)
            'perpage' => 6,
            'on_sale' => true // Chỉ lấy sản phẩm đang khuyến mãi
        ];
        
        $result = $this->sanPhamService->list($filters);
        return $result['items'] ?? collect([]);
    }

    /**
     * Lấy sản phẩm bán chạy (dựa trên số lượng đã bán trong đơn hàng)
     * Nếu không có sản phẩm nào đã bán, lấy sản phẩm mới nhất có giá
     */
    private function getBestSellingProducts()
    {
        try {
            // Lấy sản phẩm bán chạy dựa trên tổng số lượng đã bán
            $bestSellingIds = \DB::table('chitietdonhang')
                ->join('chitietsanpham', 'chitietdonhang.id_chitietsanpham', '=', 'chitietsanpham.id')
                ->join('sanpham', 'chitietsanpham.id_sp', '=', 'sanpham.id')
                ->whereNull('sanpham.deleted_at')
                ->where('sanpham.trangthai', 1)
                ->select('sanpham.id', \DB::raw('SUM(chitietdonhang.soluong) as total_sold'))
                ->groupBy('sanpham.id')
                ->orderBy('total_sold', 'desc')
                ->limit(6)
                ->pluck('sanpham.id')
                ->toArray();

            if (empty($bestSellingIds)) {
                // Nếu không có sản phẩm nào đã bán, lấy sản phẩm mới nhất có giá
                $products = \App\Models\SanPham::with(['danhmuc', 'hinhanh', 'chitietsanpham'])
                    ->where('trangthai', 1)  // Chỉ sản phẩm đang kinh doanh
                    ->whereNull('deleted_at')  // Chưa bị xóa mềm
                    ->whereExists(function ($query) {
                        // Phải có chi tiết sản phẩm (biến thể) để đảm bảo có giá
                        $query->select(\DB::raw(1))
                            ->from('chitietsanpham')
                            ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                            ->whereNull('chitietsanpham.deleted_at');
                    })
                    ->orderBy('id', 'desc')  // Sản phẩm mới nhất
                    ->limit(6)  // Giới hạn 6 sản phẩm
                    ->get();
                
                return $products;
            }

            // Lấy thông tin sản phẩm bán chạy với quan hệ đầy đủ
            $products = \App\Models\SanPham::with(['danhmuc', 'hinhanh', 'chitietsanpham'])
                ->where('trangthai', 1)
                ->whereNull('deleted_at')
                ->whereIn('id', $bestSellingIds)
                ->get()
                ->sortBy(function($product) use ($bestSellingIds) {
                    return array_search($product->id, $bestSellingIds);
                });

            return $products;
        } catch (\Exception $e) {
            \Log::error('HomeController getBestSellingProducts error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Lấy danh mục nổi bật (chỉ danh mục gốc có sản phẩm)
     */
    private function getFeaturedCategories()
    {
        try {
            // Lấy danh mục gốc (parent_id = 0) và active, sắp xếp theo sort_order
            $rootCategories = $this->danhMucService->getActiveCategories()
                ->where('parent_id', 0)
                ->where('status', 'active')
                ->sortBy('sort_order');

            // Lọc chỉ những danh mục có sản phẩm
            $categoriesWithProducts = $rootCategories->filter(function ($category) {
                $productCount = \App\Models\SanPham::where('id_danhmuc', $category->id)
                    ->where('trangthai', 1)
                    ->whereNull('deleted_at')
                    ->count();
                return $productCount > 0;
            });

            // Giới hạn 8 danh mục và giữ nguyên thứ tự sort_order
            return $categoriesWithProducts->take(8);
        } catch (\Exception $e) {
            \Log::error('HomeController getFeaturedCategories error: ' . $e->getMessage());
            return collect([]);
        }
    }
}
