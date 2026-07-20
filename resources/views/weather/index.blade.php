@extends('layouts.app')

@section('title', 'Weather Dashboard')

@section('content')

{{-- Custom Style Terintegrasi --}}
<style>
    /* Dashboard Card */
    .dashboard-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0,0,0,.08);
        transition: .35s ease;
        overflow: hidden;
    }

    .dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 45px rgba(13,110,253,.15);
    }

    /* Weather Summary */
    .dashboard-card h3 {
        font-size: 1.7rem;
    }

    .dashboard-card h6 {
        font-weight: 600;
    }

    /* Badge */
    .badge {
        padding: 8px 16px;
        border-radius: 50px;
        font-size: .85rem;
    }

    /* Select */
    .form-select {
        min-height: 48px;
        border-radius: 12px;
    }

    /* List */
    .list-group-item {
        background: transparent;
    }
</style>

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">
                Weather & Logistics Dashboard
            </h2>
            <p class="text-muted mb-0">
                Monitor weather conditions for export destination analysis.
            </p>
        </div>
    </div>

    {{-- Select Country (Auto Submit on Change) --}}
    <div class="card dashboard-card h-100 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('weather') }}">
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Select Country
                        </label>
                        <select name="country" class="form-select" onchange="this.form.submit()">
                            <option value="">
                                -- Choose Country --
                            </option>
                            @foreach($countries as $item)
                                <option value="{{ $item->id }}"
                                    {{ request('country') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($country)

    {{-- Current Weather --}}

    {{-- Baris 1 --}}
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card dashboard-card h-100 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Temperature</h6>
                    <h3 class="fw-bold text-danger mb-1">
                        {{ $weather['temperature'] ?? '--' }}°C
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Humidity</h6>
                    <h3 class="fw-bold text-info mb-1">
                        {{ $weather['humidity'] ?? '--' }}%
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Wind Speed</h6>
                    <h3 class="fw-bold text-success mb-1">
                        {{ $weather['wind_speed'] ?? '--' }} km/h
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Baris 2 --}}
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card dashboard-card h-100 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Rainfall</h6>

                    @if($weather)
                        @if($weather['rainfall'] > 10)
                            <h3 class="fw-bold text-danger">Heavy Rain</h3>
                        @elseif($weather['rainfall'] > 0)
                            <h3 class="fw-bold text-warning">Light Rain</h3>
                        @else
                            <h3 class="fw-bold text-success">No Rain</h3>
                        @endif

                        <small class="text-muted">
                            {{ $weather['rainfall'] }} mm
                        </small>
                    @endif

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Storm</h6>

                    @if($weather)
                        @if($weather['storm'])
                            <h3 class="fw-bold text-danger">High Risk</h3>
                            <span class="badge bg-danger">Storm Detected</span>
                        @elseif($weather['wind_speed'] >= 30)
                            <h3 class="fw-bold text-warning">Moderate</h3>
                            <span class="badge bg-warning text-dark">Strong Wind</span>
                        @else
                            <h3 class="fw-bold text-success">Safe</h3>
                            <span class="badge bg-success">No Storm</span>
                        @endif
                    @endif

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Condition</h6>
                    <h3 class="fw-bold text-dark">
                        {{ $weather['condition'] ?? '--' }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Map + Chart --}}
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h5 class="fw-bold">
                        Global Weather Monitoring
                    </h5>
                    <hr>
                    {{-- OpenStreetMap Container --}}
                    <div id="weatherMap" style="height:400px; border-radius:8px; position: relative; overflow: hidden;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h5 class="fw-bold">
                        Temperature Trend
                    </h5>
                    <hr>
                    <div style="height:400px; position: relative;">
                        @if(!empty($temperatureTrend))
                            <canvas id="temperatureChart"></canvas>
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                No temperature trend data available.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Logistics + Recommendation --}}
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h5 class="fw-bold">
                        Logistics Impact
                    </h5>
                    <hr>
                    @if($logisticsStatus)
                        <div class="alert alert-{{ $logisticsColor }} rounded-4 border-0 shadow-sm mb-0">
                            <h4 class="alert-heading fw-bold">{{ $logisticsStatus }}</h4>
                            <p class="mb-0">
                                Logistics assessment is currently flagged as <strong>{{ $logisticsStatus }}</strong> based on the real-time weather metrics collected.
                            </p>
                        </div>
                    @else
                        <p class="text-muted">
                            Logistics analysis is unavailable. Make sure weather logs are synchronized.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h5 class="fw-bold">
                        Weather Recommendation
                    </h5>
                    <hr>
                    @if(!empty($weatherRecommendations))
                        <ul class="list-group list-group-flush">
                            @foreach($weatherRecommendations as $rec)
                                <li class="list-group-item border-0 px-0 py-2">
                                    <i class="fas fa-check-circle text-{{ $logisticsColor ?? 'primary' }} me-2"></i>
                                    {{ $rec }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">
                            Weather recommendations will appear here once destination metrics are established.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @endif

</div>

{{-- Scripts for Mapping and Charts --}}
@if($country)
    {{-- Leaflet Assets for Map Integration --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    {{-- ChartJS Asset --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // 1. Map Initialization
            const lat = {{ $country->latitude ?? 0 }};
            const lng = {{ $country->longitude ?? 0 }};
            
            if (lat !== 0 || lng !== 0) {
                const map = L.map('weatherMap').setView([lat, lng], 5);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Popup lengkap info cuaca (Tanpa Emoji)
                let popup = `
                    <b>{{ $country->name }}</b><br>
                    Temperature : {{ $weather['temperature'] ?? '--' }} °C<br>
                    Humidity : {{ $weather['humidity'] ?? '--' }} %<br>
                    Wind Speed : {{ $weather['wind_speed'] ?? '--' }} km/h<br>
                    Rainfall : {{ $weather['rainfall'] ?? 0 }} mm<br>
                    Storm : {{ !empty($weather['storm']) ? 'Yes' : 'No' }}
                `;

                L.marker([lat, lng]).addTo(map)
                    .bindPopup(popup)
                    .openPopup();

                // Tambahkan Lingkaran Warna berdasarkan kondisi cuaca ekstrim
                const isStorm = {{ !empty($weather['storm']) ? 'true' : 'false' }};
                const rainfall = {{ $weather['rainfall'] ?? 0 }};

                if (isStorm) {
                    L.circle([lat, lng], {
                        radius: 50000,
                        color: 'red',
                        fillColor: 'red',
                        fillOpacity: 0.4
                    }).addTo(map);
                } else if (rainfall > 2.5) { 
                    L.circle([lat, lng], {
                        radius: 30000,
                        color: 'blue',
                        fillColor: '#4da6ff',
                        fillOpacity: 0.35
                    }).addTo(map);
                }

            } else {
                document.getElementById('weatherMap').innerHTML = 
                    '<div class="d-flex align-items-center justify-content-center h-100 text-muted">Geographical coordinates missing for map projection.</div>';
            }

            // 2. Temperature Trend Chart Initialization
            const trendData = @json($temperatureTrend);
            
            if (trendData && trendData.length > 0) {
                const labels = trendData.map(item => item.time);
                const values = trendData.map(item => item.temperature);

                const ctx = document.getElementById('temperatureChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Hourly Temperature (°C)',
                            data: values,
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.05)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                title: { display: true, text: 'Temperature (°C)' }
                            },
                            x: {
                                title: { display: true, text: 'Time (24h Format)' }
                            }
                        }
                    }
                });
            }
        }); 
    </script>
@endif

@endsection