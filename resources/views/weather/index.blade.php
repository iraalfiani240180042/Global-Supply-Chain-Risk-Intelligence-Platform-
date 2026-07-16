@extends('layouts.app')

@section('title', 'Weather Dashboard')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">
                走 Weather & Logistics Dashboard
            </h2>
            <p class="text-muted mb-0">
                Monitor weather conditions for export destination analysis.
            </p>
        </div>


    </div>

    {{-- Select Country (Auto Submit on Change) --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('weather') }}">
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            🌍 Select Country
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
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2"> Pergantian Suhu 🌡 Temperature</h6>
                    <h3 class="fw-bold text-danger mb-0">
                        {{ $weather ? $weather['temperature'] . '°C' : '--' }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">💧 Humidity</h6>
                    <h3 class="fw-bold text-info mb-0">
                        {{ $weather ? $weather['humidity'] . '%' : '--' }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">💨 Wind Speed</h6>
                    <h3 class="fw-bold text-success mb-0">
                        {{ $weather ? $weather['wind_speed'] . ' km/h' : '--' }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">🌤 Condition</h6>
                    <h5 class="fw-bold mb-0 text-dark">
                        {{ $weather ? $weather['condition'] : '--' }}
                    </h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Map + Chart --}}
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold">
                        🌍 Global Weather Monitoring
                    </h5>
                    <hr>
                    {{-- OpenStreetMap Container --}}
                    <div id="weatherMap" style="height:400px; border-radius:8px; position: relative; overflow: hidden;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold">
                        📈 Temperature Trend
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
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold">
                        🚢 Logistics Impact
                    </h5>
                    <hr>
                    @if($logisticsStatus)
                        <div class="alert alert-{{ $logisticsColor }} mb-0">
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
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold">
                        📌 Weather Recommendation
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

                L.marker([lat, lng]).addTo(map)
                    .bindPopup('<b>{{ $country->name }}</b><br>Lat: ' + lat + ', Lng: ' + lng)
                    .openPopup();
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