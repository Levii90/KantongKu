@props(['categories' => [], 'month' => null, 'year' => null])

<div x-data="{
    isOpen: false,
    categoryId: '',
    amountLimit: '',
    month: {{ (int) ($month ?? now()->month) }},
    year: {{ (int) ($year ?? now()->year) }},
    isSubmitting: false,
    expenseCategories: {{ Js::from(collect($categories)->filter(fn($c) => is_array($c) ? ($c['type'] ?? '') === 'expense' : $c->type === 'expense')->values()) }},
    init() {
        if (this.expenseCategories.length > 0) {
            this.categoryId = this.expenseCategories[0].id;
        }
    }
}"
@open-budget-modal.window="isOpen = true"
@keydown.escape.window="isOpen = false"
x-cloak>
    <!-- Modal Backdrop -->
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">

        <!-- Modal Dialog Surface -->
        <div x-show="isOpen"
             @click.outside="isOpen = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 sm:p-7 shadow-2xl relative">

            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80 mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center border border-indigo-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white tracking-tight">Atur Anggaran Bulanan</h3>
                        <p class="text-xs text-slate-400">Batasi pengeluaran per kategori untuk periode ini</p>
                    </div>
                </div>
                <button type="button" @click="isOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('budgets.store') }}" @submit="isSubmitting = true" class="space-y-4">
                @csrf

                <!-- Category Dropdown -->
                <div>
                    <label for="budget_category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Kategori Pengeluaran</label>
                    <select id="budget_category_id"
                            name="category_id"
                            x-model="categoryId"
                            required
                            class="w-full rounded-xl border border-slate-800 bg-slate-950 py-2.5 px-3 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <template x-for="cat in expenseCategories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Amount Limit -->
                <div>
                    <label for="budget_amount_limit" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Batas Maksimal Anggaran (Rp)</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">
                            Rp
                        </div>
                        <input id="budget_amount_limit"
                               type="number"
                               step="0.01"
                               min="1000"
                               name="amount_limit"
                               x-model="amountLimit"
                               required
                               placeholder="1.000.000"
                               class="w-full rounded-xl border border-slate-800 bg-slate-950 py-2.5 pl-11 pr-4 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                </div>

                <!-- Month & Year Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="budget_month" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Bulan</label>
                        <select id="budget_month"
                                name="month"
                                x-model="month"
                                required
                                class="w-full rounded-xl border border-slate-800 bg-slate-950 py-2.5 px-3 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="budget_year" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Tahun</label>
                        <input id="budget_year"
                               type="number"
                               name="year"
                               x-model="year"
                               required
                               class="w-full rounded-xl border border-slate-800 bg-slate-950 py-2.5 px-3 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800/80">
                    <button type="button"
                            @click="isOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-950 hover:bg-slate-800 text-xs font-semibold text-slate-300 transition">
                        Batal
                    </button>

                    <button type="submit"
                            :disabled="isSubmitting"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition focus:outline-none focus:ring-2 focus:ring-indigo-500/40 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg x-show="isSubmitting" x-cloak class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Anggaran'">Simpan Anggaran</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
