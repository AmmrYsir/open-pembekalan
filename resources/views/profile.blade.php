<x-layouts.app>
    <x-slot:title>My Profile</x-slot:title>

    <div class="space-y-6 max-w-4xl">
        <!-- Profile Banner Card -->
        <x-ui.card>
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                <!-- Avatar Setup -->
                @livewire('user.profile-avatar')

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
            @livewire('user.profile-info')

            <hr class="border-zinc-200 dark:border-zinc-805">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Description -->
                <div class="space-y-1">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Security</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Maintain your account security by updating passwords regularly.</p>
                </div>

                <!-- Password Update Form -->
                @livewire('user.update-password')
            </div>
        </div>
    </div>
</x-layouts.app>
