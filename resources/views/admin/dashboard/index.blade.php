@extends('admin.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        padding: 1.5rem;
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
    .fas, .far {
        /* Xóa dòng này hoặc sửa lại như sau để dùng font mặc định */
        /* font-family: 'Font Awesome 6 Pro' !important; */
    }
</style>
@endpush

@section('content')
    <div class="dashboard-header animate__animated animate__fadeIn mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="h3 mb-2 fw-bold">Bảng Điều Khiển</h1>
                <p class="text-muted mb-0">Chào mừng trở lại, {{Auth::user()->fullname}}({{Auth::user()->role === 'admin' ? 'Admin' : 'Nhân viên'}}) !</p>
            </div>
            <div class="date-info">
                <span class="badge bg-light text-dark shadow-sm fs-6 py-2 px-3">
                    <i class="fas fa-calendar-alt me-2"></i>
                    {{ now()->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row g-4 mb-4">
        <!-- Tổng Sản Phẩm -->
        <div class="col-12 col-md-3">
            <div class="stats-card bg-white shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-primary bg-opacity-10">
                            <i class="fas fa-shirt text-primary"></i>
                        </div>
                    </div>
                    <h3 class="card-title h2 mb-2 fw-bold">{{ number_format($totalProducts ?? 0) }}</h3>
                    <p class="card-text text-muted mb-0">Tổng Sản Phẩm</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-link text-primary p-0 text-decoration-none fw-semibold">
                            Xem chi tiết <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Mã Giảm Giá Hoạt Động -->
        <div class="col-12 col-md-3">
            <div class="stats-card bg-white shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-success bg-opacity-10">
                            <i class="fas fa-ticket-alt text-success"></i>
                        </div>
                    </div>
                    <h3 class="card-title h2 mb-2 fw-bold">{{ number_format($activeCoupons ?? 0) }}</h3>
                    <p class="card-text text-muted mb-0">Mã Giảm Giá Hoạt Động</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-link text-success p-0 text-decoration-none fw-semibold">
                            Xem chi tiết <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tổng Người Dùng -->
        <div class="col-12 col-md-3">
            <div class="stats-card bg-white shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-info bg-opacity-10">
                            <i class="fas fa-users text-info"></i>
                        </div>
                    </div>
                    <h3 class="card-title h2 mb-2 fw-bold">{{ number_format($totalUsers) }}</h3>
                    <p class="card-text text-muted mb-0">Tổng Người Dùng</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-link text-info p-0 text-decoration-none fw-semibold">
                            Xem chi tiết <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tổng Đơn Hàng -->
        <div class="col-12 col-md-3">
            <div class="stats-card bg-white shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-warning bg-opacity-10">
                            <i class="fas fa-shopping-cart text-warning"></i>
                        </div>
                    </div>
                    <h3 class="card-title h2 mb-2 fw-bold">{{ number_format($totalOrders ?? 0) }}</h3>
                    <p class="card-text text-muted mb-0">Tổng Đơn Hàng</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-link text-warning p-0 text-decoration-none fw-semibold">
                            Xem chi tiết <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mt-4"> <!-- Thêm mt-4 để tạo khoảng cách phía trên -->
        <div class="col-12 col-lg-8">
            <div class="chart-card bg-white h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4 pb-2">
                    <h5 class="card-title mb-0 fw-bold">Biểu Đồ Doanh Thu Năm {{ $currentYear }}</h5>
                </div>
                <div class="card-body pt-0">
                    <canvas id="revenueChart" height="350"></canvas> <!-- Tăng chiều cao lên 350 -->
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="chart-card bg-white h-100">
                <div class="card-header bg-transparent border-0 p-4 pb-2">
                    <h5 class="card-title mb-0 fw-bold">Trạng Thái Đơn Hàng</h5>
                </div>
                <div class="card-body pt-0">
                    <canvas id="orderStatusChart" height="350"></canvas>
                </div>
            </div>
        </div>
    </div>

        <!-- Bảng đơn hàng mới nhất -->
        <div class="table-card bg-white mt-4">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4 pb-2">
            <h5 class="card-title mb-0 fw-bold">Đơn Hàng Mới Nhất</h5>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm fw-semibold rounded-pill px-3">
                <i class="fas fa-eye me-1"></i> Xem tất cả
            </a>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Mã Đơn Hàng</th>
                            <th>Khách Hàng</th>
                            <th>Ngày Đặt</th>
                            <th>Tổng Tiền</th>
                            <th>Phương Thức Thanh Toán</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td class="fw-bold">#{{ $order->code }}</td>
                            <td>{{ $order->fullname ?? $order->user->name ?? 'Khách vãng lai' }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                            <td>{{ $order->payment->name ?? 'Chưa xác định' }}</td>
                            <td>{!! $order->fulfilment_status_badge !!}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-light btn-sm rounded-pill px-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">Chưa có đơn hàng nào</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Recent Products Table -->
    <div class="table-card bg-white mt-4">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4 pb-2">
            <h5 class="card-title mb-0 fw-bold">Sản Phẩm Mới</h5>
            <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-sm fw-semibold rounded-pill px-3">
                <i class="fas fa-eye me-1"></i> Xem tất cả
            </a>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Mã Sản Phẩm</th>
                            <th>Tên Sản Phẩm</th>
                            <th>Thương Hiệu</th>
                            <th>Giá</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                <tbody>
                    @foreach($recentProducts as $product)
                        @php
                            if ($product->variants && $product->variants->count()) {
                                $minPrice = $product->variants->min('regular_price');
                                $maxPrice = $product->variants->max('regular_price');
                            } else {
                                $minPrice = $maxPrice = $product->price;
                            }
                        @endphp

                        <tr>
                            <td><span class="fw-bold">{{ $product->sku }}</span></td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->brand->name ?? '-' }}</td>
                            <td>
                                @if ($minPrice == $maxPrice)
                                    <span class="fw-bold">{{ number_format($minPrice, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="fw-bold">{{ number_format($minPrice, 0, ',', '.') }}đ</span>
                                    <small class="text-muted">- {{ number_format($maxPrice, 0, ',', '.') }}đ</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }} px-3 py-2">
                                    {{ $product->is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-light btn-sm rounded-pill px-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script>
const ctx = document.getElementById('revenueChart').getContext('2d');
const gradient = ctx.createLinearGradient(0, 0, 0, 320);
gradient.addColorStop(0, 'rgba(13,110,253,0.18)');
gradient.addColorStop(1, 'rgba(13,110,253,0.01)');

const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
            'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
        ],
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: @json(array_values($revenueData)),
            fill: true,
            backgroundColor: gradient,
            borderColor: '#0d6efd',
            borderWidth: 2,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#0d6efd',
            pointRadius: 7,
            pointHoverRadius: 11,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: {
                top: 30,
                bottom: 20
            }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0d6efd',
                titleColor: '#fff',
                bodyColor: '#fff',
                padding: 14,
                borderRadius: 8,
                titleFont: { size: 15, weight: 'bold' },
                bodyFont: { size: 14 }
            },
            datalabels: {
                display: true,
                align: 'top',
                anchor: 'end',
                color: '#0d6efd',
                font: {
                    weight: 'bold',
                    size: 15
                },
                formatter: function(value) {
                    return value > 0 ? value.toLocaleString('vi-VN') + 'đ' : '';
                }
            }
        },
        scales: {
            x: {
                grid: { display: false }
            },
            y: {
                beginAtZero: true,
                grid: { color: '#f1f3f5' },
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('vi-VN') + 'đ';
                    }
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});

// Order status doughnut chart (fixed colors, center total, clickable slices)
const statusCtx = document.getElementById('orderStatusChart')?.getContext('2d');
if (statusCtx) {
    const statusLabels = @json($orderStatusLabels ?? []);
    const statusCounts = @json($orderStatusCounts ?? []);

    // Tổng số đơn để hiển thị phần trăm và center label
    const totalOrders = statusCounts.reduce((a, b) => a + (Number(b) || 0), 0);

    // Bảng màu cố định theo nhãn (tham chiếu theo các sàn lớn)
    // Phối màu theo nghĩa trạng thái (gợi nhớ trực quan)
    const statusColorMap = {
        // Đang chờ/đang xử lý → xanh dương
        'Chờ xác nhận': '#0d6efd',
        'Chờ lấy hàng': '#fd7e14',     // chuẩn bị lấy → cam
        'Đang giao': '#0dcaf0',        // vận chuyển → cyan
        // Thành công/hoàn tất → xanh lá
        'Giao hàng thành công': '#198754',
        'Nhận hàng thành công': '#2eb85c',
        // Quy trình trả/đổi → tím
        'Chờ trả hàng': '#6f42c1',
        'Đã trả hàng': '#845ef7',
        // Tiền/hoàn tiền → vàng/amber
        'Hoàn tiền': '#e6a700',
        // Hủy/ lỗi → đỏ
        'Đã hủy': '#dc3545',
        // Đã gửi đi (handover) → teal
        'Gửi hàng': '#20c997'
    };

    const sliceColors = statusLabels.map(l => statusColorMap[l] || '#6c757d');

    // Plugin hiển thị tổng số đơn ở giữa donut
    const centerText = {
        id: 'centerText',
        afterDraw(chart) {
            const {ctx, chartArea: {left, right, top, bottom}} = chart;
            ctx.save();
            const x = (left + right) / 2;
            const y = (top + bottom) / 2;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#333';
            ctx.font = '600 18px system-ui, -apple-system, Segoe UI, Roboto, Arial';
            ctx.fillText(totalOrders.toLocaleString('vi-VN'), x, y - 6);
            ctx.fillStyle = '#6c757d';
            ctx.font = '400 12px system-ui, -apple-system, Segoe UI, Roboto, Arial';
            ctx.fillText('tổng đơn', x, y + 12);
            ctx.restore();
        }
    };

    const orderStatusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: sliceColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label(context) {
                            const value = Number(context.parsed) || 0;
                            return `${context.label}: ${value.toLocaleString('vi-VN')}`;
                        }
                    }
                },
                datalabels: {
                    color: '#fff',
                    formatter: (value) => {
                        const v = Number(value) || 0;
                        return v > 0 ? v.toLocaleString('vi-VN') : '';
                    }
                }
            },
            onClick: (evt, elements) => {
                if (!elements.length) return;
                const idx = elements[0].index;
                const label = statusLabels[idx];
                const baseUrl = `{{ route('admin.orders.index') }}`;
                window.location.href = `${baseUrl}?status=${encodeURIComponent(label)}`;
            }
        },
        plugins: [ChartDataLabels, centerText]
    });
}
</script>
@endpush
@endsection

@push('scripts')
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/chart.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@php
    $topProducts = $topProducts ?? collect([]);
@endphp
