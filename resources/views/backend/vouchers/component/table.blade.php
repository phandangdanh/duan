<div class="table-responsive">
    <table class="table table-bordered voucher-table" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="15%">Mã Voucher</th>
                <th width="20%">Tên Voucher</th>
                <th width="10%">Loại</th>
                <th width="10%">Giá trị</th>
                <th width="10%">Số lượng</th>
                <th width="10%">Trạng thái</th>
                <th width="10%">Hạn sử dụng</th>
                <th width="10%">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vouchers as $voucher)
            <tr>
                <td>{{ $vouchers->firstItem() + $loop->index }}</td>
                <td>
                    <span class="badge badge-info">{{ $voucher->ma_voucher }}</span>
                </td>
                <td>
                    <div class="font-weight-bold">{{ $voucher->ten_voucher }}</div>
                    @if($voucher->mota)
                        <small class="text-muted">{{ Str::limit($voucher->mota, 50) }}</small>
                    @endif
                </td>
                <td>
                    @if($voucher->loai_giam_gia === 'phan_tram')
                        <span class="badge badge-primary">Phần trăm</span>
                    @else
                        <span class="badge badge-success">Tiền mặt</span>
                    @endif
                </td>
                <td>
                    @if($voucher->loai_giam_gia === 'phan_tram')
                        {{ $voucher->gia_tri }}%
                    @else
                        {{ number_format($voucher->gia_tri, 0, ',', '.') }}đ
                    @endif
                    @if($voucher->gia_tri_toi_da)
                        <br><small class="text-muted">Tối đa: {{ number_format($voucher->gia_tri_toi_da, 0, ',', '.') }}đ</small>
                    @endif
                </td>
                <td>
                    <div class="progress mb-1" style="height: 20px;">
                        @php
                            $percentage = $voucher->so_luong > 0 ? ($voucher->so_luong_da_su_dung / $voucher->so_luong) * 100 : 0;
                        @endphp
                        <div class="progress-bar {{ $percentage > 80 ? 'bg-danger' : ($percentage > 50 ? 'bg-warning' : 'bg-success') }}" 
                             role="progressbar" style="width: {{ $percentage }}%">
                            {{ round($percentage, 1) }}%
                        </div>
                    </div>
                    <small class="text-muted">
                        {{ $voucher->so_luong_da_su_dung }}/{{ $voucher->so_luong }}
                    </small>
                </td>
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
                <td>
                    <div class="voucher-time-remaining">
                        <div><strong>Từ:</strong> {{ $voucher->ngay_bat_dau ? $voucher->ngay_bat_dau->format('d/m/Y H:i') : 'N/A' }}</div>
                        <div><strong>Đến:</strong> {{ $voucher->ngay_ket_thuc ? $voucher->ngay_ket_thuc->format('d/m/Y H:i') : 'N/A' }}</div>
                        @if($voucher->ngay_ket_thuc)
                            @php
                                $daysLeft = now()->diffInDays($voucher->ngay_ket_thuc, false);
                            @endphp
                            @if($daysLeft > 0)
                                <small class="text-success">{{ $daysLeft }} ngày còn lại</small>
                            @elseif($daysLeft == 0)
                                <small class="text-warning">Hết hạn hôm nay</small>
                            @else
                                <small class="text-danger">Đã hết hạn {{ abs($daysLeft) }} ngày</small>
                            @endif
                        @else
                            <small class="text-muted">N/A</small>
                        @endif
                    </div>
                </td>
                <td class="voucher-actions">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.vouchers.show', $voucher->id) }}" 
                           class="btn btn-info btn-sm" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" 
                           class="btn btn-warning btn-sm" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('admin.vouchers.toggle-status', $voucher->id) }}" 
                           class="btn btn-sm toggle-status {{ $voucher->trang_thai ? 'btn-deactivate' : 'btn-activate' }}" 
                           title="{{ $voucher->trang_thai ? 'Tạm dừng' : 'Kích hoạt' }}"
                           data-id="{{ $voucher->id }}">
                            <i class="fas fa-{{ $voucher->trang_thai ? 'pause' : 'play' }}"></i>
                        </a>
                        <a href="{{ route('admin.vouchers.destroy', $voucher->id) }}" 
                           class="btn btn-danger btn-sm delete-voucher" title="Xóa">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                        <p>Không có voucher nào</p>
                        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tạo voucher đầu tiên
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
/* Voucher Table Styling */
#dataTable {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border: none;
}

#dataTable thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.8px;
    padding: 18px 15px;
    text-align: center;
    position: relative;
}

#dataTable thead th:first-child {
    border-top-left-radius: 12px;
}

#dataTable thead th:last-child {
    border-top-right-radius: 12px;
}

#dataTable tbody td {
    vertical-align: middle;
    padding: 18px 15px;
    border-bottom: 1px solid #f0f2f5;
    transition: all 0.3s ease;
    background: white;
}

#dataTable tbody tr:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}

#dataTable tbody tr:last-child td:first-child {
    border-bottom-left-radius: 12px;
}

#dataTable tbody tr:last-child td:last-child {
    border-bottom-right-radius: 12px;
}

/* Enhanced Badge Styles */
.badge {
    font-size: 11px;
    padding: 8px 14px;
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}

.badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.badge-warning {
    background: linear-gradient(45deg, #ff9a56, #ffad56);
    color: white;
}

.badge-info {
    background: linear-gradient(45deg, #4facfe, #00f2fe);
    color: white;
}

.badge-primary {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
}

.badge-success {
    background: linear-gradient(45deg, #56ab2f, #a8e6cf);
    color: white;
}

.badge-danger {
    background: linear-gradient(45deg, #ff416c, #ff4b2b);
    color: white;
}

.badge-secondary {
    background: linear-gradient(45deg, #bdc3c7, #2c3e50);
    color: white;
}

/* Action Buttons */
.btn-group .btn {
    margin-right: 4px;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: none;
    padding: 8px 12px;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.btn-info {
    background: linear-gradient(45deg, #17a2b8, #20c997);
    color: white;
}

.btn-warning {
    background: linear-gradient(45deg, #ffc107, #ff8c00);
    color: white;
}

.btn-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
}

.btn-secondary {
    background: linear-gradient(45deg, #6c757d, #495057);
    color: white;
}

.btn-danger {
    background: linear-gradient(45deg, #dc3545, #c82333);
    color: white;
}

/* Progress Bar Enhancement */
.progress {
    height: 10px;
    border-radius: 15px;
    background: linear-gradient(90deg, #e9ecef, #f8f9fa);
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.progress-bar {
    border-radius: 15px;
    transition: width 0.8s ease;
    position: relative;
    overflow: hidden;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Voucher Code Styling */
.voucher-code {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    font-size: 13px;
}

/* Time Remaining */
.time-remaining {
    font-size: 12px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 12px;
    display: inline-block;
}

.time-remaining.text-success {
    background: linear-gradient(45deg, #d4edda, #c3e6cb);
    color: #155724 !important;
}

.time-remaining.text-warning {
    background: linear-gradient(45deg, #fff3cd, #ffeaa7);
    color: #856404 !important;
}

.time-remaining.text-danger {
    background: linear-gradient(45deg, #f8d7da, #f5c6cb);
    color: #721c24 !important;
}

/* Empty State */
.text-muted i.fa-ticket-alt {
    color: #dee2e6;
    margin-bottom: 15px;
}

/* Responsive Design */
@media (max-width: 768px) {
    #dataTable {
        font-size: 12px;
    }
    
    #dataTable th,
    #dataTable td {
        padding: 12px 8px;
    }
    
    .badge {
        font-size: 10px;
        padding: 6px 10px;
    }
    
    .btn-group .btn {
        padding: 6px 8px;
        font-size: 11px;
    }
}

/* Loading Animation */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.loading {
    animation: pulse 1.5s infinite;
}

/* Hover Effects for Interactive Elements */
.voucher-row {
    cursor: pointer;
    transition: all 0.3s ease;
}

.voucher-row:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
}

/* Status Icons */
.status-icon {
    margin-right: 6px;
    font-size: 14px;
}

/* Custom Scrollbar for Table */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: linear-gradient(45deg, #667eea, #764ba2);
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(45deg, #5a6fd8, #6a4190);
}
</style>
