@extends('layouts.app')

@section('title','Countries')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>
        Countries Management
    </h2>

    <div>

        <a href="{{ route('countries.sync') }}" class="btn btn-success">
            <i class="bi bi-arrow-repeat"></i>
            Sync API
        </a>

        <a href="{{ route('countries.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Add Country
        </a>

    </div>

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
                    <th>Flag</th>
                    <th>Country</th>
                    <th>Capital</th>
                    <th>Region</th>
                    <th>Population</th>
                    <th width="120">Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($countries as $country)

                <tr>

                    <td>{{ $countries->firstItem() + $loop->index }}</td>

                    <td>

                        @if($country->flag)

                            <img src="{{ $country->flag }}"
                                 width="40"
                                 class="rounded border">

                        @else

                            -

                        @endif

                    </td>

                    <td>{{ $country->name }}</td>

                    <td>{{ $country->capital }}</td>

                    <td>{{ $country->region->name ?? '-' }}</td>

                    <td>{{ number_format($country->population) }}</td>

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

                    <td colspan="7" class="text-center py-4">

                        No countries available.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>
<div class="mt-3">

            {{ $countries->links() }}

        </div> 
    </div>

</div>

@endsection