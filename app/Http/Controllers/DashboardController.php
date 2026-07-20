<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Port;
use App\Models\Country;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // Total User
        $totalUsers = User::count();

        // Total Active Ports
        $activePorts = Port::with('status')->count();

        // Total Countries
        $totalCountries = Country::count();

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

        return view('dashboard.index', compact(
            'weather',
            'totalUsers',
            'activePorts',
            'totalCountries'
        ));
    }
}