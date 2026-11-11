<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 border-b border-slate-800/80 pb-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Anggaran Bulanan</h1>
                <p class="mt-1 text-sm text-slate-400">Buat, ubah, dan hapus batas pengeluaran per kategori.</p>
            </div>
            <form method="GET" action="{{ route('budgets.index') }}" class="flex gap-2">
                <select name="month" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-white">
                    @foreach(range(1, 12) as $item)
                        <option value="{{ $item }}" @selected($month === $item)>{{ DateTime::createFromFormat('!m', $item)->format('F') }}</option>
                    @endforeach
                </select>
                <input name="year" type="number" value="{{ $year }}" min="2000" max="32767" class="w-24 rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-white">
                <button class="rounded-xl bg-slate-800 px-3 py-2 text-sm text-white">Filter</button>
            </form>
        </div>

        @if($errors->any())
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-300">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('budgets.store') }}" class="glass-card grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-5">
            @csrf
            <div class="lg:col-span-2">
                <label for="budget-category" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Kategori Pengeluaran</label>
                <select id="budget-category" name="category_id" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                    @foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="budget-limit" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Batas (Rp)</label>
                <input id="budget-limit" name="amount_limit" type="number" step="0.01" min="0.01" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
            </div>
            <div>
                <label for="budget-create-month" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Bulan</label>
                <select id="budget-create-month" name="month" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                    @foreach(range(1, 12) as $item)<option value="{{ $item }}" @selected($month === $item)>{{ DateTime::createFromFormat('!m', $item)->format('F') }}</option>@endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <input aria-label="Tahun anggaran" name="year" type="number" min="2000" max="32767" value="{{ $year }}" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                <button class="whitespace-nowrap rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white">Tambah</button>
            </div>
        </form>

        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="border-b border-slate-700 bg-slate-950/50 text-xs uppercase text-slate-400">
                        <tr><th class="px-5 py-3">Kategori</th><th class="px-5 py-3">Terpakai</th><th class="px-5 py-3">Batas</th><th class="px-5 py-3">Progres</th><th class="px-5 py-3 text-right">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($budgets as $budget)
                            <tr>
                                <td class="px-5 py-4 font-semibold text-white">{{ $budget['category_name'] }}</td>
                                <td class="px-5 py-4">Rp {{ number_format($budget['spent'], 0, ',', '.') }}</td>
                                <td class="px-5 py-4">Rp {{ number_format($budget['amount_limit'], 0, ',', '.') }}</td>
                                <td class="px-5 py-4">{{ $budget['percentage'] }}%</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('budgets.edit', $budget['id']) }}" class="font-semibold text-indigo-400">Edit</a>
                                        <form method="POST" action="{{ route('budgets.destroy', $budget['id']) }}" onsubmit="return confirm('Hapus anggaran ini?')">
                                            @csrf @method('DELETE')
                                            <button class="font-semibold text-rose-400">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">Belum ada anggaran untuk periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
