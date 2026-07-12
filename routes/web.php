<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\CountryController;

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

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Countries
    Route::get('/countries/sync', [CountryController::class, 'sync'])
        ->name('countries.sync');

    Route::resource('countries', CountryController::class);

    // Weather
    Route::get('/weather', [WeatherController::class, 'index'])
        ->name('weather');

    Route::get('/weather/sync', [WeatherController::class, 'sync'])
        ->name('weather.sync');

    // Currency
    Route::view('/currency', 'currency.index')->name('currency');

    // News
    Route::view('/news', 'news.index')->name('news');

    // Ports
    Route::view('/ports', 'ports.index')->name('ports');

    // Analytics
    Route::view('/analytics', 'analytics.index')->name('analytics');

    // Comparison
    Route::view('/comparison', 'comparison.index')->name('comparison');

    // Profile
    Route::view('/profile', 'profile.index')->name('profile');
});