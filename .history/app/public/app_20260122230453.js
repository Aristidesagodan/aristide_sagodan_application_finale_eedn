let map;
let markers = [];
let currentPoiId = null;
let editingReviewId = null;

document.addEventListener('DOMContentLoaded', () => {
    // =====================
    // THEME
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
// INTERCEPTION FORM AVIS
// =====================
document.addEventListener('submit', e => {
    if (e.target && e.target.id === 'reviewForm') {
        e.preventDefault();
        submitReview();
    }
});

// =====================
// SEARCH CITY
// =====================
function searchCity() {
    const city = document.getElementById('cityInput').value.trim();
    if (!city) return;

    fetch(`/api/cities/${encodeURIComponent(city)}/pois`, {
        credentials: 'same-origin'
    })
        .then(r => r.json())
        .then(renderResults)
        .catch(console.error);
}

// =====================
// RENDER POIS
// =====================
function renderResults(data) {
    document.getElementById('results').style.display = 'block';
    document.getElementById('cityTitle').textContent = data.city;

    clearMarkers();

    const activities = document.getElementById('activities');
    const accommodations = document.getElementById('accommodations');
    activities.innerHTML = '';
    accommodations.innerHTML = '';

    data.activities.forEach(poi => {
        activities.innerHTML += renderPoi(poi);
        addMarker(poi);
    });

    data.accommodations.forEach(poi => {
        accommodations.innerHTML += renderPoi(poi);
        addMarker(poi);
    });
}

function renderPoi(poi) {
    return `
        <li>
            ${poi.name}
            <button onclick="showReviewForm(${poi.id})">Avis</button>
            <button onclick="loadReviews(${poi.id})">Voir avis</button>
        </li>
    `;
}

// =====================
// MAP
// =====================
function addMarker(poi) {
    if (!poi.latitude || !poi.longitude) return;

    const marker = L.marker([poi.latitude, poi.longitude])
        .addTo(map)
        .bindPopup(poi.name);

    markers.push(marker);
}

function clearMarkers() {
    markers.forEach(m => map.removeLayer(m));
    markers = [];
}

// =====================
// FORM AVIS
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
    editingReviewId = null;
    document.getElementById('reviewFormContainer').style.display = 'none';
}

// =====================
// ADD / EDIT REVIEW
// =====================
function submitReview() {
    const rating = Number(document.getElementById('reviewRating').value);
    const comment = document.getElementById('reviewComment').value.trim();

    if (!rating || !comment) {
        alert('Tous les champs sont obligatoires');
        return;
    }

    const url = editingReviewId
        ? `/api/review/edit/${editingReviewId}`
        : '/api/review/add';

    const method = editingReviewId ? 'PUT' : 'POST';

    fetch(url, {
        method,
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            poi_id: currentPoiId,
            rating,
            comment
        })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                hideReviewForm();
                loadReviews(currentPoiId);
            } else {
                alert(data.error || 'Erreur');
            }
        })
        .catch(console.error);
}

// =====================
// LOAD REVIEWS
// =====================
function loadReviews(poiId) {
    fetch(`/api/review/all?poi_id=${poiId}`, {
        credentials: 'same-origin'
    })
        .then(r => r.json())
        .then(reviews => {
            const ul = document.getElementById('reviewsContainer');
            ul.innerHTML = '';

            reviews.forEach(r => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <strong>${r.is_owner ? 'Vous' : 'Utilisateur'} :</strong>
                    ${escapeHtml(r.comment)} (${r.rating}/5)
                    ${r.is_owner ? `
                        <button onclick='editReview(${JSON.stringify(r)})'>✏️</button>
                        <button onclick='deleteReview(${r.id}, ${poiId})'>🗑</button>
                    ` : ''}
                `;
                ul.appendChild(li);
            });

            document.getElementById('reviewsList').style.display = 'block';
        })
        .catch(console.error);
}

// =====================
// EDIT
// =====================
function editReview(review) {
    showReviewForm(currentPoiId, review);
}

// =====================
// DELETE
// =====================
function deleteReview(id, poiId) {
    if (!confirm('Supprimer cet avis ?')) return;

    fetch(`/api/review/delete/${id}`, {
        method: 'DELETE',
        credentials: 'same-origin'
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                loadReviews(poiId);
            } else {
                alert(data.error || 'Erreur suppression');
            }
        })
        .catch(console.error);
}

// =====================
// SECURITY
// =====================
function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}
