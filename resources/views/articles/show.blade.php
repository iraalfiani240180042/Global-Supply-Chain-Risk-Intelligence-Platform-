@extends('layouts.app')

@section('title', $article->title)

@section('content')

<div class="container-fluid">

    <div class="mb-4 d-flex justify-content-between align-items-center">

        <div>
            <h2 class="fw-bold">{{ $article->title }}</h2>

            <p class="text-muted mb-0">
                Analysis Article Details
            </p>
        </div>

        <div>
            <a href="{{ route('articles.index') }}"
               class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
        </div>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            {{-- Article Image --}}
            @if($article->image)
                <div class="mb-4">
                    <img src="{{ asset('storage/'.$article->image) }}"
                         class="img-fluid rounded shadow-sm w-100"
                         style="height:400px; object-fit:cover;">
                </div>
            @endif

            {{-- Category --}}
            <span class="badge bg-primary mb-3">
                {{ $article->category }}
            </span>

            {{-- Country --}}
            <h5 class="mb-3">

                <img src="{{ $article->country->flag }}"
                     width="35"
                     class="me-2">

                {{ $article->country->name }}

            </h5>

            {{-- Risk --}}
            <div class="mb-3">

                @if($article->risk_level == 'High')

                    <span class="badge bg-danger">
                        Risk : {{ $article->risk_level }}
                    </span>

                @elseif($article->risk_level == 'Medium')

                    <span class="badge bg-warning text-dark">
                        Risk : {{ $article->risk_level }}
                    </span>

                @else

                    <span class="badge bg-success">
                        Risk : {{ $article->risk_level }}
                    </span>

                @endif

                @if($article->recommended)

                    <span class="badge bg-success ms-2">
                        Recommended
                    </span>

                @else

                    <span class="badge bg-secondary ms-2">
                        Not Recommended
                    </span>

                @endif

            </div>

            {{-- Published --}}
            <p class="text-muted">

                <strong>Published :</strong>

                {{ optional($article->published_at)->format('d F Y') }}

            </p>

            <hr>

            {{-- Summary --}}
            <h4 class="fw-bold">
                Summary
            </h4>

            <p class="text-muted">
                {{ $article->summary }}
            </p>

            <hr>

            {{-- Content --}}
            <h4 class="fw-bold">
                Analysis Content
            </h4>

            <p style="text-align: justify; line-height: 1.8;">
                {!! nl2br(e($article->content)) !!}
            </p>

        </div>

    </div>

</div>

@endsection