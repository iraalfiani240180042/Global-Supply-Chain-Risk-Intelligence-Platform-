<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get();

        $country = $request->country;
        $category = $request->category;

        $articles = [];

        if (!empty($country) && !empty($category)) {

            $query = $country . " " . $category;

            $response = Http::get('https://gnews.io/api/v4/search', [
                'q'      => $query,
                'lang'   => 'en',
                'max'    => 20,
                'apikey' => config('services.gnews.key'),
            ]);

            if ($response->successful()) {
                $articles = $response->json()['articles'];
            }
        }

        return view('news.index', compact(
            'countries',
            'country',
            'category',
            'articles'
        ));
    }
}