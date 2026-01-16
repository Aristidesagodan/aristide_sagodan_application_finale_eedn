<?php

namespace App\DataFixtures;

use App\Entity\PoiCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PoiCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Hotel', 'Camping', 'RentalAccommodation', 'SelfCateringAccommodation', 'FoodEstablishment',
            'CulturalSite', 'Conference', 'CulturalEvent', 'MovieTheater', 'EntertainmentAndEvent'
        ];

        foreach ($categories as $index => $name) {
            $category = $manager->getRepository(PoiCategory::class)->findOneBy(['name' => $name]);
            if (!$category) {
                $category = new PoiCategory();
                $category->setName($name);
                $manager->persist($category);
            }
            $this->addReference('category_' . ($index + 1), $category);
        }

        $manager->flush();
    }
}
