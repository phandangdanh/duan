@extends('backend.layout')
@section('title', 'Chi tiết sản phẩm')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/css/product-create-enhanced.css') }}">
<style>
    .product-detail-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .product-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px 12px 0 0;
        margin-bottom: 20px;
    }

    .product-title {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: .2px;
    }

    .product-content {
        padding: 20px;
    }

    .product-info-item {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .product-info-label {
        font-weight: 600;
        color: #333;
        min-width: 120px;
        display: inline-block;
    }

    .product-image-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }

    .product-image-container {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .product-image-container:hover {
        transform: scale(1.05);
    }

    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .product-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-image-container:hover .product-image-overlay {
        opacity: 1;
    }

    .view-full-btn {
        color: white;
        background: rgba(255,255,255,0.2);
        padding: 8px 15px;
        border-radius: 20px;
        border: 1px solid white;
        cursor: pointer;
    }

    .detail-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .detail-table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 18px 16px;
        font-weight: 700;
        font-size: 15px;
    }

    .detail-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #eee;
        font-size: 15px;
    }

    .detail-table tbody tr:last-child td {
        border-bottom: none;
    }

    .detail-table tbody tr:hover {
        background-color: #f8f9ff;
    }

    .badge {
        padding: 8px 12px;
        border-radius: 20px;
        font-weight: 500;
    }

    .badge-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .badge-danger {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    /* Thêm style mới cho gallery */
    .image-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
        margin: 20px 0;
    }

    /* Container ảnh chính */
    .main-image-container {
        position: relative;
        width: 100%;
        margin-bottom: 20px;
    }

    /* Ảnh chính: lớn nhất, chiếm toàn bộ chiều ngang khu vực */
    .main-image {
        width: 100%;
        height: 600px;
        object-fit: contain;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Nút clear ảnh chính */
    .clear-main-image-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .clear-main-image-btn:hover {
        background: rgba(220, 53, 69, 1);
        transform: scale(1.1);
    }

    /* Thông báo không có ảnh chính */
    .no-main-image {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 400px;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .no-image-icon {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 16px;
    }

    .no-image-text h4 {
        color: #495057;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .no-image-text p {
        color: #6c757d;
        margin: 0;
        font-size: 14px;
    }



    /* Gallery ảnh phụ: hiển thị 4 cột với kích thước cố định */
    .thumbnail-gallery {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-top: 18px;
        justify-items: center;
    }

    .thumbnail-container {
        text-align: center;
    }

    .thumbnail {
        position: relative;
        cursor: pointer;
        border-radius: 50%; /* Hình tròn */
        overflow: hidden;
        width: 120px; /* Kích thước cố định */
        height: 120px; /* Kích thước cố định */
        background: #fff;
        box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        flex-shrink: 0;
    }

    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .thumbnail:hover img {
        transform: scale(1.1);
    }

    .thumbnail.active {
        border: 3px solid #667eea;
    }

    .thumbnail-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .thumbnail:hover .thumbnail-overlay {
        opacity: 1;
    }

    /* Nút xóa ảnh */
    .delete-image-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .delete-image-btn:hover {
        background: rgba(220, 53, 69, 1);
        transform: scale(1.1);
    }

    /* Caption cho thumbnail */
    .thumb-caption {
        text-align: center;
        margin-top: 8px;
        font-size: 12px;
        color: #666;
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Badge styles */
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-info {
        background-color: #17a2b8;
        color: #fff;
    }

    .image-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 15px;
    }

    .zoom-btn, .download-btn {
        padding: 8px 15px;
        border-radius: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .zoom-btn:hover, .download-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    /* Bảng thông tin tổng quan */
    .info-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.06);
        margin-bottom: 20px;
    }
    .info-table tbody tr td {
        padding: 14px 16px;
        border-bottom: 1px solid #eee;
        vertical-align: top;
        font-size: 15px;
    }
    .info-table tbody tr td:first-child {
        width: 220px;
        font-weight: 600;
        color: #495057;
        background: #f8f9fb;
    }

    .image-caption, .thumb-caption {
        text-align: center;
        font-size: 13px;
        color: #6c757d;
        margin-top: 6px;
        word-break: break-all;
    }
    
    /* Đảm bảo mỗi thumbnail có caption riêng */
    .thumbnail + .thumb-caption {
        margin-top: 8px;
        margin-bottom: 16px;
    }
/* phần còn lại giữ nếu có custom nhỏ */
</style>
@endpush

@section('content')
<div class="wrapper wrapper-content">
    <div class="product-detail-card">
        <div class="product-header">
            <h1 class="product-title">{{ $sanpham->tenSP }}</h1>
        </div>

        <div class="product-content">
            <!-- Bảng thông tin cơ bản -->
            <table class="info-table">
                <tbody>
                    <tr>
                        <td>Tên sản phẩm</td>
                        <td>{{ $sanpham->tenSP }}</td>
                    </tr>
                    <tr>
                        <td>Mã sản phẩm</td>
                        <td><span class="badge bg-info">{{ $sanpham->maSP }}</span></td>
                    </tr>
                    <tr>
                        <td>Danh mục</td>
                        <td>
                            @if($sanpham->danhmuc)
                                {{ $sanpham->danhmuc->name }}
                            @else
                                <span class="text-muted">Chưa có danh mục</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Trạng thái</td>
                        <td>
                            @php
                                // Kiểm tra trạng thái dựa trên tổng tồn kho (sản phẩm chính + variant)
                                $mainStock = $sanpham->soLuong ?? 0;
                                $variantStock = $sanpham->chitietsanpham->sum('soLuong');
                                $totalStock = $mainStock + $variantStock;
                                $hasStock = $totalStock > 0;
                                $hasDetails = $sanpham->chitietsanpham->count() > 0;
                            @endphp
                            
                            @if($sanpham->trangthai == 1 && $hasDetails && $hasStock)
                                <span class="badge badge-success">Đang kinh doanh</span>
                            @elseif($sanpham->trangthai == 1 && $hasDetails && !$hasStock)
                                <span class="badge badge-warning">Hết hàng</span>
                            @elseif($sanpham->trangthai == 1 && !$hasDetails)
                                <span class="badge badge-info">Chưa có biến thể</span>
                            @else
                                <span class="badge badge-danger">Ngừng kinh doanh</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Mô tả</td>
                        <td>{!! nl2br(e($sanpham->moTa)) !!}</td>
                    </tr>
                    <tr>
                        <td>Giá gốc (sản phẩm chính)</td>
                        <td>
                            @if($sanpham->base_price)
                                <strong>{{ number_format($sanpham->base_price, 0, ',', '.') }}đ</strong>
                            @else
                                <span class="text-muted">Chưa thiết lập</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Giá khuyến mãi (sản phẩm chính)</td>
                        <td>
                            @if($sanpham->base_sale_price)
                                <strong class="text-danger">{{ number_format($sanpham->base_sale_price, 0, ',', '.') }}đ</strong>
                            @else
                                <span class="text-muted">Chưa thiết lập</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Chi tiết sản phẩm -->
            @if($sanpham->chitietsanpham && count($sanpham->chitietsanpham) > 0)
                <div class="product-info-item">
                    <span class="product-info-label">Chi tiết sản phẩm:</span>
                    <div class="table-responsive mt-3">
                        <table class="detail-table">
                            <thead>
                                <tr>
                                    <th>Mã chi tiết</th>
                                    <th>Màu sắc</th>
                                    <th>Size</th>
                                    <th>Số lượng</th>
                                    <th>Giá bán</th>
                                    <th>Giá khuyến mãi</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sanpham->chitietsanpham as $chitiet)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">SP{{ str_pad($chitiet->id, 3, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>
                                        @if($chitiet->mausac)
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="color-preview" style="
                                                    width: 20px;
                                                    height: 20px;
                                                    border-radius: 50%;
                                                    display: inline-block;
                                                    background-color: {{ $chitiet->mausac->mota }};
                                                    border: 2px solid #ddd;
                                                "></span>
                                                <span>{{ $chitiet->mausac->mota }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">Chưa có màu sắc</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($chitiet->size)
                                            <span class="badge bg-info">{{ $chitiet->size->mota }}</span>
                                        @else
                                            <span class="text-muted">Chưa có size</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Tổng tồn kho = Số lượng sản phẩm chính + Số lượng variant hiện tại
                                            $mainStock = $sanpham->soLuong ?? 0;
                                            $variantStock = $chitiet->soluong ?? 0;
                                            $totalStock = $mainStock + $variantStock;
                                        @endphp
                                        @if($totalStock > 0)
                                            <span class="badge bg-success">{{ number_format($totalStock) }}</span>
                                            @if($mainStock > 0)
                                                <br><small class="text-muted">Chính: {{ $mainStock }}</small>
                                            @endif
                                            @if($variantStock > 0)
                                                <br><small class="text-muted">Variant: {{ $variantStock }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-danger">Hết hàng</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $displayPrice = $chitiet->gia && $chitiet->gia > 0 ? $chitiet->gia : null;
                                        @endphp
                                        @if($displayPrice)
                                            {{ number_format($displayPrice, 0, ',', '.') }}đ
                                        @else
                                            @if($sanpham->base_price)
                                                <span class="text-muted">Theo giá gốc: {{ number_format($sanpham->base_price, 0, ',', '.') }}đ</span>
                                            @else
                                                <span class="text-muted">Theo giá sản phẩm chính</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($chitiet->gia_khuyenmai)
                                            <span class="text-danger">
                                                {{ number_format($chitiet->gia_khuyenmai, 0, ',', '.') }}đ
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Tổng tồn kho = Số lượng sản phẩm chính + Số lượng variant hiện tại
                                            $mainStock = $sanpham->soLuong ?? 0;
                                            $variantStock = $chitiet->soluong ?? 0;
                                            $totalStock = $mainStock + $variantStock;
                                        @endphp
                                        @if($totalStock > 0)
                                            <span class="badge bg-success">Còn hàng</span>
                                        @else
                                            <span class="badge bg-danger">Hết hàng</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    Sản phẩm chưa có biến thể màu sắc và size
                </div>
            @endif

                         <div class="product-info-item">
                 <span class="product-info-label">Hình ảnh sản phẩm:</span>
                 @if($sanpham->hinhanh && count($sanpham->hinhanh) > 0)
                     <div class="image-section">
                         <!-- Ảnh chính -->
                         @php 
                             $main = $sanpham->hinhanh->firstWhere('is_default', 1);
                         @endphp
                         
                         @if($main)
                             <div class="main-image-container">
                                 <img id="main-product-image" 
                                      src="{{ asset($main->url) }}" 
                                      class="main-image" 
                                      alt="Hình ảnh chính sản phẩm">
                                 
                                 <!-- Nút xóa ảnh chính (chỉ hiện khi đang xem ảnh phụ) -->
                                 <button type="button" 
                                         id="clear-main-image-btn" 
                                         class="clear-main-image-btn" 
                                         onclick="clearMainImage()"
                                         title="Trở về ảnh chính mặc định"
                                         style="display: none;">
                                     <i class="fa fa-times"></i>
                                 </button>
                             </div>
                             <div class="image-caption" id="main-image-caption">Ảnh chính: {{ basename($main->url) }}</div>
                             
                             <!-- Thanh công cụ ảnh -->
                             <div class="image-actions">
                                 <a href="{{ asset($main->url) }}" 
                                    id="zoom-btn"
                                    class="zoom-btn" 
                                    target="_blank">
                                     <i class="fa fa-search-plus"></i> Phóng to
                                 </a>
                                 <a href="{{ asset($main->url) }}" 
                                    id="download-btn"
                                    class="download-btn" 
                                    download>
                                     <i class="fa fa-download"></i> Tải xuống
                                 </a>
                             </div>
                         @else
                             <!-- Thông báo không có ảnh chính hoặc hiển thị ảnh phụ được chọn -->
                             <div id="no-main-image-container">
                                 <div class="no-main-image" id="no-main-image-placeholder">
                                     <div class="no-image-icon">
                                         <i class="fa fa-image"></i>
                                     </div>
                                     <div class="no-image-text">
                                         <h4>Không có ảnh chính</h4>
                                         <p>Sản phẩm chưa có ảnh chính được thiết lập</p>
                                     </div>
                                 </div>
                                 
                                 <!-- Ảnh phụ được chọn (ẩn ban đầu) -->
                                 <div class="main-image-container" id="selected-image-container" style="display: none;">
                                     <img id="selected-product-image" 
                                          src="" 
                                          class="main-image" 
                                          alt="Ảnh phụ được chọn">
                                     
                                     <!-- Nút tắt ảnh phụ -->
                                     <button type="button" 
                                             id="close-selected-image-btn" 
                                             class="clear-main-image-btn" 
                                             onclick="closeSelectedImage()"
                                             title="Tắt ảnh phụ">
                                         <i class="fa fa-times"></i>
                                     </button>
                                 </div>
                                 <div class="image-caption" id="selected-image-caption" style="display: none;"></div>
                                 
                                 <!-- Thanh công cụ ảnh (ẩn ban đầu) -->
                                 <div class="image-actions" id="selected-image-actions" style="display: none;">
                                     <a href="" 
                                        id="selected-zoom-btn"
                                        class="zoom-btn" 
                                        target="_blank">
                                         <i class="fa fa-search-plus"></i> Phóng to
                                     </a>
                                     <a href="" 
                                        id="selected-download-btn"
                                        class="download-btn" 
                                        download>
                                         <i class="fa fa-download"></i> Tải xuống
                                     </a>
                                 </div>
                             </div>
                         @endif

                         <!-- Gallery thumbnail (4 cột) - hiển thị tất cả ảnh phụ -->
                         @php
                             $thumbnails = $sanpham->hinhanh->where('is_default', '!=', 1);
                         @endphp
                         @if($thumbnails->count() > 0)
                             <div class="thumbnail-gallery">
                                 @foreach($thumbnails as $index => $img)
                                     <div class="thumbnail-container">
                                         <div class="thumbnail" 
                                              data-image="{{ asset($img->url) }}"
                                              data-image-id="{{ $img->id }}"
                                              data-image-name="{{ basename($img->url) }}">
                                             <img src="{{ asset($img->url) }}" alt="Ảnh phụ {{ $index + 1 }}">
                                             
                                             <!-- Overlay khi hover -->
                                             <div class="thumbnail-overlay">
                                                 <i class="fa fa-expand text-white"></i>
                                             </div>
                                         </div>
                                         <div class="thumb-caption">{{ basename($img->url) }}</div>
                                     </div>
                                 @endforeach
                             </div>
                         @else
                             <div class="text-muted text-center mt-3">
                                 <i class="fa fa-image"></i> Không có ảnh phụ
                             </div>
                         @endif
                         
                         <!-- Thông báo -->
                         <div class="text-center mt-3">
                             <small class="text-muted">
                                 <i class="fa fa-info-circle"></i> 
                                 Click ảnh phụ để xem trên ảnh chính, click "×" để tắt
                             </small>
                         </div>
                     </div>
                 @else
                     <div class="text-muted mt-2">Chưa có hình ảnh</div>
                 @endif
             </div>
            <div class="action-buttons" style="margin-top: 20px;">
                <a href="{{ route('sanpham.edit', $sanpham->id) }}" class="btn btn-warning">
                    <i class="fa fa-edit"></i> Sửa sản phẩm
                </a>
                <a href="{{ route('sanpham.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
                 </div>
     </div>
 </div>


 @endsection

@push('scripts')
<script>
// Lưu ảnh chính mặc định
let defaultMainImage = null;
let defaultMainImageName = null;

document.addEventListener('DOMContentLoaded', function() {
    // Lưu ảnh chính mặc định (nếu có)
    const mainImage = document.getElementById('main-product-image');
    const mainImageCaption = document.getElementById('main-image-caption');
    
    if (mainImage && mainImageCaption) {
        defaultMainImage = mainImage.src;
        defaultMainImageName = mainImageCaption.textContent;
    }
    
    // Xử lý click vào thumbnail để load lên ảnh chính
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function(e) {
            const imageUrl = this.dataset.image;
            const imageName = this.dataset.imageName;
            
            // Nếu có ảnh chính, cập nhật nó
            if (mainImage && mainImageCaption) {
                mainImage.src = imageUrl;
                mainImageCaption.textContent = 'Ảnh phụ: ' + imageName;
                
                // Cập nhật links phóng to và tải xuống
                const zoomBtn = document.getElementById('zoom-btn');
                const downloadBtn = document.getElementById('download-btn');
                if (zoomBtn) zoomBtn.href = imageUrl;
                if (downloadBtn) downloadBtn.href = imageUrl;
                
                // Hiện nút clear
                const clearBtn = document.getElementById('clear-main-image-btn');
                if (clearBtn) clearBtn.style.display = 'block';
            } else {
                // Nếu không có ảnh chính, hiển thị ảnh phụ ở vị trí ảnh chính
                showSelectedImage(imageUrl, imageName);
            }
            
            // Cập nhật trạng thái active
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

// Hàm clear ảnh chính về mặc định
function clearMainImage() {
    const mainImage = document.getElementById('main-product-image');
    const mainImageCaption = document.getElementById('main-image-caption');
    const clearBtn = document.getElementById('clear-main-image-btn');
    const zoomBtn = document.getElementById('zoom-btn');
    const downloadBtn = document.getElementById('download-btn');
    
    // Chỉ xử lý nếu có ảnh chính mặc định
    if (mainImage && mainImageCaption && defaultMainImage) {
        // Trở về ảnh chính mặc định
        mainImage.src = defaultMainImage;
        mainImageCaption.textContent = defaultMainImageName;
        
        // Cập nhật links
        if (zoomBtn) zoomBtn.href = defaultMainImage;
        if (downloadBtn) downloadBtn.href = defaultMainImage;
        
        // Ẩn nút clear
        if (clearBtn) clearBtn.style.display = 'none';
    }
    
    // Bỏ active tất cả thumbnail
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(t => t.classList.remove('active'));
}

// Hàm hiển thị ảnh phụ ở vị trí ảnh chính (khi không có ảnh chính)
function showSelectedImage(imageUrl, imageName) {
    const placeholder = document.getElementById('no-main-image-placeholder');
    const selectedContainer = document.getElementById('selected-image-container');
    const selectedImage = document.getElementById('selected-product-image');
    const selectedCaption = document.getElementById('selected-image-caption');
    const selectedActions = document.getElementById('selected-image-actions');
    const selectedZoomBtn = document.getElementById('selected-zoom-btn');
    const selectedDownloadBtn = document.getElementById('selected-download-btn');
    
    // Ẩn placeholder
    placeholder.style.display = 'none';
    
    // Hiện ảnh phụ được chọn
    selectedImage.src = imageUrl;
    selectedCaption.textContent = 'Ảnh phụ: ' + imageName;
    selectedCaption.style.display = 'block';
    
    // Cập nhật links
    selectedZoomBtn.href = imageUrl;
    selectedDownloadBtn.href = imageUrl;
    selectedActions.style.display = 'flex';
    
    // Hiện container
    selectedContainer.style.display = 'block';
}

// Hàm đóng ảnh phụ được chọn (trở về placeholder)
function closeSelectedImage() {
    const placeholder = document.getElementById('no-main-image-placeholder');
    const selectedContainer = document.getElementById('selected-image-container');
    const selectedCaption = document.getElementById('selected-image-caption');
    const selectedActions = document.getElementById('selected-image-actions');
    
    // Ẩn ảnh phụ được chọn
    selectedContainer.style.display = 'none';
    selectedCaption.style.display = 'none';
    selectedActions.style.display = 'none';
    
    // Hiện placeholder
    placeholder.style.display = 'flex';
    
    // Bỏ active tất cả thumbnail
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(t => t.classList.remove('active'));
}




</script>
@endpush

