<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get();

        $country = null;
        $exchangeRate = null;
        $trend = collect(); // Default collection kosong agar tidak error di view saat data belum dipilih
        $risk = null;
        $riskColor = null;
        $recommendation = null;
        $trendStatus = 'Stable'; // Inisialisasi default agar aman saat pertama kali load
        $changePercent = 0; // Inisialisasi default perubahan persentase

        if ($request->filled('country')) {
            $country = Country::find($request->country);

            if ($country && $country->currency_code) {
                try {
                    $response = Http::timeout(10)->get(
                        "https://v6.exchangerate-api.com/v6/"
                        . env('EXCHANGERATE_API_KEY')
                        . "/latest/USD"
                    );

                    if ($response->successful()) {
                        $rates = $response->json()['conversion_rates'];
                        $rawRate = $rates[$country->currency_code] ?? null;

                        if ($rawRate) {
                            // Simpan atau perbarui log kurs hari ini
                            $currencyLog = CurrencyLog::updateOrCreate(
                                [
                                    'country_id' => $country->id,
                                    'recorded_at' => now()->toDateString()
                                ],
                                [
                                    'exchange_rate' => $rawRate
                                ]
                            );

                            // Sinkronisasi nilai exchangeRate dari log database ter-update
                            $exchangeRate = $currencyLog->exchange_rate;

                            // Ambil riwayat log 7 data terakhir
                            $trend = CurrencyLog::where('country_id', $country->id)
                                ->orderBy('recorded_at', 'desc')
                                ->take(7)
                                ->get()
                                ->reverse(); // Diurutkan dari tanggal terlama ke terbaru untuk Chart

                            // Perhitungan persentase perubahan harian dibanding data sebelumnya
                            if ($trend->count() >= 2) {
                                $previous = $trend->values()[$trend->count() - 2]->exchange_rate;
                                $latest = $trend->last()->exchange_rate;

                                if ($previous > 0) {
                                    $changePercent = (($latest - $previous) / $previous) * 100;
                                }
                            }

                            // Menentukan status tren (Naik/Turun/Stabil) dibanding data sebelumnya
                            if ($trend->count() >= 2) {
                                $previous = $trend->values()[$trend->count() - 2]->exchange_rate;
                                $latest = $trend->last()->exchange_rate;

                                if ($latest > $previous) {
                                    $trendStatus = "Increasing";
                                } elseif ($latest < $previous) {
                                    $trendStatus = "Decreasing";
                                }
                            }

                            // Proses kalkulasi risiko jika data historis tersedia
                            if ($trend->count() > 1) {
                                $max = $trend->max('exchange_rate');
                                $min = $trend->min('exchange_rate');

                                $change = $exchangeRate > 0 ? (($max - $min) / $exchangeRate) * 100 : 0;

                                if ($change < 1) {
                                    $risk = "Low Risk";
                                    $riskColor = "success";
                                    $recommendation = "Exchange rate is stable. Suitable for export transactions.";
                                } elseif ($change < 3) {
                                    $risk = "Moderate";
                                    $riskColor = "warning";
                                    $recommendation = "Monitor exchange rate fluctuations closely before processing exports.";
                                } else {
                                    $risk = "High Risk";
                                    $riskColor = "danger";
                                    $recommendation = "High volatility detected. Consider hedging or delaying export if possible.";
                                }
                            } else {
                                // Kondisi jika baru memiliki 1 record data historis
                                $risk = "Low Risk";
                                $riskColor = "success";
                                $recommendation = "Exchange rate is stable. Initial tracking established today.";
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $exchangeRate = null;
                }
            }
        }

        return view(
            'currency.index',
            compact(
                'countries',
                'country',
                'exchangeRate',
                'trend',
                'trendStatus',
                'changePercent',
                'risk',
                'riskColor',
                'recommendation'
            )
        );
    }
}