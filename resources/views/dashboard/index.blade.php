@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- HERO -->
<div class="hero">

    <div class="hero-left">

        <span class="badge bg-light text-primary mb-3">
            AI Risk Monitoring
        </span>

        <h1>
            Welcome Back,
            {{ auth()->user()->name }} 👋
        </h1>

        <p>
            Monitor weather, currency, logistics,
            ports and world events in one intelligent dashboard.
        </p>

        <button class="btn btn-light mt-3">
            <i class="bi bi-bar-chart-line"></i>
            Explore Analytics
        </button>

    </div>

</div>

<!-- KPI -->
<div class="row mt-4 g-4">

    <div class="col-lg-3">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>
                        <small>Countries</small>
                        <h2>195</h2>
                    </div>

                    <i class="bi bi-globe2 text-primary"></i>

                </div>

                <small class="text-success">
                    ▲ +2 Today
                </small>

            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>
                        <small>High Risk</small>
                        <h2>12</h2>
                    </div>

                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>

                </div>

                <small class="text-danger">
                    ▲ 3 New Alerts
                </small>

            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>
                        <small>News Today</small>
                        <h2>158</h2>
                    </div>

                    <i class="bi bi-newspaper text-warning"></i>

                </div>

                <small class="text-success">
                    Updated 5 mins ago
                </small>

            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>
                        <small>Active Ports</small>
                        <h2>83</h2>
                    </div>

                    <i class="bi bi-truck text-info"></i>

                </div>

                <small class="text-success">
                    All Operational
                </small>

            </div>
        </div>
    </div>

</div>

<!-- QUICK ACCESS -->
<div class="card mt-4 border-0 shadow-sm">

    <div class="card-body">

        <h5 class="mb-4">
            Quick Access
        </h5>

        <div class="row text-center">

            <div class="col">
                <button class="btn btn-outline-primary w-100">
                    <i class="bi bi-globe2"></i><br>
                    Countries
                </button>
            </div>

            <div class="col">
                <button class="btn btn-outline-success w-100">
                    <i class="bi bi-cloud-sun"></i><br>
                    Weather
                </button>
            </div>

            <div class="col">
                <button class="btn btn-outline-warning w-100">
                    <i class="bi bi-currency-exchange"></i><br>
                    Currency
                </button>
            </div>

            <div class="col">
                <button class="btn btn-outline-danger w-100">
                    <i class="bi bi-newspaper"></i><br>
                    News
                </button>
            </div>

        </div>

    </div>

</div>

<!-- MAP + WEATHER -->
<div class="row mt-4">

    <!-- MAP -->
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h5>
                        <i class="bi bi-globe-americas"></i>
                        Global Risk Map
                    </h5>

                    <span class="badge bg-success">
                        Live Monitoring
                    </span>

                </div>

                <div id="worldMap" class="world-map"></div>

                <div class="mt-3 d-flex gap-4">

                    <span>
                        <span class="badge bg-success">&nbsp;</span>
                        Low Risk
                    </span>

                    <span>
                        <span class="badge bg-warning">&nbsp;</span>
                        Medium Risk
                    </span>

                    <span>
                        <span class="badge bg-danger">&nbsp;</span>
                        High Risk
                    </span>

                </div>

            </div>

        </div>

    </div>

    <!-- WEATHER -->
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <h5>
                    🌦 Today's Weather
                </h5>

                <hr>

                <h2>
                    {{ $weather['current']['temperature_2m'] }}°C
                </h2>

                <p>
                    💧 Humidity :
                    {{ $weather['current']['relative_humidity_2m'] }}%
                </p>

                <p>
                    🌬 Wind :
                    {{ $weather['current']['wind_speed_10m'] }} km/h
                </p>

            </div>

        </div>

    </div>

</div>

@endsection