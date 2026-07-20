<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // Menampilkan daftar negara favorit user
    public function index()
    {
        $favorites = auth()->user()
            ->favoriteCountries()
            ->with('region')
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    // Menambahkan negara ke favorit
    public function store(Country $country)
    {
        Favorite::firstOrCreate([
            'user_id' => auth()->id(),
            'country_id' => $country->id,
        ]);

        return back()->with('success', 'Country added to favorites.');
    }

    // Menghapus negara dari favorit
    public function destroy(Country $country)
    {
        Favorite::where('user_id', auth()->id())
            ->where('country_id', $country->id)
            ->delete();

        return back()->with('success', 'Country removed from favorites.');
    }

    // Menambah atau menghapus favorit (Toggle) berdasarkan ID negara
    public function toggle($countryId)
    {
        $favorite = Favorite::where('user_id', auth()->id())
            ->where('country_id', $countryId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Country removed from favorites.';
        } else {
            Favorite::create([
                'user_id' => auth()->id(),
                'country_id' => $countryId,
            ]);
            $message = 'Country added to favorites.';
        }

        return back()->with('success', $message);
    }
}