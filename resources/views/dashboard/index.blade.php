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
                        <h1 class="fw-bold mt-2">{{ $totalCountries }}</h1>

                        <small class="text-success">
                            countries
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
                             Alerts
                        </small>
                    </div>

                    <i class="bi bi-exclamation-triangle-fill text-danger fs-1"></i>

                </div>

            </div>
        </div>
    </div>

    <!-- Users -->
<div class="col-lg-3 col-md-6">
    <a href="{{ route('users.index') }}" class="text-decoration-none text-dark">
    <div class="card shadow-sm border-0 kpi-card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-start">

                <div>
                    <small class="text-muted">Users</small>
                    <h1 class="fw-bold mt-2">{{ $totalUsers }}</h1>

                    <small class="text-success">
                        Registered Users
                    </small>
                </div>

                <i class="bi bi-people-fill text-success fs-1"></i>

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
                        <h1 class="fw-bold mt-2">{{ $activePorts }}</h1>

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