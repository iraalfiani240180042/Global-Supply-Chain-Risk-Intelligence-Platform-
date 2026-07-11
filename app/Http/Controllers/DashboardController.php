<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
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

            $weather = $response->json();

        } catch (\Exception $e) {

            $weather = [
                'current' => [
                    'temperature_2m' => '-',
                    'relative_humidity_2m' => '-',
                    'wind_speed_10m' => '-'
                ]
            ];
        }

        return view('dashboard.index', compact('weather'));
    }
}