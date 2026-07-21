@props([
    'title' => null,
    'subtitle' => null,
    'hoverable' => false,
])

<div {{ $attributes->merge([
    'class' => 'bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800/80 rounded-2xl p-6 shadow-xs ' .
    ($hoverable ? 'hover:shadow-md hover:border-zinc-200 dark:hover:border-zinc-700/80 transition-all duration-200' : '')
]) }}>
    @if($title || $subtitle || isset($header))
        <div class="mb-5 flex items-start justify-between">
            <div>
                @if($title)
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            @if(isset($header))
                <div>
                    {{ $header }}
                </div>
            @endif
        </div>
    @endif

    <div class="text-sm text-zinc-600 dark:text-zinc-300">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="mt-6 pt-5 border-t border-zinc-100 dark:border-zinc-800/50 flex items-center justify-end gap-3">
            {{ $footer }}
        </div>
    @endif
</div>
