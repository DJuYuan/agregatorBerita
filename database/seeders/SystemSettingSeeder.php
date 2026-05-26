<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'fetch_interval_minutes',
                'value' => '30',
                'label' => 'Interval Penarikan Berita (Menit)',
                'type' => 'number',
                'group' => 'crawler',
            ],
            [
                'key' => 'article_retention_days',
                'value' => '30',
                'label' => 'Batas Usia Artikel Disimpan (Hari)',
                'type' => 'number',
                'group' => 'database',
            ],
            [
                'key' => 'crawler_user_agent',
                'value' => 'MuaraJogja-Bot/1.0',
                'label' => 'Identitas Bot Pengeruk (User Agent)',
                'description' => 'User-agent crawler saat mengambil RSS (Wajib diisi agar tidak di-block).',
                'type' => 'text',
                'group' => 'crawler',
            ],
            [
                'key' => 'app_tagline',
                'value' => 'Kompilasi Informasi Yogyakarta Hari Ini',
                'label' => 'Slogan Website Utama',
                'type' => 'text',
                'group' => 'general',
            ],
        ];

        foreach ($settings as $setting) {
            \App\Models\SystemSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
