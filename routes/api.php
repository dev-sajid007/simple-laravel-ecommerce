<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes for CRM integration and mobile app
Route::prefix('v1')->group(function () {
    // Public API routes
    Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::get('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'show']);
    Route::get('/categories', [\App\Http\Controllers\Api\ProductController::class, 'categories']);
    
    // Customer authentication API
    Route::post('/customer/register', [\App\Http\Controllers\Api\CustomerController::class, 'register']);
    Route::post('/customer/login', [\App\Http\Controllers\Api\CustomerController::class, 'login']);
    
    // Protected API routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/customer/profile', [\App\Http\Controllers\Api\CustomerController::class, 'profile']);
        Route::put('/customer/profile', [\App\Http\Controllers\Api\CustomerController::class, 'updateProfile']);
        Route::get('/customer/orders', [\App\Http\Controllers\Api\CustomerController::class, 'orders']);
        
        // Cart API
        Route::get('/cart', [\App\Http\Controllers\Api\CartController::class, 'index']);
        Route::post('/cart/add', [\App\Http\Controllers\Api\CartController::class, 'add']);
        Route::put('/cart/update/{id}', [\App\Http\Controllers\Api\CartController::class, 'update']);
        Route::delete('/cart/remove/{id}', [\App\Http\Controllers\Api\CartController::class, 'remove']);
        
        // Order API
        Route::post('/orders/place', [\App\Http\Controllers\Api\OrderController::class, 'place']);
        Route::get('/orders/{orderNumber}/track', [\App\Http\Controllers\Api\OrderController::class, 'track']);
    });
});