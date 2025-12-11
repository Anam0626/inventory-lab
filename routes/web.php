<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BorrowingController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk memproses form peminjaman (Method POST)
Route::post('/peminjaman/store', [BorrowingController::class, 'store'])->name('peminjaman.store')->middleware('auth');

// Route untuk melihat hasil preprocessing (Method GET)
Route::get('/peminjaman/history', [BorrowingController::class, 'history'])->name('peminjaman.history')->middleware('auth');