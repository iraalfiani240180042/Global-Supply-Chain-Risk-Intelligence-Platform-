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
                Dashboard
            </a>
        </li>

        <li>
       <a href="{{ route('countries.index') }}"
   class="{{ request()->routeIs('countries.*') ? 'active' : '' }}">
                Countries
            </a>
        </li>

        <li>
           <a href="{{ route('weather') }}"
   class="{{ request()->routeIs('weather') ? 'active' : '' }}">
                Weather
            </a>
        </li>

        <li>
           <a href="{{ route('currency') }}"
   class="{{ request()->routeIs('currency') ? 'active' : '' }}">
                Currency
            </a>
        </li>

        <li>
           <a href="{{ route('news') }}"
   class="{{ request()->routeIs('news') ? 'active' : '' }}">
                News
            </a>
        </li>

        <li>
            <a href="{{ route('ports') }}"
   class="{{ request()->routeIs('ports') ? 'active' : '' }}">
                Ports
            </a>
        </li>

        <li>
            <a href="{{ route('analytics') }}"
   class="{{ request()->routeIs('analytics') ? 'active' : '' }}">
                Analytics
            </a>
        </li>

        <li>
            <a href="{{ route('comparison') }}"
   class="{{ request()->routeIs('comparison') ? 'active' : '' }}">
                Comparison
            </a>
        </li>

        <li>
            <a href="{{ route('profile') }}"
   class="{{ request()->routeIs('profile') ? 'active' : '' }}">
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