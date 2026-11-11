<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 border-b border-slate-800/80 pb-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Kategori</h1>
                <p class="mt-1 text-sm text-slate-400">Buat dan kelola kategori pemasukan serta pengeluaran pribadi.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-300">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('categories.store') }}" class="glass-card grid gap-4 p-5 sm:grid-cols-2">
            @csrf
            <div>
                <label for="category_name" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Nama Kategori</label>
                <input id="category_name" name="name" value="{{ old('name') }}" maxlength="100" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white" placeholder="Contoh: Transportasi">
            </div>
            <div>
                <label for="category_type" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Jenis</label>
                <select id="category_type" name="type" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                    <option value="expense">Pengeluaran</option>
                    <option value="income">Pemasukan</option>
                </select>
            </div>
            <div>
                <label for="category_icon" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Icon key</label>
                <input id="category_icon" name="icon" value="{{ old('icon', 'tag') }}" maxlength="50" class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
            </div>
            <div class="flex items-end gap-3">
                <label for="category_color" class="text-xs font-semibold uppercase text-slate-400">Warna</label>
                <input id="category_color" type="color" name="color" value="{{ old('color', '#6B7280') }}" class="h-11 w-20 rounded-lg border border-slate-700 bg-slate-950 p-1">
                <button class="ml-auto rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white">Tambah Kategori</button>
            </div>
        </form>

        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="border-b border-slate-700 bg-slate-950/50 text-xs uppercase text-slate-400">
                        <tr><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Jenis</th><th class="px-5 py-3">Warna</th><th class="px-5 py-3 text-right">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($categories as $category)
                            <tr>
                                <td class="px-5 py-4 font-semibold text-white">{{ $category->name }}</td>
                                <td class="px-5 py-4">{{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                                <td class="px-5 py-4"><span class="mr-2 inline-block h-3 w-3 rounded-full" style="background-color: {{ $category->color }}"></span>{{ $category->color }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('categories.edit', $category) }}" class="font-semibold text-indigo-400 hover:text-indigo-300">Edit</a>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf @method('DELETE')
                                            <button class="font-semibold text-rose-400 hover:text-rose-300">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-slate-400">Belum ada kategori pribadi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
