/**
 * JavaScript để load dữ liệu trang chủ từ API
 * Sử dụng API Client structure để tổ chức code tốt hơn
 */

class HomePageManager {
    constructor() {
        this.api = window.API; // Sử dụng API Client chính
        this.loadingStates = {
            featuredProducts: false,
            saleProducts: false,
            bestSellingProducts: false,
            categories: false,
            statistics: false
        };
    }

    /**
     * Load tất cả dữ liệu trang chủ
     */
    async loadAllData() {
        try {
            this.api.showLoading();
            
            const data = await this.api.home.getAllData();
            this.renderHomePage(data);
            
        } catch (error) {
            const errorResult = this.api.handleError(error, 'loading home page data');
            this.api.showError(errorResult.message);
        } finally {
            this.api.hideLoading();
        }
    }

    /**
     * Load sản phẩm nổi bật
     */
    async loadFeaturedProducts(limit = 8) {
        if (this.loadingStates.featuredProducts) return;
        
        try {
            this.loadingStates.featuredProducts = true;
            this.showSectionLoading('featured-products');
            
            const products = await this.api.home.getFeaturedProducts(limit);
            this.renderFeaturedProducts(products);
            
        } catch (error) {
            const errorResult = this.api.handleError(error, 'loading featured products');
            this.showSectionError('featured-products', errorResult.message);
        } finally {
            this.loadingStates.featuredProducts = false;
            this.hideSectionLoading('featured-products');
        }
    }

    /**
     * Load sản phẩm khuyến mãi
     */
    async loadSaleProducts(limit = 6) {
        if (this.loadingStates.saleProducts) return;
        
        try {
            this.loadingStates.saleProducts = true;
            this.showSectionLoading('sale-products');
            
            const products = await this.api.home.getSaleProducts(limit);
            this.renderSaleProducts(products);
            
        } catch (error) {
            const errorResult = this.api.handleError(error, 'loading sale products');
            this.showSectionError('sale-products', errorResult.message);
        } finally {
            this.loadingStates.saleProducts = false;
            this.hideSectionLoading('sale-products');
        }
    }

    /**
     * Load sản phẩm bán chạy
     */
    async loadBestSellingProducts(limit = 6) {
        if (this.loadingStates.bestSellingProducts) return;
        
        try {
            this.loadingStates.bestSellingProducts = true;
            this.showSectionLoading('best-selling-products');
            
            const products = await this.api.home.getBestSellingProducts(limit);
            this.renderBestSellingProducts(products);
            
        } catch (error) {
            const errorResult = this.api.handleError(error, 'loading best selling products');
            this.showSectionError('best-selling-products', errorResult.message);
        } finally {
            this.loadingStates.bestSellingProducts = false;
            this.hideSectionLoading('best-selling-products');
        }
    }

    /**
     * Load danh mục nổi bật
     */
    async loadFeaturedCategories(limit = 8) {
        if (this.loadingStates.categories) return;
        
        try {
            this.loadingStates.categories = true;
            this.showSectionLoading('categories');
            
            const categories = await this.api.home.getFeaturedCategories(limit);
            this.renderFeaturedCategories(categories);
            
        } catch (error) {
            const errorResult = this.api.handleError(error, 'loading categories');
            this.showSectionError('categories', errorResult.message);
        } finally {
            this.loadingStates.categories = false;
            this.hideSectionLoading('categories');
        }
    }

    /**
     * Load thống kê
     */
    async loadStatistics() {
        if (this.loadingStates.statistics) return;
        
        try {
            this.loadingStates.statistics = true;
            
            const stats = await this.api.home.getStatistics();
            this.renderStatistics(stats);
            
        } catch (error) {
            const errorResult = this.api.handleError(error, 'loading statistics');
            console.error('Error loading statistics:', errorResult.message);
        } finally {
            this.loadingStates.statistics = false;
        }
    }

    /**
     * Tìm kiếm sản phẩm
     */
    async searchProducts(keyword, limit = 10) {
        try {
            const results = await this.api.home.searchProducts(keyword, limit);
            return results;
        } catch (error) {
            const errorResult = this.api.handleError(error, 'searching products');
            throw new Error(errorResult.message);
        }
    }

    /**
     * Render toàn bộ trang chủ
     */
    renderHomePage(data) {
        if (data.featured_categories) {
            this.renderFeaturedCategories(data.featured_categories);
        }
        if (data.featured_products) {
            this.renderFeaturedProducts(data.featured_products);
        }
        if (data.sale_products && data.sale_products.length > 0) {
            this.renderSaleProducts(data.sale_products);
            document.getElementById('sale-products-section').style.display = 'block';
        }
        if (data.best_selling_products && data.best_selling_products.length > 0) {
            this.renderBestSellingProducts(data.best_selling_products);
            document.getElementById('best-selling-section').style.display = 'block';
        }
        if (data.statistics) {
            this.renderStatistics(data.statistics);
        }
    }

    /**
     * Render sản phẩm nổi bật
     */
    renderFeaturedProducts(products) {
        const container = document.querySelector('.san-pham-noi-bat .row');
        if (!container) return;

        if (products.length === 0) {
            container.innerHTML = '<div class="col-12 text-center"><p class="text-muted">Chưa có sản phẩm nào</p></div>';
            return;
        }

        container.innerHTML = products.map(product => this.createProductCard(product, 'featured')).join('');
    }

    /**
     * Render sản phẩm khuyến mãi
     */
    renderSaleProducts(products) {
        const container = document.querySelector('.san-pham-khuyen-mai .row');
        if (!container) return;

        if (products.length === 0) {
            container.innerHTML = '<div class="col-12 text-center"><p class="text-muted">Chưa có sản phẩm khuyến mãi nào</p></div>';
            return;
        }

        container.innerHTML = products.map(product => this.createProductCard(product, 'sale')).join('');
    }

    /**
     * Render sản phẩm bán chạy
     */
    renderBestSellingProducts(products) {
        const container = document.querySelector('.san-pham-ban-chay .row');
        if (!container) return;

        if (products.length === 0) {
            container.innerHTML = '<div class="col-12 text-center"><p class="text-muted">Chưa có sản phẩm bán chạy nào</p></div>';
            return;
        }

        container.innerHTML = products.map(product => this.createProductCard(product, 'bestselling')).join('');
    }

    /**
     * Render danh mục nổi bật
     */
    renderFeaturedCategories(categories) {
        const container = document.querySelector('.danh-muc-noi-bat .row');
        if (!container) return;

        if (categories.length === 0) {
            container.innerHTML = '<div class="col-12 text-center"><p class="text-muted">Chưa có danh mục nào</p></div>';
            return;
        }

        container.innerHTML = categories.map(category => `
            <div class="col-6 col-md-3">
                <a href="/danh-muc/${category.slug}" class="text-decoration-none">
                    <div class="border rounded-3 text-center p-4 shadow-sm bg-white h-100 hover-lift">
                        <i class="bi bi-grid-3x3-gap fs-1 text-primary mb-3"></i>
                        <h6 class="mb-0 text-dark">${category.name}</h6>
                        <small class="text-muted">${category.product_count} sản phẩm</small>
                    </div>
                </a>
            </div>
        `).join('');
    }

    /**
     * Render thống kê
     */
    renderStatistics(stats) {
        // Có thể hiển thị thống kê ở footer hoặc một section riêng
        console.log('Statistics loaded:', stats);
    }

    /**
     * Tạo card sản phẩm
     */
    createProductCard(product, type = 'featured') {
        const colClass = type === 'featured' ? 'col-12 col-sm-6 col-md-4 col-lg-3' : 'col-12 col-sm-6 col-md-4 col-lg-2';
        const badgeClass = type === 'sale' ? 'bg-danger' : type === 'bestselling' ? 'bg-success' : 'bg-primary';
        const badgeText = type === 'sale' ? 'Sale' : type === 'bestselling' ? 'Hot' : 'New';
        const buttonClass = type === 'sale' ? 'btn-danger' : type === 'bestselling' ? 'btn-success' : 'btn-primary';
        
        const imageUrl = product.main_image?.url || '/fontend/img/aosomi1.png';
        const priceHtml = this.createPriceHtml(product, type);
        const categoryBadge = product.danhmuc ? `<span class="badge bg-secondary small">${product.danhmuc.name}</span>` : '';

        return `
            <div class="${colClass}">
                <div class="card border-0 shadow-lg h-100 position-relative hover-lift">
                    <img src="${imageUrl}" 
                         class="card-img-top" 
                         style="height: ${type === 'featured' ? '200px' : '150px'}; object-fit: cover;" 
                         alt="${product.tenSP}"
                         onerror="this.src='/fontend/img/aosomi1.png'">
                    
                    ${product.is_on_sale ? `<span class="badge ${badgeClass} position-absolute top-0 start-0 m-2">${badgeText}</span>` : ''}
                    
                    <div class="card-body text-center ${type !== 'featured' ? 'p-2' : ''}">
                        ${categoryBadge ? `<div class="mb-2">${categoryBadge}</div>` : ''}
                        
                        <h6 class="fw-semibold text-dark ${type !== 'featured' ? '' : 'mb-2'}">${this.truncateText(product.tenSP, type === 'featured' ? 40 : 25)}</h6>
                        
                        <div class="${type !== 'featured' ? 'mb-2' : 'mb-3'}">
                            ${priceHtml}
                        </div>
                        
                        <div class="d-flex ${type !== 'featured' ? 'gap-2' : 'justify-content-center gap-2'}">
                            <a href="/sanpham/${product.id}" class="btn btn-outline-${type === 'sale' ? 'danger' : type === 'bestselling' ? 'success' : 'primary'} btn-sm ${type !== 'featured' ? 'flex-fill' : 'rounded-pill px-3'}">Xem chi tiết</a>
                            <button class="btn ${buttonClass} btn-sm ${type !== 'featured' ? '' : 'rounded-pill px-3'}" onclick="buyNowFromHomepage(${product.id})">Mua ngay</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Tạo HTML hiển thị giá
     */
    createPriceHtml(product, type = 'featured') {
        if (product.is_on_sale && product.best_sale_price > 0) {
            return `
                <div class="d-flex flex-column align-items-center">
                    <span class="text-decoration-line-through text-muted small">${this.formatPrice(product.best_price)} VNĐ</span>
                    <span class="fw-bold text-danger ${type === 'featured' ? 'fs-6' : 'small'}">${this.formatPrice(product.best_sale_price)} VNĐ</span>
                </div>
            `;
        } else if (product.best_price > 0) {
            return `<span class="fw-bold text-primary ${type === 'featured' ? 'fs-6' : 'small'}">${this.formatPrice(product.best_price)} VNĐ</span>`;
        } else {
            return `<span class="fw-bold text-muted ${type === 'featured' ? 'fs-6' : 'small'}">Liên hệ</span>`;
        }
    }

    /**
     * Format giá tiền
     */
    formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price);
    }

    /**
     * Cắt ngắn text
     */
    truncateText(text, length) {
        return text.length > length ? text.substring(0, length) + '...' : text;
    }

    /**
     * Hiển thị loading
     */
    showLoading() {
        const loadingElement = document.getElementById('home-loading');
        if (loadingElement) {
            loadingElement.style.display = 'block';
        }
    }

    /**
     * Ẩn loading
     */
    hideLoading() {
        const loadingElement = document.getElementById('home-loading');
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
    }

    /**
     * Hiển thị loading cho section
     */
    showSectionLoading(sectionName) {
        const section = document.querySelector(`.${sectionName.replace('_', '-')}`);
        if (section) {
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'text-center py-5';
            loadingDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            loadingDiv.id = `${sectionName}-loading`;
            section.appendChild(loadingDiv);
        }
    }

    /**
     * Ẩn loading cho section
     */
    hideSectionLoading(sectionName) {
        const loadingElement = document.getElementById(`${sectionName}-loading`);
        if (loadingElement) {
            loadingElement.remove();
        }
    }

    /**
     * Hiển thị lỗi
     */
    showError(message) {
        console.error(message);
        // Có thể hiển thị toast notification hoặc alert
        if (typeof showNotification === 'function') {
            showNotification(message, 'error');
        } else {
            alert(message);
        }
    }

    /**
     * Hiển thị lỗi cho section
     */
    showSectionError(sectionName, message) {
        const section = document.querySelector(`.${sectionName.replace('_', '-')}`);
        if (section) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'col-12 text-center py-5';
            errorDiv.innerHTML = `<p class="text-danger">${message}</p>`;
            section.appendChild(errorDiv);
        }
    }
}

// Khởi tạo và load dữ liệu khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const homeManager = new HomePageManager();
    
    // Load tất cả dữ liệu trang chủ
    homeManager.loadAllData();
    
    // Export để có thể sử dụng từ bên ngoài
    window.HomePageManager = homeManager;
});
