<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\PaymentDashboardController;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [PaymentDashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/payments', [PaymentDashboardController::class, 'payments'])->name('dashboard.payments');
    Route::post('dashboard/payments/{id}/flag', [PaymentDashboardController::class, 'toggleFlag'])->name('dashboard.payments.flag');
});

require __DIR__ . '/settings.php';
