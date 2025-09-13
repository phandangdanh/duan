<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover" id="donhang-table">
        <thead>
            <tr>
                <th width="5%">
                    <input type="checkbox" id="select-all" class="form-check-input">
                </th>
                <th width="8%">Mã đơn hàng</th>
                <th width="15%">Khách hàng</th>
                <th width="10%">Trạng thái</th>
                <th width="12%">Ngày tạo</th>
                <th width="12%">Ngày thanh toán</th>
                <th width="8%">Voucher</th>
                <th width="10%">Tổng tiền</th>
                <th width="8%">Nhân viên</th>
                <th width="17%">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donhangs as $donhang)
                <tr data-id="{{ $donhang->id }}">
                    <td>
                        <input type="checkbox" class="form-check-input select-item" 
                               value="{{ $donhang->id }}">
                    </td>
                    <td>
                        <strong>#{{ $donhang->id }}</strong>
                    </td>
                    <td>
                        <div class="customer-info">
                            <strong>{{ $donhang->user->name ?? 'N/A' }}</strong>
                            @if($donhang->user)
                                <br>
                                <small class="text-muted">{{ $donhang->user->email }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $donhang->trang_thai_badge_class }}">
                            {{ $donhang->trang_thai_text }}
                        </span>
                    </td>
                    <td>
                        {{ $donhang->ngaytao ? $donhang->ngaytao->format('d/m/Y H:i') : 'N/A' }}
                    </td>
                    <td>
                        {{ $donhang->ngaythanhtoan ? $donhang->ngaythanhtoan->format('d/m/Y H:i') : 'Chưa thanh toán' }}
                    </td>
                    <td>
                        @if($donhang->donHangVoucher && $donhang->donHangVoucher->count() > 0)
                            @foreach($donhang->donHangVoucher as $donHangVoucher)
                                @if($donHangVoucher->voucher)
                                    <span class="badge badge-info" title="{{ $donHangVoucher->voucher->ten_voucher }}">
                                        {{ $donHangVoucher->voucher->ma_voucher }}
                                    </span>
                                    <br>
                                @endif
                            @endforeach
                        @else
                            <span class="text-muted">Không có</span>
                        @endif
                    </td>
                    <td>
                        <strong class="text-success">{{ $donhang->tong_tien_formatted }}</strong>
                    </td>
                    <td>
                        {{ $donhang->nhanvien ?? 'N/A' }}
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.donhang.show', $donhang->id) }}" 
                               class="btn btn-info btn-sm" title="Xem chi tiết">
                                <i class="fa fa-eye"></i>
                            </a>
                            
                            <button type="button" class="btn btn-warning btn-sm update-trangthai-btn" 
                                    data-id="{{ $donhang->id }}" 
                                    data-trangthai="{{ $donhang->trangthai }}"
                                    title="Cập nhật trạng thái">
                                <i class="fa fa-edit"></i>
                            </button>
                            
                            <a href="{{ route('admin.donhang.print', $donhang->id) }}" 
                               class="btn btn-primary btn-sm" title="In hóa đơn" target="_blank">
                                <i class="fa fa-print"></i>
                            </a>
                            
                            <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                    data-id="{{ $donhang->id }}" 
                                    title="{{ $donhang->trangthai === \App\Models\DonHang::TRANGTHAI_DA_HUY ? 'Xóa đơn hàng' : 'Chỉ có thể xóa đơn hàng đã hủy' }}"
                                    {{ $donhang->trangthai !== \App\Models\DonHang::TRANGTHAI_DA_HUY ? 'disabled' : '' }}>
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Không có đơn hàng nào
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
.customer-info strong {
    color: #2c3e50;
    font-size: 14px;
}

.customer-info small {
    font-size: 12px;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Style cho nút disabled */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn:disabled:hover {
    opacity: 0.6;
}

#donhang-table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

#donhang-table td {
    vertical-align: middle;
    padding: 12px 8px;
}

.badge {
    font-size: 11px;
    padding: 4px 8px;
}

.badge-warning {
    background-color: #f0ad4e;
}

.badge-info {
    background-color: #5bc0de;
}

.badge-primary {
    background-color: #337ab7;
}

.badge-success {
    background-color: #5cb85c;
}

.badge-danger {
    background-color: #d9534f;
}

.badge-secondary {
    background-color: #6c757d;
}
</style>
