<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// ── Inspirational quote (bawaan Laravel) ──────────────────────────────────
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// ══════════════════════════════════════════════════════════════════════════
//  JADWAL OTOMATIS — AGREGATOR BERITA LOKAL YOGYAKARTA
// ══════════════════════════════════════════════════════════════════════════

/**
 * Mesin Penarik Berita: Berjalan setiap jam.
 * Menarik seluruh sumber RSS aktif dan menyimpan artikel baru ke database.
 * Log sukses/gagal dicatat otomatis oleh command untuk monitoring Dasbor Admin.
 */
Schedule::command('news:fetch')
    ->everyThirtyMinutes()
    ->name('Tarik Berita RSS Otomatis')
    ->withoutOverlapping()   // Cegah eksekusi ganda jika run sebelumnya belum selesai
    ->appendOutputTo(storage_path('logs/fetch-news.log'));

/**
 * Modul Retensi Database (Native Laravel Prunable):
 * Menggantikan custom command news:cleanup dengan mekanisme bawaan Laravel
 * yang terintegrasi langsung dengan trait MassPrunable di model Article.
 * Lebih efisien: satu bulk DELETE query tanpa looping per-record.
 */
Schedule::command('model:prune', ['--model' => [\App\Models\Article::class]])
    ->dailyAt('00:00')
    ->name('Pangkas Artikel Kadaluarsa (>30 hari)')
    ->appendOutputTo(storage_path('logs/cleanup-articles.log'));
