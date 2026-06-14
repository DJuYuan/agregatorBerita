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

        // Kategori dan Sumber RSS tidak di-seed secara default untuk production.
        // Silakan tambahkan Kategori dan Sumber URL melalui Panel Admin.
    }
}
