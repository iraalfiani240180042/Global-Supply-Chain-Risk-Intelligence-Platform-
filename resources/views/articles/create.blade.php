@extends('layouts.app')

@section('title', 'Create Analysis Article')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">
        <div class="card-body">

            <h2 class="fw-bold mb-4">Create Analysis Article</h2>

            <form action="{{ route('articles.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                {{-- Title --}}
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="{{ old('title') }}"
                        required>
                </div>

                {{-- Country --}}
                <div class="mb-3">
                    <label class="form-label">Country</label>

                    <select
                        name="country_id"
                        class="form-select"
                        required>

                        <option value="">-- Select Country --</option>

                        @foreach($countries as $country)
                            <option
                                value="{{ $country->id }}"
                                {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- Category --}}
                <div class="mb-3">
                    <label class="form-label">Category</label>

                    <select name="category" class="form-select" required>

                        <option value="">-- Select Category --</option>
                        <option value="Market Analysis">Market Analysis</option>
                        <option value="Export Opportunity">Export Opportunity</option>
                        <option value="Risk Analysis">Risk Analysis</option>
                        <option value="Logistics">Logistics</option>

                    </select>
                </div>

                {{-- Article Image --}}
                <div class="mb-3">
                    <label class="form-label">
                        Article Image
                    </label>

                    <input
                        type="file"
                        name="image"
                        class="form-control"
                        accept="image/*">

                    <small class="text-muted">
                        JPG, PNG, JPEG (Max 2 MB)
                    </small>
                </div>

                {{-- Summary --}}
                <div class="mb-3">
                    <label class="form-label">Summary</label>

                    <textarea
                        name="summary"
                        rows="5"
                        class="form-control"
                        required>{{ old('summary') }}</textarea>
                </div>

                {{-- Content --}}
                <div class="mb-3">
                    <label class="form-label">Content</label>

                    <textarea
                        name="content"
                        rows="8"
                        class="form-control"
                        required>{{ old('content') }}</textarea>
                </div>

                {{-- Risk Level --}}
                <div class="mb-3">
                    <label class="form-label">Risk Level</label>

                    <select
                        name="risk_level"
                        class="form-select">

                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>

                    </select>
                </div>

                {{-- Recommendation --}}
                <div class="mb-3">
                    <label class="form-label">Recommendation</label>

                    <select
                        name="recommended"
                        class="form-select">

                        <option value="1">Recommended</option>
                        <option value="0">Not Recommended</option>

                    </select>
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select">

                        <option value="Draft">
                            Draft
                        </option>

                        <option value="Published">
                            Published
                        </option>

                    </select>
                </div>

                {{-- Published Date --}}
                <div class="mb-4">
                    <label class="form-label">Published Date</label>

                    <input
                        type="date"
                        name="published_at"
                        class="form-control"
                        value="{{ old('published_at', date('Y-m-d')) }}">
                </div>

                <button class="btn btn-primary">
                    <i class="bi bi-save"></i>
                    Save Article
                </button>

                <a href="{{ route('articles.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>

@endsection