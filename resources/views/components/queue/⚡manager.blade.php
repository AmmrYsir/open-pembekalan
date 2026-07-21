<?php

use App\Models\FailedJob;
use App\Models\Job;
use App\Models\JobBatch;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $activeTab = 'pending';

    public bool $showPayloadModal = false;
    public string $selectedTitle = '';
    public string $selectedPayload = '';
    public string $selectedException = '';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage('pendingPage');
        $this->resetPage('failedPage');
        $this->resetPage('batchPage');
    }

    #[Computed]
    public function pendingCount(): int
    {
        return Job::count();
    }

    #[Computed]
    public function failedCount(): int
    {
        return FailedJob::count();
    }

    #[Computed]
    public function batchCount(): int
    {
        return JobBatch::count();
    }

    #[Computed]
    public function pendingJobs(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Job::query()
            ->orderBy('id', 'desc')
            ->paginate(10, pageName: 'pendingPage');
    }

    #[Computed]
    public function failedJobs(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return FailedJob::query()
            ->orderBy('id', 'desc')
            ->paginate(10, pageName: 'failedPage');
    }

    #[Computed]
    public function jobBatches(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return JobBatch::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10, pageName: 'batchPage');
    }

    public function retryFailedJob(string|int $id): void
    {
        try {
            Artisan::call('queue:retry', ['id' => [(string) $id]]);
            session()->flash('success', "Failed job #{$id} has been requeued.");
        } catch (\Throwable $e) {
            session()->flash('error', "Could not retry job: {$e->getMessage()}");
        }
    }

    public function retryAllFailedJobs(): void
    {
        try {
            Artisan::call('queue:retry', ['id' => ['all']]);
            session()->flash('success', 'All failed jobs have been pushed back to the queue.');
        } catch (\Throwable $e) {
            session()->flash('error', "Could not retry failed jobs: {$e->getMessage()}");
        }
    }

    public function forgetFailedJob(string|int $id): void
    {
        try {
            Artisan::call('queue:forget', ['id' => (string) $id]);
            session()->flash('success', "Failed job #{$id} deleted.");
        } catch (\Throwable $e) {
            FailedJob::where('id', $id)->orWhere('uuid', $id)->delete();
            session()->flash('success', "Failed job #{$id} deleted.");
        }
    }

    public function flushFailedJobs(): void
    {
        try {
            Artisan::call('queue:flush');
            session()->flash('success', 'All failed jobs cleared.');
        } catch (\Throwable $e) {
            FailedJob::query()->delete();
            session()->flash('success', 'All failed jobs cleared.');
        }
    }

    public function cancelPendingJob(int $id): void
    {
        Job::where('id', $id)->delete();
        session()->flash('success', "Pending job #{$id} cancelled.");
    }

    public function inspectPayload(int|string $id, string $type = 'pending'): void
    {
        $this->selectedTitle = "Inspect Job #{$id}";
        $this->selectedPayload = '';
        $this->selectedException = '';

        if ($type === 'pending') {
            $job = Job::find($id);
            if ($job) {
                $decoded = json_decode($job->payload, true);
                $this->selectedPayload = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: $job->payload;
            }
        } else {
            $failed = FailedJob::find($id);
            if ($failed) {
                $decoded = json_decode($failed->payload, true);
                $this->selectedPayload = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: $failed->payload;
                $this->selectedException = $failed->exception;
            }
        }

        $this->showPayloadModal = true;
    }

    public function closeModal(): void
    {
        $this->showPayloadModal = false;
    }
};
?>

<div class="space-y-6">
    {{-- Toast feedback --}}
    @if (session('success'))
        <x-alert type="success" title="Success" dismissible>
            {{ session('success') }}
        </x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-alert>
    @endif

    {{-- Stats Cards Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Pending Jobs</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ number_format($this->pendingCount) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <x-heroicon-o-clock class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Failed Jobs</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ number_format($this->failedCount) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Job Batches</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ number_format($this->batchCount) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <x-heroicon-o-squares-2x2 class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Queue Driver</p>
                    <p class="text-base font-bold text-emerald-600 dark:text-emerald-400 mt-1 uppercase">{{ config('queue.default', 'database') }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <x-heroicon-o-cpu-chip class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-card>
    </div>

    {{-- Main Table Container Card --}}
    <x-card>
        {{-- Header Tabs & Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-2">
                <button wire:click="setTab('pending')" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all {{ $activeTab === 'pending' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-800/80 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                    Pending Jobs ({{ $this->pendingCount }})
                </button>
                <button wire:click="setTab('failed')" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all {{ $activeTab === 'failed' ? 'bg-rose-600 text-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-800/80 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                    Failed Jobs ({{ $this->failedCount }})
                </button>
                <button wire:click="setTab('batches')" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all {{ $activeTab === 'batches' ? 'bg-blue-600 text-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-800/80 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                    Job Batches ({{ $this->batchCount }})
                </button>
            </div>

            @if ($activeTab === 'failed' && $this->failedCount > 0)
                <div class="flex items-center gap-2">
                    <button wire:click="retryAllFailedJobs" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-500 transition-all inline-flex items-center gap-1.5 shadow-xs cursor-pointer">
                        <x-heroicon-o-arrow-path class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Retry All Failed Jobs
                    </button>
                    <button wire:click="flushFailedJobs" onclick="return confirm('Clear all failed jobs?') || event.stopImmediatePropagation()" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-rose-600 text-white hover:bg-rose-500 transition-all inline-flex items-center gap-1.5 shadow-xs cursor-pointer">
                        <x-heroicon-o-trash class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Flush Failed Jobs
                    </button>
                </div>
            @endif
        </div>

        {{-- Pending Jobs Tab Content --}}
        @if ($activeTab === 'pending')
            <x-table :headers="['Job ID', 'Queue', 'Attempts', 'Reserved At', 'Available At', 'Job Payload', 'Actions']">
                @forelse ($this->pendingJobs as $job)
                    @php
                        $payloadArr = json_decode($job->payload, true);
                        $displayName = $payloadArr['displayName'] ?? ($payloadArr['job'] ?? 'Closure');
                    @endphp
                    <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-zinc-900 dark:text-zinc-100">
                            #{{ $job->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-badge variant="neutral">{{ $job->queue }}</x-badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-700 dark:text-zinc-300">
                            {{ $job->attempts }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $job->reserved_at ? date('Y-m-d H:i:s', $job->reserved_at) : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            {{ date('Y-m-d H:i:s', $job->available_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-xs px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium" title="{{ $displayName }}">
                                {{ Str::limit($displayName, 35) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button
                                    wire:click="inspectPayload({{ $job->id }}, 'pending')"
                                    title="Inspect Payload"
                                    class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-950/30 dark:hover:text-sky-400 transition-all"
                                >
                                    <x-heroicon-o-eye class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                </button>
                                <button
                                    wire:click="cancelPendingJob({{ $job->id }})"
                                    title="Cancel Job"
                                    class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 dark:hover:text-rose-400 transition-all"
                                >
                                    <x-heroicon-o-trash class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                    <x-heroicon-o-check-circle class="w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" />
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">No pending queue jobs</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">All background queue tasks have finished processing.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-table>
            <div class="mt-4">
                {{ $this->pendingJobs->links() }}
            </div>
        @endif

        {{-- Failed Jobs Tab Content --}}
        @if ($activeTab === 'failed')
            <x-table :headers="['ID / UUID', 'Queue', 'Failed At', 'Job Class', 'Exception Summary', 'Actions']">
                @forelse ($this->failedJobs as $failed)
                    @php
                        $payloadArr = json_decode($failed->payload, true);
                        $displayName = $payloadArr['displayName'] ?? ($payloadArr['job'] ?? 'Unknown Job');
                        $exceptionSnippet = Str::limit(strtok($failed->exception, "\n"), 70);
                    @endphp
                    <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-zinc-900 dark:text-zinc-100" title="{{ $failed->uuid }}">
                            #{{ $failed->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-badge variant="danger">{{ $failed->queue }}</x-badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $failed->failed_at ? \Carbon\Carbon::parse($failed->failed_at)->format('Y-m-d H:i:s') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm" title="{{ $displayName }}">
                                {{ Str::limit(class_basename($displayName), 25) }}
                            </div>
                            <div class="text-xs font-mono text-zinc-500 dark:text-zinc-400 mt-0.5 truncate max-w-xs" title="{{ $displayName }}">
                                {{ $displayName }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-rose-600 dark:text-rose-400 leading-relaxed max-w-xs truncate" title="{{ $failed->exception }}">
                                {{ $exceptionSnippet }}
                            </p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button
                                    wire:click="inspectPayload({{ $failed->id }}, 'failed')"
                                    title="Inspect Details"
                                    class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-950/30 dark:hover:text-sky-400 transition-all"
                                >
                                    <x-heroicon-o-eye class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                </button>
                                <button
                                    wire:click="retryFailedJob({{ $failed->id }})"
                                    title="Retry Job"
                                    class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 dark:hover:text-emerald-400 transition-all"
                                >
                                    <x-heroicon-o-arrow-path class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                </button>
                                <button
                                    wire:click="forgetFailedJob({{ $failed->id }})"
                                    title="Delete Job"
                                    class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 dark:hover:text-rose-400 transition-all"
                                >
                                    <x-heroicon-o-trash class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                    <x-heroicon-o-check-badge class="w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" />
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">No failed queue jobs</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">All background tasks are completing without errors.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-table>
            <div class="mt-4">
                {{ $this->failedJobs->links() }}
            </div>
        @endif

        {{-- Job Batches Tab Content --}}
        @if ($activeTab === 'batches')
            <x-table :headers="['Batch ID', 'Batch Name', 'Total Jobs', 'Pending', 'Failed', 'Created At', 'Status']">
                @forelse ($this->jobBatches as $batch)
                    <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-zinc-900 dark:text-zinc-100" title="{{ $batch->id }}">
                            {{ Str::limit($batch->id, 14) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $batch->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-700 dark:text-zinc-300">
                            {{ $batch->total_jobs }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-amber-600 dark:text-amber-400 font-bold">
                            {{ $batch->pending_jobs }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-600 dark:text-rose-400 font-bold">
                            {{ $batch->failed_jobs }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            {{ date('Y-m-d H:i:s', $batch->created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($batch->finished_at)
                                <x-badge variant="success">Finished</x-badge>
                            @elseif ($batch->cancelled_at)
                                <x-badge variant="danger">Cancelled</x-badge>
                            @else
                                <x-badge variant="warning">In Progress</x-badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                    <x-heroicon-o-squares-2x2 class="w-7 h-7 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" />
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">No job batches</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">No batched queue operations recorded.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-table>
            <div class="mt-4">
                {{ $this->jobBatches->links() }}
            </div>
        @endif
    </x-card>

    {{-- Inspection Modal --}}
    @if ($showPayloadModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="relative z-10 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200/80 dark:border-zinc-800/80 max-w-3xl w-full p-6 space-y-4 max-h-[85vh] flex flex-col">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-150 dark:border-zinc-800">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $selectedTitle }}</h3>
                    <button wire:click="closeModal" class="p-1 rounded-lg text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">
                        <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto space-y-4 pr-1">
                    @if ($selectedPayload)
                        <div>
                            <h4 class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mb-1.5">Job Payload (JSON)</h4>
                            <pre class="p-3.5 bg-zinc-900 text-emerald-400 rounded-xl text-xs overflow-x-auto font-mono max-h-60">{{ $selectedPayload }}</pre>
                        </div>
                    @endif

                    @if ($selectedException)
                        <div>
                            <h4 class="text-xs font-bold text-rose-500 dark:text-rose-400 mb-1.5">Exception Trace</h4>
                            <pre class="p-3.5 bg-rose-950/40 border border-rose-900/50 text-rose-200 rounded-xl text-xs overflow-x-auto font-mono max-h-60">{{ $selectedException }}</pre>
                        </div>
                    @endif
                </div>

                <div class="pt-3 border-t border-zinc-150 dark:border-zinc-800 flex justify-end">
                    <x-button wire:click="closeModal" variant="outline" size="sm">Close</x-button>
                </div>
            </div>
        </div>
    @endif
</div>
