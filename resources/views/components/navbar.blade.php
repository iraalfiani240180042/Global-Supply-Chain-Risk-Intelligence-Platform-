<nav class="navbar navbar-expand-lg bg-white shadow-sm px-4 py-3">

    <div class="container-fluid">

        <div>

            <h4 class="fw-bold mb-0">

                Global Supply Chain Risk Intelligence Platform

            </h4>

            <small class="text-muted">

                Monitor Global Risk in Real Time

            </small>

        </div>

        <div class="d-flex align-items-center gap-3">

            <button class="btn btn-light">

                <i class="bi bi-bell"></i>

            </button>

            <div class="d-flex align-items-center gap-2">

                <i class="bi bi-person-circle fs-3"></i>

              <div>
    @auth
        <strong>{{ auth()->user()->name }}</strong>
        <br>

        @if(auth()->user()->role == 'admin')
            <small class="text-secondary">Administrator</small>
        @else
            <small class="text-secondary">Exporter</small>
        @endif
    @endauth
</div>

            </div>

        </div>

    </div>

</nav>