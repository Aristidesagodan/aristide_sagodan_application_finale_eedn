let map;
let markers = [];
let currentPoiId = null;
let editingReviewId = null;

document.addEventListener('DOMContentLoaded', () => {
    // =====================
    // MODE LIGHT / DARK
    // =====================
    const toggle = document.getElementById('themeToggle');
    if (toggle) {
        toggle.addEventListener('click', () => {
            document.body.classList.toggle('dark');
            document.body.classList.toggle('light');
        });
    }

    // =====================
    // MAP
    // =====================
    const mapContainer = document.getElementById('map');
    if (mapContainer) {
        map = L.map('map').setView([46.5, 2.5], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
    }

    // =====================
    // SEARCH
    // =====================
    const searchBtn = document.getElementById('searchBtn');
    if (searchBtn) {
        searchBtn.addEventListener('click', searchCity);
    }
});

// =====================
// INTERCEPTION FORMULAIRE AVIS (IMPORTANT)
// =====================
document.addEventListener('submit', function (e) {
    if (e.target && e.target.id === 'reviewForm') {
        e.preventDefault();
        submitReview();
    }
});

// =====================
// RECHERCHE VILLE
// =====================
function searchCity() {
    const input = document.getElementById('cityInput');
    if (!input) return;

    const city = input.value.trim();
    if (!city) return;

    fetch(`/api/cities/${encodeURIComponent(city)}/pois`, {
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            renderResults(data);
        })
        .catch(err => {
            console.error(err);
            alert('Erreur lors de la recherche');
        });
}

// =====================
// AFFICHAGE POIS
// =====================
function renderResults(data) {
    document.getElementById('results').style.display = 'block';
    document.getElementById('cityTitle').textContent = data.city;

    const activities = document.getElementById('activities');
    const accommodations = document.getElementById('accommodations');

    activities.innerHTML = '';
    accommodations.innerHTML = '';

    clearMarkers();

    data.activities.slice(0, 10).forEach(poi => {
        activities.innerHTML += renderPoiItem(poi);
        addMarker(poi);
    });

    data.accommodations.slice(0, 10).forEach(poi => {
        accommodations.innerHTML += renderPoiItem(poi);
        addMarker(poi);
    });
}

function renderPoiItem(poi) {
    return `
        <li data-poi-id="${poi.id}">
            ${poi.name}
            <button onclick="showReviewForm(${poi.id})">Avis</button>
            <button onclick="loadReviews(${poi.id})">Voir avis</button>
        </li>
    `;
}

// =====================
// MAP MARKERS
// =====================
function addMarker(poi) {
    if (!map || !poi.latitude || !poi.longitude) return;

    const marker = L.marker([poi.latitude, poi.longitude])
        .addTo(map)
        .bindPopup(`<strong>${poi.name}</strong><br>${poi.category || ''}`);

    markers.push(marker);
}

function clearMarkers() {
    if (!map) return;
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
}

// =====================
// FORMULAIRE AVIS
// =====================
function showReviewForm(poiId, review = null) {
    currentPoiId = poiId;
    editingReviewId = review ? review.id : null;

    document.getElementById('reviewPoiId').value = poiId;
    document.getElementById('reviewRating').value = review ? review.rating : '';
    document.getElementById('reviewComment').value = review ? review.comment : '';

    document.getElementById('reviewFormContainer').style.display = 'block';
}

function hideReviewForm() {
    document.getElementById('reviewFormContainer').style.display = 'none';
    currentPoiId = null;
    editingReviewId = null;
}

// =====================
// ENVOI AVIS (POST)
// =====================
function submitReview() {
    const rating = parseFloat(document.getElementById('reviewRating').value);
    const comment = document.getElementById('reviewComment').value.trim();

    if (!currentPoiId || !rating || !comment) {
        alert('Tous les champs sont obligatoires');
        return;
    }

    fetch('/api/review/add', {
        method: 'POST',
        credentials: 'same-origin', // 🔐 SESSION
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            poi_id: currentPoiId,
            rating: rating,
            comment: comment
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                hideReviewForm();
                loadReviews(currentPoiId);
            } else {
                alert(data.error || 'Erreur lors de l’envoi');
            }
        })
        .catch(err => {
            console.error('Erreur envoi avis:', err);
        });
}

// =====================
// CHARGEMENT AVIS
// =====================
function loadReviews(poiId) {
    fetch(`/api/review/all?poi_id=${poiId}`, {
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('reviewsContainer');
            container.innerHTML = '';

            data.forEach(r => {
                const li = document.createElement('li');
                const safeComment = r.comment
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');

                li.innerHTML = `
                    <strong>${r.is_owner ? 'Vous' : 'Utilisateur'} :</strong>
                    ${safeComment} (${r.rating}/5)
                `;

                container.appendChild(li);
            });

            document.getElementById('reviewsList').style.display = 'block';
        })
        .catch(err => {
            console.error('Erreur chargement avis:', err);
        });
}
