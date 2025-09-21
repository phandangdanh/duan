{{-- resources\views\backend\sanpham\create.blade.php --}}
@extends('backend.layout')
@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="container-fluid">
    <h1 class="ctsph1">Thêm sản phẩm</h1>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('sanpham.store') }}" method="POST" enctype="multipart/form-data" id="create-product-form">
                @csrf
                <div id="form-messages"></div>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Đã có lỗi xảy ra:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tên sản phẩm -->
                <div class="margin-bottom-4" style="margin: 20px 0px;">
                    <label for="tenSP" class="form-label"><span style="font-weight: bold;">Tên sản phẩm</span><span class="text-danger"> * </span></label>
                    <input type="text" name="tenSP" id="tenSP"
                           class="form-control @error('tenSP') is-invalid @enderror"
                           placeholder="Nhập tên sản phẩm..."
                           value="{{ old('tenSP') }}" required>
                    @error('tenSP')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mã sản phẩm -->
                <div class="margin-bottom-4" style="margin: 20px 0px;">
                    <label for="maSP" class="form-label">Mã sản phẩm</label>
                    <input type="text" name="maSP" id="maSP" class="form-control @error('maSP') is-invalid @enderror" value="{{ old('maSP') }}">
                    @error('maSP')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Danh mục -->
                <div class="margin-bottom-4" style="margin: 20px 0px;">
                    <label for="id_danhmuc" class="form-label" style="font-weight: bold;">Danh mục</label>
                    <select style="height: 40px;" name="id_danhmuc" id="id_danhmuc" class="form-select @error('id_danhmuc') is-invalid @enderror">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($danhmucs as $dm)
                            <option value="{{ $dm->id }}" {{ old('id_danhmuc') == $dm->id ? 'selected' : '' }}>
                                {{ $dm->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_danhmuc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mô tả -->
                <div class="margin-bottom-4" style="margin: 20px 0px;">
                    <label for="moTa" class="form-label" style="font-weight: bold;">Mô tả chi tiết</label>
                    <textarea name="moTa" id="moTa" class="form-control @error('moTa') is-invalid @enderror" rows="5" placeholder="Mô tả sản phẩm...">{{ old('moTa') }}</textarea>
                    @error('moTa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Giá chung (áp dụng mặc định cho biến thể nếu không nhập) -->
                {{-- <div class="card margin-bottom-4">
                    <div class="card-header" style="margin: 20px 0px;">
                        <h1 class="ctsph1">Giá sản phẩm</h1>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold"><i class="fa fa-money-bill me-1"></i>Giá gốc (sản phẩm chính)</label>
                                <div class="input-group">
                                    <input type="text" name="base_price" class="form-control" placeholder="0" value="{{ old('base_price') }}">
                                    <span class="input-group-text">VND</span>
                                </div>
                                <small class="form-text text-muted">Biến thể không nhập giá sẽ dùng giá này</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold"><i class="fa fa-tags me-1"></i>Giá khuyến mãi (sản phẩm chính)</label>
                                <div class="input-group">
                                    <input type="text" name="base_sale_price" class="form-control" placeholder="0" value="{{ old('base_sale_price') }}">
                                    <span class="input-group-text">VND</span>
                                </div>
                                <small class="form-text text-muted">Để trống nếu không có khuyến mãi</small>
                            </div>
                            
                        </div>
                    </div>
                </div> --}}

                <!-- Giá sản phẩm chính -->
                <div class="card margin-bottom-4">
                    <div class="card-header" style="margin: 20px 0px;">
                        <h1 class="ctsph1">Giá sản phẩm</h1>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 margin-bottom-4" style="margin: 20px 0px;">
                                <label for="soLuong" class="form-label">
                                    <span style="font-weight: bold;">Số lượng sản phẩm chính</span>
                                </label>
                                <input type="number" name="soLuong" id="soLuong" 
                                    class="form-control @error('soLuong') is-invalid @enderror" 
                                    placeholder="1" value="{{ old('soLuong', 1) }}" min="0">
                                <small class="form-text text-muted">Số lượng tồn kho chính (không tính variant)</small>
                                @error('soLuong')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 margin-bottom-4" style="margin: 20px 0px;">
                                <label for="base_price" class="form-label">
                                    <span style="font-weight: bold;">Giá gốc (sản phẩm chính)</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" name="base_price" id="base_price" 
                                        class="form-control @error('base_price') is-invalid @enderror" 
                                        placeholder="0" value="{{ old('base_price') }}">
                                    <span class="input-group-text">VND</span>
                                </div>
                                <small class="form-text text-muted">Biến thể không lập giá sẽ dùng giá này</small>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 margin-bottom-4" style="margin: 20px 0px;">
                                <label for="base_sale_price" class="form-label">
                                    <span style="font-weight: bold;">Giá khuyến mãi (sản phẩm chính)</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" name="base_sale_price" id="base_sale_price" 
                                        class="form-control @error('base_sale_price') is-invalid @enderror" 
                                        placeholder="0" value="{{ old('base_sale_price') }}">
                                    <span class="input-group-text">VND</span>
                                </div>
                                <small class="form-text text-muted">Để trống nếu không có khuyến mãi</small>
                                @error('base_sale_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Hình ảnh sản phẩm -->
                <div class="card margin-bottom-4">
                    <div class="card-header" style="margin: 20px 0px;">
                        <h1 class="ctsph1">Hình ảnh sản phẩm</h1>
                        <div class="alert alert-info mb-0">
                            <i class="fa fa-info-circle me-2"></i>
                            <strong>Hướng dẫn:</strong> Tải lên ít nhất 1 hình ảnh chính và có thể thêm nhiều hình ảnh phụ.
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Hình ảnh chính -->
                        <div class="margin-bottom-4" style="margin: 20px 0px;">
                            <label for="image_main" class="form-label" style="font-weight: bold;">
                                Hình ảnh chính <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="image_main" id="image_main" 
                                   class="form-control @error('image_main') is-invalid @enderror" 
                                   accept="image/*" required>
                            <input type="hidden" name="temp_main_key" id="temp_main_key" value="{{ old('temp_main_key') }}">
                            <small class="form-text text-muted">Chọn hình ảnh chính cho sản phẩm (JPG, PNG, GIF)</small>
                            @error('image_main')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="image_main_preview" class="mt-2" style="display: none;">
                                <img id="image_main_preview_img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        </div>

                        <!-- Hình ảnh phụ -->
                        <div class="margin-bottom-4" style="margin: 20px 0px;">
                            <label for="image_extra" class="form-label" style="font-weight: bold;">
                                Hình ảnh phụ
                            </label>
                            <input type="file" name="image_extra[]" id="image_extra" 
                                   class="form-control @error('image_extra') is-invalid @enderror" 
                                   accept="image/*" multiple>
                            <input type="hidden" name="image_extra_uploaded" value="0">
                            @php $oldTempKeys = old('temp_extra_keys'); @endphp
                            @if(is_array($oldTempKeys) && count($oldTempKeys))
                                @foreach($oldTempKeys as $k)
                                    <input type="hidden" name="temp_extra_keys[]" value="{{ $k }}">
                                @endforeach
                            @endif
                            <small class="form-text text-muted">Chọn nhiều hình ảnh phụ (có thể chọn nhiều file cùng lúc)</small>
                            @error('image_extra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="image_extra_preview" class="mt-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Chi tiết sản phẩm -->
                <div class="card margin-bottom-4">
                    <div class="card-header" style="margin: 20px 0px;">
                        <h1 class="ctsph1">Chi tiết sản phẩm</h1>
                        <div class="alert alert-info mb-0">
                            <i class="fa fa-info-circle me-2"></i>
                            <strong>Hướng dẫn:</strong> Biến thể là tùy chọn. Bạn có thể tạo sản phẩm chỉ với giá chính, hoặc thêm biến thể với màu sắc và size khác nhau.
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="variants-container">
                            <!-- Biến thể đầu tiên -->
                            <div class="variant-item mb-4" data-variant-index="0">
                                <div class="variant-header">
                                    <div class="variant-title">
                                        <h5 class="mb-0">
                                            <i class="fa fa-palette me-2"></i>
                                            Biến thể #1
                                        </h5>
                                        <small class="text-muted">Chọn màu sắc và thêm các size</small>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-variant" data-action="remove-variant">
                                        <i class="fa fa-trash me-1"></i>Xóa biến thể
                                    </button>
                                </div>
                                
                                <div class="variant-content">
                                    <div class="row">
                                        <!-- Màu sắc -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="fa fa-palette me-1"></i>Màu sắc <span class="text-danger">*</span>
                                            </label>
                                    <div class="color-select-container">
                                                <select style="height: 40px;" name="variants[0][mausac]" class="form-select color-select">
                                            <option value="">-- Chọn màu sắc --</option>
                                            @foreach($mausacs as $mau)
                                                <option value="{{ $mau->id }}" data-color-name="{{ $mau->name }}" {{ old('variants.0.mausac') == $mau->id ? 'selected' : '' }}>{{ $mau->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="color-preview"></div>
                                    </div>
                                            <small class="form-text text-muted">Chọn màu chính của biến thể này</small>
                                        </div>
                                    </div>

                                    <!-- Bảng size và thông tin -->
                                    <div class="size-variants-container">
                                        <div class="size-header">
                                            <h6 class="mb-3">
                                                <i class="fa fa-ruler me-2"></i>
                                                Danh sách size và thông tin
                                            </h6>
                                            <p class="text-muted mb-3">
                                                <i class="fa fa-lightbulb me-1"></i>
                                                Mỗi size sẽ có số lượng, giá gốc và giá khuyến mãi riêng biệt
                                            </p>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-bordered size-variants-table">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th width="20%">
                                                            <i class="fa fa-ruler me-1"></i>Size
                                                        </th>
                                                        <th width="20%">
                                                            <i class="fa fa-boxes me-1"></i>Số lượng
                                                        </th>
                                                        <th width="25%">
                                                            <i class="fa fa-money-bill-wave me-1"></i>Giá gốc
                                                        </th>
                                                        <th width="25%">
                                                            <i class="fa fa-tags me-1"></i>Giá khuyến mãi
                                                        </th>
                                                        <th width="10%">
                                                            <i class="fa fa-cogs me-1"></i>Thao tác
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="size-variant-row">
                                <td>
                                    <div class="size-select-container">
                                                                <select style="height: 40px;" name="variants[0][sizes][0][size]" class="form-select size-select">
                                            <option value="">-- Chọn size --</option>
                                            @foreach($sizes as $size)
                                                <option value="{{ $size->id }}" data-size-name="{{ $size->name }}" {{ old('variants.0.sizes.0.size') == $size->id ? 'selected' : '' }}>{{ $size->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="size-preview"></div>
                                    </div>
                                </td>
                                                        <td>
                                                            <input type="number" name="variants[0][sizes][0][so_luong]" 
                                                                   class="form-control" value="{{ old('variants.0.sizes.0.so_luong', 1) }}" min="0">
                                                            <small class="form-text text-muted">Số lượng tồn kho</small>
                                                        </td>
                                                        <td>
                                                                                                                         <div class="input-group">
                                                                 <input type="text" name="variants[0][sizes][0][gia]" 
                                                                        class="form-control price-input" placeholder="0" value="{{ old('variants.0.sizes.0.gia') }}">
                                                                 <span class="input-group-text">VND</span>
                                                             </div>
                                                            <small class="form-text text-muted">Bỏ trống để dùng giá sản phẩm chính</small>
                                                        </td>
                                                        <td>
                                                                                                                         <div class="input-group">
                                                                 <input type="text" name="variants[0][sizes][0][gia_khuyenmai]" 
                                                                        class="form-control price-input" placeholder="0" value="{{ old('variants.0.sizes.0.gia_khuyenmai') }}">
                                                                 <span class="input-group-text">VND</span>
                                                             </div>
                                                            <small class="form-text text-muted">Để trống nếu không có KM</small>
                                                        </td>
                                                        <td class="text-center">
                                                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-size-row" 
                                    title="Xóa size này" data-action="remove-size-row">
                                <i class="fa fa-times"></i>
                            </button>
                                                        </td>
                            </tr>
                        </tbody>
                    </table>
                                        </div>
                                        
                                        <div class="size-actions">
                                            <button type="button" class="btn btn-info btn-sm add-size-row" data-variant-index="0" data-action="add-size-row">
                                                <i class="fa fa-plus me-1"></i>Thêm size mới
                                            </button>
                                            <small class="form-text text-muted ms-2">
                                                Biến thể có thể không có size (tùy chọn)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="add-variant-section">
                            <button type="button" id="add-variant" class="btn btn-success btn-lg" data-action="add-variant">
                                <i class="fa fa-plus me-2"></i>Thêm biến thể mới
                            </button>
                            <p class="text-muted mt-2">
                                <i class="fa fa-info-circle me-1"></i>
                                Thêm biến thể mới để tạo sản phẩm với nhiều màu sắc khác nhau
                            </p>
                        </div>
                    </div>
                </div>



               
                <!-- Trạng thái sản phẩm -->
                <div class="card margin-bottom-4">
                    <div class="card-header" style="margin: 20px 0px;">
                        <h1 class="ctsph1">Thông tin hiển thị</h1>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 margin-bottom-4" style="margin: 26px 0px;">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select style="height: 40px;" name="status" id="status" class="form-select">
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Kinh doanh</option>
                                    <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Không kinh doanh</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="d-flex justify-content-end" style="margin: 30px 0px 60px 0px;">
                    <a href="{{ route('sanpham.index') }}" class="btn btn-secondary me-2">Quay lại</a>
                    <button type="submit" class="btn btn-success">Lưu sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Kiểm tra jQuery
    if (typeof $ === 'undefined') {
        return;
    }
    
    // Khôi phục variants data từ old() values nếu có lỗi validation
    @if(old('variants'))
        // Trigger change events để khôi phục preview màu sắc
        setTimeout(function() {
            $('.color-select').each(function() {
                $(this).trigger('change');
            });
            // Khôi phục event handlers sau khi load old data
            restoreEventHandlers();
        }, 100);
    @endif
    
    // Khôi phục event handlers cho tất cả trường hợp (không chỉ khi có old data)
    setTimeout(function() {
        restoreEventHandlers();
    }, 1000);
    
    // Dọn error styling nhẹ nhàng (không xóa thông báo lỗi server)
    @if(old('image_main_name') || old('image_extra_names'))
        setTimeout(function() {
            $('#image_main, #image_extra').removeClass('is-invalid');
            $('#image_main, #image_extra').siblings('.invalid-feedback').remove();
        }, 100);
    @endif
    
    // Khôi phục thông tin ảnh từ old data
    let restoredMainRendered = false;
    let restoredExtraRendered = false;
    @if(old('image_main_name'))
        
        // Xóa error message ngay lập tức
        setTimeout(function() {
            $('#image_main').removeClass('is-invalid');
            $('#image_main').siblings('.invalid-feedback').remove();
        }, 50);
        
        // Xóa các block đã render trước đó để tránh lặp
        $('.restored-main-block, .restored-main-preview').remove();
        // Hiển thị thông báo
        $('#image_main').after('<div class="alert alert-info mt-2 restored-main-block"><i class="fa fa-info-circle me-2"></i>Ảnh chính đã chọn: <strong>{{ old("image_main_name") }}</strong></div>');
        
        // Ẩn block lớn, chỉ hiển thị dòng thông tin nhỏ ở trên
        
        // Tạo fake file input để bypass validation
        const fakeFileInput = document.createElement('input');
        fakeFileInput.type = 'file';
        fakeFileInput.name = 'image_main_fake';
        fakeFileInput.id = 'image_main_fake';
        fakeFileInput.style.display = 'none';
        fakeFileInput.setAttribute('data-fake', 'true');
        document.getElementById('create-product-form').appendChild(fakeFileInput);
        
        // Giữ input thật hiển thị để có thể chọn lại trực tiếp
        $('#image_main_fake').show();
        
        // Xóa required và name để form không gửi file rỗng
        $('#image_main').removeAttr('required');
        $('#image_main').attr('data-original-name', 'image_main');
        $('#image_main').removeAttr('name');
        
        // Xóa error message nếu có
        $('#image_main').removeClass('is-invalid');
        $('#image_main').siblings('.invalid-feedback').remove();
        
        // Tạo fake file object để giả lập file đã chọn
        const fakeFile = new File([''], '{{ old("image_main_name") }}', { type: 'image/png' });
        const dataTransferMain = new DataTransfer();
        dataTransferMain.items.add(fakeFile);
        fakeFileInput.files = dataTransferMain.files;
        
        // Không render preview lớn khi chỉ khôi phục tên file
        
        // Không copy từ fake sang thật; chỉ khi user chọn lại mới phục hồi name
        
        // Khôi phục event handlers sau khi load old data
        setTimeout(function() { restoreEventHandlers(); }, 500);
    @endif
    
    // Function khôi phục event handlers
    function restoreEventHandlers() {
        
        // Khôi phục event handlers cho variant/size management
        $(document).off('click', '.remove-variant').on('click', '.remove-variant', function() {
            $(this).closest('.variant-group').remove();
        });
        
        $(document).off('click', '.remove-size-row').on('click', '.remove-size-row', function() {
            $(this).closest('.size-row').remove();
        });
        
        $(document).off('click', '.add-size-row').on('click', '.add-size-row', function() {
            const variantGroup = $(this).closest('.variant-group');
            const sizeContainer = variantGroup.find('.size-container');
            const sizeRowTemplate = sizeContainer.find('.size-row').first().clone();
            sizeRowTemplate.find('input').val('');
            sizeContainer.append(sizeRowTemplate);
        });
        
        $(document).off('click', '#add-variant, [data-action="add-variant"]').on('click', '#add-variant, [data-action="add-variant"]', function() {
            const variantContainer = $('.variants-container');
            const variantTemplate = variantContainer.find('.variant-group').first().clone();
            variantTemplate.find('input').val('');
            variantTemplate.find('select').val('');
            variantContainer.append(variantTemplate);
        });
        
        // Khôi phục color preview
        $(document).off('change', '.color-select').on('change', '.color-select', function() {
            const selectedOption = $(this).find('option:selected');
            const colorName = selectedOption.data('color-name');
            const colorPreview = $(this).closest('.color-select-container').find('.color-preview');
            
            if (colorName) {
                // Map tên màu với mã màu
                const colorMap = {
                    'Đỏ': '#dc3545',
                    'Xanh dương': '#007bff',
                    'Xanh lá': '#28a745',
                    'Vàng': '#ffc107',
                    'Cam': '#fd7e14',
                    'Tím': '#6f42c1',
                    'Hồng': '#e83e8c',
                    'Xám': '#6c757d',
                    'Đen': '#343a40',
                    'Trắng': '#ffffff',
                    'Nâu': '#795548',
                    'Xanh ngọc': '#17a2b8',
                    'Xanh navy': '#001f3f',
                    'Xanh mint': '#20c997',
                    'Xanh olive': '#6c757d'
                };
                
                const colorCode = colorMap[colorName] || '#6c757d';
                colorPreview.css('background-color', colorCode);
                colorPreview.attr('title', colorName);
                colorPreview.show();
            } else {
                colorPreview.css('background-color', '');
                colorPreview.attr('title', '');
                colorPreview.hide();
            }
        });
        
        // Trigger color preview cho tất cả color select hiện tại
        $('.color-select').each(function() { $(this).trigger('change'); });
        
        // Khôi phục size preview
        $(document).off('change', '.size-select').on('change', '.size-select', function() {
            const selectedOption = $(this).find('option:selected');
            const sizePreview = $(this).siblings('.size-preview');
            sizePreview.text(selectedOption.text());
        });
    }
    
    @if(old('image_extra_names') || old('temp_extra_keys'))
        @php
            $extraNames = old('image_extra_names');
            $tempKeys = old('temp_extra_keys');
        @endphp
        @if(is_array($extraNames) && count($extraNames) > 0)
            // Xóa error message ngay lập tức
            setTimeout(function() {
                $('#image_extra').removeClass('is-invalid');
                $('#image_extra').siblings('.invalid-feedback').remove();
            }, 50);
            
            // Xóa block tránh lặp
            $('.restored-extra-block, .restored-extra-preview').remove();
            // Hiển thị thông báo
            $('#image_extra').after('<div class="alert alert-info mt-2 restored-extra-block"><i class="fa fa-info-circle me-2"></i>Ảnh phụ đã chọn: <strong>{{ implode(", ", $extraNames) }}</strong></div>');
            
            // Không hiển thị block lớn cho ảnh phụ
            
            // Tạo fake file input để bypass validation
            const fakeExtraInput = document.createElement('input');
            fakeExtraInput.type = 'file';
            fakeExtraInput.name = 'image_extra_fake[]';
            fakeExtraInput.id = 'image_extra_fake';
            fakeExtraInput.multiple = true;
            fakeExtraInput.style.display = 'none';
            fakeExtraInput.setAttribute('data-fake', 'true');
            document.getElementById('create-product-form').appendChild(fakeExtraInput);
            
            // Giữ input thật hiển thị để có thể chọn lại trực tiếp
            $('#image_extra_fake').show();
            
            // Xóa required và name để form không gửi file rỗng
            $('#image_extra').removeAttr('required');
            $('#image_extra').attr('data-original-name', 'image_extra[]');
            $('#image_extra').removeAttr('name');
            
            // Xóa error message nếu có
            $('#image_extra').removeClass('is-invalid');
            $('#image_extra').siblings('.invalid-feedback').remove();
            
            // Tạo fake file objects để giả lập files đã chọn
            const dataTransferExtra = new DataTransfer();
            @foreach($extraNames as $fileName)
                const fakeFile{{ $loop->index }} = new File([''], '{{ $fileName }}', { type: 'image/png' });
                dataTransferExtra.items.add(fakeFile{{ $loop->index }});
            @endforeach
            fakeExtraInput.files = dataTransferExtra.files;
            
            // Không render preview khi chỉ khôi phục tên file
            restoredExtraRendered = true;
            
            // Đảm bảo giữ lại temp keys khi back withInput
            if (Array.isArray(@json(old('temp_extra_keys')))) {
                const keys = @json(old('temp_extra_keys'));
                $('input[name="temp_extra_keys[]"]').remove();
                keys.forEach(function(key){
                    $('<input>').attr({ type: 'hidden', name: 'temp_extra_keys[]', value: key }).appendTo('#create-product-form');
                });
            }
            
            // Khôi phục event handlers sau khi load old data
            setTimeout(function() { restoreEventHandlers(); }, 500);
        @endif
    @endif
    
    // Upload tạm + Preview hình ảnh chính
    $('#image_main, #image_main_fake').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Xóa placeholder cũ nếu có
            $('.alert-warning').remove();
            
            // Hiển thị lại file input thật nếu đang ẩn
            if ($(this).attr('id') === 'image_main_fake') {
                $('#image_main').show();
                const originalName = $('#image_main').attr('data-original-name') || 'image_main';
                $('#image_main').attr('name', originalName);
                $('#image_main').attr('required', 'required');
                $('#image_main_fake').remove();
            }
            
            // Khi user chọn file mới, xóa các block restored để không bị lặp
            $('.restored-main-block, .restored-main-preview').remove();
            // Upload tạm qua AJAX
            const formData = new FormData();
            formData.append('file', file);
            formData.append('type', 'main');
            $.ajax({
                url: '{{ route('ajax.sanpham.uploadTemp') }}',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp){
                    if(resp.success){
                        $('#temp_main_key').val(resp.temp_key);
                    }
                }
            });
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image_main_preview_img').attr('src', e.target.result);
                $('#image_main_preview').show();
            };
            reader.readAsDataURL(file);
            
            // Xóa hidden input cũ nếu có
            $('input[name="image_main_name"]').remove();
            
            // Lưu tên file vào hidden input để giữ lại khi validation fail
            $('<input>').attr({
                type: 'hidden',
                name: 'image_main_name',
                value: file.name
            }).appendTo('#create-product-form');
        } else {
            $('#image_main_preview').hide();
        }
    });

    // Upload tạm + Preview hình ảnh phụ
    $('#image_extra, #image_extra_fake').on('change', function(e) {
        const files = e.target.files;
        const previewContainer = $('#image_extra_preview');
        previewContainer.empty();

        if (files.length > 0) {
            // Chỉ set uploaded = 1 khi thực sự có file
            $('input[name="image_extra_uploaded"]').val('1');
            // Xóa placeholder cũ nếu có
            $('.alert-warning').remove();
            
            // Hiển thị lại file input thật nếu đang ẩn
            if ($(this).attr('id') === 'image_extra_fake') {
                $('#image_extra').show();
                const originalName = $('#image_extra').attr('data-original-name') || 'image_extra[]';
                $('#image_extra').attr('name', originalName);
                $('#image_extra').attr('required', 'required');
                $('#image_extra_fake').remove();
            }
            
            // Khi user chọn file mới, xóa các block restored để không bị lặp
            $('.restored-extra-block, .restored-extra-preview').remove();
            previewContainer.append('<h6><i class="fa fa-images me-2"></i>Hình ảnh đã chọn:</h6>');
            
            // Lưu tên các file vào hidden input
            const fileNames = [];
            const tempKeys = [];
            Array.from(files).forEach((file, index) => {
                // Chỉ xử lý file hợp lệ
                if (file && file.size > 0) {
                    fileNames.push(file.name);
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('type', 'extra');
                    $.ajax({
                        url: '{{ route('ajax.sanpham.uploadTemp') }}',
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        data: formData,
                        async: false,
                        contentType: false,
                        processData: false,
                        success: function(resp){
                            if(resp.success && resp.temp_key){ 
                                tempKeys.push(resp.temp_key); 
                            }
                        }
                    });
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgContainer = $('<div class="image-preview-item">');
                    const img = $('<img>').attr({
                        'src': e.target.result,
                        'alt': 'Preview ' + (index + 1),
                        'style': 'max-width: 100px; max-height: 100px;'
                    });
                    const removeBtn = $('<button type="button" class="image-preview-remove" title="Xóa ảnh này">×</button>');
                    
                    removeBtn.on('click', function() {
                        imgContainer.remove();
                    });
                    
                    imgContainer.append(img).append(removeBtn);
                    previewContainer.append(imgContainer);
                };
                reader.readAsDataURL(file);
            });
            
            // Xóa hidden input cũ nếu có
            $('input[name="image_extra_names[]"]').remove();
            
            // Lưu tên các file vào hidden input để giữ lại khi validation fail
            fileNames.forEach(function(fileName) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'image_extra_names[]',
                    value: fileName
                }).appendTo('#create-product-form');
            });

            // Lưu các temp key - chỉ khi có keys thực sự
            // Clear cũ
            $('input[name="temp_extra_keys[]"]').remove();
            if (tempKeys.length > 0) {
                tempKeys.forEach(function(key){
                    if (key && key.trim() !== '') {
                        $('<input>').attr({ type: 'hidden', name: 'temp_extra_keys[]', value: key }).appendTo('#create-product-form');
                    }
                });
            }
        } else {
            // Khi không có file, xóa tất cả temp keys và set uploaded = 0
            $('input[name="temp_extra_keys[]"]').remove();
            $('input[name="image_extra_uploaded"]').val('0');
            $('input[name="image_extra_names[]"]').remove();
        }
    });

    // Preview màu sắc
    $(document).on('change', '.color-select', function() {
        const selectedOption = $(this).find('option:selected');
        const colorName = selectedOption.data('color-name');
        const colorPreview = $(this).closest('.color-select-container').find('.color-preview');
        
        if (colorName) {
            // Map tên màu với mã màu
            const colorMap = {
                'Đỏ': '#dc3545',
                'Xanh dương': '#007bff',
                'Xanh lá': '#28a745',
                'Vàng': '#ffc107',
                'Cam': '#fd7e14',
                'Tím': '#6f42c1',
                'Hồng': '#e83e8c',
                'Xám': '#6c757d',
                'Đen': '#343a40',
                'Trắng': '#ffffff',
                'Nâu': '#795548',
                'Xanh ngọc': '#17a2b8',
                'Xanh navy': '#001f3f',
                'Xanh mint': '#20c997',
                'Xanh olive': '#6c757d'
            };
            
            const colorCode = colorMap[colorName] || '#6c757d';
            colorPreview.css('background-color', colorCode);
            colorPreview.attr('title', colorName);
            colorPreview.show();
        } else {
            colorPreview.hide();
        }
    });
    
    // Khởi tạo màu sắc cho các select đã có sẵn
    $('.color-select').each(function() {
        $(this).trigger('change');
    });

    // Preview size
    $(document).on('change', '.size-select', function() {
        const selectedOption = $(this).find('option:selected');
        const sizeName = selectedOption.data('size-name');
        const sizePreview = $(this).closest('.size-select-container').find('.size-preview');
        
        if (sizeName) {
            sizePreview.text(sizeName).show();
        } else {
            sizePreview.hide();
        }
    });

    // Tắt validation HTML5 cho tất cả input
    $('input, select, textarea').removeAttr('required');
    
    // Validation form trước khi submit
    $('#create-product-form').on('submit', function(e) {
        let isValid = true;
        let errorMessages = [];

        // Kiểm tra tên sản phẩm
        const tenSP = $('#tenSP').val().trim();
        if (!tenSP) {
            errorMessages.push('Tên sản phẩm không được để trống');
            $('#tenSP').addClass('is-invalid');
            isValid = false;
        } else {
            $('#tenSP').removeClass('is-invalid');
        }

        // Kiểm tra danh mục
        const id_danhmuc = $('#id_danhmuc').val();
        if (!id_danhmuc) {
            errorMessages.push('Vui lòng chọn danh mục');
            $('#id_danhmuc').addClass('is-invalid');
            isValid = false;
        } else {
            $('#id_danhmuc').removeClass('is-invalid');
        }

        // Kiểm tra hình ảnh chính (bỏ qua nếu đã có temp_main_key)
        const imageMain = $('#image_main')[0].files[0];
        const hasTempMain = $('#temp_main_key').val();
        if (!imageMain && !hasTempMain) {
            errorMessages.push('Vui lòng chọn hình ảnh chính');
            $('#image_main').addClass('is-invalid');
            isValid = false;
        } else {
            $('#image_main').removeClass('is-invalid');
        }

        // Kiểm tra biến thể - không bắt buộc, nhưng nếu có thì phải hợp lệ
        // Cho phép tạo sản phẩm chỉ với giá sản phẩm chính (không bắt buộc biến thể)

        // Kiểm tra mỗi biến thể có ít nhất một size và validation giá (chỉ khi có biến thể)
        $('.variant-item').each(function(index) {
            const sizes = $(this).find('.size-variant-row').length;
            if (sizes === 0) {
                // Không bắt buộc size, chỉ cảnh báo
                console.log(`Biến thể #${index + 1} không có size - sẽ bỏ qua biến thể này`);
            }
            
            // Kiểm tra giá trong mỗi size (chỉ khi có size)
            $(this).find('.price-input').each(function() {
                const priceValue = $(this).val().trim();
                if (priceValue && (isNaN(priceValue) || parseFloat(priceValue) < 0)) {
                    errorMessages.push(`Giá phải là số và lớn hơn hoặc bằng 0`);
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Mặc định số lượng = 1 nếu bỏ trống và validation (chỉ khi có size)
            $(this).find('input[name*="[so_luong]"]').each(function(){
                if($(this).val() === '' || $(this).val() === null){
                    $(this).val(1);
                }
                
                // Validation số lượng
                const quantity = parseInt($(this).val());
                if (isNaN(quantity) || quantity < 0) {
                    errorMessages.push(`Số lượng phải là số và lớn hơn hoặc bằng 0`);
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        });

        if (!isValid) {
            e.preventDefault();
            
            // Hiển thị thông báo lỗi
            let errorHtml = '<div class="alert alert-danger"><strong>Vui lòng sửa các lỗi sau:</strong><ul class="mb-0">';
            errorMessages.forEach(function(message) {
                errorHtml += '<li>' + message + '</li>';
            });
            errorHtml += '</ul></div>';
            
            $('#form-messages').html(errorHtml);
            
            // Scroll to top
            $('html, body').animate({
                scrollTop: 0
            }, 500);
        }
    });

    // Xóa thông báo lỗi khi user bắt đầu nhập
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
        $('#form-messages').empty();
    });

    // Xử lý xóa biến thể với modal xác nhận đẹp
    $(document).on('click', '[data-action="remove-variant"]', function() {
        const self = this;
        showConfirm('Bạn có chắc chắn muốn xóa biến thể này?').then(function(agree){
            if (agree) {
                $(self).closest('.variant-item').fadeOut(300, function(){ $(this).remove(); });
            }
        });
    });

    // Xử lý xóa size với modal xác nhận đẹp
    $(document).on('click', '[data-action="remove-size-row"]', function() {
        const btn = this;
        const variantItem = $(btn).closest('.variant-item');
        const sizeRows = variantItem.find('.size-variant-row');
        // Cho phép xóa tất cả size - không bắt buộc phải có ít nhất 1 size
        showConfirm('Bạn có chắc chắn muốn xóa size này?').then(function(agree){
            if (agree) {
                $(btn).closest('.size-variant-row').fadeOut(300, function(){ $(this).remove(); });
            }
        });
    });

    // Xử lý thêm size mới
    $(document).on('click', '[data-action="add-size-row"]', function() {
        console.log('Add size clicked');
        const variantIndex = $(this).data('variant-index');
        const tbody = $(this).closest('.size-variants-container').find('tbody');
        const newIndex = tbody.find('tr').length;
        
        const newRow = `
            <tr class="size-variant-row">
                <td>
                    <div class="size-select-container">
                        <select style="height: 40px;" name="variants[${variantIndex}][sizes][${newIndex}][size]" class="form-select size-select">
                            <option value="">-- Chọn size --</option>
                            @foreach($sizes as $size)
                                <option value="{{ $size->id }}" data-size-name="{{ $size->name }}">{{ $size->name }}</option>
                            @endforeach
                        </select>
                        <div class="size-preview"></div>
                    </div>
                </td>
                <td>
                    <input type="number" name="variants[${variantIndex}][sizes][${newIndex}][so_luong]" class="form-control" value="1" min="0">
                    <small class="form-text text-muted">Số lượng tồn kho</small>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" name="variants[${variantIndex}][sizes][${newIndex}][gia]" class="form-control price-input" placeholder="0">
                        <span class="input-group-text">VND</span>
                    </div>
                    <small class="form-text text-muted">Bỏ trống để dùng giá sản phẩm chính</small>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" name="variants[${variantIndex}][sizes][${newIndex}][gia_khuyenmai]" class="form-control price-input" placeholder="0">
                        <span class="input-group-text">VND</span>
                    </div>
                    <small class="form-text text-muted">Để trống nếu không có KM</small>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-size-row" title="Xóa size này" data-action="remove-size-row">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        
        tbody.append(newRow);
        
        // Tắt validation HTML5 cho element mới
        tbody.find('input, select, textarea').removeAttr('required');
    });

    // Xử lý thêm biến thể mới
    $(document).on('click', '[data-action="add-variant"]', function() {
        console.log('Add variant clicked');
        const variantIndex = $('.variant-item').length;
        
        const newVariant = `
            <div class="variant-item mb-4" data-variant-index="${variantIndex}">
                <div class="variant-header">
                    <div class="variant-title">
                        <h5 class="mb-0">
                            <i class="fa fa-palette me-2"></i>
                            Biến thể #${variantIndex + 1}
                        </h5>
                        <small class="text-muted">Chọn màu sắc và thêm các size</small>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-variant" data-action="remove-variant">
                        <i class="fa fa-trash me-1"></i>Xóa biến thể
                    </button>
                </div>
                
                <div class="variant-content">
                    <div class="row">
                        <!-- Màu sắc -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fa fa-palette me-1"></i>Màu sắc <span class="text-danger">*</span>
                            </label>
                            <div class="color-select-container">
                                <select style="height: 40px;" name="variants[${variantIndex}][mausac]" class="form-select color-select">
                                    <option value="">-- Chọn màu sắc --</option>
                                    @foreach($mausacs as $mau)
                                        <option value="{{ $mau->id }}" data-color-name="{{ $mau->name }}">{{ $mau->name }}</option>
                                    @endforeach
                                </select>
                                <div class="color-preview"></div>
                            </div>
                            <small class="form-text text-muted">Chọn màu chính của biến thể này</small>
                        </div>
                    </div>

                    <!-- Bảng size và thông tin -->
                    <div class="size-variants-container">
                        <div class="size-header">
                            <h6 class="mb-3">
                                <i class="fa fa-ruler me-2"></i>
                                Danh sách size và thông tin
                            </h6>
                            <p class="text-muted mb-3">
                                <i class="fa fa-lightbulb me-1"></i>
                                Mỗi size sẽ có số lượng, giá gốc và giá khuyến mãi riêng biệt
                            </p>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered size-variants-table">
                                <thead class="table-primary">
                                    <tr>
                                        <th width="20%">
                                            <i class="fa fa-ruler me-1"></i>Size
                                        </th>
                                        <th width="20%">
                                            <i class="fa fa-boxes me-1"></i>Số lượng
                                        </th>
                                        <th width="25%">
                                            <i class="fa fa-money-bill-wave me-1"></i>Giá gốc
                                        </th>
                                        <th width="25%">
                                            <i class="fa fa-tags me-1"></i>Giá khuyến mãi
                                        </th>
                                        <th width="10%">
                                            <i class="fa fa-cogs me-1"></i>Thao tác
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="size-variant-row">
                                        <td>
                                            <div class="size-select-container">
                                                <select style="height: 40px;" name="variants[${variantIndex}][sizes][0][size]" class="form-select size-select">
                                                    <option value="">-- Chọn size --</option>
                                                    @foreach($sizes as $size)
                                                        <option value="{{ $size->id }}" data-size-name="{{ $size->name }}">{{ $size->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="size-preview"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" name="variants[${variantIndex}][sizes][0][so_luong]" 
                                                   class="form-control" value="1" min="0">
                                            <small class="form-text text-muted">Số lượng tồn kho</small>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="variants[${variantIndex}][sizes][0][gia]" 
                                                       class="form-control price-input" placeholder="0">
                                                <span class="input-group-text">VND</span>
                                            </div>
                                            <small class="form-text text-muted">Bỏ trống để dùng giá sản phẩm chính</small>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="variants[${variantIndex}][sizes][0][gia_khuyenmai]" 
                                                       class="form-control price-input" placeholder="0">
                                                <span class="input-group-text">VND</span>
                                            </div>
                                            <small class="form-text text-muted">Để trống nếu không có KM</small>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-size-row" 
                                                title="Xóa size này" data-action="remove-size-row">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="size-actions">
                            <button type="button" class="btn btn-info btn-sm add-size-row" data-variant-index="${variantIndex}" data-action="add-size-row">
                                <i class="fa fa-plus me-1"></i>Thêm size mới
                            </button>
                            <small class="form-text text-muted ms-2">
                                Biến thể có thể không có size (tùy chọn)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#variants-container').append(newVariant);
        
        // Tắt validation HTML5 cho biến thể mới
        $('#variants-container').find('input, select, textarea').removeAttr('required');
    });
});
</script>
<!-- Bootstrap Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle me-2 text-danger"></i>Xác nhận</h5>
                <button type="button" class="btn-close close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="mb-1 confirm-message">Bạn có chắc chắn?</p>
                <small class="text-danger">Hành động này không thể hoàn tác!</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmModalAgree">Đồng ý</button>
            </div>
        </div>
    </div>
</div>
<script>
function showConfirm(message){
    return new Promise(function(resolve){
        const modalEl = document.getElementById('confirmModal');
        $('#confirmModal .confirm-message').text(message || 'Bạn có chắc chắn?');
        const agreeBtn = document.getElementById('confirmModalAgree');
        function onAgree(){ cleanup(); resolve(true); }
        function onCancel(){ cleanup(); resolve(false); }
        function cleanup(){
            if (window.bootstrap && bootstrap.Modal.getInstance(modalEl)) {
                bootstrap.Modal.getInstance(modalEl).hide();
            } else { $('#confirmModal').modal('hide'); }
            agreeBtn.removeEventListener('click', onAgree);
            modalEl.removeEventListener('hidden.bs.modal', onCancel);
        }
        if (window.bootstrap && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
            agreeBtn.addEventListener('click', onAgree);
            modalEl.addEventListener('hidden.bs.modal', onCancel, { once: true });
            modal.show();
        } else if (typeof $ !== 'undefined' && typeof $('#confirmModal').modal === 'function') {
            agreeBtn.addEventListener('click', onAgree);
            $('#confirmModal').one('hidden.bs.modal', onCancel).modal({ backdrop: 'static', keyboard: false, show: true });
        } else {
            // Fallback native confirm nếu thiếu bootstrap
            const ok = window.confirm(message || 'Bạn có chắc chắn?');
            resolve(ok);
        }
    });
}
function showToast(text){
    const id = 'toast-lite';
    let el = document.getElementById(id);
    if(!el){ el = document.createElement('div'); el.id = id; el.className = 'toast-lite'; document.body.appendChild(el); }
    el.textContent = text || '';
    el.classList.add('show');
    setTimeout(()=> el.classList.remove('show'), 2000);
}
</script>
@endpush

@push('styles')
<link href="{{ asset('backend/css/product-create-enhanced.css') }}" rel="stylesheet">
<style>
    /* Form layout tweaks */
    .card .form-label { font-weight: 600; }
    .card .form-control, .card .form-select { border-radius: 6px; }
    .size-variants-table th, .size-variants-table td { vertical-align: middle; }
    .alert-info { background: #e8f6ff; border-color: #b6e0fe; color: #095a9d; }
    .alert-danger { border-radius: 8px; }
    /* Remove custom overlay modal to avoid layout cover - use Bootstrap */
    .toast-lite{position:fixed;right:16px;bottom:16px;background:#333;color:#fff;padding:10px 14px;border-radius:6px;opacity:0;transform:translateY(8px);transition:all .2s;z-index:1060}
    .toast-lite.show{opacity:1;transform:none}
    /* Color preview styles */
    .color-select-container {
        position: relative;
    }
    
    .color-preview {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #ddd;
        display: none;
    }
    
    /* Size preview styles */
    .size-select-container {
        position: relative;
    }
    
    .size-preview {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: #007bff;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: bold;
        display: none;
    }
    
    /* Image preview styles */
    .image-preview-item {
        position: relative;
        display: inline-block;
        margin: 5px;
    }
    
    .image-preview-remove {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .image-preview-remove:hover {
        background: #c82333;
    }
    
    /* Image placeholder styles */
    .image-placeholder {
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .image-placeholder:hover {
        border-color: #007bff;
        background: #e3f2fd !important;
    }
    
    .image-placeholder i {
        opacity: 0.7;
    }
    
    .image-placeholder:hover i {
        opacity: 1;
        color: #007bff !important;
    }
</style>
@endpush



