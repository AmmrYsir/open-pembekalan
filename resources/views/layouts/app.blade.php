<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'openPembekalan Portal' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    
    <!-- Alpine & Livewire Styles/Scripts -->
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 transition-colors duration-200 antialiased font-sans"
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
          sidebarOpen: false
      }"
      x-init="
          if (darkMode) {
              document.documentElement.classList.add('dark');
          } else {
              document.documentElement.classList.remove('dark');
          }
      ">

    <div class="min-h-full flex">
        <!-- Sidebar Backdrop (Mobile Only) -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-zinc-950/40 backdrop-blur-xs lg:hidden"
             style="display: none;"></div>

        <!-- Sidebar Component -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-zinc-900 border-r border-zinc-200/80 dark:border-zinc-800/80 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:h-screen lg:shrink-0">
            
            <div class="flex flex-col overflow-y-auto flex-1">
                <!-- Sidebar Header -->
                <div class="h-16 flex items-center px-6 border-b border-zinc-100 dark:border-zinc-800/50 justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-600 dark:bg-emerald-500 flex items-center justify-center text-white font-bold shadow-lg shadow-emerald-500/20">O</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 tracking-tight text-lg">openPembekalan</span>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <!-- Navigation Section -->
                <nav class="flex-1 px-4 py-6 space-y-6">
                    <div class="space-y-1">
                        <x-ui.label class="px-2 mb-2 text-[10px] text-zinc-400 dark:text-zinc-500">Core</x-ui.label>
                        
                        <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('dashboard') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-squares-2x2 class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Dashboard
                        </a>
                    </div>

                    <div class="space-y-1">
                        <x-ui.label class="px-2 mb-2 text-[10px] text-zinc-400 dark:text-zinc-500">Procurement</x-ui.label>
                        
                        <a href="{{ route('acquisition') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('acquisition') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-document-chart-bar class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Acquisitions
                            <x-ui.badge variant="warning" pill class="ml-auto">3 Active</x-ui.badge>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <x-ui.label class="px-2 mb-2 text-[10px] text-zinc-400 dark:text-zinc-500">Reporting</x-ui.label>
                        
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('report') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-squares-2x2 class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            In Progress
                        </a>
                    </div>

                    <div class="space-y-1">
                        <x-ui.label class="px-2 mb-2 text-[10px] text-zinc-400 dark:text-zinc-500">System Management</x-ui.label>

                        <a href="/403" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200">
                            <x-heroicon-o-lock-closed class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            User Management
                        </a>

                    </div>
                </nav>
            </div>

            <!-- Sidebar Footer User Card -->
            @livewire('sidebar-footer-user-card')
        </aside>

        <!-- Main Body Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <!-- Header Component -->
            <header class="h-16 border-b border-zinc-200/80 dark:border-zinc-800/80 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md px-6 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-lg text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        <x-heroicon-o-bars-3 class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                    
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                        <span>Dashboard</span>
                        <x-heroicon-o-chevron-right class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        <span class="text-zinc-500 font-normal">{{ $title ?? 'Dashboard' }}</span>
                    </h2>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-4">
                    <!-- Notifications Mock -->
                    <button class="relative p-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-rose-500"></span>
                        <x-heroicon-o-bell class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>

                    <!-- Theme Toggle -->
                    <x-ui.theme-toggle />
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-6 md:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
