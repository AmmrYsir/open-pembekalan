@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6']) }}>
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex items-center gap-3 shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>
