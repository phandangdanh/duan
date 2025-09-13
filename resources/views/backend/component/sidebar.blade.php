@php
$segment = request()->segment(1);
@endphp

<div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element"> <span>
                                <img alt="image" class="img-circle" src="{{ asset('backend/img/phandangdanh.jpg') }}"
                                    style="width:48px;height:48px" />
                        </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs">
                                    <strong class="font-bold">Phan
                                        Đăng Danh
                                    </strong>
                                </span> <span class="text-muted text-xs block">Art Director
                                    <b class="caret"></b></span> </span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="profile.html">Profile</a></li>
                            <li><a href="contacts.html">Contacts</a></li>
                            <li><a href="mailbox.html">Mailbox</a></li>
                            <li class="divider"></li>
                            <li><a href="login.html">Logout</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        IN+
                    </div>
                </li>
                <li class="">
                    <a href="{{ route('admin.dashboard') }}"><i class="fa fa-bar-chart"></i> <span class="nav-label">Thống kê tổng quan</span></a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-user"></i> <span class="nav-label">Quản lý user</span><span
                            class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li><a href="{{ route('user.index') }}">Danh sách user</a></li>
                        <li><a href="{{ route('user.create') }}">Thêm user mới</a></li>
                        <li><a href="{{ route('user.statistics') }}">Thống kê user</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#"><i class="fa fa-list"></i> <span class="nav-label">Quản lý danh mục</span><span
                            class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li><a href="{{ route('danhmuc.index') }}">Danh sách danh mục</a></li>
                        <li><a href="{{ route('danhmuc.create') }}">Thêm danh mục mới</a></li>
                        <li><a href="{{ route('danhmuc.statistics') }}">Thống kê danh mục</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#"><i class="fa fa-shopping-bag"></i> 
                        <span class="nav-label">Quản lý sản phẩm</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        <li><a href="{{ route('sanpham.index') }}">Danh sách sản phẩm</a></li>
                        <li><a href="{{ route('sanpham.create') }}">Thêm sản phẩm mới</a></li>
                        <li><a href="{{ route('sanpham.statistics.page') }}">Thống kê sản phẩm</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#"><i class="fa fa-shopping-cart"></i> 
                        <span class="nav-label">Quản lý đơn hàng</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        <li><a href="{{ route('admin.donhang.index') }}">Danh sách đơn hàng</a></li>
                        <li><a href="{{ route('admin.donhang.statistics') }}">Thống kê đơn hàng</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#"><i class="fa fa-gift"></i> 
                        <span class="nav-label">Quản lý voucher</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        <li><a href="{{ route('admin.vouchers.index') }}">Danh sách voucher</a></li>
                        <li><a href="{{ route('admin.vouchers.create') }}">Thêm voucher mới</a></li>
                        <li><a href="{{ route('admin.vouchers.statistics') }}">Thống kê voucher</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div id="page-wrapper" class="gray-bg dashbard-1">