@props([
    'id' => null,
    'label' => null,
    'checked' => false,
])

@php
    $checkboxId = $id ?? 'checkbox-' . uniqid();
@endphp

{{--
    Custom Checkbox Component — x-checkbox
    ==========================================
    Usage:
        <x-checkbox id="remember" name="remember" label="Remember me" />
        <x-checkbox id="agree" wire:model="agreed">I agree to the terms</x-checkbox>

    Props:
        id      — HTML id (auto-generated if omitted)
        label   — Short label text (alternative to slot)
        checked — Pre-checked state (boolean)
    Other attributes (wire:model, name, etc.) are forwarded to the native input.
--}}

<label for="{{ $checkboxId }}" class="group inline-flex items-center gap-2.5 cursor-pointer select-none">

    {{-- Native checkbox: sr-only but accessible, drives sibling peer classes --}}
    <input
        id="{{ $checkboxId }}"
        type="checkbox"
        @if($checked) checked @endif
        {{ $attributes->except(['class', 'id']) }}
        class="peer sr-only"
    />

    {{-- Custom visual tick box (sibling of peer input → peer-checked works) --}}
    <span class="
        relative flex-shrink-0 w-4 h-4 rounded-md
        border border-zinc-300 dark:border-zinc-600
        bg-white dark:bg-zinc-900
        transition-all duration-150
        shadow-xs
        group-hover:border-emerald-400 dark:group-hover:border-emerald-500
        peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500
        peer-focus-visible:ring-offset-2 peer-focus-visible:ring-offset-white
        dark:peer-focus-visible:ring-offset-zinc-950
        peer-checked:bg-emerald-600 peer-checked:border-emerald-600
        peer-checked:shadow-sm peer-checked:shadow-emerald-500/30
        dark:peer-checked:bg-emerald-500 dark:peer-checked:border-emerald-500
        flex items-center justify-center
        overflow-hidden
    " aria-hidden="true">
        {{-- Checkmark SVG inside the box —
             Since this SVG is inside the styled span (not a direct sibling of peer),
             we use CSS-only approach: the span itself changes bg via peer-checked,
             and we always show the SVG but it's only "visible" when the bg turns emerald.
             We use opacity + scale transition driven by the parent span's peer-checked state.
        --}}
        <x-icon-checkbox-check class="w-2.5 h-2.5 text-white opacity-0 scale-50 transition-all duration-150"
            style="transition-property: opacity, transform;"
            viewBox="0 0 10 10"
            fill="none"
            stroke="currentColor"
            stroke-width="2.2"
            stroke-linecap="round"
            stroke-linejoin="round" />
    </span>

    @if($label)
        <span class="text-xs text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-800 dark:group-hover:text-zinc-200 transition-colors duration-150">
            {{ $label }}
        </span>
    @elseif($slot->isNotEmpty())
        <span class="text-xs text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-800 dark:group-hover:text-zinc-200 transition-colors duration-150">
            {{ $slot }}
        </span>
    @endif

</label>

<style>
    /* Drive SVG visibility from the peer input state via CSS sibling + descendant */
    input.peer:checked + span > svg {
        opacity: 1;
        transform: scale(1);
    }
</style>
