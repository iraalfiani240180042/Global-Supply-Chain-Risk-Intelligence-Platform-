<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index()
    {
        $response = Http::get(
            'https://api.open-meteo.com/v1/forecast',
            [
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m'
            ]
        );

        $weather = $response->json();

        return response()->json($weather);
    }
}