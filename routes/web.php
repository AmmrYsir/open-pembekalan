<?php

use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Route;
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
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');
    Route::view('/acquisition', 'pages.acquisition')->name('acquisition');
    Route::view('/notifications', 'pages.notifications')->middleware(EnsureFeaturesAreActive::using('system-notifications'))->name('notifications');
    Route::view('/verify-email', 'auth.verify-email')->name('verify-email');
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
Route::view('/forgot-password', 'auth.forgot-password')->name('forgot-password');
Route::view('/403', 'errors.403')->name('403');
Route::view('/404', 'errors.404')->name('404');
Route::view('/agency', 'pages.agency')->name('agency');
Route::view('/portal', 'pages.portal')->name('portal');
