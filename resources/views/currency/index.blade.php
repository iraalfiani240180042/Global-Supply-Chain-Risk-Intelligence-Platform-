@extends('layouts.app')

@section('title', 'Currency Impact Dashboard')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                💱 Currency Impact Dashboard
            </h2>
            <p class="text-muted mb-0">
                Monitor exchange rates and export currency risks.
            </p>
        </div>
    </div>

    {{-- Select Country --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('currency') }}">
                <div class="row align-items-end">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Choose Country
                        </label>
                        <select name="country" class="form-select" onchange="this.form.submit()">
                            <option value="">
                                -- Select Country --
                            </option>
                            @foreach($countries as $item)
                                <option value="{{ $item->id }}"
                                    {{ optional($country)->id == $item->id ? 'selected' : '' }}>
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

    {{-- Summary Cards --}}
    <div class="row mb-4">
        {{-- Card 1: Exchange Rate & Change Percent --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h6 class="text-muted mb-3">
                        💵 Exchange Rate
                    </h6>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark">
                            @if($exchangeRate)
                                1 USD = {{ number_format($exchangeRate, 2) }} {{ $country->currency_code }}
                            @else
                                -
                            @endif
                        </h3>
                        
                        @if($trend->count() >= 2)
                            <small class="mt-2 d-block">
                                @if($changePercent > 0)
                                    <span class="text-success fw-semibold">
                                        ▲ +{{ number_format($changePercent, 2) }}%
                                    </span>
                                @elseif($changePercent < 0)
                                    <span class="text-danger fw-semibold">
                                        ▼ {{ number_format($changePercent, 2) }}%
                                    </span>
                                @else
                                    <span class="text-secondary fw-semibold">
                                        ➜ 0.00%
                                    </span>
                                @endif
                                <span class="text-muted ms-1">
                                    from previous record
                                </span>
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Trend Status --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h6 class="text-muted mb-3">
                        📈 Trend Status
                    </h6>
                    <h3 class="fw-bold mb-0">
                        @if($trendStatus == 'Increasing')
                            <span class="text-success">
                                ↗ Increasing
                            </span>
                        @elseif($trendStatus == 'Decreasing')
                            <span class="text-danger">
                                ↘ Decreasing
                            </span>
                        @else
                            <span class="text-primary">
                                → Stable
                            </span>
                        @endif
                    </h3>
                </div>
            </div>
        </div>

        {{-- Card 3: Risk Level --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h6 class="text-muted mb-3">
                        ⚠ Risk Level
                    </h6>
                    <div>
                        <span class="badge bg-{{ $riskColor ?? 'success' }} fs-6">
                            {{ $risk ?? 'Low Risk' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">
                📈 Exchange Rate Trend
            </h5>
            
            <div style="height: 350px; position: relative;">
                @if($trend->isNotEmpty())
                    <canvas id="currencyChart"></canvas>
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        No historical exchange rate data available yet.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Information & Recommendation --}}
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        💱 Currency Information
                    </h5>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th class="ps-0 text-muted" style="width: 40%;">Country</th>
                            <td class="pe-0 text-dark fw-semibold">{{ $country->name }}</td>
                        </tr>
                        <tr>
                            <th class="ps-0 text-muted">Currency</th>
                            <td class="pe-0 text-dark">{{ $country->currency_name }}</td>
                        </tr>
                        <tr>
                            <th class="ps-0 text-muted">Code</th>
                            <td class="pe-0 text-dark font-monospace">{{ $country->currency_code }}</td>
                        </tr>
                        <tr>
                            <th class="ps-0 text-muted">Symbol</th>
                            <td class="pe-0 text-dark">{{ $country->currency_symbol }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        📌 Currency Recommendation
                    </h5>
                    
                    @if(isset($recommendation))
                        <div class="alert alert-{{ $riskColor ?? 'success' }} d-flex align-items-start mb-0" role="alert">
                            <span class="fs-4 me-3">
                                @if(($riskColor ?? 'success') === 'success')
                                    ✔
                                @elseif($riskColor === 'warning')
                                    ⚠
                                @else
                                    ❌
                                @endif
                            </span>
                            <div>
                                <h6 class="fw-bold alert-heading mb-1">Export Action Plan:</h6>
                                <p class="mb-0">{{ $recommendation }}</p>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            No recommendations calculated. Synchronize your logs to display market recommendations.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @endif

</div>

@endsection

@push('scripts')
{{-- Load CDN library Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if($country && $trend->isNotEmpty())
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const labels = @json(
            $trend->pluck('recorded_at')->map(function($date){
                return \Carbon\Carbon::parse($date)->format('d M');
            })
        );

        const rates = @json(
            $trend->pluck('exchange_rate')
        );

        const ctx = document.getElementById('currencyChart');

        if(ctx){
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Exchange Rate (USD to {{ $country->currency_code }})',
                        data: rates,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.05)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('en-US', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endif
@endpush