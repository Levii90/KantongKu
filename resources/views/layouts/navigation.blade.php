<nav x-data="{ open: false, userMenu: false }" class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Side: Brand & Links -->
            <div class="flex items-center gap-8">
                <!-- Brand Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold tracking-tight text-white">Kantong<span class="text-indigo-400">Ku</span></span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:space-x-1">
                    <a href="{{ route('dashboard') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('dashboard') ? 'bg-indigo-600/15 text-indigo-400 border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('transactions.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('transactions.*') ? 'bg-indigo-600/15 text-indigo-400 border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span>Transaksi</span>
                    </a>

                    <a href="{{ route('budgets.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('budgets.*') ? 'bg-indigo-600/15 text-indigo-400 border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Anggaran
                    </a>

                    <a href="{{ route('categories.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('categories.*') ? 'bg-indigo-600/15 text-indigo-400 border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Kategori
                    </a>

                    <a href="{{ route('insights.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('insights.*') ? 'bg-indigo-600/15 text-indigo-400 border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Insights
                    </a>

                    <a href="{{ route('reports.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('reports.*') ? 'bg-indigo-600/15 text-indigo-400 border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Laporan</span>
                    </a>
                </div>
            </div>

            <!-- Right Side: Quick Action & User Menu -->
            <div class="hidden sm:flex sm:items-center sm:gap-3">
                <!-- Add Transaction Quick Button -->
                <button type="button"
                        @click="$dispatch('open-transaction-modal')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Catat Transaksi</span>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative" @click.outside="userMenu = false">
                    <button @click="userMenu = !userMenu"
                            type="button"
                            class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-lg border border-slate-800 bg-slate-900/60 hover:bg-slate-800 text-slate-200 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-slate-700">
                        <!-- Initials Avatar -->
                        <div class="w-7 h-7 rounded-full bg-indigo-500/20 text-indigo-400 font-bold text-xs flex items-center justify-center border border-indigo-500/30">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <span class="max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{'rotate-180': userMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userMenu"
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 rounded-xl bg-slate-900 border border-slate-800 shadow-xl py-1 z-50 divide-y divide-slate-800/80">
                        <div class="px-4 py-2.5">
                            <p class="text-xs text-slate-400 font-medium">Signed in as</p>
                            <p class="text-sm text-slate-200 font-semibold truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-800/60 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Profile Settings</span>
                            </a>
                        </div>
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-colors text-left">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Log Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger Button for Mobile -->
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                        type="button"
                        class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="open" x-cloak class="sm:hidden border-b border-slate-800 bg-slate-900/95 px-4 pt-3 pb-4 space-y-3">
        <div class="space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-800' }}">
                Dashboard
            </a>
            <a href="{{ route('transactions.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('transactions.*') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-800' }}">
                Transaksi
            </a>
            <a href="{{ route('budgets.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('budgets.*') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-800' }}">
                Anggaran
            </a>
            <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('categories.*') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-800' }}">
                Kategori
            </a>
            <a href="{{ route('insights.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('insights.*') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-800' }}">
                Insights
            </a>
            <a href="{{ route('reports.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.*') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-800' }}">
                Laporan
            </a>
        </div>

        <div class="pt-3 border-t border-slate-800 flex flex-col gap-2">
            <button type="button"
                    @click="open = false; $dispatch('open-transaction-modal')"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Catat Transaksi</span>
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full px-3 py-2 text-sm text-rose-400 hover:bg-slate-800 rounded-lg text-left">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</nav>
