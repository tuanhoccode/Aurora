<?php


use Dom\Comment;

use App\Models\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use function Laravel\Prompts\password;
use Laravel\Socialite\Facades\Socialite;

use Laravel\Socialite\Two\GoogleProvider;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ShopController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Client\ErrorController;
use App\Http\Controllers\admin\CommentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Client\ReviewController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\ProfileController;


use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\WishlistController;
use App\Http\Controllers\Client\Auth\LoginController;
use App\Http\Controllers\Client\Auth\GoogleController;
use App\Http\Controllers\Client\ShoppingCartController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\ProductGalleryController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Client\Auth\RegisterController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Client\ChangePasswordController;
use App\Http\Controllers\Client\Auth\VerifyEmailController;
use App\Http\Controllers\Client\Auth\LoginHistoryController;
use App\Http\Controllers\Client\Auth\ResetPasswordController;
use App\Http\Controllers\Client\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Client\ContactController as ClientContactController;
use App\Http\Controllers\Client\ProductController as ClientProductController;
//Auth Admin
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('showLoginForm');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
//Admin
Route::middleware(['auth', 'check.admin-or-employee'])->prefix('admin')->name('admin.')->group(function () {
    // Media Upload Route
    Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');

    Route::get('refunds', [RefundController::class, 'index'])->name('refunds.index');
    Route::patch('refunds/{refund}', [RefundController::class, 'update'])->name('refunds.update');
    // Blog Comments Routes
    Route::prefix('blog/comments')->name('blog.comments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BlogCommentController::class, 'index'])->name('index');
        Route::post('/{comment}/reply', [\App\Http\Controllers\Admin\BlogCommentController::class, 'reply'])->name('reply');
        Route::patch('/{comment}/approve', [\App\Http\Controllers\Admin\BlogCommentController::class, 'approve'])->name('approve');
        Route::post('/{comment}/toggle-status', [\App\Http\Controllers\Admin\BlogCommentController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{comment}', [\App\Http\Controllers\Admin\BlogCommentController::class, 'destroy'])->name('destroy');
    });

    // Blog Posts Routes
    Route::prefix('blog/posts')->name('blog.posts.')->group(function () {
        // Trash Management - Phải đặt trước các route có tham số {post}
        Route::get('/trash', [\App\Http\Controllers\Admin\BlogPostController::class, 'trash'])->name('trash');
        Route::post('/empty-trash', [\App\Http\Controllers\Admin\BlogPostController::class, 'emptyTrash'])->name('empty-trash');

        // Danh sách và tạo bài viết
        Route::get('/', [\App\Http\Controllers\Admin\BlogPostController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BlogPostController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\BlogPostController::class, 'store'])->name('store');

        // Các route có tham số {post}
        Route::prefix('{post}')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BlogPostController::class, 'show'])->name('show');
            Route::get('/edit', [\App\Http\Controllers\Admin\BlogPostController::class, 'edit'])->name('edit');
            Route::put('/', [\App\Http\Controllers\Admin\BlogPostController::class, 'update'])->name('update');
            Route::delete('/', [\App\Http\Controllers\Admin\BlogPostController::class, 'destroy'])->name('destroy');
            Route::patch('/restore', [\App\Http\Controllers\Admin\BlogPostController::class, 'restore'])->name('restore');
            Route::delete('/force-delete', [\App\Http\Controllers\Admin\BlogPostController::class, 'forceDelete'])->name('force-delete');

            // Single Post Actions
            Route::patch('/publish', [\App\Http\Controllers\Admin\BlogPostController::class, 'publish'])->name('publish');
            Route::patch('/draft', [\App\Http\Controllers\Admin\BlogPostController::class, 'draft'])->name('draft');
        });

        // Bulk Actions
        Route::post('/bulk-activate', [\App\Http\Controllers\Admin\BlogPostController::class, 'bulkActivate'])->name('bulk-activate');
        Route::post('/bulk-deactivate', [\App\Http\Controllers\Admin\BlogPostController::class, 'bulkDeactivate'])->name('bulk-deactivate');
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\BlogPostController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Blog Categories Routes
    Route::prefix('blog/categories')->name('blog.categories.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'store'])->name('store');

        // Trash management routes - must come before {category} routes
        Route::get('/trash', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'trash'])->name('trash');
        Route::delete('/trash/empty', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'emptyTrash'])->name('trash.empty');

        // Category routes with parameters
        Route::get('/{category}', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'destroy'])->name('destroy');

        // Bulk actions
        Route::post('/bulk-activate', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'bulkActivate'])->name('bulk-activate');
        Route::post('/bulk-deactivate', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'bulkDeactivate'])->name('bulk-deactivate');
        Route::post('/bulk-destroy', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'bulkDestroy'])->name('bulk-destroy');

        // Restore and force delete routes
        Route::patch('/{id}/restore', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'forceDelete'])->name('force-delete');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Liên hệ
    Route::resource('contacts', AdminContactController::class);
    Route::post('contacts/{id}/update-status', [AdminContactController::class, 'updateStatus'])->name('contacts.updateStatus');
    Route::post('contacts/{id}/reply', [AdminContactController::class, 'reply'])->name('contacts.reply');

    // Route cho upload ảnh từ CKEditor
    // Route::post('/upload', [UploadController::class, 'upload'])->name('upload');
    Route::patch('contacts/{id}/restore', [AdminContactController::class, 'restore'])->name('contacts.restore');
    Route::delete('contacts/{id}/force-delete', [AdminContactController::class, 'forceDelete'])->name('contacts.forceDelete');
    Route::get('contacts-trash', [AdminContactController::class, 'trash'])->name('contacts.trash');



    //Coupon routes
    Route::middleware('admin.only')->prefix('coupons')->name('coupons.')->group(function () {
        Route::post('/bulk-delete', [CouponController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-restore', [CouponController::class, 'bulkRestore'])->name('bulk-restore');
        Route::post('/bulk-force-delete', [CouponController::class, 'bulkForceDelete'])->name('bulk-force-delete');

        Route::post('/', [CouponController::class, 'store'])->name('store');

        Route::get('/', [CouponController::class, 'index'])->name('index');
        Route::get('/create', [CouponController::class, 'create'])->name('create');
        Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
        Route::put('/{coupon}', [CouponController::class, 'update'])->name('update');
        Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy');

        Route::get('/trash', [CouponController::class, 'trash'])->name('trash');
        Route::put('/{id}/restore', [CouponController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [CouponController::class, 'forceDelete'])->name('force-delete');
    });
    /// Orders Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/update-status', [OrderController::class, 'updateStatusForm'])->name('orders.update-status-form');
    Route::patch('/orders/{id}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    // Products Routes
    Route::prefix('products')->name('products.')->group(function () {
        // List và Form routes
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        // Product Gallery Images
        // Xóa ảnh gallery của sản phẩm
        // Route::delete('/{product}/gallery/{image}', [ProductGalleryController::class, 'delete'])
        //     ->name('delete-gallery-image');
        Route::delete('/{product}/gallery', [ProductController::class, 'deleteGalleryImage'])
            ->name('delete-gallery-image');


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
        // Xóa ảnh gallery của biến thể
        Route::delete('/{product}/variants/delete-gallery-image/{image}', [ProductVariantController::class, 'deleteGalleryImage'])->name('variants.delete-gallery-image');

        // Xử lý ảnh gallery cho biến thể
        Route::post('/variants/upload-gallery', [ProductVariantController::class, 'uploadGallery'])->name('variants.upload-gallery');
        Route::post('/{product}/variants/delete-image', [ProductVariantController::class, 'deleteVariantImage'])->name('variants.delete-image');
        Route::post('/{product}/variants/{variant}/delete-default-image', [ProductVariantController::class, 'deleteDefaultImage'])->name('variants.delete-default-image');

        // Lấy danh sách ảnh của biến thể
        Route::get('/{product}/variants/{variant}/images', [ProductVariantController::class, 'getImages'])->name('variants.images');

        // Xóa ảnh biến thể
        Route::delete('/{product}/variants/{variant}/images/{image}', [ProductVariantController::class, 'deleteImage'])->name('variants.delete-image');
    });
    Route::resource('users', UserController::class)->middleware('admin.only');
    Route::patch('users/{user}/change-status', [UserController::class, 'changeStatus'])->name('users.changeStatus')->middleware('admin.only');
    Route::patch('users/{user}/change-role', [UserController::class, 'changeRole'])->name('admin.users.changeRole')->middleware('admin.only');
    // Brands Routes
    Route::middleware('admin.only')->prefix('brands')->name('brands.')->group(function () {
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
        Route::put('/{brand}/toggle-visible', [BrandController::class, 'toggleVisible'])->name('toggle-visible');
        Route::put('/{brand}/toggle-active', [BrandController::class, 'toggleActive'])->name('toggle-active');
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
    Route::middleware('admin.only')->prefix('attributes')->name('attributes.')->group(function () {
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
        Route::get('/trashed', [AttributeValueController::class, 'trashed'])->name('trashed');
        Route::post('/{id}/restore', [AttributeValueController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [AttributeValueController::class, 'forceDelete'])->name('forceDelete');


        // Bulk Actions
        Route::post('/bulk-delete', [AttributeValueController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-toggle', [AttributeValueController::class, 'bulkToggle'])->name('bulk-toggle');
    });

    // Quản lý tồn kho sản phẩm - product_stocks
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/products/{product}/stocks', [StockController::class, 'productStocks'])->name('products.stocks.index');
    Route::resource('stocks', StockController::class)->except(['show']);
    Route::get('/stocks/{stock}', [StockController::class, 'show'])->name('stocks.show');

    // Quản lý ảnh phụ
    Route::get('/product-images', [ProductGalleryController::class, 'all'])->name('product-images.all');
    Route::get('/product-images/create', [ProductGalleryController::class, 'createGeneral'])->name('product-images.create');
    Route::get('/product-images/{id}/edit', [ProductGalleryController::class, 'edit'])->name('product-images.edit');
    Route::put('/product-images/{id}', [ProductGalleryController::class, 'update'])->name('product-images.update');
    Route::post('/product-images/store', [ProductGalleryController::class, 'storeGeneral'])->name('product-images.store-general');
    Route::delete('/product-images/{id}', [ProductGalleryController::class, 'destroy'])->name('product-images.destroy');

    //Quản lý bình luận
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [CommentController::class, 'index'])->name('comments');
        Route::get('/{type}/{id}', [CommentController::class, 'showComment'])->name('showComment');
        Route::patch('/approve/{type}/{id}', [CommentController::class, 'approve'])->name('approve');
        Route::patch('/reject/{type}/{id}', [CommentController::class, 'reject'])->name('reject');
        Route::get('/trash-comment', [CommentController::class, 'trashComments'])->name('trashComments');

        Route::delete('/delete/{id}', [CommentController::class, 'destroyComment'])->name('destroyComment');
        Route::put('/restore/{id}', [CommentController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [CommentController::class, 'forceDelete'])->name('forceDelete');
        Route::post('/bulk-restore', [CommentController::class, 'bulkRestore'])->name('bulkRestore');
        //Admin phản hồi bình luận
        Route::post('/reply', [ReviewController::class, 'reply'])->name('reply');
        Route::post('/{type}/reply/{id}', [CommentController::class, 'reply'])->name('replies');

        //search comment
        Route::get('/comment-search', [CommentController::class, 'searchComment'])->name('searchComment');
    });

    // Banner Routes
    Route::middleware('admin.only')->prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/', [BannerController::class, 'store'])->name('store');
        Route::get('/{banner}', [BannerController::class, 'show'])->name('show');
        Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
        Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
        Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
        Route::put('/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('toggle-status');
    });
});




//Client

Route::get('/', [HomeController::class, 'index'])->name('home');

// Blog Routes
Route::prefix('blog')->name('blog.')->group(function () {
    // Trang chủ blog
    Route::get('/', [\App\Http\Controllers\Client\BlogController::class, 'index'])
        ->name('index');

    // Xem chi tiết bài viết
    Route::get('/{slug}', [\App\Http\Controllers\Client\BlogController::class, 'show'])
        ->name('show');

    // Bình luận
    Route::post('/{post}/comments', [\App\Http\Controllers\Client\BlogController::class, 'storeComment'])
        ->name('comments.store')
        ->middleware('throttle:3,1'); // Giới hạn 3 request mỗi phút

    // Danh mục bài viết
    Route::get('/category/{slug}', [\App\Http\Controllers\Client\BlogController::class, 'showByCategory'])
        ->name('category');

    // Bài viết theo tag
    Route::get('/tag/{slug}', [\App\Http\Controllers\Client\BlogController::class, 'showByTag'])
        ->name('tag');

    // Lưu trữ bài viết theo tháng/năm
    Route::get('/archive/{year}/{month?}', [\App\Http\Controllers\Client\BlogController::class, 'archive'])
        ->where(['year' => '[0-9]{4}', 'month' => '0[1-9]|1[0-2]'])
        ->name('archive');
});

// Product routes
Route::get('/products/{slug}', [\App\Http\Controllers\Client\ProductController::class, 'show'])->name('client.products.show');

// Yêu thích
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

// Chi tiết sản phẩm
Route::get('/product/{slug}', [ClientProductController::class, 'show'])
    ->name('client.product.show');

// Chi tiết danh mục

Route::middleware(['auth'])->prefix('client')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/show', [OrderController::class, 'show'])->name('orders.show');
});
// Client Category
Route::prefix('categories')->name('client.categories.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Client\CategoryController::class, 'index'])->name('index');
    Route::get('/{id}', [\App\Http\Controllers\Client\CategoryController::class, 'show'])->name('show');
});

Route::middleware('web')->group(function () {
    //login & register
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

    //form nhập mật khẩu mới
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    //Xác thực email khi đăng ký thành công
    Route::get('/email/verify', function () {
        return view('client.auth.verify-email');
    })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $req) {
        $req->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Đã gửi lại liên kết xác thực email!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    //Trang lịch sử đăng nhập
    Route::get('/login-history', [LoginHistoryController::class, 'loginHistory'])->middleware(['auth', 'verified'])->name('loginHistory');
    Route::post('/logout-all', [LoginHistoryController::class, 'logoutAll'])->middleware(['auth'])->name('logoutAll');
    //điều hướng đến gg
    Route::get('/auth/google', function () {
        return Socialite::driver('google')->redirect();
    })->name('google.login');

    //Callback từ gg
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
    //Xác thực email mới được vào tk
    Route::middleware(['auth', 'verified'])->group(function(){
    //Profile
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('showProfile')->middleware('auth');
    Route::post('/profile', [ProfileController::class, 'avatar'])->name('avatar');
    Route::put('/update-profile', [ProfileController::class, 'updateProfile'])->name('updateProfile');
    //change password
    Route::post('/profile/change-password', [ChangePasswordController::class, 'changePassword'])->name('changePassword');

    // Shopping Cart routes
    Route::get('/shopping-cart', [ShoppingCartController::class, 'index'])->name('shopping-cart.index');
    // Route::get('/shopping-cart/checkout', [ShoppingCartController::class, 'checkout'])->name('shopping-cart.checkout');
    Route::post('/shopping-cart/add', [ShoppingCartController::class, 'addToCart'])->name('shopping-cart.add');
    Route::get('/shopping-cart/count', [ShoppingCartController::class, 'getCartCount'])->name('shopping-cart.count');
    Route::delete('/shopping-cart/remove/{itemId}', [ShoppingCartController::class, 'removeFromCart'])->name('shopping-cart.remove');
    Route::get('/shopping-cart/mini-cart', [ShoppingCartController::class, 'miniCart'])->name('shopping-cart.mini-cart');
    Route::match(['put', 'post'], '/shopping-cart/update/{item}', [ShoppingCartController::class, 'update'])->name('shopping-cart.update');
    //Checkout
    Route::put('/shopping-cart/update/{item}', [ShoppingCartController::class, 'update'])->name('shopping-cart.update');
    // Checkout
    Route::get('/shopping-cart/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/shopping-cart/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/shopping-cart/vnpay/return', [CheckoutController::class, 'vnpayReturn'])->name('vnpay.return');
    Route::get('/shopping-cart/checkout/success/{order_number}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
    Route::post('/checkout/apply-coupon-by-id', [CheckoutController::class, 'applyCouponById'])->name('checkout.apply-coupon-by-id');
    Route::post('/checkout/remove-coupon', action: [CheckoutController::class, 'removeCoupon'])->name('checkout.remove-coupon');
    Route::post('/checkout/clear-coupon-session', action: [CheckoutController::class, 'clearCouponSession'])->name('checkout.clear-coupon-session');
    Route::get('/checkout/success/{order_number}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/update', [CheckoutController::class, 'update'])->name('checkout.update');
    Route::get('/checkout/retry-payment/{order_code}', [CheckoutController::class, 'retryPendingPayment'])->name('checkout.retry-payment');
    Route::get('/checkout/vnpay-return', [CheckoutController::class, 'vnpayReturn'])->name('checkout.vnpay-return');
    // Address Management
    Route::get('/address/create', [CheckoutController::class, 'createAddress'])->name('address.create');
    Route::post('/address/store', [CheckoutController::class, 'storeAddress'])->name('address.store');
    Route::get('/address/edit/{id?}', [CheckoutController::class, 'editAddress'])->name('address.edit');
    Route::post('/address/save', [CheckoutController::class, 'saveAddress'])->name('address.save');

    // Trang liên hệ
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

    // Đơn hàng (Order)
    Route::middleware(['auth'])->prefix('client')->group(function () {
        Route::get('/orders', [\App\Http\Controllers\Client\OrderController::class, 'index'])->name('orders');
        Route::get('/orders/show', [\App\Http\Controllers\Client\OrderController::class, 'show'])->name('orders.show')->middleware('admin.only');
    });
    Route::post('/refund', [RefundController::class, 'store'])->name('client.refund.store');
    // Client Category
    Route::prefix('categories')->name('client.categories.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\CategoryController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Client\CategoryController::class, 'show'])->name('show');
    });
});
});

Route::middleware(['web', 'auth'])->prefix('client')->name('client.')->group(function () {
    // Shopping Cart Routes
    Route::get('/shopping-cart', [ShoppingCartController::class, 'index'])->name('shopping-cart.index')->middleware(['auth', 'verified']);
    Route::post('/shopping-cart/add', [ShoppingCartController::class, 'addToCart'])->name('shopping-cart.add')->middleware(['auth', 'verified']);
    Route::delete('/shopping-cart/remove/{itemId}', [ShoppingCartController::class, 'removeFromCart'])->name('shopping-cart.remove')->middleware(['auth', 'verified']);
    Route::delete('/shopping-cart/bulk-delete', [ShoppingCartController::class, 'bulkDelete'])->name('shopping-cart.bulk-delete')->middleware(['auth', 'verified']);

    Route::middleware(['auth', 'verified'])->group(function(){
        Route::get('/orders', [\App\Http\Controllers\Client\OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [\App\Http\Controllers\Client\OrderController::class, 'show'])->name('orders.show');
        //Tracking đơn hàng
        Route::get('/orders/{order}/tracking', [\App\Http\Controllers\Client\OrderController::class, 'tracking'])->name('orders.tracking');
        //Hủy đơn hàng
        Route::put('/orders/{order}/cancel', [\App\Http\Controllers\Client\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/orders/sync-statuses', [\App\Http\Controllers\Client\OrderController::class, 'syncOrderStatuses'])->name('orders.sync-statuses');
        //Đánh giá sản phẩm
        Route::post('/reviews/{product}', [ReviewController::class, 'store'])->name('store');

    });
});


Route::get('/search', [App\Http\Controllers\Client\SearchController::class, 'index'])->name('search');

Route::get('/list-product', [\App\Http\Controllers\Client\ShopController::class, 'index'])->name('shop');

Route::post('/shopping-cart/bulk-delete', [App\Http\Controllers\Client\ShoppingCartController::class, 'bulkDelete'])->name('shopping-cart.bulk-delete');
