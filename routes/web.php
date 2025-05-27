<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;

// Nhóm route cho admin
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Product routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::get('/show', [ProductController::class, 'show'])->name('show');
        Route::get('/edit', [ProductController::class, 'edit'])->name('edit');
    });

    // Brand routes (resource + custom)
    Route::resource('brands', BrandController::class);
    Route::delete('brands/{id}/force', [BrandController::class, 'forceDelete'])->name('brands.forceDelete');

    // Category routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');

        Route::get('/trash', [CategoryController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [CategoryController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('force-delete');

        Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-restore', [CategoryController::class, 'bulkRestore'])->name('bulk-restore');
        Route::post('/bulk-force-delete', [CategoryController::class, 'bulkForceDelete'])->name('bulk-force-delete');

        Route::post('/{id}/toggle-active', [CategoryController::class, 'toggleActive'])->name('toggle-active');
    });

    // (Gợi ý: thêm các route nhóm như orders, users... nếu cần sau)
});
