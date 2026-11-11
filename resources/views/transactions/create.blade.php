<x-app-layout>
    <div class="max-w-xl mx-auto space-y-6" x-data="{
        type: @js(old('type', $transaction->type ?? 'expense')),
        amount: @js(old('amount', $transaction->amount ?? '')),
        categoryId: @js(old('category_id', $transaction->category_id ?? '')),
        transactionDate: @js(old('transaction_date', isset($transaction) ? $transaction->transaction_date->format('Y-m-d') : date('Y-m-d'))),
        description: @js(old('description', $transaction->description ?? '')),
        isSubmitting: false,
        allCategories: {{ Js::from($categories) }},
        get filteredCategories() {
            return this.allCategories.filter(cat => cat.type === this.type);
        },
        selectType(newType) {
            this.type = newType;
            const available = this.filteredCategories;
            if (available.length > 0) {
                this.categoryId = available[0].id;
            } else {
                this.categoryId = '';
            }
        },
        init() {
            if (!this.categoryId && this.filteredCategories.length > 0) {
                this.categoryId = this.filteredCategories[0].id;
            }
        }
    }">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">{{ isset($transaction) ? 'Edit Transaksi' : 'Formulir Transaksi' }}</h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">{{ isset($transaction) ? 'Perbarui detail transaksi Anda.' : 'Tambahkan pemasukan atau pengeluaran baru ke dalam catatan keuangan Anda.' }}</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition flex items-center gap-1 font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali</span>
            </a>
        </div>

        <!-- Form Card Surface -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 sm:p-8 backdrop-blur-md shadow-2xl relative">
            <div class="absolute -top-10 -right-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <form method="POST" action="{{ isset($transaction) ? route('transactions.update', $transaction) : route('transactions.store') }}" @submit="isSubmitting = true" class="space-y-5">
                @csrf
                @if(isset($transaction))
                    @method('PUT')
                @endif

                <!-- Transaction Type Toggle -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-slate-950 border border-slate-800">
                        <button type="button"
                                @click="selectType('expense')"
                                :class="type === 'expense' ? 'bg-rose-500/20 text-rose-300 border-rose-500/40 shadow-sm' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                                class="py-2.5 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 border">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            <span>Pengeluaran (Expense)</span>
                        </button>

                        <button type="button"
                                @click="selectType('income')"
                                :class="type === 'income' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-sm' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                                class="py-2.5 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 border">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span>Pemasukan (Income)</span>
                        </button>
                    </div>
                    <input type="hidden" name="type" :value="type">
                </div>

                <!-- Amount Input -->
                <div>
                    <label for="create_amount" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Jumlah Transaksi (Rp)</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">
                            Rp
                        </div>
                        <input id="create_amount"
                               type="number"
                               step="0.01"
                               min="1"
                               name="amount"
                               x-model="amount"
                               required
                               placeholder="100.000"
                               class="w-full rounded-xl border @error('amount') border-rose-500 @else border-slate-800 @enderror bg-slate-950 py-3 pl-11 pr-4 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-xs text-rose-400 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Category & Date -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="create_category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Kategori</label>
                        <select id="create_category_id"
                                name="category_id"
                                x-model="categoryId"
                                required
                                class="w-full rounded-xl border @error('category_id') border-rose-500 @else border-slate-800 @enderror bg-slate-950 py-2.5 px-3 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <template x-for="cat in filteredCategories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-rose-400 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="create_transaction_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal</label>
                        <input id="create_transaction_date"
                               type="date"
                               name="transaction_date"
                               x-model="transactionDate"
                               required
                               class="w-full rounded-xl border @error('transaction_date') border-rose-500 @else border-slate-800 @enderror bg-slate-950 py-2.5 px-3 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        @error('transaction_date')
                            <p class="mt-1 text-xs text-rose-400 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="create_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Catatan / Deskripsi (Opsional)</label>
                    <textarea id="create_description"
                              name="description"
                              x-model="description"
                              rows="3"
                              placeholder="Keterangan tambahan transaksi..."
                              class="w-full rounded-xl border border-slate-800 bg-slate-950 py-2.5 px-3.5 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button with Double-Click Guard (Acceptance Test TC-TX-01) -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800/80">
                    <a href="{{ route('transactions.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-950 hover:bg-slate-800 text-xs font-semibold text-slate-300 transition">
                        Batal
                    </a>
                    <button type="submit"
                            :disabled="isSubmitting"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition focus:outline-none focus:ring-2 focus:ring-indigo-500/40 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg x-show="isSubmitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Menyimpan Transaksi...' : '{{ isset($transaction) ? 'Perbarui Transaksi' : 'Simpan Transaksi' }}'">{{ isset($transaction) ? 'Perbarui Transaksi' : 'Simpan Transaksi' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
