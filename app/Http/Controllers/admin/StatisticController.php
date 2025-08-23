<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function index()
    {
        return view('admin.statistics.index');
    }
    
    /**
     * Get dashboard statistics
     */
    public function dashboard()
    {
        try {
            $today = now();
            $yesterday = now()->subDay();
            $startOfMonth = now()->startOfMonth();
            $startOfLastMonth = now()->subMonth()->startOfMonth();
            $endOfLastMonth = now()->subMonth()->endOfMonth();
            
            // Today's revenue from completed orders
            $todayRevenue = Order::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('total_amount');
                
            // Yesterday's revenue for comparison
            $yesterdayRevenue = Order::whereDate('created_at', $yesterday)
                ->where('status', 'completed')
                ->sum('total_amount');
                
            // Monthly revenue from completed orders
            $monthlyRevenue = Order::where('status', 'completed')
                ->whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->sum('total_amount');
                
            // Last month's revenue for comparison
            $lastMonthRevenue = Order::where('status', 'completed')
                ->whereMonth('created_at', $startOfLastMonth->month)
                ->whereYear('created_at', $startOfLastMonth->year)
                ->sum('total_amount');
                
            // Calculate percentage changes
            $revenueChange = $yesterdayRevenue > 0 
                ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
                : ($todayRevenue > 0 ? 100 : 0);
                
            $monthlyRevenueChange = $lastMonthRevenue > 0 
                ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
                : ($monthlyRevenue > 0 ? 100 : 0);
                
            // Total orders this month
            $totalOrders = Order::whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->count();
                
            // Today's orders
            $todayOrders = Order::whereDate('created_at', $today)->count();
                
            // Total orders last month for comparison
            $lastMonthOrders = Order::whereMonth('created_at', $startOfLastMonth->month)
                ->whereYear('created_at', $startOfLastMonth->year)
                ->count();
                
            $orderChange = $lastMonthOrders > 0 
                ? round((($totalOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
                : ($totalOrders > 0 ? 100 : 0);
                
            // New customers this month
            $newCustomers = \App\Models\User::whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->count();
                
            // New customers today
            $todayNewCustomers = \App\Models\User::whereDate('created_at', $today)->count();
                
            // New customers last month for comparison
            $lastMonthNewCustomers = \App\Models\User::whereMonth('created_at', $startOfLastMonth->month)
                ->whereYear('created_at', $startOfLastMonth->year)
                ->count();
                
            $customerChange = $lastMonthNewCustomers > 0
                ? round((($newCustomers - $lastMonthNewCustomers) / $lastMonthNewCustomers) * 100, 1)
                : ($newCustomers > 0 ? 100 : 0);
                
            // Get top selling products
            $topProducts = \App\Models\OrderItem::select(
                    'products.name',
                    'products.thumbnail',
                    DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_quantity'),
                    DB::raw('COALESCE(SUM(order_items.price * order_items.quantity), 0) as total_revenue')
                )
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->whereMonth('orders.created_at', $today->month)
                ->whereYear('orders.created_at', $today->year)
                ->groupBy('order_items.product_id', 'products.name', 'products.thumbnail')
                ->orderByDesc('total_quantity')
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->name,
                        'thumbnail' => $item->thumbnail ? asset('storage/' . $item->thumbnail) : null,
                        'total_quantity' => (int)$item->total_quantity,
                        'total_revenue' => (float)$item->total_revenue
                    ];
                });
            
            // Get order status counts
            $orderStatusCounts = Order::select(
                    'status',
                    DB::raw('COUNT(*) as count')
                )
                ->whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
            
            // Get revenue chart data (last 30 days)
            $revenueChartData = $this->getRevenueChartDataForDashboard();
            
            // Get recent orders for the table
            $recentOrders = Order::with(['user'])
                ->latest()
                ->take(10)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'code' => $order->code,
                        'customer_name' => $order->user ? $order->user->name : 'Khách vãng lai',
                        'order_date' => $order->created_at,
                        'total_amount' => $order->total_amount,
                        'status' => $order->status,
                        'status_text' => $this->getStatusText($order->status)
                    ];
                });
            
            return response()->json([
                'success' => true,
                'stats' => [
                    'today_revenue' => (float)$todayRevenue,
                    'yesterday_revenue' => (float)$yesterdayRevenue,
                    'revenue_change' => (float)$revenueChange,
                    'monthly_revenue' => (float)$monthlyRevenue,
                    'monthly_revenue_change' => (float)$monthlyRevenueChange,
                    'total_orders' => (int)$totalOrders,
                    'today_orders' => (int)$todayOrders,
                    'orders_change' => (float)$orderChange,
                    'new_customers' => (int)$newCustomers,
                    'today_new_customers' => (int)$todayNewCustomers,
                    'customers_change' => (float)$customerChange,
                    'order_status_counts' => $orderStatusCounts,
                    'top_products' => $topProducts
                ],
                'revenue_chart' => $revenueChartData,
                'recent_orders' => $recentOrders
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải thống kê: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get revenue chart data for dashboard
     */
    private function getRevenueChartDataForDashboard()
    {
        $months = [];
        $revenueData = [];
        $ordersData = [];
        
        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            
            $revenue = Order::where('status', 'completed')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->sum('total_amount');
                
            $orders = Order::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();
                
            $months[] = 'Th' . $month . '/' . substr($year, 2);
            $revenueData[] = $revenue;
            $ordersData[] = $orders;
        }
        
        return [
            'labels' => $months,
            'revenue_data' => $revenueData,
            'orders_data' => $ordersData
        ];
    }
    
    /**
     * Get recent orders for dashboard
     */
    public function recentOrders()
    {
        try {
            $orders = Order::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($order) {
                    return [
                        'code' => $order->code,
                        'customer_name' => $order->user ? $order->user->name : 'Khách vãng lai',
                        'order_date' => $order->created_at,
                        'total_amount' => $order->total_amount,
                        'status' => $order->status,
                        'status_text' => $this->getStatusText($order->status)
                    ];
                });
                
            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải đơn hàng gần đây: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get status display text
     */
    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
        ];
        
        return $statuses[$status] ?? ucfirst($status);
    }
    
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        try {
            // Get all-time revenue
            $totalRevenue = Order::where('status', 'completed')
                ->sum('total_amount');
                
            // Get total orders
            $totalOrders = Order::count();
            $completedOrders = Order::where('status', 'completed')->count();
            
            // Get total customers
            $totalCustomers = \App\Models\User::count();
            $newCustomers = $totalCustomers; // Show total customers as new customers for now
            
            // Set default values for changes (0% since we're showing all-time data)
            $revenueChange = 0;
            $monthlyChange = 0;
            $customerChange = 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_revenue' => $totalRevenue,
                    'revenue_change' => $revenueChange,
                    'total_orders' => $totalOrders,
                    'completed_orders' => $completedOrders,
                    'total_customers' => $totalCustomers,
                    'new_customers' => $newCustomers,
                    'customer_change' => $customerChange,
                    'order_completion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải thống kê: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get revenue chart data
     */
    public function getRevenueChartData(Request $request)
    {
        try {
            // Get all orders grouped by month
            $data = Order::query()
                ->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN status = "completed" THEN total_amount ELSE 0 END) as revenue'),
                    DB::raw('COUNT(CASE WHEN status = "completed" THEN id END) as orders')
                )
                ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
                ->orderBy('year')
                ->orderBy('month')
                ->get();
            
            $labels = [];
            $revenueData = [];
            $ordersData = [];
            
            // Format the data for the chart
            foreach ($data as $item) {
                $month = (int)$item->month;
                $year = (int)$item->year;
                $labels[] = "Th{$month}/{$year}";
                $revenueData[] = (float)($item->revenue / 1000000); // Convert to millions
                $ordersData[] = (int)$item->orders;
            }
            
            // If no data, return empty arrays
            if (empty($data)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'labels' => [],
                        'revenue' => [],
                        'orders' => []
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'revenue' => $revenueData,
                    'orders' => $ordersData
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải dữ liệu biểu đồ: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get recent orders
     */
    public function getRecentOrders()
    {
        try {
            $orders = Order::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($order) {
                    return [
                        'code' => $order->code,
                        'customer_name' => $order->user ? $order->user->name : 'Khách vãng lai',
                        'order_date' => $order->created_at->format('d/m/Y H:i'),
                        'total_amount' => $order->total_amount,
                        'status' => $order->status,
                        'status_display' => $this->getStatusDisplay($order->status)
                    ];
                });
                
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải đơn hàng gần đây: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Helper to get status display text and class
     */
    private function getStatusDisplay($status)
    {
        $statuses = [
            'pending' => ['text' => 'Chờ xử lý', 'class' => 'pending'],
            'processing' => ['text' => 'Đang xử lý', 'class' => 'processing'],
            'shipped' => ['text' => 'Đang giao hàng', 'class' => 'shipped'],
            'completed' => ['text' => 'Hoàn thành', 'class' => 'completed'],
            'cancelled' => ['text' => 'Đã hủy', 'class' => 'cancelled'],
            'refunded' => ['text' => 'Đã hoàn tiền', 'class' => 'refunded'],
        ];
        
        return $statuses[$status] ?? ['text' => ucfirst($status), 'class' => 'default'];
    }

    public function getMonthlyRevenue()
    {
        try {
            $currentYear = now()->year;
            
            $revenueByMonth = Order::where('status', 'completed')
                ->whereYear('created_at', $currentYear)
                ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();
                
            // Check if there's any data
            if (empty($revenueByMonth)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'hasData' => false,
                    'message' => 'Chưa có dữ liệu doanh thu tháng nào trong năm ' . $currentYear
                ]);
            }
            
            // Ensure all 12 months are present with 0 for months with no data
            $monthlyData = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthlyData[$m] = (float)($revenueByMonth[$m] ?? 0);
            }
            
            return response()->json([
                'success' => true,
                'data' => $monthlyData,
                'hasData' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tải dữ liệu doanh thu tháng',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function getDailySales()
    {
        try {
            $today = Carbon::today();
            
            $orders = Order::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->with('items.product')
                ->get();
                
            $totalSales = $orders->sum('total_amount');
            $totalOrders = $orders->count();
            
            // Thống kê sản phẩm bán chạy trong ngày
            $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
                ->whereHas('order', function($query) use ($today) {
                    $query->whereDate('created_at', $today)
                          ->where('status', 'completed');
                })
                ->with('product')
                ->groupBy('product_id')
                ->orderBy('total_quantity', 'DESC')
                ->take(5)
                ->get();
                
            // Nếu không có đơn hàng nào, trả về dữ liệu mẫu
            if ($totalOrders === 0) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_sales' => '0 đ',
                        'total_orders' => 0,
                        'top_products' => [
                            ['name' => 'Chưa có dữ liệu', 'quantity' => 0, 'revenue' => '0 đ']
                        ]
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_sales' => number_format($totalSales) . ' đ',
                    'total_orders' => $totalOrders,
                    'top_products' => $topProducts->map(function($item) {
                        return [
                            'name' => $item->product ? $item->product->name : 'Sản phẩm đã bị xóa',
                            'quantity' => $item->total_quantity,
                            'revenue' => number_format($item->total_quantity * $item->price) . ' đ'
                        ];
                    })
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getDailySales: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy dữ liệu thống kê ngày',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getWeeklySales()
    {
        try {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            
            $orders = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->where('status', 'completed')
                ->with('items.product')
                ->get();
                
            $totalSales = $orders->sum('total_amount');
            $totalOrders = $orders->count();
            
            // Thống kê theo ngày trong tuần
            $dailySales = [];
            $daysOfWeek = [];
            $salesData = [];
            
            for ($i = 0; $i < 7; $i++) {
                $day = $startOfWeek->copy()->addDays($i);
                $dayName = $day->isoFormat('dddd');
                $daySales = Order::whereDate('created_at', $day)
                    ->where('status', 'completed')
                    ->sum('total_amount');
                    
                $dailySales[] = [
                    'day' => $dayName,
                    'sales' => $daySales
                ];
                
                $daysOfWeek[] = $dayName;
                $salesData[] = $daySales;
            }
            
            // Nếu không có đơn hàng nào, trả về dữ liệu mẫu
            if ($totalOrders === 0) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_sales' => '0 đ',
                        'total_orders' => 0,
                        'daily_sales' => $dailySales,
                        'chart_data' => [
                            'labels' => $daysOfWeek,
                            'data' => array_fill(0, 7, 0)
                        ]
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_sales' => number_format($totalSales) . ' đ',
                    'total_orders' => $totalOrders,
                    'daily_sales' => $dailySales,
                    'chart_data' => [
                        'labels' => $daysOfWeek,
                        'data' => $salesData
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getWeeklySales: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy dữ liệu thống kê tuần',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getMonthlySales()
    {
        try {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            
            $orders = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('status', 'completed')
                ->with('items.product')
                ->get();
                
            $totalSales = $orders->sum('total_amount');
            $totalOrders = $orders->count();
            
            // Thống kê theo tuần trong tháng
            $weeklySales = [];
            $weekLabels = [];
            $salesData = [];
            $currentWeek = $startOfMonth->copy();
            $weekNumber = 1;
            
            while ($currentWeek < $endOfMonth) {
                $weekEnd = $currentWeek->copy()->endOfWeek();
                if ($weekEnd > $endOfMonth) {
                    $weekEnd = $endOfMonth;
                }
                
                $weekSales = Order::whereBetween('created_at', [$currentWeek, $weekEnd])
                    ->where('status', 'completed')
                    ->sum('total_amount');
                    
                $weekLabel = 'Tuần ' . $weekNumber;
                $weeklySales[] = [
                    'week' => $weekLabel,
                    'sales' => $weekSales
                ];
                
                $weekLabels[] = $weekLabel;
                $salesData[] = $weekSales;
                
                $currentWeek = $weekEnd->copy()->addDay();
                $weekNumber++;
            }
            
            // Nếu không có đơn hàng nào, trả về dữ liệu mẫu
            if ($totalOrders === 0) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_sales' => '0 đ',
                        'total_orders' => 0,
                        'weekly_sales' => $weeklySales,
                        'chart_data' => [
                            'labels' => $weekLabels,
                            'data' => array_fill(0, count($weekLabels), 0)
                        ]
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_sales' => number_format($totalSales) . ' đ',
                    'total_orders' => $totalOrders,
                    'weekly_sales' => $weeklySales,
                    'chart_data' => [
                        'labels' => $weekLabels,
                        'data' => $salesData
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getMonthlySales: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy dữ liệu thống kê tháng',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
