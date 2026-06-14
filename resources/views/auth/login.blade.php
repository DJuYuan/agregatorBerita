<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Admin Portal</h2>
        <p class="text-sm text-gray-500 mt-2">Secure access for authorized personnel only</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email Address" class="font-medium" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-200 focus:border-gray-400 focus:ring-gray-400 rounded-lg shadow-sm" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-6">
            <x-input-label for="password" value="Password" class="font-medium" />
            <x-text-input id="password" class="block mt-1 w-full border-gray-200 focus:border-gray-400 focus:ring-gray-400 rounded-lg shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-8">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors">
                Masuk Sebagai Admin
            </button>
        </div>
    </form>
</x-guest-layout>
