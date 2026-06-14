<?php

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
Route::get('/go/{slug}', [PublicController::class, 'go'])->name('article.go');

// ============================================================
// PANEL ADMIN (DIBATASI OLEH AUTH)
// ============================================================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dasbor
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Kategori ──
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);

    // ── Sumber RSS ──
    Route::resource('sources', SourceController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::patch('/sources/{source}/toggle', [SourceController::class, 'toggle'])->name('sources.toggle')->withTrashed();
    Route::post('/sources/{id}/restore', [SourceController::class, 'restore'])->name('sources.restore');
    Route::post('/sources/{source}/test', [SourceController::class, 'testFetch'])->name('sources.test')->withTrashed();

    // ── Master Artikel ──
    Route::get('/articles', \App\Livewire\Admin\ArticleManager::class)->name('articles.index');
    Route::get('/articles/quarantine', \App\Livewire\Admin\QuarantineManager::class)->name('articles.quarantine');

    // ── Pengaturan Sistem ──
    Route::get('/settings', \App\Livewire\Admin\SettingsManager::class)->name('settings.index');



    // ── Log Sistem ──
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});

require __DIR__.'/auth.php';

