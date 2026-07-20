<button
    x-data="{ 
        darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    }"
    x-init="
        $watch('darkMode', val => {
            if (val) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        });
        
        // Initial setup
        if (darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    "
    @click="darkMode = !darkMode; window.dispatchEvent(new CustomEvent('theme-changed', { detail: darkMode }))"
    @theme-changed.window="darkMode = $event.detail"
    type="button"
    class="relative p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 focus:outline-none rounded-xl bg-zinc-100/50 hover:bg-zinc-100 dark:bg-zinc-800/50 dark:hover:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/80 transition-all duration-200 flex items-center justify-center w-10 h-10 shadow-sm"
    aria-label="Toggle dark mode"
>
    <!-- Sun icon -->
    <x-heroicon-o-sun x-show="!darkMode" class="w-5 h-5 text-amber-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
    <!-- Moon icon -->
    <x-heroicon-o-moon x-show="darkMode" class="w-5 h-5 text-indigo-400 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;" />
</button>
