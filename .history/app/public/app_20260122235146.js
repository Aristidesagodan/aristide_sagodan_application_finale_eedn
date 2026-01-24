let map;
let markers = [];
let currentPoiId = null;
let editingReviewId = null;

document.addEventListener('DOMContentLoaded', () => {
    // =====================
    // MODE LIGHT / DARK
    // =====================
    document.getElementById('themeToggle').addEventListener('click', () => {
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
    const searchBtn = document.getElementById('searchBtn');
    if(searchBtn) searchBtn.addEventListener('click', searchCity);

    // =====================
    // FORMULAIRE AVIS
    // =====================
    const form = document.getElementById('reviewForm');
    if(form) form.addEventListener('submit', submitReview);

    document.getElementById('cancelReview')?.addEventListener('click', hideReviewForm);
});

// =====================
// RECHERCHE VILLE
// =====================
function searchCity() {
    const cityInput = document.getElementById('cityInput');
    if(!cityInput) return;

    const city = cityInput.value.trim();
    if (!city) return;

    fetch(`/api/cities/${encodeURIComponent(city)}/pois`)
        .then(res => res.json())
        .then(data => {
            if(data.error) return alert(data.error);
            renderResults(data);
        })
        .catch(() => alert('Ville introuvable'));
}

// =====================
// AFFICHAGE POIS
// =====================
function renderResults(data) {
    const resultsDiv = document.getElementById('results');
    if(resultsDiv) resultsDiv.style.display = 'block';

    const cityTitle = document.getElementById('cityTitle');
    if(cityTitle) cityTitle.textContent = data.city;

    const activities = document.getElementById('activities');
    const accommodations = document.getElementById('accommodations');
    if(!activities || !accommodations) return;

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
    </li>`;
}

// =====================
// MARQUEURS MAP
// =====================
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

// =====================
// FORMULAIRE AVIS
// =====================
function showReviewForm(poiId, review = null) {
    currentPoiId = poiId;
    editingReviewId = review?.id || null;

    document.getElementById('reviewPoiId').value = poiId;
    document.getElementById('reviewRating').value = review?.rating || '';
    document.getElementById('reviewComment').value = review?.comment || '';

    document.getElementById('reviewFormContainer').style.display = 'block';
}

function hideReviewForm() {
    document.getElementById('reviewFormContainer').style.display = 'none';
    currentPoiId = null;
    editingReviewId = null;
}

// =====================
// ENVOI AVIS
// =====================
function submitReview(e) {
    e.preventDefault();

    const rating = parseFloat(document.getElementById('reviewRating').value);
    const comment = document.getElementById('reviewComment').value;

    if (!currentPoiId) return alert("Erreur POI non sélectionné");

    const url = editingReviewId 
        ? `/api/review/edit/${editingReviewId}` 
        : '/api/review/add';

    const method = editingReviewId ? 'PUT' : 'POST';

    fetch(url, {
        method,
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({poi_id: currentPoiId, rating, comment})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            hideReviewForm();
            loadReviews(currentPoiId);
        } else {
            alert(data.error || 'Erreur lors de l’envoi de l’avis');
        }
    })
    .catch(() => alert('Erreur réseau'));
}

// =====================
// CHARGEMENT AVIS
// =====================
function loadReviews(poiId) {
    fetch(`/api/review/all?poi_id=${poiId}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('reviewsContainer');
            container.innerHTML = '';

            data.forEach(r => {
                const li = document.createElement('li');
                const safeComment = r.comment.replace(/</g, "&lt;").replace(/>/g, "&gt;");

                li.innerHTML = `
                    <strong>${r.is_owner ? 'Vous' : 'Utilisateur'}:</strong> ${safeComment} (${r.rating}/5)
                    ${r.is_owner ? `
                        <button onclick="editReview('${r.id}', ${r.rating}, '${safeComment.replace(/'/g,"\\'")}')">Modifier</button>
                        <button onclick="deleteReview('${r.id}', ${poiId})">Supprimer</button>
                    ` : ''}
                `;
                container.appendChild(li);
            });

            document.getElementById('reviewsList').style.display = 'block';
        });
}

// =====================
// MODIFIER AVIS
// =====================
function editReview(id, rating, comment) {
    showReviewForm(currentPoiId, {id, rating, comment});
}

// =====================
// SUPPRIMER AVIS
// =====================
function deleteReview(id, poiId) {
    if(!confirm('Supprimer cet avis ?')) return;

    fetch(`/api/review/delete/${id}`, { method: 'DELETE' })
        .then(res => res.json())
        .then(data => {
            if(data.success) loadReviews(poiId);
            else alert(data.error || 'Erreur lors de la suppression');
        });
}
