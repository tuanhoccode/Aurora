@extends('admin.layouts.app')

@section('title', 'Product Management')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Product Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Products</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="#" class="btn btn-warning">
                <i class="fas fa-exclamation-triangle me-1"></i> Low Stock
            </a>
            <a href="#" class="btn btn-success">
                <i class="fas fa-chart-line me-1"></i> Best Selling
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Product
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-1">Total Products</h6>
                    <h2 class="mb-0">123</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-1">Active Products</h6>
                    <h2 class="mb-0">95</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-1">Low Stock</h6>
                    <h2 class="mb-0">7</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-1">Out of Stock</h6>
                    <h2 class="mb-0">3</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Product List Card -->
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Product List</h5>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <form action="#" method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search by name or SKU">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option value="">All Categories</option>
                            <option>Điện thoại</option>
                            <option>Laptop</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                        <a href="#" class="btn btn-light">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Product Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <img src="https://via.placeholder.com/50" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-medium">iPhone 14 Pro Max</div>
                                <small class="text-muted">Điện thoại cao cấp của Apple với nhiều tính năng hiện đại...</small>
                            </td>
                            <td><span class="badge bg-light text-dark">IPH14PRO</span></td>
                            <td>Điện thoại</td>
                            <td>
                                <span class="text-decoration-line-through text-muted">$1200.00</span><br>
                                <span class="text-danger fw-bold">$999.00</span>
                            </td>
                            <td><span class="badge bg-success">20</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.products.edit') }}" class="btn btn-sm btn-light" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-light text-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                (Dữ liệu chỉ là mẫu giao diện – không kết nối backend)
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">Showing 1 to 10 of 100 entries</div>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item disabled"><a class="page-link">«</a></li>
                        <li class="page-item active"><a class="page-link">1</a></li>
                        <li class="page-item"><a class="page-link">2</a></li>
                        <li class="page-item"><a class="page-link">3</a></li>
                        <li class="page-item"><a class="page-link">»</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection
