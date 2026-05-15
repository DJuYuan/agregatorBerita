<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SourceController;
use Illuminate\Support\Facades\Route;

// ============================================================
// HALAMAN PUBLIK UTAMA
// ============================================================
Route::get('/', [PublicController::class, 'index'])->name('home');

// ============================================================
// PANEL ADMIN (DIBATASI OLEH AUTH)
// ============================================================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dasbor
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Test Toast (UI dev helper)
    Route::get('/test-toast', function () {
        return redirect()->route('admin.dashboard')->with('success', 'Sumber RSS Berhasil Ditambahkan');
    })->name('test-toast');

    // ── Kategori ──
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);

    // ── Sumber RSS ──
    Route::resource('sources', SourceController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::patch('/sources/{source}/toggle', [SourceController::class, 'toggle'])->name('sources.toggle');

    // ── Profil Admin ──
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Log Sistem ──
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});

require __DIR__.'/auth.php';

