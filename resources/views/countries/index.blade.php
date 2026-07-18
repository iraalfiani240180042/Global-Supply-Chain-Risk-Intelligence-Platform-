@extends('layouts.app')

@section('title', 'Country Insights')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-content d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                Country Insights
            </h2>
            <p class="text-muted mb-0">
                Analyze export destinations using real-time country information.
            </p>
        </div>

        <div>
            <a href="{{ route('countries.sync') }}" class="btn btn-success">
                <i class="bi bi-arrow-repeat"></i> Sync Countries
            </a>
            <a href="{{ route('countries.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Country
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Search Country --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <span class="badge bg-primary mb-2">
                Global Export Analysis
            </span>
            <h3 class="fw-bold">
                Export Destination Analysis
            </h3>
            <p class="text-muted">
                Evaluate export destinations through economic indicators, weather conditions, currency trends and country risk assessments.
            </p>

            <select 
                class="form-select form-select-lg" 
                onchange="if(this.value!='') window.location=this.value">
                <option value="">
                    🌍 Select Country
                </option>
                @foreach($countries as $item)
                    <option 
                        value="{{ route('countries.show', $item->id) }}" 
                        {{ isset($country) && $country->id == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @if(isset($country))

    {{-- Country Information --}}
    <div class="row g-3">
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <img src="{{ $country->flag }}" class="img-fluid rounded border mb-3" style="height:70px">
                    <h6 class="text-muted">Country</h6>
                    <h4 class="fw-bold">
                        {{ $country->name }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">🌍 Region</h6>
                    <h4 class="fw-bold">
                        {{ $country->region->name ?? '-' }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">🏛 Capital</h6>
                    <h4 class="fw-bold">
                        {{ $country->capital }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">👥 Population</h6>
                    <h4 class="fw-bold">
                        {{ number_format($country->population) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Economic Indicators --}}
    <div class="row g-3 mt-1">
        {{-- Currency Card --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">💵 Currency</h6>
                    <h4 class="fw-bold text-success">
                        {{ $country->currency_code }}
                    </h4>
                    <p class="mb-1">
                        {{ $country->currency_name }}
                    </p>
                    <small class="text-muted">
                        {{ $country->currency_symbol }}
                    </small>
                </div>
            </div>
        </div>

        {{-- GDP Card --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">📈 GDP</h6>
                    @if($gdp)
                        <h4 class="fw-bold text-primary">
                            ${{ number_format($gdp, 0) }}
                        </h4>
                    @else
                        <h4 class="fw-bold text-primary">--</h4>
                    @endif
                    <small class="text-muted">
                        Gross Domestic Product (Current US$)
                    </small>
                </div>
            </div>
        </div>

        {{-- Inflation Card --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">📉 Inflation</h6>
                    @if($inflation)
                        <h4 class="fw-bold text-danger">
                            {{ number_format($inflation, 2) }}%
                        </h4>
                    @else
                        <h4 class="fw-bold text-danger">--</h4>
                    @endif
                    <small class="text-muted">
                        Annual Inflation Rate
                    </small>
                </div>
            </div>
        </div>

        {{-- Weather Component --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">🌦 Weather</h6>
                    @if(isset($weather))
                        <h3 class="fw-bold text-warning mb-1">
                            {{ $weather['temperature_2m'] }}°C
                        </h3>
                        <small class="text-muted d-block">
                            Condition: {{ $weather['description'] ?? 'Unknown' }}
                        </small>
                        <small class="text-muted d-block">
                            Humidity : {{ $weather['relative_humidity_2m'] }}%
                        </small>
                        <small class="text-muted d-block">
                            Wind : {{ $weather['wind_speed_10m'] }} km/h
                        </small>
                    @else
                        <h4 class="fw-bold text-warning">--</h4>
                        <small class="text-muted">
                            Weather unavailable
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Exchange Rate & Risk --}}
    <div class="row g-3 mt-1">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h4 class="mb-3">💱 Exchange Rate</h4>
                    <h2 class="fw-bold text-success">
                        @if($exchangeRate)
                            1 USD = {{ number_format($exchangeRate, 2) }} {{ $country->currency_code }}
                        @else
                            --
                        @endif
                    </h2>
                    <p class="text-muted">Live exchange rate against USD.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h4 class="mb-3">⚠ Risk Analysis</h4>
                    @if(isset($riskLevel))
                        <h2 class="fw-bold text-{{ $riskColor }}">
                            {{ $riskLevel }}
                        </h2>

                        <div class="progress mb-3" style="height:10px;">
                            <div
                                class="progress-bar bg-{{ $riskColor }}"
                                style="width: {{ $riskScore }}%">
                            </div>
                        </div>

                        <h5 class="fw-bold">
                            {{ $riskScore }}/100
                        </h5>

                        <p class="text-muted">
                            Overall export destination risk based on weather conditions, inflation, exchange rate, and news sentiment.
                        </p>
                    @else
                        <h2 class="fw-bold text-warning">--</h2>
                        <p class="text-muted">Overall export destination risk based on market data availability.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- News Sentiment Card --}}
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>📰 News Sentiment</h5>
                    <h3 class="fw-bold">
                        {{ $newsSentiment ?? 'Neutral' }}
                    </h3>
                    <p class="text-muted mb-0">
                        Positive News : {{ $positiveNews ?? 0 }} | Negative News : {{ $negativeNews ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- GDP Chart --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <h4>📈 GDP Trend</h4>
            <hr>
            <div style="height:350px; position: relative;">
                <canvas id="gdpChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Inflation Trend --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <h4>📉 Inflation Trend</h4>
            <hr>
            <div style="height:350px; position: relative;">
                <canvas id="inflationChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Currency Trend --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <h4>💱 Currency Trend</h4>
            <hr>
            <div style="height:350px; position: relative;">
                <canvas id="currencyChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Risk Trend --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <h4>⚠ Risk Trend</h4>
            <hr>
            <div style="height:350px; position: relative;">
                <canvas id="riskChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Latest News --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <h4 class="mb-3">📰 Latest News</h4>
            <hr>

            <div class="list-group list-group-flush">
                @forelse($news as $item)
                    <div class="list-group-item px-0 py-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                @if(!empty($item['image']))
                                    <img
                                        src="{{ $item['image'] }}"
                                        class="img-fluid rounded border shadow-sm"
                                        style="height:110px;width:100%;object-fit:cover;"
                                    >
                                @ite
                                @else
                                    <div class="bg-light rounded border d-flex justify-content-center align-items-center"
                                         style="height:110px;">
                                        <i class="bi bi-image fs-2 text-muted"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-9">
                                <h5 class="fw-bold">
                                    {{ $item['title'] }}
                                </h5>

                                <small class="text-muted">
                                    <span class="badge bg-secondary">
                                        {{ $item['source']['name'] ?? 'Unknown' }}
                                    </span>
                                    •
                                    {{ \Carbon\Carbon::parse($item['publishedAt'])->format('d M Y') }}
                                </small>

                                <p class="mt-2 text-secondary">
                                    {{ \Illuminate\Support\Str::limit($item['description'] ?? '',120) }}
                                </p>

                                <a
                                    href="{{ $item['url'] }}"
                                    target="_blank"
                                    class="btn btn-primary btn-sm"
                                >
                                    Read More
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">
                            No news available.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recommendation --}}
    <div class="card shadow-sm border-0 mt-4 mb-5">
        <div class="card-body">
            <h4>📌 Export Recommendation</h4>
            <hr>
            <ul class="mb-0">
                <li class="mb-2">
                    <strong>Weather:</strong>
                    {{ $weather['description'] ?? '-' }}
                </li>

                <li class="mb-2">
                    <strong>Inflation:</strong>
                    {{ $inflation ? number_format($inflation,2).'%' : '-' }}
                </li>

                <li class="mb-2">
                    <strong>Exchange Rate:</strong>
                    1 USD = {{ $exchangeRate ? number_format($exchangeRate,2) : '-' }} {{ $country->currency_code }}
                </li>

                <li class="mb-2">
                    <strong>News Sentiment:</strong>
                    {{ $newsSentiment ?? 'Neutral' }}
                </li>

                <li><strong>Risk Recommendation:</strong> Monitor the latest local updates in the news stream above to watch out for real-time customs adjustments or distribution bottlenecks.</li>
            </ul>
        </div>
    </div>

    @else

    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/854/854878.png" width="90" class="mb-3">
            <h3 class="fw-bold">Select a Country</h3>
            <p class="text-muted">Choose a country above to view export analysis.</p>
        </div>
    </div>

    @endif

</div>

{{-- External Libraries & Chart Script --}}
@if(isset($country))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            
            // ==========================
            // GDP Trend
            // ==========================
            @if(!empty($gdpTrend))
                new Chart(document.getElementById('gdpChart'), {
                    type: 'line',
                    data: {
                        labels: @json(array_column($gdpTrend, 'year')),
                        datasets: [{
                            label: 'GDP (Billion USD)',
                            data: @json(array_column($gdpTrend, 'value')),
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, .15)',
                            borderWidth: 3,
                            fill: true,
                            tension: .35,
                            pointBackgroundColor: '#0d6efd',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: false, title: { display: true, text: 'Billion USD' } },
                            x: { title: { display: true, text: 'Year' } }
                        }
                    }
                });
            @endif

            // ==========================
            // Inflation Trend
            // ==========================
            @if(!empty($inflationTrend))
                new Chart(document.getElementById('inflationChart'), {
                    type: 'line',
                    data: {
                        labels: @json(array_column($inflationTrend, 'year')),
                        datasets: [{
                            label: 'Inflation %',
                            data: @json(array_column($inflationTrend, 'value')),
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, .15)',
                            borderWidth: 3,
                            fill: true,
                            tension: .35,
                            pointBackgroundColor: '#dc3545',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: false, title: { display: true, text: 'Percentage (%)' } },
                            x: { title: { display: true, text: 'Year' } }
                        }
                    }
                });
            @endif

            // ==========================
            // Currency Trend
            // ==========================
            @if(!empty($currencyTrend))
                new Chart(document.getElementById('currencyChart'), {
                    type: 'line',
                    data: {
                        labels: @json(array_column($currencyTrend, 'day')),
                        datasets: [{
                            label: 'Exchange Rate',
                            data: @json(array_column($currencyTrend, 'value')),
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25, 135, 84, .15)',
                            borderWidth: 3,
                            fill: true,
                            tension: .35,
                            pointBackgroundColor: '#198754',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: false, title: { display: true, text: 'Value against USD' } },
                            x: { title: { display: true, text: 'Day' } }
                        }
                    }
                });
            @endif

            // ==========================
            // Risk Trend
            // ==========================
            @if(!empty($riskTrend))
                new Chart(document.getElementById('riskChart'), {
                    type: 'bar',
                    data: {
                        labels: @json(array_column($riskTrend, 'year')),
                        datasets: [{
                            label: 'Risk Score',
                            data: @json(array_column($riskTrend, 'score')),
                            backgroundColor: '#ffc107',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, max: 100, title: { display: true, text: 'Risk Score (0-100)' } },
                            x: { title: { display: true, text: 'Year' } }
                        }
                    }
                });
            @endif

        });
    </script>
@endif

@endsection