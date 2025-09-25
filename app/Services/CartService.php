<?php

namespace App\Services;

use App\Models\SanPham;
use App\Models\ChiTietSanPham;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartService
{
    const CART_SESSION_KEY = 'cart';
    const CART_COUNT_SESSION_KEY = 'cart_count';

    /**
     * Lấy toàn bộ giỏ hàng
     */
    public function getCart()
    {
        $cart = Session::get(self::CART_SESSION_KEY, []);
        Log::info('CartService getCart called:', [
            'cart' => $cart,
            'cart_count' => count($cart),
            'session_id' => session()->getId()
        ]);
        return $cart;
    }

    /**
     * Lấy giỏ hàng với thông tin chi tiết sản phẩm
     */
    public function getCartWithDetails()
    {
        $cart = $this->getCart();
        $cartWithDetails = [];

        foreach ($cart as $key => $item) {
            $product = SanPham::with(['hinhanh'])->find($item['product_id']);
            if (!$product) {
                Log::warning('Product not found for cart item', ['product_id' => $item['product_id']]);
                continue;
            }

            // Tính max_stock dựa trên variant hoặc sản phẩm chính
            $maxStock = 0;
            if ($item['variant_id']) {
                $variant = ChiTietSanPham::find($item['variant_id']);
                if ($variant) {
                    $maxStock = $variant->soLuong ?? 0;
                }
            } else {
                $maxStock = $product->soLuong ?? 0;
            }

            $cartItem = [
                'key' => $key,
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total_price' => $item['price'] * $item['quantity'],
                'product_name' => $item['product_name'],
                'variant_name' => $item['variant_name'] ?? null,
                'image' => $item['image'] ?? null,
                'max_stock' => $maxStock,
                'product' => $product
            ];

            // Thêm thông tin variant nếu có
            if ($item['variant_id']) {
                $variant = ChiTietSanPham::find($item['variant_id']);
                if ($variant) {
                    $cartItem['variant'] = $variant;
                }
            }

            $cartWithDetails[] = $cartItem;
        }

        return $cartWithDetails;
    }

    /**
     * Lấy số lượng sản phẩm trong giỏ hàng
     */
    public function getCartCount()
    {
        $cart = $this->getCart();
        return array_sum(array_column($cart, 'quantity'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart($productId, $variantId = null, $quantity = 1)
    {
        Log::info('=== CartService addToCart called ===', [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $quantity
        ]);
        
        $cart = $this->getCart();
        $key = $this->generateCartKey($productId, $variantId);
        
        Log::info('Generated cart key:', ['key' => $key]);

        // Lấy thông tin sản phẩm
        $product = SanPham::with(['hinhanh', 'chitietsanpham'])->find($productId);
        if (!$product) {
            Log::error('Product not found', ['product_id' => $productId]);
            return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];
        }
        
        Log::info('Product found:', [
            'product_id' => $product->id,
            'product_name' => $product->tenSP,
            'main_stock' => $product->soLuong
        ]);

        // Lấy thông tin variant nếu có
        $variant = null;
        $price = 0;
        $variantName = '';

        if ($variantId) {
            Log::info('Processing variant:', ['variant_id' => $variantId]);
            $variant = ChiTietSanPham::find($variantId);
            if (!$variant || $variant->id_sp != $productId) {
                Log::error('Invalid variant', [
                    'variant_id' => $variantId,
                    'variant_found' => !!$variant,
                    'variant_product_id' => $variant ? $variant->id_sp : null,
                    'expected_product_id' => $productId
                ]);
                return ['success' => false, 'message' => 'Biến thể sản phẩm không hợp lệ'];
            }
            
            // Logic tính giá rõ ràng:
            // 1. Nếu variant có giá khuyến mãi > 0, dùng giá khuyến mãi
            // 2. Nếu variant có giá bán > 0, dùng giá bán
            // 3. Nếu variant không có giá, dùng giá sản phẩm chính
            if ($variant->gia_khuyenmai && $variant->gia_khuyenmai > 0) {
                $price = $variant->gia_khuyenmai;
            } elseif ($variant->gia && $variant->gia > 0) {
                $price = $variant->gia;
            } else {
                // Fallback về giá sản phẩm chính
                $price = $product->gia;
            }
            
            $variantName = $variant->tenSp;
            Log::info('Variant processed:', [
                'variant_name' => $variantName,
                'price' => $price,
                'variant_stock' => $variant->soLuong
            ]);
        } else {
            // Không có variant, dùng giá sản phẩm chính
            Log::info('Processing main product (no variant)');
            $price = $product->gia;
            Log::info('Main product processed:', [
                'price' => $price,
                'main_stock' => $product->soLuong
            ]);
        }

        // Bỏ kiểm tra stock - cho phép thêm vào giỏ hàng

        // Thêm hoặc cập nhật sản phẩm trong giỏ hàng
        if (isset($cart[$key])) {
            Log::info('Updating existing cart item:', [
                'key' => $key,
                'old_quantity' => $cart[$key]['quantity'],
                'new_quantity' => $cart[$key]['quantity'] + $quantity
            ]);
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cartItem = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'product_name' => $product->tenSP,
                'variant_name' => $variantName,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $product->hinhanh->first()->url ?? 'default.jpg',
                'max_stock' => $variant ? $variant->soLuong : 999,
            ];
            
            Log::info('Adding new cart item:', [
                'key' => $key,
                'cart_item' => $cartItem
            ]);
            
            $cart[$key] = $cartItem;
        }

        // Không cần trừ số lượng hiển thị vì getDisplayStock đã tính từ giỏ hàng

        // Cập nhật session
        Log::info('Saving cart to session:', [
            'cart_keys' => array_keys($cart),
            'total_items' => count($cart)
        ]);
        
        Session::put(self::CART_SESSION_KEY, $cart);
        $this->updateCartCount();
        
        // Log thành công
        Log::info('Product added to cart successfully', [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $quantity
        ]);

        // Trả về kết quả với cảnh báo nếu có
        $message = 'Đã thêm sản phẩm vào giỏ hàng';
        if (isset($warningMessage)) {
            $message .= '. ' . $warningMessage;
        }

        return ['success' => true, 'message' => $message];
    }

    /**
     * Cập nhật số lượng sản phẩm
     */
    public function updateQuantity($productId, $variantId = null, $quantity)
    {
        $cart = $this->getCart();
        $key = $this->generateCartKey($productId, $variantId);

        if (!isset($cart[$key])) {
            return ['success' => false, 'message' => 'Sản phẩm không có trong giỏ hàng'];
        }

        if ($quantity <= 0) {
            $result = $this->removeFromCart($productId, $variantId);
            if ($result['success']) {
                $result['message'] = 'Đã xóa sản phẩm khỏi giỏ hàng';
            }
            return $result;
        }

        // Lấy variant để kiểm tra tồn kho thực tế
        $variant = null;
        if ($variantId) {
            $variant = ChiTietSanPham::find($variantId);
        }

        // Kiểm tra tồn kho hiển thị
        $displayStock = $this->getDisplayStock($productId, $variantId);
        if ($displayStock < $quantity) {
            return ['success' => false, 'message' => "Chỉ còn {$displayStock} sản phẩm"];
        }

        // Không cần cộng/trừ số lượng hiển thị vì getDisplayStock đã tính từ giỏ hàng

        // Cập nhật số lượng trong giỏ hàng
        $cart[$key]['quantity'] = $quantity;

        Session::put(self::CART_SESSION_KEY, $cart);
        $this->updateCartCount();

        return ['success' => true, 'message' => 'Đã cập nhật số lượng'];
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart($productId, $variantId = null)
    {
        $cart = $this->getCart();
        $key = $this->generateCartKey($productId, $variantId);

        if (isset($cart[$key])) {
            // Cộng lại số lượng hiển thị
            $quantity = $cart[$key]['quantity'];
            // Không cần cộng lại số lượng hiển thị vì getDisplayStock đã tính từ giỏ hàng
            
            unset($cart[$key]);
            Session::put(self::CART_SESSION_KEY, $cart);
            $this->updateCartCount();
            return ['success' => true, 'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'];
        }

        return ['success' => false, 'message' => 'Sản phẩm không có trong giỏ hàng'];
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart()
    {
        // Cộng lại tất cả số lượng hiển thị
        $cart = $this->getCart();
        foreach ($cart as $key => $item) {
            // Không cần cộng lại số lượng hiển thị vì getDisplayStock đã tính từ giỏ hàng
        }
        
        Session::forget(self::CART_SESSION_KEY);
        Session::forget(self::CART_COUNT_SESSION_KEY);
        return ['success' => true, 'message' => 'Đã xóa toàn bộ giỏ hàng'];
    }

    /**
     * Tính tổng tiền giỏ hàng
     */
    public function getTotal()
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    /**
     * Kiểm tra tồn kho (chỉ kiểm tra, không trừ)
     */
    public function checkInventory()
    {
        $cart = $this->getCart();
        $warnings = [];
        
        foreach ($cart as $item) {
            if ($item['variant_id']) {
                $variant = ChiTietSanPham::find($item['variant_id']);
                if ($variant) {
                    // Chỉ kiểm tra tồn kho, không chặn
                    if ($variant->soLuong < $item['quantity']) {
                        $warnings[] = "Sản phẩm {$item['product_name']} chỉ còn {$variant->soLuong} sản phẩm, bạn yêu cầu {$item['quantity']} sản phẩm";
                    }
                }
            }
        }
        
        if (!empty($warnings)) {
            return [
                'success' => true, 
                'message' => 'Có thể thanh toán nhưng có cảnh báo: ' . implode('; ', $warnings),
                'warnings' => $warnings
            ];
        }
        
        return ['success' => true, 'message' => 'Tồn kho đủ'];
    }

    /**
     * Trừ số lượng tồn kho khi thanh toán thành công
     */
    public function deductInventory()
    {
        $cart = $this->getCart();
        
        foreach ($cart as $item) {
            if ($item['variant_id']) {
                $variant = ChiTietSanPham::find($item['variant_id']);
                if ($variant) {
                    // Kiểm tra tồn kho trước khi trừ
                    if ($variant->soLuong >= $item['quantity']) {
                        $variant->soLuong -= $item['quantity'];
                        $variant->save();
                    } else {
                        // Nếu không đủ tồn kho, trả về lỗi
                        return [
                            'success' => false, 
                            'message' => "Sản phẩm {$item['product_name']} không đủ tồn kho"
                        ];
                    }
                }
            }
        }
        
        return ['success' => true, 'message' => 'Đã trừ tồn kho thành công'];
    }


    /**
     * Tạo key cho giỏ hàng
     */
    private function generateCartKey($productId, $variantId = null)
    {
        return $productId . '_' . ($variantId ?? 'default');
    }

    /**
     * Cập nhật số lượng giỏ hàng trong session
     */
    private function updateCartCount()
    {
        Session::put(self::CART_COUNT_SESSION_KEY, $this->getCartCount());
    }

    /**
     * Kiểm tra giỏ hàng có rỗng không
     */
    public function isEmpty()
    {
        return empty($this->getCart());
    }

    /**
     * Lấy số lượng sản phẩm duy nhất trong giỏ hàng
     */
    public function getUniqueItemCount()
    {
        return count($this->getCart());
    }

    /**
     * Lấy số lượng hiển thị (tính cả số lượng đã thêm vào giỏ hàng)
     */
    public function getDisplayStock($productId, $variantId = null)
    {
        // Lấy số lượng thực tế từ database
        $product = SanPham::find($productId);
        if (!$product) {
            Log::error('Product not found in getDisplayStock', ['product_id' => $productId]);
            return 0;
        }

        // Tính stock cho variant cụ thể hoặc tổng stock
        if ($variantId) {
            // Nếu có variant, chỉ tính stock của variant đó + sản phẩm chính
            $variant = ChiTietSanPham::find($variantId);
            $variantStock = $variant ? $variant->soLuong : 0;
            $mainStock = $product->soLuong ?? 0;
            $totalRealStock = $mainStock + $variantStock;
            
            Log::info('Variant stock calculation:', [
                'variant_id' => $variantId,
                'variant_stock' => $variantStock,
                'main_stock' => $mainStock,
                'total_real_stock' => $totalRealStock
            ]);
        } else {
            // Nếu không có variant, tính tổng stock (sản phẩm chính + tất cả variant)
            $mainStock = $product->soLuong ?? 0;
            $variantStock = $product->chitietsanpham->sum('soLuong');
            $totalRealStock = $mainStock + $variantStock;
            
            Log::info('Main product stock calculation:', [
                'main_stock' => $mainStock,
                'variant_stock' => $variantStock,
                'total_real_stock' => $totalRealStock
            ]);
        }

        // Trừ đi số lượng đã thêm vào giỏ hàng cho sản phẩm này
        $cart = $this->getCart();
        $cartQuantity = 0;
        
        Log::info('Calculating cart quantity:', [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'cart_items' => $cart
        ]);
        
        foreach ($cart as $item) {
            if ($item['product_id'] == $productId) {
                // Nếu không có variant, chỉ tính sản phẩm chính
                if (!$variantId && !$item['variant_id']) {
                    $cartQuantity += $item['quantity'];
                    Log::info('Added main product quantity:', [
                        'item' => $item,
                        'cart_quantity' => $cartQuantity
                    ]);
                }
                // Nếu có variant, chỉ tính variant cụ thể
                elseif ($variantId && $item['variant_id'] == $variantId) {
                    $cartQuantity += $item['quantity'];
                    Log::info('Added variant quantity:', [
                        'item' => $item,
                        'cart_quantity' => $cartQuantity
                    ]);
                }
            }
        }

        $displayStock = max(0, $totalRealStock - $cartQuantity);
        
        Log::info('Final stock calculation:', [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'total_real_stock' => $totalRealStock,
            'cart_quantity' => $cartQuantity,
            'display_stock' => $displayStock
        ]);

        return $displayStock;
    }

    /**
     * Lấy thông tin stock chi tiết cho trang sản phẩm
     */
    public function getDetailedStockInfo($productId)
    {
        $product = SanPham::with(['chitietsanpham.mausac', 'chitietsanpham.size'])->find($productId);
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Sản phẩm không tồn tại'
            ];
        }

        $cart = $this->getCart();
        
        Log::info('Cart data in getDetailedStockInfo:', [
            'product_id' => $productId,
            'cart' => $cart,
            'cart_count' => count($cart)
        ]);
        
        // Tính số lượng đã thêm vào giỏ cho sản phẩm chính
        $mainCartQuantity = 0;
        foreach ($cart as $item) {
            Log::info('Checking cart item:', [
                'item' => $item,
                'product_match' => $item['product_id'] == $productId,
                'variant_id' => $item['variant_id'],
                'is_main_product' => !$item['variant_id']
            ]);
            
            if ($item['product_id'] == $productId && !$item['variant_id']) {
                $mainCartQuantity += $item['quantity'];
                Log::info('Added to main cart quantity:', [
                    'quantity' => $item['quantity'],
                    'total_main_cart_quantity' => $mainCartQuantity
                ]);
            }
        }
        
        Log::info('Main product cart quantity calculation:', [
            'product_id' => $productId,
            'main_cart_quantity' => $mainCartQuantity,
            'cart_items' => $cart
        ]);

        // Tính số lượng đã thêm vào giỏ cho từng variant
        $variantCartQuantities = [];
        foreach ($cart as $item) {
            if ($item['product_id'] == $productId && $item['variant_id']) {
                $variantCartQuantities[$item['variant_id']] = ($variantCartQuantities[$item['variant_id']] ?? 0) + $item['quantity'];
            }
        }

        // Tính stock hiển thị cho sản phẩm chính
        $mainStock = max(0, ($product->soLuong ?? 0) - $mainCartQuantity);

        // Tính stock hiển thị cho từng variant
        $variantDetails = [];
        $totalVariantStock = 0;
        foreach ($product->chitietsanpham as $variant) {
            $cartQuantity = $variantCartQuantities[$variant->id] ?? 0;
            $displayStock = max(0, ($variant->soLuong ?? 0) - $cartQuantity);
            
            $variantDetails[] = [
                'id' => $variant->id,
                'color_name' => $variant->mausac ? $variant->mausac->ten : 'Không xác định',
                'size_name' => $variant->size ? $variant->size->ten : 'Không xác định',
                'stock' => $displayStock,
                'original_stock' => $variant->soLuong ?? 0,
                'cart_quantity' => $cartQuantity
            ];
            
            $totalVariantStock += $displayStock;
        }

        return [
            'success' => true,
            'product_id' => $productId,
            'main_stock' => $mainStock,
            'main_original_stock' => $product->soLuong ?? 0,
            'main_cart_quantity' => $mainCartQuantity,
            'variant_details' => $variantDetails,
            'total_variant_stock' => $totalVariantStock,
            'total_stock' => $mainStock + $totalVariantStock
        ];
    }

    // Method subtractDisplayStock đã được xóa vì không cần thiết

    // Method addBackDisplayStock đã được xóa vì không cần thiết

    // Method clearSubtractedStock đã được xóa vì không cần thiết
}
