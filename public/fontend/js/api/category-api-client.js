/**
 * API Client cho danh mục
 * Xử lý tất cả các API calls liên quan đến danh mục
 */

class CategoryAPIClient {
    constructor() {
        this.baseUrl = '/api/categories';
        this.timeout = 10000;
    }

    /**
     * Lấy danh sách tất cả danh mục
     */
    async getAllCategories() {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/`);
            return response.data;
        } catch (error) {
            console.error('Error loading categories:', error);
            throw error;
        }
    }

    /**
     * Lấy chi tiết danh mục theo ID
     */
    async getCategoryById(id) {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/${id}`);
            return response.data;
        } catch (error) {
            console.error('Error loading category details:', error);
            throw error;
        }
    }

    /**
     * Lấy danh mục gốc (parent_id = 0)
     */
    async getRootCategories() {
        try {
            const allCategories = await this.getAllCategories();
            return allCategories.filter(category => category.parent_id === 0);
        } catch (error) {
            console.error('Error loading root categories:', error);
            throw error;
        }
    }

    /**
     * Lấy danh mục con theo parent_id
     */
    async getChildCategories(parentId) {
        try {
            const allCategories = await this.getAllCategories();
            return allCategories.filter(category => category.parent_id === parentId);
        } catch (error) {
            console.error('Error loading child categories:', error);
            throw error;
        }
    }

    /**
     * Lấy danh mục active
     */
    async getActiveCategories() {
        try {
            const allCategories = await this.getAllCategories();
            return allCategories.filter(category => category.status === 'active');
        } catch (error) {
            console.error('Error loading active categories:', error);
            throw error;
        }
    }

    /**
     * Tìm kiếm danh mục theo tên
     */
    async searchCategories(keyword) {
        try {
            const allCategories = await this.getAllCategories();
            return allCategories.filter(category => 
                category.name.toLowerCase().includes(keyword.toLowerCase()) ||
                (category.description && category.description.toLowerCase().includes(keyword.toLowerCase()))
            );
        } catch (error) {
            console.error('Error searching categories:', error);
            throw error;
        }
    }

    /**
     * Lấy cây danh mục (hierarchical structure)
     */
    async getCategoryTree() {
        try {
            const allCategories = await this.getAllCategories();
            return this.buildCategoryTree(allCategories);
        } catch (error) {
            console.error('Error building category tree:', error);
            throw error;
        }
    }

    /**
     * Xây dựng cây danh mục từ danh sách phẳng
     */
    buildCategoryTree(categories, parentId = 0) {
        const tree = [];
        
        categories.forEach(category => {
            if (category.parent_id === parentId) {
                const children = this.buildCategoryTree(categories, category.id);
                if (children.length > 0) {
                    category.children = children;
                }
                tree.push(category);
            }
        });
        
        return tree;
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
