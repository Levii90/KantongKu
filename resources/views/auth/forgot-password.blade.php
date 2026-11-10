<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-6 bg-slate-950">
        <div class="w-full max-w-md p-8 rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-md shadow-2xl space-y-6">
            <!-- Brand -->
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-white tracking-tight">Kantong<span class="text-indigo-400">Ku</span></span>
            </div>

            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Lupa Password?</h1>
                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                    Masukkan alamat email akun Anda dan kami akan mengirimkan tautan untuk mengatur ulang password baru.
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-2" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="m@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-slate-200 transition">
                        Kembali ke Login
                    </a>
                    <x-primary-button>
                        {{ __('Kirim Tautan Reset') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
