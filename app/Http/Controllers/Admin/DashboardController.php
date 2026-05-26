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
        $totalArticles = Article::count();

        // ── Statistik Cepat ──────────────────────────────────────
        $stats = [
            'total_articles'       => $totalArticles,
            'total_sources_active' => Source::where('is_active', true)->count(),
            'total_categories'     => Category::count(),
            'total_sources'        => Source::count(),
            'total_clicks'         => Article::sum('clicks'),
        ];

        // ── Peringkat Portal Kontributor Teratas (Leaderboard) ──
        $sourceLeaderboard = Source::withCount('articles')
            ->orderByDesc('articles_count')
            ->take(5)
            ->get()
            ->map(function ($source) use ($totalArticles) {
                $percentage = $totalArticles > 0 ? round(($source->articles_count / $totalArticles) * 100, 1) : 0;
                return [
                    'name' => $source->name,
                    'count' => $source->articles_count,
                    'percentage' => $percentage,
                ];
            });

        // ── 5 Berita Terpopuler (Top 5 Clicks) ──
        $trendingArticles = Article::with('source')
            ->orderByDesc('clicks')
            ->where('clicks', '>', 0)
            ->take(5)
            ->get();

        // ── Log Aktivitas (15 Terbaru, Gabungan Sukses + Gagal) ──
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

        return view('dashboard', compact('stats', 'activityLog', 'sourceLeaderboard', 'trendingArticles'));
    }
}
