<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PaymentController;
use App\Http\Middleware\VerifySecToken;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware([VerifySecToken::class])->group(function () {
    Route::get('/order', [PaymentController::class, 'orderPayment']);
    Route::get('/payment', [PaymentController::class, 'payment']);
    Route::get('/status', [PaymentController::class, 'checkStatus']);
});
