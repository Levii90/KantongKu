<x-app-layout>
    <div class="space-y-8" x-data="{
        categoryChartInstance: null,
        trendChartInstance: null,
        initCharts() {
            // 1. Category Breakdown Doughnut Chart
            const catCtx = document.getElementById('reportCategoryChart');
            const categoryData = {{ Js::from($summary['category_breakdown'] ?? []) }};

            if (catCtx && categoryData.length > 0) {
                if (this.categoryChartInstance) this.categoryChartInstance.destroy();

                const labels = categoryData.map(c => c.category_name);
                const values = categoryData.map(c => c.total_amount);
                const colors = [
                    '#EF4444', '#3B82F6', '#F59E0B', '#10B981',
                    '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'
                ];

                this.categoryChartInstance = new Chart(catCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors.slice(0, labels.length),
                            borderWidth: 2,
                            borderColor: '#0f172a'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 11 }, padding: 14 }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        const val = ctx.raw;
                                        return ctx.label + ': Rp ' + Number(val).toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }

            // 2. Monthly Trend Chart
            const trendCtx = document.getElementById('reportTrendChart');
            const trendData = {{ Js::from($summary['monthly_trends'] ?? []) }};

            if (trendCtx && trendData.length > 0) {
                if (this.trendChartInstance) this.trendChartInstance.destroy();

                this.trendChartInstance = new Chart(trendCtx, {
                    type: 'bar',
                    data: {
                        labels: trendData.map(t => t.month),
                        datasets: [
                            {
                                label: 'Pemasukan (Income)',
                                data: trendData.map(t => t.income),
                                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                borderRadius: 6
                            },
                            {
                                label: 'Pengeluaran (Expense)',
                                data: trendData.map(t => t.expense),
                                backgroundColor: 'rgba(239, 68, 68, 0.85)',
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: '#cbd5e1', font: { family: 'Plus Jakarta Sans', weight: 'bold' } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        return ctx.dataset.label + ': Rp ' + Number(ctx.raw).toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(51, 65, 85, 0.2)' },
                                ticks: { color: '#94a3b8' }
                            },
                            y: {
                                grid: { color: 'rgba(51, 65, 85, 0.2)' },
                                ticks: {
                                    color: '#94a3b8',
                                    callback: function(val) {
                                        if (val >= 1000000) return (val / 1000000) + ' Jt';
                                        if (val >= 1000) return (val / 1000) + ' Rb';
                                        return val;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    }" x-init="initCharts()">

        <!-- Header & Date Filter Bar (Acceptance Test TC-REP-01) -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-800/80">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Laporan & Analisis Keuangan</h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">Evaluasi tren pengeluaran, agregasi kategori, dan riwayat pertumbuhan tabungan Anda.</p>
            </div>

            <!-- Date Range Filter Form -->
            <form method="GET" action="{{ route('reports.index') }}" id="reportFilterForm" class="flex flex-wrap items-center gap-2.5">
                <div class="flex items-center gap-2 bg-slate-900 border border-slate-800 px-3 py-1.5 rounded-xl">
                    <span class="text-xs text-slate-400 font-semibold">Dari:</span>
                    <input type="date"
                           name="start_date"
                           value="{{ $startDate }}"
                           class="bg-transparent border-none text-xs text-slate-200 focus:ring-0 p-0 font-medium">
                </div>

                <div class="flex items-center gap-2 bg-slate-900 border border-slate-800 px-3 py-1.5 rounded-xl">
                    <span class="text-xs text-slate-400 font-semibold">Sampai:</span>
                    <input type="date"
                           name="end_date"
                           value="{{ $endDate }}"
                           class="bg-transparent border-none text-xs text-slate-200 focus:ring-0 p-0 font-medium">
                </div>

                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-md shadow-indigo-600/20">
                    Terapkan Filter
                </button>
            </form>
        </div>

        <!-- Bento Grid Analytical KPI Cards (MagicBento Inspired) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Pemasukan -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-md shadow-lg relative overflow-hidden">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pemasukan</div>
                <div class="text-xl sm:text-2xl font-extrabold text-emerald-400 tracking-tight">
                    +Rp {{ number_format($summary['total_income'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">Pada rentang tanggal yang dipilih</div>
            </div>

            <!-- Total Pengeluaran -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-md shadow-lg relative overflow-hidden">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pengeluaran</div>
                <div class="text-xl sm:text-2xl font-extrabold text-rose-400 tracking-tight">
                    -Rp {{ number_format($summary['total_expense'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">Total beban belanja & biaya hidup</div>
            </div>

            <!-- Tabungan Bersih (Net Savings) -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-md shadow-lg relative overflow-hidden">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tabungan Bersih</div>
                <div class="text-xl sm:text-2xl font-extrabold {{ ($summary['net_savings'] ?? 0) >= 0 ? 'text-indigo-400' : 'text-rose-400' }} tracking-tight">
                    Rp {{ number_format($summary['net_savings'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">Surplus arus kas berjalan</div>
            </div>

            <!-- Rasio Tabungan (% Savings Rate) -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-md shadow-lg relative overflow-hidden">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Rasio Tabungan</div>
                <div class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    @if(($summary['total_income'] ?? 0) > 0)
                        {{ round(((($summary['total_income'] - $summary['total_expense']) / $summary['total_income']) * 100), 1) }}%
                    @else
                        0%
                    @endif
                </div>
                <div class="text-[11px] text-slate-500 mt-1">Persentase dari total pemasukan</div>
            </div>
        </div>

        <!-- Analytical Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Category Breakdown Chart -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-md shadow-lg flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Distribusi Pengeluaran</h3>
                        <p class="text-xs text-slate-400">Porsi pengeluaran berdasarkan kategori</p>
                    </div>
                </div>

                @if(empty($summary['category_breakdown']))
                    <div class="py-16 text-center text-slate-500 text-xs">
                        Belum ada data pengeluaran pada rentang tanggal ini.
                    </div>
                @else
                    <div class="h-64 relative">
                        <canvas id="reportCategoryChart"></canvas>
                    </div>
                    <!-- Category Breakdown Progress Bars -->
                    <div class="mt-6 space-y-2.5 pt-4 border-t border-slate-800/80">
                        @foreach($summary['category_breakdown'] as $cat)
                            <div>
                                <div class="flex justify-between text-xs font-semibold mb-1">
                                    <span class="text-slate-200">{{ $cat['category_name'] }}</span>
                                    <span class="text-slate-400">{{ $cat['percentage'] }}% (Rp {{ number_format($cat['total_amount'], 0, ',', '.') }})</span>
                                </div>
                                <div class="w-full bg-slate-950 rounded-full h-2 overflow-hidden border border-slate-800">
                                    <div class="bg-gradient-to-r from-indigo-500 to-rose-500 h-2 rounded-full" style="width: {{ min(100, $cat['percentage']) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Monthly Trends Chart -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-md shadow-lg flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Tren Arus Kas Bulanan</h3>
                        <p class="text-xs text-slate-400">Perbandingan pemasukan vs pengeluaran antar bulan</p>
                    </div>
                </div>

                @if(empty($summary['monthly_trends']))
                    <div class="py-16 text-center text-slate-500 text-xs">
                        Belum ada data tren bulanan.
                    </div>
                @else
                    <div class="h-80 relative">
                        <canvas id="reportTrendChart"></canvas>
                    </div>
                @endif
            </div>
        </div>

        <!-- Filtered Transaction Table -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 backdrop-blur-md shadow-lg overflow-hidden">
            <div class="p-5 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Rincian Transaksi pada Periode Ini</h3>
                    <p class="text-xs text-slate-400">Menampilkan seluruh mutasi keuangan yang cocok dengan filter tanggal</p>
                </div>
            </div>

            @if(empty($transactions))
                <div class="py-12 text-center text-slate-500 text-xs">
                    Tidak ada mutasi transaksi pada rentang tanggal ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950/60 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th scope="col" class="py-3 px-4 font-bold">Tanggal</th>
                                <th scope="col" class="py-3 px-4 font-bold">Kategori</th>
                                <th scope="col" class="py-3 px-4 font-bold">Deskripsi</th>
                                <th scope="col" class="py-3 px-4 font-bold">Tipe</th>
                                <th scope="col" class="py-3 px-4 font-bold text-right">Nominal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach($transactions as $tx)
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-xs whitespace-nowrap text-slate-300">
                                        {{ \Carbon\Carbon::parse($tx['transaction_date'])->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs font-semibold {{ $tx['type'] === 'income' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                            <span>{{ $tx['category_name'] }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-xs text-slate-200">
                                        {{ $tx['description'] ?: '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-xs font-bold uppercase tracking-wider">
                                        <span class="{{ $tx['type'] === 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                            {{ $tx['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-sm tracking-tight whitespace-nowrap {{ $tx['type'] === 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $tx['type'] === 'income' ? '+' : '-' }}Rp {{ number_format($tx['amount'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
