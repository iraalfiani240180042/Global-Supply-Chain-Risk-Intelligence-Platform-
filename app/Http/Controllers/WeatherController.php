<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherLog;
use App\Models\WeatherType;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index()
    {
        $weatherLogs = WeatherLog::with(['country', 'weatherType'])
            ->latest('recorded_at')
            ->paginate(20);

        return view('weather.index', compact('weatherLogs'));
    }

    public function sync()
    {
        $countries = Country::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->limit(10)
            ->get();

        foreach ($countries as $country) {

            // Lewati jika koordinat kosong
            if (empty($country->latitude) || empty($country->longitude)) {
                continue;
            }

            try {

                $response = Http::timeout(60)
                    ->retry(2, 1000)
                    ->get(
                        'https://api.open-meteo.com/v1/forecast',
                        [
                            'latitude' => $country->latitude,
                            'longitude' => $country->longitude,
                            'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code'
                        ]
                    );

                if (!$response->successful()) {
                    continue;
                }

                $current = $response->json('current');

                $weatherName = $this->getWeatherName(
                    $current['weather_code'] ?? -1
                );

                $weatherType = WeatherType::firstOrCreate(
                    [
                        'name' => $weatherName
                    ],
                    [
                        'description' => $weatherName
                    ]
                );

                WeatherLog::create([
                    'country_id'      => $country->id,
                    'weather_type_id' => $weatherType->id,
                    'temperature'     => $current['temperature_2m'] ?? 0,
                    'humidity'        => $current['relative_humidity_2m'] ?? 0,
                    'wind_speed'      => $current['wind_speed_10m'] ?? 0,
                    'recorded_at'     => now(),
                ]);

            } catch (\Exception $e) {

                // Jika satu negara gagal, lanjut ke negara berikutnya
                continue;

            }
        }

        return redirect()
            ->route('weather')
            ->with('success', 'Weather synchronized successfully!');
    }

    private function getWeatherName($code)
    {
        return match (true) {

            $code == 0 => 'Clear Sky',

            in_array($code, [1, 2, 3]) => 'Cloudy',

            in_array($code, [45, 48]) => 'Fog',

            in_array($code, [51, 53, 55, 56, 57]) => 'Drizzle',

            in_array($code, [61, 63, 65, 66, 67]) => 'Rain',

            in_array($code, [71, 73, 75, 77]) => 'Snow',

            in_array($code, [80, 81, 82]) => 'Rain Shower',

            in_array($code, [85, 86]) => 'Snow Shower',

            in_array($code, [95, 96, 99]) => 'Thunderstorm',

            default => 'Unknown',
        };
    }
}