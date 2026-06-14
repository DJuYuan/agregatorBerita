<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogFailed;
use App\Models\LogSuccess;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all'); // all | success | failed

        // Ambil log sukses
        $logsSuccess = collect();
        if ($filter === 'all' || $filter === 'success') {
            $logsSuccess = LogSuccess::with('source')
                ->latest('fetched_at')
                ->get()
                ->map(fn($log) => [
                    'type'       => 'success',
                    'source'     => $log->source?->name ?? 'Sumber Dihapus',
                    'source_url' => $log->source?->rss_url ?? '-',
                    'detail'     => "{$log->total_fetched} berita berhasil ditarik",
                    'total'      => $log->total_fetched,
                    'time'       => $log->fetched_at,
                    'time_human' => $log->fetched_at->diffForHumans(),
                ]);
        }

        // Ambil log gagal
        $logsFailed = collect();
        if ($filter === 'all' || $filter === 'failed') {
            $logsFailed = LogFailed::with('source')
                ->latest('failed_at')
                ->get()
                ->map(fn($log) => [
                    'type'       => 'failed',
                    'source'     => $log->source?->name ?? 'Sumber Dihapus',
                    'source_url' => $log->source?->rss_url ?? '-',
                    'detail'     => \App\Helpers\LogErrorTranslator::translate($log->error_message),
                    'total'      => 0,
                    'time'       => $log->failed_at,
                    'time_human' => $log->failed_at->diffForHumans(),
                ]);
        }

        // Gabungkan dan urutkan berdasarkan waktu terbaru
        $logs = $logsSuccess
            ->concat($logsFailed)
            ->sortByDesc('time')
            ->values();

        // Ringkasan statistik
        $stats = [
            'total_success' => LogSuccess::count(),
            'total_failed'  => LogFailed::count(),
            'total_fetched' => LogSuccess::sum('total_fetched'),
        ];

        return view('admin.logs.index', compact('logs', 'stats', 'filter'));
    }
}
