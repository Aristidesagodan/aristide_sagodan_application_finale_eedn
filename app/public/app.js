let map;
let markers = [];
let currentPoiId = null;
let editingReviewId = null;
let cityCenter = null;

document.addEventListener('DOMContentLoaded', () => {

    // =====================
    // THEME
    // =====================
    document.getElementById('themeToggle')?.addEventListener('click', () => {
        document.body.classList.toggle('dark');
        document.body.classList.toggle('light');
    });

    // =====================
    // MAP INIT
    // =====================
    if (document.getElementById('map')) {
        map = L.map('map').setView([46.5, 2.5], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
    }

    // =====================
    // EVENTS
    // =====================
    document.getElementById('searchBtn')?.addEventListener('click', searchCity);
    document.getElementById('reviewForm')?.addEventListener('submit', submitReview);
    document.getElementById('cancelReview')?.addEventListener('click', hideReviewForm);

    initCookieConsent();
});


// =====================
// RECHERCHE VILLE (PROXY SYMFONY)
// =====================
async function searchCity() {

    const cityInput = document.getElementById('cityInput');
    if (!cityInput) return;

    const city = cityInput.value.trim();
    if (!city) return;

    try {

        // Appel proxy Symfony (évite CORS Nominatim)
        const geoRes = await fetch(`/api/cities/geocode?city=${encodeURIComponent(city)}`);
        if (!geoRes.ok) throw new Error("Erreur géocodage");

        const geoData = await geoRes.json();

        if (!geoData || geoData.length === 0) {
            alert('Ville introuvable (géocodage)');
            return;
        }

        cityCenter = [
            parseFloat(geoData[0].lat),
            parseFloat(geoData[0].lon)
        ];

        const res = await fetch(`/api/cities/${encodeURIComponent(city)}/pois`);
        if (!res.ok) throw new Error("Erreur API POI");

        const data = await res.json();

        if (data.error) {
            alert(data.error);
            return;
        }

        renderResults(data);

    } catch (e) {
        console.error(e);
        alert('Erreur lors de la recherche');
    }
}


// =====================
// AFFICHAGE POIS
// =====================
function renderResults(data) {

    const resultsBlock = document.getElementById('results');
    if (!resultsBlock) return;

    resultsBlock.style.display = 'block';
    document.getElementById('cityTitle').textContent = data.city;

    const activities = document.getElementById('activities');
    const accommodations = document.getElementById('accommodations');

    activities.innerHTML = '';
    accommodations.innerHTML = '';

    clearMarkers();

    const bounds = [];

    data.activities.slice(0, 10).forEach(poi => {
        activities.innerHTML += renderPoiCard(poi);
        addMarker(poi);
        if (poi.latitude !== null && poi.longitude !== null) {
            bounds.push([poi.latitude, poi.longitude]);
        }
    });

    data.accommodations.slice(0, 10).forEach(poi => {
        accommodations.innerHTML += renderPoiCard(poi);
        addMarker(poi);
        if (poi.latitude !== null && poi.longitude !== null) {
            bounds.push([poi.latitude, poi.longitude]);
        }
    });

    if (!map) return;

    if (bounds.length === 0 && cityCenter) {
        map.setView(cityCenter, 12);
    } else if (bounds.length === 1) {
        map.setView(bounds[0], 14);
    } else if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [50, 50] });
    }
}


// =====================
// SECURE STRING (ANTI XSS)
// =====================
function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}


// =====================
// POI CARD
// =====================
function renderPoiCard(poi) {

    const safeDescription = escapeHtml(
        poi.description ? poi.description.substring(0, 250) : 'Description non disponible'
    );

    const safeName = escapeHtml(poi.name);
    const safeAddress = escapeHtml(poi.address || 'Non disponible');
    const safeContact = escapeHtml(poi.contacts || 'Non disponible');
    const safeClassement = escapeHtml(poi.classements || 'Non classé');

    return `
    <li class="poi-card">

        ${poi.image ? `<img src="${poi.image}" alt="${safeName}" class="poi-image">` : ''}

        <h4>${safeName}</h4>

        <p><strong>Adresse :</strong> ${safeAddress}</p>

        <p><strong>Description :</strong> ${safeDescription}</p>

        <p><strong>Contact :</strong> ${safeContact}</p>

        <p><strong>Classements :</strong> ${safeClassement}</p>

        <p>
            ⭐ <strong>${poi.average_rating ?? '—'}</strong>
            (${poi.review_count} avis)
        </p>

        <div class="poi-actions">
            <button onclick="showReviewForm(${poi.id})">Ajouter un avis</button>
            <button onclick="loadReviews(${poi.id})">Voir avis</button>
        </div>

    </li>`;
}


// =====================
// MAP MARKERS
// =====================
function addMarker(poi) {

    if (!map || poi.latitude === null || poi.longitude === null) return;

    const popupContent = `
        <div style="max-width:220px">
            ${poi.image ? `<img src="${poi.image}" style="width:100%;border-radius:6px;margin-bottom:6px;">` : ''}
            <strong>${escapeHtml(poi.name)}</strong><br>
            ${escapeHtml(poi.address || '')}<br>
            ⭐ ${poi.average_rating ?? '—'} (${poi.review_count} avis)
        </div>
    `;

    const marker = L.marker([poi.latitude, poi.longitude])
        .addTo(map)
        .bindPopup(popupContent);

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
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ poi_id: currentPoiId, rating, comment })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            hideReviewForm();
            loadReviews(currentPoiId);
            searchCity();
        } else {
            alert(data.error || 'Erreur lors de l’envoi');
        }
    });
}


// =====================
// LOAD REVIEWS
// =====================
function loadReviews(poiId) {

    currentPoiId = poiId;

    fetch(`/api/review/all?poi_id=${poiId}`)
        .then(res => res.json())
        .then(data => {

            const container = document.getElementById('reviewsContainer');
            if (!container) return;

            container.innerHTML = '';

            data.forEach(r => {

                const safeComment = escapeHtml(r.comment);

                const li = document.createElement('li');

                const ownerText = document.createElement('strong');
                ownerText.textContent = r.is_owner ? 'Vous:' : 'Utilisateur:';
                li.appendChild(ownerText);

                li.appendChild(document.createTextNode(` ${safeComment} (${r.rating}/5) `));

                if (r.is_owner) {

                    const editBtn = document.createElement('button');
                    editBtn.textContent = 'Modifier';
                    editBtn.addEventListener('click', () => editReview(r.id, r.rating, r.comment));
                    li.appendChild(editBtn);

                    const deleteBtn = document.createElement('button');
                    deleteBtn.textContent = 'Supprimer';
                    deleteBtn.addEventListener('click', () => deleteReview(r.id, poiId));
                    li.appendChild(deleteBtn);
                }

                container.appendChild(li);
            });

            document.getElementById('reviewsList').style.display = 'block';
        });
}


// =====================
// EDIT / DELETE
// =====================
function editReview(id, rating, comment) {
    showReviewForm(currentPoiId, { id, rating, comment });
}

function deleteReview(id, poiId) {

    if (!confirm('Voulez-vous vraiment supprimer cet avis ?')) return;

    fetch(`/api/review/delete/${id}`, { method: 'DELETE' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadReviews(poiId);
            } else {
                alert(data.error || 'Erreur lors de la suppression');
            }
        });
}


// =====================
// COOKIE CONSENT
// =====================
function initCookieConsent() {

    const cookieBanner = document.getElementById('cookieBanner');
    const acceptBtn = document.getElementById('acceptCookies');
    const rejectBtn = document.getElementById('rejectCookies');

    if (!cookieBanner) return;

    if (!localStorage.getItem('cookieConsent')) {
        cookieBanner.style.display = 'flex';
    }

    acceptBtn?.addEventListener('click', () => {
        localStorage.setItem('cookieConsent', 'accepted');
        cookieBanner.style.display = 'none';
    });

    rejectBtn?.addEventListener('click', () => {
        localStorage.setItem('cookieConsent', 'rejected');
        cookieBanner.style.display = 'none';
    });
}
