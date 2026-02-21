# aristide_sagodan_travel_guide_dynamique_eedn

🌍 Travel Guide – Symfony 8

Plateforme de guide touristique permettant de rechercher des villes, afficher des Points of Interest (POI) (activités, hébergements…), consulter des avis stockés en MongoDB, et accéder à une API REST sécurisée.

🧱 Stack technique
Backend

Symfony 8

PHP 8.2

Doctrine ORM (MySQL)

Doctrine ODM (MongoDB – avis)

Doctrine Fixtures

Security (IsGranted, rôles)

Frontend

Twig

JavaScript (ES6)

CSS custom (Light / Dark)

Tests Jest

Base de données

MySQL : données métier

MongoDB : avis POI

Environnement

Docker

Apache

PHP

MySQL

MongoDB

📁 Structure du projet
project/
├── assets/
│   ├── js/
│   │   ├── app.js
│   │   └── __tests__/
│   │       └── themeToggle.test.js
│   └── styles/
├── src/
│   ├── Controller/
│   ├── Entity/
│   ├── Document/        # MongoDB
│   ├── DataFixtures/
│   └── Security/
├── templates/
├── public/
├── docker/
├── docker-compose.yml
├── jest.config.js
└── README.md

🐳 Installation avec Docker
docker compose up -d --build

📦 Doctrine Fixtures (SQL)

Fixtures non destructives (--append) :

docker exec -it symfony_app php bin/console doctrine:fixtures:load --append


📌 Données générées :

30 villes françaises

Catégories POI

10 activités + 10 hébergements par ville

🍃 MongoDB – Avis POI

Les avis sont stockés dans MongoDB via Doctrine ODM.

{
  "poiId": 12,
  "rating": 5,
  "comment": "Lieu magnifique",
  "author": "Alice",
  "createdAt": "2025-01-01"
}

🔐 Sécurité

Authentification formulaire

Autorisation par rôles (ROLE_USER, ROLE_ADMIN)

Sécurisation API via #[IsGranted()]

Pas besoin de firewall spécifique pour chaque endpoint interne

🧪 Tests Backend (PHPUnit)
Types

Unitaires

Fonctionnels (Login, API, MongoDB)

Sécurité API

Lancer les tests
docker exec -it symfony_app php bin/phpunit

Coverage
php bin/phpunit --coverage-html coverage/

🧪 Tests Frontend – Jest (JavaScript)
Installation Jest
npm install --save-dev jest @testing-library/dom

Configuration jest.config.js
module.exports = {
  testEnvironment: 'jsdom',
  roots: ['assets/js'],
};

Exemple : test du Light / Dark mode

📁 assets/js/__tests__/themeToggle.test.js

import '@testing-library/jest-dom';

document.body.innerHTML = `
  <button id="themeToggle"></button>
`;

require('../app');

test('toggle dark/light mode', () => {
  const button = document.getElementById('themeToggle');

  document.body.classList.add('light');
  button.click();

  expect(document.body.classList.contains('dark')).toBe(true);
});

Lancer les tests Jest
npm test

📊 Diagrammes fournis

MERISE : MCD / MLD / MPD

Diagramme de classes

Diagramme de séquence (recherche + POI + MongoDB)

Diagramme d’activités

Diagramme de cas d’utilisation

🚀 Améliorations futures
📸 Images des POI

Ajout d’une entité PoiImage

Upload via Symfony Form

Stockage :

local (/public/uploads)

ou cloud (S3 / Cloudinary)

Relation OneToMany (1 POI → plusieurs images)

Miniatures et galerie

Autres pistes

Authentification JWT (API publique)

Pagination API

Recherche avancée (ElasticSearch)

Moyenne des notes MongoDB

Cache HTTP / Redis

✅ Bonnes pratiques

SQL + NoSQL combinés intelligemment

Fixtures sécurisées (non destructives)

Tests backend & frontend

Docker full stack

Sécurité par rôles

👨‍💻 Auteur

Projet réalisé par Aristide SAGODAN
Symfony 8 – Architecture moderne & scalable
