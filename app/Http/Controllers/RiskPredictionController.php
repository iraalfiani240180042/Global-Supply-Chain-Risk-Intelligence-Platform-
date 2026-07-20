<?php

namespace App\Http\Controllers;

use App\Models\Country;

class RiskPredictionController extends Controller
{

    public function index()
    {
        $countries = Country::orderBy('name')->get();

        return view('analytics.risk-prediction', compact('countries'));
    }


    public function calculate($countryId)
    {

        $country = Country::findOrFail($countryId);


        $weather = 30;
        $inflation = 20;
        $currency = 10;
        $news = 40;


        $score = 
            $weather +
            $inflation +
            $currency +
            $news;


        if($score <= 30){

            $status = "Low Risk";

        }elseif($score <= 60){

            $status = "Medium Risk";

        }else{

            $status = "High Risk";

        }


        return response()->json([

            'country'=>$country->name,
            'weather'=>$weather,
            'inflation'=>$inflation,
            'currency'=>$currency,
            'news'=>$news,
            'score'=>$score,
            'status'=>$status

        ]);

    }

}