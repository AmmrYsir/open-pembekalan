<?php

namespace App\Providers;

use App\Models\EmailLog;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Support\FeatureRegistry;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Mail\Events\MessageFailed;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Pennant\Feature;
use Livewire\Blaze\Blaze;
use Symfony\Component\Mime\Email;

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
        $this->registerFeatureDefinitions();

        Event::listen(Verified::class, function (Verified $event): void {
            /** @var User $user */
            $user = $event->user;
            $user->notify(new SystemNotification(
                title: 'Email Verified',
                message: 'Your email address has been verified successfully.',
                action_url: route('dashboard'),
                icon: 'check-circle'
            ));
        });

        Event::listen(PasswordReset::class, function (PasswordReset $event): void {
            /** @var User $user */
            $user = $event->user;
            $user->notify(new SystemNotification(
                title: 'Password Reset Successful',
                message: 'Your account password was updated successfully.',
                action_url: route('login'),
                icon: 'key'
            ));
        });

        Event::listen(MessageSent::class, function (MessageSent $event): void {
            /** @var Email $message */
            $message = $event->message;

            $recipients = array_map(fn ($address) => $address->getAddress(), $message->getTo());
            $recipientNames = array_map(fn ($address) => $address->getName(), $message->getTo());

            $recipientEmail = implode(', ', $recipients);
            $recipientName = implode(', ', array_filter($recipientNames)) ?: null;

            EmailLog::create([
                'recipient_email' => $recipientEmail,
                'recipient_name' => $recipientName,
                'subject' => $message->getSubject() ?? '(No Subject)',
                'mailable_class' => $event->data['__laravel_notification'] ?? null,
                'status' => 'sent',
                'body_html' => $message->getHtmlBody(),
                'body_text' => $message->getTextBody(),
                'sent_at' => now(),
            ]);
        });

        Event::listen(MessageFailed::class, function (MessageFailed $event): void {
            /** @var Email $message */
            $message = $event->message;

            $recipients = array_map(fn ($address) => $address->getAddress(), $message->getTo());
            $recipientNames = array_map(fn ($address) => $address->getName(), $message->getTo());

            EmailLog::create([
                'recipient_email' => implode(', ', $recipients),
                'recipient_name' => implode(', ', array_filter($recipientNames)) ?: null,
                'subject' => $message->getSubject() ?? '(No Subject)',
                'mailable_class' => $event->data['__laravel_notification'] ?? null,
                'status' => 'failed',
                'body_html' => $message->getHtmlBody(),
                'body_text' => $message->getTextBody(),
                'error_message' => 'Message sending failed.',
            ]);
        });
    }

    /**
     * Register Pennant feature definitions and scope checks.
     */
    protected function registerFeatureDefinitions(): void
    {
        Feature::define('experimental-features', fn (?User $user = null): bool => (bool) $user?->is_experimental_user);
        Feature::define('linked-accounts', fn (?User $user = null): bool => (bool) $user?->is_experimental_user);

        foreach (FeatureRegistry::all() as $feature) {
            if (in_array($feature['key'], ['experimental-features', 'linked-accounts'])) {
                continue;
            }

            Feature::define($feature['key'], fn (): bool => $feature['default_active']);
        }
    }

    /**
     * Register Blade directives and Gate authorization hooks for roles.
     */
    protected function registerRoleDirectives(): void
    {
        Blade::if('hasrole', fn (mixed $roles): bool => auth()->check() && auth()->user()->hasRole($roles));
        Blade::if('role', fn (mixed $roles): bool => auth()->check() && auth()->user()->hasRole($roles));
        Blade::if('hasanyrole', fn (mixed $roles): bool => auth()->check() && auth()->user()->hasAnyRole($roles));
        Blade::if('hasallroles', fn (mixed $roles): bool => auth()->check() && auth()->user()->hasAllRoles($roles));
        Blade::if('unlessrole', fn (mixed $roles): bool => auth()->check() && auth()->user()->unlessRole($roles));

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
