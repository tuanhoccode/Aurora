<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

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
            
        return view('admin.dashboard.index', compact(
            'totalProducts',
            'activeProducts',
            'recentProducts',
            'totalUsers',
            'activeUsers'
        ));
    }
}