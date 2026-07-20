<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentWordSeeder extends Seeder
{
    public function run(): void
    {

        $positive = [
            'growth',
            'increase',
            'improve',
            'profit',
            'stable',
            'success',
            'strong',
            'boost'
        ];


        foreach($positive as $word){

            PositiveWord::create([
                'word'=>$word
            ]);

        }



        $negative = [
            'war',
            'crisis',
            'inflation',
            'delay',
            'disaster',
            'risk',
            'decline',
            'loss'
        ];


        foreach($negative as $word){

            NegativeWord::create([
                'word'=>$word
            ]);

        }

    }
}