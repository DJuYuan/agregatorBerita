<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    protected $fillable = [
        'source_id',
        'guid',
        'title',
        'slug',
        'link',
        'description',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    // ── Relasi ───────────────────────────────────────────────────────────

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function tags(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    // ── Prunable — Retensi Otomatis (>30 Hari) ───────────────────────────
    // Menggunakan MassPrunable agar lebih efisien: tidak memuat model satu-per-satu
    // ke memory. Satu DELETE query dengan subquery, cukup untuk skala besar.
    // Gambar dihapus oleh constraint cascadeOnDelete() di level database.

    /**
     * Mendefinisikan kriteria artikel yang harus dipangkas.
     * Semua artikel dengan published_at > 30 hari yang lalu akan dihapus.
     */
    public function prunable(): Builder
    {
        return static::where('published_at', '<', now()->subDays(30));
    }

    /**
     * Hook yang dipanggil sebelum setiap record dipangkas.
     * Digunakan sebagai pengaman untuk menghapus file gambar lokal
     * jika kelak aplikasi beralih ke penyimpanan lokal (Storage::disk).
     * Untuk URL eksternal, penghapusan record di tabel images ditangani
     * oleh constraint cascadeOnDelete() di database — sudah cukup efisien.
     */
    protected function pruning(): void
    {
        // Placeholder untuk penghapusan file storage lokal di masa depan:
        // $this->images->each(function (Image $image) {
        //     Storage::disk('public')->delete($image->image_path);
        // });
    }

    // ── Local Scope: Filter Pencarian & Kategori ──────────────────────────
    // Menggunakan when() agar klausul WHERE hanya ditambahkan
    // jika parameter benar-benar diisi. Tidak ada IF bersarang di controller.

    /**
     * Scope filter utama — mendukung kombinasi parameter ?q= dan &category=.
     * Eager loading dilakukan di controller agar scope tetap reusable.
     *
     * @param Builder $query
     * @param array   $filters ['search' => string, 'category' => string]
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            // Filter teks: mencocokkan judul ATAU deskripsi (case-insensitive)
            ->when(
                $filters['search'] ?? null,
                fn(Builder $q, string $search) => $q->where(
                    fn(Builder $inner) => $inner
                        ->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                )
            )
            // Filter kategori: hanya artikel dari sumber yang terhubung ke kategori tertentu
            ->when(
                $filters['category'] ?? null,
                fn(Builder $q, string $slug) => $q->whereHas(
                    'source.category',
                    fn(Builder $inner) => $inner->where('slug', $slug)
                )
            );
    }
}
