<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Hiển thị trang giỏ hàng
     */
    public function index()
    {
        try {
            $cartItems = $this->cartService->getCartWithDetails();
            $total = $this->cartService->getTotal();
            $cartCount = $this->cartService->getCartCount();

            return view('fontend.cart.giohang', compact(
                'cartItems',
                'total',
                'cartCount'
            ));
        } catch (\Exception $e) {
            Log::error('CartController index error: ' . $e->getMessage());
            
            return view('fontend.cart.giohang', [
                'cartItems' => [],
                'total' => 0,
                'cartCount' => 0
            ]);
        }
    }

    /**
     * Thêm sản phẩm vào giỏ hàng (AJAX)
     */
    public function add(Request $request)
    {
        try {
            Log::info('CartController add called', [
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                'all_data' => $request->all()
            ]);
            
            $request->validate([
                'product_id' => 'required|integer|exists:sanpham,id',
                'variant_id' => 'nullable|integer|exists:chitietsanpham,id',
                'quantity' => 'required|integer|min:1|max:999',
            ]);

            $result = $this->cartService->addToCart(
                $request->product_id,
                $request->variant_id,
                $request->quantity
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'cart_count' => $this->cartService->getCartCount(),
                    'cart_total' => $this->cartService->getTotal(),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('CartController add error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng',
            ], 500);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm (AJAX)
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer',
                'variant_id' => 'nullable|integer',
                'quantity' => 'required|integer|min:0|max:999',
            ]);

            $result = $this->cartService->updateQuantity(
                $request->product_id,
                $request->variant_id,
                $request->quantity
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'cart_count' => $this->cartService->getCartCount(),
                    'cart_total' => $this->cartService->getTotal(),
                    'item_total' => $request->quantity * ($request->price ?? 0),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('CartController update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật giỏ hàng',
            ], 500);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng (AJAX)
     */
    public function remove(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer',
                'variant_id' => 'nullable|integer',
            ]);

            $result = $this->cartService->removeFromCart(
                $request->product_id,
                $request->variant_id
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'cart_count' => $this->cartService->getCartCount(),
                    'cart_total' => $this->cartService->getTotal(),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('CartController remove error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa sản phẩm khỏi giỏ hàng',
            ], 500);
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng (AJAX)
     */
    public function clear(Request $request)
    {
        try {
            $result = $this->cartService->clearCart();

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'cart_count' => 0,
                'cart_total' => 0,
            ]);
        } catch (\Exception $e) {
            Log::error('CartController clear error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa giỏ hàng',
            ], 500);
        }
    }

    /**
     * Lấy thông tin giỏ hàng (AJAX)
     */
    public function getCartInfo(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'cart_count' => $this->cartService->getCartCount(),
                'cart_total' => $this->cartService->getTotal(),
                'is_empty' => $this->cartService->isEmpty(),
            ]);
        } catch (\Exception $e) {
            Log::error('CartController getCartInfo error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin giỏ hàng',
            ], 500);
        }
    }

    /**
     * Lấy số lượng hiển thị của sản phẩm (API)
     */
    public function getDisplayStock($productId)
    {
        try {
            $product = \App\Models\SanPham::find($productId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại'
                ], 404);
            }

            // Lấy số lượng sản phẩm chính
            $mainStock = $product->soLuong ?? 0;
            
            // Lấy tổng số lượng biến thể
            $variantStock = $product->chitietsanpham->sum('soLuong');
            
            // Tính tổng stock thực tế
            $totalRealStock = $mainStock + $variantStock;
            
            // Lấy số lượng đã thêm vào giỏ hàng
            $cart = $this->cartService->getCart();
            $cartQuantity = 0;
            
            foreach ($cart as $item) {
                if ($item['product_id'] == $productId) {
                    $cartQuantity += $item['quantity'];
                }
            }
            
            // Sử dụng CartService để tính toán chính xác
            $displayStock = $this->cartService->getDisplayStock($productId);
            
            return response()->json([
                'success' => true,
                'totalStock' => $displayStock, // Số lượng hiển thị (đã trừ giỏ hàng)
                'mainStock' => $mainStock, // Số lượng sản phẩm chính thực tế
                'variantStock' => $variantStock, // Số lượng biến thể thực tế
                'totalRealStock' => $totalRealStock, // Tổng số lượng thực tế (chưa trừ giỏ hàng)
                'cartQuantity' => $cartQuantity // Số lượng đã thêm vào giỏ hàng
            ]);
            
        } catch (\Exception $e) {
            Log::error('CartController getDisplayStock error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy số lượng hiển thị',
            ], 500);
        }
    }
}
