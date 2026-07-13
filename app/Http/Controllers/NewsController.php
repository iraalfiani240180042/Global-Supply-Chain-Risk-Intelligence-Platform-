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
        $country = Country::first();

        if (!$country) {
            return redirect()
                ->route('news')
                ->with('error', 'No country data found.');
        }

        $response = Http::timeout(60)->get(
            'https://gnews.io/api/v4/top-headlines',
            [
                'token' => env('GNEWS_API_KEY'),
                'lang' => 'en',
                'max' => 10
            ]
        );

        if (!$response->successful()) {
            return redirect()
                ->route('news')
                ->with('error', 'Failed to fetch news.');
        }

        $articles = $response->json('articles');

        $category = NewsCategory::firstOrCreate([
            'name' => 'General'
        ]);

        foreach ($articles as $article) {

            News::updateOrCreate(

                [
                    'url' => $article['url']
                ],

                [
                    'country_id' => $country->id,
                    'category_id' => $category->id,
                    'title' => $article['title'] ?? '',
                    'source' => $article['source']['name'] ?? '-',
                    'published_at' => $article['publishedAt'] ?? now(),
                ]

            );

        }

        return redirect()
            ->route('news')
            ->with('success', 'News synchronized successfully!');
    }
}