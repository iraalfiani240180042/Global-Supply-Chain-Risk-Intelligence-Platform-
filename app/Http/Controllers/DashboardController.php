<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Port;
use App\Models\Country;
use App\Models\AnalysisArticle;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================
        // Dashboard Statistics
        // ==========================
        $totalUsers = User::count();
        $totalCountries = Country::count();
        $activePorts = Port::count();
        $totalArticles = AnalysisArticle::count();

        // ==========================
        // Latest Data
        // ==========================
        $latestArticles = AnalysisArticle::with('country')
            ->latest()
            ->take(5)
            ->get();

        $latestUsers = User::latest()
            ->take(5)
            ->get();

        // ==========================
        // Weather API
        // ==========================
        try {

            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => -6.2088,
                    'longitude' => 106.8456,
                    'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m'
                ]);

            $weather = $response->successful()
                ? $response->json()
                : [
                    'current' => [
                        'temperature_2m' => '-',
                        'relative_humidity_2m' => '-',
                        'wind_speed_10m' => '-'
                    ]
                ];

        } catch (\Exception $e) {

            $weather = [
                'current' => [
                    'temperature_2m' => '-',
                    'relative_humidity_2m' => '-',
                    'wind_speed_10m' => '-'
                ]
            ];

        }

        // ==========================
        // Return View
        // ==========================
        return view('dashboard.index', compact(
            'weather',
            'totalUsers',
            'totalCountries',
            'activePorts',
            'totalArticles',
            'latestArticles',
            'latestUsers'
        ));
    }
}