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
    document.getElementById('searchBtn')?.addEventListener('click', searchCity);

    // =====================
    // FORMULAIRE AVIS
    // =====================
    const form = document.getElementById('reviewForm');
    form?.addEventListener('submit', submitReview);

    document.getElementById('cancelReview')?.addEventListener('click', () => {
        hideReviewForm();
    });
});

// =====================
// AFFICHAGE FORMULAIRE AVIS
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
// ENVOI FORMULAIRE AVIS
// =====================
function submitReview(e) {
    e.preventDefault();

    const rating = parseFloat(document.getElementById('reviewRating').value);
    const comment = document.getElementById('reviewComment').value;

    const url = editingReviewId ? `/api/review/${editingReviewId}/update` : '/api/review/add';
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
        } else {
            alert(data.error || 'Erreur lors de l’envoi de l’avis');
        }
    })
    .catch(err => alert('Erreur réseau ou serveur : ' + err));
}

// =====================
// CHARGEMENT DES AVIS
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
    if (!confirm('Supprimer cet avis ?')) return;

    fetch(`/api/review/${id}/delete`, { method: 'DELETE' })
        .then(res => res.json())
        .then(data => {
            if (data.success) loadReviews(poiId);
            else alert(data.error || 'Erreur lors de la suppression');
        });
}
