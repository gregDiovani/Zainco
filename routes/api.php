<?php

use App\Http\Controllers\API\ProductCategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('categories', [ProductCategoryController::class, 'all']);
    Route::get('products', [ProductController::class, 'all']);
    Route::post('products/create', [ProductController::class, 'makeProduct']);
    Route::post('products/update/{id}', [ProductController::class, 'updateProduct']);
    Route::post('categories/create', [ProductCategoryController::class, 'makeCategory']);
    Route::post('categories/update/{id}', [ProductCategoryController::class, 'updateProductCategory']);
    Route::post('categories/destroy/{id}', [ProductCategoryController::class, 'destroy']);
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('updateprofile', [UserController::class, 'updateProfile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('transaction', [TransactionController::class, 'all']);
    Route::post('checkout', [TransactionController::class, 'checkout']);
});
