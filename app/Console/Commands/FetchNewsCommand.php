<?php

namespace App\Console\Commands;

use App\Jobs\ProcessRssSourceJob;
use App\Models\Source;
use Illuminate\Console\Command;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     */
    protected $description = 'Mendelegasikan tugas penarikan RSS ke sistem Queue (Latar Belakang)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 1. Tarik semua sumber yang aktif
        $sources = Source::where('is_active', true)->get();

        if ($sources->isEmpty()) {
            $this->warn('Tidak ada sumber RSS aktif yang ditemukan.');
            return self::SUCCESS;
        }

        $this->info("Mendelegasikan {$sources->count()} sumber ke sistem Queue.");
        $this->newLine();

        // 2. Distribusi Antrean (Dispatch)
        $delaySeconds = 0;
        foreach ($sources as $index => $source) {
            // =========================================================
            // PENGUJIAN DELAY: Polite Crawling (Skenario 1)
            // Setiap sumber diberi jeda eksekusi bertahap.
            // Sumber 1: 0s, Sumber 2: 15s, Sumber 3: 30s.
            // Ini membuktikan sistem tidak menyerang portal berita serentak.
            // =========================================================
            
            ProcessRssSourceJob::dispatch($source)
                ->delay(now()->addSeconds($delaySeconds));

            $this->line("  [DISPATCHED] {$source->name} — dijadwalkan jalan dalam {$delaySeconds} detik.");
            
            // Tambah 15 detik untuk sumber berikutnya
            $delaySeconds += 15;
        }

        $this->newLine();
        $this->info('════ Seluruh tugas berhasil masuk antrean. ════');
        $this->info('Jalankan: php artisan queue:work untuk mulai memproses.');

        return self::SUCCESS;
    }
}
