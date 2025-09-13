    @extends('backend.layout')
    @section('title', 'Trang User')
    @section('content')
        <div class="wrapper wrapper-content">
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>{{ config('apps.user.title') }}</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard.index') }}">Dashboard</a>
                        </li>
                        <li class="active">
                            <a href="{{ route('user.index') }}">
                                <strong>{{ $config['seo']['title'] }}</strong>
                            </a>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
            <!-- KPI tổng quan User -->
            @php
                $__total = (int)($stats['total_users'] ?? 0);
                $__active = (int)($stats['active_users'] ?? 0);
                $__inactive = (int)($stats['inactive_users'] ?? 0);
                $__admin = (int)($stats['admin_users'] ?? 0);
                $pct = function($v,$t){ return $t>0 ? number_format($v*100/$t,1) : 0; };
            @endphp
            <div class="row mt10">
                <div class="col-lg-3">
                    <div class="ibox">
                        <div class="ibox-content">
                            <h5 class="m-b-none">Tổng user</h5>
                            <h2 class="no-margins text-primary">{{ number_format($stats['total_users']) }}</h2>
                            <small>Tất cả user trong hệ thống</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="ibox">
                        <div class="ibox-content">
                            <h5 class="m-b-none">Đang hoạt động</h5>
                            <h2 class="no-margins text-success">{{ number_format($stats['active_users']) }}</h2>
                            <small>Tỉ lệ: {{ $pct($__active,$__total) }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="ibox">
                        <div class="ibox-content">
                            <h5 class="m-b-none">Ngừng hoạt động</h5>
                            <h2 class="no-margins text-warning">{{ number_format($stats['inactive_users']) }}</h2>
                            <small>Tỉ lệ: {{ $pct($__inactive,$__total) }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="ibox">
                        <div class="ibox-content">
                            <h5 class="m-b-none">Admin</h5>
                            <h2 class="no-margins text-danger">{{ number_format($stats['admin_users']) }}</h2>
                            <small>Tỉ lệ: {{ $pct($__admin,$__total) }}%</small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Trang danh sách: bỏ thống kê/biểu đồ để gọn, thống kê chuyển sang trang riêng -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>
                                <a href="{{ route('user.index') }}" style="color: inherit; text-decoration: none;">
                                    {{ $config['seo']['table'] }}
                                </a>
                            </h5>
                            @include('backend.user.component.toolbox')
                        </div>
                        <div class="ibox-content">
                            @include('backend.user.component.filter')
                            @include('backend.user.component.table')
                        </div>
                    </div>
                </div>
            </div>

        @endsection

        @push('scripts')
        <script>
        $(document).ready(function() {
            // Gỡ các handler xung đột từ trang Đơn hàng
            $('#bulk-delete-btn,#bulk-update-trangthai-btn,#confirmBulkDelete,#confirmBulkUpdateTrangThai').off('click');
            // Cấu hình toastr tránh trùng lặp thông báo
            if (typeof toastr !== 'undefined') {
                if (!toastr.options) toastr.options = {};
                toastr.options.preventDuplicates = true;
                toastr.options.newestOnTop = true;
                toastr.options.timeOut = 3000;
                toastr.options.extendedTimeOut = 1000;
                toastr.options.closeButton = true;
                toastr.options.progressBar = true;
                toastr.clear();
            }
            // Xử lý click vào nút trạng thái
            $('.status-btn').on('click', function() {
                const userId = $(this).data('id');
                const currentStatus = $(this).hasClass('btn-success') ? 1 : 0;
                const newStatus = currentStatus === 1 ? 0 : 1;
                
                // Toggle switch
                const switchElement = $(this).siblings('.js-switch');
                switchElement.prop('checked', newStatus === 1);
                
                // Update button
                if (newStatus === 1) {
                    $(this).removeClass('btn-danger').addClass('btn-success');
                    $(this).html('<i class="fa fa-check-circle"></i> Hoạt động');
                } else {
                    $(this).removeClass('btn-success').addClass('btn-danger');
                    $(this).html('<i class="fa fa-lock"></i> Khóa');
                }
                
                // Gửi AJAX request để cập nhật status
                $.ajax({
                    url: '{{ route("ajax.user.toggleStatus") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: userId,
                        status: newStatus
                    },
                    success: function(response) {
                        if (response && response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.clear();
                                toastr.success(response.message || 'Cập nhật trạng thái thành công');
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.clear();
                                toastr.error((response && response.message) || 'Có lỗi xảy ra khi cập nhật trạng thái');
                            } else {
                                alert('Có lỗi xảy ra khi cập nhật trạng thái');
                            }
                        }
                    },
                    error: function() {
                        if (typeof toastr !== 'undefined') {
                            toastr.clear();
                            toastr.error('Có lỗi xảy ra khi cập nhật trạng thái');
                        } else {
                            alert('Có lỗi xảy ra khi cập nhật trạng thái');
                        }
                    }
                });
            });

            // Xử lý toggle trực tiếp trên checkbox js-switch
            $('.js-switch.toggle-status').on('change', function() {
                const newStatus = $(this).is(':checked') ? 1 : 0;
                const userId = $(this).data('id');

                // Đồng bộ nút trạng thái bên cạnh
                const btn = $(this).siblings('.status-btn');
                if (newStatus === 1) {
                    btn.removeClass('btn-danger').addClass('btn-success');
                    btn.html('<i class="fa fa-check-circle"></i> Hoạt động');
                } else {
                    btn.removeClass('btn-success').addClass('btn-danger');
                    btn.html('<i class="fa fa-lock"></i> Khóa');
                }

                $.ajax({
                    url: '{{ route("ajax.user.toggleStatus") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: userId,
                        status: newStatus
                    },
                    success: function(response) {
                        if (!response.success) {
                            alert('Có lỗi xảy ra khi cập nhật trạng thái');
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi cập nhật trạng thái');
                        location.reload();
                    }
                });
            });

            // Chọn tất cả checkbox trên bảng user
            const checkAll = document.getElementById('checkAll');
            const itemCheckboxes = document.querySelectorAll('.checkbox-item');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const bulkLockBtn = document.getElementById('bulk-lock-btn');
            const bulkUnlockBtn = document.getElementById('bulk-unlock-btn');
            const bulkCollabBtn = document.getElementById('bulk-collab-btn');
            const bulkAdminBtn = document.getElementById('bulk-admin-btn');

            function updateBulkButtons() {
                const anyChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
                if (bulkDeleteBtn) bulkDeleteBtn.disabled = !anyChecked;
                if (bulkLockBtn) bulkLockBtn.disabled = !anyChecked;
                if (bulkUnlockBtn) bulkUnlockBtn.disabled = !anyChecked;
                if (bulkCollabBtn) bulkCollabBtn.disabled = !anyChecked;
                if (bulkAdminBtn) bulkAdminBtn.disabled = !anyChecked;
            }

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    itemCheckboxes.forEach(cb => { cb.checked = checkAll.checked; });
                    updateBulkButtons();
                });
            }

            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (checkAll) {
                        const allChecked = Array.from(itemCheckboxes).every(x => x.checked);
                        const noneChecked = Array.from(itemCheckboxes).every(x => !x.checked);
                        // Optional: set indeterminate state
                        checkAll.indeterminate = !allChecked && !noneChecked;
                        checkAll.checked = allChecked;
                    }
                    updateBulkButtons();
                });
            });

            // Khởi tạo trạng thái ban đầu
            updateBulkButtons();

            // Helper: lấy danh sách ID đã chọn
            function getSelectedIds() {
                return Array.from(document.querySelectorAll('.checkbox-item:checked')).map(cb => cb.value);
            }

            // Gắn xử lý submit cho các form bulk để nhét danh sách ids
            ['bulk-delete-form','bulk-lock-form','bulk-unlock-form','bulk-collab-form','bulk-admin-form'].forEach(function(formId){
                const form = document.getElementById(formId);
                if (!form) return;
                form.addEventListener('submit', function(e){
                    const ids = getSelectedIds();
                    if (ids.length === 0) {
                        e.preventDefault();
                        if (typeof toastr !== 'undefined') {
                            toastr.clear();
                            toastr.error('Vui lòng chọn ít nhất một user.');
                        } else {
                            alert('Vui lòng chọn ít nhất một user.');
                        }
                        return false;
                    }
                    let input = form.querySelector('input[name="ids"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids';
                        form.appendChild(input);
                    }
                    input.value = ids.join(',');
                });
            });
        });

        // Charts
        (function(){
            if (typeof Chart === 'undefined') return;
            const lineCtx = document.getElementById('userLineChart');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: @json($chart['labels'] ?? []),
                        datasets: [{
                            label: 'Đăng ký',
                            data: @json($chart['data'] ?? []),
                            borderColor: '#1ab394',
                            backgroundColor: 'rgba(26,179,148,0.15)',
                            tension: 0.3,
                            fill: true,
                        }]
                    },
                    options: {responsive: true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}
                });
            }
            const donutCtx = document.getElementById('userDonutChart');
            if (donutCtx) {
                const active = {{ (int)($stats['active_users'] ?? 0) }};
                const inactive = {{ (int)($stats['inactive_users'] ?? 0) }};
                const admin = {{ (int)($stats['admin_users'] ?? 0) }};
                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active','Inactive','Admin'],
                        datasets: [{
                            data: [active, inactive, admin],
                            backgroundColor: ['#1ab394','#f8ac59','#ed5565']
                        }]
                    },
                    options: {responsive:true, plugins:{legend:{position:'bottom'}}}
                });
            }
        })();
        </script>
        @endpush
