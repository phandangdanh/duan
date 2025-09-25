/**
 * Cart API JavaScript
 * Xử lý tất cả chức năng giỏ hàng với localStorage integration
 */

class CartAPIManager {
    constructor() {
        this.cartItems = [];
        this.init();
    }

    /**
     * Initialize cart manager
     */
    init() {
        console.log('Initializing Cart API Manager...');
        this.loadCartFromLocalStorage();
        this.setupEventListeners();
        this.renderCartItems();
        this.updateCartDisplay();
    }

    /**
     * Load cart data from localStorage
     */
    loadCartFromLocalStorage() {
        try {
            const cartData = localStorage.getItem('cart_data');
            if (cartData) {
                this.cartItems = JSON.parse(cartData);
                console.log('Cart loaded from localStorage:', this.cartItems);
                
                // Clean cart data
                this.cleanCartData();
            } else {
                this.cartItems = [];
                console.log('No cart data in localStorage');
            }
        } catch (error) {
            console.error('Error loading cart from localStorage:', error);
            this.cartItems = [];
        }
    }

    /**
     * Clean cart data - remove duplicates and fix product names
     */
    cleanCartData() {
        if (!this.cartItems || this.cartItems.length === 0) return;
        
        console.log('Cleaning cart data...');
        
        // Fix product names
        this.cartItems.forEach(item => {
            if (!item.name || item.name === 'undefined') {
                item.name = 'Sản phẩm';
                console.log('Fixed product name for item:', item.product_id);
            }
        });
        
        // Remove duplicates and merge quantities
        const uniqueItems = new Map();
        
        this.cartItems.forEach(item => {
            const key = `${item.product_id}_${item.variant_id || 0}`;
            
            if (uniqueItems.has(key)) {
                // Merge quantities
                const existingItem = uniqueItems.get(key);
                existingItem.quantity += item.quantity;
                console.log(`Merged duplicate item ${key}: ${item.quantity} + ${existingItem.quantity - item.quantity} = ${existingItem.quantity}`);
            } else {
                uniqueItems.set(key, { ...item });
            }
        });
        
        const cleanedItems = Array.from(uniqueItems.values());
        
        if (cleanedItems.length !== this.cartItems.length) {
            console.log(`Cleaned cart: ${this.cartItems.length} → ${cleanedItems.length} items`);
            this.cartItems = cleanedItems;
            this.saveCartToLocalStorage();
        }
        
        console.log('Cart data cleaned:', this.cartItems);
    }

    /**
     * Save cart data to localStorage
     */
    saveCartToLocalStorage() {
        try {
            localStorage.setItem('cart_data', JSON.stringify(this.cartItems));
            console.log('Cart saved to localStorage:', this.cartItems);
        } catch (error) {
            console.error('Error saving cart to localStorage:', error);
        }
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Quantity update buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-outline-secondary')) {
                const button = e.target.closest('.btn-outline-secondary');
                const row = button.closest('tr[data-product-id]');
                if (row) {
                    const productId = parseInt(row.getAttribute('data-product-id'));
                    const variantId = parseInt(row.getAttribute('data-variant-id'));
                    const quantityInput = row.querySelector('input[type="number"]');
                    const currentQuantity = parseInt(quantityInput.value);
                    
                    if (button.querySelector('.fa-plus')) {
                        this.updateQuantity(productId, variantId, currentQuantity + 1);
                    } else if (button.querySelector('.fa-minus')) {
                        this.updateQuantity(productId, variantId, currentQuantity - 1);
                    }
                }
            }
        });

        // Quantity input change is handled by giohang.blade.php inline functions
        // No event listeners needed here to avoid duplicate calls

        // Remove item buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-danger')) {
                const button = e.target.closest('.btn-danger');
                const row = button.closest('tr[data-product-id]');
                if (row) {
                    const productId = parseInt(row.getAttribute('data-product-id'));
                    const variantId = parseInt(row.getAttribute('data-variant-id'));
                    this.showDeleteModal(productId, variantId);
                }
            }
        });

        // Clear all button
        const clearAllBtn = document.getElementById('clearAllBtn');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', () => {
                this.clearAllCart();
            });
        }
        
        // Quantity controls are handled by giohang.blade.php inline functions
        // No event listeners needed here to avoid duplicate calls
        
        // Remove any existing event listeners to prevent conflicts
        this.removeAllEventListeners();
    }

    /**
     * Update quantity of cart item - DISABLED (using giohang.blade.php function instead)
     */
    updateQuantity(productId, variantId, quantity) {
        console.log('Cart API updateQuantity called but disabled - using giohang.blade.php function instead');
        // This function is disabled to prevent duplicate calls
        // The giohang.blade.php updateQuantity function handles all updates
        return;
    }
    
    /**
     * Prevent duplicate calls by removing all event listeners
     */
    removeAllEventListeners() {
        // Remove any existing event listeners to prevent conflicts
        document.removeEventListener('click', this.handleQuantityClick);
        document.removeEventListener('change', this.handleQuantityChange);
        console.log('All event listeners removed to prevent duplicate calls');
    }

    /**
     * Update local cart item
     */
    updateLocalCartItem(productId, variantId, quantity) {
        const itemIndex = this.cartItems.findIndex(item => 
            item.product_id === productId && item.variant_id === variantId
        );
        
        if (itemIndex !== -1) {
            this.cartItems[itemIndex].quantity = quantity;
            this.saveCartToLocalStorage();
        }
    }

    /**
     * Check stock availability for quantity update
     */
    checkStockAvailability(productId, variantId, requestedQuantity) {
        const storageKey = `product_stock_${productId}`;
        const stored = localStorage.getItem(storageKey);
        
        if (!stored) {
            console.log('No stock data found, allowing quantity increase');
            return { available: true, message: '' }; // No stock data, allow update
        }
        
        const stockData = JSON.parse(stored);
        let availableStock = 0;
        
        if (variantId && variantId !== 0) {
            // Check variant stock
            const variantKey = `variant_${variantId}`;
            availableStock = stockData.variants?.[variantKey] || 0;
            console.log(`Checking variant stock: ${variantKey} = ${availableStock}`);
        } else {
            // Check main product stock
            availableStock = stockData.mainStock || 0;
            console.log(`Checking main product stock: ${availableStock}`);
        }
        
        console.log(`Stock check: requested=${requestedQuantity}, available=${availableStock}`);
        
        if (requestedQuantity > availableStock) {
            const itemType = variantId ? 'biến thể' : 'sản phẩm chính';
            const message = `Không đủ hàng! ${itemType} chỉ còn ${availableStock} sản phẩm.`;
            console.log(`Stock check failed: ${message}`);
            return {
                available: false,
                message: message
            };
        }
        
        console.log('Stock check passed');
        return { available: true, message: '' };
    }

    /**
     * Update stock in localStorage
     */
    updateStockInLocalStorage(productId, variantId, quantity, isRestore = false) {
        const storageKey = `product_stock_${productId}`;
        const stored = localStorage.getItem(storageKey);
        
        if (stored) {
            const stockData = JSON.parse(stored);
            
            if (variantId && variantId !== 0) {
                // Update variant stock
                if (!stockData.variants) {
                    stockData.variants = {};
                }
                
                const variantKey = `variant_${variantId}`;
                if (stockData.variants[variantKey] !== undefined) {
                    if (isRestore) {
                        stockData.variants[variantKey] += quantity; // Restore stock
                    } else {
                        stockData.variants[variantKey] = Math.max(0, stockData.variants[variantKey] - quantity); // Reduce stock
                    }
                }
            } else {
                // Update main product stock
                if (isRestore) {
                    stockData.mainStock += quantity; // Restore stock
                } else {
                    stockData.mainStock = Math.max(0, stockData.mainStock - quantity); // Reduce stock
                }
            }
            
            localStorage.setItem(storageKey, JSON.stringify(stockData));
            console.log('Stock updated:', { productId, variantId, quantity, isRestore, stockData });
        }
    }

    /**
     * Restore stock when item is removed from cart
     */
    restoreStockInLocalStorage(productId, variantId, quantity) {
        const storageKey = `product_stock_${productId}`;
        const stored = localStorage.getItem(storageKey);
        
        if (stored) {
            const stockData = JSON.parse(stored);
            
            if (variantId && variantId !== 0) {
                // Restore variant stock
                if (!stockData.variants) {
                    stockData.variants = {};
                }
                
                const variantKey = `variant_${variantId}`;
                if (stockData.variants[variantKey] !== undefined) {
                    stockData.variants[variantKey] += quantity;
                }
            } else {
                // Restore main product stock
                stockData.mainStock += quantity;
            }
            
            localStorage.setItem(storageKey, JSON.stringify(stockData));
        }
    }

    /**
     * Get max quantity for a product/variant
     */
    getMaxQuantity(productId, variantId) {
        const storageKey = `product_stock_${productId}`;
        const stored = localStorage.getItem(storageKey);
        
        if (stored) {
            const stockData = JSON.parse(stored);
            if (variantId && variantId !== 0) {
                // Check variant stock
                const variantKey = `variant_${variantId}`;
                return stockData.variants?.[variantKey] || 999;
            } else {
                // Check main product stock
                return stockData.mainStock || 999;
            }
        }
        
        return 999; // Default max
    }

    /**
     * Clear all stock data
     */
    clearAllStockData() {
        // Get all product IDs from cart
        const productIds = [...new Set(this.cartItems.map(item => item.product_id))];
        
        productIds.forEach(productId => {
            const storageKey = `product_stock_${productId}`;
            localStorage.removeItem(storageKey);
        });
    }

    /**
     * Remove item from cart - localStorage only
     */
    removeFromCart(productId, variantId) {
        try {
            // Find item to get quantity before removing
            const itemToRemove = this.cartItems.find(item => 
                item.product_id === productId && item.variant_id === variantId
            );
            
            if (!itemToRemove) {
                this.showNotification('Sản phẩm không tồn tại trong giỏ hàng', 'error');
                return;
            }
            
            // Remove from local cart
            this.cartItems = this.cartItems.filter(item => 
                !(item.product_id === productId && item.variant_id === variantId)
            );
            this.saveCartToLocalStorage();
            
            // Update display
            this.renderCartItems();
            this.updateCartDisplay();
            
            // Update stock in localStorage (restore stock)
            this.restoreStockInLocalStorage(productId, variantId, itemToRemove.quantity);
            
            this.showNotification('Đã xóa sản phẩm khỏi giỏ hàng', 'success');
            
        } catch (error) {
            console.error('Error removing from cart:', error);
            this.showNotification('Có lỗi xảy ra khi xóa sản phẩm', 'error');
        }
    }

    /**
     * Clear all cart items - localStorage only
     */
    clearAllCart() {
        try {
            // Clear local cart
            this.cartItems = [];
            this.saveCartToLocalStorage();
            
            // Update display
            this.renderCartItems();
            this.updateCartDisplay();
            
            // Clear all stock data
            this.clearAllStockData();
            
            this.showNotification('Đã xóa tất cả sản phẩm khỏi giỏ hàng', 'success');
            
        } catch (error) {
            console.error('Error clearing cart:', error);
            this.showNotification('Có lỗi xảy ra khi xóa giỏ hàng', 'error');
        }
    }

    /**
     * Render cart items from localStorage
     */
    renderCartItems() {
        const cartEmptyDiv = document.getElementById('cart-empty');
        const cartContentDiv = document.getElementById('cart-content');
        const cartItemsTbody = document.getElementById('cart-items');
        
        if (!cartItemsTbody) return;
        
        if (this.cartItems.length === 0) {
            // Show empty cart
            if (cartEmptyDiv) cartEmptyDiv.style.display = 'block';
            if (cartContentDiv) cartContentDiv.style.display = 'none';
            return;
        }
        
        // Show cart content
        if (cartEmptyDiv) cartEmptyDiv.style.display = 'none';
        if (cartContentDiv) cartContentDiv.style.display = 'block';
        
        // Render cart items
        cartItemsTbody.innerHTML = this.cartItems.map(item => {
            const itemType = item.variant_id && item.variant_id !== 0 ? 'Biến thể' : 'Sản phẩm chính';
            const variantInfo = item.variant_id && item.variant_id !== 0 ? 
                `<small class="text-muted">${item.color_name || ''} - ${item.size_name || ''}</small>` : 
                '<small class="text-muted">Sản phẩm gốc</small>';
            
            // Fix product name display
            const productName = item.name || item.product_name || 'Sản phẩm';
            
            const totalPrice = item.price * item.quantity;
            
            // Get max quantity for this item
            const maxQuantity = this.getMaxQuantity(item.product_id, item.variant_id);
            const isAtMax = item.quantity >= maxQuantity;
            
            return `
                <tr data-product-id="${item.product_id}" data-variant-id="${item.variant_id || 0}">
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="${item.image || '/backend/img/p1.jpg'}" 
                                 class="cart-item-image me-3" 
                                 alt="${productName}" 
                                 onerror="this.src='/backend/img/p1.jpg'" />
                            <div>
                                <h6 class="mb-1">${productName}</h6>
                                ${variantInfo}
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge ${item.variant_id && item.variant_id !== 0 ? 'bg-info' : 'bg-primary'}">${itemType}</span>
                    </td>
                    <td>
                        <span class="price">${new Intl.NumberFormat('vi-VN').format(item.price)}₫</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary btn-sm quantity-minus" 
                                    onclick="decreaseQuantity(${item.product_id}, ${item.variant_id || 0})">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                   class="form-control quantity-input mx-2 text-center" 
                                   value="${item.quantity}" 
                                   min="1"
                                   readonly>
                            <button class="btn btn-outline-secondary btn-sm quantity-plus" 
                                    onclick="increaseQuantity(${item.product_id}, ${item.variant_id || 0})">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td>
                        <span class="item-total">${new Intl.NumberFormat('vi-VN').format(totalPrice)}₫</span>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm" 
                                onclick="removeFromCart(${item.product_id}, ${item.variant_id || 0})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    /**
     * Update cart display
     */
    updateCartDisplay() {
        // Calculate accurate cart count
        const totalItems = this.cartItems.reduce((sum, item) => sum + item.quantity, 0);
        console.log('Cart count calculation:', {
            items: this.cartItems.length,
            totalQuantity: totalItems,
            items: this.cartItems.map(item => ({id: item.product_id, variant: item.variant_id, qty: item.quantity}))
        });
        
        // Update cart count in header if exists
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = totalItems;
            
            // Show/hide badge based on count
            if (totalItems === 0) {
                cartCountElement.style.display = 'none';
            } else {
                cartCountElement.style.display = 'inline-block';
            }
        }

        // Update totals
        this.updateCartTotals();
        
        // Also update global cart count function
        if (typeof updateCartCount === 'function') {
            updateCartCount();
        }
    }

    /**
     * Update cart totals
     */
    updateCartTotals() {
        const total = this.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        const cartTotalElement = document.getElementById('cart-total');
        const cartFinalTotalElement = document.getElementById('cart-final-total');
        
        if (cartTotalElement) {
            cartTotalElement.textContent = new Intl.NumberFormat('vi-VN').format(total) + '₫';
        }
        
        if (cartFinalTotalElement) {
            cartFinalTotalElement.textContent = new Intl.NumberFormat('vi-VN').format(total) + '₫';
        }
    }

    /**
     * Show delete confirmation modal
     */
    showDeleteModal(productId, variantId) {
        const modal = document.getElementById('deleteModal');
        const messageElement = document.getElementById('deleteModalMessage');
        
        if (modal && messageElement) {
            messageElement.textContent = 'Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?';
            
            // Store current item for deletion
            modal.dataset.productId = productId;
            modal.dataset.variantId = variantId;
            
            modal.classList.add('show');
        }
    }

    /**
     * Close delete modal
     */
    closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }

    /**
     * Confirm delete
     */
    confirmDelete() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            const productId = parseInt(modal.dataset.productId);
            const variantId = parseInt(modal.dataset.variantId);
            
            this.removeFromCart(productId, variantId);
            this.closeDeleteModal();
        }
    }

    /**
     * Show notification - only one at a time
     */
    showNotification(message, type = 'info') {
        // Remove existing notifications first
        const existingNotifications = document.querySelectorAll('.toast-notification');
        existingNotifications.forEach(notification => {
            if (notification.parentElement) {
                notification.remove();
            }
        });
        
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add CSS for toast
        toast.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 99999;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
            font-size: 14px;
        `;
        
        document.body.appendChild(toast);
        
        // Animation show
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 3000);
    }
}

// Initialize cart manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart page loaded, initializing Cart API Manager...');
    window.cartAPIManager = new CartAPIManager();
});

// Global functions for backward compatibility
function updateQuantity(productId, variantId, quantity) {
    if (window.cartAPIManager) {
        window.cartAPIManager.updateQuantity(productId, variantId, quantity);
    }
}

function removeFromCart(productId, variantId) {
    if (window.cartAPIManager) {
        window.cartAPIManager.showDeleteModal(productId, variantId);
    }
}

function closeDeleteModal() {
    if (window.cartAPIManager) {
        window.cartAPIManager.closeDeleteModal();
    }
}

function confirmDelete() {
    if (window.cartAPIManager) {
        window.cartAPIManager.confirmDelete();
    }
}

function showNotification(message, type = 'info') {
    if (window.cartAPIManager) {
        window.cartAPIManager.showNotification(message, type);
    }
}
