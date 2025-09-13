$(document).ready(function() {
    
    
    // Get base URL from current location
    window.BASE_URL = window.location.pathname.split('/admin/')[0];
    
    
    // Initialize product management functionality
    initProductManagement();
});

function initProductManagement() {
    
    
    // Initialize Bootstrap modals
    initModals();
    
    // Bulk selection functionality
    initBulkSelection();
    
    // Bulk actions
    initBulkActions();
    
    // Individual product actions
    initIndividualActions();
    
    // Filter functionality
    initFilterFunctionality();
    
    // Clear filter functionality
    initClearFilter();
    
    // Refresh functionality
    initRefreshFunctionality();
    
    // Soft delete management functionality
    initSoftDeleteManagement();
    
    
}

function initModals() {
    
    
    // Check if Bootstrap modal is available
    if (typeof $.fn.modal === 'undefined') {
        console.error('Bootstrap modal not found!');
        return;
    }
    
    // Initialize delete modal (only if on index page)
    if (window.location.pathname.includes('/admin/sanpham') && !window.location.pathname.includes('/show/') && !window.location.pathname.includes('/edit/') && !window.location.pathname.includes('/create/')) {
        if ($('#deleteModal').length > 0) {
            
        } else {
            console.error('Delete modal not found!');
        }
        
        // Initialize bulk delete modal
        if ($('#bulkDeleteModal').length > 0) {
            
        } else {
            console.error('Bulk delete modal not found!');
        }
    }
    
    
}

function initBulkSelection() {
    const selectAll = $('#select-all');
    const productCheckboxes = $('.product-checkbox');
    const bulkActions = $('.bulk-actions');
    const selectedCount = $('.selected-count');
    
    // Select all functionality
    selectAll.on('change', function() {
        const isChecked = $(this).is(':checked');
        productCheckboxes.prop('checked', isChecked);
        updateBulkActionsVisibility();
    });
    
    // Individual checkbox functionality
    productCheckboxes.on('change', function() {
        updateSelectAllState();
        updateBulkActionsVisibility();
    });
    
    function updateSelectAllState() {
        const totalCheckboxes = productCheckboxes.length;
        const checkedCheckboxes = productCheckboxes.filter(':checked').length;
        
        if (checkedCheckboxes === 0) {
            selectAll.prop('checked', false).prop('indeterminate', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            selectAll.prop('checked', true).prop('indeterminate', false);
        } else {
            selectAll.prop('checked', false).prop('indeterminate', true);
        }
    }
    
    function updateBulkActionsVisibility() {
        const checkedCount = productCheckboxes.filter(':checked').length;
        
        if (checkedCount > 0) {
            bulkActions.removeClass('d-none');
            selectedCount.text(checkedCount + ' sản phẩm được chọn');
        } else {
            bulkActions.addClass('d-none');
        }
    }
}

function initBulkActions() {
    // Bulk status update
    $('.bulk-status-btn').on('click', function() {
        const status = $(this).data('status');
        const selectedIds = getSelectedProductIds();
        
        if (selectedIds.length === 0) {
            showAlert('Vui lòng chọn ít nhất một sản phẩm!', 'warning');
            return;
        }
        
        const statusText = status == 1 ? 'kinh doanh' : 'ngừng kinh doanh';
        const confirmMessage = `Bạn có chắc chắn muốn cập nhật trạng thái ${statusText} cho ${selectedIds.length} sản phẩm đã chọn?`;
        if (typeof showConfirm === 'function') {
            showConfirm(confirmMessage).then(function(agree){ if(agree){ updateBulkStatus(selectedIds, status); } });
        } else {
            if (confirm(confirmMessage)) { updateBulkStatus(selectedIds, status); }
        }
    });
    
    // Bulk delete
    $('.bulk-delete-btn').on('click', function() {
        const selectedIds = getSelectedProductIds();
        
        if (selectedIds.length === 0) {
            showAlert('Vui lòng chọn ít nhất một sản phẩm!', 'warning');
            return;
        }
        
        $('#bulk-delete-count').text(selectedIds.length);
        $('#bulkDeleteModal').modal('show');
    });
    
    // Confirm bulk delete
    $('#confirm-bulk-delete').on('click', function() {
        const selectedIds = getSelectedProductIds();
        performBulkDelete(selectedIds);
        $('#bulkDeleteModal').modal('hide');
    });
}

function initIndividualActions() {
    // Toggle status for individual product
    $('.toggle-status-btn').on('click', function() {
        const productId = $(this).data('id');
        const currentStatus = $(this).data('status');
        const newStatus = currentStatus == 1 ? 0 : 1;
        
        toggleProductStatus(productId, newStatus);
    });
    
    // Delete individual product
    $('.delete-btn').on('click', function() {
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        
        $('#delete-product-name').text(productName);
        const directUrl = $(this).data('url');
        const actionUrl = (typeof SANPHAM_ENDPOINTS !== 'undefined' && directUrl) ? directUrl : (window.BASE_URL + `/admin/sanpham/destroy/${productId}`);
        $('#delete-form').attr('action', actionUrl);
        $('#deleteModal').modal('show');
    });

    // Handle delete form submission
    $('#delete-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const action = form.attr('action');
        const formData = new FormData(form[0]);
        
        // Show loading state
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Đang xóa...').prop('disabled', true);
        
        $.ajax({
            url: action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                showAlert('Xóa sản phẩm thành công!', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                let message = 'Có lỗi xảy ra khi xóa sản phẩm!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    message = 'Không tìm thấy sản phẩm hoặc route không tồn tại!';
                } else if (xhr.status === 500) {
                    message = 'Lỗi server khi xóa sản phẩm!';
                }
                showAlert(message, 'error');
            },
            complete: function() {
                // Reset button state
                submitBtn.html(originalText).prop('disabled', false);
                $('#deleteModal').modal('hide');
            }
        });
    });
}

function initFilterFunctionality() {
    // Clear filter
    $('#clear-filter').on('click', function() {
        $('#filter-form')[0].reset();
        $('#filter-form').submit();
    });
    
    // Auto-submit on select change
    $('#category, #status, #stock, #perpage').on('change', function() {
        $('#filter-form').submit();
    });
}

function initRefreshFunctionality() {
    $('#refresh-btn').on('click', function() {
        location.reload();
    });
}

// Helper functions
function getSelectedProductIds() {
    return $('.product-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
}

function updateBulkStatus(ids, status) {
    $.ajax({
        url: (typeof SANPHAM_ENDPOINTS !== 'undefined' ? SANPHAM_ENDPOINTS.bulkStatus : window.BASE_URL + '/admin/sanpham/bulk-status'),
        type: 'POST',
        data: {
            ids: ids,
            status: status,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert(response.message, 'error');
            }
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Có lỗi xảy ra khi cập nhật trạng thái!';
            showAlert(message, 'error');
        }
    });
}

function performBulkDelete(ids) {
    $.ajax({
        url: (typeof SANPHAM_ENDPOINTS !== 'undefined' ? SANPHAM_ENDPOINTS.bulkDelete : window.BASE_URL + '/admin/sanpham/bulk-delete'),
        type: 'POST',
        data: {
            ids: ids,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert(response.message, 'error');
            }
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Có lỗi xảy ra khi xóa sản phẩm!';
            showAlert(message, 'error');
        }
    });
}

function toggleProductStatus(productId, newStatus) {
    $.ajax({
        url: (typeof SANPHAM_ENDPOINTS !== 'undefined' ? SANPHAM_ENDPOINTS.toggleStatus.replace(':id', productId) : window.BASE_URL + `/ajax/sanpham/toggle-status/${productId}`),
        type: 'POST',
        data: {
            status: newStatus,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const statusText = newStatus == 1 ? 'kinh doanh' : 'ngừng kinh doanh';
                showAlert(`Đã cập nhật trạng thái ${statusText} thành công!`, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showAlert(response.message || 'Có lỗi xảy ra!', 'error');
            }
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Có lỗi xảy ra khi cập nhật trạng thái!';
            showAlert(message, 'error');
        }
    });
}

function showAlert(message, type) {
    // Use toastr if available
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
        // Fallback to alert
        alert(message);
    }
}

// Unified pretty confirm using SweetAlert2 if available, else fallback to confirm()
function showSweetConfirm(opts){
    return new Promise(function(resolve){
        if (typeof Swal !== 'undefined' && Swal.fire) {
            Swal.fire({
                title: opts.title || 'Xác nhận',
                html: (opts.text || '').replace(/\n/g,'<br/>'),
                icon: opts.icon || 'question',
                showCancelButton: true,
                confirmButtonText: opts.confirmText || 'Đồng ý',
                cancelButtonText: opts.cancelText || 'Hủy',
                confirmButtonColor: opts.confirmColor || '#28a745',
                cancelButtonColor: '#6c757d'
            }).then(function(result){ resolve(!!result.isConfirmed); });
        } else {
            resolve(window.confirm(opts.text || 'Bạn có chắc chắn?'));
        }
    });
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

function formatNumber(number) {
    return new Intl.NumberFormat('vi-VN').format(number);
}

// Clear all filter fields function
function clearAllFilters() {
    
    
    // Clear search input
    $('#filter-form input[name="search"]').val('');
    
    // Reset all select dropdowns to first option (default)
    $('#filter-form select[name="category"]').prop('selectedIndex', 0);
    $('#filter-form select[name="status"]').prop('selectedIndex', 0);
    $('#filter-form select[name="stock"]').prop('selectedIndex', 0);
    $('#filter-form select[name="perpage"]').prop('selectedIndex', 0);
    
    // Submit form to reload with cleared filters
    $('#filter-form').submit();
}

// Soft delete management functionality
function initSoftDeleteManagement() {
    
    
    // Show individual product details
    $(document).on('click', '.show-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        // Show product detail modal
        showProductDetailModal(id, name);
    });
    
    // Restore individual product
    $(document).on('click', '.restore-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        // Show restore confirmation modal
        $('#restore-product-name').text(name);
        $('#restoreModal').modal('show');
        
        // Store product ID for confirmation
        $('#confirm-restore').data('id', id);
    });
    
    // Force delete individual product
    $(document).on('click', '.force-delete-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        // Show force delete confirmation modal
        $('#force-delete-product-name').text(name);
        $('#forceDeleteModal').modal('show');
        
        // Store product ID for confirmation
        $('#confirm-force-delete').data('id', id);
    });
    
    // Confirm restore
    $(document).on('click', '#confirm-restore', function(e) {
        e.preventDefault();
        const isBulk = $(this).data('bulk') === 'all';
        const id = $(this).data('id');
        $('#restoreModal').modal('hide');
        if (isBulk) {
            $(this).removeData('bulk');
            restoreAllProducts();
        } else {
        restoreProduct(id);
        }
    });
    
    // Confirm force delete
    $(document).on('click', '#confirm-force-delete', function(e) {
        e.preventDefault();
        const isBulk = $(this).data('bulk') === 'all';
        const id = $(this).data('id');
        $('#forceDeleteModal').modal('hide');
        if (isBulk) {
            $(this).removeData('bulk');
            forceDeleteAllProducts();
        } else {
        forceDeleteProduct(id);
        }
    });
    
    // Restore all products using existing modal
    $(document).on('click', '#restore-all-btn', function(e) {
        e.preventDefault();
        if ($('#restoreModal').length) {
            $('#restore-product-name').text('tất cả sản phẩm đã xóa');
            $('#confirm-restore').data('bulk', 'all');
            $('#restoreModal').modal('show');
        } else {
            showSweetConfirm({
                title: 'Phục hồi tất cả',
                text: 'Bạn có chắc chắn muốn phục hồi TẤT CẢ sản phẩm đã xóa?',
                confirmText: 'Phục hồi',
                confirmColor: '#f0ad4e'
            }).then(function(agree){ if(agree){ restoreAllProducts(); } });
        }
    });
    
    // Force delete all products using existing modal
    $(document).on('click', '#force-delete-all-btn', function(e) {
        e.preventDefault();
        if ($('#forceDeleteModal').length) {
            $('#force-delete-product-name').text('tất cả sản phẩm đã xóa');
            $('#confirm-force-delete').data('bulk', 'all');
            $('#forceDeleteModal').modal('show');
        } else {
            showSweetConfirm({
                title: 'Xóa vĩnh viễn tất cả',
                text: 'Bạn có chắc chắn muốn xóa vĩnh viễn TẤT CẢ sản phẩm đã xóa?\nHành động này KHÔNG THỂ hoàn tác!',
                confirmText: 'Xóa vĩnh viễn',
                confirmColor: '#d9534f'
            }).then(function(agree){ if(agree){ forceDeleteAllProducts(); } });
        }
    });
}

// Restore individual product
function restoreProduct(id) {
    $.ajax({
        url: window.BASE_URL + '/admin/sanpham/restore/' + id,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showAlert('Phục hồi sản phẩm thành công!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Có lỗi xảy ra khi phục hồi sản phẩm!';
            showAlert(message, 'error');
        }
    });
}

// Force delete individual product
function forceDeleteProduct(id) {
    $.ajax({
        url: (function(){
            // Try to read url from data attribute if provided (POST fallback)
            var btn = document.querySelector('.force-delete-btn[data-id="'+id+'"]');
            var urlPost = btn && btn.getAttribute('data-url-post');
            return urlPost || (window.BASE_URL + '/admin/sanpham/force-delete/' + id);
        })(),
        type: (function(){
            var btn = document.querySelector('.force-delete-btn[data-id="'+id+'"]');
            return btn && btn.getAttribute('data-url-post') ? 'POST' : 'DELETE';
        })(),
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showAlert('Xóa vĩnh viễn sản phẩm thành công!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Có lỗi xảy ra khi xóa vĩnh viễn sản phẩm!';
            showAlert(message, 'error');
        }
    });
}

// Restore all products
function restoreAllProducts() {
    $.ajax({
        url: window.BASE_URL + '/admin/sanpham/restore-all',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showAlert('Phục hồi tất cả sản phẩm thành công!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Có lỗi xảy ra khi phục hồi tất cả sản phẩm!';
            showAlert(message, 'error');
        }
    });
}

// Force delete all products
function forceDeleteAllProducts() {
    $.ajax({
        url: window.BASE_URL + '/admin/sanpham/force-delete-all',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showAlert('Xóa vĩnh viễn tất cả sản phẩm thành công!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Có lỗi xảy ra khi xóa vĩnh viễn tất cả sản phẩm!';
            showAlert(message, 'error');
        }
    });
}

// Clear filter functionality
function initClearFilter() {
    
    
    // Clear filter button
    $(document).on('click', '#clear-filter', function(e) {
        e.preventDefault();
        
        
        // Clear all form fields
        clearAllFilters();
    });
}

// Refresh functionality
function initRefreshFunctionality() {
    
    
    // Refresh button by ID
    $(document).on('click', '#refresh-btn', function(e) {
        e.preventDefault();
        
        
        // Clear all form fields
        clearAllFilters();
    });
    
    // Refresh button by data-action
    $(document).on('click', '[data-action="refresh"]', function(e) {
        e.preventDefault();
        
        
        // Clear all form fields
        clearAllFilters();
    });
    
    // Also handle refresh button by class
    $(document).on('click', '.btn-refresh', function(e) {
        e.preventDefault();
        
        
        // Clear all form fields
        clearAllFilters();
    });
}

// Show product detail modal
function showProductDetailModal(id, name) {
    // Get product data from the table row
    const row = $(`.show-btn[data-id="${id}"]`).closest('tr');
    
    // Extract data from the row
    const productId = row.find('td:eq(0)').text().trim();
    const productCode = row.find('td:eq(1)').text().trim();
    const productName = row.find('td:eq(2)').find('strong').text().trim();
    const productCategory = row.find('td:eq(3)').find('.badge').text().trim();
    const productStatus = row.find('td:eq(4)').find('.badge').text().trim();
    const productDeletedAt = row.find('td:eq(5)').text().trim();
    
    // Get product image
    const productImage = row.find('td:eq(2)').find('img').attr('src');
    
    // Get product description (if available)
    const productDescription = row.find('td:eq(2)').find('small').text().trim() || 'Không có mô tả';
    
    // Populate modal with data
    $('#modal-product-id').text(productId);
    $('#modal-product-code').text(productCode);
    $('#modal-product-name').text(productName);
    $('#modal-product-category').text(productCategory);
    $('#modal-product-status').text(productStatus);
    $('#modal-product-description').text(productDescription);
    $('#modal-product-deleted-at').text(productDeletedAt);
    
    // Set product image
    if (productImage && !productImage.includes('no-image.png')) {
        // Use the same logic as the table - direct asset() call
        let imageUrl = productImage;
        if (productImage.startsWith('/')) {
            imageUrl = window.location.origin + productImage;
        } else if (!productImage.startsWith('http')) {
            imageUrl = window.location.origin + '/' + productImage;
        }
        $('#modal-product-image').attr('src', imageUrl).show();
    } else {
        $('#modal-product-image').attr('src', window.location.origin + '/backend/images/no-image.png').show();
    }
    
    // Show modal
    $('#productDetailModal').modal('show');
}
