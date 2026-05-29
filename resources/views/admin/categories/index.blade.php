<x-admin-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Kategori</h1>
            <p class="mt-1 text-sm text-gray-500">Tambah dan hapus kategori yang digunakan sebagai label berita.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Tambah Kategori -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Kategori Baru
                </h2>
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Pariwisata"
                            class="block w-full px-3 py-2 border {{ $errors->has('name') ? 'border-red-500 bg-red-50' : 'border-gray-300' }} rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-400">Slug akan dibuat otomatis dari nama.</p>
                    </div>

                    <div class="mb-4">
                        <label for="keywords" class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci Penapisan (Opsional)</label>
                        <textarea
                            id="keywords"
                            name="keywords"
                            rows="3"
                            placeholder="Contoh: wisata, destinasi, liburan, pantai (pisahkan dengan koma)"
                            class="block w-full px-3 py-2 border {{ $errors->has('keywords') ? 'border-red-500 bg-red-50' : 'border-gray-300' }} rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >{{ old('keywords') }}</textarea>
                        @error('keywords')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-400">Kosongkan untuk meloloskan semua artikel tanpa filter kata kunci.</p>
                    </div>
                    <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Kategori
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Kategori -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-900">Daftar Kategori</h2>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full font-medium">{{ $categories->count() }} kategori</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Slug</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kata Kunci</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Sumber</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($categories as $category)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">{{ $category->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $category->slug }}</code>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($category->keywords)
                                            <span class="text-xs text-gray-600 line-clamp-2" title="{{ $category->keywords }}">{{ $category->keywords }}</span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tanpa Filter</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm text-gray-600 font-semibold">{{ $category->sources_count }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button
                                            x-data
                                            @click="$dispatch('open-confirm-modal', {
                                                url: '{{ route('admin.categories.destroy', $category) }}',
                                                method: 'DELETE'
                                            })"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 hover:text-red-700 focus:outline-none transition-colors"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-400">
                                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        Belum ada kategori. Tambahkan kategori pertama Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <x-confirm-modal
        title="Hapus Kategori?"
        body="Apakah Anda yakin ingin menghapus kategori ini? Semua sumber RSS yang terhubung dengan kategori ini akan ikut terpengaruh!"
    />
</x-admin-layout>
