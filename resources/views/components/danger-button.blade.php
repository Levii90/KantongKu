<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-rose-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-wider hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500/40 transition-all shadow-md shadow-rose-600/20 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
