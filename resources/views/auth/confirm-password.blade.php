<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-6 bg-slate-950">
        <div class="w-full max-w-md p-8 rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-md shadow-2xl space-y-6">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-white tracking-tight">Kantong<span class="text-indigo-400">Ku</span></span>
            </div>

            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Konfirmasi Keamanan</h1>
                <p class="text-xs text-slate-400 mt-1">Ini adalah area aman. Harap masukkan password Anda untuk melanjutkan.</p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                <div class="flex justify-end pt-2">
                    <x-primary-button class="w-full">
                        {{ __('Konfirmasi Password') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
