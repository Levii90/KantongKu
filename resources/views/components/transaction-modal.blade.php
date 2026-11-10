@props(['categories' => []])

<div x-data="{
    isOpen: false,
    type: 'expense',
    amount: '',
    categoryId: '',
    transactionDate: '{{ date('Y-m-d') }}',
    description: '',
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
        if (this.filteredCategories.length > 0) {
            this.categoryId = this.filteredCategories[0].id;
        }
    }
}"
@open-transaction-modal.window="isOpen = true"
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
             class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 sm:p-7 shadow-2xl shadow-black/60 relative">

            <!-- Glowing Ridges subtle ambient accent -->
            <div class="absolute -top-10 -right-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80 mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center border border-indigo-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight">Catat Transaksi Baru</h3>
                        <p class="text-xs text-slate-400">Pilih jenis dan masukkan detail transaksi keuangan Anda</p>
                    </div>
                </div>
                <button type="button" @click="isOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('transactions.store') }}" @submit="isSubmitting = true" class="space-y-4">
                @csrf

                <!-- Transaction Type Toggle (Pemasukan vs Pengeluaran) -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-slate-950 border border-slate-800">
                        <button type="button"
                                @click="selectType('expense')"
                                :class="type === 'expense' ? 'bg-rose-500/20 text-rose-300 border-rose-500/40 shadow-sm' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                                class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 border">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            <span>Pengeluaran (Expense)</span>
                        </button>

                        <button type="button"
                                @click="selectType('income')"
                                :class="type === 'income' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-sm' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                                class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 border">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span>Pemasukan (Income)</span>
                        </button>
                    </div>
                    <input type="hidden" name="type" :value="type">
                </div>

                <!-- Amount (Nominal) -->
                <div>
                    <label for="modal_amount" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Jumlah (Rp)</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">
                            Rp
                        </div>
                        <input id="modal_amount"
                               type="number"
                               step="0.01"
                               min="1"
                               name="amount"
                               x-model="amount"
                               required
                               placeholder="50.000"
                               class="w-full rounded-xl border border-slate-800 bg-slate-950 py-2.5 pl-11 pr-4 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category & Date Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Category Dropdown -->
                    <div>
                        <label for="modal_category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Kategori</label>
                        <select id="modal_category_id"
                                name="category_id"
                                x-model="categoryId"
                                required
                                class="w-full rounded-xl border border-slate-800 bg-slate-950 py-2.5 px-3 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <template x-for="cat in filteredCategories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <label for="modal_transaction_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal</label>
                        <input id="modal_transaction_date"
                               type="date"
                               name="transaction_date"
                               x-model="transactionDate"
                               required
                               class="w-full rounded-xl border border-slate-800 bg-slate-950 py-2.5 px-3 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        @error('transaction_date')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description / Catatan -->
                <div>
                    <label for="modal_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Catatan / Deskripsi (Opsional)</label>
                    <textarea id="modal_description"
                              name="description"
                              x-model="description"
                              rows="2"
                              placeholder="Misal: Makan siang di kantor, Gaji pokok, dll."
                              class="w-full rounded-xl border border-slate-800 bg-slate-950 py-2 px-3 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions / Buttons with Double-Click Guard -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800/80">
                    <button type="button"
                            @click="isOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-950 hover:bg-slate-800 text-xs font-semibold text-slate-300 transition">
                        Batal
                    </button>

                    <!-- Double-Click Guard & Loading Spinner (Acceptance Test TC-TX-01) -->
                    <button type="submit"
                            :disabled="isSubmitting"
                            id="btn-save-transaction"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition focus:outline-none focus:ring-2 focus:ring-indigo-500/40 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg x-show="isSubmitting" x-cloak class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Transaksi'">Simpan Transaksi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
