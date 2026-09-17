<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\AuthController;
use App\Services\SlotService;

// 1. Dynamic Slot Availability Endpoint
Route::get('/slots/availability', function (Request $request) {
    $date = $request->query('date', now()->toDateString());
    $court = $request->query('court', array_keys(config('booking.courts', []))[0] ?? '');
    
    return response()->json(\App\Services\SlotService::getCourtAvailability($date, $court));
})->name('slots.availability');

// 2. Unified Landing Portal
Route::get('/', function () {
    return view('portal');
})->name('portal');

// 3. Client Booking Pages
Route::get('/book', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/book', [BookingController::class, 'store'])->name('bookings.store');

// 4. Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 5. Protected Admin Panel (Requires Auth and Admin Middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.updateStatus');
    Route::delete('/bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');
});

use App\Http\Controllers\PaymentController;

// Payment Flow Routes
Route::get('/payment/checkout/{payment}', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::get('/payment/return', [PaymentController::class, 'handleReturn'])->name('payment.return');
Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');