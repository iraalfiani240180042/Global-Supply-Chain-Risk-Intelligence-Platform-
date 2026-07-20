@extends('layouts.app')

@section('title', 'Supply Chain Risk Prediction')

@section('content')

<div class="container-fluid">

    <h1 class="fw-bold mb-4">
        Supply Chain Risk Prediction
    </h1>

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- Select Country --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Select Country</label>
                <select id="country" class="form-select" required>
                    <option value="">-- Choose Country --</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>

    {{-- Tempat Hasil Output AJAX --}}
    <div id="risk-result" class="mt-4"></div>

</div>

@endsection

@push('scripts')
<script>
document.getElementById('country').addEventListener('change', function() {
    let countryId = this.value;
    let resultContainer = document.getElementById('risk-result');

    if (!countryId) {
        resultContainer.innerHTML = '';
        return;
    }

    // Tampilkan loading state sederhana sewaktu fetch data
    resultContainer.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    fetch('/risk-prediction/' + countryId)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            // Tentukan warna badge berdasarkan status risk
            let badgeClass = 'bg-success';
            if (data.status === 'Medium Risk') {
                badgeClass = 'bg-warning text-dark';
            } else if (data.status === 'High Risk') {
                badgeClass = 'bg-danger';
            }

            resultContainer.innerHTML = `
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <h3 class="fw-bold mb-1">${data.country}</h3>
                        <p class="text-muted mb-4">Real-time Supply Chain Risk Assessment</p>
                        
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded bg-light">
                                    <small class="text-muted d-block mb-1">Weather Risk</small>
                                    <span class="fw-bold fs-5">${data.weather}%</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded bg-light">
                                    <small class="text-muted d-block mb-1">Inflation Risk</small>
                                    <span class="fw-bold fs-5">${data.inflation}%</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded bg-light">
                                    <small class="text-muted d-block mb-1">Currency Risk</small>
                                    <span class="fw-bold fs-5">${data.currency}%</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded bg-light">
                                    <small class="text-muted d-block mb-1">News Sentiment</small>
                                    <span class="fw-bold fs-5">${data.news}%</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <h4 class="text-secondary mb-2">Overall Risk Score</h4>
                        <h1 class="display-4 fw-black text-dark mb-3">${data.score}%</h1>
                        
                        <span class="badge ${badgeClass} fs-5 px-4 py-2 rounded-pill">
                            ${data.status}
                        </span>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            resultContainer.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    Gagal mengambil data prediksi risiko. Silakan coba lagi nanti.
                </div>
            `;
        });
});
</script>
@endpush