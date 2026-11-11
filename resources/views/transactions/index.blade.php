<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800/80">
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Riwayat Transaksi</h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">Daftar lengkap seluruh transaksi pemasukan dan pengeluaran yang tercatat.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button"
                        @click="$dispatch('open-transaction-modal')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Catat Transaksi Baru</span>
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-4 backdrop-blur-md shadow-lg">
            <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <!-- Type Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tipe</label>
                    <select name="type" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-indigo-500 focus:outline-none">
                        <option value="">Semua Tipe</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran (Expense)</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan (Income)</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Kategori</label>
                    <select name="category_id" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-indigo-500 focus:outline-none">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Month Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Bulan</label>
                    <select name="month" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-indigo-500 focus:outline-none">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Year Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tahun</label>
                    <select name="year" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-indigo-500 focus:outline-none">
                        <option value="">Semua Tahun</option>
                        @foreach(range(now()->year - 2, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Transactions Table Card -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 backdrop-blur-md shadow-lg overflow-hidden">
            @if(empty($transactions))
                <div class="py-16 px-4 text-center">
                    <div class="w-12 h-12 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Tidak ada transaksi ditemukan</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Coba sesuaikan filter pencarian Anda atau catat transaksi baru.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950/60 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th scope="col" class="py-3.5 px-4 font-bold">Tanggal</th>
                                <th scope="col" class="py-3.5 px-4 font-bold">Kategori</th>
                                <th scope="col" class="py-3.5 px-4 font-bold">Deskripsi</th>
                                <th scope="col" class="py-3.5 px-4 font-bold">Tipe</th>
                                <th scope="col" class="py-3.5 px-4 font-bold text-right">Nominal (Rp)</th>
                                <th scope="col" class="py-3.5 px-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach($transactions as $tx)
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-4 text-xs whitespace-nowrap text-slate-300 font-medium">
                                        {{ \Carbon\Carbon::parse($tx['transaction_date'])->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $tx['type'] === 'income' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $tx['type'] === 'income' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                                            <span>{{ $tx['category_name'] }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-200">
                                        {{ $tx['description'] ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-xs uppercase tracking-wider font-bold">
                                        <span class="{{ $tx['type'] === 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                            {{ $tx['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-bold text-sm tracking-tight whitespace-nowrap {{ $tx['type'] === 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $tx['type'] === 'income' ? '+' : '-' }}Rp {{ number_format($tx['amount'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-3">
                                            <a href="{{ route('transactions.edit', $tx['id']) }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">Edit</a>
                                            <form method="POST" action="{{ route('transactions.destroy', $tx['id']) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                                                @csrf @method('DELETE')
                                                <button class="text-xs font-semibold text-rose-400 hover:text-rose-300">Hapus</button>
                                            </form>
                                        </div>
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
