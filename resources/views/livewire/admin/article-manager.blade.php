<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Master Data Artikel</h1>
            <p class="mt-1 text-sm text-gray-500">Pantau seluruh artikel berita yang ditarik oleh mesin agregator.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="fixed top-20 right-6 z-50 w-full max-w-sm max-h-80 overflow-y-auto bg-white rounded-xl shadow-2xl ring-1 ring-gray-200 p-4 transition-all" role="alert">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-gray-900">Berhasil</p>
                    <p class="mt-1 text-sm text-gray-600">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Filter & Search Bar --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="w-full md:w-1/3 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul berita..." class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        
        <div class="w-full md:w-2/3 flex gap-4">
            <select wire:model.live="filterCategory" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterSource" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Semua Portal Sumber</option>
                @foreach($sources as $source)
                    <option value="{{ $source->id }}">{{ $source->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabel Artikel --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
        <!-- Loading overlay saat memproses -->
        <div wire:loading class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 flex items-center justify-center">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Berita</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sumber</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Ditarik</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Views</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($articles as $article)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 mb-1 leading-snug">{{ $article->title }}</div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($article->tags as $tag)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $article->source->name ?? '-' }}</div>
                                <div class="text-xs text-blue-600">{{ $article->source->category->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-gray-900">{{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $article->published_at ? $article->published_at->format('H:i') : $article->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ number_format($article->clicks ?? 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ $article->link }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500 transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        Buka
                                    </a>
                                    
                                    <button
                                        onclick="confirm('Yakin ingin menghapus artikel ini secara permanen?') || event.stopImmediatePropagation()"
                                        wire:click="deleteArticle({{ $article->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 hover:text-red-700 focus:outline-none transition-colors"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                Belum ada artikel yang ditarik atau sesuai dengan pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
