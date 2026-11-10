<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-wider hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all shadow-md shadow-indigo-600/20 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
