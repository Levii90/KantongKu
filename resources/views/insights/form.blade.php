<x-app-layout>
    <div class="mx-auto max-w-xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white">{{ isset($insight) ? 'Edit Insight' : 'Insight Baru' }}</h1>
                <p class="mt-1 text-sm text-slate-400">Insight manual disimpan terpisah dari analisis otomatis.</p>
            </div>
            <a href="{{ route('insights.index') }}" class="text-sm font-semibold text-indigo-400">Kembali</a>
        </div>

        <form method="POST" action="{{ isset($insight) ? route('insights.update', $insight) : route('insights.store') }}" class="glass-card space-y-5 p-6">
            @csrf
            @if(isset($insight)) @method('PUT') @endif
            <div>
                <label for="message" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Insight</label>
                <textarea id="message" name="message" maxlength="500" rows="4" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">{{ old('message', $insight->message ?? '') }}</textarea>
                @error('message') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="month" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Bulan</label>
                    <select id="month" name="month" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                        @foreach(range(1, 12) as $item)
                            <option value="{{ $item }}" @selected(old('month', $insight->month ?? now()->month) == $item)>{{ DateTime::createFromFormat('!m', $item)->format('F') }}</option>
                        @endforeach
                    </select>
                    @error('month') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="year" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Tahun</label>
                    <input id="year" name="year" type="number" min="2000" max="32767" value="{{ old('year', $insight->year ?? now()->year) }}" required class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                    @error('year') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-800 pt-4">
                <a href="{{ route('insights.index') }}" class="rounded-xl border border-slate-700 px-4 py-2.5 text-sm text-slate-300">Batal</a>
                <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white">{{ isset($insight) ? 'Simpan Perubahan' : 'Buat Insight' }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
