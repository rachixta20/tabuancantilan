<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [MarketplaceController::class, 'landing'])->name('home');
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace');
Route::get('/marketplace/{product:slug}', [MarketplaceController::class, 'show'])->name('marketplace.show');
Route::get('/map', [MarketplaceController::class, 'map'])->name('map');

// Auth — rate limited to 5 attempts per minute
Route::middleware(['guest', 'throttle:5,1'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->withoutMiddleware('throttle:5,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Password Reset
Route::middleware(['guest', 'throttle:5,1'])->group(function () {
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Cart
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout', [CartController::class, 'placeOrder'])->name('cart.place-order');
});

// Buyer
Route::middleware('auth')->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', [BuyerController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [BuyerController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [BuyerController::class, 'orderDetail'])->name('order-detail');
    Route::get('/wishlist', [BuyerController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/{product}', [BuyerController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::patch('/orders/{order}/cancel', [BuyerController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/orders/{order}/review', [BuyerController::class, 'storeReview'])->name('review.store');
});

// Farmer
Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', [FarmerController::class, 'dashboard'])->name('dashboard');
    Route::post('/profile', [FarmerController::class, 'updateProfile'])->name('profile.update');
    Route::post('/location', [FarmerController::class, 'updateLocation'])->name('location.update');
    Route::post('/live', [FarmerController::class, 'toggleLive'])->name('live.toggle');
    Route::get('/products', [FarmerController::class, 'products'])->name('products');
    Route::get('/products/create', [FarmerController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [FarmerController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [FarmerController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [FarmerController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [FarmerController::class, 'deleteProduct'])->name('products.delete');
    Route::get('/orders', [FarmerController::class, 'orders'])->name('orders');
    Route::patch('/orders/{order}/status', [FarmerController::class, 'updateOrderStatus'])->name('orders.status');
});

// Reports
Route::middleware('auth')->prefix('reports')->name('reports.')->group(function () {
    Route::get('/create', [ReportController::class, 'create'])->name('create');
    Route::post('/', [ReportController::class, 'store'])->name('store');
});

// Messages
Route::middleware('auth')->prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [MessageController::class, 'index'])->name('index');
    Route::get('/{conversation}', [MessageController::class, 'show'])->name('show');
    Route::post('/start/{product}', [MessageController::class, 'startConversation'])->name('start');
    Route::post('/{conversation}/send', [MessageController::class, 'send'])->name('send');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'viewUser'])->name('users.view');
    Route::patch('/users/{user}/approve', [AdminController::class, 'approveSeller'])->name('users.approve');
    Route::post('/users/{user}/reject', [AdminController::class, 'rejectSeller'])->name('users.reject');
    Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('users.suspend');
    Route::patch('/users/{user}/reinstate', [AdminController::class, 'reinstateUser'])->name('users.reinstate');
    Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
    Route::patch('/users/{user}/verify', [AdminController::class, 'verifyUser'])->name('users.verify');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::patch('/products/{product}/approve', [AdminController::class, 'approveProduct'])->name('products.approve');
    Route::patch('/products/{product}/reject', [AdminController::class, 'rejectProduct'])->name('products.reject');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/audit-log', [AdminController::class, 'auditLog'])->name('audit-log');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/{report}', [AdminController::class, 'viewReport'])->name('reports.view');
    Route::patch('/reports/{report}/resolve', [AdminController::class, 'resolveReport'])->name('reports.resolve');
});
