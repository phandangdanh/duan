@extends('backend.layout')

@section('title', 'Tạo Voucher Mới')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    @include('backend.component.breadcrum', [
        'title' => 'Tạo Voucher Mới',
        'items' => [
            ['text' => 'Dashboard', 'url' => route('dashboard.index')],
            ['text' => 'Voucher', 'url' => route('admin.vouchers.index')],
            ['text' => 'Tạo mới', 'active' => true]
        ]
    ])

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle"></i> 
                Có lỗi xảy ra khi tạo voucher!
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
            <form action="{{ route('admin.vouchers.store') }}" method="POST" id="voucher-form">
                @csrf
                
                <div class="row">
                    <!-- Mã voucher -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ma_voucher">Mã Voucher <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('ma_voucher') is-invalid @enderror" 
                                       id="ma_voucher" name="ma_voucher" value="{{ old('ma_voucher') }}" 
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
                                   id="ten_voucher" name="ten_voucher" value="{{ old('ten_voucher') }}" 
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
                              placeholder="Mô tả chi tiết về voucher">{{ old('mota') }}</textarea>
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
                                <option value="phan_tram" {{ old('loai_giam_gia') == 'phan_tram' ? 'selected' : '' }}>Phần trăm (%)</option>
                                <option value="tien_mat" {{ old('loai_giam_gia') == 'tien_mat' ? 'selected' : '' }}>Tiền mặt (VNĐ)</option>
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
                                       id="gia_tri" name="gia_tri" value="{{ old('gia_tri') }}" 
                                       step="0.01" min="0" 
                                       placeholder="Nhập giá trị giảm giá" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="gia_tri_unit">%</span>
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
                                       value="{{ old('gia_tri_toi_thieu', 0) }}" step="0.01" min="0" 
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
                                       value="{{ old('gia_tri_toi_da') }}" step="0.01" min="0" 
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
                                   id="so_luong" name="so_luong" value="{{ old('so_luong', 1) }}" 
                                   min="1" required>
                            @error('so_luong')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="trang_thai">Trạng Thái</label>
                            <select class="form-control @error('trang_thai') is-invalid @enderror" 
                                    id="trang_thai" name="trang_thai">
                                <option value="1" {{ old('trang_thai', 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ old('trang_thai') == 0 ? 'selected' : '' }}>Tạm dừng</option>
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
                                   value="{{ old('ngay_bat_dau', now()->format('Y-m-d\TH:i')) }}" required>
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
                                   value="{{ old('ngay_ket_thuc', now()->addDays(30)->format('Y-m-d\TH:i')) }}" required>
                            @error('ngay_ket_thuc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="card mt-4" id="preview-card" style="display: none;">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-info">Xem Trước Voucher</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Mã:</strong> <span id="preview-ma"></span></p>
                                <p><strong>Tên:</strong> <span id="preview-ten"></span></p>
                                <p><strong>Loại:</strong> <span id="preview-loai"></span></p>
                                <p><strong>Giá trị:</strong> <span id="preview-gia-tri"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tối thiểu:</strong> <span id="preview-toi-thieu"></span></p>
                                <p><strong>Tối đa:</strong> <span id="preview-toi-da"></span></p>
                                <p><strong>Số lượng:</strong> <span id="preview-so-luong"></span></p>
                                <p><strong>Thời gian:</strong> <span id="preview-thoi-gian"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="form-group text-right">
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="button" class="btn btn-info" id="preview-btn">
                        <i class="fas fa-eye"></i> Xem trước
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Tạo Voucher
                    </button>
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
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tạo voucher...';
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
/* Voucher Create Form Styling */
.card {
    border-radius: 20px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 25px 30px;
}

.card-header h6 {
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.card-body {
    padding: 30px;
    background: #fafbfc;
}

.form-group label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-control {
    border-radius: 12px;
    border: 2px solid #e9ecef;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
    height: 45px;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    transform: translateY(-1px);
}

.input-group-append .btn {
    border-radius: 0 12px 12px 0;
    border-left: none;
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    border: 2px solid #e9ecef;
    border-left: none;
    transition: all 0.3s ease;
}

.input-group-append .btn:hover {
    background: linear-gradient(45deg, #5a6fd8, #6a4190);
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    border-radius: 12px;
    padding: 12px 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.btn-secondary {
    background: linear-gradient(45deg, #6c757d, #495057);
    border: none;
    border-radius: 12px;
    padding: 12px 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
}

/* Preview Card */
.preview-card {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
    border: 2px dashed #667eea;
    border-radius: 15px;
    padding: 25px;
    margin-top: 20px;
    transition: all 0.3s ease;
}

.preview-card:hover {
    border-color: #5a6fd8;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}

.preview-voucher-code {
    font-family: 'Courier New', monospace;
    font-size: 1.5rem;
    font-weight: bold;
    color: #667eea;
    text-align: center;
    margin-bottom: 15px;
    padding: 10px;
    background: white;
    border-radius: 8px;
    border: 2px solid #e9ecef;
}

.preview-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.preview-item {
    background: white;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.preview-item strong {
    color: #2c3e50;
    display: block;
    margin-bottom: 5px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.preview-item span {
    color: #6c757d;
    font-size: 14px;
}

/* Form Validation */
.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.invalid-feedback {
    color: #dc3545;
    font-size: 12px;
    margin-top: 5px;
    font-weight: 500;
}

/* Loading States */
.btn.loading {
    position: relative;
    color: transparent;
}

.btn.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid transparent;
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .card-body {
        padding: 20px;
    }
    
    .preview-details {
        grid-template-columns: 1fr;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 10px;
    }
}

/* Animation for form elements */
.form-group {
    animation: fadeInUp 0.6s ease-out;
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
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
        if (code) {
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

    // Preview voucher
    $('#preview-btn').click(function() {
        updatePreview();
        $('#preview-card').show();
    });

    function updatePreview() {
        $('#preview-ma').text($('#ma_voucher').val() || 'Chưa nhập');
        $('#preview-ten').text($('#ten_voucher').val() || 'Chưa nhập');
        
        const loai = $('#loai_giam_gia').val();
        $('#preview-loai').text(loai === 'phan_tram' ? 'Phần trăm' : 'Tiền mặt');
        
        const giaTri = $('#gia_tri').val();
        const donVi = loai === 'phan_tram' ? '%' : 'VNĐ';
        $('#preview-gia-tri').text(giaTri ? giaTri + ' ' + donVi : 'Chưa nhập');
        
        $('#preview-toi-thieu').text($('#gia_tri_toi_thieu').val() ? 
            parseInt($('#gia_tri_toi_thieu').val()).toLocaleString() + ' VNĐ' : 'Chưa nhập');
        
        $('#preview-toi-da').text($('#gia_tri_toi_da').val() ? 
            parseInt($('#gia_tri_toi_da').val()).toLocaleString() + ' VNĐ' : 'Không giới hạn');
        
        $('#preview-so-luong').text($('#so_luong').val() || 'Chưa nhập');
        
        const ngayBatDau = $('#ngay_bat_dau').val();
        const ngayKetThuc = $('#ngay_ket_thuc').val();
        if (ngayBatDau && ngayKetThuc) {
            const start = new Date(ngayBatDau).toLocaleDateString('vi-VN');
            const end = new Date(ngayKetThuc).toLocaleDateString('vi-VN');
            $('#preview-thoi-gian').text(start + ' - ' + end);
        } else {
            $('#preview-thoi-gian').text('Chưa nhập');
        }
    }

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
