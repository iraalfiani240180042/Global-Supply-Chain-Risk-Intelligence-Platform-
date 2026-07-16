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
        // Inflation (World Bank API)
        // =====================
        $inflation = null;

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
                $inflation = $data[1][0]['value'];
            }
        }

        // =====================
        // Risk Analysis
        // =====================
        $riskScore = 0;

        // GDP
        if ($gdp) {
            if ($gdp >= 1000000000000) {          // >= 1 Trillion
                $riskScore += 30;
            } elseif ($gdp >= 100000000000) {     // >= 100 Billion
                $riskScore += 20;
            } else {
                $riskScore += 10;
            }
        }

        // Inflation
        if ($inflation !== null) {
            if ($inflation < 3) {
                $riskScore += 25;
            } elseif ($inflation < 6) {
                $riskScore += 15;
            } else {
                $riskScore += 5;
            }
        }

        // Weather
        if ($weather) {
            if (in_array($weather['weather_code'], [0, 1, 2, 3])) {
                $riskScore += 20;
            } else {
                $riskScore += 10;
            }

            // Wind
            if ($weather['wind_speed_10m'] < 20) {
                $riskScore += 15;
            } else {
                $riskScore += 5;
            }
        }

        // Exchange Rate tersedia
        if ($exchangeRate) {
            $riskScore += 10;
        }

        // Tentukan kategori
        $riskLevel = match (true) {
            $riskScore >= 80 => 'Low Risk',
            $riskScore >= 60 => 'Medium Risk',
            default => 'High Risk',
        };

        $riskColor = match ($riskLevel) {
            'Low Risk' => 'success',
            'Medium Risk' => 'warning',
            default => 'danger',
        };

        // =====================
        // Latest News
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
            'news'
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
                        'iso_code' => $item['codes']['alpha_2'] ?? ''
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