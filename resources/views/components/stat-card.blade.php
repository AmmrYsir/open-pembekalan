@props([
    'value',
    'label',
])

<div {{ $attributes->merge([
    'class' => 'bg-white/60 dark:bg-zinc-900/60 backdrop-blur-md border border-zinc-100 dark:border-zinc-800/80 rounded-2xl p-6 shadow-xs flex items-center gap-4 transition-all duration-200 hover:border-emerald-500/35 dark:hover:border-emerald-500/35'
]) }}>
    @if(isset($icon))
        <div class="p-3 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0" aria-hidden="true">
            {{ $icon }}
        </div>
    @endif
    <div>
        <span class="block text-2xl md:text-3xl font-extrabold text-zinc-950 dark:text-white tracking-tight">{{ $value }}</span>
        <span class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $label }}</span>
    </div>
</div>
