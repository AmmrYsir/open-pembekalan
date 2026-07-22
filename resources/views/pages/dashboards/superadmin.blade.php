<div class="space-y-6">
    <!-- Superadmin Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Welcome back, {{ auth()->user()?->name ?? 'Superadmin' }}</h1>
                <span class="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-950/40 px-2 py-1 text-xs font-medium text-purple-700 dark:text-purple-400 ring-1 ring-inset ring-purple-700/20">System Administrator</span>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Overview of system health, active user roles, agency configurations, and background jobs.</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="outline" size="sm" href="{{ route('admin.email-tracker.index') }}">
                <x-heroicon-o-envelope class="w-4 h-4 mr-2" />
                Email Logs
            </x-button>
            <x-button variant="primary" size="sm" href="{{ route('admin.queues.index') }}">
                <x-heroicon-o-cpu-chip class="w-4 h-4 mr-2" />
                Queue Monitor
            </x-button>
        </div>
    </div>

    <!-- Superadmin Metric Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total Users</span>
                <span class="text-indigo-600 dark:text-indigo-400 p-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/30">
                    <x-heroicon-o-users class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">1,248</h3>
                <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                    <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">
                        <x-heroicon-o-arrow-up class="w-3.5 h-3.5" />
                        +24
                    </span>
                    <span>new users this week</span>
                </p>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Agencies & Subagencies</span>
                <span class="text-blue-600 dark:text-blue-400 p-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/30">
                    <x-heroicon-o-building-office class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">48 / 162</h3>
                <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Fully configured</span>
                </p>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">System Health</span>
                <span class="text-emerald-600 dark:text-emerald-400 p-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30">
                    <x-heroicon-o-check-badge class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">100%</h3>
                <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                    <span class="text-emerald-600 font-semibold">0 failed jobs</span>
                    <span>in last 24h</span>
                </p>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total System Projects</span>
                <span class="text-purple-600 dark:text-purple-400 p-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/30">
                    <x-heroicon-o-cube class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">156</h3>
                <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                    <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">RM 14.2M</span>
                    <span>cumulative value</span>
                </p>
            </div>
        </x-card>
    </div>

    <!-- Main Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Side: Role Breakdown & Management Shortcuts -->
        <div class="lg:col-span-2 space-y-6">
            <x-card title="System Role Distribution" subtitle="Active users grouped by assigned authorization roles">
                <x-table :headers="['Role Name', 'Role Slug', 'Active Users', 'Description', 'Action']">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-zinc-900 dark:text-zinc-100">Super Admin</td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-zinc-500">superadmin</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-purple-600 dark:text-purple-400">8</td>
                        <td class="px-6 py-4 text-xs text-zinc-500 truncate max-w-xs">Full administrative access</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button variant="outline" size="sm">Manage</x-button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-zinc-900 dark:text-zinc-100">Procurement Officer</td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-zinc-500">officer</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-blue-600 dark:text-blue-400">142</td>
                        <td class="px-6 py-4 text-xs text-zinc-500 truncate max-w-xs">Creates & manages acquisitions</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button variant="outline" size="sm">Manage</x-button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-zinc-900 dark:text-zinc-100">Supplier</td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-zinc-500">supplier</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-emerald-600 dark:text-emerald-400">980</td>
                        <td class="px-6 py-4 text-xs text-zinc-500 truncate max-w-xs">Submits tender quotations & bids</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button variant="outline" size="sm">Manage</x-button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-zinc-900 dark:text-zinc-100">Urusetia L1 / L2</td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-zinc-500">urusetia-1 / 2</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-amber-600 dark:text-amber-400">118</td>
                        <td class="px-6 py-4 text-xs text-zinc-500 truncate max-w-xs">Secretariat & review committee</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button variant="outline" size="sm">Manage</x-button>
                        </td>
                    </tr>
                </x-table>
            </x-card>

            <!-- Administration Quick Controls -->
            <x-card title="System Master Management" subtitle="Quick access to core lookup tables and settings">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <a href="{{ route('admin.agencies.index') }}" class="p-3.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex flex-col items-center text-center gap-2 group">
                        <x-heroicon-o-building-office class="w-6 h-6 text-zinc-600 dark:text-zinc-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" />
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Agencies</span>
                    </a>
                    <a href="{{ route('admin.subagencies.index') }}" class="p-3.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex flex-col items-center text-center gap-2 group">
                        <x-heroicon-o-building-office-2 class="w-6 h-6 text-zinc-600 dark:text-zinc-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" />
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Sub-Agencies</span>
                    </a>
                    <a href="{{ route('admin.agency-officers.index') }}" class="p-3.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex flex-col items-center text-center gap-2 group">
                        <x-heroicon-o-user-group class="w-6 h-6 text-zinc-600 dark:text-zinc-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" />
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Agency Officers</span>
                    </a>
                    <a href="{{ route('admin.suppliers.index') }}" class="p-3.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex flex-col items-center text-center gap-2 group">
                        <x-heroicon-o-truck class="w-6 h-6 text-zinc-600 dark:text-zinc-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" />
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Suppliers</span>
                    </a>
                    <a href="{{ route('admin.committees.index') }}" class="p-3.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex flex-col items-center text-center gap-2 group">
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-zinc-600 dark:text-zinc-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" />
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Committees</span>
                    </a>
                    <a href="{{ route('admin.features.index') }}" class="p-3.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex flex-col items-center text-center gap-2 group">
                        <x-heroicon-o-adjustments-horizontal class="w-6 h-6 text-zinc-600 dark:text-zinc-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" />
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Feature Flags</span>
                    </a>
                </div>
            </x-card>
        </div>

        <!-- Right Side: System Logs & Audit Timeline -->
        <div class="space-y-6">
            <x-card title="System Audit Logs" subtitle="Recent administrative events and logs">
                <div class="space-y-4 flow-root">
                    <ul class="-mb-8">
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-zinc-200 dark:bg-zinc-800" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                            <x-heroicon-o-user-plus class="w-4 h-4" />
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-xs text-zinc-600 dark:text-zinc-400">New officer assigned to <span class="font-semibold text-zinc-800 dark:text-zinc-200">Kementerian Kewangan</span></p>
                                        </div>
                                        <div class="text-right text-[10px] whitespace-nowrap text-zinc-400 dark:text-zinc-500">12m ago</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-zinc-200 dark:bg-zinc-800" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                            <x-heroicon-o-shield-check class="w-4 h-4" />
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-xs text-zinc-600 dark:text-zinc-400">MOF Code database updated successfully</p>
                                        </div>
                                        <div class="text-right text-[10px] whitespace-nowrap text-zinc-400 dark:text-zinc-500">1h ago</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="relative pb-8">
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-lg bg-purple-50 dark:bg-purple-950/30 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                            <x-heroicon-o-cpu-chip class="w-4 h-4" />
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-xs text-zinc-600 dark:text-zinc-400">Queue worker processed 140 notification jobs</p>
                                        </div>
                                        <div class="text-right text-[10px] whitespace-nowrap text-zinc-400 dark:text-zinc-500">3h ago</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </x-card>
        </div>
    </div>
</div>
