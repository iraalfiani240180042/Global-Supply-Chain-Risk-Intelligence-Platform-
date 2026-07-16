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
                            Overall export destination risk based on GDP, inflation, weather, wind speed and exchange rate.
                        </p>
                    @else
                        <h2 class="fw-bold text-warning">--</h2>
                        <p class="text-muted">Overall export destination risk based on market data availability.</p>
                    @endif
                </div>
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
                                    <img src="{{ $item['image'] }}" 
                                         class="img-fluid rounded border shadow-sm" 
                                         style="height:110px; width:100%; object-fit:cover;" 
                                         alt="{{ $item['title'] }}">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded border" style="height:110px; width:100%;">
                                        <i class="bi bi-image text-muted fs-2"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-9">
                                <h5 class="fw-bold text-dark mb-1">
                                    {{ $item['title'] }}
                                </h5>
                                <small class="text-muted d-block mb-2">
                                    <span class="badge bg-secondary me-1">{{ $item['source']['name'] ?? 'Unknown Source' }}</span>
                                    • {{ \Carbon\Carbon::parse($item['publishedAt'])->format('d M Y') }}
                                </small>
                                <p class="text-secondary mb-3">
                                    {{ \Illuminate\Support\Str::limit($item['description'] ?? '', 120) }}
                                </p>
                                <a href="{{ $item['url'] }}" target="_blank" class="btn btn-primary btn-sm rounded">
                                    Read More
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">No news available.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- GDP Chart --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <h4>
                📈 GDP Trend
            </h4>
            <hr>
            <div style="height:350px; position: relative;">
                <canvas id="gdpChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Recommendation --}}
    <div class="card shadow-sm border-0 mt-4 mb-5">
        <div class="card-body">
            <h4>📌 Export Recommendation</h4>
            <hr>
            <ul class="mb-0">
                @if($gdp)
                    <li class="mb-2"><strong>Economy Analysis:</strong> Market size is substantial with a current GDP of ${{ number_format($gdp, 0) }} USD.</li>
                @else
                    <li class="mb-2"><strong>Economy Analysis:</strong> Market scale indicators are currently limited.</li>
                @endif

                @if(isset($weather))
                    <li class="mb-2"><strong>Weather Recommendation:</strong> Present temperature is {{ $weather['temperature_2m'] }}°C ({{ $weather['description'] ?? 'Clear' }}). Ideal for scheduling typical logistics operations.</li>
                @else
                    <li class="mb-2"><strong>Weather Recommendation:</strong> Local weather forecast data is currently unavailable. Check regional channels before dispatching seasonal items.</li>
                @endif

                @if($exchangeRate)
                    <li class="mb-2"><strong>Currency Recommendation:</strong> Exchange rate is holding steady at 1 USD = {{ number_format($exchangeRate, 2) }} {{ $country->currency_code }}. Keep tabs on conversion volatility.</li>
                @else
                    <li class="mb-2"><strong>Currency Recommendation:</strong> Live exchange rate data could not be parsed.</li>
                @endif

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
@if(isset($country) && !empty($gdpTrend))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const rawData = @json($gdpTrend);
            
            const labels = rawData.map(item => item.year);
            const dataValues = rawData.map(item => item.value);

            const ctx = document.getElementById('gdpChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'GDP (Billion USD)',
                        data: dataValues,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#0d6efd',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Billion USD'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Year'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endif

@endsection