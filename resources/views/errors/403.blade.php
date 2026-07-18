<x-layouts.guest>
    <x-slot:title>Access Denied - 403</x-slot:title>

    <x-ui.card>
        <div class="text-center py-6">
            <!-- 403 Visual element -->
            <div class="relative w-32 h-32 mx-auto mb-6 flex items-center justify-center">
                <div class="absolute inset-0 bg-amber-50 dark:bg-amber-950/20 rounded-full animate-pulse"></div>
                <span class="text-5xl font-extrabold text-amber-500 relative z-10">403</span>
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Access Denied</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 max-w-sm mx-auto">
                You do not have the required permissions to access this administrative section. Only Staff or Administrators can manage acquisitions.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="/dashboard">
                    <x-ui.button variant="primary" class="w-full sm:w-auto">
                        Return to Dashboard
                    </x-ui.button>
                </a>
                <button onclick="history.back()" type="button">
                    <x-ui.button variant="outline" class="w-full sm:w-auto">
                        Go Back
                    </x-ui.button>
                </button>
            </div>
        </div>
    </x-ui.card>
</x-layouts.guest>
