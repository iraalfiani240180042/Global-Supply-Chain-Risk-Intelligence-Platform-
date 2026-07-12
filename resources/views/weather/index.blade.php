@extends('layouts.app')

@section('title', 'Weather')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Weather Management</h2>

    <a href="{{ route('weather.sync') }}" class="btn btn-success">
        <i class="bi bi-arrow-repeat"></i>
        Sync Weather
    </a>

</div>

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

<div class="card shadow-sm border-0">

    <div class="card-body">

        <table class="table table-hover align-middle">

            <thead class="table-light">

                <tr>

                    <th>No</th>
                    <th>Country</th>
                    <th>Weather</th>
                    <th>Temperature</th>
                    <th>Humidity</th>
                    <th>Wind Speed</th>
                    <th>Recorded At</th>

                </tr>

            </thead>

            <tbody>

                @forelse($weatherLogs as $log)

                <tr>

                    <td>{{ $weatherLogs->firstItem() + $loop->index }}</td>

                    <td>{{ $log->country->name }}</td>

                    <td>
                        <span class="badge bg-info">
                            {{ $log->weatherType->name }}
                        </span>
                    </td>

                    <td>{{ $log->temperature }} °C</td>

                    <td>{{ $log->humidity }} %</td>

                    <td>{{ $log->wind_speed }} km/h</td>

                    <td>{{ $log->recorded_at->format('d M Y H:i') }}</td>

                </tr>

                @empty

                <tr>

                    <td colspan="7" class="text-center py-4">

                        No weather data available.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

        <div class="mt-3">

            {{ $weatherLogs->links() }}

        </div>

    </div>

</div>

@endsection