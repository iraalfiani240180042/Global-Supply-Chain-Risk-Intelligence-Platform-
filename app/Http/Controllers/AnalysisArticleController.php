<?php

namespace App\Http\Controllers;

use App\Models\AnalysisArticle;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnalysisArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = AnalysisArticle::with('country')
            ->latest()
            ->get();

        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::orderBy('name')->get();

        return view('articles.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_id'   => 'required|exists:countries,id',
            'title'        => 'required|string|max:255',
            'summary'      => 'required|string',
            'content'      => 'required|string',
            'category'     => 'required|string|max:100',
            'risk_level'   => 'required|string|max:50',
            'recommended'  => 'required|boolean',
            'status'       => 'required|string',
            'published_at' => 'required|date',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
        }

        AnalysisArticle::create([
            'country_id'   => $request->country_id,
            'title'        => $request->title,
            'summary'      => $request->summary,
            'content'      => $request->content,
            'category'     => $request->category,
            'risk_level'   => $request->risk_level,
            'recommended'  => $request->recommended,
            'status'       => $request->status,
            'published_at' => $request->published_at,
            'image'        => $imagePath,
        ]);

        return redirect()
            ->route('articles.index')
            ->with('success', 'Artikel berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AnalysisArticle $article)
    {
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AnalysisArticle $article)
    {
        $countries = Country::orderBy('name')->get();

        return view('articles.edit', compact('article', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AnalysisArticle $article)
    {
        $request->validate([
            'country_id'   => 'required|exists:countries,id',
            'title'        => 'required|string|max:255',
            'summary'      => 'required|string',
            'content'      => 'required|string',
            'category'     => 'required|string|max:100',
            'risk_level'   => 'required|string|max:50',
            'recommended'  => 'required|boolean',
            'status'       => 'required|string',
            'published_at' => 'required|date',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = $article->image;
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada sebelum mengunggah yang baru
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $imagePath = $request->file('image')->store('articles', 'public');
        }

        $article->update([
            'country_id'   => $request->country_id,
            'title'        => $request->title,
            'summary'      => $request->summary,
            'content'      => $request->content,
            'category'     => $request->category,
            'risk_level'   => $request->risk_level,
            'recommended'  => $request->recommended,
            'status'       => $request->status,
            'published_at' => $request->published_at,
            'image'        => $imagePath,
        ]);

        return redirect()
            ->route('articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AnalysisArticle $article)
    {
        // Hapus gambar dari storage jika artikel memiliki file gambar
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return redirect()
            ->route('articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }
}