<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiHomeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * API Controller cho trang chủ
 * Cung cấp các endpoint để lấy dữ liệu hiển thị trang chủ
 */
class ApiHomeController extends Controller
{
    protected $apiHomeService;

    public function __construct(ApiHomeService $apiHomeService)
    {
        $this->apiHomeService = $apiHomeService;
    }

    /**
     * Lấy tất cả dữ liệu trang chủ
     * GET /api/home
     */
    public function index()
    {
        try {
            $data = $this->apiHomeService->getHomePageData();
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Lấy dữ liệu trang chủ thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('ApiHomeController index error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy dữ liệu trang chủ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy sản phẩm nổi bật
     * GET /api/home/featured-products
     */
    public function getFeaturedProducts(Request $request)
    {
        try {
            $limit = $request->get('limit', 8);
            $products = $this->apiHomeService->getFeaturedProducts($limit);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Lấy sản phẩm nổi bật thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('ApiHomeController getFeaturedProducts error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy sản phẩm nổi bật',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy sản phẩm khuyến mãi
     * GET /api/home/sale-products
     */
    public function getSaleProducts(Request $request)
    {
        try {
            $limit = $request->get('limit', 6);
            $products = $this->apiHomeService->getSaleProducts($limit);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Lấy sản phẩm khuyến mãi thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('ApiHomeController getSaleProducts error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy sản phẩm khuyến mãi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy sản phẩm bán chạy
     * GET /api/home/best-selling-products
     */
    public function getBestSellingProducts(Request $request)
    {
        try {
            $limit = $request->get('limit', 6);
            $products = $this->apiHomeService->getBestSellingProducts($limit);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Lấy sản phẩm bán chạy thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('ApiHomeController getBestSellingProducts error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy sản phẩm bán chạy',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh mục nổi bật
     * GET /api/home/featured-categories
     */
    public function getFeaturedCategories(Request $request)
    {
        try {
            $limit = $request->get('limit', 8);
            $categories = $this->apiHomeService->getFeaturedCategories($limit);
            
            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Lấy danh mục nổi bật thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('ApiHomeController getFeaturedCategories error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh mục nổi bật',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê tổng quan
     * GET /api/home/statistics
     */
    public function getStatistics()
    {
        try {
            $stats = $this->apiHomeService->getStatistics();
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Lấy thống kê thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('ApiHomeController getStatistics error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thống kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tìm kiếm sản phẩm nhanh cho trang chủ
     * GET /api/home/search
     */
    public function search(Request $request)
    {
        try {
            $keyword = $request->get('q', '');
            $limit = $request->get('limit', 10);
            
            if (empty($keyword)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập từ khóa tìm kiếm'
                ], 400);
            }

            $results = $this->apiHomeService->searchProducts($keyword, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Tìm kiếm thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('ApiHomeController search error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
