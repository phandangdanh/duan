@extends('backend.layout')
@section('title', 'Trang Danh mục')

@section('css')
    <link rel="stylesheet" href="{{ asset('backend/css/danhmuc.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/danhmuc-table.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
    <link href="{{ asset('backend/css/plugins/switchery/switchery.css') }}" rel="stylesheet">
@endsection

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>{{ config('apps.danhmuc.title', 'Danh mục') }}</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('dashboard.index') }}">Dashboard</a>
                    </li>
                    <li class="active">
                        <a href="{{ route('danhmuc.index') }}">
                            <strong>{{ $config['seo']['title'] ?? 'Danh mục' }}</strong>
                        </a>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2"></div>
        </div>
        <!-- Thống kê tổng quan -->
        <div class="row mt10">
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-content">
                        <h5 class="m-b-none">Tổng danh mục</h5>
                        <h2 class="no-margins text-primary">{{ number_format($stats['total_categories']) }}</h2>
                        <small>Tất cả danh mục</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-content">
                        <h5 class="m-b-none">Đang hoạt động</h5>
                        <h2 class="no-margins text-success">{{ number_format($stats['active_categories']) }}</h2>
                        <small>Danh mục active</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-content">
                        <h5 class="m-b-none">Ngừng hoạt động</h5>
                        <h2 class="no-margins text-warning">{{ number_format($stats['inactive_categories']) }}</h2>
                        <small>Danh mục inactive</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-content">
                        <h5 class="m-b-none">Danh mục gốc</h5>
                        <h2 class="no-margins text-info">{{ number_format($stats['root_categories']) }}</h2>
                        <small>Danh mục cha</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>
                            <a href="{{ route('danhmuc.index') }}" style="color: inherit; text-decoration: none;">
                                {{ $config['seo']['table'] ?? 'Danh sách danh mục' }}
                            </a>
                        </h5>
                        @include('backend.danhmuc.component.toolbox')
                    </div>
                    <div class="ibox-content">
                        @include('backend.danhmuc.component.filter')
                        @include('backend.danhmuc.component.table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('backend/library/danhmuc-bulk-action.js') }}"></script>
@endsection