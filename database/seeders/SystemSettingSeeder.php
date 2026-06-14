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
                'key' => 'crawler_user_agent',
                'value' => 'MuaraJogja-Bot/1.0',
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

        foreach ($settings as $setting) {
            \App\Models\SystemSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
