


<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <!-- Bulk Actions đã được di chuyển vào filter component -->
    </div>
    <div>
    <h4 class="mb-0">Danh sách danh mục</h4>
    </div>
</div>

<div class="table-responsive danhmuc-table">
    <table class="table table-striped table-bordered table-hover dataTables-example">
        <thead>
            <tr>
                <th width="5%">
                    <input type="checkbox" id="check-all">
                </th>
                <th width="10%">Ảnh</th>
                <th width="25%">Tên danh mục</th>
                <th width="15%">Danh mục cha</th>
                <th width="10%">Ưu tiên hiển thị</th>
                <th width="15%">Mô tả</th>
                <th width="10%">Trạng thái</th>
                <th width="15%">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($categories) && $categories->count() > 0)
                @foreach($categories as $category)
                    <tr>
                        <td>
                            <input type="checkbox" class="checkbox-item" value="{{ $category->id }}">
                        </td>
                        <td>
                            @if($category->image)
                                <img src="{{ asset('uploads/' . $category->image) }}" 
                                     alt="{{ $category->name }}" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="default-image-placeholder">
                                    Default
                                </div>
                            @endif
                        </td>
                        <td>
                            @php($level = $category->level)
                            <div style="padding-left: {{ max(0, ($level)) * 18 }}px">
                                <strong>
                                    {!! $level > 0 ? str_repeat('— ', $level) : '' !!}{{ $category->name }}
                                </strong>
                                <span class="label label-{{ $level === 0 ? 'default' : 'primary' }}" style="margin-left:6px;">
                                    {{ $level === 0 ? 'Cấp 0 - Gốc' : 'Cấp ' . $level }}
                                </span>
                                <br>
                                <small class="text-muted">Slug: {{ $category->slug }}</small>
                            </div>
                        </td>
                        <td>
                            @if($category->parent)
                                <span class="label label-info">{{ $category->parent->name }}</span>
                            @else
                                <span class="label label-default">Danh mục gốc</span>
                            @endif
                        </td>
                        <td>
                            @php($orderVal = is_null($category->sort_order) ? 0 : (int) $category->sort_order)
                            <span class="badge" title="Ưu tiên hiển thị (0 là mặc định)">{{ $orderVal }}</span>
                        </td>
                        <td>
                            @if($category->description)
                                {{ Str::limit($category->description, 50) }}
                            @else
                                <span class="text-muted">Không có mô tả</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm status-btn-danhmuc {{ $category->status == 'active' ? 'btn-success' : 'btn-danger' }}" data-id="{{ $category->id }}">
                                <i class="fa {{ $category->status == 'active' ? 'fa-check-circle' : 'fa-lock' }}"></i>
                                {{ $category->status == 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                            </button>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('danhmuc.edit', $category->id) }}" class="btn btn-success btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form action="{{ route('danhmuc.destroy', $category->id) }}" method="POST" class="form-delete-danhmuc d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-delete-danhmuc">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center">Không có dữ liệu</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if(isset($categories) && $categories->hasPages())
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Hiển thị {{ $categories->firstItem() ?? 0 }} đến {{ $categories->lastItem() ?? 0 }} trong tổng số {{ $categories->total() }} kết quả
                </div>
                <div>
                    {{ $categories->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Gỡ các handler xung đột từ trang Đơn hàng nếu có
    if (window.jQuery) {
        try { $('#bulk-delete-btn,#bulk-update-trangthai-btn,#confirmBulkDelete,#confirmBulkUpdateTrangThai').off('click'); } catch (e) {}
    }
    // Chặn toastr hiển thị trùng lặp
    if (typeof toastr !== 'undefined') {
        if (!toastr.options) toastr.options = {};
        toastr.options.preventDuplicates = true;
        toastr.options.newestOnTop = true;
        toastr.options.timeOut = 3000;
        toastr.options.extendedTimeOut = 1000;
        toastr.options.closeButton = true;
        toastr.options.progressBar = true;
        
        // Đảm bảo toastr error hiển thị màu đỏ
        toastr.options.iconClasses = {
            error: 'toast-error',
            info: 'toast-info',
            success: 'toast-success',
            warning: 'toast-warning'
        };
        

    }
    // Xác nhận xóa từng danh mục
    const deleteForms = document.querySelectorAll('.form-delete-danhmuc');
    deleteForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Bạn sẽ không thể hoàn tác thao tác này!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Toggle status bằng nút bấm thay vì checkbox
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.status-btn-danhmuc');
        if (!btn) return;

        var id = btn.getAttribute('data-id');
        var isActive = btn.classList.contains('btn-success');
        var status = isActive ? 'inactive' : 'active';

        $.ajax({
            url: BASE_URL + '/ajax/danhmuc/toggle-status-danhmuc',
            type: 'POST',
            data: {
                id: id,
                status: status,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    if (status === 'active') {
                        btn.classList.remove('btn-danger');
                        btn.classList.add('btn-success');
                        btn.innerHTML = '<i class="fa fa-check-circle"></i> Hoạt động';
                    } else {
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-danger');
                        btn.innerHTML = '<i class="fa fa-lock"></i> Không hoạt động';
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.clear();
                        (status === 'active' ? toastr.success : toastr.error)(data.message);
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.clear();
                        toastr.error(data.message || 'Cập nhật trạng thái thất bại!');
                    }
                }
            },
            error: function () {
                if (typeof toastr !== 'undefined') {
                    toastr.clear();
                    toastr.error('Có lỗi xảy ra!');
                }
            }
        });
    });

    // Hàm xóa các thông báo cũ - chỉ xóa toastr
    function clearOldNotifications() {
        if (typeof toastr !== 'undefined') {
            toastr.clear();
        }
    }



    // Bulk actions - sử dụng các form có sẵn
    const checkboxes = document.querySelectorAll('.checkbox-item');
    const checkAll = document.getElementById('check-all');
    const bulkActions = document.querySelector('.bulk-actions');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const bulkActivateBtn = document.getElementById('bulk-activate-btn');
    const bulkDeactivateBtn = document.getElementById('bulk-deactivate-btn');

    function updateBulkActionButtons() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        
        // Ẩn/hiện bulk actions
        if (anyChecked) {
            if (bulkActions) {
                bulkActions.classList.remove('hide');
                bulkActions.classList.add('show');
                bulkActions.style.display = 'flex';
            }
        } else {
            if (bulkActions) {
                bulkActions.classList.remove('show');
                bulkActions.classList.add('hide');
            bulkActions.style.display = 'none';
            }
        }
        
        // Enable/disable buttons
        if (bulkDeleteBtn) bulkDeleteBtn.disabled = !anyChecked;
        if (bulkActivateBtn) bulkActivateBtn.disabled = !anyChecked;
        if (bulkDeactivateBtn) bulkDeactivateBtn.disabled = !anyChecked;
    }

    // Thêm event listener cho từng checkbox
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateBulkActionButtons);
    });

    // Thêm event listener cho checkbox "chọn tất cả"
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = checkAll.checked;
            });
            updateBulkActionButtons();
        });
    }

    // Bulk Delete - sử dụng form có sẵn
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function(e) {
            // Chặn tất cả handler khác (trang Đơn hàng) và tự xử lý
            e.preventDefault();
            if (e.stopImmediatePropagation) e.stopImmediatePropagation();
            const selectedIds = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            if (selectedIds.length === 0) {
                if (typeof toastr !== 'undefined') { toastr.clear(); toastr.error('Vui lòng chọn ít nhất một danh mục!'); }
                return;
            }
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: `Bạn sẽ xóa ${selectedIds.length} danh mục đã chọn!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('bulk-delete-form');
                    let idsInput = form.querySelector('input[name="ids"]');
                    if (!idsInput) {
                        idsInput = document.createElement('input');
                        idsInput.type = 'hidden';
                        idsInput.name = 'ids';
                        form.appendChild(idsInput);
                    }
                    idsInput.value = selectedIds.join(',');
                    form.submit();
                }
            });
        });
    }

    // Chặn submit nếu chưa chọn cho cả 3 form bulk (delete/activate/deactivate)
    ['bulk-delete-form','bulk-activate-form','bulk-deactivate-form'].forEach(function(formId){
        const form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener('submit', function(e){
            const selectedIds = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
            if (selectedIds.length === 0) {
                e.preventDefault();
                if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                if (typeof toastr !== 'undefined') { toastr.clear(); toastr.error('Vui lòng chọn ít nhất một danh mục!'); }
                return false;
            }
            let input = form.querySelector('input[name="ids"]');
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids';
                form.appendChild(input);
            }
            input.value = selectedIds.join(',');
        }, true);
    });

    // Bulk Activate - sử dụng form có sẵn
    if (bulkActivateBtn) {
        bulkActivateBtn.addEventListener('click', function() {
            const selectedIds = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (selectedIds.length === 0) return;
            
            const form = document.getElementById('bulk-activate-form');
            const idsInput = form.querySelector('input[name="ids"]');
            if (!idsInput) {
                const newInput = document.createElement('input');
                newInput.type = 'hidden';
                newInput.name = 'ids';
                newInput.value = selectedIds.join(',');
                form.appendChild(newInput);
            } else {
                idsInput.value = selectedIds.join(',');
            }
            form.submit();
        });
    }

    // Bulk Deactivate - sử dụng form có sẵn
    if (bulkDeactivateBtn) {
        bulkDeactivateBtn.addEventListener('click', function() {
            const selectedIds = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (selectedIds.length === 0) return;
            
            const form = document.getElementById('bulk-deactivate-form');
            const idsInput = form.querySelector('input[name="ids"]');
            if (!idsInput) {
                const newInput = document.createElement('input');
                newInput.type = 'hidden';
                newInput.name = 'ids';
                newInput.value = selectedIds.join(',');
                form.appendChild(newInput);
            } else {
                idsInput.value = selectedIds.join(',');
            }
            form.submit();
        });
    }

    // Khởi tạo trạng thái ban đầu - ẩn bulk actions
    updateBulkActionButtons();
});
</script>