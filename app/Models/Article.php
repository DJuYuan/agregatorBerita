<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use SoftDeletes, MassPrunable;

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

    // ── Prunable — Pemusnahan Permanen Artikel Karantina ─────────────────
    // Menggunakan MassPrunable untuk efisiensi: satu bulk forceDelete() query
    // tanpa loop per-record. Hanya artikel yang SUDAH di karantina (trashed)
    // dan melampaui batas quarantine_retention_days yang akan dimusnahkan.

    /**
     * Mendefinisikan kriteria artikel yang harus dimusnahkan secara permanen.
     * Kueri ini hanya menargetkan artikel yang SUDAH di-soft-delete (karantina)
     * DAN usianya telah melampaui batas masa karantina dari pengaturan sistem.
     */
    public function prunable(): Builder
    {
        // Ambil batas masa karantina dari pengaturan sistem (default: 90 hari)
        $days = (int) SystemSetting::getValue('quarantine_retention_days', 90);

        // Hanya musnahkan artikel yang sudah di-karantina (deleted_at terisi)
        // DAN usia publikasinya sudah melampaui batas karantina
        return static::onlyTrashed()->where('deleted_at', '<', now()->subDays($days));
    }

    /**
     * Hook yang dipanggil sebelum setiap record dipangkas.
     * Untuk URL eksternal, penghapusan cascade images ditangani oleh
     * constraint cascadeOnDelete() di level database — sudah efisien.
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
