<?php

use App\Models\EmailLog;
use App\Models\FailedJob;
use App\Models\Job;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';

    public bool $showDrawer = false;
    public ?EmailLog $selectedLog = null;
    public string $activeViewTab = 'html'; // 'html' or 'text'

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function totalSentCount(): int
    {
        return EmailLog::where('status', 'sent')->count();
    }

    #[Computed]
    public function totalFailedCount(): int
    {
        return EmailLog::where('status', 'failed')->count() + FailedJob::count();
    }

    #[Computed]
    public function totalQueuedCount(): int
    {
        return Job::count();
    }

    #[Computed]
    public function deliveryRate(): string
    {
        $sent = $this->totalSentCount;
        $failed = $this->totalFailedCount;
        $total = $sent + $failed;

        if ($total === 0) {
            return '100%';
        }

        return round(($sent / $total) * 100, 1) . '%';
    }

    #[Computed]
    public function emailLogs(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return EmailLog::query()
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('recipient_email', 'like', "%{$this->search}%")
                        ->orWhere('recipient_name', 'like', "%{$this->search}%")
                        ->orWhere('subject', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    #[Computed]
    public function failedMailJobs(): \Illuminate\Database\Eloquent\Collection
    {
        return FailedJob::orderBy('failed_at', 'desc')->get();
    }

    public function inspectEmail(int $id): void
    {
        $this->selectedLog = EmailLog::find($id);
        $this->activeViewTab = 'html';
        $this->showDrawer = true;
    }

    public function closeDrawer(): void
    {
        $this->showDrawer = false;
        $this->selectedLog = null;
    }

    public function resendEmail(int $id): void
    {
        $log = EmailLog::find($id);
        if (! $log) {
            session()->flash('error', 'Email record not found.');
            return;
        }

        try {
            $htmlBody = $log->body_html;
            $recipient = $log->recipient_email;
            $subject = $log->subject;

            Mail::html($htmlBody ?? ($log->body_text ?? 'No Content'), function ($message) use ($recipient, $subject) {
                $message->to($recipient)->subject('[RESENT] ' . $subject);
            });

            session()->flash('success', "Email successfully resent to {$recipient}.");
        } catch (\Throwable $e) {
            session()->flash('error', "Failed to resend email: {$e->getMessage()}");
        }
    }

    public function retryFailedMailJob(string|int $id): void
    {
        try {
            Artisan::call('queue:retry', ['id' => [(string) $id]]);
            session()->flash('success', "Failed mail job #{$id} has been requeued.");
        } catch (\Throwable $e) {
            session()->flash('error', "Could not retry mail job: {$e->getMessage()}");
        }
    }

    public function retryAllFailedMailJobs(): void
    {
        try {
            Artisan::call('queue:retry', ['id' => ['all']]);
            session()->flash('success', 'All failed mail jobs have been pushed back to queue.');
        } catch (\Throwable $e) {
            session()->flash('error', "Could not retry failed mail jobs: {$e->getMessage()}");
        }
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

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Sent Emails</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ number_format($this->totalSentCount) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <x-heroicon-o-paper-airplane class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Queued Mail Jobs</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ number_format($this->totalQueuedCount) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <x-heroicon-o-clock class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Failed Mail Jobs</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ number_format($this->totalFailedCount) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Delivery Rate</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ $this->deliveryRate }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <x-heroicon-o-chart-bar class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-card>
    </div>

    {{-- Failed Mail Jobs Banner --}}
    @if ($this->failedMailJobs->count() > 0)
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-800/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <x-heroicon-o-arrow-path class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-rose-950 dark:text-rose-200">Failed Mail Jobs Detected ({{ $this->failedMailJobs->count() }})</h4>
                    <p class="text-xs text-rose-700 dark:text-rose-400 mt-0.5">Some email queue jobs encountered errors and require retrying.</p>
                </div>
            </div>
            <button wire:click="retryAllFailedMailJobs" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-rose-600 text-white hover:bg-rose-500 transition-all inline-flex items-center gap-1.5 shadow-xs cursor-pointer shrink-0">
                <x-heroicon-o-arrow-path class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Retry All Failed Mail Jobs
            </button>
        </div>
    @endif

    {{-- Email Logs Main Table Card --}}
    <x-card>
        {{-- Search & Filter Bar matching theme --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search recipient email, name, or subject..."
                       class="w-full pl-10 pr-4 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all" />
            </div>

            <div class="flex items-center gap-2">
                <select wire:model.live="statusFilter"
                        class="px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer">
                    <option value="all">All Statuses</option>
                    <option value="sent">Sent</option>
                    <option value="failed">Failed</option>
                    <option value="queued">Queued</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <x-table :headers="['Recipient', 'Subject', 'Notification Class', 'Sent / Logged At', 'Status', 'Actions']">
            @forelse ($this->emailLogs as $log)
                <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $log->recipient_email }}</div>
                        @if ($log->recipient_name)
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $log->recipient_name }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-800 dark:text-zinc-200 max-w-xs truncate" title="{{ $log->subject }}">
                        {{ $log->subject }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono text-xs px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-medium">
                            {{ $log->mailable_class ? class_basename($log->mailable_class) : 'System Mailable' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : $log->created_at->format('Y-m-d H:i:s') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($log->status === 'sent')
                            <x-badge variant="success" pill>Sent</x-badge>
                        @elseif ($log->status === 'failed')
                            <x-badge variant="danger" pill>Failed</x-badge>
                        @else
                            <x-badge variant="warning" pill>Queued</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button
                                wire:click="inspectEmail({{ $log->id }})"
                                title="Inspect Email"
                                class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-950/30 dark:hover:text-sky-400 transition-all"
                            >
                                <x-heroicon-o-eye class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            </button>
                            <button
                                wire:click="resendEmail({{ $log->id }})"
                                title="Resend Email"
                                class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 dark:hover:text-emerald-400 transition-all"
                            >
                                <x-heroicon-o-paper-airplane class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <span class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                <x-heroicon-o-envelope-open class="w-7 h-7 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">No email logs found</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Outbound emails sent by the application will automatically appear here.</p>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="mt-4">
            {{ $this->emailLogs->links() }}
        </div>
    </x-card>

    {{-- Inspection Side Drawer --}}
    @if ($showDrawer && $selectedLog)
        <div class="fixed inset-0 z-50 flex items-center justify-end">
            <div class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm" wire:click="closeDrawer"></div>
            <div class="relative z-10 bg-white dark:bg-zinc-900 border-l border-zinc-200/80 dark:border-zinc-800/80 h-full max-w-2xl w-full p-6 shadow-2xl flex flex-col space-y-4">
                {{-- Drawer Header --}}
                <div class="flex items-center justify-between pb-3 border-b border-zinc-150 dark:border-zinc-800">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Email Inspection</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $selectedLog->recipient_email }}</p>
                    </div>
                    <button wire:click="closeDrawer" class="p-1 rounded-lg text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">
                        <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                {{-- Metadata Grid --}}
                <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 text-xs">
                    <div>
                        <span class="text-zinc-400">Subject:</span>
                        <p class="font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5">{{ $selectedLog->subject }}</p>
                    </div>
                    <div>
                        <span class="text-zinc-400">Status:</span>
                        <p class="mt-0.5">
                            @if ($selectedLog->status === 'sent')
                                <x-badge variant="success" pill>Sent</x-badge>
                            @else
                                <x-badge variant="danger" pill>Failed</x-badge>
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-zinc-400">Sent At:</span>
                        <p class="font-mono text-zinc-700 dark:text-zinc-300 mt-0.5">{{ $selectedLog->sent_at ? $selectedLog->sent_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-zinc-400">Notification Class:</span>
                        <p class="font-mono text-zinc-700 dark:text-zinc-300 mt-0.5">{{ $selectedLog->mailable_class ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Content View Tabs --}}
                <div class="flex items-center gap-2 border-b border-zinc-150 dark:border-zinc-800 pb-2">
                    <button wire:click="$set('activeViewTab', 'html')" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer {{ $activeViewTab === 'html' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-800/80 text-zinc-600 dark:text-zinc-300' }}">
                        Rendered HTML Preview
                    </button>
                    <button wire:click="$set('activeViewTab', 'text')" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer {{ $activeViewTab === 'text' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-800/80 text-zinc-600 dark:text-zinc-300' }}">
                        Plain Text View
                    </button>
                </div>

                {{-- Content Body --}}
                <div class="flex-1 overflow-y-auto border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 bg-white dark:bg-zinc-950">
                    @if ($activeViewTab === 'html')
                        @if ($selectedLog->body_html)
                            <iframe srcdoc="{{ e($selectedLog->body_html) }}" class="w-full h-full min-h-[350px] border-0 rounded-lg"></iframe>
                        @else
                            <p class="text-xs text-zinc-400 italic">No HTML content available.</p>
                        @endif
                    @else
                        <pre class="text-xs font-mono text-zinc-800 dark:text-zinc-200 whitespace-pre-wrap">{{ $selectedLog->body_text ?? 'No plain text content.' }}</pre>
                    @endif
                </div>

                @if ($selectedLog->error_message)
                    <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-100 dark:border-rose-900/40 text-xs text-rose-700 dark:text-rose-400">
                        <strong class="block font-semibold">Error Exception:</strong>
                        <span>{{ $selectedLog->error_message }}</span>
                    </div>
                @endif

                {{-- Footer Actions --}}
                <div class="pt-3 border-t border-zinc-150 dark:border-zinc-800 flex items-center justify-between">
                    <x-button wire:click="resendEmail({{ $selectedLog->id }})" variant="primary" size="sm" class="bg-emerald-600 hover:bg-emerald-500 text-white">
                        <x-heroicon-o-paper-airplane class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Resend Email Now
                    </x-button>
                    <x-button wire:click="closeDrawer" variant="outline" size="sm">
                        Close
                    </x-button>
                </div>
            </div>
        </div>
    @endif
</div>
