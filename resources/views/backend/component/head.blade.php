<!DOCTYPE html>
<html>

<head>
    <base href="{{ env('APP_URL') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (isset($config['css']) && is_array($config['css']))
        @foreach ($config['css'] as $key => $value)
            {!! '<link rel="stylesheet" href="' . asset($value) . '">' !!}
        @endforeach
    @endif  
    <link href="{{ asset('backend/css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <!-- Font Awesome 5 CDN for fas icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('backend/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/customize.css') }}" rel="stylesheet">
    
    <style>
    /* Fix footer positioning */
    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #f3f3f4;
        border-top: 1px solid #e7eaec;
        padding: 10px 20px;
        z-index: 1000;
        text-align: center;
    }
    
    .footer .pull-right {
        float: right;
    }
    
    .footer div:first-child {
        float: left;
    }
    
    /* Ensure content doesn't overlap with footer */
    body {
        padding-bottom: 60px;
    }
    
    .wrapper-content {
        min-height: calc(100vh - 120px);
    }
    
    /* Fix layout structure - IMPORTANT FIXES */
    #wrapper {
        position: relative;
        min-height: 100vh;
    }
    
    #page-wrapper {
        margin-left: 220px;
        min-height: 100vh;
        padding: 20px;
        position: relative;
    }
    
    /* Sidebar fixes - ensure full height */
    .navbar-static-side {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 220px !important;
        height: 100% !important;
        min-height: 100vh !important;
        z-index: 1001 !important;
        background: #2f4050 !important;
        overflow-y: auto !important;
    }
    
    /* Ensure sidebar content fills full height */
    .sidebar-collapse {
        height: 100% !important;
        min-height: 100vh !important;
    }
    
    /* Navbar positioning */
    .navbar-static-top {
        margin-left: 220px !important;
        position: relative !important;
        z-index: 1000 !important;
    }
    
    /* Force sidebar background to extend full height */
    .navbar-static-side::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #2f4050;
        z-index: -1;
    }
    
    /* Fix checkbox styling */
    .form-check-input {
        width: 18px !important;
        height: 18px !important;
        margin: 0 !important;
        cursor: pointer !important;
        border: 2px solid #ddd !important;
        border-radius: 3px !important;
        background-color: #fff !important;
        transition: all 0.2s ease !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
    }
    
    .form-check-input:checked {
        background-color: #1ab394 !important;
        border-color: #1ab394 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e") !important;
        background-size: 14px !important;
        background-repeat: no-repeat !important;
        background-position: center !important;
    }
    
    .form-check-input.checked {
        background-color: #1ab394 !important;
        border-color: #1ab394 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e") !important;
        background-size: 14px !important;
        background-repeat: no-repeat !important;
        background-position: center !important;
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(26, 179, 148, 0.25) !important;
        outline: none !important;
    }
    
    /* Table styling */
    .table th {
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #dee2e6 !important;
        font-weight: 600 !important;
    }
    
    .table td {
        vertical-align: middle !important;
    }
    
    /* Show delete button for all orders, but disable for non-cancelled orders */
    .delete-btn {
        display: inline-block !important;
    }
    
    /* Fix bulk action buttons styling */
    .btn:disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
    }
    
    .btn:disabled:hover {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
    }
    
    /* Ensure bulk buttons are visible when enabled */
    .btn:not(:disabled) {
        opacity: 1 !important;
        cursor: pointer !important;
    }
    </style>
    
    <!-- Toastr style -->
    <link href="{{ asset('backend/css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <!-- Gritter -->
    <link href="{{ asset('backend/js/plugins/gritter/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('backend/font-awesome/css/font-awesome.min.css') }}">
    <link href="{{ asset('backend/css/plugins/switchery/switchery.css') }}" rel="stylesheet">
    


    <script src="{{ asset('backend/js/jquery-3.1.1.min.js') }}"></script>
    <script>
        window.$province_id = "{{ old('province_id', $user->province_id ?? '') }}";
        window.$district_id = "{{ old('district_id', $user->district_id ?? '') }}";
        window.$ward_id = "{{ old('ward_id', $user->ward_id ?? '') }}";
    </script>
    {{-- <script src="{{ asset('backend/library/location.js') }}"></script> --}}
    <script src="{{ asset('backend/library/user-toggle-status.js') }}"></script>
    <script src="{{ asset('backend/library/danhmuc-bulk-action.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('backend/js/plugins/switchery/switchery.js') }}"></script>
    
    <!-- Load sanpham-admin-fixed.js early so pages can hook showConfirm -->
    <script src="{{ asset('backend/js/sanpham-admin-fixed.js') }}?v={{ time() }}&cache={{ rand(1000,9999) }}"></script>
    
    <!-- Load donhang-admin.js for order management -->
    @if (request()->is('admin/donhang*'))
    <script src="{{ asset('backend/js/donhang-admin.js') }}?v={{ time() }}&cache={{ rand(1000,9999) }}"></script>
    @endif
    
    <script>
        // Global confirm helper (Bootstrap modal if available, else native)
        window.showConfirm = function(message){
            return new Promise(function(resolve){
                var modalEl = document.getElementById('confirmModal');
                // If page has confirmModal, use it
                if(modalEl){
                    var $msg = document.querySelector('#confirmModal .confirm-message');
                    if($msg){ $msg.textContent = message || 'Bạn có chắc chắn?'; }
                    var agreeBtn = document.getElementById('confirmModalAgree');
                    function cleanup(){
                        if(window.bootstrap && bootstrap.Modal.getInstance(modalEl)){
                            bootstrap.Modal.getInstance(modalEl).hide();
                        } else if (window.jQuery && typeof jQuery('#confirmModal').modal === 'function'){
                            jQuery('#confirmModal').modal('hide');
                        }
                        if(agreeBtn){ agreeBtn.removeEventListener('click', onAgree); }
                        modalEl.removeEventListener('hidden.bs.modal', onCancel);
                    }
                    function onAgree(){ cleanup(); resolve(true); }
                    function onCancel(){ cleanup(); resolve(false); }
                    if(window.bootstrap && bootstrap.Modal){
                        var modal = new bootstrap.Modal(modalEl, {backdrop:'static', keyboard:false});
                        if(agreeBtn){ agreeBtn.addEventListener('click', onAgree); }
                        modalEl.addEventListener('hidden.bs.modal', onCancel, {once:true});
                        modal.show();
                    } else if (window.jQuery && typeof jQuery('#confirmModal').modal === 'function'){
                        if(agreeBtn){ agreeBtn.addEventListener('click', onAgree); }
                        jQuery('#confirmModal').one('hidden.bs.modal', onCancel).modal({backdrop:'static', keyboard:false, show:true});
                    } else {
                        resolve(window.confirm(message || 'Bạn có chắc chắn?'));
                    }
                } else {
                    // Fallback
                    resolve(window.confirm(message || 'Bạn có chắc chắn?'));
                }
            });
        };
    </script>
    
    <!-- Global image error handler -->
    <script>
        // Xử lý lỗi hình ảnh toàn cục
        document.addEventListener('DOMContentLoaded', function() {
            // Thêm onerror cho tất cả hình ảnh
            var images = document.querySelectorAll('img');
            images.forEach(function(img) {
                if (!img.hasAttribute('onerror')) {
                    img.setAttribute('onerror', "this.src='{{ asset('backend/images/no-image.png') }}'");
                }
            });
            
            // Xử lý lỗi hình ảnh động
            document.addEventListener('error', function(e) {
                if (e.target.tagName === 'IMG') {
                    var src = e.target.getAttribute('src');
                    if (src && !src.includes('no-image.png')) {
                        console.warn('Image failed to load:', src);
                        e.target.src = '{{ asset('backend/images/no-image.png') }}';
                    }
                }
            }, true);
        });
    </script>
    
    
    
    <style>
    .onoffswitch {
        position: relative; width: 50px;
        -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
    }
    .onoffswitch-checkbox {
        display: none;
    }
    .onoffswitch-label {
        display: block; overflow: hidden; cursor: pointer;
        border: 2px solid #999999; border-radius: 20px;
    }
    .onoffswitch-inner {
        display: block; width: 200%; margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    .onoffswitch-inner:before, .onoffswitch-inner:after {
        display: block; float: left; width: 50%; height: 20px; padding: 0; line-height: 20px;
        font-size: 10px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
        box-sizing: border-box;
    }
    .onoffswitch-inner:before {
        content: "ON";
        padding-left: 10px;
        background-color: #1AB394; color: #FFFFFF;
    }
    .onoffswitch-inner:after {
        content: "OFF";
        padding-right: 10px;
        background-color: #EEEEEE; color: #999999;
        text-align: right;
    }
    .onoffswitch-switch {
        display: block; width: 18px; margin: 1px;
        background: #FFFFFF;
        position: absolute; top: 0; bottom: 0;
        right: 28px;
        border: 2px solid #999999; border-radius: 20px;
        transition: all 0.3s ease-in 0s;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 0px;
    }
    
    /* User Status Button Styling */
    .status-container {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }
    
    .status-btn {
        border-radius: 20px !important;
        padding: 6px 12px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        transition: all 0.3s ease !important;
        border: none !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        min-width: 80px !important;
    }
    
    .status-btn.btn-success {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        color: white !important;
    }
    
    .status-btn.btn-success:hover {
        background: linear-gradient(135deg, #218838, #1e7e34) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3) !important;
    }
    
    .status-btn.btn-danger {
        background: linear-gradient(135deg, #dc3545, #e74c3c) !important;
        color: white !important;
    }
    
    .status-btn.btn-danger:hover {
        background: linear-gradient(135deg, #c82333, #d63031) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3) !important;
    }
    
    .status-btn i {
        margin-right: 4px !important;
        font-size: 11px !important;
    }
    
    /* Switch styling */
    .js-switch {
        transform: scale(0.8) !important;
    }
    
    /* Statistics Dashboard Styling */
    .widget.style1 {
        border-radius: 10px !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
        transition: transform 0.3s ease !important;
    }
    
    .widget.style1:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
    }
    
    .widget.style1 .fa-5x {
        opacity: 0.8 !important;
        transition: opacity 0.3s ease !important;
    }
    
    .widget.style1:hover .fa-5x {
        opacity: 1 !important;
    }
    
    .ibox {
        border-radius: 10px !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }
    
    .ibox-title {
        border-radius: 10px 10px 0 0 !important;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
    }
    
    .table th {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
        border: none !important;
        font-weight: 600 !important;
    }
    
    .badge {
        border-radius: 15px !important;
        padding: 5px 10px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }
    
    .badge-primary {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
    }
    
    .badge-info {
        background: linear-gradient(135deg, #17a2b8, #138496) !important;
    }
    
    .badge-success {
        background: linear-gradient(135deg, #28a745, #1e7e34) !important;
    }
    
    .badge-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800) !important;
    }
    
    .badge-danger {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
    }
    
    /* Date Filter Styling */
    .form-inline .form-group {
        margin-bottom: 10px !important;
    }
    
    .form-inline .control-label {
        font-weight: 600 !important;
        color: #333 !important;
    }
    
    .form-inline .form-control {
        border-radius: 5px !important;
        border: 1px solid #ddd !important;
        padding: 8px 12px !important;
        height: 40px;
    }
    
    .form-inline .form-control:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }
    
    .btn-group .btn {
        border-radius: 5px !important;
        margin-right: 5px !important;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0 !important;
    }
    
    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
        border-color: #007bff !important;
        color: white !important;
    }
    
    /* Loading Animation */
    .sk-loading {
        position: relative !important;
        opacity: 0.7 !important;
    }
    
    .sk-loading::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        background: rgba(255, 255, 255, 0.8) !important;
        z-index: 1000 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    .sk-loading::after {
        content: 'Đang tải dữ liệu...' !important;
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        z-index: 1001 !important;
        display: inline-block !important;
        white-space: nowrap !important;
        background: #007bff !important;
        color: #fff !important;
        padding: 6px 12px !important;
        border-radius: 4px !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
        pointer-events: none !important;
        width: auto !important;
        height: auto !important;
        line-height: 1.2 !important;
        font-size: 12px !important;
        text-align: center !important;
        min-width: 140px !important;
        max-width: 220px !important;
        text-align: center !important;
        max-height: 30px !important;
    }
    
    /* Spinner Animation */
    .fa-spin {
        animation: fa-spin 1s infinite linear !important;
    }
    
    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>
    <!-- Page-specific CSS (optional) -->
    @stack('page_css')



</head>
