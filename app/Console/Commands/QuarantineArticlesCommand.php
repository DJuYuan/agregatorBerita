<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\SystemSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class QuarantineArticlesCommand extends Command
{
    /**
     * Nama dan tanda tangan perintah konsol.
     */
    protected $signature = 'articles:quarantine';

    /**
     * Deskripsi perintah konsol.
     */
    protected $description = 'Memindahkan artikel yang melewati masa aktif ke karantina (soft delete) secara otomatis dan massal';

    /**
     * Eksekusi perintah konsol.
     *
     * Alur kerja:
     * 1. Ambil konfigurasi batas masa aktif artikel dari tabel system_settings.
     * 2. Eksekusi satu kueri UPDATE massal (bukan loop per-record) untuk
     *    mengisi kolom `deleted_at` pada artikel-artikel yang sudah melewati
     *    batas usia. Ini jauh lebih efisien daripada memuat tiap model satu-per-satu.
     * 3. Catat hasil operasi ke dalam log sistem.
     */
    public function handle(): int
    {
        $this->info('━━━ Memulai Proses Karantina Artikel ━━━');

        // Ambil batas masa aktif dari pengaturan sistem (default: 30 hari)
        $days = (int) \App\Models\SystemSetting::getValue('active_retention_days', 30);

        $this->line("  [INFO] Batas masa aktif artikel: {$days} hari.");
        $this->line("  [INFO] Mencari artikel dengan usia publikasi > {$days} hari...");

        // Eksekusi bulk soft-delete: satu kueri UPDATE ke database
        // Karena model Article menggunakan trait SoftDeletes,
        // memanggil delete() di sini akan mengisi kolom deleted_at (BUKAN menghapus fisik)
        $count = Article::where('published_at', '<', now()->subDays($days))->delete();

        if ($count > 0) {
            $message = "Berhasil memindahkan {$count} artikel ke karantina (usia > {$days} hari).";
            $this->info("  ✔ {$message}");
            Log::info("  [QUARANTINE] {$message}");
        } else {
            $this->line('  [INFO] Tidak ada artikel yang perlu dikarantina saat ini.');
            Log::info('  [QUARANTINE] Tidak ada artikel yang memenuhi kriteria karantina.');
        }

        $this->info('━━━ Proses Karantina Selesai ━━━');

        return self::SUCCESS;
    }
}
