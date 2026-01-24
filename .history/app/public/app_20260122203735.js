let map;
let markers = [];
let currentPoiId = null;      // POI actuellement sélectionné pour avis
let editingReviewId = null;   // ID de l'avis en cours d'édition

document.addEventListener('DOMContentLoaded', () => {
    // =====================
    // Mode Light / Dark
    // =====================
    document.getElementById('themeToggle')?.addEventListener('click', () => {
        document.body.classList.toggle('dark');
        document.body.classList.toggle('light');
    });

    // =====================
    // Initialisation de la carte Leaflet
    // =====================
    map = L.map('map').setView([46.5, 2.5], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // =====================
    // Recherche de ville
    // =====================
    document.getElementById('searchBtn')?.addEventListener('click', searchCity);

    // =====================
    // Formulaire d'avis
    // =====================
    const form = document.getElementById('reviewForm');
    if (form) form.addEventListener('submit', submitReview);

    document.getElementById('cancelReview')?.addEventListener('click', hideReviewForm);
});

// =====================
// Recherche d'une ville et récupération des POI
// =====================
function searchCity() {
    const city = document.getElementById('cityInput').value.trim();
    if (!city) return;

    fetch(`/api/cities/${encodeURIComponent(city)}/pois`, {
        credentials: 'same-origin' // ⚠ Important pour envoyer le cookie de session
    })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            renderResults(data);
        })
        .catch(() => alert('Ville introuvable'));
}

// =====================
// Affichage des POI dans la liste et sur la carte
// =====================
function renderResults(data) {
    document.getElementById('results').style.display = 'block';
    document.getElementById('cityTitle').textContent = data.city;

    const activities = document.getElementById('activities');
    const accommodations = document.getElementById('accommodations');

    activities.innerHTML = '';
    accommodations.innerHTML = '';

    clearMarkers();

    data.activities.slice(0,10).forEach(poi => {
        activities.innerHTML += renderPoiItem(poi);
        addMarker(poi);
    });

    data.accommodations.slice(0,10).forEach(poi => {
        accommodations.innerHTML += renderPoiItem(poi);
        addMarker(poi);
    });
}

// =====================
// HTML pour un POI avec boutons Avis et Voir Avis
// =====================
function renderPoiItem(poi) {
    return `
    <li data-poi-id="${poi.id}">
        ${poi.name} 
        <button onclick="showReviewForm(${poi.id})">Avis</button>
        <button onclick="loadReviews(${poi.id})">Voir avis</button>
    </li>`;
}

// =====================
// Gestion des marqueurs sur la carte
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
// Afficher le formulaire pour créer/modifier un avis
// =====================
function showReviewForm(poiId, review = null) {
    currentPoiId = poiId;
    editingReviewId = review?.id || null;

    document.getElementById('reviewPoiId').value = poiId;
    document.getElementById('reviewRating').value = review?.rating || '';
    document.getElementById('reviewComment').value = review?.comment || '';

    document.getElementById('reviewFormContainer').style.display = 'block';
}

// =====================
// Masquer le formulaire
// =====================
function hideReviewForm() {
    document.getElementById('reviewFormContainer').style.display = 'none';
    currentPoiId = null;
    editingReviewId = null;
}

// =====================
// Envoi du formulaire vers le back Symfony + MongoDB
// =====================
function submitReview(e) {
    e.preventDefault();

    const rating = parseFloat(document.getElementById('reviewRating').value);
    const comment = document.getElementById('reviewComment').value;

    if (!currentPoiId || !rating || !comment.trim()) {
        alert('Tous les champs sont obligatoires');
        return;
    }

    const url = editingReviewId ? `/api/review/${editingReviewId}/update` : '/api/review/add';
    const method = editingReviewId ? 'PUT' : 'POST';

    fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ poi_id: currentPoiId, rating, comment }),
        credentials: 'same-origin' // ⚠ IMPORTANT pour que Symfony lise la session utilisateur
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            hideReviewForm();
            loadReviews(currentPoiId); // Recharge les avis
        } else {
            alert(data.error || 'Erreur lors de l’envoi');
        }
    })
    .catch(err => {
        console.error('Erreur lors de l’envoi de l’avis :', err);
        alert('Impossible d’envoyer l’avis');
    });
}

// =====================
// Charger et afficher les avis pour un POI
// =====================
function loadReviews(poiId) {
    fetch(`/api/review/all?poi_id=${poiId}`, {
        credentials: 'same-origin' // ⚠ Important pour la session
    })
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
    })
    .catch(err => console.error('Erreur chargement avis:', err));
}

// =====================
// Modifier un avis existant
// =====================
function editReview(id, rating, comment) {
    showReviewForm(currentPoiId, {id, rating, comment});
}

// =====================
// Supprimer un avis existant
// =====================
function deleteReview(id, poiId) {
    if (!confirm('Supprimer cet avis ?')) return;

    fetch(`/api/review/${id}/delete`, {
        method: 'DELETE',
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) loadReviews(poiId);
        else alert(data.error || 'Erreur lors de la suppression');
    })
    .catch(err => console.error('Erreur suppression avis:', err));
}
