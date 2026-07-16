@extends('layouts.app')

@section('title','Add Port')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Add Port</h2>

        <a href="{{ route('ports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>

    </div>

    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <div class="card shadow-sm">

        <div class="card-body">

            <form action="{{ route('ports.store') }}" method="POST">

                @csrf

                <div class="mb-3">
                    <label class="form-label">Country</label>

                    <select name="country_id" class="form-select" required>

                        <option value="">-- Select Country --</option>

                        @foreach($countries as $country)

                            <option value="{{ $country->id }}">
                                {{ $country->name }}
                            </option>

                        @endforeach

                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>

                    <select name="status_id" class="form-select" required>

                        <option value="">-- Select Status --</option>

                        @foreach($statuses as $status)

                            <option value="{{ $status->id }}">
                                {{ $status->status }}
                            </option>

                        @endforeach

                    </select>
                </div>

                <div class="mb-3">

                    <label class="form-label">Port Name</label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">City</label>

                    <input
                        type="text"
                        name="city"
                        class="form-control"
                        required>

                </div>

                <div class="row">

                    <div class="col-md-6">

                        <div class="mb-3">

                            <label class="form-label">Latitude</label>

                            <input
                                type="number"
                                step="0.0000001"
                                name="latitude"
                                class="form-control"
                                required>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="mb-3">

                            <label class="form-label">Longitude</label>

                            <input
                                type="number"
                                step="0.0000001"
                                name="longitude"
                                class="form-control"
                                required>

                        </div>

                    </div>

                </div>

                <button class="btn btn-primary">

                    <i class="bi bi-save"></i>

                    Save Port

                </button>

            </form>

        </div>

    </div>

</div>

@endsection