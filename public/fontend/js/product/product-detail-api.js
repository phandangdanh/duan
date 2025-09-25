/**
 * Product Detail Page Manager
 * Handles product detail page functionality using API
 */
class ProductDetailManager {
    constructor() {
        this.api = new ProductDetailApiClient();
        this.productId = null;
        this.product = null;
        this.relatedProducts = [];
        this.selectedColor = null;
        this.selectedSize = null;
        this.selectedVariant = null;
        this.quantity = 1;
        this.basePrice = 0;
        this.baseSalePrice = 0;
    }

    /**
     * Initialize the product detail manager
     */
    async init() {
        console.log('=== INITIALIZING PRODUCT DETAIL ===');
        
        // Get product ID from URL
        const pathParts = window.location.pathname.split('/');
        this.productId = pathParts[pathParts.length - 1];
        console.log('Product ID from URL:', this.productId);
        
        if (!this.productId || isNaN(this.productId)) {
            console.error('Invalid product ID:', this.productId);
            this.showError('ID sản phẩm không hợp lệ');
            return;
        }
        
        console.log('ProductDetailManager available, initializing...');
        await this.loadProductDetail();
    }

    /**
     * Load product detail from API
     */
    async loadProductDetail() {
        try {
            console.log('Loading product detail for ID:', this.productId);
            const response = await this.api.getProductDetail(this.productId);
            console.log('Product detail response:', response);
            
            if (response.status_code === 200 && response.data) {
                this.product = response.data;
                console.log('Processing product data:', this.product);
                this.processProductData();
                this.renderProductDetail();
                console.log('Product data processed:', this.product);
            } else {
                console.error('Invalid API response:', response);
                this.showError('Không thể tải thông tin sản phẩm');
            }
        } catch (error) {
            console.error('Error loading product detail:', error);
            this.showError('Có lỗi xảy ra khi tải sản phẩm');
        }
    }

    /**
     * Auto-sync stock from cart when page loads
     */
    autoSyncStockFromCart() {
        const storageKey = `product_stock_${this.product.id}`;
        const stored = localStorage.getItem(storageKey);
        
        // Nếu chưa có stock data, tạo mới và sync từ cart
        if (!stored) {
            console.log('📦 No stock data found, creating and syncing from cart...');
            this.syncStockFromCart();
        } else {
            console.log('📦 Stock data exists, keeping existing data');
        }
    }

    /**
     * Process product data and set defaults
     */
    processProductData() {
        if (!this.product) return;
        console.log('Processing product data:', this.product);
        this.quantity = 1;
        this.basePrice = this.product.base_price || 0;
        this.baseSalePrice = this.product.base_sale_price || 0;
        
        // Auto-sync stock from cart first
        this.autoSyncStockFromCart();
        
        // Load stock from localStorage
        this.updateStockDisplay(); // This will update this.product.soLuong and variant.soLuong
        
        // Check and switch to variants if main product is out of stock
        this.checkStockAndSwitch();
        
        this.selectedColor = null;
        this.selectedSize = null;
        this.selectedVariant = null;
        
        console.log('Product data processed:', this.product);
    }

    /**
     * Render product detail information
     */
    renderProductDetail() {
        console.log('Rendering product detail...', this.product);
        
        // Update product title
        const titleElement = document.getElementById('product-title');
        if (titleElement) {
            const productName = this.product.tenSP || this.product.ten || this.product.name || 'Đang tải...';
            titleElement.textContent = productName;
            console.log('Product title updated:', productName);
        }
        
        // Update product description
        const descElement = document.getElementById('product-description');
        if (descElement) {
            descElement.innerHTML = this.product.mota || 'Đang tải...';
            console.log('Product description updated');
        }
        
        // Update price
        this.updatePrice();
        
        // Render images
        this.renderProductImages();
        console.log('Calling renderProductImages...');
        
        // Render variants
        console.log('Rendering color options...');
        this.renderColorOptions();
        console.log('Rendering size options...');
        this.renderSizeOptions();
        
        // Update stock info
        this.updateStockInfo();
        
        console.log('Product detail rendering completed');
    }

    /**
     * Render product images
     */
    renderProductImages() {
        console.log('Rendering product images...', this.product);
        
        const mainImageElement = document.getElementById('main-product-image');
        const thumbnailContainer = document.getElementById('thumbnail-gallery');
        
        console.log('Main image element:', mainImageElement);
        console.log('Thumbnail container:', thumbnailContainer);
        
        if (!this.product.hinhanh || this.product.hinhanh.length === 0) {
            console.log('No images found for product');
            return;
        }
        
        const images = this.product.hinhanh;
        console.log('Images to render:', images);
        
        // Update main image
        if (mainImageElement && images.length > 0) {
            const firstImage = images[0];
            mainImageElement.src = firstImage.url || firstImage;
            mainImageElement.alt = this.product.ten || 'Product Image';
            console.log('Main image updated:', firstImage.url || firstImage);
        }
        
        // Render thumbnails
        if (thumbnailContainer) {
            const thumbnailsHtml = images.map((image, index) => {
                const imageUrl = image.url || image;
                const isActive = index === 0 ? 'active' : '';
                return `
                    <img src="${imageUrl}" 
                         alt="${this.product.ten || 'Product Image'}" 
                         class="thumbnail ${isActive}" 
                         onclick="productDetailManager.selectImage(${index})">
                `;
            }).join('');
            
            thumbnailContainer.innerHTML = thumbnailsHtml;
            console.log('Thumbnails rendered:', thumbnailsHtml);
        }
    }

    /**
     * Select image for main display
     */
    selectImage(index) {
        const mainImageElement = document.getElementById('main-product-image');
        const thumbnails = document.querySelectorAll('.thumbnail');
        
        if (this.product.hinhanh && this.product.hinhanh[index]) {
            const selectedImage = this.product.hinhanh[index];
            const imageUrl = selectedImage.url || selectedImage;
            
            if (mainImageElement) {
                mainImageElement.src = imageUrl;
            }
            
            // Update active thumbnail
            thumbnails.forEach((thumb, i) => {
                thumb.classList.toggle('active', i === index);
            });
        }
    }

    /**
     * Render color options
     */
    renderColorOptions() {
        console.log('renderColorOptions called');
        if (!this.product || !this.product.chitietsanpham) {
            console.log('No product or variants data');
            return;
        }
        
        // Find color container - look for variant-options in color section
        // Try multiple selectors for color section
        let colorSection = document.querySelector('.variant-section:nth-child(2) .variant-group .variant-options');
        console.log('Color section found (nth-child):', colorSection);
        
        if (!colorSection) {
            // Try first variant-section (color is usually first)
            colorSection = document.querySelectorAll('.variant-section')[0]?.querySelector('.variant-options');
            console.log('Color section found (first):', colorSection);
        }
        
        if (!colorSection) {
            // Try by label text
            const variantSections = document.querySelectorAll('.variant-section');
            for (let i = 0; i < variantSections.length; i++) {
                const label = variantSections[i].querySelector('.variant-label');
                if (label && label.textContent.includes('Màu sắc')) {
                    colorSection = variantSections[i].querySelector('.variant-options');
                    console.log('Color section found by label:', colorSection);
                    break;
                }
            }
        }
        
        if (!colorSection) {
            console.log('Color section not found with any selector');
            return;
        }
        
        // Always show color options
        console.log('Product variants:', this.product.chitietsanpham);
        const colors = [...new Set(this.product.chitietsanpham.map(v => ({
            id: v.id_mausac,
            name: v.mausac ? v.mausac.ten : 'Không xác định'
        })))].filter((color, index, self) => 
            index === self.findIndex(c => c.id === color.id)
        );
        
        console.log('Colors to render:', colors);
        
        colorSection.innerHTML = colors.map(color => `
            <div class="variant-option"
                 data-color-id="${color.id}"
                 data-color-name="${color.name}"
                 onclick="productDetailManager.selectColor(${color.id}, '${color.name}')">
                ${color.name}
            </div>
        `).join('');
        
        console.log('Color options rendered');
    }

    /**
     * Render size options
     */
    renderSizeOptions() {
        console.log('renderSizeOptions called');
        if (!this.product || !this.product.chitietsanpham) {
            console.log('No product or variants data');
            return;
        }
        
        // Find size container - look for variant-options in size section
        let sizeSection = document.querySelector('.variant-section:nth-child(3) .variant-group .variant-options');
        console.log('Size section found:', sizeSection);
        if (!sizeSection) {
            console.log('Size section not found, trying alternative selector...');
            // Try different approaches to find size section
            const variantSections = document.querySelectorAll('.variant-section');
            console.log('All variant sections:', variantSections.length);
            
            // Look for size section by label text
            for (let i = 0; i < variantSections.length; i++) {
                const label = variantSections[i].querySelector('.variant-label');
                if (label && label.textContent.includes('Size')) {
                    sizeSection = variantSections[i].querySelector('.variant-options');
                    console.log('Found size section by label:', sizeSection);
                    break;
                }
            }
            
            if (!sizeSection) return;
        }
        
        if (!this.selectedColor) {
            sizeSection.innerHTML = '<p class="text-muted">Vui lòng chọn màu sắc trước</p>';
            console.log('📏 No color selected, showing placeholder');
            return;
        }
        
        console.log('📏 Selected color:', this.selectedColor);
        console.log('📏 All variants:', this.product.chitietsanpham);
        
        // Filter variants by selected color
        const colorVariants = this.product.chitietsanpham.filter(v => v.id_mausac === this.selectedColor);
        console.log('📏 Variants for selected color:', colorVariants);
        
        const sizes = [...new Set(colorVariants.map(v => ({
            id: v.id_size,
            name: v.size ? v.size.ten : 'Không xác định'
        })))].filter((size, index, self) => 
            index === self.findIndex(s => s.id === size.id)
        );
        
        console.log('📏 Sizes to render:', sizes);
        
        sizeSection.innerHTML = sizes.map(size => `
            <div class="variant-option"
                 data-size-id="${size.id}"
                 data-size-name="${size.name}"
                 onclick="productDetailManager.selectSize(${size.id}, '${size.name}')">
                ${size.name}
            </div>
        `).join('');
        
        console.log('Size options rendered');
        
        console.log('Rendering size options for color:', this.selectedColor);
    }

    /**
     * Select color option
     */
    selectColor(colorId, colorName) {
        console.log('🎨 selectColor called:', { colorId, colorName });
        const isSameColor = this.selectedColor === colorId;
        console.log('🎨 Is same color:', isSameColor);
        
        if (isSameColor) {
            // Toggle off - deselect color
            console.log('🎨 Deselecting color');
            this.selectedColor = null;
            this.selectedSize = null;
            this.selectedVariant = null;
        } else {
            // Select new color
            console.log('🎨 Selecting new color:', colorName);
            this.selectedColor = colorId;
            this.selectedSize = null;
            this.selectedVariant = null;
        }
        
        console.log('🎨 Selected color after update:', this.selectedColor);
        this.renderSizeOptions();
        this.updatePrice();
        this.updateStockInfo();
        
        // Update active state
        document.querySelectorAll('[data-color-id]').forEach(option => {
            option.classList.remove('active');
        });
        
        if (this.selectedColor) {
            const colorElement = document.querySelector(`[data-color-id="${colorId}"]`);
            if (colorElement) {
                colorElement.classList.add('active');
                console.log('🎨 Color element activated:', colorElement);
            } else {
                console.log('🎨 Color element not found for ID:', colorId);
            }
        }
    }

    /**
     * Select size option
     */
    selectSize(sizeId, sizeName) {
        this.selectedSize = sizeId;
        this.selectedVariant = this.getSelectedVariant();
        
        this.updatePrice();
        this.updateStockInfo();
        
        // Update active state
        document.querySelectorAll('[data-size-id]').forEach(option => {
            option.classList.remove('active');
        });
        
        const sizeElement = document.querySelector(`[data-size-id="${sizeId}"]`);
        if (sizeElement) {
            sizeElement.classList.add('active');
        }
    }

    /**
     * Get selected variant
     */
    getSelectedVariant() {
        console.log('🔍 getSelectedVariant called');
        console.log('🔍 Selected color:', this.selectedColor);
        console.log('🔍 Selected size:', this.selectedSize);
        console.log('🔍 Product variants:', this.product.chitietsanpham);
        
        if (!this.selectedColor || !this.selectedSize || !this.product.chitietsanpham) {
            console.log('🔍 No variant selected (missing color, size, or variants)');
            return null;
        }
        
        const variant = this.product.chitietsanpham.find(v => 
            v.id_mausac === this.selectedColor && v.id_size === this.selectedSize
        );
        
        console.log('🔍 Found variant:', variant);
        return variant;
    }

    /**
     * Update price display
     */
    updatePrice() {
        const priceElement = document.getElementById('product-price');
        if (!priceElement) return;
        
        let priceHtml = '';
        
        if (this.selectedVariant) {
            // Show variant price
            const variantPrice = this.selectedVariant.gia || 0;
            const variantSalePrice = this.selectedVariant.gia_khuyen_mai || 0;
            
            if (variantSalePrice > 0 && variantSalePrice < variantPrice) {
                priceHtml = `
                    <span class="text-decoration-line-through text-muted me-2">${new Intl.NumberFormat('vi-VN').format(variantPrice)} VNĐ</span>
                    <span class="text-danger fw-bold">${new Intl.NumberFormat('vi-VN').format(variantSalePrice)} VNĐ</span>
                `;
            } else {
                priceHtml = `<span class="text-danger fw-bold">${new Intl.NumberFormat('vi-VN').format(variantPrice)} VNĐ</span>`;
            }
        } else {
            // Show base price
            if (this.baseSalePrice > 0 && this.baseSalePrice < this.basePrice) {
                priceHtml = `
                    <span class="text-decoration-line-through text-muted me-2">${new Intl.NumberFormat('vi-VN').format(this.basePrice)} VNĐ</span>
                    <span class="text-danger fw-bold">${new Intl.NumberFormat('vi-VN').format(this.baseSalePrice)} VNĐ</span>
                `;
            } else {
                priceHtml = `<span class="text-danger fw-bold">${new Intl.NumberFormat('vi-VN').format(this.basePrice)} VNĐ</span>`;
            }
        }
        
        priceElement.innerHTML = priceHtml;
        console.log('Price updated:', priceHtml);
    }

    /**
     * Update stock information display
     */
    updateStockInfo() {
        const variant = this.getSelectedVariant();
        
        // Load stock from localStorage first
        const stockData = this.getLocalStock();
        const mainStock = stockData.mainStock || this.product.soLuong || 0;
        
        // Update product stock with localStorage data
        this.product.soLuong = mainStock;
        
        const variants = this.product.chitietsanpham || [];
        
        // Update variant stocks from localStorage
        if (variants && stockData.variants) {
            variants.forEach(variant => {
                const variantKey = `variant_${variant.id}`;
                if (stockData.variants[variantKey] !== undefined) {
                    variant.soLuong = stockData.variants[variantKey];
                }
            });
        }
        
        const totalVariantStock = variants.reduce((sum, v) => sum + (v.soLuong || 0), 0);
        const uniqueColors = [...new Set(variants.map(v => v.id_mausac))].length;
        const uniqueSizes = [...new Set(variants.map(v => v.id_size))].length;
        const totalAvailableStock = mainStock + totalVariantStock;
        
        // Dynamic "Số lượng có sẵn" - shows current selection
        let currentAvailableStock;
        if (this.selectedColor && this.selectedSize && variant) {
            currentAvailableStock = variant.soLuong; // Variant selected
        } else {
            currentAvailableStock = mainStock; // No variant selected, show main product stock
        }
        
        // Update stock display elements
        const availableStockElement = document.getElementById('available-stock');
        const mainStockElement = document.getElementById('main-stock');
        const variantStockElement = document.getElementById('variant-stock');
        const totalStockElement = document.getElementById('total-stock');
        
        if (availableStockElement) {
            availableStockElement.textContent = currentAvailableStock;
        }
        
        if (mainStockElement) {
            mainStockElement.textContent = mainStock;
        }
        
        if (variantStockElement) {
            variantStockElement.textContent = `${totalVariantStock} sản phẩm (${uniqueColors} màu, ${uniqueSizes} size)`;
        }
        
        if (totalStockElement) {
            totalStockElement.textContent = totalAvailableStock;
        }
        
        // Update quantity input
        const quantityInput = document.getElementById('quantity-input');
        if (quantityInput) {
            const maxQuantity = currentAvailableStock;
            quantityInput.max = maxQuantity;
            quantityInput.value = Math.min(this.quantity, maxQuantity);
        }
    }

    /**
     * Update quantity
     */
    updateQuantity() {
        const quantityInput = document.getElementById('quantity-input');
        if (quantityInput) {
            this.quantity = parseInt(quantityInput.value) || 1;
            this.updateStockInfo();
        }
    }

    /**
     * Buy now (đơn giản như trang chủ)
     */
    async buyNow() {
        try {
            console.log('=== ProductDetailManager buyNow called ===');
            console.log('Product ID:', this.productId);
            console.log('Quantity:', this.quantity);
            
            // Đơn giản: chỉ gọi function buyNowFromHomepage như trang chủ
            if (typeof buyNowFromHomepage === 'function') {
                buyNowFromHomepage(this.productId);
            } else {
                // Fallback: redirect trực tiếp
                console.log('buyNowFromHomepage not available, redirecting directly...');
                window.location.href = `/checkout?product_id=${this.productId}&quantity=${this.quantity}`;
            }
            
        } catch (error) {
            console.error('Error in buyNow:', error);
            // Fallback: redirect trực tiếp
            window.location.href = `/checkout?product_id=${this.productId}&quantity=${this.quantity}`;
        }
    }

    /**
     * Add to cart
     */
    async addToCart() {
        try {
            const variant = this.getSelectedVariant();
            let cartData = {
                product_id: this.product.id,
                quantity: this.quantity
            };
            
            let stockType = 'main';
            if (variant) {
                cartData.variant_id = variant.id;
                cartData.color_id = variant.id_mausac;
                cartData.size_id = variant.id_size;
                stockType = 'variant';
            }
            
            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(cartData)
            });
            
            const data = await response.json();
            console.log('🛒 Server response:', data);
            
            if (data.success) {
                // Check stock before updating
                const currentStock = this.getCurrentStock();
                console.log('📦 Current stock before update:', currentStock);
                console.log('📦 Quantity to subtract:', this.quantity);
                
                if (currentStock <= 0) {
                    const itemType = variant ? 'biến thể' : 'sản phẩm chính';
                    const itemName = variant ? `${variant.mausac?.ten || 'Màu'} - ${variant.size?.ten || 'Size'}` : 'sản phẩm chính';
                    this.showError(`Sản phẩm đã hết hàng! ${itemType} "${itemName}" không còn sản phẩm nào`);
                    console.log(`❌ Stock error: ${itemType} "${itemName}" - Requested: ${this.quantity}, Available: ${currentStock}`);
                    return;
                }
                
                if (this.quantity > currentStock) {
                    const itemType = variant ? 'biến thể' : 'sản phẩm chính';
                    const itemName = variant ? `${variant.mausac?.ten || 'Màu'} - ${variant.size?.ten || 'Size'}` : 'sản phẩm chính';
                    this.showError(`Không đủ hàng! ${itemType} "${itemName}" chỉ còn ${currentStock} sản phẩm`);
                    console.log(`❌ Stock error: ${itemType} "${itemName}" - Requested: ${this.quantity}, Available: ${currentStock}`);
                    return;
                }
                
                // Update local stock
                console.log('📦 Updating local stock...');
                this.updateLocalStock(stockType, this.quantity);
                
                // Add to local cart
                console.log('🛒 Adding to local cart...');
                this.addToLocalCart(cartData, variant);
                
                // Update cart count if function exists
                if (typeof updateCartCount === 'function') {
                    updateCartCount();
                }
                
                // Also update cart count directly
                this.updateCartCountDirectly();
                
                this.showSuccess('Đã thêm vào giỏ hàng');
            } else {
                this.showError(data.message || 'Thêm vào giỏ hàng thất bại');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showError('Có lỗi xảy ra khi thêm vào giỏ hàng');
        }
    }

    /**
     * Add item to local cart storage
     */
    addToLocalCart(cartData, variant) {
        try {
            // Get existing cart
            const existingCart = JSON.parse(localStorage.getItem('cart_data') || '[]');
            
            // Check if item already exists
            const existingItemIndex = existingCart.findIndex(item => 
                item.product_id === cartData.product_id && 
                item.variant_id === (cartData.variant_id || 0)
            );
            
            const cartItem = {
                product_id: cartData.product_id,
                variant_id: cartData.variant_id || 0,
                quantity: cartData.quantity,
                name: this.product.tenSP || this.product.ten || this.product.name || 'Sản phẩm',
                price: variant ? (variant.gia_khuyen_mai || variant.gia) : (this.baseSalePrice || this.basePrice),
                image: this.product.hinhanh && this.product.hinhanh[0] ? this.product.hinhanh[0].url : '/backend/img/p1.jpg',
                color_name: variant ? variant.mausac.ten : null,
                size_name: variant ? variant.size.ten : null
            };
            
            if (existingItemIndex !== -1) {
                // Update existing item quantity
                existingCart[existingItemIndex].quantity += cartData.quantity;
            } else {
                // Add new item
                existingCart.push(cartItem);
            }
            
            // Save to localStorage
            localStorage.setItem('cart_data', JSON.stringify(existingCart));
            console.log('Item added to local cart:', cartItem);
            
        } catch (error) {
            console.error('Error adding to local cart:', error);
        }
    }

    /**
     * Get current stock for selected item
     */
    getCurrentStock() {
        const variant = this.getSelectedVariant();
        const stockData = this.getLocalStock();
        
        console.log('📦 getCurrentStock called');
        console.log('📦 Selected variant:', variant);
        console.log('📦 Stock data:', stockData);
        
        if (variant) {
            // Return variant stock
            const variantKey = `variant_${variant.id}`;
            const variantStock = stockData.variants?.[variantKey] || variant.soLuong || 0;
            console.log(`📦 Variant stock calculation:`);
            console.log(`📦   - Variant key: ${variantKey}`);
            console.log(`📦   - Stock from localStorage: ${stockData.variants?.[variantKey]}`);
            console.log(`📦   - Stock from variant.soLuong: ${variant.soLuong}`);
            console.log(`📦   - Final stock: ${variantStock}`);
            return variantStock;
        } else {
            // Return main product stock
            const mainStock = stockData.mainStock || this.product.soLuong || 0;
            console.log('📦 Main product stock:', mainStock);
            return mainStock;
        }
    }

    /**
     * Update local stock after adding to cart
     */
    updateLocalStock(stockType, quantity) {
        console.log('📦 updateLocalStock called:', { stockType, quantity });
        
        const storageKey = `product_stock_${this.product.id}`;
        let stockData = this.getLocalStock(); // This will create initial data if not exists
        
        console.log('📦 Current stock data:', stockData);
        
        if (stockType === 'variant' && this.selectedVariant) {
            // Update variant stock
            if (!stockData.variants) {
                stockData.variants = {};
            }
            
            const variantKey = `variant_${this.selectedVariant.id}`;
            const oldStock = stockData.variants[variantKey] || this.selectedVariant.soLuong || 0;
            stockData.variants[variantKey] = Math.max(0, oldStock - quantity);
            
            console.log(`📦 Variant ${variantKey}: ${oldStock} → ${stockData.variants[variantKey]}`);
        } else {
            // Update main product stock
            const oldStock = stockData.mainStock || this.product.soLuong || 0;
            stockData.mainStock = Math.max(0, oldStock - quantity);
            
            console.log(`📦 Main product: ${oldStock} → ${stockData.mainStock}`);
        }
        
        localStorage.setItem(storageKey, JSON.stringify(stockData));
        console.log('📦 Stock saved to localStorage');
        
        // Update display
        this.updateStockDisplay();
        
        // Check if main product is out of stock and switch to variants
        this.checkStockAndSwitch();
    }

    /**
     * Get local stock data
     */
    getLocalStock() {
        const storageKey = `product_stock_${this.product.id}`;
        const stored = localStorage.getItem(storageKey);
        
        let stockData;
        
        if (stored) {
            stockData = JSON.parse(stored);
            console.log('📦 Found stored stock data:', stockData);
            
            // Đảm bảo có đầy đủ variants nếu thiếu
            if (this.product.chitietsanpham && stockData.variants) {
                this.product.chitietsanpham.forEach(variant => {
                    const variantKey = `variant_${variant.id}`;
                    if (stockData.variants[variantKey] === undefined) {
                        stockData.variants[variantKey] = variant.soLuong || 0;
                        console.log(`📦 Adding missing variant ${variantKey}: ${variant.soLuong || 0}`);
                    }
                });
            }
        } else {
            // Create initial stock data if not found - chỉ tạo với giá trị ban đầu
            stockData = {
                mainStock: this.product.soLuong || 0,
                variants: {}
            };
            console.log('📦 Creating initial stock data:', stockData);
        }
        
        // Always ensure all variants are included
        if (this.product.chitietsanpham) {
            if (!stockData.variants) {
                stockData.variants = {};
            }
            
            this.product.chitietsanpham.forEach(variant => {
                const variantKey = `variant_${variant.id}`;
                // Only set if not already exists (preserve existing stock)
                if (stockData.variants[variantKey] === undefined) {
                    stockData.variants[variantKey] = variant.soLuong || 0;
                    console.log(`📦 Adding missing variant ${variantKey}: ${variant.soLuong || 0}`);
                }
            });
        }
        
        // Save updated stock data only if it was modified
        const currentStored = localStorage.getItem(storageKey);
        const currentStockData = currentStored ? JSON.parse(currentStored) : null;
        
        // Only save if data has changed
        if (!currentStockData || JSON.stringify(currentStockData) !== JSON.stringify(stockData)) {
            localStorage.setItem(storageKey, JSON.stringify(stockData));
            console.log('📦 Stock data updated:', stockData);
        } else {
            console.log('📦 Stock data unchanged, keeping existing data');
        }
        
        console.log('📦 Final stock data:', stockData);
        
        return stockData;
    }

    /**
     * Sync stock data from cart (when needed)
     */
    syncStockFromCart() {
        const storageKey = `product_stock_${this.product.id}`;
        const cartData = localStorage.getItem('cart_data');
        let cartItems = [];
        
        if (cartData) {
            try {
                cartItems = JSON.parse(cartData);
            } catch (error) {
                console.error('Error parsing cart data:', error);
                return;
            }
        }
        
        // Tính toán stock đã dùng trong cart cho sản phẩm này
        let mainStockUsed = 0;
        let variantStockUsed = {};
        
        cartItems.forEach(item => {
            if (item.product_id === this.product.id) {
                if (item.variant_id && item.variant_id !== 0) {
                    const variantKey = `variant_${item.variant_id}`;
                    variantStockUsed[variantKey] = (variantStockUsed[variantKey] || 0) + item.quantity;
                } else {
                    mainStockUsed += item.quantity;
                }
            }
        });
        
        console.log('📦 Syncing stock from cart:', {
            mainStockUsed,
            variantStockUsed,
            cartItems: cartItems.filter(item => item.product_id === this.product.id)
        });
        
        // Cập nhật stock data
        const stockData = {
            mainStock: Math.max(0, (this.product.soLuong || 0) - mainStockUsed),
            variants: {}
        };
        
        // Cập nhật variants
        if (this.product.chitietsanpham) {
            this.product.chitietsanpham.forEach(variant => {
                const variantKey = `variant_${variant.id}`;
                const variantUsed = variantStockUsed[variantKey] || 0;
                stockData.variants[variantKey] = Math.max(0, (variant.soLuong || 0) - variantUsed);
            });
        }
        
        localStorage.setItem(storageKey, JSON.stringify(stockData));
        console.log('📦 Stock synced from cart:', stockData);
        
        // Update display
        this.updateStockDisplay();
    }

    /**
     * Reset stock data to original values (for debugging)
     */
    resetStockData() {
        const storageKey = `product_stock_${this.product.id}`;
        
        // Create original stock data
        const originalStockData = {
            mainStock: this.product.soLuong || 0,
            variants: {}
        };
        
        // Add all variants with original stock
        if (this.product.chitietsanpham) {
            this.product.chitietsanpham.forEach(variant => {
                const variantKey = `variant_${variant.id}`;
                originalStockData.variants[variantKey] = variant.soLuong || 0;
            });
        }
        
        localStorage.setItem(storageKey, JSON.stringify(originalStockData));
        console.log('📦 Stock data reset to original:', originalStockData);
        
        // Update display
        this.updateStockDisplay();
    }

    /**
     * Update stock display from localStorage
     */
    updateStockDisplay() {
        console.log('📦 updateStockDisplay called');
        const stockData = this.getLocalStock();
        console.log('📦 Stock data from localStorage:', stockData);
        console.log('📦 Original product stock:', this.product.soLuong);
        
        // Update main product stock
        this.product.soLuong = stockData.mainStock;
        console.log('📦 Updated product stock:', this.product.soLuong);
        
        // Update variant stocks
        if (this.product.chitietsanpham) {
            this.product.chitietsanpham.forEach(variant => {
                const variantKey = `variant_${variant.id}`;
                if (stockData.variants && stockData.variants[variantKey] !== undefined) {
                    variant.soLuong = stockData.variants[variantKey];
                    console.log(`📦 Updated variant ${variantKey} stock from localStorage:`, variant.soLuong);
                } else {
                    // Keep original stock if not in localStorage
                    console.log(`📦 Keeping original variant ${variantKey} stock:`, variant.soLuong);
                }
            });
        }
        
        // Update UI
        this.updateStockInfo();
    }

    /**
     * Check stock and switch to variants if main product is out
     */
    checkStockAndSwitch() {
        const mainStock = this.product.soLuong || 0;
        console.log('🔍 checkStockAndSwitch called, main stock:', mainStock);
        console.log('🔍 Product variants:', this.product.chitietsanpham);
        console.log('🔍 Variants length:', this.product.chitietsanpham?.length);
        
        if (mainStock === 0 && this.product.chitietsanpham && this.product.chitietsanpham.length > 0) {
            console.log('⚠️ Main product out of stock, switching to variants');
            this.showInfo('⚠️ Sản phẩm chính đã hết hàng! Tự động chuyển sang biến thể...');
            
            // Only auto-select if no user selection exists
            if (!this.selectedColor && !this.selectedSize) {
                // Auto-select first color if available
                const firstVariant = this.product.chitietsanpham[0];
                console.log('🔍 First variant:', firstVariant);
                
                if (firstVariant && firstVariant.id_mausac) {
                    console.log('🎨 Auto-selecting first color:', firstVariant.mausac.ten);
                    this.selectColor(firstVariant.id_mausac, firstVariant.mausac.ten);
                } else {
                    console.log('❌ First variant has no color data');
                }
            } else {
                console.log('🎨 User has selection, keeping it');
                // Keep current selection, just update display
                this.updatePrice();
                this.updateStockInfo();
            }
        } else if (mainStock > 0) {
            console.log('✅ Main product has stock, but keeping current selection');
            // Don't reset selection if user has already selected color/size
            // Only reset if no selection was made
            if (!this.selectedColor && !this.selectedSize) {
                console.log('🔄 No user selection, staying on main product');
                this.renderSizeOptions();
                this.updatePrice();
                this.updateStockInfo();
            } else {
                console.log('🎨 User has selection, keeping it');
                // Keep current selection, just update display
                this.updatePrice();
                this.updateStockInfo();
            }
        } else {
            console.log('❌ No stock available for main product or variants');
        }
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    /**
     * Show error message
     */
    showError(message) {
        this.showNotification(message, 'error');
    }

    /**
     * Show info message
     */
    showInfo(message) {
        this.showNotification(message, 'info');
    }

    /**
     * Update cart count directly
     */
    updateCartCountDirectly() {
        try {
            const cartData = localStorage.getItem('cart_data');
            let cartCount = 0;
            
            if (cartData) {
                const cartItems = JSON.parse(cartData);
                cartCount = cartItems.reduce((sum, item) => sum + item.quantity, 0);
            }
            
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = cartCount;
                if (cartCount === 0) {
                    cartCountElement.style.display = 'none';
                } else {
                    cartCountElement.style.display = 'inline-block';
                }
            }
            
            console.log('Cart count updated directly:', cartCount);
        } catch (error) {
            console.error('Error updating cart count directly:', error);
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
        
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
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
        
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
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

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing product detail...');
    
    // Prevent duplicate initialization
    if (window.productDetailManager) {
        console.log('ProductDetailManager already initialized, skipping...');
        return;
    }
    
    // Wait for ProductDetailApiClient to be available
    if (typeof ProductDetailApiClient !== 'undefined') {
        console.log('ProductDetailManager available, initializing...');
        window.productDetailManager = new ProductDetailManager();
        window.productDetailManager.init();
        
        // Expose reset function globally for debugging
        window.resetProductStock = () => {
            if (window.productDetailManager && window.productDetailManager.resetStockData) {
                window.productDetailManager.resetStockData();
            }
        };
        
        // Expose sync function globally for debugging
        window.syncProductStockFromCart = () => {
            if (window.productDetailManager && window.productDetailManager.syncStockFromCart) {
                window.productDetailManager.syncStockFromCart();
            }
        };
    } else {
        console.error('ProductDetailApiClient not available');
    }
});
