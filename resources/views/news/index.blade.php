@extends('layouts.app')

@section('content')

<div class="container">

    {{-- 1. Header Update --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">
            News Intelligence Dashboard
        </h2>
        <p class="text-muted mb-0">
            Monitor the latest logistics, trade, shipping, and economic news from selected countries.
        </p>
    </div>

    {{-- Sentiment Summary --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">
                News Sentiment Analysis
            </h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="sentiment-card positive">
                        <h6>Positive</h6>
                        <h3>{{ $sentimentPercentage['Positive'] }}%</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sentiment-card neutral">
                        <h6>Neutral</h6>
                        <h3>{{ $sentimentPercentage['Neutral'] }}%</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sentiment-card negative">
                        <h6>Negative</h6>
                        <h3>{{ $sentimentPercentage['Negative'] }}%</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Bungkus Filter dalam Card --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('news') }}">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Select Country
                        </label>
                        <select class="form-select" name="country">
                            <option value="">
                                Choose Country
                            </option>
                            @foreach($countries as $item)
                                <option value="{{ $item->name }}"
                                    {{ $country == $item->name ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($country)

    {{-- 3. Kategori dengan Model Grid Card --}}
    <div class="mb-4">
        <label class="form-label fw-semibold mb-2">Select Category</label>
        @php
            $categories = [
                'Logistics',
                'Trade',
                'Shipping',
                'Economy'
            ];
        @endphp

        <div class="row g-3 mb-4">
            @foreach($categories as $name)
                <div class="col-md-3">
                    <a href="{{ route('news', ['country' => $country, 'category' => $name]) }}" class="text-decoration-none">
                        <div class="card category-card h-100 border-0 shadow-sm {{ $category == $name ? 'active-category' : '' }}">
                            <div class="card-body text-center">
                                <h6 class="fw-semibold mb-0">
                                    {{ $name }}
                                </h6>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    @endif

    {{-- 4. Judul Berita Aktif --}}
    @if($country && $category)
    <div class="mb-4">
        <h4 class="fw-bold">
            Latest News
        </h4>
        <p class="text-muted">
            Showing <strong>{{ $category }}</strong> news from <strong>{{ $country }}</strong>
        </p>
    </div>
    @endif

    {{-- Grid List Berita --}}
    <div class="row">
        @forelse($articles as $article)
        <div class="col-md-6 mb-4">
            {{-- 5. Card Berita Modern --}}
            <div class="card border-0 shadow-sm h-100 news-card">
                @if(!empty($article['image']))
                    <img src="{{ $article['image'] }}" class="card-img-top" style="height:220px; object-fit:cover;">
                @endif
                <div class="card-body d-flex flex-column">
                    @if($article['sentiment'] == 'Positive')

<span class="badge bg-success mb-2">
    🟢 Positive Sentiment
</span>

@elseif($article['sentiment'] == 'Negative')

<span class="badge bg-danger mb-2">
    🔴 Negative Sentiment
</span>

@else

<span class="badge bg-secondary mb-2">
    🟡 Neutral Sentiment
</span>

@endif
                    <p class="text-muted flex-grow-1">
                        {{ \Illuminate\Support\Str::limit($article['description'], 120) }}
                    </p>
                    <div class="small text-secondary mb-3">
                        <div class="mb-1">
                            <i class="bi bi-building me-1"></i>
                            {{ $article['source']['name'] }}
                        </div>
                        <div>
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ \Carbon\Carbon::parse($article['publishedAt'])->format('d M Y') }}
                        </div>
                    </div>
                    <a href="{{ $article['url'] }}" target="_blank" class="btn btn-primary mt-auto">
                        Read Full Article
                    </a>
                </div>
            </div>
        </div>
        @empty
            @if($country && $category)
            <div class="col-12">
                <div class="alert alert-warning border-0 shadow-sm" style="border-radius: 10px;">
                    Tidak ada berita ditemukan untuk kombinasi negara dan kategori ini.
                </div>
            </div>
            @endif
        @endforelse
    </div>

</div>

{{-- 6. Tambahkan Kustom CSS --}}
<style>
    .category-card {
        border-radius: 12px;
        transition: .3s;
        cursor: pointer;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,.15) !important;
    }

    .active-category {
        background: #0d6efd;
        color: #fff;
    }

    .active-category h6 {
        color: #fff;
    }

    .news-card {
        transition: .3s;
        border-radius: 15px;
        overflow: hidden;
    }

    .news-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 18px 35px rgba(0,0,0,.18) !important;
    }

    .card-img-top {
        transition: .4s;
    }

    .news-card:hover .card-img-top {
        transform: scale(1.05);
    }

    .btn {
        border-radius: 10px;
    }

    .card {
        border-radius: 15px;
    }

    /* Sentiment Analysis Styles */
    .sentiment-card {
        padding: 20px;
        border-radius: 15px;
        text-align: center;
    }

    .sentiment-card h3 {
        font-size: 32px;
        font-weight: bold;
    }

    .positive {
        background: #d1e7dd;
        color: #146c43;
    }

    .neutral {
        background: #fff3cd;
        color: #997404;
    }

    .negative {
        background: #f8d7da;
        color: #b02a37;
    }
</style>

@endsection