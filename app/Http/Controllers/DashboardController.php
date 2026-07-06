<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data cuaca Jakarta
        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m'
        ]);

        $weather = $response->json();

        return view('dashboard.index', compact('weather'));
    }
}