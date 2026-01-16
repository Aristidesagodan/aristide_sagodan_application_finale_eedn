<?php

namespace App\Controller\Api;

use App\Repository\CityRepository;
use App\Repository\PoiRepository;
use App\Service\PoiClassifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

#[Route('/api/cities', name: 'api_cities_')]
#[IsGranted('ROLE_USER')] // 🔐 SÉCURITÉ API ICI
class CityPoiController extends AbstractController
{
    public function __construct(
        private PoiRepository $poiRepo,
        private CityRepository $cityRepo,
        private PoiClassifier $classifier
    ) {}

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

            return [
                'city' => $city->getName(),
                'activities' => array_map([$this, 'poiToArray'], $classified['activities']),
                'accommodations' => array_map([$this, 'poiToArray'], $classified['accommodations']),
            ];
        });

        return $this->json($data);
    }

    private function poiToArray($poi): array
    {
        return [
            'id' => $poi->getId(),
            'name' => $poi->getName(),
            'latitude' => $poi->getLatitude(),
            'longitude' => $poi->getLongitude(),
            'category' => $poi->getCategory()?->getName(),
        ];
    }
}
