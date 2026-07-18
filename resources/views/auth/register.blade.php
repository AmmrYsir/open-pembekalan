<x-layouts.guest>
    <x-slot:title>Create Account - openPembekalan</x-slot:title>

    <x-ui.card>
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Create Account</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1.5">Join the openPembekalan procurement portal</p>
        </div>

        <div x-data="{ role: 'supplier' }" class="space-y-5">
            <!-- Role Toggle Segmented Control -->
            <div class="p-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex">
                <button @click="role = 'supplier'"
                        type="button"
                        :class="role === 'supplier' ? 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200'"
                        class="flex-1 text-center py-2 text-xs font-semibold rounded-lg transition-all duration-200 focus:outline-none">
                    Supplier Portal
                </button>
                <button @click="role = 'staff'"
                        type="button"
                        :class="role === 'staff' ? 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200'"
                        class="flex-1 text-center py-2 text-xs font-semibold rounded-lg transition-all duration-200 focus:outline-none">
                    Procurement Staff
                </button>
            </div>

            <form action="/verify-email" method="GET" class="space-y-4">
                <!-- Common Fields -->
                <x-ui.input id="name" type="text" label="Full Name" placeholder="Ammar Yasir" required>
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.input>

                <x-ui.input id="email" type="email" label="Work Email Address" placeholder="ammar@company.com" required>
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                        </svg>
                    </x-slot:icon>
                </x-ui.input>

                <!-- Supplier Specific Fields -->
                <div x-show="role === 'supplier'" class="space-y-4">
                    <x-ui.input id="company_name" type="text" label="Company Name" placeholder="Acme Logistics Sdn Bhd">
                        <x-slot:icon>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </x-slot:icon>
                    </x-ui.input>

                    <x-ui.input id="company_reg" type="text" label="Company Registration No." placeholder="1234567-A">
                        <x-slot:icon>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </x-slot:icon>
                    </x-ui.input>
                </div>

                <!-- Staff Specific Fields -->
                <div x-show="role === 'staff'" class="space-y-4" style="display: none;">
                    <x-ui.input id="employee_id" type="text" label="Employee ID" placeholder="OB-90812">
                        <x-slot:icon>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                        </x-slot:icon>
                    </x-ui.input>

                    <div class="space-y-1.5 w-full">
                        <x-ui.label for="department">Department / Unit</x-ui.label>
                        <div class="relative rounded-xl shadow-xs">
                            <select id="department" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm transition-all focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:outline-none">
                                <option>Acquisitions Division</option>
                                <option>Finance & Budgeting</option>
                                <option>Supplier Operations & Audit</option>
                                <option>IT Administration</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Password Fields -->
                <x-ui.input id="password" type="password" label="Password" placeholder="••••••••" required>
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.input>

                <div class="flex items-center">
                    <label class="flex items-start gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" required class="w-4 h-4 mt-0.5 rounded border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 focus:outline-none">
                        <span class="text-xs text-zinc-600 dark:text-zinc-400">I agree to the <a href="#" class="text-emerald-600 hover:text-emerald-500 font-semibold underline">Terms of Service</a> and <a href="#" class="text-emerald-600 hover:text-emerald-500 font-semibold underline">Privacy Policy</a></span>
                    </label>
                </div>

                <x-ui.button type="submit" class="w-full">
                    Register Account
                </x-ui.button>
            </form>
        </div>

        <x-slot:footer>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Already registered?</span>
            <a href="/login" class="text-xs font-bold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300">Sign in instead</a>
        </x-slot:footer>
    </x-ui.card>
</x-layouts.guest>
