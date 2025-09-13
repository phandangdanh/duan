@extends('backend.layout')

@section('title', 'Chỉnh sửa Voucher')

@section('content')
<div class="container-fluid voucher-edit-page">
    <!-- Breadcrumb -->
    @include('backend.component.breadcrum', [
        'title' => 'Chỉnh sửa Voucher',
        'items' => [
            ['text' => 'Dashboard', 'url' => route('dashboard.index')],
            ['text' => 'Voucher', 'url' => route('admin.vouchers.index')],
            ['text' => 'Chỉnh sửa', 'active' => true]
        ]
    ])

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle"></i> 
                Có lỗi xảy ra khi cập nhật voucher!
            </h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-times-circle"></i> {{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin Voucher</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST" id="voucher-form">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Mã voucher -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ma_voucher">Mã Voucher <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('ma_voucher') is-invalid @enderror" 
                                       id="ma_voucher" name="ma_voucher" value="{{ old('ma_voucher', $voucher->ma_voucher) }}" 
                                       placeholder="VD: GIAM10" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="generate-code">
                                        <i class="fas fa-magic"></i> Tự động
                                    </button>
                                </div>
                            </div>
                            @error('ma_voucher')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Chỉ sử dụng chữ hoa, số và dấu gạch dưới</small>
                        </div>
                    </div>

                    <!-- Tên voucher -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ten_voucher">Tên Voucher <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten_voucher') is-invalid @enderror" 
                                   id="ten_voucher" name="ten_voucher" value="{{ old('ten_voucher', $voucher->ten_voucher) }}" 
                                   placeholder="VD: Giảm 10%" required>
                            @error('ten_voucher')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Mô tả -->
                <div class="form-group">
                    <label for="mota">Mô tả</label>
                    <textarea class="form-control @error('mota') is-invalid @enderror" 
                              id="mota" name="mota" rows="3" 
                              placeholder="Mô tả chi tiết về voucher">{{ old('mota', $voucher->mota) }}</textarea>
                    @error('mota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <!-- Loại giảm giá -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="loai_giam_gia">Loại Giảm Giá <span class="text-danger">*</span></label>
                            <select class="form-control @error('loai_giam_gia') is-invalid @enderror" 
                                    id="loai_giam_gia" name="loai_giam_gia" required>
                                <option value="">Chọn loại giảm giá</option>
                                <option value="phan_tram" {{ old('loai_giam_gia', $voucher->loai_giam_gia) == 'phan_tram' ? 'selected' : '' }}>Phần trăm (%)</option>
                                <option value="tien_mat" {{ old('loai_giam_gia', $voucher->loai_giam_gia) == 'tien_mat' ? 'selected' : '' }}>Tiền mặt (VNĐ)</option>
                            </select>
                            @error('loai_giam_gia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Giá trị giảm giá -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gia_tri">Giá Trị Giảm Giá <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('gia_tri') is-invalid @enderror" 
                                       id="gia_tri" name="gia_tri" value="{{ old('gia_tri', $voucher->gia_tri) }}" 
                                       step="0.01" min="0" 
                                       placeholder="Nhập giá trị giảm giá" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="gia_tri_unit">{{ $voucher->loai_giam_gia === 'phan_tram' ? '%' : 'VNĐ' }}</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Tối đa: 999,999,999,999.99
                            </small>
                            @error('gia_tri')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Giá trị tối thiểu -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gia_tri_toi_thieu">Giá Trị Đơn Hàng Tối Thiểu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('gia_tri_toi_thieu') is-invalid @enderror" 
                                       id="gia_tri_toi_thieu" name="gia_tri_toi_thieu" 
                                       value="{{ old('gia_tri_toi_thieu', $voucher->gia_tri_toi_thieu) }}" step="0.01" min="0" 
                                       placeholder="Ví dụ: 50000.50" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Có thể nhập số lẻ (ví dụ: 50000.50)
                            </small>
                            @error('gia_tri_toi_thieu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Giá trị tối đa -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gia_tri_toi_da">Giá Trị Giảm Tối Đa</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('gia_tri_toi_da') is-invalid @enderror" 
                                       id="gia_tri_toi_da" name="gia_tri_toi_da" 
                                       value="{{ old('gia_tri_toi_da', $voucher->gia_tri_toi_da) }}" step="0.01" min="0" 
                                       placeholder="Ví dụ: 100000.99">
                                <div class="input-group-append">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Có thể nhập số lẻ (ví dụ: 100000.99)
                            </small>
                            @error('gia_tri_toi_da')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Chỉ áp dụng cho loại giảm giá phần trăm</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Số lượng -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="so_luong">Số Lượng Voucher <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('so_luong') is-invalid @enderror" 
                                   id="so_luong" name="so_luong" value="{{ old('so_luong', $voucher->so_luong) }}" 
                                   min="{{ $voucher->so_luong_da_su_dung }}" required>
                            @error('so_luong')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Đã sử dụng: {{ $voucher->so_luong_da_su_dung }}</small>
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="trang_thai">Trạng Thái</label>
                            <select class="form-control @error('trang_thai') is-invalid @enderror" 
                                    id="trang_thai" name="trang_thai">
                                <option value="1" {{ old('trang_thai', $voucher->trang_thai) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ old('trang_thai', $voucher->trang_thai) == 0 ? 'selected' : '' }}>Tạm dừng</option>
                            </select>
                            @error('trang_thai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Ngày bắt đầu -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ngay_bat_dau">Ngày Bắt Đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('ngay_bat_dau') is-invalid @enderror" 
                                   id="ngay_bat_dau" name="ngay_bat_dau" 
                                   value="{{ old('ngay_bat_dau', $voucher->ngay_bat_dau ? $voucher->ngay_bat_dau->format('Y-m-d\TH:i') : '') }}" required>
                            @error('ngay_bat_dau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Ngày kết thúc -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ngay_ket_thuc">Ngày Kết Thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('ngay_ket_thuc') is-invalid @enderror" 
                                   id="ngay_ket_thuc" name="ngay_ket_thuc" 
                                   value="{{ old('ngay_ket_thuc', $voucher->ngay_ket_thuc ? $voucher->ngay_ket_thuc->format('Y-m-d\TH:i') : '') }}" required>
                            @error('ngay_ket_thuc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Thông tin sử dụng -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-chart-pie"></i> Thông Tin Sử Dụng
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="usage-info">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="usage-item">
                                        <span class="usage-label">
                                            <i class="fas fa-boxes text-primary"></i> Tổng số lượng:
                                        </span>
                                        <span class="usage-value">{{ $voucher->so_luong }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="usage-item">
                                        <span class="usage-label">
                                            <i class="fas fa-check-circle text-success"></i> Đã sử dụng:
                                        </span>
                                        <span class="usage-value">{{ $voucher->so_luong_da_su_dung }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="usage-item">
                                        <span class="usage-label">
                                            <i class="fas fa-clock text-warning"></i> Còn lại:
                                        </span>
                                        <span class="usage-value">{{ $voucher->so_luong_con_lai }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="usage-item">
                                        <span class="usage-label">
                                            <i class="fas fa-percentage text-info"></i> Tỷ lệ sử dụng:
                                        </span>
                                        <span class="usage-value">{{ round(($voucher->so_luong_da_su_dung / $voucher->so_luong) * 100, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="progress mt-3">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $voucher->so_luong > 0 ? ($voucher->so_luong_da_su_dung / $voucher->so_luong) * 100 : 0 }}%"
                                     aria-valuenow="{{ $voucher->so_luong_da_su_dung }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="{{ $voucher->so_luong }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="form-group text-right mt-4">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật Voucher
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto refresh CSRF token every 30 minutes
setInterval(function() {
    fetch('/admin/vouchers/refresh-csrf', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.csrf_token) {
            // Update all CSRF tokens in forms
            document.querySelectorAll('input[name="_token"]').forEach(input => {
                input.value = data.csrf_token;
            });
            // Update meta tag
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
        }
    })
    .catch(error => {
        console.log('CSRF refresh failed:', error);
    });
}, 30 * 60 * 1000); // 30 minutes

// Handle form submission with error handling
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Add loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật voucher...';
            }
        });
    }
});
</script>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Generate code
    $('#generate-code').click(function() {
        const prefix = $('#ma_voucher').val() || 'VOUCHER';
        $.post('{{ route("admin.vouchers.generate-code") }}', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            prefix: prefix
        })
        .done(function(response) {
            if (response.success) {
                $('#ma_voucher').val(response.code);
            }
        });
    });

    // Check code uniqueness
    $('#ma_voucher').blur(function() {
        const code = $(this).val();
        if (code && code !== '{{ $voucher->ma_voucher }}') {
            $.post('{{ route("admin.vouchers.check-code") }}', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                code: code
            })
            .done(function(response) {
                if (response.exists) {
                    $('#ma_voucher').addClass('is-invalid');
                    $('#ma_voucher').after('<div class="invalid-feedback">' + response.message + '</div>');
                } else {
                    $('#ma_voucher').removeClass('is-invalid');
                    $('#ma_voucher').next('.invalid-feedback').remove();
                }
            });
        }
    });

    // Change unit based on discount type
    $('#loai_giam_gia').change(function() {
        const unit = $(this).val() === 'phan_tram' ? '%' : 'VNĐ';
        $('#gia_tri_unit').text(unit);
        
        if ($(this).val() === 'phan_tram') {
            $('#gia_tri').attr('max', 100);
        } else {
            $('#gia_tri').removeAttr('max');
        }
    });

    // Form validation
    $('#voucher-form').submit(function(e) {
        const ngayBatDau = new Date($('#ngay_bat_dau').val());
        const ngayKetThuc = new Date($('#ngay_ket_thuc').val());
        
        if (ngayKetThuc <= ngayBatDau) {
            e.preventDefault();
            alert('Ngày kết thúc phải sau ngày bắt đầu!');
            return false;
        }
        
        const loai = $('#loai_giam_gia').val();
        const giaTri = parseFloat($('#gia_tri').val());
        
        if (loai === 'phan_tram' && giaTri > 100) {
            e.preventDefault();
            alert('Giá trị giảm giá theo phần trăm không được vượt quá 100%!');
            return false;
        }
    });
});
</script>
@endpush
