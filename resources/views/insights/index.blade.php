<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 border-b border-slate-800/80 pb-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Financial Insights</h1>
                <p class="mt-1 text-sm text-slate-400">Kelola catatan insight pribadi. Analisis otomatis tetap dibuat sistem.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-indigo-400">Kembali ke Dashboard</a>
        </div>

        @if($errors->any())
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-300">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('insights.store') }}" class="glass-card grid gap-4 p-5 sm:grid-cols-[1fr_auto_auto_auto] sm:items-end">
            @csrf
            <div>
                <label for="insight-message" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Insight manual</label>
                <input id="insight-message" name="message" maxlength="500" required value="{{ old('message') }}" placeholder="Tuliskan insight atau target keuangan..." class="w-full rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
            </div>
            <div>
                <label for="insight-month" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Bulan</label>
                <select id="insight-month" name="month" required class="rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
                    @foreach(range(1, 12) as $item)
                        <option value="{{ $item }}" @selected($item === now()->month)>{{ DateTime::createFromFormat('!m', $item)->format('M') }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="insight-year" class="mb-1.5 block text-xs font-semibold uppercase text-slate-400">Tahun</label>
                <input id="insight-year" name="year" type="number" min="2000" max="32767" value="{{ now()->year }}" required class="w-24 rounded-xl border border-slate-700 bg-slate-950/80 px-3 py-2.5 text-sm text-white">
            </div>
            <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white">Tambah</button>
        </form>

        <div class="space-y-3">
            @forelse($insights as $insight)
                <article class="glass-card flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 flex-shrink-0 rounded-full bg-indigo-400"></span>
                        <div>
                            <p class="text-sm leading-relaxed text-slate-200">{{ $insight->message }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ DateTime::createFromFormat('!m', $insight->month)->format('F') }} {{ $insight->year }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pl-5 sm:pl-0">
                        <a href="{{ route('insights.edit', $insight) }}" class="text-sm font-semibold text-indigo-400">Edit</a>
                        <form method="POST" action="{{ route('insights.destroy', $insight) }}" onsubmit="return confirm('Hapus insight ini?')">
                            @csrf @method('DELETE')
                            <button class="text-sm font-semibold text-rose-400">Hapus</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="glass-card p-10 text-center text-sm text-slate-400">Belum ada insight manual. Insight otomatis tetap muncul di dashboard.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
