@extends('backend.layout')

@section('title', 'Chi tiết Voucher')

@section('content')
<div class="container-fluid voucher-detail-page">
    <!-- Breadcrumb -->
    @include('backend.component.breadcrum', [
        'title' => 'Chi tiết Voucher',
        'items' => [
            ['text' => 'Dashboard', 'url' => route('dashboard.index')],
            ['text' => 'Voucher', 'url' => route('admin.vouchers.index')],
            ['text' => 'Chi tiết', 'active' => true]
        ]
    ])

    <div class="row">
        <!-- Thông tin cơ bản -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin Voucher</h6>
                    <div>
                        <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.vouchers.toggle-status', $voucher->id) }}" 
                           class="btn btn-{{ $voucher->trang_thai ? 'secondary' : 'success' }} btn-sm toggle-status">
                            <i class="fas fa-{{ $voucher->trang_thai ? 'pause' : 'play' }}"></i> 
                            {{ $voucher->trang_thai ? 'Tạm dừng' : 'Kích hoạt' }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Mã Voucher:</strong></td>
                                    <td><span class="badge badge-info">{{ $voucher->ma_voucher }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Tên Voucher:</strong></td>
                                    <td>{{ $voucher->ten_voucher }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Loại giảm giá:</strong></td>
                                    <td>
                                        @if($voucher->loai_giam_gia === 'phan_tram')
                                            <span class="badge badge-primary">Phần trăm</span>
                                        @else
                                            <span class="badge badge-success">Tiền mặt</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Giá trị giảm:</strong></td>
                                    <td>
                                        @if($voucher->loai_giam_gia === 'phan_tram')
                                            {{ $voucher->gia_tri }}%
                                        @else
                                            {{ number_format($voucher->gia_tri, 0, ',', '.') }}đ
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Giá trị tối thiểu:</strong></td>
                                    <td>{{ number_format($voucher->gia_tri_toi_thieu, 0, ',', '.') }}đ</td>
                                </tr>
                                @if($voucher->gia_tri_toi_da)
                                <tr>
                                    <td><strong>Giá trị tối đa:</strong></td>
                                    <td>{{ number_format($voucher->gia_tri_toi_da, 0, ',', '.') }}đ</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Trạng thái:</strong></td>
                                    <td>
                                        @if($voucher->trang_thai)
                                            @if($voucher->isUsable())
                                                <span class="badge badge-success">Hoạt động</span>
                                            @elseif(now() < $voucher->ngay_bat_dau)
                                                <span class="badge badge-info">Chưa bắt đầu</span>
                                            @elseif(now() > $voucher->ngay_ket_thuc)
                                                <span class="badge badge-danger">Hết hạn</span>
                                            @elseif($voucher->so_luong_da_su_dung >= $voucher->so_luong)
                                                <span class="badge badge-warning">Hết số lượng</span>
                                            @else
                                                <span class="badge badge-secondary">Khác</span>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">Tạm dừng</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày bắt đầu:</strong></td>
                                    <td>{{ $voucher->ngay_bat_dau ? $voucher->ngay_bat_dau->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày kết thúc:</strong></td>
                                    <td>{{ $voucher->ngay_ket_thuc ? $voucher->ngay_ket_thuc->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Thời gian còn lại:</strong></td>
                                    <td>
                                        @if($voucher->ngay_ket_thuc)
                                            @php
                                                $daysLeft = now()->diffInDays($voucher->ngay_ket_thuc, false);
                                            @endphp
                                            @if($daysLeft > 0)
                                                <span class="text-success">{{ $daysLeft }} ngày</span>
                                            @elseif($daysLeft == 0)
                                                <span class="text-warning">Hết hạn hôm nay</span>
                                            @else
                                                <span class="text-danger">Đã hết hạn {{ abs($daysLeft) }} ngày</span>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tạo:</strong></td>
                                    <td>{{ $voucher->created_at ? $voucher->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cập nhật lần cuối:</strong></td>
                                    <td>{{ $voucher->updated_at ? $voucher->updated_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($voucher->mota)
                    <div class="mt-3">
                        <strong>Mô tả:</strong>
                        <p class="mt-2">{{ $voucher->mota }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Thống kê sử dụng -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê sử dụng</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tổng số lượng</span>
                            <span class="font-weight-bold text-primary">{{ $voucher->so_luong }}</span>
                        </div>
                        @php
                            $percentage = $voucher->so_luong > 0 ? ($voucher->so_luong_da_su_dung / $voucher->so_luong) * 100 : 0;
                        @endphp
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $percentage > 80 ? 'bg-danger' : ($percentage > 50 ? 'bg-warning' : 'bg-success') }}" 
                                 role="progressbar" style="width: {{ $percentage }}%">
                            </div>
                        </div>
                    </div>

                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="border-right">
                                <div class="h4 text-primary mb-1">{{ $voucher->so_luong_da_su_dung }}</div>
                                <div class="text-muted small">Đã sử dụng</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success mb-1">{{ $voucher->so_luong_con_lai }}</div>
                            <div class="text-muted small">Còn lại</div>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="text-muted small mb-1">Tỷ lệ sử dụng</div>
                        <div class="h3 text-primary mb-0">{{ round($percentage, 1) }}%</div>
                    </div>
                </div>
            </div>

            <!-- Thông tin bổ sung -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin bổ sung</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Trạng thái hoạt động:</span>
                            <span class="badge badge-success">{{ $voucher->trang_thai_text }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Có thể sử dụng:</span>
                            @if($voucher->isUsable())
                                <span class="text-success"><i class="fas fa-check-circle"></i> Có</span>
                            @else
                                <span class="text-danger"><i class="fas fa-times-circle"></i> Không</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Loại giảm giá:</span>
                            <span class="badge badge-info">{{ $voucher->loai_giam_gia_text }}</span>
                        </div>
                    </div>

                    @if($voucher->gia_tri_toi_da)
                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Giới hạn tối đa:</span>
                            <span class="font-weight-bold text-primary">{{ number_format($voucher->gia_tri_toi_da, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch sử sử dụng (nếu có) -->
    @if($voucher->so_luong_da_su_dung > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lịch sử sử dụng</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Chức năng xem lịch sử sử dụng voucher sẽ được triển khai trong phiên bản tiếp theo.
            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="card shadow mb-4">
        <div class="card-body text-center">
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher->id) }}" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger delete-voucher">
                    <i class="fas fa-trash"></i> Xóa voucher
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Toggle status with SweetAlert2
    $('.toggle-status').click(function(e) {
        e.preventDefault();
        const button = $(this);
        const url = button.attr('href');
        const currentStatus = button.hasClass('btn-secondary'); // true if currently active (pause button)
        const newStatusText = currentStatus ? 'Tạm dừng' : 'Kích hoạt';
        const confirmText = `Bạn có chắc chắn muốn ${newStatusText.toLowerCase()} voucher này?`;

        Swal.fire({
            title: 'Xác nhận thay đổi trạng thái',
            text: confirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Vâng, ${newStatusText.toLowerCase()}!`,
            cancelButtonText: 'Hủy bỏ',
            customClass: {
                popup: 'swal2-popup-custom',
                confirmButton: 'swal2-confirm-custom',
                cancelButton: 'swal2-cancel-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: response.message,
                            icon: 'success',
                            customClass: {
                                popup: 'swal2-popup-custom',
                                confirmButton: 'swal2-confirm-custom'
                            }
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: response.message,
                            icon: 'error',
                            customClass: {
                                popup: 'swal2-popup-custom',
                                confirmButton: 'swal2-confirm-custom'
                            }
                        });
                    }
                })
                .fail(function() {
                    Swal.fire({
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra khi cập nhật trạng thái.',
                        icon: 'error',
                        customClass: {
                            popup: 'swal2-popup-custom',
                            confirmButton: 'swal2-confirm-custom'
                        }
                    });
                });
            }
        });
    });

    // Delete confirmation with SweetAlert2
    $('.delete-voucher').click(function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        Swal.fire({
            title: 'Xác nhận xóa voucher',
            text: 'Bạn có chắc chắn muốn xóa voucher này? Hành động này không thể hoàn tác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Vâng, xóa voucher!',
            cancelButtonText: 'Hủy bỏ',
            customClass: {
                popup: 'swal2-popup-custom',
                confirmButton: 'swal2-confirm-custom',
                cancelButton: 'swal2-cancel-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form thay vì dùng AJAX
                form.submit();
            }
        });
    });
});
</script>
@endpush
