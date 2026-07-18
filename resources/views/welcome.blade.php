<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>openPembekalan Launchpad</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 transition-colors duration-200 antialiased font-sans flex flex-col justify-between"
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
      x-init="
          if (darkMode) {
              document.documentElement.classList.add('dark');
          } else {
              document.documentElement.classList.remove('dark');
          }
      ">
    
    <div class="fixed top-6 right-6 z-50">
        <x-ui.theme-toggle />
    </div>

    <main class="flex-1 flex flex-col justify-center items-center p-6 relative overflow-hidden py-16">
        <div class="absolute inset-0 -z-10 pointer-events-none opacity-40 dark:opacity-20 transition-opacity duration-300">
            <div class="absolute top-[-20%] left-[-20%] w-[80%] h-[80%] rounded-full bg-emerald-300/30 dark:bg-emerald-800/30 blur-3xl"></div>
            <div class="absolute bottom-[-20%] right-[-20%] w-[80%] h-[80%] rounded-full bg-indigo-300/20 dark:bg-indigo-800/20 blur-3xl"></div>
        </div>

        <div class="w-full max-w-4xl space-y-10">
            <div class="text-center space-y-3">
                <div class="inline-flex items-center gap-2.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    UI/UX Boilerplate Complete
                </div>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight text-zinc-950 dark:text-white">openPembekalan Launchpad</h1>
                <p class="text-sm md:text-base text-zinc-500 dark:text-zinc-400 max-w-xl mx-auto">
                    A premium procurement and supply management system designed for Suppliers, Staff, and Administration. Preview all implemented interface views below.
                </p>
            </div>

            <!-- Page Selection Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Login Page -->
                <a href="/login" class="block group">
                    <x-ui.card hoverable class="h-full flex flex-col justify-between">
                        <div class="space-y-2">
                            <span class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 inline-block group-hover:bg-emerald-600 group-hover:text-white transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-zinc-950 dark:text-white mt-4">Sign In Screen</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Portal entry for Suppliers and Procurement Staff with clean inputs and remember states.</p>
                        </div>
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-6 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Open View &rarr;
                        </div>
                    </x-ui.card>
                </a>

                <!-- Register Page -->
                <a href="/register" class="block group">
                    <x-ui.card hoverable class="h-full flex flex-col justify-between">
                        <div class="space-y-2">
                            <span class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 inline-block group-hover:bg-emerald-600 group-hover:text-white transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-zinc-950 dark:text-white mt-4">Registration Screen</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Features an interactive segment role switcher showing custom Supplier/Staff fields dynamically.</p>
                        </div>
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-6 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Open View &rarr;
                        </div>
                    </x-ui.card>
                </a>

                <!-- Dashboard Page -->
                <a href="/dashboard" class="block group">
                    <x-ui.card hoverable class="h-full flex flex-col justify-between">
                        <div class="space-y-2">
                            <span class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 inline-block group-hover:bg-emerald-600 group-hover:text-white transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-zinc-950 dark:text-white mt-4">Core Dashboard</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Main admin portal containing summary widgets, SVG pipeline distribution, and ongoing project listings.</p>
                        </div>
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-6 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Open View &rarr;
                        </div>
                    </x-ui.card>
                </a>

                <!-- User Profile Page -->
                <a href="/profile" class="block group">
                    <x-ui.card hoverable class="h-full flex flex-col justify-between">
                        <div class="space-y-2">
                            <span class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 inline-block group-hover:bg-emerald-600 group-hover:text-white transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-zinc-950 dark:text-white mt-4">User Profile Settings</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Settings dashboard to configure personal information details and password credential updating.</p>
                        </div>
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-6 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Open View &rarr;
                        </div>
                    </x-ui.card>
                </a>

                <!-- Verify Email Page -->
                <a href="/verify-email" class="block group">
                    <x-ui.card hoverable class="h-full flex flex-col justify-between">
                        <div class="space-y-2">
                            <span class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 inline-block group-hover:bg-emerald-600 group-hover:text-white transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25-1.5a2 2 0 00-2.22 0l-2.25 1.5" />
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-zinc-950 dark:text-white mt-4">Email Verification</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Confirmation page with notification alerts indicating verification resend states.</p>
                        </div>
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-6 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Open View &rarr;
                        </div>
                    </x-ui.card>
                </a>

                <!-- Forgot Password Page -->
                <a href="/forgot-password" class="block group">
                    <x-ui.card hoverable class="h-full flex flex-col justify-between">
                        <div class="space-y-2">
                            <span class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 inline-block group-hover:bg-emerald-600 group-hover:text-white transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-zinc-950 dark:text-white mt-4">Forgot Password Screen</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Email password resetting form screen linking back into login view.</p>
                        </div>
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-6 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Open View &rarr;
                        </div>
                    </x-ui.card>
                </a>

                <!-- 404 Page -->
                <a href="/404" class="block group">
                    <x-ui.card hoverable class="h-full flex flex-col justify-between">
                        <div class="space-y-2">
                            <span class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 inline-block group-hover:bg-emerald-600 group-hover:text-white transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-zinc-950 dark:text-white mt-4">Error 404 Page</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Customized 404 page not found screen featuring a clean illustration and navigation back buttons.</p>
                        </div>
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-6 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Open View &rarr;
                        </div>
                    </x-ui.card>
                </a>

                <!-- 403 Page -->
                <a href="/403" class="block group">
                    <x-ui.card hoverable class="h-full flex flex-col justify-between">
                        <div class="space-y-2">
                            <span class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 inline-block group-hover:bg-emerald-600 group-hover:text-white transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-zinc-950 dark:text-white mt-4">Error 403 Page</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Customized 403 forbidden screen detailing role permission warnings for administration modules.</p>
                        </div>
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-6 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Open View &rarr;
                        </div>
                    </x-ui.card>
                </a>
            </div>
        </div>
    </main>

    <footer class="py-6 text-center text-xs text-zinc-400 dark:text-zinc-600">
        &copy; {{ date('Y') }} openPembekalan. All rights reserved.
    </footer>

    @livewireScripts
</body>
</html>
