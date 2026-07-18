@extends('layouts.app')

@section('title', 'Port Location Dashboard')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                🚢 Port Location Dashboard
            </h2>
            <p class="text-muted mb-0">
                Monitor global seaports and analyze international logistics routes.
            </p>
        </div>
        <div>
            <a href="{{ route('ports.sync') }}" class="btn btn-success me-2">
                <i class="bi bi-arrow-repeat"></i>
                Sync Ports
            </a>

            <a href="{{ route('ports.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Add Port
            </a>
        </div>
    </div>

    {{-- Alert Notification --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Search & Filter (Updated to 2 columns with labels) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        🌍 Country
                    </label>
                    <select id="countrySelect" class="form-select">
                        <option value="">
                            Select Country
                        </option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        🚢 Port
                    </label>
                    <select id="portSelect" class="form-select">
                        <option value="">
                            Select Port
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="row mb-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">
                        Total Ports
                    </h6>
                    <h2 class="fw-bold text-primary">
                        {{ $totalPorts }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">
                        Countries
                    </h6>
                    <h2 class="fw-bold text-success">
                        {{ $totalCountries }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">
                        Active Ports
                    </h6>
                    <h2 class="fw-bold text-warning">
                        {{ $activePorts }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">
                        Regions
                    </h6>
                    <h2 class="fw-bold text-danger">
                        {{ $totalRegions }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Layout (Updated: Map is now Full Width) --}}
    <div class="row">
        {{-- Map --}}
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <strong>
                        🌍 Interactive World Map
                    </strong>
                </div>
                <div class="card-body">
                    <div id="worldMap" style="height:600px;border-radius:15px;"></div>
                </div>
            </div>

            {{-- Information --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5>
                        📍 Port Information
                    </h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <strong>Name :</strong>
                                <span id="portName">-</span>
                            </p>
                            <p>
                                <strong>Country :</strong>
                                <span id="portCountry">-</span>
                            </p>
                            <p>
                                <strong>Region :</strong>
                                <span id="portRegion">-</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Latitude :</strong>
                                <span id="portLat">-</span>
                            </p>
                            <p>
                                <strong>Longitude :</strong>
                                <span id="portLng">-</span>
                            </p>
                            <p>
                                <strong>Status :</strong>
                                <span id="portStatus" class="badge bg-secondary">
                                    -
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    // Inisialisasi Peta Leaflet
    const map = L.map('worldMap').setView([20,0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Siapkan wadah marker global agar bisa dipindahkan/dihapus secara dinamis
    let currentMarker = null;

    // Logika Dropdown Dependen
    const countrySelect = document.getElementById('countrySelect');
    const portSelect = document.getElementById('portSelect');

    countrySelect.addEventListener('change', function(){
        if(this.value == "") {
            portSelect.innerHTML = '<option value="">Select Port</option>';
            resetPortInfo();
            return;
        }

        fetch('/ports/country/' + this.value)
            .then(response => response.json())
            .then(data => {
                portSelect.innerHTML = '<option value="">Select Port</option>';
                data.forEach(function(port){
                    portSelect.innerHTML += `
                        <option value="${port.id}">
                            ${port.name}
                        </option>
                    `;
                });
                resetPortInfo();
            });
    });

    // Event Handler saat Port dipilih
    portSelect.addEventListener('change', function(){
        const portId = this.value;
        if(portId == "") {
            resetPortInfo();
            return;
        }

        fetch('/ports/detail/' + portId)
            .then(response => response.json())
            .then(data => {
                // 1. Update elemen informasi di view
                document.getElementById('portName').innerHTML = data.name;
                document.getElementById('portCountry').innerHTML = data.country;
                document.getElementById('portRegion').innerHTML = data.region;
                document.getElementById('portLat').innerHTML = data.latitude;
                document.getElementById('portLng').innerHTML = data.longitude;
                
                // Mengatur tampilan status dengan style badge bootstrap yang sesuai
                const statusBadge = document.getElementById('portStatus');
                statusBadge.innerHTML = data.status;
                if(data.status.toLowerCase() === 'active') {
                    statusBadge.className = 'badge bg-success';
                } else {
                    statusBadge.className = 'badge bg-danger';
                }

                // 2. Tempatkan atau pindahkan marker di peta
                if (currentMarker) {
                    currentMarker.setLatLng([data.latitude, data.longitude]);
                } else {
                    currentMarker = L.marker([data.latitude, data.longitude]).addTo(map);
                }

                // Tambahkan popup interaktif pada marker
                currentMarker.bindPopup(`<b>${data.name}</b><br>${data.country}`).openPopup();

                // 3. Geser & Zoom peta secara halus ke target koordinat pelabuhan
                map.flyTo([data.latitude, data.longitude], 9);
            })
            .catch(error => {
                console.error('Error fetching port details:', error);
            });
    });

    // Helper untuk mereset tampilan info dan menghapus marker jika tidak ada port yang dipilih
    function resetPortInfo() {
        document.getElementById('portName').innerHTML = '-';
        document.getElementById('portCountry').innerHTML = '-';
        document.getElementById('portRegion').innerHTML = '-';
        document.getElementById('portLat').innerHTML = '-';
        document.getElementById('portLng').innerHTML = '-';
        
        const statusBadge = document.getElementById('portStatus');
        statusBadge.innerHTML = '-';
        statusBadge.className = 'badge bg-secondary';

        if (currentMarker) {
            map.removeLayer(currentMarker);
            currentMarker = null;
        }
        
        // Kembalikan fokus peta ke posisi awal
        map.flyTo([20, 0], 2);
    }
</script>
@endpush