<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PerfumeController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// Beranda (landing page)
Route::get('/', [PerfumeController::class, 'home'])->name('home');

// Parfum: dashboard (khusus user login) dan detail
Route::get('/parfum', [PerfumeController::class, 'index'])->name('perfumes.index');
Route::get('/parfum/{perfume}', [PerfumeController::class, 'show'])->name('perfumes.show');

// Login, registrasi, logout
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Premium dan chat konsultasi (wajib login)
Route::middleware('auth')->group(function () {
    Route::get('/premium', [PremiumController::class, 'index'])->name('premium');
    Route::post('/premium/checkout', [PremiumController::class, 'checkout'])->name('premium.checkout');
    Route::get('/premium/pembayaran/{subscription}', [PremiumController::class, 'payment'])->name('premium.payment');
    Route::post('/premium/pembayaran/{subscription}/konfirmasi', [PremiumController::class, 'confirm'])->name('premium.confirm');
    Route::post('/premium/pembayaran/{subscription}/batal', [PremiumController::class, 'cancel'])->name('premium.cancel');

    Route::post('/parfum/{perfume}/ulasan', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
});