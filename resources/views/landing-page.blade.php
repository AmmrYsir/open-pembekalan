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
    <header class="sticky top-0 z-40 w-full border-b border-zinc-200/80 dark:border-zinc-800/80 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md px-6 lg:px-6 py-42 lg:py-3 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-xl bg-emerald-600 dark:bg-emerald-500 flex items-center justify-center text-white font-bold shadow-lg shadow-emerald-500/20">O</span>
            <span class="font-bold text-zinc-900 dark:text-zinc-100 tracking-tight text-lg">openPembekalan</span>
        </div>

        <div class="flex items-center gap-4">
            <x-ui.theme-toggle />
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
                    <a href="{{ route('portal') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-500 active:bg-emerald-700 transition-all duration-200 shadow-md shadow-emerald-600/10 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50" aria-label="View public tender portal">
                        Access Portal
                        <x-heroicon-o-arrow-right class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true" />
                    </a>
                    <a href="/register" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-md text-zinc-700 dark:text-zinc-300 font-bold hover:bg-zinc-50 dark:hover:bg-zinc-850 hover:text-zinc-950 dark:hover:text-white transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50" aria-label="Join as a supplier or staff">
                        Register as Supplier
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
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section (Reusable Components) -->
        <section class="w-full py-12" aria-label="Key Performance Indicators">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
                <x-ui.stat-card value="RM 142.8M" label="Total Volume Audited">
                    <x-slot:icon>
                        <x-heroicon-o-currency-dollar class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </x-slot:icon>
                </x-ui.stat-card>
                
                <x-ui.stat-card value="16" label="Connected Partner Agencies">
                    <x-slot:icon>
                        <x-heroicon-o-building-office class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </x-slot:icon>
                </x-ui.stat-card>
                
                <x-ui.stat-card value="1,420+" label="Onboarded Active Suppliers">
                    <x-slot:icon>
                        <x-heroicon-o-users class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </x-slot:icon>
                </x-ui.stat-card>
                
                <x-ui.stat-card value="100%" label="Compliant Tax & SSM Audits">
                    <x-slot:icon>
                        <x-heroicon-o-shield-check class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
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
                        <x-heroicon-o-user-plus class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </x-slot:icon>
                </x-ui.feature-card>
                
                <x-ui.feature-card 
                    title="Transparent Audits" 
                    description="Clear tracking of department allocations, budget methods, and active supply requests with focal point details.">
                    <x-slot:icon>
                        <x-heroicon-o-clipboard class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </x-slot:icon>
                </x-ui.feature-card>

                <x-ui.feature-card 
                    title="Focal Coordination" 
                    description="Connect directly with verified focal officers. Complete list of partner procurement networks across divisions.">
                    <x-slot:icon>
                        <x-heroicon-o-chat-bubble-left-right class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </x-slot:icon>
                </x-ui.feature-card>

                <x-ui.feature-card 
                    title="Compliance Engine" 
                    description="Automated tracking of tax references, registration numbers, CIDB certifications, and MOF statuses.">
                    <x-slot:icon>
                        <x-heroicon-o-shield-check class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </x-slot:icon>
                </x-ui.feature-card>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-zinc-200/80 dark:border-zinc-800/80 bg-white dark:bg-zinc-900 px-6 py-8 flex flex-col sm:flex-row items-center justify-end gap-4 text-xs text-zinc-500 dark:text-zinc-400">
        <div>
            &copy; {{ date('Y') }} openPembekalan Portal. All rights reserved.
        </div>
    </footer>

    @livewireScripts
</body>
</html>
