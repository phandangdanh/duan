<!DOCTYPE html>
<html>

<head>
    @include('backend.component.head')
    <title>@yield('title', 'Thống kê tổng quan')</title>
    <link rel="stylesheet" href="{{ asset('backend/css/voucher-admin.css') }}">
    @yield('css')
    @stack('styles')
</head>

<body>
    @include('backend.component.sidebar')
    @include('backend.component.navbar')
    
    <!-- Breadcrumb -->
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>@yield('title', 'Thống kê tổng quan')</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Trang chủ</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>@yield('title', 'Thống kê tổng quan')</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
        </div>
    </div>
    
    @yield('content')
    @include('backend.component.footer')
    @include('backend.component.script')
    @yield('js')
    @stack('scripts')
</body>

</html>
