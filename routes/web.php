<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestNotificationController;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

Route::prefix('test')->name('test.')->middleware('auth')->group(function () {
    Route::get('/notification/hello-world', TestNotificationController::class)->name('notification.hello-world');
});

Route::middleware('guest')->group(function () {
    Route::view('/', 'pages.landing-page')->name('home');
    Route::view('/login', 'auth.login')->name('login');

    // Password Reset Routes
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// Auth Routes (Email Verification & Logout)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Authenticated & Verified Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');
    Route::view('/acquisition', 'pages.acquisition')->name('acquisition');
    Route::view('/notifications', 'pages.notifications')->middleware(EnsureFeaturesAreActive::using('system-notifications'))->name('notifications');
    Route::view('/link-account', 'auth.link-account')->name('accounts.link');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

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
