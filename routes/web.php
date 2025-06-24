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
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Client\Auth\RegisterController;
use App\Http\Controllers\Client\Auth\LoginController;
use App\Http\Controllers\Client\ErrorController;
use App\Http\Controllers\Client\Auth\ForgotPasswordController;
use App\Http\Controllers\Client\Auth\GoogleController;
use App\Http\Controllers\Client\Auth\LoginHistoryController;
use App\Http\Controllers\Client\Auth\ResetPasswordController;
use App\Http\Controllers\Client\Auth\VerifyEmailController;
use App\Http\Controllers\Client\ChangePasswordController;
use App\Http\Controllers\Client\ProfileController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Two\GoogleProvider;

use function Laravel\Prompts\password;

//Auth Admin
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('showLoginForm');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
//Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
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
        Route::delete('/{id}/force', [AttributeController::class, 'forceDelete'])->name('force-delete');


        // Bulk Actions
        Route::post('/bulk-delete', [AttributeController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-restore', [AttributeController::class, 'bulkRestore'])->name('bulk-restore');
        Route::post('/bulk-force-delete', [AttributeController::class, 'bulkForceDelete'])->name('bulk-force-delete');
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



//Client
Route::get('/', function () {
    return view('client.home');
})->name('home');
Route::middleware('web')->group(function () {
    // ->middleware(['auth', 'verified'])
    //login & registerregister
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('showRegister');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    //404
    Route::fallback([ErrorController::class, 'notFound']);
    //reset password nhập email
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendRequestLinkEmail'])->name('password.email');

    //form nhập mk mới
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    //Xác thực email khi đăng ký thành công
    //trang thông báo
    Route::get('/email/verify', function () {
        return view('client.auth.verify-email');
    })->middleware('auth')->name('verification.notice');
    //Xử lý xác thực từ link
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->middleware(['signed'])
        ->name('verification.verify');;
    //Gửi lại link xác thực
    Route::post('/email/verification-notification', function (Request $req) {
        $req->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Đã gửi lại liên kết xác thực email!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');
    //Trang lịch sử đăng nhập
    Route::get('/login-history', [LoginHistoryController::class, 'loginHistory'])->middleware(['auth', 'verified'])->name('loginHistory');
    //đăng xuất tất cả hỏi các tb
    Route::post('/logout-all', [LoginHistoryController::class, 'logoutAll'])->middleware(['auth'])->name('logoutAll');
    //điều hướng đến gg
    Route::get('/auth/google', function () {
        return Socialite::driver('google')->redirect();
    })->name('google.login');
    
    //Callback từ gg
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

    //Profile 
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('showProfile')->middleware('auth');
    Route::post('/profile', [ProfileController::class, 'avatar'])->name('avatar');
    // Route::get('/profile-imformation', [ProfileController::class, 'showImformation'])->name('showImformation')->middleware('auth');
    Route::put('/update-profile', [ProfileController::class, 'updateProfile'])->name('updateProfile');
    //changepassword
    Route::post('/profile/change-password', [ChangePasswordController::class, 'changePassword'])->name('changePassword');

});
