<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherLog;
use App\Models\WeatherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    /**
     * Weather Dashboard
     */
    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get();

        $country = null;
        $weather = null;
        $temperatureTrend = [];
        $logisticsStatus = null;
        $logisticsColor = 'secondary';
        $weatherRecommendations = [];

        if ($request->filled('country')) {
            $country = Country::find($request->country);

            if ($country) {
                // Mengambil data cuaca saat ini dan tren suhu sekaligus dari API Open-Meteo
                if ($country->latitude && $country->longitude) {
                    // 1. Perubahan parameter 'current' di index()
                    $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                        'latitude' => $country->latitude,
                        'longitude' => $country->longitude,
                        'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code,rain,precipitation',
                        'hourly' => 'temperature_2m',
                        'forecast_days' => 1,
                        'timezone' => 'auto',
                    ]);

                    if ($response->successful()) {
                        $result = $response->json();
                        

                        // 2. Menambahkan Rainfall dan Precipitation ke array $weather
                        $weather = [
                            'temperature' => $result['current']['temperature_2m'],
                            'humidity' => $result['current']['relative_humidity_2m'],
                            'wind_speed' => $result['current']['wind_speed_10m'],
                            'weather_code' => $result['current']['weather_code'],
                            'condition' => $this->getWeatherName($result['current']['weather_code']),
                            'rainfall' => $result['current']['rain'] ?? 0,
                            'precipitation' => $result['current']['precipitation'] ?? 0,
                        ];

                        // 3. Menambahkan Status Badai (Storm Status)
                        $weather['storm'] = false;
                        if (
                            $weather['weather_code'] >= 95 ||
                            $weather['wind_speed'] >= 50
                        ) {
                            $weather['storm'] = true;
                        }

                        // Memetakan tren suhu per jam
                        if (isset($result['hourly']['time'])) {
                            foreach ($result['hourly']['time'] as $i => $time) {
                                $temperatureTrend[] = [
                                    'time' => date('H:i', strtotime($time)),
                                    'temperature' => $result['hourly']['temperature_2m'][$i]
                                ];
                            }
                        }
                    }
                }

                // Penilaian Dampak Logistik & Rekomendasi
                if ($weather) {
                    if ($weather['condition'] == 'Thunderstorm' || $weather['wind_speed'] > 40) {
                        $logisticsStatus = 'High Risk';
                        $logisticsColor = 'danger';
                    } elseif (in_array($weather['condition'], ['Rain', 'Rain Shower', 'Drizzle'])) {
                        $logisticsStatus = 'Moderate';
                        $logisticsColor = 'warning';
                    } else {
                        $logisticsStatus = 'Good';
                        $logisticsColor = 'success';
                    }

                    // Pembuatan Rekomendasi berdasarkan status logistik
                    if ($logisticsStatus == 'Good') {
                        $weatherRecommendations[] = 'Weather conditions are favorable for logistics operations.';
                        $weatherRecommendations[] = 'Road transportation is expected to operate normally.';
                        $weatherRecommendations[] = 'Port activities are unlikely to experience delays.';
                        $weatherRecommendations[] = 'Air cargo transportation can operate as scheduled.';
                    }

                    if ($logisticsStatus == 'Moderate') {
                        $weatherRecommendations[] = 'Rain may cause minor transportation delays.';
                        $weatherRecommendations[] = 'Monitor weather conditions before dispatching cargo.';
                        $weatherRecommendations[] = 'Allow additional delivery time if necessary.';
                    }

                    if ($logisticsStatus == 'High Risk') {
                        $weatherRecommendations[] = 'Severe weather may disrupt logistics operations.';
                        $weatherRecommendations[] = 'Delay non-essential shipments.';
                        $weatherRecommendations[] = 'Monitor airport and seaport operational status.';
                        $weatherRecommendations[] = 'Review transportation schedules before dispatch.';
                    }
                }
            }
        }

        return view('weather.index', compact(
            'countries',
            'country',
            'weather',
            'temperatureTrend',
            'logisticsStatus',
            'logisticsColor',
            'weatherRecommendations'
        ));
    }

    /**
     * Sync Weather (Chunked by 50 countries per request)
     */
    public function sync(Request $request)
    {
        $limit = 50;
        $offset = (int) $request->get('offset', 0);

        // Ambil data negara dengan limit dan offset
        $countries = Country::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('id')
            ->skip($offset)
            ->take($limit)
            ->get();

        // Jika tidak ada data lagi yang diproses, kembali ke halaman utama
        if ($countries->isEmpty()) {
            return redirect()
                ->route('weather')
                ->with('success', 'All countries have been synchronized.');
        }

        $success = 0;
        $failed = 0;

        foreach ($countries as $country) {
            // Log informasi proses negara yang sedang berjalan
            Log::info("Sync Weather: {$country->id} - {$country->name}");

            try {
                // 1. Perubahan parameter 'current' di sync()
                $response = Http::timeout(3)
                    ->retry(1, 1000)
                    ->get('https://api.open-meteo.com/v1/forecast', [
                        'latitude' => $country->latitude,
                        'longitude' => $country->longitude,
                        'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code,rain,precipitation'
                    ]);

                if (!$response->successful()) {
                    Log::error('Open-Meteo API Error', [
                        'country' => $country->name,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    $failed++;
                    continue;
                }

                $current = $response->json('current');

                if (!$current) {
                    Log::error('Current Weather Empty', [
                        'country' => $country->name,
                        'response' => $response->json(),
                    ]);
                    $failed++;
                    continue;
                }

                $weatherType = WeatherType::firstOrCreate(
                    [
                        'name' => $this->getWeatherName($current['weather_code'])
                    ],
                    [
                        'description' => $this->getWeatherName($current['weather_code'])
                    ]
                );

                WeatherLog::updateOrCreate(
                    [
                        'country_id' => $country->id
                    ],
                    [
                        'weather_type_id' => $weatherType->id,
                        'temperature' => $current['temperature_2m'],
                        'humidity' => $current['relative_humidity_2m'],
                        'wind_speed' => $current['wind_speed_10m'],
                        'recorded_at' => now(),
                    ]
                );

                $success++;

            } catch (\Exception $e) {
                Log::error("Failed to sync weather for country ID {$country->id}: " . $e->getMessage());
                $failed++;
            }
        }

        // Cek apakah masih ada data tersisa untuk batch berikutnya
        $nextOffset = $offset + $limit;
        $hasMore = Country::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->skip($nextOffset)
            ->exists();

        $message = "Batch processed (Offset {$offset}-" . ($offset + $countries->count()) . "). Success: {$success} | Failed: {$failed}";

        // Jika masih ada data, redirect otomatis ke dirinya sendiri dengan offset baru
        if ($hasMore) {
            return redirect()
                ->route('weather.sync', ['offset' => $nextOffset])
                ->with('success', $message . " | Moving to next batch...");
        }

        // Jika ini adalah batch terakhir
        return redirect()
            ->route('weather')
            ->with('success', $message . " | All batches completed successfully.");
    }

    /**
     * Convert Weather Code
     */
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