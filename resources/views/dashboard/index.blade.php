@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="row g-4">

    <!-- Countries -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <small class="text-muted">Countries</small>
                        <h1 class="fw-bold mt-2">195</h1>

                        <small class="text-success">
                            ▲ +2 Today
                        </small>
                    </div>

                    <i class="bi bi-globe2 text-primary fs-1"></i>

                </div>

            </div>
        </div>
    </div>

    <!-- High Risk -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <small class="text-muted">High Risk</small>
                        <h1 class="fw-bold mt-2">12</h1>

                        <small class="text-danger">
                            ▲ 3 New Alerts
                        </small>
                    </div>

                    <i class="bi bi-exclamation-triangle-fill text-danger fs-1"></i>

                </div>

            </div>
        </div>
    </div>

    <!-- News -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <small class="text-muted">News Today</small>
                        <h1 class="fw-bold mt-2">158</h1>

                        <small class="text-success">
                            Updated 5 mins ago
                        </small>
                    </div>

                    <i class="bi bi-newspaper text-warning fs-1"></i>

                </div>

            </div>
        </div>
    </div>

    <!-- Ports -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <small class="text-muted">Active Ports</small>
                        <h1 class="fw-bold mt-2">83</h1>

                        <small class="text-success">
                            All Operational
                        </small>
                    </div>

                    <i class="bi bi-truck text-info fs-1"></i>

                </div>

            </div>
        </div>
    </div>

</div>

@endsection