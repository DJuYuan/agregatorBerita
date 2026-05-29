<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengaturan Sistem</h1>
            <p class="mt-1 text-sm text-gray-500">Konfigurasi dinamis seluruh modul agregator berita tanpa menyentuh file kode.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="space-y-8">
            @foreach($groupedSettings as $groupName => $items)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    {{-- Header Group --}}
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700">{{ $groupName }}</h2>
                    </div>

                    {{-- Form Fields --}}
                    <div class="p-6 divide-y divide-gray-100 space-y-6">
                        @foreach($items as $index => $item)
                            {{-- Cari indeks asli dari array settings agar wire:model sinkron --}}
                            @php
                                $originalIndex = collect($settings)->search(fn($s) => $s['id'] === $item['id']);
                            @endphp

                            <div class="pt-6 first:pt-0 grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-800">{{ $item['label'] }}</label>
                                    <p class="mt-1 text-xs text-gray-400">{{ $item['description'] ?? '' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    @if($item['type'] === 'number')
                                        <input wire:model="settings.{{ $originalIndex }}.value" type="number" placeholder="Contoh: 10" class="w-full max-w-md rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    @elseif($item['type'] === 'boolean')
                                        <select wire:model="settings.{{ $originalIndex }}.value" class="w-full max-w-md rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="true">Aktif</option>
                                            <option value="false">Nonaktif</option>
                                        </select>
                                    @else
                                        <input wire:model="settings.{{ $originalIndex }}.value" type="text" placeholder="Masukkan {{ strtolower($item['label']) }}..." class="w-full max-w-md rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Submit --}}
            <div class="flex justify-end pt-4">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-sm">
                    <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Simpan Konfigurasi
                </button>
            </div>
        </div>
    </form>
</div>
