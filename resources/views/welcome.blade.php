<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>KantongKu - Personal Finance Tracker</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="bg-slate-950 text-slate-100 antialiased selection:bg-indigo-500 selection:text-white min-h-screen flex flex-col justify-between overflow-x-hidden relative">

        <!-- Glowing Ridges Ambient Background Gradients -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-gradient-to-b from-indigo-600/15 via-violet-600/5 to-transparent blur-3xl pointer-events-none -z-10"></div>
        <div class="absolute top-1/3 -right-48 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <div class="absolute top-2/3 -left-48 w-96 h-96 bg-rose-500/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <!-- Navbar -->
        <header class="w-full border-b border-slate-800/80 bg-slate-950/70 backdrop-blur-md sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Brand -->
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">Kantong<span class="text-indigo-400">Ku</span></span>
                </a>

                <!-- Nav Actions -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition-all">
                            <span>Buka Dashboard</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition-all">
                            <span>Daftar Gratis</span>
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1">
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 sm:pt-24 pb-16 text-center">
                <!-- Pill Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-indigo-500/30 bg-indigo-500/10 text-indigo-300 text-xs font-semibold mb-6 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    <span>Aplikasi Personal Finance Tracker Modern</span>
                </div>

                <!-- Main Headline -->
                <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight max-w-4xl mx-auto leading-tight sm:leading-none">
                    Kelola uang Anda dengan bijak, pantau <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-violet-400 to-emerald-400">setiap rupiah</span> dengan tenang.
                </h1>

                <p class="mt-6 text-sm sm:text-base text-slate-400 max-w-2xl mx-auto leading-relaxed">
                    KantongKu membantu Anda mencatat arus kas pemasukan dan pengeluaran, menghitung saldo secara akurat langsung dari ledger transaksi, serta memberikan insight finansial pintar.
                </p>

                <!-- CTAs -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3.5">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/25 transition-all">
                            <span>Menuju Dashboard Anda</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/25 transition-all">
                            <span>Mulai Sekarang — Gratis</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="{{ route('login') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl border border-slate-800 bg-slate-900/60 hover:bg-slate-800 text-slate-200 text-sm font-semibold transition-colors">
                            <span>Masuk ke Akun</span>
                        </a>
                    @endauth
                </div>

                <!-- Preview App Mockup -->
                <div class="mt-14 max-w-5xl mx-auto rounded-2xl border border-slate-800/80 bg-slate-900/40 p-2 sm:p-4 shadow-2xl backdrop-blur-md">
                    <div class="rounded-xl border border-slate-800 bg-slate-950 p-6 sm:p-8 text-left">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                                <span class="text-xs text-slate-500 font-mono ml-2">kantongku.local/dashboard</span>
                            </div>
                            <span class="text-xs text-indigo-400 font-semibold">Live Preview</span>
                        </div>

                        <!-- Mini Mock Dashboard -->
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                                <div class="text-[11px] text-slate-400 uppercase font-bold">Total Saldo</div>
                                <div class="text-xl font-extrabold text-white mt-1">Rp 12.500.000</div>
                                <div class="text-[11px] text-emerald-400 mt-1">Saldo Keuangan Aktif</div>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                                <div class="text-[11px] text-slate-400 uppercase font-bold">Pemasukan Bulan Ini</div>
                                <div class="text-xl font-extrabold text-emerald-400 mt-1">+Rp 8.000.000</div>
                                <div class="text-[11px] text-slate-400 mt-1">Gaji & Investasi</div>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                                <div class="text-[11px] text-slate-400 uppercase font-bold">Pengeluaran Bulan Ini</div>
                                <div class="text-xl font-extrabold text-rose-400 mt-1">-Rp 3.200.000</div>
                                <div class="text-[11px] text-slate-400 mt-1">Makanan, Transport, Tagihan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bento Features Section -->
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-slate-800/80">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                        Fitur Utama yang Dirancang untuk Anda
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-2">
                        Teknologi keuangan yang dirancang untuk kesederhanaan, transparansi, dan keamanan maksimal.
                    </p>
                </div>

                <!-- Bento Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Feature 1 -->
                    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/50 p-6 backdrop-blur-md shadow-lg relative overflow-hidden group hover:border-slate-700 transition">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center mb-4 border border-indigo-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-white tracking-tight">Single Ledger Truth</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Saldo tidak disimpan dalam cache yang bisa korup. Dihitung langsung dari mutasi riil pemasukan dikurangi pengeluaran dengan presisi desimal perbankan.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/50 p-6 backdrop-blur-md shadow-lg relative overflow-hidden group hover:border-slate-700 transition">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-4 border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-white tracking-tight">Otomatisasi Financial Insights</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Algoritma server-side mendeteksi tren lonjakan pengeluaran dan kategori konsumtif terbesar secara otomatis setiap bulan.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/50 p-6 backdrop-blur-md shadow-lg relative overflow-hidden group hover:border-slate-700 transition">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center mb-4 border border-rose-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-white tracking-tight">Keamanan & Isolasi Data</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Proteksi IDOR penuh, pencegahan double submission, autentikasi terenkripsi Breeze & Fortify, serta pembatasan akses data transaksi yang ketat.
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        @include('layouts.footer')
    </body>
</html>
