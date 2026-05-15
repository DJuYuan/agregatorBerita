<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\LogFailed;
use App\Models\LogSuccess;
use App\Models\Source;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Statistik Cepat ──────────────────────────────────────
        $stats = [
            'total_articles'       => Article::count(),
            'total_sources_active' => Source::where('is_active', true)->count(),
            'total_categories'     => Category::count(),
            'total_sources'        => Source::count(),
        ];

        // ── Log Aktivitas (10 Terbaru, Gabungan Sukses + Gagal) ──
        $logsSuccess = LogSuccess::with('source')
            ->latest('fetched_at')
            ->limit(20)
            ->get()
            ->map(fn ($log) => [
                'type'       => 'success',
                'source'     => $log->source?->name ?? 'Sumber Dihapus',
                'detail'     => "{$log->total_fetched} berita berhasil ditarik",
                'time'       => $log->fetched_at,
                'time_human' => $log->fetched_at->diffForHumans(),
            ]);

        $logsFailed = LogFailed::with('source')
            ->latest('failed_at')
            ->limit(20)
            ->get()
            ->map(fn ($log) => [
                'type'       => 'failed',
                'source'     => $log->source?->name ?? 'Sumber Dihapus',
                'detail'     => $log->error_message,
                'time'       => $log->failed_at,
                'time_human' => $log->failed_at->diffForHumans(),
            ]);

        // Gabungkan dan urutkan berdasarkan waktu (terbaru dulu)
        $activityLog = $logsSuccess
            ->concat($logsFailed)
            ->sortByDesc('time')
            ->take(15)
            ->values();

        return view('dashboard', compact('stats', 'activityLog'));
    }
}
