<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// ── Command bawaan Laravel (inspire) telah dihapus untuk rilis produksi ──

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
 * Modul Retensi Artikel — Tahap 1: Karantina (Soft Delete).
 * Memindahkan artikel yang sudah melewati batas masa aktif ke masa karantina
 * (kolom deleted_at terisi). Artikel tidak lagi tampil di halaman publik,
 * tetapi masih bisa dipantau oleh admin melalui Dasbor Karantina.
 * Dijalankan setiap hari pada tengah malam.
 */
Schedule::command('articles:quarantine')
    ->dailyAt('00:00')
    ->name('Karantina Artikel Kadaluarsa')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/quarantine-articles.log'));

/**
 * Modul Retensi Artikel — Tahap 2: Pemusnahan Permanen (Hard Delete).
 * Menggunakan mekanisme bawaan Laravel MassPrunable untuk menghancurkan
 * secara permanen artikel yang sudah berada di karantina dan usianya
 * telah melampaui batas quarantine_retention_days dari pengaturan sistem.
 * Dijalankan pada tengah malam (5 menit setelah karantina selesai).
 */
Schedule::command('model:prune', ['--model' => [\App\Models\Article::class]])
    ->dailyAt('00:05')
    ->name('Musnahkan Artikel Karantina Kadaluarsa')
    ->appendOutputTo(storage_path('logs/prune-articles.log'));
