/**
 * Product Detail API Client
 * Handles API calls for product detail page
 */
class ProductDetailApiClient {
    constructor() {
        this.baseUrl = '/api/products';
    }

    /**
     * Get product detail by ID
     */
    async getProductDetail(productId) {
        try {
            console.log('Fetching product detail for ID:', productId);
            const response = await fetch(`${this.baseUrl}/${productId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            console.log('API response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('API response data:', data);
            return data;
        } catch (error) {
            console.error('Error fetching product detail:', error);
            throw error;
        }
    }

    /**
     * Get related products
     */
    async getRelatedProducts(categoryId, excludeId, limit = 4) {
        try {
            const response = await fetch(`${this.baseUrl}?category=${categoryId}&per_page=${limit}&exclude=${excludeId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching related products:', error);
            throw error;
        }
    }

    /**
     * Get product stock info
     */
    async getProductStock(productId) {
        try {
            const response = await fetch(`/api/product/${productId}/stock`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching product stock:', error);
            throw error;
        }
    }
}

// Export for use in other files
window.ProductDetailApiClient = ProductDetailApiClient;
