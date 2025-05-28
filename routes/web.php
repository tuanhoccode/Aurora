<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;

use App\Http\Controllers\Admin\BrandController;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');

    // Đặt các route thủ công trước resource
    Route::delete('brands/force-delete/{id}', [BrandController::class, 'forceDelete'])->name('brands.forceDelete');
    Route::get('/brands/trash', [BrandController::class, 'trash'])->name('brands.trash');
    Route::put('/brands/{id}/restore', [BrandController::class, 'restore'])->name('brands.restore');

    Route::resource('brands', BrandController::class);
});
