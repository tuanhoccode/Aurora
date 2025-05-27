<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BrandController;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('products/edit', [ProductController::class, 'edit'])->name('products.edit');

    Route::resource('brands', BrandController::class);
    Route::delete('brands/{id}/force', [BrandController::class, 'forceDelete'])->name('brands.forceDelete');
});
