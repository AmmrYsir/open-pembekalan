@props([
    'variant' => 'primary',
    'size' => 'md',
    'loading' => false,
    'loadingTarget' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center whitespace-nowrap cursor-pointer font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 disabled:opacity-50 disabled:cursor-not-allowed';
    
    $variants = [
        'primary' => 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm shadow-emerald-600/10 hover:shadow-lg hover:shadow-emerald-500/20 focus:ring-emerald-500 dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:focus:ring-emerald-400 border border-transparent',
        'secondary' => 'bg-zinc-100 hover:bg-zinc-200 text-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 focus:ring-zinc-500 border border-zinc-200 dark:border-zinc-700/80',
        'outline' => 'bg-transparent border border-zinc-300 hover:bg-zinc-50 text-zinc-700 dark:border-zinc-700 dark:hover:bg-zinc-800/50 dark:text-zinc-300 focus:ring-zinc-500',
        'danger' => 'bg-rose-600 hover:bg-rose-500 text-white shadow-sm shadow-rose-600/10 hover:shadow-lg hover:shadow-rose-500/20 focus:ring-rose-500 dark:bg-rose-500 dark:hover:bg-rose-400 dark:focus:ring-rose-400 border border-transparent',
    ];
    
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
    
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button 
    {{ $attributes->merge(['class' => $classes]) }} 
    @if($loadingTarget)
        wire:loading.attr="disabled"
        wire:target="{{ $loadingTarget }}"
    @endif
>
    @if($loadingTarget)
        <svg wire:loading wire:target="{{ $loadingTarget }}" class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @elseif($loading)
        <x-icon-spinner class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" fill="none" viewBox="0 0 24 24" />
    @endif
    {{ $slot }}
</button>
