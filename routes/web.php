<?php

use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Route;

Route::prefix('test')->name('test.')->middleware('auth')->group(function () {

    Route::get('/notification/hello-world', function () {
        $user = auth()->user();

        $systemNotification = new SystemNotification('Hello World', 'This is a test notification.');

        $user->notify($systemNotification);

        return 'Notification sent!';
    })->name('notification.hello-world');

});

Route::middleware('guest')->group(function () {
    Route::view('/', 'landing-page')->name('home');
    Route::view('/login', 'auth.login')->name('login');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/acquisition', 'acquisition')->name('acquisition');
    Route::view('/notifications', 'notifications')->name('notifications');
    Route::view('/verify-email', 'auth.verify-email')->name('verify-email');
    Route::view('/link-account', 'auth.link-account')->name('accounts.link');
    Route::get('/profile', function () {
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        return view('profile', compact('user'));
    })->name('profile');

    // System Management Routes
    Route::view('/features', 'features')->name('features.index');
    Route::view('/suppliers', 'suppliers')->name('suppliers.index');
    Route::view('/agencies', 'agencies')->name('agencies.index');
    Route::view('/subagencies', 'subagencies')->name('subagencies.index');
    Route::view('/agency-officers', 'agency-officers')->name('agency-officers.index');
    Route::view('/committees', 'committees')->name('committees.index');
    Route::view('/mof-categories', 'mof-categories')->name('mof-categories.index');
    Route::view('/mof-subcategories', 'mof-subcategories')->name('mof-subcategories.index');
    Route::view('/mof-codes', 'mof-codes')->name('mof-codes.index');
    Route::view('/states', 'states')->name('states.index');
    Route::view('/vot-types', 'vot-types')->name('vot-types.index');
});

Route::view('/register', 'auth.register')->name('register');
Route::view('/forgot-password', 'auth.forgot-password')->name('forgot-password');
Route::view('/403', 'errors.403')->name('403');
Route::view('/404', 'errors.404')->name('404');
Route::view('/agency', 'agencies')->name('agency');
Route::view('/portal', 'portal')->name('portal');
