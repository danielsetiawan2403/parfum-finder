<?php

use App\Http\Controllers\Admin;
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

// Panel admin (wajib login dan role = admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Data Parfum: daftar, tambah, ubah, hapus
    Route::resource('parfum', Admin\PerfumeController::class)
        ->parameters(['parfum' => 'perfume'])
        ->except('show')
        ->names('perfumes');

    // Data User (customer)
    Route::get('user', [Admin\UserController::class, 'index'])->name('users.index');
    Route::delete('user/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Chat konsultasi: daftar user, room chat per user, dan balas
    Route::get('chat', [Admin\ChatController::class, 'index'])->name('chats.index');
    Route::get('chat/{user}', [Admin\ChatController::class, 'show'])->name('chats.show');
    Route::post('chat/{user}', [Admin\ChatController::class, 'reply'])->name('chats.reply');
});
