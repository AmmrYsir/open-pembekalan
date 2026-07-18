@props([
    'headers' => [],
])

<div class="overflow-x-auto rounded-2xl border border-zinc-100 dark:border-zinc-800/80 shadow-xs">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-zinc-100 dark:divide-zinc-800/50']) }}>
        <thead class="bg-zinc-50/70 dark:bg-zinc-800/40">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-100 dark:divide-zinc-800/50">
            {{ $slot }}
        </tbody>
    </table>
</div>
