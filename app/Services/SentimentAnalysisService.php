<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentAnalysisService
{
    public function analyze($text)
    {
        $positiveWords = PositiveWord::pluck('word')->toArray();
        $negativeWords = NegativeWord::pluck('word')->toArray();

        $words = explode(' ', strtolower($text));

        $positiveScore = 0;
        $negativeScore = 0;


        foreach ($words as $word) {

            $word = preg_replace('/[^a-z]/', '', $word);

            if (in_array($word, $positiveWords)) {
                $positiveScore++;
            }

            if (in_array($word, $negativeWords)) {
                $negativeScore++;
            }
        }


        if ($positiveScore > $negativeScore) {
            $sentiment = "Positive";
        } elseif ($negativeScore > $positiveScore) {
            $sentiment = "Negative";
        } else {
            $sentiment = "Neutral";
        }


        return [
            'sentiment' => $sentiment,
            'positive' => $positiveScore,
            'negative' => $negativeScore
        ];
    }
}