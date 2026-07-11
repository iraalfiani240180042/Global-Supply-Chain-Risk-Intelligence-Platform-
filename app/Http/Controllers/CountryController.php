<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CountryController extends Controller
{
    /**
     * Menampilkan daftar negara
     */
    public function index()
    {
        $countries = Country::with('region')
            ->orderBy('name')
            ->paginate(20);

        return view('countries.index', compact('countries'));
    }

    /**
     * Form tambah negara
     */
    public function create()
    {
        $regions = Region::orderBy('name')->get();

        return view('countries.create', compact('regions'));
    }

    /**
     * Simpan negara baru
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
     * Sinkronisasi negara dari REST Countries API
     */
    public function sync()
    {
        $response = Http::timeout(60)->get('https://restcountries.com/v3.1/all');

        if (!$response->successful()) {
            return back()->with('error', 'Failed to fetch countries.');
        }

       dd($response->status(), $response->body());

        foreach ($countries as $item) {

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
                    'iso_code' => $item['cca2'] ?? ''
                ],

                [
                    'region_id'  => $region->id,
                    'name'       => $item['name']['common'] ?? '',
                    'capital'    => $item['capital'][0] ?? '-',
                    'population' => $item['population'] ?? 0,
                    'latitude'   => $item['latlng'][0] ?? null,
                    'longitude'  => $item['latlng'][1] ?? null,
                    'flag'       => $item['flags']['png'] ?? null,
                ]

            );
        }

        return redirect()
            ->route('countries.index')
            ->with('success', 'Countries synchronized successfully!');
    }
}