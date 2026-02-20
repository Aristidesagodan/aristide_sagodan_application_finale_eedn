<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<City>
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    /**
     * Recherche une ville par son nom, insensible à la casse et aux accents
     */
    public function findByNameInsensitive(string $name): ?City
    {
        // Normalisation : suppression des accents et conversion en minuscules
        $normalizedName = mb_strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $name));

        $qb = $this->createQueryBuilder('c');

        // On applique la même normalisation côté base via LOWER + REPLACE pour les accents
        // Ici, on remplace seulement les accents français les plus courants
        $qb->where(
            "LOWER(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(c.name, 'é', 'e'), 
                            'è', 'e'), 
                        'ê', 'e'), 
                    'à', 'a'), 
                'ô', 'o')
            ) = :name"
        )
        ->setParameter('name', $normalizedName);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
