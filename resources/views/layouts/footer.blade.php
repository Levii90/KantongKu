<footer class="border-t border-slate-800/80 bg-slate-950 py-10 text-slate-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center gap-6 text-center">
        <!-- Brand & Mission -->
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <span class="text-base font-bold text-white tracking-tight">Kantong<span class="text-indigo-400">Ku</span></span>
        </div>

        <p class="text-xs text-slate-500 max-w-md">
            Aplikasi Personal Finance Tracker cerdas dan aman untuk mengelola anggaran, memantau pengeluaran, dan meraih kemandirian finansial.
        </p>

        <!-- Navigation Links -->
        <div class="flex flex-wrap items-center justify-center gap-6 text-xs font-medium text-slate-400">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-200 transition-colors">Dashboard</a>
            <a href="{{ route('transactions.index') }}" class="hover:text-slate-200 transition-colors">Transaksi</a>
            <a href="{{ route('reports.index') }}" class="hover:text-slate-200 transition-colors">Laporan</a>
            <a href="{{ route('profile.edit') }}" class="hover:text-slate-200 transition-colors">Pengaturan Profil</a>
        </div>

        <!-- Social Icons -->
        <div class="flex items-center gap-4 text-slate-400">
            <a href="#" class="p-2 rounded-full hover:bg-slate-800/80 hover:text-slate-200 transition-colors" aria-label="Github">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                </svg>
            </a>
            <a href="#" class="p-2 rounded-full hover:bg-slate-800/80 hover:text-slate-200 transition-colors" aria-label="Twitter">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
            </a>
            <a href="#" class="p-2 rounded-full hover:bg-slate-800/80 hover:text-slate-200 transition-colors" aria-label="Email">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </a>
        </div>

        <div class="text-[11px] text-slate-600">
            &copy; {{ date('Y') }} KantongKu. Seluruh hak cipta dilindungi undang-undang.
        </div>
    </div>
</footer>
