<div class="ibox-tools">
    <div class="btn-group">
        <button type="button" class="btn btn-info btn-sm" id="refresh-btn">
            <i class="fa fa-refresh"></i> Làm mới
        </button>
    </div>
    
    <div class="btn-group ml-2">
        <button type="button" class="btn btn-warning btn-sm" id="bulk-update-trangthai-btn" disabled>
            <i class="fa fa-edit"></i> Cập nhật trạng thái
        </button>
        
        <button type="button" class="btn btn-danger btn-sm" id="bulk-delete-btn" disabled>
            <i class="fa fa-trash"></i> Xóa đã chọn
        </button>
    </div>
    
    <div class="btn-group ml-2">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-filter"></i> Lọc nhanh <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="{{ route('admin.donhang.index', ['trangthai' => \App\Models\DonHang::TRANGTHAI_CHO_XAC_NHAN]) }}">
                    <i class="fa fa-clock-o text-warning"></i> Chờ xác nhận
                </a></li>
                <li><a href="{{ route('admin.donhang.index', ['trangthai' => \App\Models\DonHang::TRANGTHAI_DA_XAC_NHAN]) }}">
                    <i class="fa fa-check text-info"></i> Đã xác nhận
                </a></li>
                <li><a href="{{ route('admin.donhang.index', ['trangthai' => \App\Models\DonHang::TRANGTHAI_DANG_GIAO]) }}">
                    <i class="fa fa-truck text-primary"></i> Đang giao
                </a></li>
                <li><a href="{{ route('admin.donhang.index', ['trangthai' => \App\Models\DonHang::TRANGTHAI_DA_GIAO]) }}">
                    <i class="fa fa-check-circle text-success"></i> Đã giao
                </a></li>
                <li><a href="{{ route('admin.donhang.index', ['trangthai' => \App\Models\DonHang::TRANGTHAI_DA_HUY]) }}">
                    <i class="fa fa-times-circle text-danger"></i> Đã hủy
                </a></li>
                <li role="separator" class="divider"></li>
                <li><a href="{{ route('admin.donhang.index', ['from_date' => date('Y-m-d')]) }}">
                    <i class="fa fa-calendar"></i> Hôm nay
                </a></li>
                <li><a href="{{ route('admin.donhang.index', ['from_date' => date('Y-m-01')]) }}">
                    <i class="fa fa-calendar"></i> Tháng này
                </a></li>
            </ul>
        </div>
    </div>

    <div class="btn-group ml-2">
        <button type="button" class="btn btn-primary btn-sm" id="edit-store-info-btn">
            <i class="fa fa-building"></i> Sửa thông tin cửa hàng
        </button>
    </div>
</div>

<!-- Modal cập nhật trạng thái hàng loạt -->
<div class="modal fade" id="bulkUpdateTrangThaiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cập nhật trạng thái hàng loạt</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkUpdateTrangThaiForm">
                    <div class="form-group">
                        <label for="bulk_trangthai">Trạng thái mới</label>
                        <select class="form-control" id="bulk_trangthai" name="trangthai" required>
                            <option value="">-- Chọn trạng thái --</option>
                            @foreach($trangThaiOptions as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bulk_nhanvien">Nhân viên xử lý</label>
                        <input type="text" class="form-control" id="bulk_nhanvien" name="nhanvien" 
                               value="{{ auth()->user()->name ?? '' }}" placeholder="Tên nhân viên">
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Sẽ cập nhật trạng thái cho <span id="selected-count">0</span> đơn hàng đã chọn.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmBulkUpdateTrangThai">Cập nhật</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa thông tin cửa hàng -->
<div class="modal fade" id="storeInfoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Sửa thông tin cửa hàng</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="storeInfoForm">
                    <div class="form-group">
                        <label for="store_name">Tên cửa hàng</label>
                        <input type="text" class="form-control" id="store_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="store_address">Địa chỉ</label>
                        <input type="text" class="form-control" id="store_address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="store_phone">Điện thoại</label>
                        <input type="text" class="form-control" id="store_phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="store_email">Email</label>
                        <input type="email" class="form-control" id="store_email" name="email">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="save-store-info-btn">Lưu</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    // Simple toast notification
    function showToast(type, message) {
        const colors = {
            success: '#1ab394', // green
            error: '#e74c3c',   // red
            info: '#3498db',    // blue
            warning: '#f39c12'  // orange
        };
        const $toast = $('<div class="toast-notice"></div>').text(message);
        $toast.css({
            position: 'fixed', top: '15px', left: '50%', transform: 'translateX(-50%)',
            background: colors[type] || colors.info, color: '#fff', padding: '10px 16px',
            borderRadius: '4px', zIndex: 9999, boxShadow: '0 2px 8px rgba(0,0,0,.2)',
            opacity: 0
        });
        $('body').append($toast);
        $toast.animate({ opacity: 1 }, 150);
        setTimeout(function() { $toast.fadeOut(200, function(){ $(this).remove(); }); }, 2000);
    }

    function loadStoreInfo() {
        $.get('{{ route('admin.store.get') }}', function(res) {
            if (res.success && res.data) {
                $('#store_name').val(res.data.name || '');
                $('#store_address').val(res.data.address || '');
                $('#store_phone').val(res.data.phone || '');
                $('#store_email').val(res.data.email || '');
            }
        });
    }

    $('#edit-store-info-btn').on('click', function() {
        loadStoreInfo();
        $('#storeInfoModal').modal('show');
    });

    $('#save-store-info-btn').on('click', function() {
        const data = {
            name: $('#store_name').val(),
            address: $('#store_address').val(),
            phone: $('#store_phone').val(),
            email: $('#store_email').val(),
            _token: '{{ csrf_token() }}'
        };
        $.post('{{ route('admin.store.update') }}', data)
            .done(function(res) {
                if (res.success) {
                    showToast('success', 'Đã lưu thông tin cửa hàng');
                    $('#storeInfoModal').modal('hide');
                } else {
                    showToast('error', res.message || 'Lưu thất bại');
                }
            })
            .fail(function() { showToast('error', 'Lưu thất bại'); });
    });
});
</script>

<script>
// Đóng modal khi có lỗi
$(document).on('click', '#confirmBulkUpdateTrangThai', function() {
    // Đóng modal sau khi click
    setTimeout(function() {
        $('#bulkUpdateTrangThaiModal').modal('hide');
    }, 100);
});
</script>

</div>
