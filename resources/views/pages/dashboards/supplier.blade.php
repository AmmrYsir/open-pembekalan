<div class="space-y-6">
    <!-- Supplier Welcome Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Welcome back, {{ auth()->user()?->name ?? 'Valued Supplier' }}</h1>
                <span class="inline-flex items-center rounded-md bg-emerald-50 dark:bg-emerald-950/40 px-2 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400 ring-1 ring-inset ring-emerald-600/20">Verified Supplier</span>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Manage your active bids, monitor open tender opportunities, and track award statuses.</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="outline" size="sm">
                <x-heroicon-o-document-text class="w-4 h-4 mr-2" />
                My Submissions
            </x-button>
            <x-button variant="primary" size="sm">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 mr-2" />
                Browse Open Tenders
            </x-button>
        </div>
    </div>

    <!-- Supplier Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Open Opportunities</span>
                <span class="text-indigo-600 dark:text-indigo-400 p-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/30">
                    <x-heroicon-o-briefcase class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">18</h3>
                <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                    <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">
                        <x-heroicon-o-arrow-up class="w-3.5 h-3.5" />
                        +5 new
                    </span>
                    <span>matching your MOF code</span>
                </p>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Submitted Bids</span>
                <span class="text-blue-600 dark:text-blue-400 p-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/30">
                    <x-heroicon-o-paper-airplane class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">7</h3>
                <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                    <span class="text-blue-600 font-semibold inline-flex items-center gap-0.5">2 pending</span>
                    <span>evaluation</span>
                </p>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Contracts Awarded</span>
                <span class="text-emerald-600 dark:text-emerald-400 p-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30">
                    <x-heroicon-o-trophy class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">5</h3>
                <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                    <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">
                        <x-heroicon-o-check class="w-3.5 h-3.5" />
                        71.4%
                    </span>
                    <span>win rate</span>
                </p>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total Contract Value</span>
                <span class="text-amber-600 dark:text-amber-400 p-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/30">
                    <x-heroicon-o-currency-dollar class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 1.85M</h3>
                <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                    <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">+RM 350k</span>
                    <span>this financial year</span>
                </p>
            </div>
        </x-card>
    </div>

    <!-- Main Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Table of Submitted Bids -->
        <div class="lg:col-span-2 space-y-6">
            <x-card title="My Recent Submissions" subtitle="Track the progress of your submitted proposals and quotations">
                <x-table :headers="['Tender / Quotation Name', 'Category', 'My Quotation', 'Closing Date', 'Status', 'Action']">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">PRJ-2026-004</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Fiber Optic Infrastructure Setup</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">Telecommunications</td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-zinc-700 dark:text-zinc-300">RM 435,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-zinc-500">18 Jul 2026</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-badge variant="success">Awarded</x-badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button variant="outline" size="sm">View Offer</x-button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">PRJ-2026-003</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Cloud Storage Expansion</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">IT Services</td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-zinc-700 dark:text-zinc-300">RM 118,500</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-zinc-500">25 Jul 2026</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-badge variant="warning">Under Evaluation</x-badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button variant="outline" size="sm">Details</x-button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">PRJ-2026-001</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Solar Panel Phase 2</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">Energy</td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-zinc-700 dark:text-zinc-300">RM 820,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-zinc-500">10 Jun 2026</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-badge variant="info">Ongoing Delivery</x-badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button variant="outline" size="sm">Details</x-button>
                        </td>
                    </tr>
                </x-table>
            </x-card>
        </div>

        <!-- Right Column: Verification & Opportunity Feed -->
        <div class="space-y-6">
            <!-- Supplier Profile & Certifications Card -->
            <x-card title="Compliance & Registrations" subtitle="Status of your government procurement licenses">
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-700/50">
                        <div class="flex items-center gap-3">
                            <span class="p-2 rounded-md bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                                <x-heroicon-o-shield-check class="w-4 h-4" />
                            </span>
                            <div>
                                <h4 class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">MOF Certification</h4>
                                <p class="text-[10px] text-zinc-500">Exp: 31 Dec 2027</p>
                            </div>
                        </div>
                        <x-badge variant="success">Active</x-badge>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-700/50">
                        <div class="flex items-center gap-3">
                            <span class="p-2 rounded-md bg-blue-100 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                                <x-heroicon-o-academic-cap class="w-4 h-4" />
                            </span>
                            <div>
                                <h4 class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">CIDB Registration</h4>
                                <p class="text-[10px] text-zinc-500">Grade G7 • Category ME</p>
                            </div>
                        </div>
                        <x-badge variant="success">Active</x-badge>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-700/50">
                        <div class="flex items-center gap-3">
                            <span class="p-2 rounded-md bg-purple-100 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                                <x-heroicon-o-building-office-2 class="w-4 h-4" />
                            </span>
                            <div>
                                <h4 class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">SSM e-Statutar</h4>
                                <p class="text-[10px] text-zinc-500">Sdn Bhd Registered</p>
                            </div>
                        </div>
                        <x-badge variant="success">Verified</x-badge>
                    </div>
                </div>
            </x-card>

            <!-- Upcoming Deadlines Feed -->
            <x-card title="Urgent Deadlines" subtitle="Upcoming tender closing dates">
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-900/30">
                        <x-heroicon-o-clock class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                        <div>
                            <h4 class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">PRJ-2026-003 Cloud Storage</h4>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Clarification response required in 2 days</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
