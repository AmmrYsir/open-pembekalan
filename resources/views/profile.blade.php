<x-layouts.app>
    <x-slot:title>My Profile</x-slot:title>

    <div class="space-y-6 max-w-4xl">
        <!-- Profile Banner Card -->
        <x-ui.card>
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                <!-- Avatar Setup -->
                <div class="relative shrink-0 group">
                    <img class="h-24 w-24 rounded-2xl object-cover ring-4 ring-emerald-500/10" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Avatar">
                    <button class="absolute -bottom-1 -right-1 p-1.5 rounded-lg bg-white dark:bg-zinc-800 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 border border-zinc-200 dark:border-zinc-700 shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 text-center md:text-left space-y-1.5">
                    <div class="flex flex-col md:flex-row md:items-center gap-2">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Ammar Yasir</h2>
                        <x-ui.badge variant="success" pill>Procurement Officer</x-ui.badge>
                    </div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Department: Acquisitions & Supplier Operations</p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">Member since April 2026 &bull; ID: OB-90812</p>
                </div>
            </div>
        </x-ui.card>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Navigation or Description -->
            <div class="space-y-1">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Personal Information</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Update your work credentials, email, and display info.</p>
            </div>

            <!-- Profile Info Form -->
            <div class="md:col-span-2">
                <x-ui.card>
                    <form action="#" method="POST" class="space-y-4" onsubmit="event.preventDefault(); alert('Boilerplate: Profile saved!');">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-ui.input id="full_name" type="text" label="Full Name" value="Ammar Yasir" required />
                            <x-ui.input id="work_email" type="email" label="Work Email Address" value="ammar@company.com" required />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-ui.input id="phone_no" type="text" label="Phone Number" value="+60 12-345 6789" />
                            <div class="space-y-1.5">
                                <x-ui.label for="user_role">Portal Role</x-ui.label>
                                <input id="user_role" type="text" disabled value="Procurement Staff / Admin" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 py-2.5 px-3.5 text-sm focus:outline-none cursor-not-allowed">
                            </div>
                        </div>
                        <x-slot:footer>
                            <x-ui.button type="submit">Save Changes</x-ui.button>
                        </x-slot:footer>
                    </x-ui.card>
                </div>
            </div>

            <hr class="border-zinc-200 dark:border-zinc-805">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Description -->
                <div class="space-y-1">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Security</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Maintain your account security by updating passwords regularly.</p>
                </div>

                <!-- Password Update Form -->
                <div class="md:col-span-2">
                    <x-ui.card>
                        <form action="#" method="POST" class="space-y-4" onsubmit="event.preventDefault(); alert('Boilerplate: Password updated!');">
                            <x-ui.input id="current_password" type="password" label="Current Password" placeholder="••••••••" required />
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-ui.input id="new_password" type="password" label="New Password" placeholder="••••••••" required />
                                <x-ui.input id="new_password_confirm" type="password" label="Confirm New Password" placeholder="••••••••" required />
                            </div>
                            <x-slot:footer>
                                <x-ui.button type="submit" variant="primary">Update Password</x-ui.button>
                            </x-slot:footer>
                        </form>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
