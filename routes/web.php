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

    Route::prefix('brands')->name('brands.')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('index');
        Route::get('/create', [BrandController::class, 'create'])->name('create');
        Route::post('/', [BrandController::class, 'store'])->name('store');
        Route::get('/{brand}', [BrandController::class, 'show'])->name('show');
        Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('edit');
        Route::put('/{brand}', [BrandController::class, 'update'])->name('update');
        Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [BrandController::class, 'trash'])->name('trash');
        Route::put('/{id}/restore', [BrandController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [BrandController::class, 'forceDelete'])->name('force-delete');

        // Batch Actions
        // Route::post('/batch-action', [BrandController::class, 'batchAction'])->name('batch-action'); // Removed
        // Route::post('/batch-restore', [BrandController::class, 'batchRestore'])->name('batch-restore'); // Removed
        // Route::post('/batch-force-delete', [BrandController::class, 'batchForceDelete'])->name('batch-force-delete'); // Removed
    });
});
