/**
 * API Client cho giỏ hàng
 * Xử lý tất cả các API calls liên quan đến giỏ hàng
 */

class CartAPIClient {
    constructor() {
        this.baseUrl = '/cart';
        this.timeout = 10000;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    async addToCart(productId, variantId = null, quantity = 1) {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/add`, {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    quantity: quantity
                })
            });
            return response;
        } catch (error) {
            console.error('Error adding to cart:', error);
            throw error;
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    async updateCartItem(productId, variantId = null, quantity) {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/update`, {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    quantity: quantity
                })
            });
            return response;
        } catch (error) {
            console.error('Error updating cart item:', error);
            throw error;
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    async removeFromCart(productId, variantId = null) {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/remove`, {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId
                })
            });
            return response;
        } catch (error) {
            console.error('Error removing from cart:', error);
            throw error;
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    async clearCart() {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/clear`, {
                method: 'POST'
            });
            return response;
        } catch (error) {
            console.error('Error clearing cart:', error);
            throw error;
        }
    }

    /**
     * Lấy thông tin giỏ hàng
     */
    async getCartInfo() {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/info`);
            return response;
        } catch (error) {
            console.error('Error getting cart info:', error);
            throw error;
        }
    }

    /**
     * Lấy số lượng hiển thị stock (đã trừ giỏ hàng)
     */
    async getDisplayStock(productId) {
        try {
            const response = await this.makeRequest(`/api/cart/display-stock/${productId}`);
            return response;
        } catch (error) {
            console.error('Error getting display stock:', error);
            throw error;
        }
    }

    /**
     * Thực hiện HTTP request
     */
    async makeRequest(url, options = {}) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.timeout);

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                signal: controller.signal,
                ...options
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            clearTimeout(timeoutId);
            
            if (error.name === 'AbortError') {
                throw new Error('Request timeout');
            }
            
            throw error;
        }
    }
}
