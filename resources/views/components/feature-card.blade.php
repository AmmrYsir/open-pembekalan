@props([
    'title',
    'description',
])

<div {{ $attributes->merge([
    'class' => 'bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border border-zinc-100 dark:border-zinc-800/80 rounded-2xl p-6 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-emerald-500/25 dark:hover:border-emerald-500/25 group'
]) }}>
    @if(isset($icon))
        <div class="w-12 h-12 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 text-zinc-650 dark:text-zinc-350 flex items-center justify-center transition-all duration-300 group-hover:bg-emerald-600 group-hover:text-white group-hover:scale-110 shadow-xs shrink-0" aria-hidden="true">
            {{ $icon }}
        </div>
    @endif
    <h3 class="text-base font-bold text-zinc-950 dark:text-white mt-5 transition-colors duration-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">{{ $title }}</h3>
    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed">{{ $description }}</p>
</div>
