<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CountryController extends Controller
{
    /**
     * Country Insights
     */
    public function insights()
    {
        $countries = Country::with('region')
            ->orderBy('name')
            ->get();

        return view('countries.index', compact('countries'));
    }

    /**
     * Detail Country dengan Data Cuaca Terkini, Nilai Tukar, GDP, Inflasi, Analisis Risiko, dan Berita
     */
    public function show(Country $country)
    {
        $countries = Country::with('region')
            ->orderBy('name')
            ->get();

        $weather = null;

        // Validasi ketersediaan koordinat sebelum melakukan hit API
        if ($country->latitude && $country->longitude) {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $country->latitude,
                'longitude' => $country->longitude,
                'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                'timezone' => 'auto'
            ]);

            if ($response->successful()) {
                $weather = $response->json()['current'];

                // Menambahkan deskripsi cuaca berdasarkan weather_code
                $weather['description'] = match ($weather['weather_code']) {
                    0 => '☀️ Clear Sky',
                    1, 2, 3 => '⛅ Partly Cloudy',
                    45, 48 => '🌫 Fog',
                    51, 53, 55 => '🌦 Drizzle',
                    61, 63, 65 => '🌧 Rain',
                    71, 73, 75 => '❄ Snow',
                    80, 81, 82 => '🌧 Rain Showers',
                    95 => '⛈ Thunderstorm',
                    default => 'Unknown'
                };
            }
        }

        // Integrasi Exchange Rate API
        $exchangeRate = null;

        if ($country->currency_code) {
            $response = Http::get(
                "https://v6.exchangerate-api.com/v6/" .
                env('EXCHANGERATE_API_KEY') .
                "/latest/USD"
            );

            if ($response->successful()) {
                $rates = $response->json()['conversion_rates'] ?? [];
                $exchangeRate = $rates[$country->currency_code] ?? null;
            }
        }

        // =====================
        // GDP (World Bank API)
        // =====================
        $gdp = null;

        $response = Http::get(
            "https://api.worldbank.org/v2/country/{$country->iso_code}/indicator/NY.GDP.MKTP.CD",
            [
                'format' => 'json',
                'per_page' => 1,
            ]
        );

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data[1][0]['value'])) {
                $gdp = $data[1][0]['value'];
            }
        }

        // =====================
        // GDP Trend (10 Tahun)
        // =====================
        $gdpTrend = [];

        $response = Http::get(
            "https://api.worldbank.org/v2/country/{$country->iso_code}/indicator/NY.GDP.MKTP.CD",
            [
                'format' => 'json',
                'per_page' => 10,
            ]
        );

        if ($response->successful()) {
            $data = $response->json()[1] ?? [];

            foreach (array_reverse($data) as $item) {
                if ($item['value'] != null) {
                    $gdpTrend[] = [
                        'year' => $item['date'],
                        'value' => round($item['value'] / 1000000000, 2) // dalam Billion USD
                    ];
                }
            }
        }

        // =====================
        // Inflation Trend (10 Tahun) & Current Inflation
        // =====================
        $inflation = null;
        $inflationTrend = [];
        $inflationIndexed = []; // Untuk mempermudah lookup tren risiko historis

        $response = Http::get(
            "https://api.worldbank.org/v2/country/{$country->iso_code}/indicator/FP.CPI.TOTL.ZG",
            [
                'format' => 'json',
                'per_page' => 10,
            ]
        );

        if ($response->successful()) {
            $data = $response->json()[1] ?? [];

            foreach (array_reverse($data) as $item) {
                if ($item['value'] != null) {
                    $val = round($item['value'], 2);
                    $inflationTrend[] = [
                        'year' => $item['date'],
                        'value' => $val
                    ];
                    $inflationIndexed[$item['date']] = $val;
                }
            }

            $inflation = end($inflationTrend)['value'] ?? null;
        }

        // =====================
        // Latest News (GNews API)
        // =====================
        $news = [];

        $response = Http::get('https://gnews.io/api/v4/search', [
            'q' => $country->name . ' trade OR export OR logistics',
            'lang' => 'en',
            'max' => 5,
            'apikey' => env('GNEWS_API_KEY'),
        ]);

        if ($response->successful()) {
            $news = $response->json()['articles'] ?? [];
        }

        // ====================================
        // RISK SCORE CALCULATION
        // ====================================

        // Weight
        $weightWeather   = 0.25;
        $weightInflation = 0.25;
        $weightExchange  = 0.20;
        $weightNews      = 0.30;

        // ====================================
        // WEATHER SCORE (0-100)
        // ====================================
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

        // ====================================
        // INFLATION SCORE
        // ====================================
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

        // ====================================
        // EXCHANGE SCORE
        // ====================================
        $exchangeValue = $exchangeRate ? 100 : 40;

        // ====================================
        // NEWS SCORE
        // ====================================
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

        foreach ($news as $item) {

            $title = strtolower($item['title']);

            foreach ($negativeWords as $word) {

                if (str_contains($title, $word)) {
                    $negativeCount++;
                    break;
                }

            }

        }

        if ($negativeCount == 0) {
            $newsValue = 100;
        } elseif ($negativeCount <= 2) {
            $newsValue = 70;
        } elseif ($negativeCount <= 4) {
            $newsValue = 40;
        } else {
            $newsValue = 20;
        }

        // ====================================
        // FINAL RISK SCORE
        // ====================================

        $safeScore =
            ($weatherValue * $weightWeather) +
            ($inflationValue * $weightInflation) +
            ($exchangeValue * $weightExchange) +
            ($newsValue * $weightNews);

        $riskScore = round(100 - $safeScore);

        // ====================================
        // LEVEL
        // ====================================

        if ($riskScore <= 30) {

            $riskLevel = 'Low Risk';
            $riskColor = 'success';

        } elseif ($riskScore <= 60) {

            $riskLevel = 'Medium Risk';
            $riskColor = 'warning';

        } else {

            $riskLevel = 'High Risk';
            $riskColor = 'danger';

        }

        // ====================================
        // BREAKDOWN
        // ====================================

        $riskBreakdown = [
            'Weather'        => round((100 - $weatherValue) * $weightWeather),
            'Inflation'      => round((100 - $inflationValue) * $weightInflation),
            'Exchange Rate'  => round((100 - $exchangeValue) * $weightExchange),
            'News Sentiment' => round((100 - $newsValue) * $weightNews),
        ];

        // =====================
        // Currency Trend (7 Hari Terakhir)
        // =====================
        $currencyTrend = [];

        if ($country->currency_code) {
            for ($i = 6; $i >= 0; $i--) {
                $currencyTrend[] = [
                    'day' => now()->subDays($i)->format('d M'),
                    'value' => $exchangeRate
                ];
            }
        }

        // =====================
        // Risk Trend (Berdasarkan Tren Historis Berbasis Tahun)
        // =====================
        $riskTrend = [];

        foreach ($inflationTrend as $item) {

            $score = 100;

            if ($item['value'] >= 10) {
                $score = 20;
            } elseif ($item['value'] >= 6) {
                $score = 50;
            } elseif ($item['value'] >= 3) {
                $score = 80;
            }

            $riskTrend[] = [
                'year' => $item['year'],
                'score' => 100 - $score
            ];
        }

        return view('countries.index', compact(
            'countries',
            'country',
            'weather',
            'exchangeRate',
            'gdp',
            'gdpTrend',
            'inflation',
            'riskScore',
            'riskLevel',
            'riskColor',
            'news',
            'inflationTrend',
            'currencyTrend',
            'riskTrend',
            'riskBreakdown'
        ));
    }

    /**
     * Index
     */
    public function index()
    {
        return $this->insights();
    }

    /**
     * Form Create
     */
    public function create()
    {
        $regions = Region::orderBy('name')->get();

        return view('countries.create', compact('regions'));
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $request->validate([
            'region_id'  => 'required|exists:regions,id',
            'name'       => 'required|max:100',
            'iso_code'   => 'required|max:10|unique:countries,iso_code',
            'capital'    => 'required|max:100',
            'population' => 'nullable|numeric',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'flag'       => 'nullable',
        ]);

        Country::create($request->all());

        return redirect()
            ->route('countries.index')
            ->with('success', 'Country added successfully.');
    }

    /**
     * Sync Countries dari RestCountries V5
     */
    public function sync()
    {
        $apiKey = env('RESTCOUNTRIES_API_KEY');

        $limit = 100;
        $offset = 0;
        $success = 0;

        do {
            $response = Http::withToken($apiKey)
                ->timeout(120)
                ->get('https://api.restcountries.com/countries/v5', [
                    'limit' => $limit,
                    'offset' => $offset,
                ]);

            if (!$response->successful()) {
                return redirect()
                    ->route('countries.index')
                    ->with('error', 'Failed to connect RestCountries API.');
            }

            $result = $response->json();
            $objects = $result['data']['objects'] ?? [];

            foreach ($objects as $item) {
                $isoCode = $item['codes']['alpha_2'] ?? null;

                // Validasi agar tidak memasukkan nilai kosong pada unique key
                if (!$isoCode) {
                    continue;
                }

                $regionName = $item['region'] ?? 'Other';

                $region = Region::firstOrCreate(
                    [
                        'name' => $regionName
                    ],
                    [
                        'code' => strtoupper(substr($regionName, 0, 3))
                    ]
                );

                Country::updateOrCreate(
                    [
                        'iso_code' => $isoCode
                    ],
                    [
                        'region_id'       => $region->id,
                        'name'            => $item['names']['common'] ?? '-',
                        'capital'         => $item['capitals'][0]['name'] ?? '-',
                        'population'      => $item['population'] ?? 0,
                        'latitude'        => $item['coordinates']['lat'] ?? null,
                        'longitude'       => $item['coordinates']['lng'] ?? null,
                        'flag'            => $item['flag']['url_png'] ?? null,

                        // Currency
                        'currency_name'   => $item['currencies'][0]['name'] ?? null,
                        'currency_code'   => $item['currencies'][0]['code'] ?? null,
                        'currency_symbol' => $item['currencies'][0]['symbol'] ?? null,
                    ]
                );

                $success++;
            }

            $offset += $limit;

        } while (count($objects) == $limit);

        return redirect()
            ->route('countries.index')
            ->with('success', "{$success} countries synchronized successfully.");
    }
}