<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\SentimentAnalysisService;

class NewsController extends Controller
{
    public function index(Request $request, SentimentAnalysisService $sentiment)
    {
        $countries = Country::orderBy('name')->get();

        $country = $request->country;
        $category = $request->category;

        $articles = [];
        
        $sentimentSummary = [
            'Positive' => 0,
            'Neutral' => 0,
            'Negative' => 0
        ];

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

                foreach ($articles as &$article) {

                    $text = $article['title'] . ' ' . ($article['description'] ?? '');

                    $result = $sentiment->analyze($text);

                    $article['sentiment'] = $result['sentiment'];
                    $article['positive_score'] = $result['positive'];
                    $article['negative_score'] = $result['negative'];

                    // Hitung jumlah sentiment
                    $sentimentSummary[$result['sentiment']]++;
                }
            }
        }

        // Hitung persentase sentimen
        $totalSentiment = array_sum($sentimentSummary);

        if ($totalSentiment > 0) {
            $sentimentPercentage = [
                'Positive' => round(($sentimentSummary['Positive'] / $totalSentiment) * 100),
                'Neutral' => round(($sentimentSummary['Neutral'] / $totalSentiment) * 100),
                'Negative' => round(($sentimentSummary['Negative'] / $totalSentiment) * 100),
            ];
        } else {
            $sentimentPercentage = [
                'Positive' => 0,
                'Neutral' => 0,
                'Negative' => 0
            ];
        }

        return view('news.index', compact(
            'countries',
            'country',
            'category',
            'articles',
            'sentimentPercentage'
        ));
    }
}