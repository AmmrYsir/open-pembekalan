<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Livewire\Blaze\Blaze;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // Map layout components to resources/views/layouts folder
        Blade::component('layouts.app', 'layouts.app');
        Blade::component('layouts.guest', 'layouts.guest');

        if (class_exists(Blaze::class)) {
            Blaze::optimize()->in(resource_path('views/components'));
        }

        $this->registerRoleDirectives();
    }

    /**
     * Register Blade directives and Gate authorization hooks for roles.
     */
    protected function registerRoleDirectives(): void
    {
        Blade::if('hasRole', fn (mixed $roles): bool => auth()->check() && auth()->user()->hasRole($roles));
        Blade::if('role', fn (mixed $roles): bool => auth()->check() && auth()->user()->hasRole($roles));
        Blade::if('hasAnyRole', fn (mixed $roles): bool => auth()->check() && auth()->user()->hasAnyRole($roles));
        Blade::if('hasAllRoles', fn (mixed $roles): bool => auth()->check() && auth()->user()->hasAllRoles($roles));
        Blade::if('unlessRole', fn (mixed $roles): bool => auth()->check() && auth()->user()->unlessRole($roles));

        Gate::before(function ($user, string $ability) {
            if (str_starts_with($ability, 'role:')) {
                $role = substr($ability, 5);

                return $user->hasRole($role);
            }
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
