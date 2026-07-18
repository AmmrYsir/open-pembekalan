@props([
    'required' => false,
])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400']) }}>
    {{ $slot }}
    @if($required)
        <span class="text-rose-500 ml-0.5" title="Required">*</span>
    @endif
</label>
