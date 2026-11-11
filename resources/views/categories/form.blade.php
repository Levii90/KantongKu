<x-app-layout>
    <div class="mx-auto max-w-xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white">{{ isset($category) ? 'Edit Kategori' : 'Kategori Baru' }}</h1>
                <p class="mt-1 text-sm text-slate-400">Kelola kategori pribadi untuk transaksi Anda.</p>
            </div>
            <a href="{{ route('categories.index') }}" class="text-sm font-semibold text-indigo-400">Kembali</a>
        </div>

        <form method="POST" action="{{ isset($category) ? route('categories.update', $category) : route('categories.store') }}" class="glass-card space-y-5 p-6">
            @csrf
            @if(isset($category))
                @method('PUT')
            @endif

            <div>
                <label for="name" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Nama</label>
                <input id="name" name="name" value="{{ old('name', $category->name ?? '') }}" maxlength="100" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                @error('name') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Jenis</label>
                <select id="type" name="type" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                    <option value="expense" @selected(old('type', $category->type ?? 'expense') === 'expense')>Pengeluaran</option>
                    <option value="income" @selected(old('type', $category->type ?? '') === 'income')>Pemasukan</option>
                </select>
                @error('type') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="icon" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Icon key</label>
                    <input id="icon" name="icon" value="{{ old('icon', $category->icon ?? 'tag') }}" maxlength="50" class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                    @error('icon') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="color" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Warna</label>
                    <input id="color" type="color" name="color" value="{{ old('color', $category->color ?? '#6B7280') }}" class="h-11 w-full rounded-xl border border-slate-700 bg-slate-950/80 px-2">
                    @error('color') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-800 pt-4">
                <a href="{{ route('categories.index') }}" class="rounded-xl border border-slate-700 px-4 py-2.5 text-sm text-slate-300">Batal</a>
                <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white">{{ isset($category) ? 'Simpan Perubahan' : 'Buat Kategori' }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
