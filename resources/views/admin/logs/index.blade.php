<x-admin-layout>

    {{-- ── Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Log Sistem</h1>
            <p class="mt-1 text-sm text-gray-500">Riwayat lengkap aktivitas eksekusi mesin penarik berita <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600 font-mono text-xs">news:fetch</code>.</p>
        </div>
        {{-- Filter Tabs --}}
        <div class="flex items-center bg-white border border-gray-200 rounded-xl p-1 shadow-sm gap-1">
            <a href="{{ route('admin.logs.index') }}"
               class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all {{ $filter === 'all' ? 'bg-gray-900 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                Semua
            </a>
            <a href="{{ route('admin.logs.index', ['filter' => 'success']) }}"
               class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all {{ $filter === 'success' ? 'bg-green-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                Sukses
            </a>
            <a href="{{ route('admin.logs.index', ['filter' => 'failed']) }}"
               class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all {{ $filter === 'failed' ? 'bg-red-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                Gagal
            </a>
        </div>
    </div>

    {{-- ── Kartu Statistik Ringkasan ──────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Eksekusi</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($stats['total_success'] + $stats['total_failed']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-green-50 text-green-600 flex-shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sukses</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($stats['total_success']) }}</p>
                <p class="text-xs text-gray-400">{{ number_format($stats['total_fetched']) }} berita dikumpulkan</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-red-50 text-red-600 flex-shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Gagal</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($stats['total_failed']) }}</p>
                @if($stats['total_failed'] > 0)
                    <p class="text-xs text-red-500">Periksa URL sumber yang bermasalah</p>
                @endif
            </div>
        </div>

    </div>

    {{-- ── Tabel Log Lengkap ──────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900">
                Riwayat Aktivitas
                @if($filter !== 'all')
                    <span class="ml-2 text-sm font-normal {{ $filter === 'success' ? 'text-green-600' : 'text-red-600' }}">
                        — Filter: {{ ucfirst($filter) }}
                    </span>
                @endif
            </h2>
            <span class="text-xs text-gray-400">{{ $logs->count() }} entri</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sumber RSS</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-44">Waktu</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors">

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log['type'] === 'success')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        Sukses
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                        Gagal
                                    </span>
                                @endif
                            </td>

                            {{-- Nama & URL Sumber --}}
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $log['source'] }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs" title="{{ $log['source_url'] }}">{{ $log['source_url'] }}</p>
                            </td>

                            {{-- Keterangan --}}
                            <td class="px-6 py-4">
                                @if($log['type'] === 'success')
                                    <span class="text-sm text-gray-700">{{ $log['detail'] }}</span>
                                @else
                                    <span class="inline-block text-xs text-red-700 font-mono bg-red-50 border border-red-100 px-2 py-1 rounded max-w-sm break-all" title="{{ $log['detail'] }}">
                                        {{ Str::limit($log['detail'], 100) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Waktu --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <p class="text-xs text-gray-400" title="{{ $log['time']->format('d M Y, H:i:s') }}">
                                    {{ $log['time_human'] }}
                                </p>
                                <p class="text-xs text-gray-300 mt-0.5">{{ $log['time']->format('d M Y, H:i') }}</p>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm font-medium text-gray-500">Belum Ada Log</p>
                                    <p class="text-xs text-gray-400">Jalankan <code class="bg-gray-100 px-1.5 py-0.5 rounded font-mono text-gray-600">php artisan news:fetch</code> untuk mengisi log pertama kali.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->isNotEmpty())
            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <p class="text-xs text-gray-400">Menampilkan seluruh riwayat log dari semua sumber.</p>
                <a href="{{ route('admin.sources.index') }}"
                   class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    Kelola Sumber Bermasalah →
                </a>
            </div>
        @endif

    </div>

    <x-confirm-modal title="Konfirmasi" body="Apakah Anda yakin?" />

</x-admin-layout>
