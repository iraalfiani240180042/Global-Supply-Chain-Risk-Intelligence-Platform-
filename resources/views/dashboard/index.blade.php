@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Minimal tinggi Quick Access card */
    .quick-card {
        min-height: 180px;
    }

    /* Style khusus untuk Article Card */
    .article-card {
        background: #f8f9fa;
        border-left: 4px solid #0d6efd;
        transition: all 0.3s ease;
    }

    .article-card:hover {
        transform: translateX(5px);
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    /* Style khusus tombol putih Quick Access dengan SHADOW LEBIH TEGAS */
    .btn-quick-white {
        background-color: #ffffff;
        border: 1px solid #e0e6ed;
        color: #212529;
        /* Custom Shadow yang tampak lebih menonjol */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        border-radius: 12px; /* Dibuat sedikit melengkung agar modern */
    }

    .btn-quick-white:hover {
        background-color: #ffffff;
        border-color: #0d6efd;
        color: #0d6efd;
        /* Efek naik ke atas & shadow lebih tebal saat di-hover */
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(13, 110, 253, 0.15), 0 4px 8px rgba(0, 0, 0, 0.06) !important;
    }
</style>
@endpush

@section('content')

{{-- =================================================== --}}
{{-- ADMIN DASHBOARD                                      --}}
{{-- =================================================== --}}
@if(auth()->user()->role == 'admin')

<div class="row g-4">

    <!-- Countries -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <small class="text-muted">Countries</small>
                        <h2 class="fw-bold mt-2">{{ $totalCountries }}</h2>

                        <small class="text-success">
                            Registered Countries
                        </small>
                    </div>

                    <i class="bi bi-globe2 text-primary fs-1"></i>

                </div>
            </div>
        </div>
    </div>

    <!-- Users (KHUSUS ADMIN) -->
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('users.index') }}" class="text-decoration-none text-dark">
            <div class="card shadow-sm border-0 kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">

                        <div>
                            <small class="text-muted">Users</small>
                            <h2 class="fw-bold mt-2">{{ $totalUsers }}</h2>

                            <small class="text-success">
                                Registered Users
                            </small>
                        </div>

                        <i class="bi bi-people-fill text-success fs-1"></i>

                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Ports -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <small class="text-muted">Ports</small>
                        <h2 class="fw-bold mt-2">{{ $activePorts }}</h2>

                        <small class="text-success">
                            Active Ports
                        </small>
                    </div>

                    <i class="bi bi-truck text-info fs-1"></i>

                </div>
            </div>
        </div>
    </div>

    <!-- Articles -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <small class="text-muted">Articles</small>
                        <h2 class="fw-bold mt-2">{{ $totalArticles }}</h2>

                        <small class="text-success">
                            Analysis Articles
                        </small>
                    </div>

                    <i class="bi bi-newspaper text-warning fs-1"></i>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- Quick Access (KHUSUS ADMIN) -->
<div class="card shadow-sm border-0 mt-4 quick-card">
    <div class="card-body p-4">

        <h5 class="fw-bold mb-4">
            Quick Access
        </h5>

        <div class="row g-4">

            <div class="col-md-3">
                <a href="{{ route('articles.create') }}"
                    class="btn btn-quick-white w-100 py-4 fw-semibold">
                    <i class="bi bi-plus-circle text-primary fs-3 d-block mb-2"></i>
                    Add Article
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('users.index') }}"
                    class="btn btn-quick-white w-100 py-4 fw-semibold">
                    <i class="bi bi-people text-success fs-3 d-block mb-2"></i>
                    Manage Users
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('countries.index') }}"
                    class="btn btn-quick-white w-100 py-4 fw-semibold">
                    <i class="bi bi-globe2 text-warning fs-3 d-block mb-2"></i>
                    View Countries
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('ports.index') }}"
                    class="btn btn-quick-white w-100 py-4 fw-semibold">
                    <i class="bi bi-truck text-info fs-3 d-block mb-2"></i>
                    Manage Ports
                </a>
            </div>

        </div>

    </div>
</div>

<!-- Recent Articles & Latest Users (KHUSUS ADMIN) -->
<div class="row mt-4">

    <!-- Recent Articles -->
    <div class="col-lg-7">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-white fw-bold py-3">
                Recent Analysis Articles
            </div>

            <div class="card-body">

                @forelse($latestArticles as $article)

                <div class="article-card p-3 mb-3 rounded-3">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>
                            <h6 class="fw-bold mb-1">
                                {{ $article->title }}
                            </h6>

                            <small class="text-muted">
                                <i class="bi bi-globe2 me-1"></i>
                                {{ $article->country->name ?? '-' }}
                            </small>

                            <br>

                            <small class="text-muted">
                                <i class="bi bi-tag me-1"></i>
                                {{ $article->category ?? 'General' }}
                            </small>
                        </div>

                        <div>
                            <i class="bi bi-newspaper text-primary fs-3"></i>
                        </div>

                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $article->published_at ?? 'Recently Added' }}
                        </small>
                    </div>

                </div>

                @empty

                <div class="text-center text-muted py-4">
                    No articles available.
                </div>

                @endforelse

            </div>

        </div>

    </div>

    <!-- Latest Users -->
    <div class="col-lg-5">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-white fw-bold py-3">
                Latest Users
            </div>

            <div class="list-group list-group-flush">

                @forelse($latestUsers as $user)

                <div class="list-group-item py-3">

                    <div class="fw-semibold">
                        {{ $user->name }}
                    </div>

                    <small class="text-muted">
                        {{ $user->email }}
                    </small>

                </div>

                @empty

                <div class="list-group-item text-center text-muted py-4">
                    No users found.
                </div>

                @endforelse

            </div>

        </div>

    </div>

</div>

@endif


{{-- =================================================== --}}
{{-- USER DASHBOARD                                       --}}
{{-- =================================================== --}}
@if(auth()->user()->role == 'user')

{{-- Welcome Banner User --}}
<div class="card border-0 shadow-sm mb-4"
     style="
     background: linear-gradient(135deg,#0d6efd,#0a58ca);
     border-radius:18px;
     color:white;
     ">

    <div class="card-body p-4 p-md-5">

        <div class="row align-items-center">

            <div class="col-md-8">

                <h2 class="fw-bold mb-2">
                    Welcome back, {{ auth()->user()->name }} 
                </h2>

                <p class="text-white-50 mb-0">
                    Explore global countries, monitor logistics ports,
                    save your favorite destinations, and read the latest
                    supply chain analysis.
                </p>

            </div>


            <div class="col-md-4 text-center d-none d-md-block">

                <i class="bi bi-globe-americas"
                   style="
                   font-size:100px;
                   opacity:.25;
                   ">
                </i>

            </div>

        </div>

    </div>

</div>

<div class="row g-4 mt-1">

    {{-- Countries --}}
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <small class="text-muted">
                            Countries
                        </small>

                        <h2 class="fw-bold mt-2">
                            {{ $totalCountries }}
                        </h2>

                        <small class="text-success">
                            Available Countries
                        </small>
                    </div>

                    <i class="bi bi-globe2 text-primary fs-1"></i>

                </div>

            </div>
        </div>
    </div>


    {{-- Ports --}}
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <small class="text-muted">
                            Ports
                        </small>

                        <h2 class="fw-bold mt-2">
                            {{ $activePorts }}
                        </h2>

                        <small class="text-success">
                            Active Ports
                        </small>
                    </div>

                    <i class="bi bi-truck text-info fs-1"></i>

                </div>

            </div>
        </div>
    </div>


    {{-- Favorite Countries --}}
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <small class="text-muted">
                            Favorite Countries
                        </small>

                        <h2 class="fw-bold mt-2">
                            {{ auth()->user()->favorites()->count() }}
                        </h2>

                        <small class="text-danger">
                            Saved Countries
                        </small>
                    </div>

                    <i class="bi bi-heart-fill text-danger fs-1"></i>

                </div>

            </div>
        </div>
    </div>


    {{-- Articles --}}
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 kpi-card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <small class="text-muted">
                            Articles
                        </small>

                        <h2 class="fw-bold mt-2">
                            {{ $totalArticles }}
                        </h2>

                        <small class="text-success">
                            Intelligence Reports
                        </small>
                    </div>

                    <i class="bi bi-newspaper text-warning fs-1"></i>

                </div>

            </div>
        </div>
    </div>


</div>


{{-- Latest Articles User --}}
<div class="card shadow-sm border-0 mt-4">

    <div class="card-header bg-white fw-bold py-3">
        Latest Articles
    </div>


    <div class="card-body">

        @forelse($latestArticles as $article)

        <div class="article-card p-3 mb-3 rounded-3">

            <h6 class="fw-bold">
                {{ $article->title }}
            </h6>

            <small class="text-muted">
                <i class="bi bi-globe2"></i>
                {{ $article->country->name ?? '-' }}

                |

                <i class="bi bi-tag"></i>
                {{ $article->category }}
            </small>


            <p class="mt-2 mb-0 text-muted">
                {{ Str::limit($article->summary, 120) }}
            </p>

        </div>


        @empty

        <p class="text-muted text-center">
            No articles available.
        </p>

        @endforelse


    </div>

</div>

@endif

@endsection