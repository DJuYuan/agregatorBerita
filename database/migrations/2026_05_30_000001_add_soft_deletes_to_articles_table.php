<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom deleted_at ke tabel articles
     * untuk mengaktifkan fitur Soft Delete (hapus semu / karantina).
     *
     * Ketika kolom ini terisi (tidak null), Laravel & Eloquent secara otomatis
     * menyembunyikan artikel dari semua kueri normal (WHERE deleted_at IS NULL),
     * sehingga artikel tidak tampil di halaman publik tanpa perlu dihapus permanen.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->softDeletes(); // Menambahkan kolom `deleted_at` TIMESTAMP NULL
        });
    }

    /**
     * Mengembalikan skema ke kondisi semula (rollback).
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
