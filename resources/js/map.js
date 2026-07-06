document.addEventListener("DOMContentLoaded", function () {

    const mapElement = document.getElementById("worldMap");

    if (!mapElement) return;

    const map = L.map("worldMap").setView([20, 0], 2);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors",
        maxZoom: 18,
    }).addTo(map);

    // ===========================
    // DATA DUMMY NEGARA
    // ===========================

    const countries = [
        {
            name: "Indonesia",
            lat: -6.2,
            lng: 106.8,
            risk: "LOW",
            color: "green",
            weather: "30°C",
            currency: "IDR"
        },
        {
            name: "China",
            lat: 39.9,
            lng: 116.4,
            risk: "HIGH",
            color: "red",
            weather: "27°C",
            currency: "CNY"
        },
        {
            name: "Japan",
            lat: 35.6,
            lng: 139.6,
            risk: "MEDIUM",
            color: "orange",
            weather: "24°C",
            currency: "JPY"
        },
        {
            name: "United States",
            lat: 38.9,
            lng: -77.0,
            risk: "LOW",
            color: "green",
            weather: "26°C",
            currency: "USD"
        },
        {
            name: "Germany",
            lat: 52.5,
            lng: 13.4,
            risk: "MEDIUM",
            color: "orange",
            weather: "21°C",
            currency: "EUR"
        }
    ];

    countries.forEach(country => {

        L.circleMarker([country.lat, country.lng], {

            radius: 10,

            color: country.color,

            fillColor: country.color,

            fillOpacity: 0.8,

            weight: 2

        })

        .addTo(map)

        .bindPopup(`
            <div style="min-width:220px">
                <h5>${country.name}</h5>

                <hr>

                <b>Risk Level :</b>
                ${country.risk}

                <br><br>

                <b>Weather :</b>
                ${country.weather}

                <br>

                <b>Currency :</b>
                ${country.currency}

                <br><br>

                <span style="color:${country.color};font-weight:bold;">
                    ● Supply Chain Status
                </span>
            </div>
        `);

    });

});