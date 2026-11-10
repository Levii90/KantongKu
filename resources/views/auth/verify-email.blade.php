<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-6 bg-slate-950">
        <div class="w-full max-w-md p-8 rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-md shadow-2xl space-y-6">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-white tracking-tight">Kantong<span class="text-indigo-400">Ku</span></span>
            </div>

            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Verifikasi Email Anda</h1>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Terima kasih telah mendaftar! Sebelum memulai, silakan periksa kotak masuk email Anda dan klik tautan verifikasi yang baru saja kami kirimkan.
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium">
                    Tautan verifikasi baru telah berhasil dikirimkan ke alamat email yang Anda daftarkan.
                </div>
            @endif

            <div class="flex items-center justify-between pt-2">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-primary-button>
                        {{ __('Kirim Ulang Email') }}
                    </x-primary-button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 font-semibold transition">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
