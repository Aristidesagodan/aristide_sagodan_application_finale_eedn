<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $cities = [
            ['name' => 'Paris', 'postal_code' => '75000'],
            ['name' => 'Lyon', 'postal_code' => '69000'],
            ['name' => 'Marseille', 'postal_code' => '13000'],
            ['name' => 'Toulouse', 'postal_code' => '31000'],
            ['name' => 'Nice', 'postal_code' => '06000'],
            ['name' => 'Nantes', 'postal_code' => '44000'],
            ['name' => 'Strasbourg', 'postal_code' => '67000'],
            ['name' => 'Montpellier', 'postal_code' => '34000'],
            ['name' => 'Bordeaux', 'postal_code' => '33000'],
            ['name' => 'Lille', 'postal_code' => '59000'],
            ['name' => 'Rennes', 'postal_code' => '35000'],
            ['name' => 'Dijon', 'postal_code' => '21000'],
            ['name' => 'Grenoble', 'postal_code' => '38000'],
            ['name' => 'Avignon', 'postal_code' => '84000'],
            ['name' => 'Annecy', 'postal_code' => '74000'],
        ];

        foreach ($cities as $index => $data) {
            // Vérifie si la ville existe déjà dans la base pour ne pas dupliquer
            $city = $manager->getRepository(City::class)->findOneBy(['name' => $data['name']]);
            
            if (!$city) {
                $city = new City();
                $city->setName($data['name']);
                $city->setPostalCode($data['postal_code']);
                $manager->persist($city);
            }

            // Ajoute la référence pour PoiFixtures
            $this->addReference('city_' . ($index + 1), $city);
        }

        $manager->flush();
    }
}
