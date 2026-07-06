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
            <a href="{{ route('dashboard') }}" class="active">
                <i class="bi bi-grid-fill"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="{{ route('countries') }}">
                <i class="bi bi-globe2"></i>
                Countries
            </a>
        </li>

        <li>
            <a href="{{ route('weather') }}">
                <i class="bi bi-cloud-sun"></i>
                Weather
            </a>
        </li>

        <li>
            <a href="{{ route('currency') }}">
                <i class="bi bi-currency-exchange"></i>
                Currency
            </a>
        </li>

        <li>
            <a href="{{ route('news') }}">
                <i class="bi bi-newspaper"></i>
                News
            </a>
        </li>

        <li>
            <a href="{{ route('ports') }}">
                <i class="bi bi-truck"></i>
                Ports
            </a>
        </li>

        <li>
            <a href="{{ route('analytics') }}">
                <i class="bi bi-graph-up-arrow"></i>
                Analytics
            </a>
        </li>

        <li>
            <a href="{{ route('comparison') }}">
                <i class="bi bi-bar-chart-line"></i>
                Comparison
            </a>
        </li>

        <li>
            <a href="{{ route('profile') }}">
                <i class="bi bi-person-circle"></i>
                Profile
            </a>
        </li>

    </ul>

    <form method="POST" action="{{ route('logout') }}">

        @csrf

        <button class="logout-btn">

            <i class="bi bi-box-arrow-left"></i>

            Logout

        </button>

    </form>

</aside>