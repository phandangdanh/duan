@extends('backend.layout')
@section('title', 'Chi tiết đơn hàng #' . $donhang->id)

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/css/css-donhang.css') }}">
@endpush

@section('content')

<div class="wrapper wrapper-content">
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Chi tiết đơn hàng #{{ $donhang->id }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('dashboard.index') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ route('admin.donhang.index') }}">Quản lý đơn hàng</a>
                </li>
                <li class="active">
                    <strong>Chi tiết đơn hàng #{{ $donhang->id }}</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            <a href="{{ route('admin.donhang.index') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Quay lại
            </a>
            <a href="{{ route('admin.donhang.print', $donhang->id) }}" class="btn btn-primary btn-sm" target="_blank">
                <i class="fa fa-print"></i> In hóa đơn
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin đơn hàng -->
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin đơn hàng</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Mã đơn hàng:</strong></td>
                                    <td>#{{ $donhang->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        <span class="badge {{ $donhang->trang_thai_badge_class }}">
                                            {{ $donhang->trang_thai_text }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tạo:</strong></td>
                                    <td>{{ $donhang->ngaytao ? $donhang->ngaytao->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày thanh toán:</strong></td>
                                    <td>{{ $donhang->ngaythanhtoan ? $donhang->ngaythanhtoan->format('d/m/Y H:i:s') : 'Chưa thanh toán' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phương thức thanh toán:</strong></td>
                                    <td>
                                        @if($donhang->phuongthucthanhtoan)
                                            <span class="badge badge-info">
                                                {{ $donhang->phuongthucthanhtoan }}
                                            </span>
                                        @else
                                            <span class="text-muted">Chưa xác định</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái thanh toán:</strong></td>
                                    <td>
                                        @if($donhang->trangthaithanhtoan)
                                            <span class="badge {{ $donhang->trangthaithanhtoan == 'da_thanh_toan' ? 'badge-success' : 'badge-warning' }}">
                                                {{ $donhang->trangthaithanhtoan == 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Chưa xác định</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Nhân viên xử lý:</strong></td>
                                    <td>{{ $donhang->nhanvien ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Voucher:</strong></td>
                                    <td>
                                        @if($donhang->donHangVoucher && $donhang->donHangVoucher->count() > 0)
                                            <div class="donhang-voucher">
                                                @foreach($donhang->donHangVoucher as $donHangVoucher)
                                                    @if($donHangVoucher->voucher)
                                                        <div class="voucher-info mb-1">
                                                            <span class="badge badge-info" title="{{ $donHangVoucher->voucher->ten_voucher }}">
                                                                {{ $donHangVoucher->voucher->ma_voucher }}
                                                            </span>
                                                            <small class="text-muted ml-2">
                                                                ({{ $donHangVoucher->voucher->ten_voucher }})
                                                            </small>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">Không có voucher</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Tổng tiền:</strong></td>
                                    <td class="text-success"><strong>{{ $donhang->tong_tien_formatted }}</strong></td>
                                </tr>
                                @if($donhang->donHangVoucher && $donhang->donHangVoucher->count() > 0)
                                    @php
                                        $totalDiscount = 0;
                                        foreach($donhang->donHangVoucher as $donHangVoucher) {
                                            if($donHangVoucher->voucher) {
                                                if($donHangVoucher->voucher->loai_giam_gia == 'phan_tram') {
                                                    $totalDiscount += ($donhang->tong_tien * $donHangVoucher->voucher->gia_tri / 100);
                                                } else {
                                                    $totalDiscount += $donHangVoucher->voucher->gia_tri;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if($totalDiscount > 0)
                                        <tr>
                                            <td><strong>Giảm giá:</strong></td>
                                            <td class="donhang-discount"><strong>-{{ number_format($totalDiscount, 0, ',', '.') }} VND</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Thành tiền:</strong></td>
                                            <td class="donhang-final-amount"><strong>{{ number_format($donhang->tong_tien - $totalDiscount, 0, ',', '.') }} VND</strong></td>
                                        </tr>
                                    @endif
                                @endif
                                <tr>
                                    <td><strong>Ghi chú:</strong></td>
                                    <td>{{ $donhang->ghichu ?? 'Không có ghi chú' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chi tiết sản phẩm -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Chi tiết sản phẩm</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tên sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donhang->chiTietDonHang as $item)
                                    <tr>
                                        <td>{{ $item->tensanpham }}</td>
                                        <td>{{ $item->dongia_formatted }}</td>
                                        <td>{{ $item->soluong }}</td>
                                        <td class="text-success"><strong>{{ $item->thanhtien_formatted }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Không có sản phẩm nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="3" class="text-right"><strong>Tổng cộng:</strong></td>
                                    <td class="text-success"><strong>{{ $donhang->tong_tien_formatted }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin khách hàng và lịch sử -->
        <div class="col-lg-4">
            <!-- Thông tin khách hàng -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin khách hàng</h5>
                </div>
                <div class="ibox-content">
                    @if($donhang->user)
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Tên:</strong></td>
                                <td>{{ $donhang->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $donhang->user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Số điện thoại:</strong></td>
                                <td>{{ $donhang->user->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Địa chỉ:</strong></td>
                                <td>{{ $donhang->user->address ?? 'N/A' }}</td>
                            </tr>
                            @if($donhang->diachigiaohang)
                                <tr>
                                    <td><strong>Địa chỉ giao hàng:</strong></td>
                                    <td>{{ $donhang->diachigiaohang }}</td>
                                </tr>
                            @endif
                            @if($donhang->sodienthoai)
                                <tr>
                                    <td><strong>Số điện thoại giao hàng:</strong></td>
                                    <td>{{ $donhang->sodienthoai }}</td>
                                </tr>
                            @endif
                            @if($donhang->email)
                                <tr>
                                    <td><strong>Email giao hàng:</strong></td>
                                    <td>{{ $donhang->email }}</td>
                                </tr>
                            @endif
                            @if($donhang->hoten)
                                <tr>
                                    <td><strong>Tên người nhận:</strong></td>
                                    <td>{{ $donhang->hoten }}</td>
                                </tr>
                            @endif
                        </table>
                    @else
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            Không có thông tin khách hàng
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lịch sử trạng thái -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Lịch sử trạng thái</h5>
                </div>
                <div class="ibox-content">
                    @php
                        $lichSu = json_decode($donhang->lichsutrangthai ?? '[]', true);
                    @endphp
                    
                    @if(!empty($lichSu))
                        <div class="timeline">
                            @foreach($lichSu as $index => $item)
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">
                                            {{ \App\Models\DonHang::getTrangThaiOptions()[$item['trangthai_moi']] ?? $item['trangthai_moi'] }}
                                        </h6>
                                        <p class="timeline-text">
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($item['thoi_gian'])->format('d/m/Y H:i:s') }}
                                            </small>
                                            <br>
                                            Nhân viên: {{ $item['nhan_vien'] ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Chưa có lịch sử thay đổi trạng thái
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cập nhật trạng thái -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Cập nhật trạng thái</h5>
                </div>
                <div class="ibox-content">
                    <form id="updateTrangThaiForm">
                        <input type="hidden" name="id" value="{{ $donhang->id }}">
                        <div class="form-group">
                            <label for="trangthai">Trạng thái mới</label>
                            <select class="form-control" name="trangthai" required>
                                <option value="">-- Chọn trạng thái --</option>
                                @foreach($trangThaiOptions as $key => $value)
                                    <option value="{{ $key }}" {{ $donhang->trangthai == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nhanvien">Nhân viên xử lý</label>
                            <input type="text" class="form-control" name="nhanvien" 
                                   value="{{ auth()->user()->name ?? '' }}" placeholder="Tên nhân viên">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-save"></i> Cập nhật
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 10px;
    height: 10px;
    background-color: #007bff;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
}

.timeline-text {
    margin: 0;
    font-size: 12px;
    color: #6c757d;
}

.table-borderless td {
    border: none;
    padding: 5px 0;
}

.bg-light {
    background-color: #f8f9fa !important;
}

/* Voucher Info Styling */
.voucher-info {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.voucher-info .badge {
    font-size: 0.8rem;
    padding: 0.4em 0.8em;
    border-radius: 15px;
}

.voucher-info .text-muted {
    font-size: 0.75rem;
    color: #6c757d;
}

.voucher-info .text-muted:hover {
    color: #495057;
}
</style>

<script>
$(document).ready(function() {
    $('#updateTrangThaiForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("ajax.donhang.update.trangthai") }}',
            method: 'POST',
            data: $(this).serialize(),
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
            error: function() {
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra khi cập nhật trạng thái',
                    icon: 'error'
                });
            }
        });
    });
});
</script>

@endsection
