<aside class="sidebar">

    <div class="sidebar-header">
        <div class="logo">
            <i class="bi bi-globe-central-south-asia"></i>
        </div>

        <div class="brand">
            <h5>GSC-RIP</h5>
            <small>Risk Intelligence</small>
        </div>
    </div>

  <ul class="menu">

    <li>
        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li>
        <a href="{{ route('countries.index') }}"
           class="{{ request()->routeIs('countries.*') ? 'active' : '' }}">
            <i class="bi bi-globe2"></i>
            <span>Countries</span>
        </a>
    </li>

    <li>
        <a href="{{ route('weather') }}"
           class="{{ request()->routeIs('weather') ? 'active' : '' }}">
            <i class="bi bi-cloud-sun"></i>
            <span>Weather</span>
        </a>
    </li>

    <li>
        <a href="{{ route('currency') }}"
           class="{{ request()->routeIs('currency') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i>
            <span>Currency</span>
        </a>
    </li>

    <li>
        <a href="{{ route('news') }}"
           class="{{ request()->routeIs('news') ? 'active' : '' }}">
            <i class="bi bi-newspaper"></i>
            <span>News</span>
        </a>
    </li>

    <li>
        <a href="{{ route('ports.index') }}"
           class="{{ request()->routeIs('ports.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i>
            <span>Ports</span>
        </a>
    </li>

    <li>
        <a href="{{ route('risk.index') }}"
           class="{{ request()->routeIs('risk.*') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i>
            <span>Risk Prediction</span>
        </a>
    </li>

    <li>
        <a href="{{ route('comparison.index') }}"
           class="{{ request()->routeIs('comparison.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i>
            <span>Country Comparison</span>
        </a>
    </li>

    <li>
        <a href="{{ route('articles.index') }}"
           class="{{ request()->routeIs('articles.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i>
            <span>Analysis Articles</span>
        </a>
    </li>

    @if(auth()->user()->role == 'user')
    <li>
        <a href="{{ route('favorites.index') }}"
           class="{{ request()->routeIs('favorites.*') ? 'active' : '' }}">
            <i class="bi bi-heart"></i>
            <span>Favorite Countries</span>
        </a>
    </li>
    @endif

</ul>

    <form method="POST" action="{{ route('logout') }}">

        @csrf

        <button class="logout-btn">

            <i class="bi bi-box-arrow-left"></i>

            Logout

        </button>

    </form>

</aside>