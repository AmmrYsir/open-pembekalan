<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'openPembekalan' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    
    <!-- Alpine & Livewire Styles/Scripts -->
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 transition-colors duration-200 antialiased font-sans flex flex-col justify-between overflow-x-hidden"
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
    
    <!-- Theme Toggle at top right -->
    <div class="fixed top-6 right-6 z-50">
        <x-theme-toggle />
    </div>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col p-6 relative overflow-y-auto overflow-x-hidden">
        <!-- Ambient Background Gradients -->
        <div class="absolute inset-0 -z-10 pointer-events-none opacity-40 dark:opacity-20 transition-opacity duration-300">
            <div class="absolute top-[-20%] left-[-20%] w-[80%] h-[80%] rounded-full bg-emerald-300/30 dark:bg-emerald-800/30 blur-3xl"></div>
            <div class="absolute bottom-[-20%] right-[-20%] w-[80%] h-[80%] rounded-full bg-indigo-300/20 dark:bg-indigo-800/20 blur-3xl"></div>
        </div>

        <div class="{{ $containerClass ?? 'w-full max-w-md mx-auto my-auto' }}">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-zinc-400 dark:text-zinc-600">
        &copy; {{ date('Y') }} openPembekalan. All rights reserved.
    </footer>

    @livewireScripts
</body>
</html>
