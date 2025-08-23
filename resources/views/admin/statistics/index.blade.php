@extends('admin.layouts.app')

@section('title', 'Thống kê doanh thu')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card { @apply bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-all; }
    .stat-value { @apply text-2xl font-bold mt-2; }
    .stat-label { @apply text-sm font-medium text-gray-500 uppercase tracking-wider; }
    .status-badge { @apply px-2.5 py-1 rounded-full text-xs font-medium; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📊 Thống kê doanh thu</h1>
                <p class="text-sm text-gray-500">Tổng quan hoạt động kinh doanh</p>
            </div>
            <button id="export-report" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-file-export mr-2"></i> Xuất báo cáo
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Doanh thu hôm nay -->
            <div class="stat-card group">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="stat-label">Doanh thu hôm nay</div>
                        <div id="today-revenue" class="stat-value text-green-600">0 ₫</div>
                        <div id="revenue-change" class="text-sm text-gray-500 mt-1"></div>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg text-green-600 group-hover:bg-green-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Doanh thu tháng -->
            <div class="stat-card group">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="stat-label">Doanh thu tháng này</div>
                        <div id="monthly-revenue" class="stat-value text-blue-600">0 ₫</div>
                        <div id="monthly-revenue-change" class="text-sm text-gray-500 mt-1"></div>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg text-blue-600 group-hover:bg-blue-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tổng đơn hàng -->
            <div class="stat-card group">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="stat-label">Tổng đơn hàng</div>
                        <div class="flex items-baseline">
                            <span id="total-orders" class="stat-value text-purple-600">0</span>
                            <span class="ml-2 text-sm text-gray-500">/ <span id="today-orders">0</span> hôm nay</span>
                        </div>
                        <div id="orders-change" class="text-sm text-gray-500 mt-1"></div>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-lg text-purple-600 group-hover:bg-purple-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Khách hàng mới -->
            <div class="stat-card group">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="stat-label">Khách hàng mới</div>
                        <div class="flex items-baseline">
                            <span id="new-customers" class="stat-value text-orange-600">0</span>
                            <span class="ml-2 text-sm text-gray-500">/ <span id="today-new-customers">0</span> hôm nay</span>
                        </div>
                        <div id="customers-change" class="text-sm text-gray-500 mt-1"></div>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-lg text-orange-600 group-hover:bg-orange-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-semibold mb-4">Biểu đồ doanh thu & đơn hàng</h2>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-medium">Đơn hàng gần đây</h3>
                <a href="" class="px-3 py-1 text-sm bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200">Xem tất cả</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Mã đơn</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Khách hàng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Ngày đặt</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500">Tổng tiền</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="recent-orders" class="bg-white divide-y divide-gray-200">
                        <tr><td colspan="5" class="text-center py-4 text-gray-500">Đang tải dữ liệu...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Format tiền tệ
const formatter = new Intl.NumberFormat('vi-VN', { 
    style: 'currency', 
    currency: 'VND', 
    minimumFractionDigits: 0 
});

// Định dạng số
const numberFormatter = new Intl.NumberFormat('vi-VN');

// Định dạng ngày tháng
function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('vi-VN', { 
        day: '2-digit',
        month: '2-digit',
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

// Khởi tạo dashboard khi trang được tải
document.addEventListener('DOMContentLoaded', () => {
    loadDashboardData();
    // Tự động làm mới dữ liệu mỗi 5 phút
    setInterval(loadDashboardData, 300000);
});

// Lấy dữ liệu thống kê từ API
// Lấy các phần tử DOM
const statElements = {
    todayRevenue: document.getElementById('today-revenue'),
    monthlyRevenue: document.getElementById('monthly-revenue'),
    totalOrders: document.getElementById('total-orders'),
    todayOrders: document.getElementById('today-orders'),
    newCustomers: document.getElementById('new-customers'),
    todayNewCustomers: document.getElementById('today-new-customers'),
    revenueChange: document.getElementById('revenue-change'),
    monthlyRevenueChange: document.getElementById('monthly-revenue-change'),
    ordersChange: document.getElementById('orders-change'),
    customersChange: document.getElementById('customers-change')
};

// Hiển thị trạng thái tải
function showLoadingState() {
    const statValues = document.querySelectorAll('.stat-value, .stat-subtext');
    statValues.forEach(el => {
        el.classList.add('animate-pulse', 'bg-gray-100', 'rounded-md');
        el.innerHTML = '&nbsp;';
    });
}

// Ẩn trạng thái tải
function hideLoadingState() {
    const statValues = document.querySelectorAll('.stat-value, .stat-subtext');
    statValues.forEach(el => {
        el.classList.remove('animate-pulse', 'bg-gray-100', 'rounded-md');
    });
}

// Cập nhật thông tin thống kê
function updateStats(stats) {
    // Cập nhật doanh thu
    statElements.todayRevenue.textContent = formatter.format(stats.today_revenue || 0);
    statElements.monthlyRevenue.textContent = formatter.format(stats.monthly_revenue || 0);
    
    // Cập nhật đơn hàng
    statElements.totalOrders.textContent = numberFormatter.format(stats.total_orders || 0);
    statElements.todayOrders.textContent = numberFormatter.format(stats.today_orders || 0);
    
    // Cập nhật khách hàng
    statElements.newCustomers.textContent = numberFormatter.format(stats.new_customers || 0);
    statElements.todayNewCustomers.textContent = numberFormatter.format(stats.today_new_customers || 0);
    
    // Cập nhật phần trăm thay đổi
    updateChangeIndicator(statElements.revenueChange, stats.revenue_change || 0);
    updateChangeIndicator(statElements.monthlyRevenueChange, stats.monthly_revenue_change || 0);
    updateChangeIndicator(statElements.ordersChange, stats.orders_change || 0);
    updateChangeIndicator(statElements.customersChange, stats.customers_change || 0);
}

// Cập nhật chỉ số thay đổi
function updateChangeIndicator(element, change) {
    if (!element) return;
    
    element.textContent = change > 0 ? `+${change}%` : `${change}%`;
    element.className = 'text-sm font-medium inline-flex items-center';
    
    if (change > 0) {
        element.classList.add('text-green-600');
        element.innerHTML = `<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
        </svg>${change}%`;
    } else if (change < 0) {
        element.classList.add('text-red-600');
        element.innerHTML = `<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>${Math.abs(change)}%`;
    } else {
        element.classList.add('text-gray-500');
        element.textContent = '0%';
    }
}

// Tải dữ liệu thống kê
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    loadRecentOrders();
    
    // Tự động làm mới dữ liệu mỗi 5 phút
    setInterval(loadDashboardData, 5 * 60 * 1000);
});

// Hiển thị trạng thái đang tải
function showLoadingState() {
    const loadingElement = document.getElementById('loading');
    if (loadingElement) loadingElement.classList.remove('hidden');
}

// Ẩn trạng thái đang tải
function hideLoadingState() {
    const loadingElement = document.getElementById('loading');
    if (loadingElement) loadingElement.classList.add('hidden');
}

// Cập nhật giao diện với dữ liệu mới
function updateDashboardUI(data) {
    if (!data || !data.stats) return;
    
    const { stats, recent_orders = [] } = data;
    
    // Cập nhật doanh thu
    if (stats.today_revenue !== undefined) {
        const todayRevenue = document.getElementById('today-revenue');
        if (todayRevenue) todayRevenue.textContent = formatCurrency(stats.today_revenue);
    }
    
    if (stats.monthly_revenue !== undefined) {
        const monthlyRevenue = document.getElementById('monthly-revenue');
        if (monthlyRevenue) monthlyRevenue.textContent = formatCurrency(stats.monthly_revenue);
    }
    
    // Cập nhật số đơn hàng
    if (stats.today_orders !== undefined) {
        const todayOrders = document.getElementById('today-orders');
        if (todayOrders) todayOrders.textContent = formatNumber(stats.today_orders);
    }
    
    if (stats.total_orders !== undefined) {
        const totalOrders = document.getElementById('total-orders');
        if (totalOrders) totalOrders.textContent = formatNumber(stats.total_orders);
    }
    
    // Cập nhật phần trăm thay đổi
    if (stats.revenue_change !== undefined) {
        updatePercentageChange('revenue-change', stats.revenue_change, 'so với hôm qua');
    }
    
    if (stats.monthly_revenue_change !== undefined) {
        updatePercentageChange('monthly-revenue-change', stats.monthly_revenue_change, 'so với tháng trước');
    }
    
    // Cập nhật đơn hàng gần đây nếu có
    if (recent_orders.length > 0) {
        updateRecentOrdersTable(recent_orders);
    }
}

// Cập nhật phần trăm thay đổi
function updatePercentageChange(elementId, value, text) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const isPositive = value >= 0;
    const icon = isPositive ? '▲' : '▼';
    const colorClass = isPositive ? 'text-green-600' : 'text-red-600';
    
    element.innerHTML = `
        <span class="${colorClass} text-sm font-medium">
            ${icon} ${Math.abs(value)}% ${text}
        </span>
    `;
}

// Cập nhật bảng đơn hàng gần đây
function updateRecentOrdersTable(orders) {
    const tbody = document.querySelector('#recent-orders tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (!orders.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    Không có đơn hàng nào gần đây
                </td>
            </tr>
        `;
        return;
    }
    
    orders.forEach(order => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';
        tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <a href="" class="text-indigo-600 hover:text-indigo-900">
                    ${order.code || 'N/A'}
                </a>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${order.customer_name || 'Khách vãng lai'}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">${formatDate(order.order_date)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">
                    ${formatCurrency(order.total_amount || 0)}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2.5 py-1 text-xs font-medium rounded-full ${getStatusClass(order.status)}">
                    ${order.status_text || order.status}
                </span>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Hàm chính để tải dữ liệu thống kê
async function loadDashboardData() {
    showLoadingState();
    
    try {
        // Gọi API lấy dữ liệu thống kê
        const response = await fetch('{{ route('admin.statistics.dashboard') }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error(`Lỗi HTTP! Trạng thái: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success) {
            updateDashboardUI(data);
        } else {
            throw new Error(data.message || 'Không thể tải dữ liệu thống kê');
        }
    } catch (error) {
        console.error('Lỗi khi tải dữ liệu:', error);
        alert('Đã xảy ra lỗi khi tải dữ liệu: ' + error.message);
    } finally {
        hideLoadingState();
    }
    ({
            if (data.success) {
                // Cập nhật thẻ thống kê
                updateStatsCards(data.stats);
                
                // Vẽ biểu đồ doanh thu
                if (data.revenue_chart) {
                    renderRevenueChart(data.revenue_chart);
                }
                
                // Tải danh sách đơn hàng gần đây
                loadRecentOrders();
            } else {
                console.error('Lỗi từ máy chủ:', data.message || 'Không thể tải dữ liệu');
            }
        })
        .catch(error => {
            console.error('Lỗi khi tải dữ liệu:', error);
        })
        .finally(() => {
            // Tắt hiệu ứng loading
            statValues.forEach(el => {
                el.classList.remove('animate-pulse', 'bg-gray-100', 'rounded-md');
            });
        });
}

// Cập nhật các thẻ thống kê
function updateStatsCards(stats) {
    if (!stats) return;
    
    // Cập nhật doanh thu hôm nay
    if (stats.today_revenue !== undefined) {
        document.getElementById('today-revenue').textContent = formatter.format(stats.today_revenue);
    }
    
    // Cập nhật doanh thu tháng
    if (stats.monthly_revenue !== undefined) {
        document.getElementById('monthly-revenue').textContent = formatter.format(stats.monthly_revenue);
    }
    
    // Cập nhật tổng đơn hàng
    if (stats.total_orders !== undefined) {
        document.getElementById('total-orders').textContent = stats.total_orders.toLocaleString();
    }
    
    // Cập nhật khách hàng mới
    if (stats.new_customers !== undefined) {
        document.getElementById('new-customers').textContent = stats.new_customers.toLocaleString();
    }
}

// Biến lưu trữ biểu đồ
let revenueChart;

// Vẽ biểu đồ doanh thu
function renderRevenueChart(chartData) {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    
    // Xóa biểu đồ cũ nếu có
    if (revenueChart) {
        revenueChart.destroy();
    }
    
    // Kiểm tra dữ liệu hợp lệ
    if (!chartData || !chartData.labels || !chartData.revenue_data || !chartData.orders_data) {
        console.error('Dữ liệu biểu đồ không hợp lệ:', chartData);
        return;
    }
    
    const ctx2d = ctx.getContext('2d');
    // Tạo biểu đồ mới
    revenueChart = new Chart(ctx2d, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Doanh thu (triệu VNĐ)',
                    data: chartData.revenue_data.map(v => v / 1000000), // Chuyển đổi sang triệu VNĐ
                    backgroundColor: 'rgba(79, 70, 229, 0.7)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    yAxisID: 'y',
                    type: 'bar',
                    order: 1
                },
                {
                    label: 'Số đơn hàng',
                    data: chartData.orders_data,
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    yAxisID: 'y1',
                    type: 'line',
                    tension: 0.3,
                    order: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Doanh thu (triệu VNĐ)'
                    },
                    grid: {
                        drawOnChartArea: true
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN');
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Số đơn hàng'
                    },
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        precision: 0 // Đảm bảo số nguyên cho số đơn hàng
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                if (context.datasetIndex === 0) {
                                    // Định dạng doanh thu (đã chuyển đổi sang triệu VNĐ)
                                    return `${label} ${context.parsed.y.toLocaleString('vi-VN')} triệu VNĐ`;
                                } else {
                                    // Định dạng số đơn hàng
                                    return `${label} ${context.parsed.y.toLocaleString('vi-VN')}`;
                                }
                            }
                            return label;
                        }
                    }
                },
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        boxWidth: 12
                    }
                }
            }
        }
    });
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { 
        style: 'currency', 
        currency: 'VND',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount || 0);
}

// Format number
function formatNumber(num) {
    return new Intl.NumberFormat('vi-VN').format(num || 0);
}

// Lấy class CSS cho trạng thái đơn hàng
function getStatusClass(status) {
    const statusMap = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'processing': 'bg-blue-100 text-blue-800',
        'shipped': 'bg-indigo-100 text-indigo-800',
        'delivered': 'bg-green-100 text-green-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
        'refunded': 'bg-purple-100 text-purple-800',
        'failed': 'bg-red-100 text-red-800',
        'on_hold': 'bg-yellow-100 text-yellow-800'
    };
    return statusMap[status] || 'bg-gray-100 text-gray-800';
}

// Văn bản hiển thị cho các trạng thái đơn hàng
function getStatusText(status) {
    const statusMap = {
        'pending': 'Chờ xử lý',
        'processing': 'Đang xử lý',
        'shipped': 'Đang giao hàng',
        'delivered': 'Đã giao hàng',
        'completed': 'Hoàn thành',
        'cancelled': 'Đã hủy',
        'refunded': 'Đã hoàn tiền',
        'failed': 'Thất bại',
        'on_hold': 'Tạm giữ',
        'pending_payment': 'Chờ thanh toán'
    };
    return statusMap[status] || status;
}

// Hiển thị thông báo lỗi
function showError(message) {
    const errorContainer = document.createElement('div');
    errorContainer.className = 'bg-red-50 border-l-4 border-red-400 p-4 mb-4';
    errorContainer.innerHTML = `
        <div class='flex'>
            <div class='flex-shrink-0'>
                <svg class='h-5 w-5 text-red-400' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'>
                    <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z' clip-rule='evenodd' />
                </svg>
            </div>
            <div class='ml-3'>
                <p class='text-sm text-red-700'>${message}</p>
            </div>
        </div>
    `;
    
    const container = document.querySelector('.max-w-7xl.mx-auto');
    if (container) {
        // Chỉ thêm thông báo lỗi nếu chưa có
        if (!container.querySelector('.bg-red-50')) {
            container.insertBefore(errorContainer, container.firstChild);
        }
    }
}
</script>
@endpush
