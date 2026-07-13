@extends('layouts.app')

@section('title', 'News')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>News Management</h2>

    <a href="{{ route('news.sync') }}" class="btn btn-success">
        <i class="bi bi-arrow-repeat"></i>
        Sync News
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
                    <th>Title</th>
                    <th>Source</th>
                    <th>Country</th>
                    <th>Category</th>
                    <th>Published</th>
                    <th>Link</th>
                </tr>

            </thead>

            <tbody>

                @forelse($news as $item)

                <tr>

                    <td>{{ $news->firstItem() + $loop->index }}</td>

                    <td>{{ $item->title }}</td>

                    <td>{{ $item->source }}</td>

                    <td>{{ $item->country->name ?? '-' }}</td>

                    <td>{{ $item->category->name ?? '-' }}</td>

                    <td>{{ $item->published_at }}</td>

                    <td>

                        <a href="{{ $item->url }}"
                           target="_blank"
                           class="btn btn-primary btn-sm">

                            Read

                        </a>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="7" class="text-center">

                        No news available.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

        <div class="mt-3">

            {{ $news->links() }}

        </div>

    </div>

</div>

@endsection