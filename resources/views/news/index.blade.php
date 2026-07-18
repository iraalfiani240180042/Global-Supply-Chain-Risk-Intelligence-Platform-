@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">News Intelligence</h2>

    <form method="GET" action="{{ route('news') }}">

        <div class="row mb-4">

            <div class="col-md-5">

                <label class="form-label">
                    Pilih Negara
                </label>

                <select class="form-select" name="country" id="country">

                    <option value="">-- Pilih Negara --</option>

                    @foreach($countries as $item)

                        <option value="{{ $item->name }}"
                            {{ $country==$item->name ? 'selected' : '' }}>

                            {{ $item->name }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    Pilih Negara
                </button>
            </div>

        </div>

    </form>

    @if($country)

    <div class="mb-4">

        <label class="form-label">
            Pilih Kategori
        </label>

        @php
            $categories = [
                'Logistics' => '🚚',
                'Trade' => '📦',
                'Shipping' => '🚢',
                'Economy' => '💰'
            ];
        @endphp

        <div class="d-flex gap-3 flex-wrap">
            @foreach($categories as $name => $icon)

                <a href="{{ route('news', [
                    'country' => $country,
                    'category' => $name
                ]) }}"
                class="btn {{ $category == $name ? 'btn-primary' : 'btn-outline-primary' }}">

                    {{ $icon }} {{ $name }}

                </a>

            @endforeach
        </div>

    </div>

    @endif

    @if($country && $category)

    <h4 class="mb-4">

        News :
        <span class="text-primary">

            {{ $country }}

        </span>

        -

        <span class="text-success">

            {{ $category }}

        </span>

    </h4>

    @endif

    <div class="row">

        @forelse($articles as $article)

        <div class="col-md-6 mb-4">

            <div class="card h-100">

                @if(!empty($article['image']))
                    <img src="{{ $article['image'] }}"
                         class="card-img-top"
                         alt="{{ $article['title'] }}">
                @endif

                <div class="card-body">

                    <h5>{{ $article['title'] }}</h5>

                    <p>{{ $article['description'] }}</p>

                    <small class="text-muted">

                        {{ $article['source']['name'] }}

                    </small>

                    <br>

                    <small class="text-muted">

                        {{ \Carbon\Carbon::parse($article['publishedAt'])->format('d M Y') }}

                    </small>

                    <br><br>

                    <a href="{{ $article['url'] }}"
                       target="_blank"
                       class="btn btn-success">

                        Read More

                    </a>

                </div>

            </div>

        </div>

        @empty

            <div class="col-12">

                <div class="alert alert-warning">

                    Tidak ada berita ditemukan.

                </div>

            </div>

        @endforelse

    </div>

</div>

@endsection