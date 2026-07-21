<x-layouts.app>
    <x-slot:title>Agency Management</x-slot:title>

    <!-- Reusable Page Header -->
    <x-page-header title="Agency Management" subtitle="Overview of registered partner agencies, departments, and procurement centers.">
        <x-slot:actions>
            <x-button variant="outline" size="sm" onclick="alert('Boilerplate: Filter triggered')">
                <x-heroicon-o-funnel class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Filter Agencies
            </x-button>
            <x-button variant="primary" size="sm" onclick="alert('Boilerplate: Add Agency action triggered')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Add New Agency
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Grid Layout of Agency Information cards or tables -->
    <div class="space-y-6">
        <!-- Grid list of agencies -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <x-card title="Ministry of Communications" subtitle="AGY-10029">
                <div class="space-y-3 mt-2">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Responsible for nationwide telecom upgrades and digital infrastructure acquisitions.</p>
                    <div class="flex items-center justify-between text-xs pt-2">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-350">Active Projects:</span>
                        <x-badge variant="success">8 Active</x-badge>
                    </div>
                </div>
            </x-card>
            <x-card title="National Energy Board" subtitle="AGY-10034">
                <div class="space-y-3 mt-2">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Oversees renewable power allocation systems and solar panel supply partnerships.</p>
                    <div class="flex items-center justify-between text-xs pt-2">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-350">Active Projects:</span>
                        <x-badge variant="success">5 Active</x-badge>
                    </div>
                </div>
            </x-card>
            <x-card title="Ministry of Treasury" subtitle="AGY-10012">
                <div class="space-y-3 mt-2">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Centralized budgeting review committee, auditing major procurement allocations.</p>
                    <div class="flex items-center justify-between text-xs pt-2">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-350">Active Projects:</span>
                        <x-badge variant="info">Central Auditing</x-badge>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Styled Data Table demonstrating agency detail storage -->
        <x-card title="Detailed Registry List" subtitle="All agency focal points and registered procurement departments.">
            <x-table :headers="['Agency Name & Code', 'Focal Officer', 'Department Group', 'Supply Scope', 'Status', 'Actions']">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">National Energy Board</div>
                        <div class="text-xs text-zinc-500">AGY-10034</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">Ir. Dr. Hafiz Basri</td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">Energy & Power Grid</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge variant="primary">Solar Infrastructure</x-badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge variant="success">Verified</x-badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-button variant="outline" size="sm" onclick="alert('Boilerplate: Edit triggered')">Edit</x-button>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">Ministry of Communications</div>
                        <div class="text-xs text-zinc-500">AGY-10029</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">Sarah Michelle Lee</td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">Telecommunications</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge variant="primary">Fiber Rollouts</x-badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge variant="success">Verified</x-badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-button variant="outline" size="sm" onclick="alert('Boilerplate: Edit triggered')">Edit</x-button>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">Ministry of Transport</div>
                        <div class="text-xs text-zinc-500">AGY-10051</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">Amran Sulaiman</td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-650 dark:text-zinc-300">Logistics & Rail</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge variant="primary">Fleet Procurement</x-badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge variant="warning">Awaiting Review</x-badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-button variant="outline" size="sm" onclick="alert('Boilerplate: Edit triggered')">Edit</x-button>
                    </td>
                </tr>
            </x-table>
        </x-card>
    </div>
</x-layouts.app>
