<?php

namespace App\Service;

use App\Entity\Poi;

/**
 * Classe les POI Datatourisme en :
 * - Hébergements (lieux où l’on dort)
 * - Activités (tout le reste)
 */
class PoiClassifier
{
    /**
     * LISTE STRICTE des catégories HÉBERGEMENT
     * (basée sur TA base réelle)
     */
    private array $accommodationKeywords = [
        'Accommodation',
        'SelfCateringAccommodation',
        'RentalAccommodation',
        'AccommodationProduct',
        'Hotel',
        'Camping',
        'LodgingBusiness',
        'Apartment'
    ];

    /**
     * Classement principal
     */
    public function classify(array $pois): array
    {
        $activities = [];
        $accommodations = [];

        foreach ($pois as $poi) {
            if ($this->isAccommodation($poi)) {
                $accommodations[] = $poi;
            } else {
                $activities[] = $poi;
            }
        }

        return [
            'activities' => array_slice($activities, 0, 10),
            'accommodations' => array_slice($accommodations, 0, 10),
        ];
    }

    /**
     * Détermine si un POI est un hébergement
     */
    private function isAccommodation(Poi $poi): bool
    {
        $category = $poi->getCategory()->getName();

        foreach ($this->accommodationKeywords as $keyword) {
            if (stripos($category, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
}
