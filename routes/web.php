<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Product Routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/category/{category}', [ProductController::class, 'category'])->name('products.category');
    Route::get('/search', [ProductController::class, 'search'])->name('products.search');
});

// Cart Routes
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// Customer Authentication Routes
Route::prefix('customer')->group(function () {
    Route::get('/register', [CustomerController::class, 'showRegistrationForm'])->name('customer.register');
    Route::post('/register', [CustomerController::class, 'register']);
    Route::get('/login', [CustomerController::class, 'showLoginForm'])->name('customer.login');
    Route::post('/login', [CustomerController::class, 'login']);
    Route::post('/logout', [CustomerController::class, 'logout'])->name('customer.logout');
    
    // Protected customer routes
    Route::middleware('auth:customer')->group(function () {
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
        Route::get('/profile', [CustomerController::class, 'profile'])->name('customer.profile');
        Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');
        Route::get('/orders', [CustomerController::class, 'orders'])->name('customer.orders');
        Route::get('/orders/{id}', [CustomerController::class, 'orderDetails'])->name('customer.orders.show');
    });
});

// Order Routes
Route::middleware('auth:customer')->prefix('orders')->group(function () {
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/place', [OrderController::class, 'place'])->name('orders.place');
    Route::get('/{orderNumber}/track', [OrderController::class, 'track'])->name('orders.track');
});

// Guest checkout (optional)
Route::prefix('guest')->group(function () {
    Route::get('/checkout', [OrderController::class, 'guestCheckout'])->name('guest.checkout');
    Route::post('/place-order', [OrderController::class, 'guestPlaceOrder'])->name('guest.place-order');
});