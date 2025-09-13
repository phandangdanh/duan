@extends('backend.layout')

@push('styles')
<link href="{{ asset('backend/css/product-create-enhanced.css') }}" rel="stylesheet">
<style>
/* CSS cho ảnh đã xóa */
.deleted-image {
    opacity: 0.5 !important;
    pointer-events: none !important;
    position: relative;
}

.deleted-image::after {
    content: "Đã xóa";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(220, 53, 69, 0.8);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    z-index: 10;
}

/* CSS cho preview ảnh */
.image-preview-item {
    position: relative;
    display: inline-block;
    margin: 5px;
}

.image-preview-item img {
    max-width: 100px;
    max-height: 100px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.image-preview-item button {
    position: absolute;
    top: 2px;
    right: 2px;
    padding: 2px 6px;
    font-size: 10px;
}
</style>
@endpush

@push('scripts')
<script>
// Biến lưu trữ ảnh đã xóa - GLOBAL SCOPE
let deletedImages = [];

// Để có thể kiểm tra trong console
window.deletedImages = deletedImages;

// Hàm xóa ảnh hiện tại - GLOBAL SCOPE
function deleteCurrentImage(imageId) {
    Swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc chắn muốn xóa ảnh này?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            // Kiểm tra xem deletedImages có phải array không
            if (!Array.isArray(deletedImages)) {
                deletedImages = [];
            }
            
            deletedImages.push(imageId);
            
            // Sử dụng vanilla JavaScript
            const button = document.querySelector(`button[onclick="deleteCurrentImage(${imageId})"]`);
            
            if (button) {
                const imageItem = button.closest('.current-image-item');
                
                if (imageItem) {
                    imageItem.style.opacity = '0.5';
                    imageItem.style.pointerEvents = 'none';
                    imageItem.classList.add('deleted-image');
                }
            }
        }
    });
}

// Khởi tạo khi DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // console.log('JavaScript loaded successfully');
    
    // Preview hình ảnh chính
    const imageMainInput = document.getElementById('image_main');
    // console.log('imageMainInput:', imageMainInput);
    
    if (imageMainInput) {
        imageMainInput.addEventListener('change', function(e) {
            // console.log('Image main changed:', e.target.files[0]);
            const file = e.target.files[0];
            const previewDiv = document.getElementById('image_main_preview');
            const previewImg = document.getElementById('image_main_preview_img');
            
            // console.log('Preview elements:', { previewDiv, previewImg });
            
            if (file && previewDiv && previewImg) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // console.log('FileReader loaded');
                    previewImg.src = e.target.result;
                    previewDiv.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else if (previewDiv) {
                previewDiv.style.display = 'none';
            }
        });
    }

    // Preview hình ảnh phụ
    const imageExtraInput = document.getElementById('image_extra');
    if (imageExtraInput) {
        imageExtraInput.addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.querySelector('#image_extra_preview .image-preview-container');
            const previewDiv = document.getElementById('image_extra_preview');
            
            if (previewContainer) {
                previewContainer.innerHTML = '';
                
                if (files.length > 0) {
                    // Tạo tiêu đề
                    let titleElement = previewDiv.querySelector('h6');
                    if (!titleElement) {
                        titleElement = document.createElement('h6');
                        titleElement.innerHTML = '<i class="fa fa-images me-2"></i>Hình ảnh phụ mới đã chọn:';
                        previewDiv.insertBefore(titleElement, previewContainer);
                    }
                    
                    Array.from(files).forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const imgContainer = document.createElement('div');
                            imgContainer.className = 'image-preview-item position-relative';
                            imgContainer.style.cssText = 'display: inline-block; margin: 5px;';
                            imgContainer.dataset.fileIndex = index; // Lưu index của file
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = 'Preview ' + (index + 1);
                            img.style.cssText = 'max-width: 100px; max-height: 100px; border: 1px solid #ddd; border-radius: 4px;';
                            
                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.className = 'btn btn-danger btn-sm position-absolute';
                            removeBtn.style.cssText = 'top: 2px; right: 2px; padding: 2px 6px; font-size: 10px;';
                            removeBtn.title = 'Xóa ảnh này';
                            removeBtn.innerHTML = '×';
                            
                            removeBtn.addEventListener('click', function() {
                                imgContainer.remove();
                                updateFileInput();
                            });
                            
                            imgContainer.appendChild(img);
                            imgContainer.appendChild(removeBtn);
                            previewContainer.appendChild(imgContainer);
                        };
                        reader.readAsDataURL(file);
                    });
                    
                    // Cập nhật file count
                    updateFileCount();
                } else {
                    // Xóa tiêu đề nếu không có file
                    const titleElement = previewDiv.querySelector('h6');
                    if (titleElement) {
                        titleElement.remove();
                    }
                    
                    // Cập nhật file count
                    updateFileCount();
                }
            }
        });
    }

    // Hàm cập nhật file input khi xóa ảnh preview
    function updateFileInput() {
        const imageExtraInput = document.getElementById('image_extra');
        const previewContainer = document.querySelector('#image_extra_preview .image-preview-container');
        
        if (imageExtraInput && previewContainer) {
            const remainingImages = previewContainer.querySelectorAll('.image-preview-item');
            
            if (remainingImages.length === 0) {
                // Nếu không còn ảnh nào, xóa file input
                imageExtraInput.value = '';
                const titleElement = document.querySelector('#image_extra_preview h6');
                if (titleElement) {
                    titleElement.remove();
                }
            } else {
                // Tạo DataTransfer để cập nhật file input
                const dt = new DataTransfer();
                
                // Lấy file gốc từ input
                const originalFiles = Array.from(imageExtraInput.files);
                
                // Tạo map để track file đã được thêm
                const addedFiles = new Set();
                
                // Chỉ giữ lại các file tương ứng với preview còn lại
                remainingImages.forEach((previewItem) => {
                    const fileIndex = parseInt(previewItem.dataset.fileIndex);
                    // Tìm file tương ứng dựa trên fileIndex
                    if (originalFiles[fileIndex] && !addedFiles.has(fileIndex)) {
                        dt.items.add(originalFiles[fileIndex]);
                        addedFiles.add(fileIndex);
                    }
                });
                
                // Cập nhật file input
                imageExtraInput.files = dt.files;
                
                // Cập nhật text hiển thị số file
                updateFileCount();
            }
        }
    }

    // Hàm cập nhật text hiển thị số file
    function updateFileCount() {
        const imageExtraInput = document.getElementById('image_extra');
        const fileCountElement = document.querySelector('.file-count');
        
        if (imageExtraInput && fileCountElement) {
            const fileCount = imageExtraInput.files.length;
            if (fileCount > 0) {
                fileCountElement.textContent = fileCount + ' files';
                fileCountElement.style.display = 'inline';
            } else {
                fileCountElement.style.display = 'none';
            }
        }
    }

    // Thêm hidden input để gửi danh sách ảnh đã xóa
    const form = document.getElementById('create-product-form');
    if (form) {
        // console.log('Form found:', form);
        // console.log('Form action:', form.action);
        // console.log('Form method:', form.method);
        
        form.addEventListener('submit', function(e) {
            // console.log('=== FORM SUBMIT EVENT TRIGGERED ===');
            // console.log('Form submitting, deletedImages:', deletedImages);
            // console.log('deletedImages type:', typeof deletedImages);
            // console.log('deletedImages length:', deletedImages.length);
            // console.log('deletedImages content:', JSON.stringify(deletedImages));
            
            // Xóa tất cả hidden input cũ để tránh duplicate
            const oldInputs = form.querySelectorAll('input[name="deleted_images[]"]');
            // console.log('Old hidden inputs found:', oldInputs.length);
            oldInputs.forEach(input => {
                // console.log('Removing old input:', input);
                input.remove();
            });
            
            if (deletedImages && deletedImages.length > 0) {
                // console.log('Adding hidden inputs for deleted images...');
                deletedImages.forEach(function(imageId, index) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'deleted_images[]';
                    hiddenInput.value = imageId;
                    form.appendChild(hiddenInput);
                    // console.log(`Added hidden input ${index + 1}: name="${hiddenInput.name}", value="${hiddenInput.value}"`);
                });
                
            } else {
                // console.log('No images to delete or deletedImages is empty/null');
                // console.log('deletedImages value:', deletedImages);
            }
        });
        
        // console.log('Form submit event listener added successfully');
    } else {
        // console.error('Form not found!');
    }
});
</script>
@endpush

@section('content')
    @include('backend.component.breadcrum', ['title' => 'Cập nhật sản phẩm'])

    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('sanpham.update', $sanpham->id) }}" method="POST" enctype="multipart/form-data" id="create-product-form">
                    @csrf @method('PUT')
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
                               value="{{ old('tenSP', $sanpham->tenSP) }}" required>
                        @error('tenSP')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mã sản phẩm -->
                    <div class="margin-bottom-4" style="margin: 20px 0px;">
                        <label for="maSP" class="form-label">Mã sản phẩm</label>
                        <input type="text" name="maSP" id="maSP" class="form-control @error('maSP') is-invalid @enderror" value="{{ old('maSP', $sanpham->maSP) }}">
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
                                <option value="{{ $dm->id }}" {{ old('id_danhmuc', $sanpham->id_danhmuc) == $dm->id ? 'selected' : '' }}>
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
                        <textarea name="moTa" id="moTa" class="form-control @error('moTa') is-invalid @enderror" rows="5" placeholder="Mô tả sản phẩm...">{{ old('moTa', $sanpham->moTa) }}</textarea>
                        @error('moTa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Giá sản phẩm (giá chính ở sản phẩm) -->
                    <div class="card margin-bottom-4">
                        <div class="card-header" style="margin: 20px 0px;">
                            <h1 class="ctsph1">Giá sản phẩm</h1>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold"><i class="fa fa-money-bill me-1"></i>Giá gốc (sản phẩm chính)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="9999999999999.99" name="base_price" class="form-control" placeholder="0" value="{{ old('base_price', $sanpham->base_price) }}">
                                        <span class="input-group-text">VND</span>
                                    </div>
                                    <small class="form-text text-muted">Biến thể không nhập giá sẽ dùng giá này</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold"><i class="fa fa-tags me-1"></i>Giá khuyến mãi (sản phẩm chính)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="9999999999999.99" name="base_sale_price" class="form-control" placeholder="0" value="{{ old('base_sale_price', $sanpham->base_sale_price) }}">
                                        <span class="input-group-text">VND</span>
                                    </div>
                                    <small class="form-text text-muted">Để trống nếu không có khuyến mãi</small>
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
                                <strong>Hướng dẫn:</strong> Cập nhật hình ảnh chính và thêm/xóa hình ảnh phụ.
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Hình ảnh hiện tại -->
                            @if($sanpham->hinhanh && $sanpham->hinhanh->count() > 0)
                                <div class="margin-bottom-4" style="margin: 20px 0px;">
                                    <label class="form-label" style="font-weight: bold;">Hình ảnh hiện tại</label>
                                    <div class="current-images d-flex flex-wrap gap-3">
                                        @foreach($sanpham->hinhanh->whereNull('deleted_at')->sortByDesc('is_default') as $image)
                                            <div class="current-image-item position-relative">
                                                <img src="{{ asset($image->url) }}" alt="Current image" 
                                                     style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; border-radius: 8px;">
                                                <button type="button" class="btn btn-danger btn-sm position-absolute" 
                                                        style="top: 5px; right: 5px; padding: 2px 6px; font-size: 12px;"
                                                        onclick="deleteCurrentImage({{ $image->id }})" 
                                                        title="Xóa ảnh này">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                <div class="image-info mt-2">
                                                    <small class="text-muted">
                                                        @if($image->is_default)
                                                            <span class="badge bg-primary">ẢNH CHÍNH</span>
                                                            <br><small class="text-warning">⚠️ Có thể xóa</small>
                                                        @else
                                                            <span class="badge bg-secondary">ẢNH PHỤ</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Hình ảnh chính mới -->
                            <div class="margin-bottom-4" style="margin: 20px 0px;">
                                <label for="image_main" class="form-label" style="font-weight: bold;">
                                    Hình ảnh chính mới
                                </label>
                                <input type="file" name="image_main" id="image_main" 
                                       class="form-control @error('image_main') is-invalid @enderror" 
                                       accept="image/*">
                                <small class="form-text text-muted">Chọn hình ảnh chính mới (để trống nếu không thay đổi)</small>
                                @error('image_main')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="image_main_preview" class="mt-2" style="display: none;">
                                    <h6><i class="fa fa-image me-2"></i>Hình ảnh chính mới:</h6>
                                    <img id="image_main_preview_img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                            </div>

                            <!-- Hình ảnh phụ mới -->
                            <div class="margin-bottom-4" style="margin: 20px 0px;">
                                <label for="image_extra" class="form-label" style="font-weight: bold;">
                                    Hình ảnh phụ mới
                                </label>
                                <input type="file" name="image_extra[]" id="image_extra" 
                                       class="form-control @error('image_extra') is-invalid @enderror" 
                                       accept="image/*" multiple>
                                <small class="form-text text-muted">Chọn nhiều hình ảnh phụ mới (để trống nếu không thay đổi)</small>
                                <span class="file-count text-primary ms-2" style="display: none;"></span>
                                @error('image_extra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="image_extra_preview" class="mt-2">
                                    <div class="image-preview-container d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chi tiết sản phẩm (giống create, khởi tạo 1 biến thể trống để thêm mới) -->
                    <div class="card margin-bottom-4">
                        <div class="card-header" style="margin: 20px 0px;">
                            <h1 class="ctsph1">Chi tiết sản phẩm</h1>
                            <div class="alert alert-info mb-0">
                                <i class="fa fa-info-circle me-2"></i>
                                <strong>Hướng dẫn:</strong> Thêm/cập nhật biến thể, size, giá và tồn kho như trang tạo.
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="variants-container">
                                @php $vIndex = 0; @endphp
                                @forelse(($variants ?? []) as $variant)
                                <div class="variant-item mb-4" data-variant-index="{{ $vIndex }}">
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
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fa fa-palette me-1"></i>Màu sắc <span class="text-danger">*</span>
                                                </label>
                                                <div class="color-select-container">
                                                    <select style="height: 40px;" name="variants[{{ $vIndex }}][mausac]" class="form-select color-select" required>
                                                        <option value="">-- Chọn màu sắc --</option>
                                                        @foreach($mausacs as $mau)
                                                            <option value="{{ $mau->id }}" data-color-name="{{ $mau->name }}" {{ (isset($variant['mausac']) && $variant['mausac']==$mau->id) ? 'selected' : '' }}>{{ $mau->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="color-preview"></div>
                                                </div>
                                                <small class="form-text text-muted">Chọn màu chính của biến thể này</small>
                                            </div>
                                        </div>

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
                                                            <th width="20%"><i class="fa fa-ruler me-1"></i>Size</th>
                                                            <th width="20%"><i class="fa fa-boxes me-1"></i>Số lượng</th>
                                                            <th width="25%"><i class="fa fa-money-bill-wave me-1"></i>Giá gốc</th>
                                                            <th width="25%"><i class="fa fa-tags me-1"></i>Giá khuyến mãi</th>
                                                            <th width="10%"><i class="fa fa-cogs me-1"></i>Thao tác</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $sIndex = 0; @endphp
                                                        @forelse(($variant['sizes'] ?? []) as $s)
                                                        <tr class="size-variant-row">
                                                            <td>
                                                                <div class="size-select-container">
                                                                    <select style="height: 40px;" name="variants[{{ $vIndex }}][sizes][{{ $sIndex }}][size]" class="form-select size-select" required>
                                                                        <option value="">-- Chọn size --</option>
                                                                        @foreach($sizes as $size)
                                                                            <option value="{{ $size->id }}" data-size-name="{{ $size->name }}" {{ ($s['size']??null)==$size->id ? 'selected' : '' }}>{{ $size->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div class="size-preview"></div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="variants[{{ $vIndex }}][sizes][{{ $sIndex }}][so_luong]" class="form-control" value="{{ $s['so_luong'] ?? 1 }}" min="0" required>
                                                                <small class="form-text text-muted">Số lượng tồn kho</small>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input type="text" name="variants[{{ $vIndex }}][sizes][{{ $sIndex }}][gia]" class="form-control price-input" value="{{ $s['gia'] ?? '' }}" placeholder="0">
                                                                    <span class="input-group-text">VND</span>
                                                                </div>
                                                                <small class="form-text text-muted">Bỏ trống để dùng giá sản phẩm chính</small>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input type="text" name="variants[{{ $vIndex }}][sizes][{{ $sIndex }}][gia_khuyenmai]" class="form-control price-input" value="{{ $s['gia_khuyenmai'] ?? 0 }}" placeholder="0">
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
                                                        @php $sIndex++; @endphp
                                                        @empty
                                                        <tr class="size-variant-row">
                                                            <td>
                                                                <div class="size-select-container">
                                                                    <select name="variants[{{ $vIndex }}][sizes][0][size]" class="form-select size-select" required>
                                                                        <option value="">-- Chọn size --</option>
                                                                        @foreach($sizes as $size)
                                                                            <option value="{{ $size->id }}" data-size-name="{{ $size->name }}">{{ $size->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div class="size-preview"></div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="variants[{{ $vIndex }}][sizes][0][so_luong]" class="form-control" value="1" min="0" required>
                                                                <small class="form-text text-muted">Số lượng tồn kho</small>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input type="text" name="variants[{{ $vIndex }}][sizes][0][gia]" class="form-control price-input" placeholder="0">
                                                                    <span class="input-group-text">VND</span>
                                                                </div>
                                                                <small class="form-text text-muted">Bỏ trống để dùng giá sản phẩm chính</small>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input type="text" name="variants[{{ $vIndex }}][sizes][0][gia_khuyenmai]" class="form-control price-input" placeholder="0">
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
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="size-actions">
                                                <button type="button" class="btn btn-info btn-sm add-size-row" data-variant-index="{{ $vIndex }}" data-action="add-size-row">
                                                    <i class="fa fa-plus me-1"></i>Thêm size mới
                                                </button>
                                                <small class="form-text text-muted ms-2">Mỗi biến thể phải có ít nhất 1 size</small>
                                            </div>
                                        </div>
                                    </div>
                                    @php $vIndex++; @endphp
                                </div>
                                @empty
                                {{-- Nếu không có dữ liệu, hiển thị 1 biến thể trống giống create --}}
                                <div class="variant-item mb-4" data-variant-index="0">
                                    <div class="variant-header">
                                        <div class="variant-title">
                                            <h5 class="mb-0"><i class="fa fa-palette me-2"></i>Biến thể #1</h5>
                                            <small class="text-muted">Chọn màu sắc và thêm các size</small>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-variant" data-action="remove-variant">
                                            <i class="fa fa-trash me-1"></i>Xóa biến thể
                                        </button>
                                    </div>
                                    <div class="variant-content">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold"><i class="fa fa-palette me-1"></i>Màu sắc <span class="text-danger">*</span></label>
                                                <div class="color-select-container">
                                                    <select style="height:40px;" name="variants[0][mausac]" class="form-select color-select" required>
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
                                        <div class="size-variants-container">
                                            <div class="size-header">
                                                <h6 class="mb-3"><i class="fa fa-ruler me-2"></i>Danh sách size và thông tin</h6>
                                                <p class="text-muted mb-3"><i class="fa fa-lightbulb me-1"></i>Mỗi size sẽ có số lượng, giá gốc và giá khuyến mãi riêng biệt</p>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered size-variants-table">
                                                    <thead class="table-primary">
                                                        <tr>
                                                            <th width="20%"><i class="fa fa-ruler me-1"></i>Size</th>
                                                            <th width="20%"><i class="fa fa-boxes me-1"></i>Số lượng</th>
                                                            <th width="25%"><i class="fa fa-money-bill-wave me-1"></i>Giá gốc</th>
                                                            <th width="25%"><i class="fa fa-tags me-1"></i>Giá khuyến mãi</th>
                                                            <th width="10%"><i class="fa fa-cogs me-1"></i>Thao tác</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="size-variant-row">
                                                            <td>
                                                                <div class="size-select-container">
                                                                    <select style="height: 40px;" name="variants[0][sizes][0][size]" class="form-select size-select" required>
                                                                        <option value="">-- Chọn size --</option>
                                                                        @foreach($sizes as $size)
                                                                            <option value="{{ $size->id }}" data-size-name="{{ $size->name }}">{{ $size->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div class="size-preview"></div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="variants[0][sizes][0][so_luong]" class="form-control" value="1" min="0" required>
                                                                <small class="form-text text-muted">Số lượng tồn kho</small>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input type="text" name="variants[0][sizes][0][gia]" class="form-control price-input" placeholder="0">
                                                                    <span class="input-group-text">VND</span>
                                                                </div>
                                                                <small class="form-text text-muted">Bỏ trống để dùng giá sản phẩm chính</small>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input type="text" name="variants[0][sizes][0][gia_khuyenmai]" class="form-control price-input" placeholder="0">
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
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="size-actions">
                                                <button type="button" class="btn btn-info btn-sm add-size-row" data-variant-index="0" data-action="add-size-row">
                                                    <i class="fa fa-plus me-1"></i>Thêm size mới
                                                </button>
                                                <small class="form-text text-muted ms-2">Mỗi biến thể phải có ít nhất 1 size</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="add-variant-section">
                                <button type="button" id="add-variant" class="btn btn-primary btn-lg" data-action="add-variant">
                                    <i class="fa fa-plus me-2"></i>Thêm biến thể mới
                                </button>
                                <p class="text-muted mt-2">
                                    <i class="fa fa-info-circle me-1"></i>Thêm biến thể mới để tạo sản phẩm với nhiều màu sắc khác nhau
                                </p>
                            </div>
                        </div>
                    </div>



                    <!-- Trạng thái -->
                    <div class="card margin-bottom-4">
                        <div class="card-header" style="margin: 20px 0px;">
                            <h1 class="ctsph1">Thông tin hiển thị</h1>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 margin-bottom-4" style="margin: 26px 0px;">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select style="height: 40px;" name="status" id="status" class="form-select">
                                        <option value="1" {{ old('status', $sanpham->trangthai) == 1 ? 'selected' : '' }}>Kinh doanh</option>
                                        <option value="0" {{ old('status', $sanpham->trangthai) == 0 ? 'selected' : '' }}>Không kinh doanh</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end" style="margin: 30px 0px 60px 0px;">
                        <a href="{{ route('sanpham.index') }}" class="btn btn-secondary me-2">Quay lại</a>
                        <button type="submit" class="btn btn-success">Lưu cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// jQuery code cho các tính năng khác
$(document).ready(function() {
    // console.log('SanPham update page JavaScript loaded');
    
    // Debug: Kiểm tra jQuery
    if (typeof $ === 'undefined') {
        // console.error('jQuery is not loaded!');
        return;
    }
    
    // console.log('jQuery version:', $.fn.jquery);
    // Preview màu sắc
    $(document).on('change', '.color-select', function() {
        const selectedOption = $(this).find('option:selected');
        const colorName = selectedOption.data('color-name');
        const colorPreview = $(this).closest('.color-select-container').find('.color-preview');
        
        if (colorName) {
            // Map tên màu với mã màu
            const colorMap = {
                'Đỏ': '#dc3545',
                'Xanh': '#007bff',
                'Xanh lá': '#28a745',
                'Vàng': '#ffc107',
                'Tím': '#6f42c1',
                'Cam': '#fd7e14',
                'Hồng': '#e83e8c',
                'Xám': '#6c757d',
                'Đen': '#343a40',
                'Trắng': '#ffffff',
                'Nâu': '#795548',
                'Xanh dương': '#17a2b8',
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

        // Kiểm tra ít nhất một biến thể
        const variants = $('.variant-item').length;
        if (variants === 0) {
            errorMessages.push('Vui lòng thêm ít nhất một biến thể sản phẩm');
            isValid = false;
        }

        // Kiểm tra mỗi biến thể có ít nhất một size và validation giá
        $('.variant-item').each(function(index) {
            const sizes = $(this).find('.size-variant-row').length;
            if (sizes === 0) {
                errorMessages.push(`Biến thể #${index + 1} cần có ít nhất một size`);
                isValid = false;
            }
            
            // Kiểm tra giá trong mỗi size
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

    // Xử lý xóa biến thể
    $(document).on('click', '[data-action="remove-variant"]', function() {
        Swal.fire({
            title: 'Xác nhận xóa',
            text: 'Bạn có chắc chắn muốn xóa biến thể này?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).closest('.variant-item').fadeOut(300, function() {
                    $(this).remove();
                });
            }
        });
    });

    // Xử lý xóa size
    $(document).on('click', '[data-action="remove-size-row"]', function() {
        // console.log('Remove size clicked');
        const variantItem = $(this).closest('.variant-item');
        const sizeRows = variantItem.find('.size-variant-row');
        
        if (sizeRows.length <= 1) {
            Swal.fire({
                title: 'Cảnh báo',
                text: 'Mỗi biến thể phải có ít nhất 1 size',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        Swal.fire({
            title: 'Xác nhận xóa',
            text: 'Bạn có chắc chắn muốn xóa size này?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).closest('.size-variant-row').fadeOut(300, function() {
                    $(this).remove();
                });
            }
        });
    });

    // Xử lý thêm size mới
    $(document).on('click', '[data-action="add-size-row"]', function() {
        // console.log('Add size clicked');
        const variantIndex = $(this).data('variant-index');
        const tbody = $(this).closest('.size-variants-container').find('tbody');
        const newIndex = tbody.find('tr').length;
        
        const newRow = `
            <tr class="size-variant-row">
                <td>
                    <div class="size-select-container">
                        <select style="height: 40px;" name="variants[${variantIndex}][sizes][${newIndex}][size]" class="form-select size-select" required>
                            <option value="">-- Chọn size --</option>
                            @foreach($sizes as $size)
                                <option value="{{ $size->id }}" data-size-name="{{ $size->name }}">{{ $size->name }}</option>
                            @endforeach
                        </select>
                        <div class="size-preview"></div>
                    </div>
                </td>
                <td>
                    <input type="number" name="variants[${variantIndex}][sizes][${newIndex}][so_luong]" class="form-control" value="1" min="0" required>
                    <small class="form-text text-muted">Số lượng tồn kho</small>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" name="variants[${variantIndex}][sizes][${newIndex}][gia]" class="form-control price-input" placeholder="0" required>
                        <span class="input-group-text">VND</span>
                    </div>
                    <small class="form-text text-muted">Giá bán chính thức</small>
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
    });

    // Xử lý thêm biến thể mới
    $(document).on('click', '[data-action="add-variant"]', function() {
        // console.log('Add variant clicked');
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fa fa-palette me-1"></i>Màu sắc <span class="text-danger">*</span>
                            </label>
                            <div class="color-select-container">
                                <select style="height:40px;" name="variants[${variantIndex}][mausac]" class="form-select color-select" required>
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
                    <div class="size-variants-container">
                        <div class="size-header">
                            <h6 class="mb-3"><i class="fa fa-ruler me-2"></i>Danh sách size và thông tin</h6>
                            <p class="text-muted mb-3"><i class="fa fa-lightbulb me-1"></i>Mỗi size sẽ có số lượng, giá gốc và giá khuyến mãi riêng biệt</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered size-variants-table">
                                <thead class="table-primary">
                                    <tr>
                                        <th width="20%"><i class="fa fa-ruler me-1"></i>Size</th>
                                        <th width="20%"><i class="fa fa-boxes me-1"></i>Số lượng</th>
                                        <th width="25%"><i class="fa fa-money-bill-wave me-1"></i>Giá gốc</th>
                                        <th width="25%"><i class="fa fa-tags me-1"></i>Giá khuyến mãi</th>
                                        <th width="10%"><i class="fa fa-cogs me-1"></i>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="size-variant-row">
                                        <td>
                                            <div class="size-select-container">
                                                <select style="height: 40px;" name="variants[${variantIndex}][sizes][0][size]" class="form-select size-select" required>
                                                    <option value="">-- Chọn size --</option>
                                                    @foreach($sizes as $size)
                                                        <option value="{{ $size->id }}" data-size-name="{{ $size->name }}">{{ $size->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="size-preview"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" name="variants[${variantIndex}][sizes][0][so_luong]" class="form-control" value="1" min="0" required>
                                            <small class="form-text text-muted">Số lượng tồn kho</small>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="variants[${variantIndex}][sizes][0][gia]" class="form-control price-input" placeholder="0" required>
                                                <span class="input-group-text">VND</span>
                                            </div>
                                            <small class="form-text text-muted">Giá bán chính thức</small>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="variants[${variantIndex}][sizes][0][gia_khuyenmai]" class="form-control price-input" placeholder="0">
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
                                </tbody>
                            </table>
                        </div>
                        <div class="size-actions">
                            <button type="button" class="btn btn-info btn-sm add-size-row" data-variant-index="${variantIndex}" data-action="add-size-row">
                                <i class="fa fa-plus me-1"></i>Thêm size mới
                            </button>
                            <small class="form-text text-muted ms-2">Mỗi biến thể phải có ít nhất 1 size</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#variants-container').append(newVariant);
    });
});
</script>
@endpush
