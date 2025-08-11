<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;
use App\Models\Order;
use App\Models\BlogComment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     */
    public function index(): View
    {
        // Get product statistics
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)
            ->whereNull('deleted_at')
            ->count();

        // Get recent products
        $recentProducts = Product::with(['brand'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get user statistics
        $totalUsers = User::count();
        $activeUsers = $totalUsers; // No is_active column, use total user count

        $currentYear = now()->year;
        $revenueByMonth = Order::whereHas('currentStatus', function($q) {
                $q->where('order_status_id', 4)->where('is_current', 1);
            })
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->pluck('total', 'month');

        // Đảm bảo đủ 12 tháng, nếu thiếu thì gán 0
        $revenueData = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenueData[$m] = (float)($revenueByMonth[$m] ?? 0);
        }

        // Lấy số lượng bình luận chờ duyệt
        $unapprovedCommentsCount = BlogComment::where('is_active', false)->count();

        return view('admin.dashboard.index', compact(
            'totalProducts',
            'activeProducts',
            'recentProducts',
            'totalUsers',
            'activeUsers',
            'revenueData',
            'unapprovedCommentsCount',
            'currentYear'
        ));
    }
}
