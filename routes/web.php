<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'authenticate')->name('login.process');
    Route::post('/logout', 'logout')->name('logout');
});

// Protected Routes
Route::middleware('auth')->group(function () {

   Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

    Route::view('/countries', 'countries.index')->name('countries');

    Route::view('/weather', 'weather.index')->name('weather');

    Route::view('/currency', 'currency.index')->name('currency');

    Route::view('/news', 'news.index')->name('news');

    Route::view('/ports', 'ports.index')->name('ports');

    Route::view('/analytics', 'analytics.index')->name('analytics');

    Route::view('/comparison', 'comparison.index')->name('comparison');

    Route::view('/profile', 'profile.index')->name('profile');

    // API Weather
    Route::get('/weather-data', [WeatherController::class, 'index'])
        ->name('weather.data');
});