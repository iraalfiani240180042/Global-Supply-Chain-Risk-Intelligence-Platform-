<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\AnalysisArticleController;

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

    // Store Country
    Route::post('/countries-master', [CountryController::class, 'store'])
        ->name('countries.store');

    // Edit Country
    Route::get('/countries-master/{country}/edit', [CountryController::class, 'edit'])
        ->name('countries.edit');

    // Update Country
    Route::put('/countries-master/{country}', [CountryController::class, 'update'])
        ->name('countries.update');

    // Delete Country
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

    Route::get('/currency', [CurrencyController::class, 'index'])
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

    // Rute kustom untuk port (Harus diletakkan DI ATAS Route::resource)
    Route::get('/ports/sync', [PortController::class, 'sync'])
        ->name('ports.sync');

    Route::get('/ports/country/{country}', [PortController::class, 'getPortsByCountry'])
        ->name('ports.by-country');

    Route::get('/ports/detail/{port}', [PortController::class, 'getPortDetail'])
        ->name('ports.detail');

    // Resource Controller Ports
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

Route::get('/comparison', [ComparisonController::class, 'index'])
    ->name('comparison.index');

// AJAX Compare
Route::get('/comparison/data/{countryA}/{countryB}', [ComparisonController::class, 'compare'])
    ->name('comparison.compare');


Route::resource('articles', AnalysisArticleController::class);
    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::view('/profile', 'profile.index')
        ->name('profile');
});