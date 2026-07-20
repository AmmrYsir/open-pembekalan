<x-layouts.app>
    <x-slot:title>Dashboard</x-slot:title>

    <div class="space-y-6">
        <!-- Dashboard Welcome Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Welcome back, Ammar</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Here is what's happening with the acquisition projects today.</p>
            </div>
            <div class="flex items-center gap-3">
                <x-ui.button variant="outline" size="sm">
                    <x-heroicon-o-document-arrow-down class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    Export Report
                </x-ui.button>
                <x-ui.button variant="primary" size="sm">
                    <x-heroicon-o-plus class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    New Acquisition
                </x-ui.button>
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <x-ui.card>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total Projects</span>
                    <span class="text-emerald-600 dark:text-emerald-400 p-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30">
                        <x-heroicon-o-archive-box class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">42</h3>
                    <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                        <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">
                            <x-heroicon-o-arrow-up class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
                            +8.2%
                        </span>
                        <span>vs last month</span>
                    </p>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Active Tenders</span>
                    <span class="text-blue-600 dark:text-blue-400 p-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/30">
                        <x-heroicon-o-document-chart-bar class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">12</h3>
                    <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                        <span class="text-rose-600 font-semibold inline-flex items-center gap-0.5">
                            <x-heroicon-o-arrow-down class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
                            -4.1%
                        </span>
                        <span>vs last month</span>
                    </p>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total Allocation</span>
                    <span class="text-amber-600 dark:text-amber-400 p-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/30">
                        <x-heroicon-o-currency-dollar class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 2.4M</h3>
                    <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                        <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">
                            <x-heroicon-o-arrow-up class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
                            +12.4%
                        </span>
                        <span>vs last quarter</span>
                    </p>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Active Suppliers</span>
                    <span class="text-purple-600 dark:text-purple-400 p-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/30">
                        <x-heroicon-o-users class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">184</h3>
                    <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                        <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">
                            <x-heroicon-o-arrow-up class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
                            +3
                        </span>
                        <span>new this week</span>
                    </p>
                </div>
            </x-ui.card>
        </div>

        <!-- Main Body Splits -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Table of Tenders -->
            <div class="lg:col-span-2 space-y-6">
                <x-ui.card title="Recent Acquisition Projects" subtitle="A summary of the latest procurement activities and proposals">
                    <x-ui.table :headers="['Project ID / Name', 'Category', 'Budget', 'Status', 'Actions']">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">PRJ-2026-004</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Fiber Optic Infrastructure Setup</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">Telecommunications</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-zinc-700 dark:text-zinc-300">RM 450,000</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-ui.badge variant="success">Approved</x-ui.badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-ui.button variant="outline" size="sm">Manage</x-ui.button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">PRJ-2026-003</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Cloud Storage Expansion</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">IT Infrastructure</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-zinc-700 dark:text-zinc-300">RM 120,000</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-ui.badge variant="warning">Under Review</x-ui.badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-ui.button variant="outline" size="sm">Manage</x-ui.button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">PRJ-2026-002</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Office Security Upgrade</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">Facilities</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-zinc-700 dark:text-zinc-300">RM 85,000</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-ui.badge variant="danger">Rejected</x-ui.badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-ui.button variant="outline" size="sm">Manage</x-ui.button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">PRJ-2026-001</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Solar Panel Installation Phase 2</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">Energy & Power</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-zinc-700 dark:text-zinc-300">RM 850,000</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-ui.badge variant="info">Ongoing</x-ui.badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-ui.button variant="outline" size="sm">Manage</x-ui.button>
                            </td>
                        </tr>
                    </x-ui.table>
                </x-ui.card>
            </div>

            <!-- Right Side: Mini Widgets (Status Chart / Recent Tenders Summary) -->
            <div class="space-y-6">
                <!-- SVG Mini Donut Chart -->
                <x-ui.card title="Acquisition Pipeline" subtitle="Status distribution across current projects">
                    <div class="flex flex-col items-center py-4">
                        <div class="relative w-36 h-36 flex items-center justify-center">
                            <!-- SVG Donut Chart Mock -->
                            <x-icon-donut-chart class="w-full h-full transform -rotate-90" viewBox="0 0 36 36" />
                            <div class="absolute flex flex-col items-center">
                                <span class="text-xl font-bold text-zinc-900 dark:text-zinc-100">42</span>
                                <span class="text-[9px] font-semibold text-zinc-400 uppercase tracking-widest">Projects</span>
                            </div>
                        </div>

                        <!-- Labels -->
                        <div class="w-full grid grid-cols-2 gap-3 mt-6">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-xs text-zinc-600 dark:text-zinc-400 truncate">Approved (42%)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-400 shrink-0"></span>
                                <span class="text-xs text-zinc-600 dark:text-zinc-400 truncate">Ongoing (28%)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span>
                                <span class="text-xs text-zinc-600 dark:text-zinc-400 truncate">Review (20%)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
                                <span class="text-xs text-zinc-600 dark:text-zinc-400 truncate">Rejected (10%)</span>
                            </div>
                        </div>
                    </div>
                </x-ui.card>

                <!-- Project Activity Feeds -->
                <x-ui.card title="Recent Activities" subtitle="Updates from procurement officers">
                    <div class="space-y-4 flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-zinc-200 dark:bg-zinc-800" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                                <x-heroicon-o-check-circle class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Fiber Optic project approved by <span class="font-semibold text-zinc-800 dark:text-zinc-200">Farhan</span></p>
                                            </div>
                                            <div class="text-right text-[10px] whitespace-nowrap text-zinc-400 dark:text-zinc-500">
                                                34m ago
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-lg bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                                                <x-heroicon-o-exclamation-triangle class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Tender budget warning triggered for <span class="font-semibold text-zinc-800 dark:text-zinc-200">Cloud Storage</span></p>
                                            </div>
                                            <div class="text-right text-[10px] whitespace-nowrap text-zinc-400 dark:text-zinc-500">
                                                2h ago
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-layouts.app>
