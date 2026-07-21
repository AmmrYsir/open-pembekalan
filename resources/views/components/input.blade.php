@props([
    'label' => null,
    'error' => null,
    'id',
    'type' => 'text',
    'required' => false,
    'placeholder' => '',
])

<div class="space-y-1.5 w-full">
    @if($label)
        <x-label :for="$id" :required="$required">
            {{ $label }}
        </x-label>
    @endif

    <div class="relative rounded-xl shadow-xs">
        @if(isset($icon))
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500">
                {{ $icon }}
            </div>
        @endif

        <input
            id="{{ $id }}"
            type="{{ $type }}"
            @if($required) required @endif
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' => 'block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 py-2.5 px-3.5 text-sm transition-all focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:outline-none disabled:bg-zinc-50 dark:disabled:bg-zinc-800/50 disabled:text-zinc-500' . 
                ($error ? ' border-rose-500 dark:border-rose-500 focus:border-rose-500 dark:focus:border-rose-500 focus:ring-rose-500' : '') .
                (isset($icon) ? ' pl-10' : '') .
                (isset($suffix) ? ' pr-28' : '')
            ]) }}
        />

        @if(isset($suffix))
            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                {{ $suffix }}
            </div>
        @endif
    </div>

    @if($error)
        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1 flex items-center gap-1">
            <x-heroicon-s-exclamation-circle class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" />
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
