<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl font-bold text-xs text-slate-300 uppercase tracking-wider shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-700 disabled:opacity-50 transition-all']) }}>
    {{ $slot }}
</button>
