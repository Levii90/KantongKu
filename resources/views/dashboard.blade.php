<x-app-layout>
    <div class="space-y-8" x-data="{
        chartInstance: null,
        initChart() {
            const ctx = document.getElementById('dashboardCashflowChart');
            if (!ctx) return;

            const income = {{ $summary['total_income_this_month'] ?? 0 }};
            const expense = {{ $summary['total_expense_this_month'] ?? 0 }};

            if (this.chartInstance) {
                this.chartInstance.destroy();
            }

            this.chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Pemasukan', 'Pengeluaran'],
                    datasets: [{
                        label: 'Nominal (Rp)',
                        data: [income, expense],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.85)',
                            'rgba(239, 68, 68, 0.85)'
                        ],
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 54
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + Number(context.raw).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', weight: 'bold' } }
                        },
                        y: {
                            grid: { color: 'rgba(51, 65, 85, 0.3)' },
                            ticks: {
                                color: '#94a3b8',
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000) + ' Jt';
                                    if (value >= 1000) return (value / 1000) + ' Rb';
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        }
    }" x-init="initChart()">

        <!-- Header Section with Greeting & Period Filter -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-slate-800/80">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Halo, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-400">{{ Auth::user()->name }}</span> 👋
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">
                    Berikut ringkasan kondisi dan arus kas keuangan Anda bulan ini.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Month & Year Filter Form -->
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <select name="month" onchange="this.form.submit()" class="rounded-xl border border-slate-800 bg-slate-900 px-3 py-2 text-xs font-semibold text-slate-200 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ ($month ?? now()->month) == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endforeach
                    </select>

                    <select name="year" onchange="this.form.submit()" class="rounded-xl border border-slate-800 bg-slate-900 px-3 py-2 text-xs font-semibold text-slate-200 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        @foreach(range(now()->year - 2, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ ($year ?? now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- Quick Action Button -->
                <button type="button"
                        @click="$dispatch('open-transaction-modal')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Tambah Transaksi</span>
                </button>
            </div>
        </div>

        <!-- 3 KPI Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Saldo Total (Current Balance) -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-md relative overflow-hidden group hover:border-slate-700 transition shadow-lg">
                <div class="absolute -top-12 -right-12 w-28 h-28 bg-indigo-500/10 rounded-full blur-xl group-hover:bg-indigo-500/20 transition"></div>
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Total Saldo</span>
                    <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Rp {{ number_format($summary['current_balance'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-400 flex items-center gap-1.5">
                    <span class="inline-block w-2 h-2 rounded-full {{ ($summary['current_balance'] ?? 0) >= 0 ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                    <span>{{ ($summary['current_balance'] ?? 0) >= 0 ? 'Saldo Keuangan Aktif' : 'Defisit Anggaran' }}</span>
                </div>
            </div>

            <!-- Total Pemasukan Bulan Ini -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-md relative overflow-hidden group hover:border-slate-700 transition shadow-lg">
                <div class="absolute -top-12 -right-12 w-28 h-28 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition"></div>
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Pemasukan Bulan Ini</span>
                    <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400 tracking-tight">
                    +Rp {{ number_format($summary['total_income_this_month'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-400">
                    Periode: {{ DateTime::createFromFormat('!m', $month ?? now()->month)->format('F') }} {{ $year ?? now()->year }}
                </div>
            </div>

            <!-- Total Pengeluaran Bulan Ini -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-md relative overflow-hidden group hover:border-slate-700 transition shadow-lg sm:col-span-2 lg:col-span-1">
                <div class="absolute -top-12 -right-12 w-28 h-28 bg-rose-500/10 rounded-full blur-xl group-hover:bg-rose-500/20 transition"></div>
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Pengeluaran Bulan Ini</span>
                    <span class="p-2 rounded-xl bg-rose-500/10 text-rose-400 border border-rose-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-rose-400 tracking-tight">
                    -Rp {{ number_format($summary['total_expense_this_month'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-400">
                    Periode: {{ DateTime::createFromFormat('!m', $month ?? now()->month)->format('F') }} {{ $year ?? now()->year }}
                </div>
            </div>
        </div>

        <!-- System-generated and user-managed financial insights -->
        @if(!empty($summary['insights']) || $manualInsights->isNotEmpty())
            <div class="rounded-2xl border border-indigo-500/20 bg-gradient-to-r from-indigo-950/40 via-slate-900 to-slate-900 p-5 shadow-lg">
                <div class="flex items-start justify-between gap-3.5">
                    <div class="flex items-start gap-3.5">
                        <div class="p-2 rounded-xl bg-indigo-500/20 text-indigo-400 flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white tracking-tight mb-1">Financial Insights</h4>
                            <div class="space-y-1">
                                @foreach($summary['insights'] as $insight)
                                    <p class="text-xs text-slate-300 leading-relaxed flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                        <span>{{ $insight }}</span>
                                    </p>
                                @endforeach
                                @foreach($manualInsights as $insight)
                                    <p class="text-xs text-slate-300 leading-relaxed flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        <span>{{ $insight->message }}</span>
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('insights.index') }}" class="flex-shrink-0 text-xs font-semibold text-indigo-300 hover:text-white">Kelola</a>
                </div>
            </div>
        @endif

        <!-- Main Content Grid: Cashflow Chart & Recent Transactions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Visual Cashflow Chart Card -->
            <div class="lg:col-span-1 rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-md shadow-lg flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Perbandingan Arus Kas</h3>
                        <span class="text-[11px] text-slate-400">Bulan Ini</span>
                    </div>
                    <div class="h-64 relative flex items-center justify-center">
                        <canvas id="dashboardCashflowChart"></canvas>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-800/80 flex items-center justify-around text-center text-xs">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Rasio Tabungan</span>
                        <span class="font-bold text-white">
                            @if(($summary['total_income_this_month'] ?? 0) > 0)
                                {{ round(((($summary['total_income_this_month'] - $summary['total_expense_this_month']) / $summary['total_income_this_month']) * 100), 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                    <div class="h-6 w-px bg-slate-800"></div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Sisa Anggaran</span>
                        <span class="font-bold {{ (($summary['total_income_this_month'] ?? 0) - ($summary['total_expense_this_month'] ?? 0)) >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            Rp {{ number_format(($summary['total_income_this_month'] ?? 0) - ($summary['total_expense_this_month'] ?? 0), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Feed & Empty State (Acceptance Test TC-DASH-01) -->
            <div class="lg:col-span-2 rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-md shadow-lg">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Transaksi Terbaru</h3>
                        <p class="text-xs text-slate-400">5 transaksi keuangan terakhir yang Anda catat</p>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold transition flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                @if(empty($summary['recent_transactions']))
                    <!-- Friendly Empty State (Acceptance Test TC-DASH-01) -->
                    <div class="flex flex-col items-center justify-center py-12 px-4 text-center rounded-xl border border-dashed border-slate-800 bg-slate-950/40">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center mb-3.5 border border-indigo-500/20">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-white">Belum ada data transaksi</h4>
                        <p class="text-xs text-slate-400 max-w-sm mt-1 mb-5">
                            Total Saldo Anda saat ini adalah <span class="text-white font-semibold">Rp 0</span>. Mulai catat transaksi pertama untuk mengaktifkan pemantauan keuangan pribadi Anda.
                        </p>
                        <button type="button"
                                @click="$dispatch('open-transaction-modal')"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Catat Transaksi Pertama</span>
                        </button>
                    </div>
                @else
                    <!-- Transaction List -->
                    <div class="divide-y divide-slate-800/80">
                        @foreach($summary['recent_transactions'] as $tx)
                            <div class="py-3.5 flex items-center justify-between hover:bg-slate-800/30 px-3 rounded-xl transition">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $tx['type'] === 'income' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                        @if($tx['type'] === 'income')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-white">{{ $tx['description'] ?: $tx['category_name'] }}</span>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wider {{ $tx['type'] === 'income' ? 'bg-emerald-500/10 text-emerald-300' : 'bg-rose-500/10 text-rose-300' }}">
                                                {{ $tx['category_name'] }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-slate-400 mt-0.5">
                                            {{ \Carbon\Carbon::parse($tx['transaction_date'])->translatedFormat('d F Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold tracking-tight {{ $tx['type'] === 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $tx['type'] === 'income' ? '+' : '-' }}Rp {{ number_format($tx['amount'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
