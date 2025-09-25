/**
 * API Client cho trang chủ
 * Xử lý tất cả các API calls liên quan đến trang chủ
 */

class HomeAPIClient {
    constructor() {
        this.baseUrl = '/api/home';
        this.timeout = 10000; // 10 seconds timeout
    }

    /**
     * Lấy tất cả dữ liệu trang chủ
     */
    async getAllData() {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/`);
            return response.data;
        } catch (error) {
            console.error('Error loading all home data:', error);
            throw error;
        }
    }

    /**
     * Lấy sản phẩm nổi bật
     */
    async getFeaturedProducts(limit = 8) {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/featured-products?limit=${limit}`);
            return response.data;
        } catch (error) {
            console.error('Error loading featured products:', error);
            throw error;
        }
    }

    /**
     * Lấy sản phẩm khuyến mãi
     */
    async getSaleProducts(limit = 6) {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/sale-products?limit=${limit}`);
            return response.data;
        } catch (error) {
            console.error('Error loading sale products:', error);
            throw error;
        }
    }

    /**
     * Lấy sản phẩm bán chạy
     */
    async getBestSellingProducts(limit = 6) {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/best-selling-products?limit=${limit}`);
            return response.data;
        } catch (error) {
            console.error('Error loading best selling products:', error);
            throw error;
        }
    }

    /**
     * Lấy danh mục nổi bật
     */
    async getFeaturedCategories(limit = 8) {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/featured-categories?limit=${limit}`);
            return response.data;
        } catch (error) {
            console.error('Error loading categories:', error);
            throw error;
        }
    }

    /**
     * Lấy thống kê
     */
    async getStatistics() {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/statistics`);
            return response.data;
        } catch (error) {
            console.error('Error loading statistics:', error);
            throw error;
        }
    }

    /**
     * Tìm kiếm sản phẩm
     */
    async searchProducts(keyword, limit = 10) {
        try {
            if (!keyword || keyword.trim().length < 2) {
                throw new Error('Từ khóa tìm kiếm phải có ít nhất 2 ký tự');
            }
            
            const response = await this.makeRequest(`${this.baseUrl}/search?q=${encodeURIComponent(keyword)}&limit=${limit}`);
            return response.data;
        } catch (error) {
            console.error('Error searching products:', error);
            throw error;
        }
    }

    /**
     * Thực hiện HTTP request với timeout và error handling
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
            
            if (!data.success) {
                throw new Error(data.message || 'API request failed');
            }

            return data;
        } catch (error) {
            clearTimeout(timeoutId);
            
            if (error.name === 'AbortError') {
                throw new Error('Request timeout');
            }
            
            throw error;
        }
    }

    /**
     * Retry mechanism cho các request quan trọng
     */
    async retryRequest(requestFn, maxRetries = 3, delay = 1000) {
        for (let i = 0; i < maxRetries; i++) {
            try {
                return await requestFn();
            } catch (error) {
                if (i === maxRetries - 1) {
                    throw error;
                }
                
                console.warn(`Request failed, retrying... (${i + 1}/${maxRetries})`);
                await this.delay(delay * Math.pow(2, i)); // Exponential backoff
            }
        }
    }

    /**
     * Delay utility
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}
