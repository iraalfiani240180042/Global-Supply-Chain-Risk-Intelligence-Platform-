<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherLog;
use App\Models\CurrencyLog;
use Illuminate\Support\Facades\Http;

class RiskPredictionController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')->get();

        return view('analytics.risk-prediction', compact('countries'));
    }

    public function calculate($countryId)
    {
        $country = Country::findOrFail($countryId);

        /*
        |--------------------------------------------------------------------------
        | WEATHER RISK (30%)
        |--------------------------------------------------------------------------
        */

        $weather = 20;

        $weatherLog = WeatherLog::where('country_id', $countryId)
            ->latest()
            ->first();

        if ($weatherLog) {

            if ($weatherLog->wind_speed >= 40) {
                $weather = 100;
            } elseif ($weatherLog->wind_speed >= 20) {
                $weather = 60;
            } else {
                $weather = 20;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INFLATION RISK (20%)
        |--------------------------------------------------------------------------
        */

        $inflation = 20;

        try {

            $response = Http::get(
                "https://api.worldbank.org/v2/country/{$country->iso_code}/indicator/FP.CPI.TOTL.ZG",
                [
                    'format' => 'json',
                    'per_page' => 1,
                ]
            );

            if ($response->successful()) {

                $data = $response->json();

                if (isset($data[1][0]['value'])) {

                    $value = $data[1][0]['value'];

                    if ($value >= 10) {
                        $inflation = 100;
                    } elseif ($value >= 6) {
                        $inflation = 70;
                    } elseif ($value >= 3) {
                        $inflation = 40;
                    } else {
                        $inflation = 20;
                    }
                }
            }

        } catch (\Exception $e) {
            $inflation = 20;
        }

        /*
        |--------------------------------------------------------------------------
        | CURRENCY RISK (10%)
        |--------------------------------------------------------------------------
        */

        $currency = 20;

        $trend = CurrencyLog::where('country_id', $countryId)
            ->orderBy('recorded_at', 'desc')
            ->take(7)
            ->get();

        if ($trend->count() >= 2) {

            $max = $trend->max('exchange_rate');
            $min = $trend->min('exchange_rate');
            $latest = $trend->first()->exchange_rate;

            if ($latest > 0) {

                $change = (($max - $min) / $latest) * 100;

                if ($change < 1) {
                    $currency = 20;
                } elseif ($change < 3) {
                    $currency = 60;
                } else {
                    $currency = 100;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | POLITICAL NEWS RISK (40%)
        |--------------------------------------------------------------------------
        */

        $news = 20;

        try {

            $response = Http::get('https://gnews.io/api/v4/search', [
                'q' => $country->name . ' politics',
                'lang' => 'en',
                'max' => 10,
                'apikey' => config('services.gnews.key'),
            ]);

            if ($response->successful()) {

                $articles = $response->json()['articles'] ?? [];

                $negativeWords = [
                    'war',
                    'conflict',
                    'crisis',
                    'sanction',
                    'protest',
                    'strike',
                    'terror',
                    'earthquake',
                    'flood',
                    'tsunami'
                ];

                $negative = 0;

                foreach ($articles as $article) {

                    $text = strtolower(
                        ($article['title'] ?? '') . ' ' .
                        ($article['description'] ?? '')
                    );

                    foreach ($negativeWords as $word) {

                        if (str_contains($text, $word)) {
                            $negative++;
                            break;
                        }
                    }
                }

                if (count($articles) > 0) {

                    $ratio = ($negative / count($articles)) * 100;

                    if ($ratio >= 70) {
                        $news = 100;
                    } elseif ($ratio >= 40) {
                        $news = 70;
                    } elseif ($ratio >= 20) {
                        $news = 40;
                    } else {
                        $news = 20;
                    }
                }
            }

        } catch (\Exception $e) {
            $news = 20;
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL SCORE
        |--------------------------------------------------------------------------
        */

        $score =
            ($weather * 0.30) +
            ($inflation * 0.20) +
            ($news * 0.40) +
            ($currency * 0.10);

        if ($score <= 30) {

            $status = 'Low Risk';

        } elseif ($score <= 60) {

            $status = 'Medium Risk';

        } else {

            $status = 'High Risk';

        }

        return response()->json([
            'country' => $country->name,
            'weather' => round($weather),
            'inflation' => round($inflation),
            'currency' => round($currency),
            'news' => round($news),
            'score' => round($score),
            'status' => $status,
        ]);
    }
}