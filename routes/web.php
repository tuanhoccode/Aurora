<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\ProductVariantController;


Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products Routes
    Route::prefix('products')->name('products.')->group(function () {
        // List và Form routes
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');

        // Quản lý thùng rác
        Route::get('/trash', [ProductController::class, 'trash'])->name('trash');
        Route::put('/{id}/restore', [ProductController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [ProductController::class, 'forceDelete'])->name('force-delete');

        // Bulk Actions
        Route::post('/bulk-delete', [ProductController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-restore', [ProductController::class, 'bulkRestore'])->name('bulk-restore');
        Route::post('/bulk-force-delete', [ProductController::class, 'bulkForceDelete'])->name('bulk-force-delete');
        Route::post('/bulk-toggle-status', [ProductController::class, 'bulkToggleStatus'])->name('bulk-toggle-status');

        // Resource routes
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');

        // Toggle status
        Route::put('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');

        // Product Variants
        Route::get('/{product}/variants/create', [ProductVariantController::class, 'create'])->name('variants.create');
        Route::post('/{product}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
        Route::get('/{product}/variants/{variant}/edit', [ProductVariantController::class, 'edit'])->name('variants.edit');
        Route::put('/{product}/variants/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
        Route::delete('/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');
    });
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/change-status', [UserController::class, 'changeStatus'])->name('users.changeStatus');
    // Brands Routes
    Route::prefix('brands')->name('brands.')->group(function () {
        // List và Form routes
        Route::get('/', [BrandController::class, 'index'])->name('index');
        Route::get('/create', [BrandController::class, 'create'])->name('create');
        Route::post('/', [BrandController::class, 'store'])->name('store');

        // Quản lý thùng rác
        Route::get('/trash', [BrandController::class, 'trash'])->name('trash');
        Route::put('/{id}/restore', [BrandController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [BrandController::class, 'forceDelete'])->name('force-delete');

        // Bulk Actions
        Route::post('/bulk-delete', [BrandController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-restore', [BrandController::class, 'bulkRestore'])->name('bulk-restore');
        Route::post('/bulk-force-delete', [BrandController::class, 'bulkForceDelete'])->name('bulk-force-delete');
        Route::post('/bulk-toggle-status', [BrandController::class, 'bulkToggleStatus'])->name('bulk-toggle-status');

        // Resource routes
        Route::get('/{brand}', [BrandController::class, 'show'])->name('show');
        Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('edit');
        Route::put('/{brand}', [BrandController::class, 'update'])->name('update');
        Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('destroy');
    });

    // Categories Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        // List và Form routes
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');

        // Quản lý thùng rác
        Route::get('/trash', [CategoryController::class, 'trash'])->name('trash');
        Route::put('/{id}/restore', [CategoryController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [CategoryController::class, 'forceDelete'])->name('force-delete');

        // Bulk Actions
        Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-restore', [CategoryController::class, 'bulkRestore'])->name('bulk-restore');
        Route::post('/bulk-force-delete', [CategoryController::class, 'bulkForceDelete'])->name('bulk-force-delete');

        // Resource routes
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');

        // Toggle status
        Route::put('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/bulk-toggle', [CategoryController::class, 'bulkToggle'])->name('bulk-toggle');
    });

    // Attributes
    Route::prefix('attributes')->name('attributes.')->group(function () {
        // List và Form routes
        Route::get('/', [AttributeController::class, 'index'])->name('index');
        Route::get('/create', [AttributeController::class, 'create'])->name('create');
        Route::post('/', [AttributeController::class, 'store'])->name('store');

        // Quản lý thùng rác
        Route::get('/trashed', [AttributeController::class, 'trashed'])->name('trashed');
        Route::post('/{id}/restore', [AttributeController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [AttributeController::class, 'forceDelete'])->name('forceDelete');

        // Bulk Actions
        Route::post('/bulk-delete', [AttributeController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-toggle', [AttributeController::class, 'bulkToggle'])->name('bulk-toggle');

        // Resource routes
        Route::get('/{attribute}', [AttributeController::class, 'show'])->name('show');
        Route::get('/{attribute}/edit', [AttributeController::class, 'edit'])->name('edit');
        Route::put('/{attribute}', [AttributeController::class, 'update'])->name('update');
        Route::delete('/{attribute}', [AttributeController::class, 'destroy'])->name('destroy');

        // Variants
        Route::get('/variants', [AttributeController::class, 'variants'])->name('variants');
    });

    // Attribute Values
    Route::prefix('attributes/{attributeId}/values')->name('attribute_values.')->group(function (): void {
        Route::get('/', [AttributeValueController::class, 'index'])->name('index');
        Route::get('/create', [AttributeValueController::class, 'create'])->name('create');
        Route::post('/', [AttributeValueController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AttributeValueController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AttributeValueController::class, 'update'])->name('update');
        Route::delete('/{id}', [AttributeValueController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [AttributeValueController::class, 'restore'])->name('restore');

        // Bulk Actions
        Route::post('/bulk-delete', [AttributeValueController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-toggle', [AttributeValueController::class, 'bulkToggle'])->name('bulk-toggle');
    });
});
