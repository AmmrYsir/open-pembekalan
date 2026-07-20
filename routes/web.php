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
    Route::get('/profile', function () {
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        return view('profile', compact('user'));
    })->name('profile');
});
Route::view('/register', 'auth.register')->name('register');
Route::view('/forgot-password', 'auth.forgot-password')->name('forgot-password');
Route::view('/verify-email', 'auth.verify-email')->name('verify-email');
Route::view('/403', 'errors.403')->name('403');
Route::view('/404', 'errors.404')->name('404');
Route::view('/agency', 'agency')->name('agency');
Route::view('/portal', 'portal')->name('portal');
