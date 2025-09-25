/**
 * API Client chính - tổng hợp tất cả các API clients
 * Cung cấp interface thống nhất để truy cập tất cả API endpoints
 */

class APIClient {
    constructor() {
        try {
            this.home = typeof HomeAPIClient !== 'undefined' ? new HomeAPIClient() : null;
            this.product = typeof ProductAPIClient !== 'undefined' ? new ProductAPIClient() : null;
            this.cart = typeof CartAPIClient !== 'undefined' ? new CartAPIClient() : null;
            this.category = typeof CategoryAPIClient !== 'undefined' ? new CategoryAPIClient() : null;
            this.productDetail = typeof ProductDetailApiClient !== 'undefined' ? new ProductDetailApiClient() : null;
            this.voucher = typeof VoucherAPIClient !== 'undefined' ? new VoucherAPIClient() : null;
            
            // Cấu hình chung
            this.timeout = 10000;
            this.retryAttempts = 3;
            this.retryDelay = 1000;
            
            console.log('API Client initialized with:', {
                home: !!this.home,
                product: !!this.product,
                cart: !!this.cart,
                category: !!this.category,
                productDetail: !!this.productDetail,
                voucher: !!this.voucher
            });
        } catch (error) {
            console.error('Error initializing API Client:', error);
            // Set fallback values
            this.home = null;
            this.product = null;
            this.cart = null;
            this.category = null;
            this.productDetail = null;
            this.voucher = null;
        }
    }

    /**
     * Khởi tạo các API clients
     */
    init() {
        console.log('API Client initialized');
        return this;
    }

    /**
     * Kiểm tra kết nối API
     */
    async checkConnection() {
        try {
            const response = await fetch('/api/home/statistics', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            return response.ok;
        } catch (error) {
            console.error('API connection check failed:', error);
            return false;
        }
    }

    /**
     * Retry mechanism cho các request quan trọng
     */
    async retryRequest(requestFn, maxRetries = null, delay = null) {
        const retries = maxRetries || this.retryAttempts;
        const retryDelay = delay || this.retryDelay;
        
        for (let i = 0; i < retries; i++) {
            try {
                return await requestFn();
            } catch (error) {
                if (i === retries - 1) {
                    throw error;
                }
                
                console.warn(`Request failed, retrying... (${i + 1}/${retries})`);
                await this.delay(retryDelay * Math.pow(2, i)); // Exponential backoff
            }
        }
    }

    /**
     * Delay utility
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    /**
     * Xử lý lỗi API chung
     */
    handleError(error, context = '') {
        console.error(`API Error ${context}:`, error);
        
        let message = 'Có lỗi xảy ra';
        
        if (error.message.includes('timeout')) {
            message = 'Kết nối quá chậm, vui lòng thử lại';
        } else if (error.message.includes('404')) {
            message = 'Không tìm thấy dữ liệu';
        } else if (error.message.includes('500')) {
            message = 'Lỗi server, vui lòng thử lại sau';
        } else if (error.message) {
            message = error.message;
        }
        
        return {
            success: false,
            message: message,
            error: error
        };
    }

    /**
     * Hiển thị thông báo lỗi
     */
    showError(message, type = 'error') {
        // Sử dụng notification system có sẵn hoặc tạo mới
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else {
            console.error(message);
            alert(message);
        }
    }

    /**
     * Hiển thị thông báo thành công
     */
    showSuccess(message) {
        if (typeof showNotification === 'function') {
            showNotification(message, 'success');
        } else if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else {
            console.log(message);
        }
    }

    /**
     * Hiển thị loading
     */
    showLoading(element = null) {
        if (element) {
            element.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        } else {
            // Hiển thị loading toàn trang
            const loadingDiv = document.createElement('div');
            loadingDiv.id = 'api-loading';
            loadingDiv.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75';
            loadingDiv.style.zIndex = '9999';
            loadingDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            document.body.appendChild(loadingDiv);
        }
    }

    /**
     * Ẩn loading
     */
    hideLoading(element = null) {
        if (element) {
            element.innerHTML = '';
        } else {
            const loadingDiv = document.getElementById('api-loading');
            if (loadingDiv) {
                loadingDiv.remove();
            }
        }
    }

    /**
     * Cache management
     */
    setCache(key, data, ttl = 300000) { // 5 minutes default
        const cacheData = {
            data: data,
            timestamp: Date.now(),
            ttl: ttl
        };
        localStorage.setItem(`api_cache_${key}`, JSON.stringify(cacheData));
    }

    getCache(key) {
        try {
            const cached = localStorage.getItem(`api_cache_${key}`);
            if (!cached) return null;
            
            const cacheData = JSON.parse(cached);
            const now = Date.now();
            
            if (now - cacheData.timestamp > cacheData.ttl) {
                localStorage.removeItem(`api_cache_${key}`);
                return null;
            }
            
            return cacheData.data;
        } catch (error) {
            console.error('Error reading cache:', error);
            return null;
        }
    }

    clearCache(pattern = null) {
        if (pattern) {
            Object.keys(localStorage).forEach(key => {
                if (key.startsWith('api_cache_') && key.includes(pattern)) {
                    localStorage.removeItem(key);
                }
            });
        } else {
            Object.keys(localStorage).forEach(key => {
                if (key.startsWith('api_cache_')) {
                    localStorage.removeItem(key);
                }
            });
        }
    }
}

// Khởi tạo API Client global
window.API = new APIClient().init();

// Export để sử dụng trong modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = APIClient;
}
