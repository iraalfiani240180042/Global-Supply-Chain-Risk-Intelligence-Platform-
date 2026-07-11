<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index()
    {
        try {

            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => -6.2088,
                    'longitude' => 106.8456,
                    'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m'
                ]);

            return response()->json($response->json());

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to connect to Open-Meteo API.'
            ], 500);

        }
    }
}