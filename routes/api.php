<?php

use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Vendor\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// In routes/api.php (or web.php based on your need)
Route::middleware('auth:sanctum')->get('/products', [ProductController::class, 'index']);
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']); // View Cart
    Route::post('/cart', [CartController::class, 'store']); // Add Product to Cart
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
});
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'vendor'])->group(function () {
    // Route::get('/vendor/products', [ProductController::class, 'index']); // List vendor products
    Route::post('/vendor/products', [ProductController::class, 'store']); // Add product
    Route::put('/vendor/products/{product}', [ProductController::class, 'update']); // Update product
    Route::delete('/vendor/products/{product}', [ProductController::class, 'destroy']); // Delete product
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::patch('/admin/products/{id}/approve', [AdminProductController::class, 'approve']);
    Route::patch('/admin/products/{id}/reject', [AdminProductController::class, 'reject']);

    //admin can view all orders

    Route::get('/admin-orders', [OrderController::class, 'allOrders']);
    Route::put('/admin-orders/{orderId}/status', [OrderController::class, 'updateOrderStatus']);
});


Route::middleware('auth:sanctum')->post('/place-order', [OrderController::class, 'placeOrder']);
Route::middleware('auth:sanctum')->get('/vendor-orders', [OrderController::class, 'vendorOrders']);

