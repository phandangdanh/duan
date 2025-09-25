/**
 * API Index - Load tất cả các API clients
 * File này sẽ được include trong layout chính để load tất cả API functionality
 */

// Load các API clients theo thứ tự dependency
const apiScripts = [
    '/fontend/js/api/home-api-client.js',
    '/fontend/js/api/product-api-client.js', 
    '/fontend/js/api/cart-api-client.js',
    '/fontend/js/api/category-api-client.js',
    '/fontend/js/api/voucher-api-client.js',
    '/fontend/js/api/api-client.js'
];

/**
 * Load API scripts dynamically
 */
function loadAPIScripts() {
    return new Promise((resolve, reject) => {
        let loadedCount = 0;
        const totalScripts = apiScripts.length;
        
        apiScripts.forEach(scriptSrc => {
            const script = document.createElement('script');
            script.src = scriptSrc;
            script.onload = () => {
                loadedCount++;
                if (loadedCount === totalScripts) {
                    resolve();
                }
            };
            script.onerror = () => {
                reject(new Error(`Failed to load script: ${scriptSrc}`));
            };
            document.head.appendChild(script);
        });
    });
}

/**
 * Initialize API system
 */
async function initializeAPI() {
    try {
        console.log('Loading API clients...');
        await loadAPIScripts();
        
        // Wait for API client to be available
        if (typeof window.API !== 'undefined') {
            console.log('API system initialized successfully');
            
            // Test API connection
            const isConnected = await window.API.checkConnection();
            if (isConnected) {
                console.log('API connection verified');
            } else {
                console.warn('API connection test failed');
            }
            
            return true;
        } else {
            throw new Error('API client not available');
        }
    } catch (error) {
        console.error('Failed to initialize API system:', error);
        return false;
    }
}

/**
 * Load API system when DOM is ready
 */
document.addEventListener('DOMContentLoaded', async function() {
    const apiInitialized = await initializeAPI();
    
    if (apiInitialized) {
        // Dispatch custom event to notify other scripts that API is ready
        document.dispatchEvent(new CustomEvent('apiReady', {
            detail: { api: window.API }
        }));
    } else {
        // Fallback: show error message
        console.error('API system failed to initialize');
        if (typeof showNotification === 'function') {
            showNotification('Không thể khởi tạo hệ thống API', 'error');
        }
    }
});

/**
 * Utility function để check API availability
 */
function isAPIReady() {
    return typeof window.API !== 'undefined';
}

/**
 * Utility function để get API client
 */
function getAPI() {
    if (!isAPIReady()) {
        throw new Error('API system not ready');
    }
    return window.API;
}

// Export utilities
window.isAPIReady = isAPIReady;
window.getAPI = getAPI;
