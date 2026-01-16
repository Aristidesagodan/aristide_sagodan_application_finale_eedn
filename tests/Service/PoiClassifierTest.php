<?php

namespace App\Tests\Service;

use App\Service\PoiClassifier;
use PHPUnit\Framework\TestCase;
use App\Entity\Poi;
use App\Entity\PoiCategory;

/**
 * Test unitaire du service PoiClassifier
 * Objectif : vérifier la classification activités / hébergements
 */
class PoiClassifierTest extends TestCase
{
    public function testClassifyPois(): void
    {
        $classifier = new PoiClassifier();

        // Faux POI activité
        $activityCat = new PoiCategory();
        $activityCat->setName('CulturalSite');

        $activityPoi = new Poi();
        $activityPoi->setName('Musée');
        $activityPoi->setCategory($activityCat);

        // Faux POI hébergement
        $accommodationCat = new PoiCategory();
        $accommodationCat->setName('Hotel');

        $accommodationPoi = new Poi();
        $accommodationPoi->setName('Hôtel Test');
        $accommodationPoi->setCategory($accommodationCat);

        $result = $classifier->classify([$activityPoi, $accommodationPoi]);

        // Assertions
        $this->assertCount(1, $result['activities']);
        $this->assertCount(1, $result['accommodations']);
    }
}
