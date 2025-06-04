<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AdminLoginController;

//Auth admin
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

//Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    Route::resource('products', ProductController::class);
    Route::resource('brands', BrandController::class);
    Route::delete('brands/{id}/force', [BrandController::class, 'forceDelete'])->name('brands.forceDelete');

    Route::resource('users', UserController::class);
    Route::patch('users/{user}/change-status', [UserController::class, 'changeStatus'])->name('users.changeStatus');


    Route::resource('categories', CategoryController::class);
    Route::get('categories/trash', [CategoryController::class, 'trash'])->name('categories.trash');
    Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{id}/force', [CategoryController::class, 'forceDelete'])->name('categories.forceDelete');
    Route::post('categories/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('categories.bulkDelete');
    Route::post('categories/bulk-restore', [CategoryController::class, 'bulkRestore'])->name('categories.bulkRestore');
    Route::post('categories/bulk-force-delete', [CategoryController::class, 'bulkForceDelete'])->name('categories.bulkForceDelete');
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggleStatus');
    Route::post('categories/bulk-toggle', [CategoryController::class, 'bulkToggle'])->name('categories.bulkToggle');
});
