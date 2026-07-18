@extends('layouts.app')

@section('title', 'Country Comparison')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h2 class="fw-bold">
            🌍 Country Comparison Engine
        </h2>
        <p class="text-muted">
            Compare logistics, economy, weather and supply chain indicators.
        </p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        Country A
                    </label>
                    <select id="countryA" class="form-select">
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

                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        Country B
                    </label>
                    <select id="countryB" class="form-select">
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

                <div class="col-md-2">
                    <button id="compareBtn" class="btn btn-primary w-100">
                        <i class="bi bi-bar-chart-fill"></i>
                        Compare
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Comparison Table Result --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white py-3">
            <strong>
                📊 Comparison Result
            </strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th style="width:220px">
                                Data
                            </th>
                            <th class="text-center">
                                <img id="flagA" src="" width="70" class="mb-2 d-none">
                                <h5 id="titleA" class="fw-bold mb-0">Country A</h5>
                            </th>
                            <th class="text-center">
                                <img id="flagB" src="" width="70" class="mb-2 d-none">
                                <h5 id="titleB" class="fw-bold mb-0">Country B</h5>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>🌍 Region</td>
                            <td id="regionA">-</td>
                            <td id="regionB">-</td>
                        </tr>
                        <tr>
                            <td>👥 Population</td>
                            <td id="populationA">-</td>
                            <td id="populationB">-</td>
                        </tr>
                        <tr>
                            <td>📈 GDP</td>
                            <td id="gdpA">-</td>
                            <td id="gdpB">-</td>
                        </tr>
                        <tr>
                            <td>📉 Inflation</td>
                            <td id="inflationA">-</td>
                            <td id="inflationB">-</td>
                        </tr>
                        <tr>
                            <td>💵 Currency</td>
                            <td id="currencyA">-</td>
                            <td id="currencyB">-</td>
                        </tr>
                        <tr>
                            <td>💱 Exchange Rate</td>
                            <td id="exchangeA">-</td>
                            <td id="exchangeB">-</td>
                        </tr>
                        <tr>
                            <td>🌡 Temperature</td>
                            <td id="tempA">-</td>
                            <td id="tempB">-</td>
                        </tr>
                        <tr>
                            <td>💧 Humidity</td>
                            <td id="humidityA">-</td>
                            <td id="humidityB">-</td>
                        </tr>
                        <tr>
                            <td>🌬 Wind Speed</td>
                            <td id="windA">-</td>
                            <td id="windB">-</td>
                        </tr>
                        <tr>
                            <td>⚠ Risk Score</td>
                            <td id="riskA" class="text-center">-</td>
                            <td id="riskB" class="text-center">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('compareBtn').addEventListener('click', function(){
    let a = document.getElementById('countryA').value;
    let b = document.getElementById('countryB').value;

    if(a == "" || b == ""){
        alert("Select 2 countries.");
        return;
    }

    fetch('/comparison/data/' + a + '/' + b)
    .then(res => res.json())
    .then(function(data){
        // Tampilkan gambar bendera setelah data berhasil di-load
        let imgA = document.getElementById("flagA");
        let imgB = document.getElementById("flagB");
        imgA.src = data.countryA.flag;
        imgB.src = data.countryB.flag;
        imgA.classList.remove('d-none');
        imgB.classList.remove('d-none');

        document.getElementById("titleA").innerHTML = data.countryA.name;
        document.getElementById("titleB").innerHTML = data.countryB.name;

        document.getElementById('regionA').innerHTML = data.countryA.region;
        document.getElementById('regionB').innerHTML = data.countryB.region;

        document.getElementById('populationA').innerHTML = data.countryA.population.toLocaleString();
        document.getElementById('populationB').innerHTML = data.countryB.population.toLocaleString();

        document.getElementById("gdpA").innerHTML = "$" + Number(data.countryA.gdp).toLocaleString() + " B";
        document.getElementById("gdpB").innerHTML = "$" + Number(data.countryB.gdp).toLocaleString() + " B";

        document.getElementById("inflationA").innerHTML = data.countryA.inflation_rate + " %";
        document.getElementById("inflationB").innerHTML = data.countryB.inflation_rate + " %";

        document.getElementById("currencyA").innerHTML = data.countryA.currency;
        document.getElementById("currencyB").innerHTML = data.countryB.currency;

        document.getElementById("exchangeA").innerHTML = data.countryA.exchange_rate;
        document.getElementById("exchangeB").innerHTML = data.countryB.exchange_rate;

        document.getElementById('tempA').innerHTML = data.weatherA.temperature_2m + " °C";
        document.getElementById('tempB').innerHTML = data.weatherB.temperature_2m + " °C";

        document.getElementById('humidityA').innerHTML = data.weatherA.relative_humidity_2m + " %";
        document.getElementById('humidityB').innerHTML = data.weatherB.relative_humidity_2m + " %";

        document.getElementById('windA').innerHTML = data.weatherA.wind_speed_10m + " km/h";
        document.getElementById('windB').innerHTML = data.weatherB.wind_speed_10m + " km/h";

        // Logika penentuan warna badge dinamis berdasarkan skor risiko
        let colorA = "success";
        if (data.countryA.risk_score > 60) {
            colorA = "danger";
        } else if (data.countryA.risk_score > 30) {
            colorA = "warning";
        }

        let colorB = "success";
        if (data.countryB.risk_score > 60) {
            colorB = "danger";
        } else if (data.countryB.risk_score > 30) {
            colorB = "warning";
        }

        document.getElementById("riskA").innerHTML = `
            <span class="badge bg-${colorA} fs-6 mb-1">${data.countryA.risk_score}/100</span>
            <br>${data.countryA.risk_level}
        `;
        document.getElementById("riskB").innerHTML = `
            <span class="badge bg-${colorB} fs-6 mb-1">${data.countryB.risk_score}/100</span>
            <br>${data.countryB.risk_level}
        `;
    })
    .catch(error => {
        console.error('Error fetching comparison data:', error);
    });
});
</script>

@endsection