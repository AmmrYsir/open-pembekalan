<?php

use App\Support\FeatureRegistry;
use Illuminate\Support\Collection;
use Laravel\Pennant\Feature;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $search = '';
    public string $scopeFilter = 'all';

    public bool $showDetailsModal = false;
    public ?array $selectedFeature = null;

    #[Computed]
    public function features(): Collection
    {
        return collect(FeatureRegistry::all())
            ->map(function (array $feature) {
                $feature['is_active'] = Feature::active($feature['key']);
                return $feature;
            })
            ->when($this->search, function (Collection $collection) {
                $query = strtolower($this->search);
                return $collection->filter(function (array $feature) use ($query) {
                    return str_contains(strtolower($feature['name']), $query)
                        || str_contains(strtolower($feature['key']), $query)
                        || str_contains(strtolower($feature['description']), $query);
                });
            })
            ->when($this->scopeFilter !== 'all', function (Collection $collection) {
                return $collection->where('scope', $this->scopeFilter);
            });
    }

    #[Computed]
    public function totalCount(): int
    {
        return count(FeatureRegistry::all());
    }

    #[Computed]
    public function activeCount(): int
    {
        return collect(FeatureRegistry::all())
            ->filter(fn (array $f) => Feature::active($f['key']))
            ->count();
    }

    #[Computed]
    public function experimentalCount(): int
    {
        return collect(FeatureRegistry::all())
            ->where('scope', 'experimental')
            ->count();
    }

    public function toggleFeature(string $key): void
    {
        $feature = FeatureRegistry::find($key);

        if (! $feature) {
            return;
        }

        $currentlyActive = Feature::active($key);

        if ($currentlyActive) {
            Feature::deactivateForEveryone($key);
            session()->flash('success', "Feature '{$feature['name']}' disabled successfully.");
        } else {
            Feature::activateForEveryone($key);
            session()->flash('success', "Feature '{$feature['name']}' enabled successfully.");
        }
    }

    public function viewDetails(string $key): void
    {
        $feature = FeatureRegistry::find($key);

        if ($feature) {
            $feature['is_active'] = Feature::active($key);
            $this->selectedFeature = $feature;
            $this->showDetailsModal = true;
        }
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->selectedFeature = null;
    }
};
?>

<div class="space-y-6">
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
             class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 text-sm font-medium shadow-xs">
            <x-heroicon-o-check class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <x-ui.card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Total Registered Features</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ $this->totalCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <x-heroicon-o-adjustments-horizontal class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Active Features</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ $this->activeCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <x-heroicon-o-check-circle class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Experimental Lab Features</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ $this->experimentalCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <x-heroicon-o-beaker class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Controls & Table --}}
    <x-ui.card>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
            <div class="relative flex-1 max-w-md">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search feature name, key, or description..."
                       class="w-full pl-10 pr-4 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all" />
            </div>

            <div class="flex items-center gap-3">
                <select wire:model.live="scopeFilter"
                        class="px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer">
                    <option value="all">All Scopes</option>
                    <option value="experimental">Experimental Users</option>
                    <option value="global">Public / Global</option>
                    <option value="user">User Profiles</option>
                </select>
            </div>
        </div>

        <x-ui.table :headers="['Feature Name & Key', 'Target Scope', 'Status', 'Actions']">
            @forelse($this->features as $feature)
                <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center shrink-0 mt-0.5">
                                @if($feature['icon'] === 'beaker')
                                    <x-heroicon-o-beaker class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                @elseif($feature['icon'] === 'globe-alt')
                                    <x-heroicon-o-globe-alt class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                @elseif($feature['icon'] === 'bell')
                                    <x-heroicon-o-bell class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                @elseif($feature['icon'] === 'user-group')
                                    <x-heroicon-o-user-group class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                @elseif($feature['icon'] === 'document-chart-bar')
                                    <x-heroicon-o-document-chart-bar class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                @else
                                    <x-heroicon-o-folder-open class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $feature['name'] }}</span>
                                    <span class="font-mono text-[11px] px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-medium">{{ $feature['key'] }}</span>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">{{ $feature['description'] }}</p>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($feature['scope'] === 'experimental')
                            <x-ui.badge variant="warning" pill>
                                {{ $feature['scope_label'] }}
                            </x-ui.badge>
                        @elseif($feature['scope'] === 'global')
                            <x-ui.badge variant="info" pill>
                                {{ $feature['scope_label'] }}
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="secondary" pill>
                                {{ $feature['scope_label'] }}
                            </x-ui.badge>
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        {{-- Horizontal Toggle Switch with Status Badge --}}
                        <div class="flex items-center gap-3">
                            <button
                                wire:click="toggleFeature('{{ $feature['key'] }}')"
                                type="button"
                                role="switch"
                                aria-checked="{{ $feature['is_active'] ? 'true' : 'false' }}"
                                title="{{ $feature['is_active'] ? 'Click to disable feature' : 'Click to enable feature' }}"
                                class="relative inline-flex h-7 w-13 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500/30 {{ $feature['is_active'] ? 'bg-emerald-600 dark:bg-emerald-500' : 'bg-zinc-300 dark:bg-zinc-700' }}"
                            >
                                <span class="sr-only">Toggle feature</span>
                                <span
                                    class="pointer-events-none inline-flex h-6 w-6 transform items-center justify-center rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out {{ $feature['is_active'] ? 'translate-x-6' : 'translate-x-0' }}"
                                >
                                    @if($feature['is_active'])
                                        <x-heroicon-o-check class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" />
                                    @else
                                        <x-heroicon-o-x-mark class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" />
                                    @endif
                                </span>
                            </button>
                            <span class="text-xs font-bold uppercase tracking-wider min-w-8 cursor-pointer select-none" wire:click="toggleFeature('{{ $feature['key'] }}')">
                                @if($feature['is_active'])
                                    <span class="text-emerald-600 dark:text-emerald-400">ON</span>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500">OFF</span>
                                @endif
                            </span>
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <button
                            wire:click="viewDetails('{{ $feature['key'] }}')"
                            title="View Feature Details"
                            class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-950/30 dark:hover:text-sky-400 transition-all"
                        >
                            <x-heroicon-o-eye class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <span class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                <x-heroicon-o-adjustments-horizontal class="w-7 h-7 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">No features found</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Try adjusting your search or scope filter.</p>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>

    {{-- Responsive Details Modal --}}
    @if($this->showDetailsModal && $this->selectedFeature)
        <div
            x-data
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 md:p-8"
            role="dialog"
            aria-modal="true"
        >
            {{-- Modal Backdrop --}}
            <div class="fixed inset-0 bg-zinc-950/60 backdrop-blur-md cursor-pointer" wire:click="closeDetailsModal"></div>

            {{-- Responsive Modal Card --}}
            <div
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 bg-white dark:bg-zinc-900 rounded-2xl md:rounded-3xl shadow-2xl border border-zinc-200/80 dark:border-zinc-800/80 p-5 sm:p-7 w-full max-w-md sm:max-w-xl lg:max-w-2xl max-h-[85vh] sm:max-h-[90vh] flex flex-col justify-between overflow-y-auto space-y-6"
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800 shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-adjustments-horizontal class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </span>
                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ $this->selectedFeature['name'] }}</h3>
                            <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400 truncate block">{{ $this->selectedFeature['key'] }}</span>
                        </div>
                    </div>
                    {{-- Cross Mark Close Button with cursor-pointer --}}
                    <button
                        wire:click="closeDetailsModal"
                        title="Close Modal"
                        class="p-2 rounded-xl text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all cursor-pointer shrink-0 ml-2"
                    >
                        <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="space-y-5 text-sm flex-1 overflow-y-auto pr-1">
                    <div>
                        <label class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Description</label>
                        <p class="text-zinc-700 dark:text-zinc-300 mt-1 leading-relaxed text-xs sm:text-sm">{{ $this->selectedFeature['description'] }}</p>
                    </div>

                    {{-- Responsive Grid Breakdown --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
                            <label class="text-[11px] font-medium text-zinc-400 dark:text-zinc-500 uppercase">Target Scope</label>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100 mt-1 text-xs sm:text-sm">{{ $this->selectedFeature['scope_label'] }}</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
                            <label class="text-[11px] font-medium text-zinc-400 dark:text-zinc-500 uppercase">Pennant Status</label>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="w-2 h-2 rounded-full {{ $this->selectedFeature['is_active'] ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-400' }}"></span>
                                <p class="font-semibold {{ $this->selectedFeature['is_active'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-500' }} text-xs sm:text-sm">
                                    {{ $this->selectedFeature['is_active'] ? 'ACTIVE (Enabled)' : 'INACTIVE (Disabled)' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Blade Directive Code Usage</label>
                        <div class="mt-2 p-3.5 rounded-xl bg-zinc-900 dark:bg-zinc-950 font-mono text-xs text-emerald-400 border border-zinc-800 overflow-x-auto shadow-inner">
                            <code>
                                @@feature('{{ $this->selectedFeature['key'] }}')<br>
                                &nbsp;&nbsp;&lt;!-- {{ $this->selectedFeature['name'] }} feature component --&gt;<br>
                                @@endfeature
                            </code>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-end shrink-0">
                    <x-ui.button variant="outline" size="sm" wire:click="closeDetailsModal" class="cursor-pointer">
                        Close
                    </x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>
