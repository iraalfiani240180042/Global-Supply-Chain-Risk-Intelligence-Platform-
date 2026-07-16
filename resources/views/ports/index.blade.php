@extends('layouts.app')

@section('title','Ports')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Ports Management</h2>

    <div>

        <a href="{{ route('ports.sync') }}" class="btn btn-success">
            <i class="bi bi-arrow-repeat"></i>
            Import Dataset
        </a>

        <a href="{{ route('ports.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Add Port
        </a>

    </div>

</div>

@if(session('success'))

<div class="alert alert-success">
    {{ session('success') }}
</div>

@endif

<div class="card shadow-sm border-0">

    <div class="card-body">

        <table class="table table-hover align-middle">

            <thead class="table-light">

                <tr>

                    <th>No</th>
                    <th>Port</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Status</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th width="120">Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($ports as $port)

                <tr>

                    <td>{{ $ports->firstItem() + $loop->index }}</td>

                    <td>{{ $port->name }}</td>

                    <td>{{ $port->city }}</td>

                    <td>{{ $port->country->name ?? '-' }}</td>

                    <td>

                        <span class="badge bg-success">
                            {{ $port->status->status ?? '-' }}
                        </span>

                    </td>

                    <td>{{ $port->latitude }}</td>

                    <td>{{ $port->longitude }}</td>

                    <td>

                        <a href="#" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <a href="#" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </a>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="8" class="text-center py-4">

                        No ports available.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

        <div class="mt-3">

            {{ $ports->links() }}

        </div>

    </div>

</div>

@endsection