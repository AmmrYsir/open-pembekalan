<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/forgot-password', 'auth.forgot-password')->name('forgot-password');
Route::view('/verify-email', 'auth.verify-email')->name('verify-email');
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/profile', 'profile')->name('profile');
Route::view('/403', 'errors.403')->name('403');
Route::view('/404', 'errors.404')->name('404');
Route::view('/agency', 'agency')->name('agency');
