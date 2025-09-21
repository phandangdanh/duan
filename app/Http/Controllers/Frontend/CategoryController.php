<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\DanhMucService;
use App\Services\SanPhamService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $danhMucService;
    protected $sanPhamService;

    public function __construct(
        DanhMucService $danhMucService,
        SanPhamService $sanPhamService
    ) {
        $this->danhMucService = $danhMucService;
        $this->sanPhamService = $sanPhamService;
    }

    /**
     * Hiển thị sản phẩm theo danh mục
     */
    public function show($slug, Request $request)
    {
        try {
            // Tìm danh mục theo slug
            $category = $this->danhMucService->getActiveCategories()
                ->where('slug', $slug)
                ->first();

            if (!$category) {
                abort(404, 'Danh mục không tồn tại');
            }

            // Lấy sản phẩm trong danh mục
            $filters = [
                'category' => $category->id,
                'keyword' => $request->get('search'),
                'status' => 1,
                'sort' => $request->get('sort', 'id'),
                'perpage' => $request->get('perpage', 12)
            ];

            $result = $this->sanPhamService->list($filters);
            $products = $result['items'] ?? collect([]);
            $pagination = $result['pagination'] ?? null;

            // Lấy danh mục con nếu có
            $subCategories = $this->danhMucService->getActiveCategories()
                ->where('parent_id', $category->id);

            return view('fontend.products.sanpham', compact(
                'category',
                'products',
                'subCategories',
                'pagination',
                'filters'
            ));
        } catch (\Exception $e) {
            \Log::error('CategoryController show error: ' . $e->getMessage());
            abort(404, 'Không thể tải danh mục');
        }
    }
}
