<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/clients', [ClientController::class, 'index']);

// Route::middleware('auth:sanctum')->group(function () {
Route::apiResource('products', ProductController::class);
Route::get('suppliers', [SupplierController::class, 'index']);
// });
