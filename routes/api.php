<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\StatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/status', [StatusController::class, 'index']);

// Route::middleware('api.key')->group(['prefix' => 'products'], function () {
//   Route::get('/', [ProductController::class, 'index']);
//   Route::get('/{code}', [ProductController::class, 'show'])->where(['code' => '[0-9]+']);
//   Route::delete('/{code}', [ProductController::class, 'destroy'])->where(['code' => '[0-9]+']);
//   Route::put('/{code}', [ProductController::class, 'update'])->where(['code' => '[0-9]+']);
// });
Route::middleware('api.key')->group(function () {
  Route::get('/products', [ProductController::class, 'index']);
  Route::get('/products/{code}', [ProductController::class, 'show']);
  Route::put('/products/{code}', [ProductController::class, 'update']);
  Route::delete('/products/{code}', [ProductController::class, 'destroy']);
});
