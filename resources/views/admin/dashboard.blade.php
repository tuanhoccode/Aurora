@extends('admin.layouts.app')


@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/font-awesome-pro.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
<style>
    .dashboard-header {
        background: linear-gradient(45deg, var(--bs-light), #ffffff);
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }
    .stats-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 1rem;
        overflow: hidden;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.1);
    }
    .stats-icon {
        width: 4rem;
        height: 4rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 1rem;
        font-size: 1.5rem;
    }
    .chart-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }
    .chart-card .card-body {
        padding: 1.5rem; /* Slightly reduced padding */
    }
    .table-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }
    .table-card .table {
        margin-bottom: 0;
    }
    .table-card .table th {
        border-top: none;
        font-weight: 600;
        color: var(--bs-gray-700);
    }
    .badge {
        padding: 0.5em 1em;
        font-weight: 500;
    }
</style>
@endpush



@section('content')
    <div class="dashboard-header animate__animated animate__fadeIn">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2 fw-bold">Bảng Điều Khiển</h1>
                <p class="text-muted mb-0">Chào mừng trở lại, {{ Auth::user()->name ?? 'Admin' }}!</p>
            </div>
            <div class="date-info">
                <span class="badge bg-light text-dark shadow-sm">
                    <i class="far fa-calendar-alt me-2"></i>
                    {{ now()->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row g-4 mb-4">
        <!-- Card Sản Phẩm -->
        <div class="col-md-3">
            <div class="stats-card bg-white shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-primary bg-opacity-10">
                            <i class="far fa-tshirt text-primary"></i>
                        </div>
                        <div class="stats-trend text-success fw-bold">
                            <i class="fas fa-arrow-up me-1"></i> 12%
                        </div>
                    </div>
                    <h3 class="card-title h2 mb-2 fw-bold">150</h3>
                    <p class="card-text text-muted mb-0">Tổng Sản Phẩm</p>
                    <div class="mt-3">
                        <a href="#" class="btn btn-link text-primary p-0 text-decoration-none fw-semibold">
                            Xem chi tiết <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Người Dùng -->
        <div class="col-md-3">
            <div class="stats-card bg-white shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-success bg-opacity-10">
                            <i class="far fa-users text-success"></i>
                        </div>
                        <div class="stats-trend text-success fw-bold">
                            <i class="fas fa-arrow-up me-1"></i> 8%
                        </div>
                    </div>
                    <h3 class="card-title h2 mb-2 fw-bold">300</h3>
                    <p class="card-text text-muted mb-0">Người Dùng</p>
                    <div class="mt-3">
                        <a href="#" class="btn btn-link text-success p-0 text-decoration-none fw-semibold">
                            Xem chi tiết <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Đơn Hàng -->
        <div class="col-md-3">
            <div class="stats-card bg-white shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-warning bg-opacity-10">
                            <i class="far fa-shopping-cart text-warning"></i>
                        </div>
                        <div class="stats-trend text-danger fw-bold">
                            <i class="fas fa-arrow-down me-1"></i> 3%
                        </div>
                    </div>
                    <h3 class="card-title h2 mb-2 fw-bold">25</h3>
                    <p class="card-text text-muted mb-0">Đơn Hàng Mới</p>
                    <div class="mt-3">
                        <a href="#" class="btn btn-link text-warning p-0 text-decoration-none fw-semibold">
                            Xem chi tiết <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Doanh Thu -->
        <div class="col-md-3">
            <div class="stats-card bg-white shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-info bg-opacity-10">
                            <i class="far fa-chart-line text-info"></i>
                        </div>
                        <div class="stats-trend text-success fw-bold">
                            <i class="fas fa-arrow-up me-1"></i> 15%
                        </div>
                    </div>
                    <h3 class="card-title h2 mb-2 fw-bold">15.5M</h3>
                    <p class="card-text text-muted mb-0">Doanh Thu Tháng</p>
                    <div class="mt-3">
                        <a href="#" class="btn btn-link text-info p-0 text-decoration-none fw-semibold">
                            Xem chi tiết <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4">
        <div class="col-md-8">
            <div class="chart-card bg-white">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4">
                    <h5 class="card-title mb-0 fw-bold">Thống Kê Doanh Thu</h5>
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="#"><i class="far fa-calendar-day me-2"></i>Hôm nay</a></li>
                            <li><a class="dropdown-item" href="#"><i class="far fa-calendar-week me-2"></i>Tuần này</a></li>
                            <li><a class="dropdown-item" href="#"><i class="far fa-calendar-alt me-2"></i>Tháng này</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="chart-card bg-white">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="card-title mb-0 fw-bold">Top Sản Phẩm Bán Chạy</h5>
                </div>
                <div class="card-body">
                    <canvas id="topProductsChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="table-card bg-white mt-4">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4">
            <h5 class="card-title mb-0 fw-bold">Đơn Hàng Gần Đây</h5>
            <a href="#" class="btn btn-primary btn-sm fw-semibold">
                <i class="far fa-eye me-1"></i> Xem tất cả
            </a>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Sản Phẩm</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="fw-bold">#ORD001</span></td>
                            <td>Nguyễn Văn A</td>
                            <td>Áo Thun Basic</td>
                            <td><span class="fw-bold">350.000đ</span></td>
                            <td><span class="badge bg-success">Hoàn thành</span></td>
                            <td>
                                <button class="btn btn-light btn-sm">
                                    <i class="far fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fw-bold">#ORD002</span></td>
                            <td>Trần Thị B</td>
                            <td>Quần Jeans</td>
                            <td><span class="fw-bold">450.000đ</span></td>
                            <td><span class="badge bg-warning">Đang xử lý</span></td>
                            <td>
                                <button class="btn btn-light btn-sm">
                                    <i class="far fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Biểu đồ doanh thu với thiết kế mới
        const revenueChart = new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
                datasets: [{
                    label: 'Doanh Thu (VND)',
                    data: [5000000, 7000000, 6000000, 9000000, 8000000, 12000000],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Allow chart to not maintain aspect ratio
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString('vi-VN') + ' VND';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000).toFixed(1) + 'M';
                            },
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // Biểu đồ sản phẩm bán chạy với thiết kế mới
        const topProductsChart = new Chart(document.getElementById('topProductsChart'), {
            type: 'doughnut',
            data: {
                labels: ['Áo Thun', 'Quần Jeans', 'Váy Dạ Hội', 'Áo Sơ Mi', 'Giày Sneaker'],
                datasets: [{
                    data: [120, 90, 60, 45, 30],
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
                        '#dc3545',
                        '#0dcaf0'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Allow chart to not maintain aspect ratio
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                cutout: '70%'
            }
        });
    </script>
    @endpush
@endsection


