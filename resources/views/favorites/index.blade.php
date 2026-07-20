@extends('layouts.app')

@section('title','Favorite Countries')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h2 class="fw-bold">
            Favorite Countries
        </h2>

        <p class="text-muted">
            List of countries you have saved.
        </p>
    </div>

    <div class="row">

        @forelse($favorites as $country)

        <div class="col-lg-4 mb-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <img src="{{ $country->flag }}"
                        width="80"
                        class="mb-3 rounded border">

                    <h4>{{ $country->name }}</h4>

                    <p class="text-muted">
                        {{ $country->capital }}
                    </p>

                    <span class="badge bg-primary">
                        {{ $country->region->name ?? '-' }}
                    </span>

                    <hr>

                    <a href="{{ route('countries.show',$country->id) }}"
                        class="btn btn-primary btn-sm">

                        View Details

                    </a>

                </div>

            </div>

        </div>

        @empty

        <div class="col-12">

            <div class="alert alert-info">

                You haven't added any favorite countries yet.

            </div>

        </div>

        @endforelse

    </div>

</div>

@endsection