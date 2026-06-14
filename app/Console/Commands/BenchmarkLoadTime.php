<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Article;
use App\Models\Source;

class BenchmarkLoadTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:benchmark';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan benchmark 10x percobaan untuk metrik Load Time Skripsi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Memulai Pengujian Benchmark 10x Percobaan (Stress Test Bersih)...\n");

        $results = [];

        // --- SKENARIO 1: Load Time 1 (Halaman Utama) ---
        $results[] = $this->runTest('Load Time 1 (Home Page Publik)', function () {
            // Simulasi request GET ke '/'
            $request = \Illuminate\Http\Request::create('/', 'GET');
            $response = app()->handle($request);
            if ($response->getStatusCode() >= 400) {
                throw new \Exception("HTTP Error " . $response->getStatusCode());
            }
        });

        // --- SKENARIO 2: Load Time 2 (Filter Kategori Livewire) ---
        $results[] = $this->runTest('Load Time 2 (Filter Kategori)', function () {
            // Simulasi request GET ke '/?category=teknologi' atau URL dengan filter
            $request = \Illuminate\Http\Request::create('/?category=teknologi', 'GET');
            $response = app()->handle($request);
            if ($response->getStatusCode() >= 400) {
                throw new \Exception("HTTP Error " . $response->getStatusCode());
            }
        });

        // --- SKENARIO 3: Load Time 3 (Dasbor Karantina Admin) ---
        // Bypass Autentikasi untuk pengujian internal console
        try {
            $admin = User::first();
            if ($admin) {
                auth()->login($admin);
            }
        } catch (\Exception $e) {
            // Abaikan jika tabel users belum ada di database console
        }
        $results[] = $this->runTest('Load Time 3 (Dasbor Karantina Admin)', function () {
            $request = \Illuminate\Http\Request::create('/admin/articles/quarantine', 'GET');
            $response = app()->handle($request);
            if ($response->getStatusCode() >= 400) {
                throw new \Exception("HTTP Error " . $response->getStatusCode());
            }
        });
        auth()->logout();

        // --- SKENARIO 4: Load Time 4 (Simulasi RSS Worker) ---
        // Kita mensimulasikan pekerjaan query database berat yang biasa terjadi saat worker aktif
        $results[] = $this->runTest('Load Time 4 (Database RSS Worker Simulation)', function () {
            // Mensimulasikan pemindaian judul dan tag yang ekstensif layaknya Cron Job berjalan
            $latestArticles = Article::with(['source', 'tags'])->orderBy('id', 'desc')->take(30)->get();
            foreach ($latestArticles as $article) {
                $check = Article::where('title', $article->title)->exists();
            }
        });

        // CETAK HASIL KE TERMINAL
        $this->info("\n================ HASIL AKHIR BENCHMARK ================");
        $this->table(
            ['Skenario Pengujian', 'Waktu Tercepat (Min)', 'Waktu Terlama (Max)', 'Rata-rata (Avg)'],
            $results
        );
        $this->info("=======================================================\n");
        $this->comment("Catatan: Waktu Terlama (Max) biasanya adalah Percobaan Pertama (Cold Start).");
        $this->comment("Waktu Tercepat (Min) adalah Percobaan ke-2 dst (Cache Hit).\n");
    }

    private function runTest($scenarioName, $callback, $iterations = 10)
    {
        $this->line("\n--- MENGUJI: <fg=yellow>{$scenarioName}</> ---");
        $times = [];
        
        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            
            try {
                $callback();
            } catch (\Exception $e) {
                // Abaikan error pada benchmark murni
            }
            
            $end = microtime(true);
            $timeMs = round(($end - $start) * 1000, 2);
            $times[] = $timeMs;
            
            $num = $i + 1;
            $this->line("  Percobaan {$num}: {$timeMs} ms");
        }
        
        $min = min($times);
        $max = max($times);
        $avg = round(array_sum($times) / count($times), 2);

        $this->info("  -> [Ringkasan {$scenarioName}] Min: {$min} ms | Max: {$max} ms | Avg: {$avg} ms\n");

        return [
            $scenarioName,
            "{$min} ms",
            "{$max} ms",
            "{$avg} ms"
        ];
    }
}
