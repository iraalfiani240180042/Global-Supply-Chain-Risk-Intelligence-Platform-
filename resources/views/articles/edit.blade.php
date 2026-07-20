@extends('layouts.app')

@section('title', 'Edit Analysis Article')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">
        <div class="card-body">

            <h2 class="fw-bold mb-4">Edit Analysis Article</h2>

            <form action="{{ route('articles.update', $article->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div class="mb-3">
                    <label class="form-label">Title</label>

                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="{{ old('title', $article->title) }}"
                        required>
                </div>

                {{-- Country --}}
                <div class="mb-3">
                    <label class="form-label">Country</label>

                    <select name="country_id" class="form-select" required>

                        @foreach($countries as $country)

                            <option
                                value="{{ $country->id }}"
                                {{ old('country_id', $article->country_id) == $country->id ? 'selected' : '' }}>

                                {{ $country->name }}

                            </option>

                        @endforeach

                    </select>
                </div>

                {{-- Category --}}
                <div class="mb-3">
                    <label class="form-label">Category</label>

                    <select name="category" class="form-select" required>

                        <option value="Market Analysis"
                            {{ old('category', $article->category) == 'Market Analysis' ? 'selected' : '' }}>
                            Market Analysis
                        </option>

                        <option value="Export Opportunity"
                            {{ old('category', $article->category) == 'Export Opportunity' ? 'selected' : '' }}>
                            Export Opportunity
                        </option>

                        <option value="Risk Analysis"
                            {{ old('category', $article->category) == 'Risk Analysis' ? 'selected' : '' }}>
                            Risk Analysis
                        </option>

                        <option value="Logistics"
                            {{ old('category', $article->category) == 'Logistics' ? 'selected' : '' }}>
                            Logistics
                        </option>

                    </select>
                </div>

                {{-- Image Component --}}
                <div class="mb-3">
                    <label class="form-label">Current Image</label>

                    @if($article->image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/'.$article->image) }}"
                                 class="img-fluid rounded shadow"
                                 style="max-height:250px;">
                        </div>
                    @else
                        <p class="text-muted">
                            No image uploaded.
                        </p>
                    @endif

                    <label class="form-label">
                        Change Image
                    </label>

                    <input
                        type="file"
                        name="image"
                        class="form-control"
                        accept="image/*">

                    <small class="text-muted">
                        Leave blank if you don't want to change the image.
                    </small>
                </div>

                {{-- Summary --}}
                <div class="mb-3">
                    <label class="form-label">Summary</label>

                    <textarea
                        name="summary"
                        rows="5"
                        class="form-control"
                        required>{{ old('summary', $article->summary) }}</textarea>
                </div>

                {{-- Content --}}
                <div class="mb-3">
                    <label class="form-label">Content</label>

                    <textarea
                        name="content"
                        rows="8"
                        class="form-control"
                        required>{{ old('content', $article->content) }}</textarea>
                </div>

                {{-- Risk Level --}}
                <div class="mb-3">
                    <label class="form-label">Risk Level</label>

                    <select name="risk_level" class="form-select">

                        <option value="Low"
                            {{ old('risk_level', $article->risk_level) == 'Low' ? 'selected' : '' }}>
                            Low
                        </option>

                        <option value="Medium"
                            {{ old('risk_level', $article->risk_level) == 'Medium' ? 'selected' : '' }}>
                            Medium
                        </option>

                        <option value="High"
                            {{ old('risk_level', $article->risk_level) == 'High' ? 'selected' : '' }}>
                            High
                        </option>

                    </select>
                </div>

                {{-- Recommendation --}}
                <div class="mb-3">
                    <label class="form-label">Recommendation</label>

                    <select name="recommended" class="form-select">

                        <option value="1"
                            {{ old('recommended', $article->recommended) ? 'selected' : '' }}>
                            Recommended
                        </option>

                        <option value="0"
                            {{ !old('recommended', $article->recommended) ? 'selected' : '' }}>
                            Not Recommended
                        </option>

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

                        <option value="Draft"
                            {{ old('status', $article->status) == 'Draft' ? 'selected' : '' }}>
                            Draft
                        </option>

                        <option value="Published"
                            {{ old('status', $article->status) == 'Published' ? 'selected' : '' }}>
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
                        value="{{ old('published_at', optional($article->published_at)->format('Y-m-d')) }}">
                </div>

                <button class="btn btn-warning">
                    <i class="bi bi-pencil-square"></i>
                    Update Article
                </button>

                <a href="{{ route('articles.index') }}" class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>

@endsection