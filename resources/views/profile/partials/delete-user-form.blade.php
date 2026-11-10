<section class="space-y-4">
    <header>
        <h2 class="text-base font-bold text-rose-400 tracking-tight">
            {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-1 text-xs text-slate-400">
            {{ __('Setelah akun Anda dihapus, semua data mutasi dan transaksi akan dihapus secara permanen.') }}
        </p>
    </header>

    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition shadow-md shadow-rose-600/20">
        {{ __('Hapus Akun Saya') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-slate-900 border border-slate-800 rounded-2xl text-slate-100">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-white tracking-tight">
                {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
            </h2>

            <p class="mt-2 text-xs text-slate-400 leading-relaxed">
                {{ __('Tindakan ini tidak dapat dibatalkan. Masukkan password Anda untuk mengonfirmasi penghapusan permanen akun KantongKu.') }}
            </p>

            <div class="mt-4">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full"
                    placeholder="{{ __('Masukkan Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2 rounded-xl border border-slate-800 bg-slate-950 text-slate-300 hover:bg-slate-800 text-xs font-semibold transition">
                    {{ __('Batal') }}
                </button>

                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition shadow-md shadow-rose-600/20">
                    {{ __('Hapus Permanen') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
