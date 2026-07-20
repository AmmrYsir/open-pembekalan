@props([
    'variant' => 'info',
    'dismissible' => false,
    'title' => null,
])

@php
    $variants = [
        'success' => [
            'wrapper' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
            'icon' => 'text-emerald-500 dark:text-emerald-400',
        ],
        'error' => [
            'wrapper' => 'bg-rose-50 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
            'icon' => 'text-rose-500 dark:text-rose-400',
        ],
        'warning' => [
            'wrapper' => 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
            'icon' => 'text-amber-500 dark:text-amber-400',
        ],
        'info' => [
            'wrapper' => 'bg-sky-50 text-sky-800 dark:bg-sky-950/40 dark:text-sky-300 border-sky-200 dark:border-sky-800/60',
            'icon' => 'text-sky-500 dark:text-sky-400',
        ],
    ];

    $style = $variants[$variant] ?? $variants['info'];
@endphp

<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition
    {{ $attributes->merge(['class' => 'flex items-start gap-3 p-3.5 rounded-xl border text-xs font-medium transition-all ' . $style['wrapper']]) }}
>
    <div class="mt-0.5 shrink-0 {{ $style['icon'] }}">
        @if($variant === 'success')
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @elseif($variant === 'error')
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @elseif($variant === 'warning')
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        @else
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @endif
    </div>

    <div class="flex-1 space-y-1">
        @if($title)
            <h5 class="font-semibold leading-none">{{ $title }}</h5>
        @endif
        <div>{{ $slot }}</div>
    </div>

    @if($dismissible)
        <button type="button" @click="show = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors p-0.5 cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>
