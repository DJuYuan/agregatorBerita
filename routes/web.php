<?php

use App\Http\Controllers\PublicController;

use Illuminate\Support\Facades\Route;

// ============================================================
// HALAMAN PUBLIK UTAMA
// ============================================================
Route::get('/', [PublicController::class, 'index'])->name('home');

// (End of routes)
