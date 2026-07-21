@props([
    'variant' => 'primary',
    'pill' => false,
])

@php
    $baseClasses = 'inline-flex items-center gap-1 py-0.5 px-2 text-xs font-semibold';
    
    $variants = [
        'primary' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800/30',
        'secondary' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200/50 dark:border-zinc-700/30',
        'success' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/30',
        'danger' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border border-rose-100 dark:border-rose-800/30',
        'warning' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-100 dark:border-amber-800/30',
        'info' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400 border border-sky-100 dark:border-sky-800/30',
    ];
    
    $shape = $pill ? 'rounded-full' : 'rounded-lg';
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . $shape;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
