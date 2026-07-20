<x-layouts.app>
    <x-slot:title>My Profile</x-slot:title>

    <div class="space-y-8 max-w-4xl">
        <!-- Profile Banner Card -->
        <x-ui.card>
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                <!-- Avatar Setup -->
                @livewire('user.profile-avatar', ['user' => $user])

                <div class="flex-1 text-center md:text-left space-y-2">
                    <div class="flex flex-col md:flex-row md:items-center gap-2 flex-wrap justify-center md:justify-start">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</h2>

                        @if($user->username)
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">@ {{ $user->username }}</span>
                        @endif

                        @php
                            $roles = $user->roles ?? collect();
                        @endphp

                        @if($roles->isNotEmpty())
                            <div class="inline-flex items-center gap-1.5 flex-wrap">
                                @foreach($roles as $role)
                                    <x-ui.badge variant="primary" pill>
                                        {{ $role->name }}
                                    </x-ui.badge>
                                @endforeach
                            </div>
                        @else
                            <x-ui.badge variant="primary" pill>
                                Procurement Officer
                            </x-ui.badge>
                        @endif
                    </div>

                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Department: Acquisitions & Supplier Operations</p>

                    <div class="flex items-center gap-3 text-xs text-zinc-400 dark:text-zinc-500 flex-wrap justify-center md:justify-start">
                        <span>Member since {{ $user->created_at?->diffForHumans() }}</span>
                        <span>&bull;</span>
                        <span x-data="{ copied: false }" class="inline-flex items-center gap-1 cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors" @click="navigator.clipboard.writeText('{{ $user->uuid }}'); copied = true; setTimeout(() => copied = false, 2000)">
                            <span>ID: {{ Str::limit($user->uuid, 12) }}</span>
                            <svg x-show="!copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <svg x-show="copied" x-cloak class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span x-show="copied" x-cloak class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">Copied!</span>
                        </span>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <!-- Personal Information Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-1">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Personal Information</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Update your work credentials, email, and display info.</p>
            </div>

            @livewire('user.profile-info', ['user' => $user])
        </div>

        <hr class="border-zinc-200 dark:border-zinc-800">

        <!-- Security Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-1">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Security</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Maintain your account security by updating passwords regularly.</p>
            </div>

            @livewire('user.update-password', ['user' => $user])
        </div>
    </div>
</x-layouts.app>
