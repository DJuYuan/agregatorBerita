<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'description', 'type', 'group'];

    // Pilar 1: Skema Pengaturan Default (Mencegah blank UI)
    public const DEFAULT_SETTINGS = [
        [
            'key' => 'crawler_user_agent',
            'value' => 'WartaJogja-Bot/1.0',
            'label' => 'Identitas Bot Pengeruk (User Agent)',
            'description' => 'User-agent crawler saat mengambil RSS (Wajib diisi agar tidak di-block).',
            'type' => 'text',
            'group' => 'crawler',
        ],
        [
            'key' => 'active_retention_days',
            'value' => '30',
            'label' => 'Masa Aktif Artikel (Hari)',
            'description' => 'Artikel yang usianya melebihi batas ini akan dipindahkan ke karantina (disembunyikan dari publik).',
            'type' => 'number',
            'group' => 'database',
        ],
        [
            'key' => 'quarantine_retention_days',
            'value' => '90',
            'label' => 'Masa Karantina Artikel (Hari)',
            'description' => 'Artikel yang sudah berada di karantina melebihi batas ini akan dimusnahkan secara permanen.',
            'type' => 'number',
            'group' => 'database',
        ],
    ];

    /**
     * Memastikan semua pengaturan default ada di dalam database.
     * Dipanggil oleh SettingsManager UI sebelum ditampilkan ke layar.
     */
    public static function initializeDefaults(): void
    {
        foreach (self::DEFAULT_SETTINGS as $setting) {
            self::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }

    /**
     * Pilar 2: Caching Berperforma Tinggi
     * Mengambil nilai pengaturan secara instan dari RAM.
     */
    public static function getValue(string $key, $default = null)
    {
        // Load seluruh settings ke cache secara permanen (hingga di-clear)
        $settings = \Illuminate\Support\Facades\Cache::rememberForever('system_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Pilar 3: Cache Invalidation (Sinkronisasi Otomatis)
     * Hook bawaan Laravel ini akan terpicu otomatis setiap kali admin klik Save di UI.
     */
    protected static function booted()
    {
        // Setiap kali model dibuat, diubah, atau dihapus, hapus cache-nya
        static::saved(function ($setting) {
            \Illuminate\Support\Facades\Cache::forget('system_settings');
        });

        static::deleted(function ($setting) {
            \Illuminate\Support\Facades\Cache::forget('system_settings');
        });
    }
}
