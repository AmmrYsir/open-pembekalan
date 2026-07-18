<x-layouts.app>
    <x-slot:title>Procurement Overview</x-slot:title>

    <div class="space-y-6">
        <!-- Dashboard Welcome Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Welcome back, Ammar</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Here is what's happening with the acquisition projects today.</p>
            </div>
            <div class="flex items-center gap-3">
                <x-ui.button variant="outline" size="sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Report
                </x-ui.button>
                <x-ui.button variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
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
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">42</h3>
                    <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                        <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
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
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">12</h3>
                    <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                        <span class="text-rose-600 font-semibold inline-flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
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
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16V5" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 2.4M</h3>
                    <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                        <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
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
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">184</h3>
                    <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                        <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
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
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="15.915" fill="none" stroke="currentColor" stroke-width="3" class="text-zinc-100 dark:text-zinc-800"></circle>
                                <!-- Approved: 42% -->
                                <circle cx="18" cy="18" r="15.915" fill="none" stroke="currentColor" stroke-width="3" stroke-dasharray="42 58" stroke-dashoffset="0" class="text-emerald-500"></circle>
                                <!-- Ongoing: 28% -->
                                <circle cx="18" cy="18" r="15.915" fill="none" stroke="currentColor" stroke-width="3" stroke-dasharray="28 72" stroke-dashoffset="-42" class="text-sky-400"></circle>
                                <!-- Review: 20% -->
                                <circle cx="18" cy="18" r="15.915" fill="none" stroke="currentColor" stroke-width="3" stroke-dasharray="20 80" stroke-dashoffset="-70" class="text-amber-400"></circle>
                                <!-- Rejected: 10% -->
                                <circle cx="18" cy="18" r="15.915" fill="none" stroke="currentColor" stroke-width="3" stroke-dasharray="10 90" stroke-dashoffset="-90" class="text-rose-500"></circle>
                            </svg>
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
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
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
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
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
