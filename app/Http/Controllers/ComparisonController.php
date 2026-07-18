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

        // --- AMBIL DATA DARI WORLD BANK API ---

        // GDP Country A
        $gdpA = null;
        $responseGdpA = Http::get(
            "https://api.worldbank.org/v2/country/{$country1->iso_code}/indicator/NY.GDP.MKTP.CD",
            [
                'format' => 'json',
                'per_page' => 1,
            ]
        );
        if ($responseGdpA->successful()) {
            $data = $responseGdpA->json();
            $gdpA = $data[1][0]['value'] ?? null;
        }

        // Inflation Country A
        $inflationA = null;
        $responseInflationA = Http::get(
            "https://api.worldbank.org/v2/country/{$country1->iso_code}/indicator/FP.CPI.TOTL.ZG",
            [
                'format' => 'json',
                'per_page' => 1,
            ]
        );
        if ($responseInflationA->successful()) {
            $data = $responseInflationA->json();
            $inflationA = $data[1][0]['value'] ?? null;
        }

        // News Sentiment Country A
        $positiveNewsA = 0;
        $negativeNewsA = 0;

        $responseNewsA = Http::get('https://gnews.io/api/v4/search',[
            'q'=>$country1->name,
            'lang'=>'en',
            'max'=>5,
            'apikey'=>env('GNEWS_API_KEY')
        ]);

        if($responseNewsA->successful()){
            $articlesA = $responseNewsA->json()['articles'] ?? [];

            foreach($articlesA as $article){
                $text = strtolower(
                    ($article['title'] ?? '') . ' ' .
                    ($article['description'] ?? '')
                );

                if(
                    str_contains($text,'growth') ||
                    str_contains($text,'increase') ||
                    str_contains($text,'success') ||
                    str_contains($text,'investment')
                ){
                    $positiveNewsA++;
                }

                if(
                    str_contains($text,'war') ||
                    str_contains($text,'crisis') ||
                    str_contains($text,'inflation') ||
                    str_contains($text,'disaster') ||
                    str_contains($text,'conflict')
                ){
                    $negativeNewsA++;
                }
            }
        }

        $negativeCountA = $negativeNewsA;

        if ($negativeCountA == 0) {
            $newsValueA = 100;
        } elseif ($negativeCountA <= 2) {
            $newsValueA = 70;
        } elseif ($negativeCountA <= 4) {
            $newsValueA = 40;
        } else {
            $newsValueA = 20;
        }

        // Hitung Risiko Country A (Tanpa parameter $gdpA)
        $riskA = $this->calculateRisk(
            $inflationA,
            $weatherA,
            $exchangeRates[$country1->currency_code] ?? null,
            $newsValueA
        );

        $riskScoreA = $riskA['risk_score'];
        $riskLevelA = $riskA['risk_level'];


        // GDP Country B
        $gdpB = null;
        $responseGdpB = Http::get(
            "https://api.worldbank.org/v2/country/{$country2->iso_code}/indicator/NY.GDP.MKTP.CD",
            [
                'format' => 'json',
                'per_page' => 1,
            ]
        );
        if ($responseGdpB->successful()) {
            $data = $responseGdpB->json();
            $gdpB = $data[1][0]['value'] ?? null;
        }

        // Inflation Country B
        $inflationB = null;
        $responseInflationB = Http::get(
            "https://api.worldbank.org/v2/country/{$country2->iso_code}/indicator/FP.CPI.TOTL.ZG",
            [
                'format' => 'json',
                'per_page' => 1,
            ]
        );
        if ($responseInflationB->successful()) {
            $data = $responseInflationB->json();
            $inflationB = $data[1][0]['value'] ?? null;
        }

        // News Sentiment Country B
        $positiveNewsB = 0;
        $negativeNewsB = 0;

        $responseNewsB = Http::get('https://gnews.io/api/v4/search',[
            'q'=>$country2->name,
            'lang'=>'en',
            'max'=>5,
            'apikey'=>env('GNEWS_API_KEY')
        ]);

        if($responseNewsB->successful()){
            $articlesB = $responseNewsB->json()['articles'] ?? [];

            foreach($articlesB as $article){
                $text = strtolower(
                    ($article['title'] ?? '') . ' ' .
                    ($article['description'] ?? '')
                );

                if(
                    str_contains($text,'growth') ||
                    str_contains($text,'increase') ||
                    str_contains($text,'success') ||
                    str_contains($text,'investment')
                ){
                    $positiveNewsB++;
                }

                if(
                    str_contains($text,'war') ||
                    str_contains($text,'crisis') ||
                    str_contains($text,'inflation') ||
                    str_contains($text,'disaster') ||
                    str_contains($text,'conflict')
                ){
                    $negativeNewsB++;
                }
            }
        }

        $negativeCountB = $negativeNewsB;

        if ($negativeCountB == 0) {
            $newsValueB = 100;
        } elseif ($negativeCountB <= 2) {
            $newsValueB = 70;
        } elseif ($negativeCountB <= 4) {
            $newsValueB = 40;
        } else {
            $newsValueB = 20;
        }

        // Hitung Risiko Country B (Tanpa parameter $gdpB)
        $riskB = $this->calculateRisk(
            $inflationB,
            $weatherB,
            $exchangeRates[$country2->currency_code] ?? null,
            $newsValueB
        );

        $riskScoreB = $riskB['risk_score'];
        $riskLevelB = $riskB['risk_level'];


        // --- PENGIRIMAN JSON RESPONSE ---

        return response()->json([
            'countryA' => [
                'id' => $country1->id,
                'name' => $country1->name,
                'flag' => $country1->flag,
                'region' => $country1->region->name ?? '-',
                'population' => $country1->population,
                'currency' => $country1->currency_code,
                'exchange_rate' => $exchangeRates[$country1->currency_code] ?? '-',
                'gdp' => $gdpA,
                'inflation_rate' => $inflationA,
                'risk_score' => $riskScoreA,
                'risk_level' => $riskLevelA,
            ],

            'countryB' => [
                'id' => $country2->id,
                'name' => $country2->name,
                'flag' => $country2->flag,
                'region' => $country2->region->name ?? '-',
                'population' => $country2->population,
                'currency' => $country2->currency_code,
                'exchange_rate' => $exchangeRates[$country2->currency_code] ?? '-',
                'gdp' => $gdpB,
                'inflation_rate' => $inflationB,
                'risk_score' => $riskScoreB,
                'risk_level' => $riskLevelB,
            ],

            'weatherA' => $weatherA,
            'weatherB' => $weatherB,
        ]);
    }

    private function getWeather($country)
    {
        if (!$country->latitude || !$country->longitude) {
            return null;
        }

        $response = Http::get(
            'https://api.open-meteo.com/v1/forecast',
            [
                'latitude' => $country->latitude,
                'longitude' => $country->longitude,
                'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                'timezone' => 'auto'
            ]
        );

        if (!$response->successful()) {
            return null;
        }

        $current = $response->json()['current'];

        $current['description'] = match ($current['weather_code']) {
            0 => 'Clear Sky',
            1, 2, 3 => 'Partly Cloudy',
            45, 48 => 'Fog',
            51, 53, 55 => 'Drizzle',
            61, 63, 65 => 'Rain',
            71, 73, 75 => 'Snow',
            80, 81, 82 => 'Rain Showers',
            95 => 'Thunderstorm',
            default => 'Unknown',
        };

        return $current;
    }

    private function calculateRisk($inflation, $weather, $exchangeRate, $newsValue)
    {
        $weightWeather   = 0.25;
        $weightInflation = 0.25;
        $weightExchange  = 0.20;
        $weightNews      = 0.30;

        // Weather
        $weatherValue = 100;

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

        // Inflation
        $inflationValue = 100;

        if ($inflation !== null) {

            if ($inflation >= 10) {
                $inflationValue = 20;
            } elseif ($inflation >= 6) {
                $inflationValue = 50;
            } elseif ($inflation >= 3) {
                $inflationValue = 80;
            }

        }

        // Exchange Rate
        $exchangeValue = $exchangeRate ? 100 : 40;

        // Final Score
        $safeScore =
            ($weatherValue * $weightWeather) +
            ($inflationValue * $weightInflation) +
            ($exchangeValue * $weightExchange) +
            ($newsValue * $weightNews);

        $riskScore = round(100 - $safeScore);

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