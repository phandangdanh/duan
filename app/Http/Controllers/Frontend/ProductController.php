<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\SanPhamService;
use App\Services\DanhMucService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $sanPhamService;
    protected $danhMucService;
    protected $cartService;

    public function __construct(
        SanPhamService $sanPhamService,
        DanhMucService $danhMucService,
        CartService $cartService
    ) {
        $this->sanPhamService = $sanPhamService;
        $this->danhMucService = $danhMucService;
        $this->cartService = $cartService;
    }

    /**
     * Hiển thị danh sách sản phẩm
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'keyword' => $request->get('search'),
                'category' => $request->get('category'),
                'status' => 1, // Chỉ lấy sản phẩm active
                'sort' => $request->get('sort', 'id_desc'),
                'perpage' => $request->get('perpage', 12)
            ];
            
            // Đảm bảo perpage là số nguyên hợp lệ hoặc 'all'
            $perpage = $filters['perpage'];
            if ($perpage === 'all') {
                // Giữ nguyên 'all'
            } else {
                $perpage = (int) $perpage;
                if ($perpage <= 0 || !in_array($perpage, [12, 24, 48])) {
                    $perpage = 12;
                }
            }
            $filters['perpage'] = $perpage;

            // Xử lý bộ lọc giá
            $priceFilter = $request->get('price');
            if ($priceFilter) {
                $priceRanges = explode(',', $priceFilter);
                $minPrice = null;
                $maxPrice = null;
                
                foreach ($priceRanges as $range) {
                    if (strpos($range, '-') !== false) {
                        // Khoảng giá: 100000-200000
                        list($min, $max) = explode('-', $range);
                        $minPrice = $minPrice === null ? (float)$min : min($minPrice, (float)$min);
                        $maxPrice = $maxPrice === null ? (float)$max : max($maxPrice, (float)$max);
                    } elseif (strpos($range, '-') === 0) {
                        // Dưới giá: -100000
                        $maxPrice = $maxPrice === null ? (float)substr($range, 1) : min($maxPrice, (float)substr($range, 1));
                    } else {
                        // Trên giá: 500000-
                        $minPrice = $minPrice === null ? (float)$range : max($minPrice, (float)$range);
                    }
                }
                
                if ($minPrice !== null) {
                    $filters['gia_min'] = $minPrice;
                }
                if ($maxPrice !== null) {
                    $filters['gia_max'] = $maxPrice;
                }
            }

            // Lấy danh sách sản phẩm với relations đầy đủ
            $result = $this->sanPhamService->list($filters);
            $products = $result['items'] ?? collect([]);
            $pagination = $result['pagination'] ?? null;

            // Đảm bảo load relations cho products nếu chưa có
            if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $products->load(['danhmuc', 'hinhanh', 'chitietsanpham']);
            } elseif ($products instanceof \Illuminate\Database\Eloquent\Collection) {
                $products->load(['danhmuc', 'hinhanh', 'chitietsanpham']);
            }

            // Lấy danh mục để hiển thị filter
            $categories = $this->danhMucService->getActiveCategories();

            return view('fontend.products.sanpham', compact(
                'products',
                'categories',
                'pagination',
                'filters'
            ));
        } catch (\Exception $e) {
            Log::error('ProductController index error: ' . $e->getMessage());
            
            return view('fontend.products.sanpham', [
                'products' => collect([]),
                'categories' => collect([]),
                'pagination' => null,
                'filters' => []
            ]);
        }
    }

    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function show($id)
    {
        try {
            $product = $this->sanPhamService->getById($id);
            
            if (!$product) {
                abort(404, 'Sản phẩm không tồn tại');
            }

            // Lấy sản phẩm liên quan (cùng danh mục)
            $relatedProducts = $this->getRelatedProducts($product->id_danhmuc, $id);

            return view('fontend.product.trangchitiet', compact(
                'product',
                'relatedProducts'
            ));
        } catch (\Exception $e) {
            Log::error('ProductController show error: ' . $e->getMessage());
            abort(404, 'Không thể tải sản phẩm');
        }
    }

    /**
     * Hiển thị chi tiết sản phẩm sử dụng API
     */
    public function showApi($id)
    {
        return view('fontend.product.trangchitiet-api', compact('id'));
    }

    /**
     * Lấy sản phẩm liên quan
     */
    private function getRelatedProducts($categoryId, $excludeId)
    {
        $filters = [
            'category' => $categoryId,
            'status' => 1,
            'perpage' => 4
        ];
        
        $result = $this->sanPhamService->list($filters);
        $products = $result['items'] ?? collect([]);
        
        // Loại bỏ sản phẩm hiện tại
        return $products->where('id', '!=', $excludeId);
    }

    /**
     * Hiển thị sản phẩm nổi bật
     * 
     * Sản phẩm nổi bật được lấy theo tiêu chí:
     * 1. Sản phẩm đang kinh doanh (trangthai = 1)
     * 2. Sản phẩm chưa bị xóa mềm (deleted_at IS NULL)
     * 3. Sản phẩm có chi tiết sản phẩm (biến thể) - đảm bảo có giá
     * 4. Sắp xếp theo ID giảm dần (sản phẩm mới nhất)
     * 5. Phân trang 8 sản phẩm mỗi trang
     */
    public function featured(Request $request)
    {
        try {
            // Cố định 8 sản phẩm mỗi trang
            $perPage = 8;
            
            // Lấy sản phẩm nổi bật với logic giống HomeController
            $products = \App\Models\SanPham::with(['danhmuc', 'hinhanh', 'chitietsanpham'])
                ->where('trangthai', 1)  // Chỉ sản phẩm đang kinh doanh
                ->whereNull('deleted_at')  // Chưa bị xóa mềm
                ->whereExists(function ($query) {
                    // Phải có chi tiết sản phẩm (biến thể) để đảm bảo có giá
                    $query->select(DB::raw(1))
                        ->from('chitietsanpham')
                        ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                        ->whereNull('chitietsanpham.deleted_at');
                })
                ->orderBy('id', 'desc')  // Sản phẩm mới nhất
                ->paginate($perPage);
            
            return view('fontend.products.featured', compact('products'));
        } catch (\Exception $e) {
            Log::error('ProductController featured error: ' . $e->getMessage());
            
            return view('fontend.products.featured', [
                'products' => collect([])
            ]);
        }
    }

    /**
     * Hiển thị sản phẩm khuyến mãi
     */
    public function sale(Request $request)
    {
        try {
            $filters = [
                'status' => 1,
                'sort' => 'gia_asc',
                'perpage' => $request->get('perpage', 8),
                'on_sale' => true
            ];

            $result = $this->sanPhamService->list($filters);
            $products = $result['items'] ?? collect([]);
            $pagination = $result['pagination'] ?? null;

            return view('fontend.products.sale', compact(
                'products',
                'pagination'
            ));
        } catch (\Exception $e) {
            Log::error('ProductController sale error: ' . $e->getMessage());
            
            return view('fontend.products.sale', [
                'products' => collect([]),
                'pagination' => null
            ]);
        }
    }

    /**
     * Hiển thị sản phẩm bán chạy
     */
    public function bestselling(Request $request)
    {
        try {
            // Cố định 8 sản phẩm mỗi trang
            $perPage = 8;
            
            // Lấy sản phẩm bán chạy dựa trên tổng số lượng đã bán
            $bestSellingIds = DB::table('chitietdonhang')
                ->join('chitietsanpham', 'chitietdonhang.id_chitietsanpham', '=', 'chitietsanpham.id')
                ->join('sanpham', 'chitietsanpham.id_sp', '=', 'sanpham.id')
                ->whereNull('sanpham.deleted_at')
                ->where('sanpham.trangthai', 1)
                ->select('sanpham.id', DB::raw('SUM(chitietdonhang.soluong) as total_sold'))
                ->groupBy('sanpham.id')
                ->orderBy('total_sold', 'desc')
                ->pluck('sanpham.id')
                ->toArray();

            if (empty($bestSellingIds)) {
                // Nếu không có sản phẩm nào đã bán, lấy sản phẩm mới nhất với pagination
                $products = \App\Models\SanPham::with(['danhmuc', 'hinhanh', 'chitietsanpham'])
                    ->where('trangthai', 1)
                    ->whereNull('deleted_at')
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('chitietsanpham')
                            ->whereColumn('sanpham.id', 'chitietsanpham.id_sp')
                            ->whereNull('chitietsanpham.deleted_at');
                    })
                    ->orderBy('id', 'desc')
                    ->paginate($perPage);
            } else {
                // Lấy thông tin sản phẩm bán chạy với pagination
                $products = \App\Models\SanPham::with(['danhmuc', 'hinhanh', 'chitietsanpham'])
                    ->where('trangthai', 1)
                    ->whereNull('deleted_at')
                    ->whereIn('id', $bestSellingIds)
                    ->orderByRaw('FIELD(id, ' . implode(',', $bestSellingIds) . ')')
                    ->paginate($perPage);
            }

            return view('fontend.products.bestselling', compact('products'));
        } catch (\Exception $e) {
            Log::error('ProductController bestselling error: ' . $e->getMessage());
            
            return view('fontend.products.bestselling', [
                'products' => collect([])
            ]);
        }
    }
}
