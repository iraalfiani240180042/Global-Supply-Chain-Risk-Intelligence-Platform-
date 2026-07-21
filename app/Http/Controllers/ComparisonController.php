<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\Http;

class ComparisonController extends Controller
{
    public function index()
    {
        $countries = Country::with('region')
            ->orderBy('name')
            ->get();

        return view('comparison.index', compact('countries'));
    }

    public function compare($countryA, $countryB)
    {
        $country1 = Country::with('region')->findOrFail($countryA);
        $country2 = Country::with('region')->findOrFail($countryB);

        $weatherA = $this->getWeather($country1);
        $weatherB = $this->getWeather($country2);

        $exchangeRates = [];

        if (env('EXCHANGERATE_API_KEY')) {
            $response = Http::get(
                "https://v6.exchangerate-api.com/v6/" .
                env('EXCHANGERATE_API_KEY') .
                "/latest/USD"
            );

            if ($response->successful()) {
                $exchangeRates = $response->json()['conversion_rates'] ?? [];
            }
        }

        // --- AMBIL DATA DARI WORLD BANK API & GNEWS ---

        // GDP & Inflation Country A
        $gdpA = $this->getGdp($country1->iso_code);
        $inflationA = $this->getInflation($country1->iso_code);
        $newsValueA = $this->getNewsValue($country1->name);

        // Hitung Risiko Country A
        $riskA = $this->calculateRisk(
            $inflationA,
            $weatherA,
            $exchangeRates[$country1->currency_code] ?? null,
            $newsValueA
        );

        // GDP & Inflation Country B
        $gdpB = $this->getGdp($country2->iso_code);
        $inflationB = $this->getInflation($country2->iso_code);
        $newsValueB = $this->getNewsValue($country2->name);

        // Hitung Risiko Country B
        $riskB = $this->calculateRisk(
            $inflationB,
            $weatherB,
            $exchangeRates[$country2->currency_code] ?? null,
            $newsValueB
        );

        // --- PENGIRIMAN JSON RESPONSE ---

        return response()->json([
            'countryA' => [
                'id'             => $country1->id,
                'name'           => $country1->name,
                'flag'           => $country1->flag,
                'region'         => $country1->region->name ?? '-',
                'population'     => $country1->population,
                'currency'       => $country1->currency_code,
                'exchange_rate'  => $exchangeRates[$country1->currency_code] ?? '-',
                'gdp'            => $gdpA,
                'inflation_rate' => $inflationA,
                'risk_score'     => $riskA['risk_score'],
                'risk_level'     => $riskA['risk_level'],
            ],

            'countryB' => [
                'id'             => $country2->id,
                'name'           => $country2->name,
                'flag'           => $country2->flag,
                'region'         => $country2->region->name ?? '-',
                'population'     => $country2->population,
                'currency'       => $country2->currency_code,
                'exchange_rate'  => $exchangeRates[$country2->currency_code] ?? '-',
                'gdp'            => $gdpB,
                'inflation_rate' => $inflationB,
                'risk_score'     => $riskB['risk_score'],
                'risk_level'     => $riskB['risk_level'],
            ],

            'weatherA' => $weatherA,
            'weatherB' => $weatherB,
        ]);
    }

    /**
     * Helper Method: Ambil Data GDP dari World Bank
     */
    private function getGdp($isoCode)
    {
        $response = Http::get(
            "https://api.worldbank.org/v2/country/{$isoCode}/indicator/NY.GDP.MKTP.CD",
            [
                'format'   => 'json',
                'per_page' => 1,
            ]
        );

        if ($response->successful()) {
            $data = $response->json();
            return $data[1][0]['value'] ?? null;
        }

        return null;
    }

    /**
     * Helper Method: Ambil Data Inflasi dari World Bank
     */
    private function getInflation($isoCode)
    {
        $response = Http::get(
            "https://api.worldbank.org/v2/country/{$isoCode}/indicator/FP.CPI.TOTL.ZG",
            [
                'format'   => 'json',
                'per_page' => 1,
            ]
        );

        if ($response->successful()) {
            $data = $response->json();
            return isset($data[1][0]['value']) ? round($data[1][0]['value'], 2) : null;
        }

        return null;
    }

    /**
     * Helper Method: Analisis News Value (Sesuai penyesuaian baru)
     */
    private function getNewsValue($countryName)
    {
        $negativeWords = [
            'war',
            'conflict',
            'crisis',
            'strike',
            'earthquake',
            'tsunami',
            'flood',
            'inflation',
            'protest',
            'sanction'
        ];

        $negativeCount = 0;

        $response = Http::get('https://gnews.io/api/v4/search', [
            'q'      => $countryName . ' trade OR export OR logistics',
            'lang'   => 'en',
            'max'    => 5,
            'apikey' => env('GNEWS_API_KEY'),
        ]);

        if ($response->successful()) {
            $news = $response->json()['articles'] ?? [];

            foreach ($news as $item) {
                $title = strtolower($item['title'] ?? '');

                foreach ($negativeWords as $word) {
                    if (str_contains($title, $word)) {
                        $negativeCount++;
                        break;
                    }
                }
            }
        }

        if ($negativeCount == 0) {
            return 85;
        } elseif ($negativeCount <= 2) {
            return 70;
        } elseif ($negativeCount <= 4) {
            return 50;
        } else {
            return 30;
        }
    }

    /**
     * Helper Method: Ambil Data Cuaca
     */
    private function getWeather($country)
    {
        if (!$country->latitude || !$country->longitude) {
            return null;
        }

        $response = Http::get(
            'https://api.open-meteo.com/v1/forecast',
            [
                'latitude'  => $country->latitude,
                'longitude' => $country->longitude,
                'current'   => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                'timezone'  => 'auto'
            ]
        );

        if (!$response->successful()) {
            return null;
        }

        $current = $response->json()['current'];

        $current['description'] = match ($current['weather_code']) {
            0          => '☀️ Clear Sky',
            1, 2, 3    => '⛅ Partly Cloudy',
            45, 48     => '🌫 Fog',
            51, 53, 55 => '🌦 Drizzle',
            61, 63, 65 => '🌧 Rain',
            71, 73, 75 => '❄ Snow',
            80, 81, 82 => '🌧 Rain Showers',
            95         => '⛈ Thunderstorm',
            default    => 'Unknown',
        };

        return $current;
    }

    /**
     * Helper Method: Kalkulasi Skor Risiko (Penyesuaian Baseline 90 & Tier News)
     */
    private function calculateRisk($inflation, $weather, $exchangeRate, $newsValue)
    {
        // ====================================
        // WEATHER SCORE
        // ====================================
        $weatherValue = 90;

        if ($weather) {
            if ($weather['wind_speed_10m'] > 30) {
                $weatherValue = 30;
            } elseif ($weather['wind_speed_10m'] > 20) {
                $weatherValue = 60;
            } elseif ($weather['wind_speed_10m'] > 10) {
                $weatherValue = 80;
            }

            if ($weather['temperature_2m'] > 38) {
                $weatherValue -= 20;
            }

            $weatherValue = max(0, min(100, $weatherValue));
        }

        // ====================================
        // INFLATION SCORE
        // ====================================
        $inflationValue = 90;

        if ($inflation !== null) {
            if ($inflation >= 10) {
                $inflationValue = 20;
            } elseif ($inflation >= 6) {
                $inflationValue = 50;
            } elseif ($inflation >= 3) {
                $inflationValue = 80;
            }
        }

        // ====================================
        // EXCHANGE SCORE
        // ====================================
        $exchangeValue = $exchangeRate ? 90 : 40;

        // ====================================
        // CONVERT SAFE SCORE TO RISK SCORE
        // ====================================
        $weatherRisk   = 100 - $weatherValue;
        $inflationRisk = 100 - $inflationValue;
        $exchangeRisk  = 100 - $exchangeValue;
        $newsRisk      = 100 - $newsValue;

        // ====================================
        // FINAL RISK SCORE (Weather 30%, Inflation 20%, Exchange 10%, News 40%)
        // ====================================
        $riskScore = round(
            ($weatherRisk * 0.30) +
            ($inflationRisk * 0.20) +
            ($exchangeRisk * 0.10) +
            ($newsRisk * 0.40)
        );

        // ====================================
        // LEVEL
        // ====================================
        if ($riskScore <= 30) {
            $riskLevel = "Low Risk";
        } elseif ($riskScore <= 60) {
            $riskLevel = "Medium Risk";
        } else {
            $riskLevel = "High Risk";
        }

        return [
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel
        ];
    }
}