<?php

namespace App\Controller\Api;

use App\Repository\CityRepository;
use App\Repository\PoiRepository;
use App\Service\PoiClassifier;
use App\Service\UnsplashService;
use App\Document\Review;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api/cities', name: 'api_cities_')]
#[IsGranted('ROLE_USER')]
class CityPoiController extends AbstractController
{
    public function __construct(
        private PoiRepository $poiRepo,
        private CityRepository $cityRepo,
        private PoiClassifier $classifier,
        private DocumentManager $dm,
        private UnsplashService $unsplash,
        private HttpClientInterface $httpClient
    ) {}

    // =====================================================
    // 🔎 ROUTE PROXY GEOCODAGE (ANTI CORS)
    // =====================================================
    #[Route('/geocode', name: 'geocode', methods: ['GET'])]
    public function geocode(Request $request): JsonResponse
    {
        $city = $request->query->get('city');

        if (!$city) {
            return $this->json(['error' => 'Ville manquante'], 400);
        }

        try {
            $response = $this->httpClient->request('GET',
                'https://nominatim.openstreetmap.org/search', [
                    'query' => [
                        'format' => 'json',
                        'q' => $city . ', France'
                    ],
                    'headers' => [
                        'User-Agent' => 'TravelGuideApp'
                    ]
                ]
            );

            return $this->json($response->toArray());

        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Erreur lors du géocodage'
            ], 500);
        }
    }

    // =====================================================
    // 🏙 POI PAR VILLE
    // =====================================================
    #[Route('/{name}/pois', name: 'pois', methods: ['GET'])]
    public function poisByCity(string $name): JsonResponse
    {
        $cache = new FilesystemAdapter('', 3600);
        $cacheKey = 'city_' . md5($name) . '_pois';

        $data = $cache->get($cacheKey, function () use ($name) {

            $city = $this->cityRepo->findOneBy(['name' => $name]);

            if (!$city) {
                return ['error' => 'Ville introuvable'];
            }

            $allPois = $this->poiRepo->findByCity($city);
            $classified = $this->classifier->classify($allPois);

            $currentUserId = $this->getUser()?->getId();

            $activities = array_map(
                fn($poi) => $this->poiToArray($poi, $currentUserId),
                $classified['activities']
            );

            $accommodations = array_map(
                fn($poi) => $this->poiToArray($poi, $currentUserId),
                $classified['accommodations']
            );

            $activities = $this->addImagesToPois($activities);
            $accommodations = $this->addImagesToPois($accommodations);

            return [
                'city' => $city->getName(),
                'currentUserId' => $currentUserId,
                'activities' => $this->addReviewsToPois($activities),
                'accommodations' => $this->addReviewsToPois($accommodations),
            ];
        });

        return $this->json($data);
    }

    // =====================================================
    // 🔁 TRANSFORMATION POI
    // =====================================================
    private function poiToArray(object $poi, ?int $currentUserId): array
    {
        return [
            'id' => $poi->getId(),
            'externalId' => $poi->getExternalId(),
            'name' => $poi->getName(),

            // Cast float pour Leaflet
            'latitude' => $poi->getLatitude() !== null ? (float) $poi->getLatitude() : null,
            'longitude' => $poi->getLongitude() !== null ? (float) $poi->getLongitude() : null,

            'address' => $poi->getAddress(),
            'description' => $poi->getDescription(),
            'contacts' => $poi->getContacts(),
            'classements' => $poi->getClassements(),
            'category' => $poi->getCategory()?->getName(),
            'currentUserId' => $currentUserId,
        ];
    }

    // =====================================================
    // ⭐ AJOUT REVIEWS (MongoDB)
    // =====================================================
    private function addReviewsToPois(array $pois): array
    {
        foreach ($pois as &$poi) {

            $reviews = $this->dm->getRepository(Review::class)
                ->findBy(['poi_id' => (int) $poi['id']], ['created_at' => 'DESC']);

            $poi['review_count'] = count($reviews);

            $poi['average_rating'] = $reviews
                ? round(array_sum(array_map(
                    fn($r) => $r->getRating(),
                    $reviews
                )) / count($reviews), 2)
                : null;

            $poi['recent_reviews'] = array_slice(
                array_map(fn($r) => [
                    'user_id' => $r->getUserId(),
                    'rating' => $r->getRating(),
                    'comment' => $r->getComment(),
                    'created_at' => $r->getCreatedAt()->format('Y-m-d H:i')
                ], $reviews),
                0,
                3
            );
        }

        return $pois;
    }

    // =====================================================
    // 🖼 AJOUT IMAGES UNSPLASH
    // =====================================================
    private function addImagesToPois(array $pois): array
    {
        foreach ($pois as &$poi) {
            $poi['image'] = $this->unsplash->getImage($poi['name']) ?? null;
        }

        return $pois;
    }
}
