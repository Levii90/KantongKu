<x-app-layout>
    <div class="mx-auto max-w-xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Edit Anggaran</h1>
                <p class="mt-1 text-sm text-slate-400">Perbarui batas pengeluaran kategori.</p>
            </div>
            <a href="{{ route('budgets.index', ['month' => $budget->month, 'year' => $budget->year]) }}" class="text-sm font-semibold text-indigo-400">Kembali</a>
        </div>

        <form method="POST" action="{{ route('budgets.update', $budget) }}" class="glass-card space-y-5 p-6">
            @csrf @method('PUT')
            <div>
                <label for="category_id" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Kategori</label>
                <select id="category_id" name="category_id" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $budget->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="amount_limit" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Batas Anggaran</label>
                <input id="amount_limit" name="amount_limit" type="number" step="0.01" min="0.01" value="{{ old('amount_limit', $budget->amount_limit) }}" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                @error('amount_limit') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="month" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Bulan</label>
                    <select id="month" name="month" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                        @foreach(range(1, 12) as $item)
                            <option value="{{ $item }}" @selected(old('month', $budget->month) == $item)>{{ DateTime::createFromFormat('!m', $item)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="year" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Tahun</label>
                    <input id="year" name="year" type="number" min="2000" max="32767" value="{{ old('year', $budget->year) }}" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                </div>
            </div>
            @error('month') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
            @error('year') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
            <div class="flex justify-end gap-3 border-t border-slate-800 pt-4">
                <a href="{{ route('budgets.index', ['month' => $budget->month, 'year' => $budget->year]) }}" class="rounded-xl border border-slate-700 px-4 py-2.5 text-sm text-slate-300">Batal</a>
                <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
