@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('sidebar')
    <div class="col-md-3 col-lg-2 d-md-block bg-dark text-light sidebar">
        <div class="sidebar-header text-center py-3">
            <h3>Quản trị viên</h3>
        </div>

        <div class="list-group list-group-flush">
            <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action text-light">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action text-light">
                <i class="fas fa-boxes me-2"></i> Sản phẩm
            </a>
            <a href="#" class="list-group-item list-group-item-action text-light">
                <i class="fas fa-th-list me-2"></i> Danh mục
            </a>
            <a href="#" class="list-group-item list-group-item-action text-light">
                <i class="fas fa-images me-2"></i> Banners
            </a>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
        }
        .sidebar .list-group-item {
            background-color: #343a40;
            border: none;
        }
        .sidebar .list-group-item:hover {
            background-color: #495057;
        }
        main {
            margin-left: 200px; /* Để lại khoảng trống cho sidebar */
        }
        @media (max-width: 767.98px) {
            .sidebar {
                position: static;
                height: auto;
            }
            main {
                margin-left: 0;
            }
        }
    </style>
@endsection