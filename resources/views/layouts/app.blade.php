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

    <div class="min-h-screen flex">
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
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-zinc-900 border-r border-zinc-200/80 dark:border-zinc-800/80 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:shrink-0">
            
            <div class="flex flex-col overflow-y-auto flex-1">
                <!-- Sidebar Header -->
                <div class="py-4 flex items-center px-6 border-b border-zinc-100 dark:border-zinc-800/50 justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-600 dark:bg-emerald-500 flex items-center justify-center text-white font-bold shadow-lg shadow-emerald-500/20">O</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 tracking-tight text-lg">openPembekalan</span>
                    </a>
                    <button @click="sidebarOpen = false" class="cursor-pointer lg:hidden p-1.5 rounded-lg text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <!-- Navigation Section -->
                <nav class="flex-1 px-4 py-6 space-y-6">
                    <div class="space-y-1">
                        <x-label class="px-2 mb-2 text-[10px] text-zinc-400 dark:text-zinc-500">Core</x-label>
                        
                        <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('dashboard') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-squares-2x2 class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Dashboard
                        </a>
                    </div>

                    @hasanyrole(['superadmin','officer'])
                    <div class="space-y-1">
                        <x-label class="px-2 mb-2 text-[10px] text-zinc-400 dark:text-zinc-500">Procurement</x-label>
                        
                        <a href="{{ route('acquisition') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('acquisition') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-document-chart-bar class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Acquisitions
                            <x-badge variant="warning" pill class="ml-auto">3 Active</x-badge>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <x-label class="px-2 mb-2 text-[10px] text-zinc-400 dark:text-zinc-500">Reporting</x-label>
                        
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::is('report') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-squares-2x2 class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            In Progress
                        </a>
                    </div>
                    @endhasanyrole

                    @role('superadmin')
                    <div class="space-y-1">
                        <x-label class="px-2 mb-2 text-[10px] text-zinc-400 dark:text-zinc-500">System Management</x-label>

                        <a href="{{ route('admin.features.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.features.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-adjustments-horizontal class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Feature Management
                        </a>

                        <a href="{{ route('admin.queues.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.queues.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-cpu-chip class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Queue Management
                        </a>

                        <a href="{{ route('admin.email-tracker.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.email-tracker.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-envelope-open class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Email Tracker
                        </a>

                        <a href="{{ route('admin.suppliers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.suppliers.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-building-storefront class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Suppliers
                        </a>

                        <a href="{{ route('admin.agencies.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.agencies.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-building-office-2 class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Agencies
                        </a>

                        <a href="{{ route('admin.subagencies.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.subagencies.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-building-office class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Subagencies
                        </a>

                        <a href="{{ route('admin.agency-officers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.agency-officers.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-user-group class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Agency Officers
                        </a>

                        <a href="{{ route('admin.committees.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.committees.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-academic-cap class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Committees
                        </a>

                        <a href="{{ route('admin.mof-categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.mof-categories.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-folder class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            MOF Categories
                        </a>

                        <a href="{{ route('admin.mof-subcategories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.mof-subcategories.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-folder-open class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            MOF Subcategories
                        </a>

                        <a href="{{ route('admin.mof-codes.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.mof-codes.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-tag class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            MOF Codes
                        </a>

                        <a href="{{ route('admin.states.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.states.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-map-pin class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            States
                        </a>

                        <a href="{{ route('admin.vot-types.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ Request::routeIs('admin.vot-types.index') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                            <x-heroicon-o-banknotes class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Vot Types
                        </a>
                    </div>
                    @endrole
                </nav>
            </div>

            <!-- Sidebar Footer User Card / Experimental Account Switcher -->
            @feature('linked-accounts')
                @livewire('layout.account-switcher')
            @else
                @livewire('layout.sidebar-footer-user-card')
            @endfeature
        </aside>

        <!-- Main Body Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">
            <!-- Header Component -->
            <header class="h-16 border-b border-zinc-200/80 dark:border-zinc-800/80 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md px-6 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="cursor-pointer lg:hidden p-1.5 rounded-lg text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800">
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
                    <!-- Notifications Bell Component -->
                    @feature('system-notifications')
						@livewire('notification.bell')
					@endfeature

                    <!-- Theme Toggle -->
                    <x-theme-toggle />
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-6 md:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewire('acquisition.drawer')
    @livewire('supplier.drawer')
    @livewire('agency.drawer')
    @livewire('subagency.drawer')
    @livewire('agency-officer.drawer')
    @livewire('committee.drawer')
    @livewire('mof-category.drawer')
    @livewire('mof-subcategory.drawer')
    @livewire('mof-code.drawer')
    @livewire('state.drawer')
    @livewire('vot-type.drawer')

    @livewireScripts
</body>
</html>
