$(document).ready(function() {
    // Khởi tạo các sự kiện
    initSelectAll();
    initUpdateTrangThai();
    initDelete();
    initBulkActions();
    initRefresh();
    initExport();
    
    // Khởi tạo select all
    function initSelectAll() {
        // Fix checkbox functionality - Select All
        $('#select-all').on('change', function() {
            const isChecked = $(this).is(':checked');
            console.log('Select all changed:', isChecked); // Debug log
            
            // Update all individual checkboxes
            $('.select-item').each(function() {
                $(this).prop('checked', isChecked);
                if (isChecked) {
                    $(this).addClass('checked');
                } else {
                    $(this).removeClass('checked');
                }
            });
            
            updateBulkButtons();
        });
        
        // Individual checkbox change
        $('.select-item').on('change', function() {
            const $checkbox = $(this);
            const isChecked = $checkbox.is(':checked');
            
            console.log('Individual checkbox changed:', $checkbox.val(), 'checked:', isChecked); // Debug log
            
            // Force visual update
            if (isChecked) {
                $checkbox.addClass('checked');
            } else {
                $checkbox.removeClass('checked');
            }
            
            updateBulkButtons();
            
            // Update select-all state
            const totalItems = $('.select-item').length;
            const checkedItems = $('.select-item:checked').length;
            
            console.log('Total items:', totalItems, 'Checked items:', checkedItems); // Debug log
            
            if (checkedItems === 0) {
                $('#select-all').prop('checked', false).prop('indeterminate', false);
            } else if (checkedItems === totalItems) {
                $('#select-all').prop('checked', true).prop('indeterminate', false);
            } else {
                $('#select-all').prop('checked', false).prop('indeterminate', true);
            }
        });
        
        // Remove the conflicting click handler - only use change event
        // This was causing the double-toggle issue
    }
    
    // Cập nhật trạng thái đơn hàng
    function initUpdateTrangThai() {
        $('.update-trangthai-btn').off('click').on('click', function() {
            const donhangId = $(this).data('id');
            const currentTrangThai = $(this).data('trangthai');
            
            $('#donhang_id').val(donhangId);
            $('#modal_trangthai').val(currentTrangThai);
            $('#updateTrangThaiModal').modal('show');
        });
        
        $('#confirmUpdateTrangThai').off('click').on('click', function() {
            const formData = {
                id: $('#donhang_id').val(),
                trangthai: $('#modal_trangthai').val(),
                nhanvien: $('#nhanvien').val()
            };
            
            if (!formData.trangthai) {
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Vui lòng chọn trạng thái mới',
                    icon: 'error'
                });
                return;
            }
            
            updateTrangThai(formData);
        });
    }
    
    // Xóa đơn hàng
    function initDelete() {
        $('.delete-btn').on('click', function() {
            const $btn = $(this);
            const donhangId = $btn.data('id');
            
            // Kiểm tra nếu nút bị disabled
            if ($btn.prop('disabled')) {
                Swal.fire({
                    title: 'Không thể xóa',
                    text: 'Chỉ có thể xóa đơn hàng đã hủy',
                    icon: 'warning'
                });
                return;
            }
            
            Swal.fire({
                title: 'Xác nhận xóa',
                text: 'Bạn có chắc chắn muốn xóa đơn hàng này không?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteDonHang(donhangId);
                }
            });
        });
    }
    
    // Các thao tác hàng loạt
    function initBulkActions() {
        $('#bulk-update-trangthai-btn').on('click', function() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Vui lòng chọn ít nhất một đơn hàng',
                    icon: 'error'
                });
                return;
            }
            
            $('#selected-count').text(selectedIds.length);
            $('#bulkUpdateTrangThaiModal').modal('show');
        });
        
        $('#confirmBulkUpdateTrangThai').on('click', function() {
            const selectedIds = getSelectedIds();
            const formData = {
                trangthai: $('#bulk_trangthai').val(),
                nhanvien: $('#bulk_nhanvien').val(),
                ids: selectedIds
            };
            
            if (!formData.trangthai) {
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Vui lòng chọn trạng thái mới',
                    icon: 'error'
                });
                return;
            }
            
            bulkUpdateTrangThai(formData);
        });
        
        $('#bulk-delete-btn').on('click', function() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Vui lòng chọn ít nhất một đơn hàng',
                    icon: 'error'
                });
                return;
            }
            
            Swal.fire({
                title: 'Xác nhận xóa',
                text: `Bạn có chắc chắn muốn xóa ${selectedIds.length} đơn hàng đã chọn không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
                html: `
                    <div class="alert alert-danger text-left">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Lưu ý:</strong> Chỉ có thể xóa đơn hàng đã hủy. Các đơn hàng không đủ điều kiện sẽ được bỏ qua.
                    </div>
                `
            }).then((result) => {
                if (result.isConfirmed) {
                    bulkDelete(selectedIds);
                }
            });
        });
    }
    
    // Làm mới trang
    function initRefresh() {
        $('#refresh-btn').on('click', function() {
            location.reload();
        });
    }
    
    // Xuất Excel
    function initExport() {
        $('#export-excel-btn').on('click', function() {
            Swal.fire({
                title: 'Xuất Excel',
                text: 'Tính năng xuất Excel đang được phát triển',
                icon: 'info'
            });
        });
    }
    
    // Lấy danh sách ID đã chọn
    function getSelectedIds() {
        const selectedIds = [];
        $('.select-item:checked').each(function() {
            selectedIds.push($(this).val());
        });
        return selectedIds;
    }
    
    // Cập nhật trạng thái nút bulk actions
    function updateBulkButtons() {
        const selectedCount = $('.select-item:checked').length;
        const bulkButtons = ['#bulk-update-trangthai-btn', '#bulk-delete-btn'];
        
        console.log('Selected count:', selectedCount); // Debug log
        
        bulkButtons.forEach(function(selector) {
            const $btn = $(selector);
            if (selectedCount > 0) {
                $btn.prop('disabled', false);
                $btn.removeClass('disabled');
                console.log('Enabled button:', selector); // Debug log
            } else {
                $btn.prop('disabled', true);
                $btn.addClass('disabled');
                console.log('Disabled button:', selector); // Debug log
            }
        });
    }
    
    // AJAX: Cập nhật trạng thái
    function updateTrangThai(formData) {
        $.ajax({
            url: window.DONHANG_ENDPOINTS.updateTrangThai,
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#confirmUpdateTrangThai').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang cập nhật...');
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Thành công',
                        text: response.message,
                        icon: 'success',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Lỗi',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                let message = 'Có lỗi xảy ra khi cập nhật trạng thái';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    title: 'Lỗi',
                    text: message,
                    icon: 'error'
                });
            },
            complete: function() {
                $('#confirmUpdateTrangThai').prop('disabled', false).html('Cập nhật');
            }
        });
    }
    
    // AJAX: Xóa đơn hàng
    function deleteDonHang(donhangId) {
        $.ajax({
            url: window.DONHANG_ENDPOINTS.destroy,
            method: 'POST',
            data: { id: donhangId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Thành công',
                        text: response.message,
                        icon: 'success',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Lỗi',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                let message = 'Có lỗi xảy ra khi xóa đơn hàng';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    title: 'Lỗi',
                    text: message,
                    icon: 'error'
                });
            }
        });
    }
    
    // AJAX: Cập nhật trạng thái hàng loạt
    function bulkUpdateTrangThai(formData) {
        $.ajax({
            url: window.DONHANG_ENDPOINTS.updateTrangThaiBulk,
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#confirmBulkUpdateTrangThai').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang cập nhật...');
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Thành công',
                        text: response.message,
                        icon: 'success',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Lỗi',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                let message = 'Có lỗi xảy ra khi cập nhật trạng thái hàng loạt';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    title: 'Lỗi',
                    text: message,
                    icon: 'error'
                });
            },
            complete: function() {
                $('#confirmBulkUpdateTrangThai').prop('disabled', false).html('Cập nhật');
            }
        });
    }
    
    // AJAX: Xóa hàng loạt
    function bulkDelete(selectedIds) {
        $.ajax({
            url: window.DONHANG_ENDPOINTS.destroy,
            method: 'POST',
            data: { ids: selectedIds },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#confirmBulkDelete').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang xóa...');
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Thành công',
                        text: response.message,
                        icon: 'success',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Lỗi',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                let message = 'Có lỗi xảy ra khi xóa đơn hàng hàng loạt';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    title: 'Lỗi',
                    text: message,
                    icon: 'error'
                });
            },
            complete: function() {
                $('#confirmBulkDelete').prop('disabled', false).html('Xóa');
            }
        });
    }
    
    // Auto refresh stats mỗi 30 giây
    setInterval(function() {
        refreshStats();
    }, 30000);
    
    // Làm mới thống kê
    function refreshStats() {
        $.ajax({
            url: window.DONHANG_ENDPOINTS.getStats,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Cập nhật các số liệu thống kê
                    updateStatsDisplay(response.data);
                }
            }
        });
    }
    
    // Cập nhật hiển thị thống kê
    function updateStatsDisplay(stats) {
        // Cập nhật các số liệu trong các ibox
        $('.ibox-content h2').each(function() {
            const $this = $(this);
            const text = $this.text();
            
            if (text.includes('Tổng đơn hàng')) {
                $this.text(stats.total_orders.toLocaleString());
            } else if (text.includes('Chờ xác nhận')) {
                $this.text(stats.pending_orders.toLocaleString());
            } else if (text.includes('Đang giao')) {
                $this.text(stats.shipping_orders.toLocaleString());
            } else if (text.includes('Đã giao')) {
                $this.text(stats.delivered_orders.toLocaleString());
            }
        });
    }
});
