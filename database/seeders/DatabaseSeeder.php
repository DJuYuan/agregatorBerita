<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =====================================================
        // 1. DATA ADMIN
        // =====================================================
        DB::table('admins')->insert([
            'name'       => 'Administrator',
            'email'      => 'admin@agregatorberita.com',
            'password'   => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // =====================================================
        // 2. DATA KATEGORI (Kategorisasi Statis)
        // =====================================================
        $catUtamaId = DB::table('categories')->insertGetId([
            'name'       => 'Berita Utama & Pemerintahan',
            'slug'       => 'berita-utama-pemerintahan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $catWisataId = DB::table('categories')->insertGetId([
            'name'       => 'Wisata Jogja',
            'slug'       => 'wisata-jogja',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $catKulinerId = DB::table('categories')->insertGetId([
            'name'       => 'Kuliner Jogja',
            'slug'       => 'kuliner-jogja',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $catHiburanId = DB::table('categories')->insertGetId([
            'name'       => 'Hiburan & Film',
            'slug'       => 'hiburan-film',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // =====================================================
        // 3. DATA SUMBER RSS (Sesuai Kategori Spesifik)
        // =====================================================
        DB::table('sources')->insert([
            // --- Kategori Utama & Pemerintahan ---
            [
                'category_id'     => $catUtamaId,
                'name'            => 'Harian Jogja (Utama)',
                'rss_url'         => 'https://harianjogja.com/rss',
                'is_active'       => true,
                'last_fetched_at' => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'category_id'     => $catUtamaId,
                'name'            => 'Detik Jogja (Regional)',
                'rss_url'         => 'https://www.detik.com/jogja/rss',
                'is_active'       => true,
                'last_fetched_at' => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            
            // --- Kategori Kabar Pemerintahan ---
            [
                'category_id'     => $catWisataId,
                'name'            => 'JPNN Jogja',
                'rss_url'         => 'https://jogja.jpnn.com/feed/rss',
                'is_active'       => true,
                'last_fetched_at' => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],

            // --- Kategori Kuliner, Wisata, Hiburan ---
            [
                'category_id'     => $catKulinerId,
                'name'            => 'iNews Yogya',
                'rss_url'         => 'https://yogya.inews.id/feed',
                'is_active'       => true,
                'last_fetched_at' => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }
}
