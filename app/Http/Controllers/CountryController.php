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
        $response = Http::timeout(60)
            ->withToken(env('RESTCOUNTRIES_API_KEY'))
            ->get('https://api.restcountries.com/countries/v5', [
                'limit' => 100
            ]);

        if (!$response->successful()) {
            return redirect()
                ->route('countries.index')
                ->with('error', 'Failed to fetch countries.');
        }

        $countries = $response->json('data.objects');

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
                    'iso_code' => $item['codes']['alpha_2'] ?? ''
                ],
                [
                    'region_id'  => $region->id,
                    'name'       => $item['names']['common'] ?? '',
                    'capital'    => $item['capitals'][0]['name'] ?? '-',
                    'population' => $item['population'] ?? 0,
                    'latitude'   => $item['coordinates']['lat'] ?? null,
                    'longitude'  => $item['coordinates']['lng'] ?? null,
                    'flag'       => $item['flag']['url_png'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('countries.index')
            ->with('success', 'Countries synchronized successfully!');
    }
}