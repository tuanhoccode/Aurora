@extends('admin.layouts.app')

@section('content')
    <h1>Bảng Điều Khiển Quản Trị - Cửa Hàng Quần Áo</h1>
    <p>Chào mừng bạn đến với hệ thống quản trị.</p>

    <div class="row">
        <!-- Card Sản Phẩm -->
        <div class="col-md-4">
            <div class="card text-bg-primary mb-3">
                <div class="card-header">Sản Phẩm</div>
                <div class="card-body">
                    <h5 class="card-title">150 sản phẩm</h5>
                    <p class="card-text">Quản lý danh sách quần áo và phụ kiện.</p>
                    <a href="#" class="btn btn-light btn-sm">Xem chi tiết</a>
                </div>
            </div>
        </div>

        <!-- Card Người Dùng -->
        <div class="col-md-4">
            <div class="card text-bg-success mb-3">
                <div class="card-header">Người Dùng</div>
                <div class="card-body">
                    <h5 class="card-title">300 người dùng</h5>
                    <p class="card-text">Quản lý tài khoản khách hàng và nhân viên.</p>
                    <a href="#" class="btn btn-light btn-sm">Xem chi tiết</a>
                </div>
            </div>
        </div>

        <!-- Card Đơn Hàng -->
        <div class="col-md-4">
            <div class="card text-bg-warning mb-3">
                <div class="card-header">Đơn Hàng</div>
                <div class="card-body">
                    <h5 class="card-title">25 đơn hàng</h5>
                    <p class="card-text">Theo dõi và xử lý các đơn hàng mới.</p>
                    <a href="#" class="btn btn-light btn-sm">Xem chi tiết</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ thống kê -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Thống Kê Doanh Thu</div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Top Sản Phẩm Bán Chạy</div>
                <div class="card-body">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Biểu đồ doanh thu
            const revenueChart = new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
                    datasets: [{
                        label: 'Doanh Thu (VND)',
                        data: [5000000, 7000000, 6000000, 9000000, 8000000, 12000000],
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('vi-VN') + ' VND';
                                }
                            }
                        }
                    }
                }
            });

            // Biểu đồ sản phẩm bán chạy
            const topProductsChart = new Chart(document.getElementById('topProductsChart'), {
                type: 'bar',
                data: {
                    labels: ['Áo Thun', 'Quần Jeans', 'Váy Dạ Hội', 'Áo Sơ Mi', 'Giày Sneaker'],
                    datasets: [{
                        label: 'Số Lượng Bán',
                        data: [120, 90, 60, 45, 30],
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#17a2b8'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection