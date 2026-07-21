<?php

use App\Notifications\SystemNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

Route::prefix('test')->name('test.')->middleware('auth')->group(function () {

    Route::get('/notification/hello-world', function () {
        $user = auth()->user();

        $systemNotification = new SystemNotification('Hello World', 'This is a test notification.');

        $user->notify($systemNotification);

        return 'Notification sent!';
    })->name('notification.hello-world');

});

Route::middleware('guest')->group(function () {
    Route::view('/', 'pages.landing-page')->name('home');
    Route::view('/login', 'auth.login')->name('login');

    // Password Reset Routes
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('/reset-password/{token}', function (Request $request, string $token) {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (object $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    })->name('password.update');
});

// Auth Routes (Email Verification & Logout)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('dashboard')->with('verified', true);
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

// Authenticated & Verified Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');
    Route::view('/acquisition', 'pages.acquisition')->name('acquisition');
    Route::view('/notifications', 'pages.notifications')->middleware(EnsureFeaturesAreActive::using('system-notifications'))->name('notifications');
    Route::view('/link-account', 'auth.link-account')->name('accounts.link');
    Route::get('/profile', function () {
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        return view('pages.profile', compact('user'));
    })->name('profile');

    // System Management Routes (Admin)
    Route::name('admin.')->group(function () {
        Route::view('/features', 'pages.admin.features')->name('features.index');
        Route::view('/queues', 'pages.admin.queues')->name('queues.index');
        Route::view('/email-tracker', 'pages.admin.email-tracker')->name('email-tracker.index');
        Route::view('/suppliers', 'pages.admin.suppliers')->name('suppliers.index');
        Route::view('/agencies', 'pages.admin.agencies')->name('agencies.index');
        Route::view('/subagencies', 'pages.admin.subagencies')->name('subagencies.index');
        Route::view('/agency-officers', 'pages.admin.agency-officers')->name('agency-officers.index');
        Route::view('/committees', 'pages.admin.committees')->name('committees.index');
        Route::view('/mof-categories', 'pages.admin.mof-categories')->name('mof-categories.index');
        Route::view('/mof-subcategories', 'pages.admin.mof-subcategories')->name('mof-subcategories.index');
        Route::view('/mof-codes', 'pages.admin.mof-codes')->name('mof-codes.index');
        Route::view('/states', 'pages.admin.states')->name('states.index');
        Route::view('/vot-types', 'pages.admin.vot-types')->name('vot-types.index');
    });
});

Route::view('/register', 'auth.register')->name('register');
Route::view('/403', 'errors.403')->name('403');
Route::view('/404', 'errors.404')->name('404');
Route::view('/agency', 'pages.agency')->name('agency');
Route::view('/portal', 'pages.portal')->name('portal');
