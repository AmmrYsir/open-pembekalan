<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>openPembekalan - Modern Procurement System</title>
    
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
    
    <!-- Decorative Blurry Backgrounds -->
    <div class="fixed inset-0 -z-10 pointer-events-none opacity-40 dark:opacity-20 transition-opacity duration-300">
        <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] rounded-full bg-emerald-300/20 dark:bg-emerald-800/20 blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] rounded-full bg-indigo-300/15 dark:bg-indigo-800/15 blur-3xl"></div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 w-full border-b border-zinc-200/80 dark:border-zinc-800/80 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md px-6 lg:px-12 py-4 lg:py-5 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-xl bg-emerald-600 dark:bg-emerald-500 flex items-center justify-center text-white font-bold shadow-lg shadow-emerald-500/20">O</span>
            <span class="font-bold text-zinc-900 dark:text-zinc-100 tracking-tight text-lg">openPembekalan</span>
        </div>
        
        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center gap-6" aria-label="Main Navigation">
            <a href="/agency" class="text-sm font-medium text-zinc-650 dark:text-zinc-350 hover:text-zinc-900 dark:hover:text-white transition-colors">Agencies</a>
            <a href="/dashboard" class="text-sm font-medium text-zinc-650 dark:text-zinc-350 hover:text-zinc-900 dark:hover:text-white transition-colors">Dashboard</a>
            <a href="/profile" class="text-sm font-medium text-zinc-650 dark:text-zinc-350 hover:text-zinc-900 dark:hover:text-white transition-colors">My Profile</a>
        </nav>

        <div class="flex items-center gap-4">
            <x-ui.theme-toggle />
            
            <a href="/login" class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition-colors hidden sm:block">Sign In</a>
            <a href="/register" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 rounded-xl transition-all duration-200 shadow-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/50">Register</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col items-center py-12 md:py-20 px-6 lg:px-12 relative max-w-7xl mx-auto w-full">
        
        <!-- Hero Section -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center w-full pb-16 border-b border-zinc-200/50 dark:border-zinc-800/50" aria-label="Introduction">
            <!-- Text Content -->
            <div class="lg:col-span-6 space-y-6 text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" aria-hidden="true"></span>
                    Modern Public Procurement
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-zinc-950 dark:text-white leading-tight">
                    Smart Supply <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-indigo-600 dark:from-emerald-400 dark:to-indigo-400">Acquisitions.</span>
                </h1>
                
                <p class="text-base text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-xl">
                    An enterprise-grade, transparent procurement and acquisition platform designed specifically for Suppliers, Government Agencies, and Procurement Officers. Simplify compliance, verify certificates, and streamline vendor onboarding effortlessly.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <a href="/login" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-500 active:bg-emerald-700 transition-all duration-200 shadow-md shadow-emerald-600/10 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50" aria-label="Sign in to the portal">
                        Access Portal
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14" />
                        </svg>
                    </a>
                    <a href="/register" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-md text-zinc-700 dark:text-zinc-300 font-bold hover:bg-zinc-50 dark:hover:bg-zinc-850 hover:text-zinc-950 dark:hover:text-white transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50" aria-label="Join as a supplier or staff">
                        Onboard Supplier
                    </a>
                </div>
            </div>
            
            <!-- Dashboard Preview Mockup -->
            <div class="lg:col-span-6 flex justify-center w-full">
                <div class="w-full max-w-xl rounded-2xl bg-zinc-900 border border-zinc-800 shadow-2xl p-1 relative overflow-hidden" aria-label="Platform Interface Preview">
                    <!-- Browser Window Header -->
                    <div class="h-10 flex items-center px-4 gap-1.5 border-b border-zinc-800/80 shrink-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80 inline-block"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80 inline-block"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80 inline-block"></span>
                        <div class="mx-auto bg-zinc-800/50 text-[10px] text-zinc-500 px-10 py-1 rounded-md max-w-xs truncate">portal.openpembekalan.com</div>
                    </div>
                    
                    <!-- Inside Mockup Dashboard -->
                    <div class="bg-zinc-950 p-4 h-64 md:h-80 overflow-y-auto flex flex-col justify-between">
                        <!-- Stats grid mockup -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-zinc-900/60 border border-zinc-850 rounded-xl p-3">
                                <span class="block text-[10px] text-zinc-500 font-medium">Acquisitions</span>
                                <span class="block text-sm font-bold text-zinc-100 mt-1">RM 8.4M</span>
                            </div>
                            <div class="bg-zinc-900/60 border border-zinc-850 rounded-xl p-3">
                                <span class="block text-[10px] text-zinc-500 font-medium">Active Partners</span>
                                <span class="block text-sm font-bold text-zinc-100 mt-1">16 Agencies</span>
                            </div>
                            <div class="bg-zinc-900/60 border border-zinc-850 rounded-xl p-3">
                                <span class="block text-[10px] text-zinc-500 font-medium">System Status</span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-450 mt-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                    Optimal
                                </span>
                            </div>
                        </div>

                        <!-- Active Projects Mock list -->
                        <div class="space-y-2 mt-4">
                            <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Acquisition Pipeline</span>
                            
                            <div class="flex items-center justify-between p-2.5 bg-zinc-900/40 rounded-xl border border-zinc-850">
                                <div>
                                    <span class="text-xs font-semibold text-zinc-100">National Solar Grid Supply</span>
                                    <span class="block text-[9px] text-zinc-500">Ministry of Communications • Renewable Energy</span>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 font-semibold border border-emerald-500/10">Active</span>
                            </div>

                            <div class="flex items-center justify-between p-2.5 bg-zinc-900/40 rounded-xl border border-zinc-850">
                                <div>
                                    <span class="text-xs font-semibold text-zinc-100">Fiber Optic Expansion Phase 3</span>
                                    <span class="block text-[9px] text-zinc-500">Ministry of Transport • Infrastructure</span>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-400 font-semibold border border-indigo-500/10">Auditing</span>
                            </div>
                        </div>

                        <!-- Mini footer inside mockup -->
                        <div class="flex justify-between items-center text-[9px] text-zinc-650 border-t border-zinc-900/80 pt-3 mt-4">
                            <span>Logged in as Ammar Yasir (Staff)</span>
                            <span>openPembekalan v1.0</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section (Reusable Components) -->
        <section class="w-full py-12" aria-label="Key Performance Indicators">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
                <x-ui.stat-card value="RM 142.8M" label="Total Volume Audited">
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.stat-card>
                
                <x-ui.stat-card value="16" label="Connected Partner Agencies">
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </x-slot:icon>
                </x-ui.stat-card>
                
                <x-ui.stat-card value="1,420+" label="Onboarded Active Suppliers">
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.stat-card>
                
                <x-ui.stat-card value="100%" label="Compliant Tax & SSM Audits">
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.stat-card>
            </div>
        </section>

        <!-- Selling Points Section (Reusable Components) -->
        <section class="w-full py-16 border-t border-zinc-200/50 dark:border-zinc-800/50" aria-labelledby="features-heading">
            <div class="text-center max-w-xl mx-auto space-y-3 mb-12">
                <h2 id="features-heading" class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-white">Why openPembekalan?</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    A secure ecosystem that connects suppliers with governmental agencies under a transparent, verifiable audit protocol.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
                <x-ui.feature-card 
                    title="Supplier Onboarding" 
                    description="Simple guided forms to submit company credentials, SSM information, and paid-up capital validation details.">
                    <x-slot:icon>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.feature-card>
                
                <x-ui.feature-card 
                    title="Transparent Audits" 
                    description="Clear tracking of department allocations, budget methods, and active supply requests with focal point details.">
                    <x-slot:icon>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </x-slot:icon>
                </x-ui.feature-card>

                <x-ui.feature-card 
                    title="Focal Coordination" 
                    description="Connect directly with verified focal officers. Complete list of partner procurement networks across divisions.">
                    <x-slot:icon>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.feature-card>

                <x-ui.feature-card 
                    title="Compliance Engine" 
                    description="Automated tracking of tax references, registration numbers, CIDB certifications, and MOF statuses.">
                    <x-slot:icon>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622" />
                        </svg>
                    </x-slot:icon>
                </x-ui.feature-card>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-zinc-200/80 dark:border-zinc-800/80 bg-white dark:bg-zinc-900 px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500 dark:text-zinc-400">
        <div>
            &copy; {{ date('Y') }} openPembekalan Portal. All rights reserved.
        </div>
        <div class="flex items-center gap-6" aria-label="Footer Links">
            <a href="/login" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Sign In</a>
            <a href="/register" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Onboard Supplier</a>
            <a href="/agency" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Agencies Directory</a>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
