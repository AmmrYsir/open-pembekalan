<x-layouts.guest>
    <x-slot:title>Page Not Found - 404</x-slot:title>

    <x-card>
        <div class="text-center py-6">
            <!-- 404 Visual element -->
            <div class="relative w-32 h-32 mx-auto mb-6 flex items-center justify-center">
                <div class="absolute inset-0 bg-rose-50 dark:bg-rose-950/20 rounded-full animate-pulse"></div>
                <span class="text-5xl font-extrabold text-rose-500 relative z-10">404</span>
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Project Not Found</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 max-w-sm mx-auto">
                The resource or procurement details page you are trying to access does not exist, or has been permanently archived.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="/dashboard">
                    <x-button variant="primary" class="w-full sm:w-auto">
                        Return to Dashboard
                    </x-button>
                </a>
                <button onclick="history.back()" type="button">
                    <x-button variant="outline" class="w-full sm:w-auto">
                        Go Back
                    </x-button>
                </button>
            </div>
        </div>
    </x-card>
</x-layouts.guest>
