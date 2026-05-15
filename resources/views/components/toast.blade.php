@if(session('success') || session('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 4000)" 
         x-transition:enter="transform ease-out duration-300 transition" 
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" 
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0" 
         x-transition:leave="transition ease-in duration-100" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed bottom-0 right-0 z-50 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end">
        
        <div class="max-w-sm w-full {{ session('success') ? 'bg-green-50' : 'bg-red-50' }} shadow-lg rounded-lg pointer-events-auto ring-1 {{ session('success') ? 'ring-green-500' : 'ring-red-500' }} ring-opacity-20 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        @if(session('success'))
                            <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium {{ session('success') ? 'text-green-800' : 'text-red-800' }}">
                            {{ session('success') ? 'Berhasil!' : 'Terjadi Kesalahan!' }}
                        </p>
                        <p class="mt-1 text-sm {{ session('success') ? 'text-green-700' : 'text-red-700' }}">
                            {{ session('success') ?? session('error') }}
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false" class="{{ session('success') ? 'bg-green-50 text-green-500 hover:text-green-600' : 'bg-red-50 text-red-500 hover:text-red-600' }} rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ session('success') ? 'focus:ring-green-500' : 'focus:ring-red-500' }}">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
