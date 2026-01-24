let map;
let markers = [];

document.addEventListener('DOMContentLoaded', () => {

    // =====================
    // MODE LIGHT / DARK
    // =====================
    const toggle = document.getElementById('themeToggle');
    toggle.addEventListener('click', () => {
        document.body.classList.toggle('dark');
        document.body.classList.toggle('light');
    });

    // =====================
    // MAP
    // =====================
    map = L.map('map').setView([46.5, 2.5], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // =====================
    // SEARCH
    // =====================
    document.getElementById('searchBtn').addEventListener('click', searchCity);
});

function searchCity() {
    const city = document.getElementById('cityInput').value.trim();
    if (!city) return;

    fetch(`/api/cities/${encodeURIComponent(city)}/pois`)
        .then(res => res.json())
        .then(data => renderResults(data))
        .catch(() => alert('Ville introuvable'));
}

function renderResults(data) {
    document.getElementById('results').style.display = 'block';
    document.getElementById('cityTitle').textContent = data.city;

    const activities = document.getElementById('activities');
    const accommodations = document.getElementById('accommodations');

    activities.innerHTML = '';
    accommodations.innerHTML = '';

    clearMarkers();

    data.activities.slice(0,10).forEach(poi => {
        activities.innerHTML += `<li>${poi.name}</li>`;
        addMarker(poi);
    });

    data.accommodations.slice(0,10).forEach(poi => {
        accommodations.innerHTML += `<li>${poi.name}</li>`;
        addMarker(poi);
    });
}

function addMarker(poi) {
    if (!poi.latitude || !poi.longitude) return;

    const marker = L.marker([poi.latitude, poi.longitude])
        .addTo(map)
        .bindPopup(`<strong>${poi.name}</strong><br>${poi.category}`);

    markers.push(marker);
}

function clearMarkers() {
    markers.forEach(m => map.removeLayer(m));
    markers = [];
}
