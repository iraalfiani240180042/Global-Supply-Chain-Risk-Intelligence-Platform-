<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Currency;
use App\Models\CurrencyMaster;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::with([
                'country',
                'currencyMaster'
            ])
            ->latest()
            ->paginate(20);

        return view('currency.index', compact('currencies'));
    }

    public function sync()
    {
        $response = Http::timeout(60)->get(
            'https://v6.exchangerate-api.com/v6/' .
            env('EXCHANGE_RATE_API_KEY') .
            '/latest/USD'
        );

        if (!$response->successful()) {

            return redirect()
                ->route('currency')
                ->with('error', 'Failed to fetch exchange rates.');

        }

        $rates = $response->json('conversion_rates');

        $countries = Country::all();

        foreach ($countries as $country) {

            $code = strtoupper($country->iso_code);

            if (!isset($rates[$code])) {
                continue;
            }

            $currencyMaster = CurrencyMaster::firstOrCreate(
                [
                    'code' => $code
                ],
                [
                    'name' => $code,
                    'symbol' => $code
                ]
            );

            Currency::updateOrCreate(

                [
                    'country_id' => $country->id,
                    'currency_master_id' => $currencyMaster->id
                ],

                [
                    'exchange_rate' => $rates[$code],
                    'updated_at_api' => now()
                ]

            );
        }

        return redirect()
            ->route('currency')
            ->with('success', 'Currency synchronized successfully.');
    }
}