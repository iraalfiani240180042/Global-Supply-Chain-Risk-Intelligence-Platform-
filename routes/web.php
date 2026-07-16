<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\CurrencyController;

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'authenticate')->name('login.process');
    Route::post('/logout', 'logout')->name('logout');
});

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Country Insights
    |--------------------------------------------------------------------------
    */

    // Halaman utama
    Route::get('/countries', [CountryController::class, 'insights'])
        ->name('countries.index');

    // WAJIB di atas show()
    Route::get('/countries/sync', [CountryController::class, 'sync'])
        ->name('countries.sync');

    // Detail country
    Route::get('/countries/{country}', [CountryController::class, 'show'])
        ->name('countries.show');

    // Master Country
    Route::get('/countries-master/create', [CountryController::class, 'create'])
        ->name('countries.create');

    Route::post('/countries-master', [CountryController::class, 'store'])
        ->name('countries.store');

    Route::get('/countries-master/{country}/edit', [CountryController::class, 'edit'])
        ->name('countries.edit');

    Route::put('/countries-master/{country}', [CountryController::class, 'update'])
        ->name('countries.update');

    Route::delete('/countries-master/{country}', [CountryController::class, 'destroy'])
        ->name('countries.destroy');

    /*
    |--------------------------------------------------------------------------
    | Weather
    |--------------------------------------------------------------------------
    */

    Route::get('/weather', [WeatherController::class, 'index'])
        ->name('weather');

    

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    Route::get('/currency',[CurrencyController::class,'index'])
    ->name('currency');

    /*
    |--------------------------------------------------------------------------
    | News
    |--------------------------------------------------------------------------
    */

    Route::get('/news', [NewsController::class, 'index'])
        ->name('news');

    Route::get('/news/sync', [NewsController::class, 'sync'])
        ->name('news.sync');

    /*
    |--------------------------------------------------------------------------
    | Ports
    |--------------------------------------------------------------------------
    */

    Route::get('/ports/sync', [PortController::class, 'sync'])
        ->name('ports.sync');

    Route::resource('ports', PortController::class);

    /*
    |--------------------------------------------------------------------------
    | Analytics
    |--------------------------------------------------------------------------
    */

    Route::view('/analytics', 'analytics.index')
        ->name('analytics');

    /*
    |--------------------------------------------------------------------------
    | Comparison
    |--------------------------------------------------------------------------
    */

    Route::view('/comparison', 'comparison.index')
        ->name('comparison');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::view('/profile', 'profile.index')
        ->name('profile');
});