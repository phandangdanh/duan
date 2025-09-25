/**
 * API Client cho sản phẩm
 * Xử lý tất cả các API calls liên quan đến sản phẩm
 */

class ProductAPIClient {
    constructor() {
        this.baseUrl = '/api/products';
        this.timeout = 10000;
    }

    /**
     * Lấy danh sách sản phẩm với phân trang và bộ lọc
     */
    async getProducts(filters = {}) {
        try {
            const queryParams = new URLSearchParams();
            
            // Thêm các filter vào query params
            Object.keys(filters).forEach(key => {
                if (filters[key] !== null && filters[key] !== undefined && filters[key] !== '') {
                    queryParams.append(key, filters[key]);
                }
            });

            const url = `${this.baseUrl}?${queryParams.toString()}`;
            const response = await this.makeRequest(url);
            return response;
        } catch (error) {
            console.error('Error loading products:', error);
            throw error;
        }
    }

    /**
     * Lấy chi tiết sản phẩm theo ID
     */
    async getProductById(id) {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/${id}`);
            return response.data;
        } catch (error) {
            console.error('Error loading product details:', error);
            throw error;
        }
    }

    /**
     * Lấy thông tin stock của sản phẩm
     */
    async getProductStock(id) {
        try {
            const response = await this.makeRequest(`/api/product/${id}/stock`);
            return response;
        } catch (error) {
            console.error('Error loading product stock:', error);
            throw error;
        }
    }

    /**
     * Lấy stock chi tiết cho trang sản phẩm
     */
    async getDetailedStock(id) {
        try {
            const response = await this.makeRequest(`/api/product/${id}/detailed-stock`);
            return response;
        } catch (error) {
            console.error('Error loading detailed stock:', error);
            throw error;
        }
    }

    /**
     * Lấy stock hiển thị cho cart
     */
    async getDisplayStock(id) {
        try {
            const response = await this.makeRequest(`/api/cart/display-stock/${id}`);
            return response;
        } catch (error) {
            console.error('Error loading display stock:', error);
            throw error;
        }
    }

    /**
     * Tìm kiếm sản phẩm
     */
    async searchProducts(keyword, options = {}) {
        try {
            const filters = {
                search: keyword,
                ...options
            };
            return await this.getProducts(filters);
        } catch (error) {
            console.error('Error searching products:', error);
            throw error;
        }
    }

    /**
     * Lấy sản phẩm theo danh mục
     */
    async getProductsByCategory(categoryId, options = {}) {
        try {
            const filters = {
                category: categoryId,
                ...options
            };
            return await this.getProducts(filters);
        } catch (error) {
            console.error('Error loading products by category:', error);
            throw error;
        }
    }

    /**
     * Lấy sản phẩm khuyến mãi
     */
    async getSaleProducts(options = {}) {
        try {
            const filters = {
                on_sale: true,
                ...options
            };
            return await this.getProducts(filters);
        } catch (error) {
            console.error('Error loading sale products:', error);
            throw error;
        }
    }

    /**
     * Lấy sản phẩm mới nhất
     */
    async getLatestProducts(limit = 10) {
        try {
            const filters = {
                sort: 'created_at_desc',
                per_page: limit
            };
            return await this.getProducts(filters);
        } catch (error) {
            console.error('Error loading latest products:', error);
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
