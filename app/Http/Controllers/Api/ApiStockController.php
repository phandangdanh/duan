<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class ApiStockController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Lấy số lượng hiển thị (đã trừ giỏ hàng)
     */
    public function getDisplayStock(Request $request)
    {
        try {
            $productId = $request->get('product_id');
            $variantId = $request->get('variant_id');

            if (!$productId) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Product ID is required'
                ], 400);
            }

            $displayStock = $this->cartService->getDisplayStock($productId, $variantId);

            return response()->json([
                'status_code' => 200,
                'data' => [
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'display_stock' => $displayStock,
                    'is_out_of_stock' => $displayStock <= 0
                ],
                'message' => 'Lấy số lượng hiển thị thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin stock chi tiết (mới)
     */
    public function getStockInfo(Request $request)
    {
        try {
            $productId = $request->get('product_id');
            $variantId = $request->get('variant_id');

            if (!$productId) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Product ID is required'
                ], 400);
            }

            $stockInfo = $this->cartService->getStockInfo($productId, $variantId);

            return response()->json([
                'status_code' => 200,
                'data' => $stockInfo,
                'message' => 'Lấy thông tin stock thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra có thể thêm vào giỏ hàng không
     */
    public function canAddToCart(Request $request)
    {
        try {
            $productId = $request->get('product_id');
            $variantId = $request->get('variant_id');
            $quantity = $request->get('quantity', 1);

            if (!$productId) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Product ID is required'
                ], 400);
            }

            $canAdd = $this->cartService->canAddToCart($productId, $variantId, $quantity);
            $stockInfo = $this->cartService->getStockInfo($productId, $variantId);

            return response()->json([
                'status_code' => 200,
                'data' => [
                    'can_add' => $canAdd,
                    'stock_info' => $stockInfo,
                    'requested_quantity' => $quantity
                ],
                'message' => $canAdd ? 'Có thể thêm vào giỏ hàng' : 'Không đủ hàng'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin stock cho nhiều sản phẩm
     */
    public function getMultipleStockInfo(Request $request)
    {
        try {
            $products = $request->get('products', []);

            if (empty($products)) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Products array is required'
                ], 400);
            }

            $stockInfo = $this->cartService->getMultipleStockInfo($products);

            return response()->json([
                'status_code' => 200,
                'data' => $stockInfo,
                'message' => 'Lấy thông tin stock nhiều sản phẩm thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
