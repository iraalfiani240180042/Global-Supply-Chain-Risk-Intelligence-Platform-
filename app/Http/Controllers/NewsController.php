<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with(['country', 'category'])
            ->latest('published_at')
            ->paginate(20);

        return view('news.index', compact('news'));
    }

    public function sync()
    {
        // Ambil negara pertama
        $country = Country::first();

        if (!$country) {
            return redirect()
                ->route('news')
                ->with('error', 'No country data found.');
        }

        // Ambil API Key dari .env
        $apiKey = env('GNEWS_API_KEY');

        if (!$apiKey) {
            return redirect()
                ->route('news')
                ->with('error', 'GNEWS_API_KEY belum diisi pada file .env');
        }

        // Request ke GNews API
        $response = Http::timeout(60)->get(
            'https://gnews.io/api/v4/top-headlines',
            [
                'apikey' => $apiKey,
                'lang' => 'en',
                'country' => 'us',
                'max' => 100
            ]
        );

        // Jika gagal
        if (!$response->successful()) {

            $message = $response->json()['errors'][0]
                ?? $response->body();

            return redirect()
                ->route('news')
                ->with('error', 'Failed to fetch news. ' . $message);
        }

        $articles = $response->json('articles');

        if (empty($articles)) {
            return redirect()
                ->route('news')
                ->with('error', 'Tidak ada berita yang ditemukan.');
        }

        // Kategori default
        $category = NewsCategory::firstOrCreate([
            'name' => 'General'
        ]);

        foreach ($articles as $article) {

            News::updateOrCreate(

                [
                    'url' => $article['url']
                ],

                [
                    'country_id'   => $country->id,
                    'category_id'  => $category->id,
                    'title'        => $article['title'] ?? '-',
                    'source'       => $article['source']['name'] ?? '-',
                    'published_at' => $article['publishedAt'] ?? now(),
                ]
            );
        }

        return redirect()
            ->route('news')
            ->with('success', 'News synchronized successfully!');
    }
}