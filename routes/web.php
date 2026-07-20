<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\AnalysisArticleController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RiskPredictionController;
/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/analysis/create', function () {
    return 'CREATE BERHASIL';
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

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'index')->name('register');
    Route::post('/register', 'store')->name('register.store');
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
    | Countries
    |--------------------------------------------------------------------------
    */

    Route::get('/countries', [CountryController::class, 'insights'])
        ->name('countries.index');

    Route::get('/countries/{country}', [CountryController::class, 'show'])
        ->name('countries.show');

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

    /*
    |--------------------------------------------------------------------------
    | Ports
    |--------------------------------------------------------------------------
    */

    Route::resource('ports', PortController::class)->only(['index']);

    Route::get('/ports/country/{country}', [PortController::class, 'getPortsByCountry'])
        ->name('ports.by-country');

    Route::get('/ports/detail/{port}', [PortController::class, 'getPortDetail'])
        ->name('ports.detail');

   /*
|--------------------------------------------------------------------------
| Analytics
|--------------------------------------------------------------------------
*/

Route::get('/analytics/risk-prediction', 
[RiskPredictionController::class,'index'])
->name('risk.index');


Route::get('/risk-prediction/{country}', 
[RiskPredictionController::class,'calculate'])
->name('risk.calculate');
    
    /*
    |--------------------------------------------------------------------------
    | Comparison
    |--------------------------------------------------------------------------
    */

    Route::get('/comparison', [ComparisonController::class, 'index'])
        ->name('comparison.index');

    Route::get('/comparison/data/{countryA}/{countryB}', [ComparisonController::class, 'compare'])
        ->name('comparison.compare');

    /*
    |--------------------------------------------------------------------------
    | Analysis Articles (Public Login)
    |--------------------------------------------------------------------------
    */

    Route::get('/analysis', [AnalysisArticleController::class, 'index'])
        ->name('articles.index');

    Route::get('/analysis/{article}', [AnalysisArticleController::class, 'show'])
        ->name('articles.show');

    /*
    |--------------------------------------------------------------------------
    | Favorites
    |--------------------------------------------------------------------------
    */

    Route::get('/favorites', [FavoriteController::class, 'index'])
        ->name('favorites.index');

    Route::post('/favorites/{country}', [FavoriteController::class, 'toggle'])
        ->name('favorites.toggle');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::view('/profile', 'profile.index')
        ->name('profile');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        Route::resource('users', UserController::class)->except(['show']);

        /*
        |--------------------------------------------------------------------------
        | Country Master
        |--------------------------------------------------------------------------
        */

        Route::get('/countries/sync', [CountryController::class, 'sync'])
            ->name('countries.sync');

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
        | News Sync
        |--------------------------------------------------------------------------
        */

        Route::get('/news/sync', [NewsController::class, 'sync'])
            ->name('news.sync');

        /*
        |--------------------------------------------------------------------------
        | Ports Management
        |--------------------------------------------------------------------------
        */

        Route::get('/ports/sync', [PortController::class, 'sync'])
            ->name('ports.sync');

        Route::get('/ports/create', [PortController::class, 'create'])
            ->name('ports.create');

        Route::post('/ports', [PortController::class, 'store'])
            ->name('ports.store');

        Route::get('/ports/{port}/edit', [PortController::class, 'edit'])
            ->name('ports.edit');

        Route::put('/ports/{port}', [PortController::class, 'update'])
            ->name('ports.update');

        Route::delete('/ports/{port}', [PortController::class, 'destroy'])
            ->name('ports.destroy');

        /*
        |--------------------------------------------------------------------------
        | Analysis Articles Management
        |--------------------------------------------------------------------------
        */

        Route::get('/analysis/create', [AnalysisArticleController::class, 'create'])
            ->name('articles.create');

        Route::post('/analysis', [AnalysisArticleController::class, 'store'])
            ->name('articles.store');

        Route::get('/analysis/{article}/edit', [AnalysisArticleController::class, 'edit'])
            ->name('articles.edit');

        Route::put('/analysis/{article}', [AnalysisArticleController::class, 'update'])
            ->name('articles.update');

        Route::delete('/analysis/{article}', [AnalysisArticleController::class, 'destroy'])
            ->name('articles.destroy');

    });

});