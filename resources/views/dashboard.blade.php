<x-admin-layout>

    {{-- ── Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dasbor Pemantauan</h1>
            <p class="mt-1 text-sm text-gray-500">
                Selamat datang kembali, <span class="font-semibold text-gray-700">{{ Auth::user()->name }}</span>.
                Berikut ringkasan kondisi sistem agregasi berita saat ini.
            </p>
        </div>
        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full border border-green-200">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            Sistem Berjalan
        </span>
    </div>

    {{-- ── Kartu Statistik Cepat ──────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        {{-- Total Berita --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Berita</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($stats['total_articles']) }}</p>
            </div>
        </div>

        {{-- Sumber Aktif --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-green-50 text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 5c7.18 0 13 5.82 13 13M6 11a7 7 0 017 7M6 17a1 1 0 110-2 1 1 0 010 2z"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber Aktif</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">
                    {{ $stats['total_sources_active'] }}
                    <span class="text-sm font-normal text-gray-400">/ {{ $stats['total_sources'] }}</span>
                </p>
            </div>
        </div>

        {{-- Total Kategori --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $stats['total_categories'] }}</p>
            </div>
        </div>

        {{-- Shortcut Tambah Sumber --}}
        <a href="{{ route('admin.sources.create') }}" class="group bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-sm border border-blue-500 p-5 flex items-center gap-4 hover:shadow-md hover:from-blue-700 hover:to-blue-800 transition-all">
            <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-white/20 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-blue-200 uppercase tracking-wider">Aksi Cepat</p>
                <p class="text-sm font-bold text-white mt-0.5 group-hover:underline">Tambah Sumber RSS</p>
            </div>
        </a>

    </div>

    {{-- ── Tabel Log Aktivitas Mesin ───────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">Log Aktivitas Mesin</h2>
                <p class="text-xs text-gray-400 mt-0.5">Riwayat 15 eksekusi terakhir dari <code class="bg-gray-100 px-1 rounded">news:fetch</code></p>
            </div>
            <div class="flex items-center gap-4 text-xs text-gray-400">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>Sukses</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>Gagal</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sumber RSS</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($activityLog as $log)
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

                            {{-- Nama Sumber --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $log['source'] }}</span>
                            </td>

                            {{-- Keterangan --}}
                            <td class="px-6 py-4">
                                @if($log['type'] === 'success')
                                    <span class="text-sm text-gray-600">{{ $log['detail'] }}</span>
                                @else
                                    <span class="text-sm text-red-600 font-mono text-xs bg-red-50 px-2 py-0.5 rounded" title="{{ $log['detail'] }}">
                                        {{ Str::limit($log['detail'], 80) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Waktu --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-xs text-gray-400" title="{{ $log['time']->format('d M Y, H:i:s') }}">
                                    {{ $log['time_human'] }}
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm font-medium text-gray-500">Belum Ada Log Aktivitas</p>
                                    <p class="text-xs text-gray-400">Jalankan <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600 font-mono">php artisan news:fetch</code> untuk mengisi log pertama kali.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activityLog->isNotEmpty())
            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 text-right">
                <p class="text-xs text-gray-400">Menampilkan 15 entri terbaru dari seluruh riwayat log.</p>
            </div>
        @endif
    </div>

    {{-- Confirm modal sudah di-load global di admin-layout --}}
    <x-confirm-modal title="Konfirmasi Aksi" body="Apakah Anda yakin ingin melanjutkan aksi ini?" />

</x-admin-layout>

