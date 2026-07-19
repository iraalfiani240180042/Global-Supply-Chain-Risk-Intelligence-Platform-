@extends('layouts.app')

@section('title','Artikel Analisis')

@section('content')

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h1 class="fw-bold">
Artikel Analisis
</h1>

<p class="text-muted">
Analysis reports and strategic recommendations for global trade and supply chain activities.
</p>

</div>

<a href="{{ route('articles.create') }}"
class="btn btn-primary">

<i class="bi bi-plus-circle"></i>

Add Artikel

</a>

</div>

<div class="row">

@foreach($articles as $article)

<div class="col-lg-6 mb-4">

<div class="card shadow-sm h-100">

<div class="card-body">

<span class="badge bg-primary mb-3">

{{ $article->category }}

</span>

<h2 class="fw-bold">

{{ $article->title }}

</h2>

<h5 class="text-muted">

<img src="{{ $article->country->flag }}"
width="35">

{{ strtolower($article->country->name) }}

</h5>

<p class="mt-3">

{{ $article->summary }}

</p>

<div class="mb-4">

<span class="badge bg-warning text-dark">

Risk:
{{ $article->risk_level }}

</span>

@if($article->recommended)

<span class="badge bg-success">

Recommended

</span>

@endif

</div>

<p class="text-muted">

published:

{{ $article->published_at->format('d M Y') }}

</p>

<div class="mt-4">
    <a href="{{ route('articles.show', $article->id) }}" class="btn btn-outline-primary">
        <i class="bi bi-book"></i> Read More
    </a>
    <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-warning">
        <i class="bi bi-pencil-square"></i> Edit
    </a>

    <!-- Form Delete -->
    <form action="{{ route('articles.destroy', $article->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash"></i> Delete
        </button>
    </form>
</div>

</div>

</div>

</div>

@endforeach

</div>

</div>

@endsection