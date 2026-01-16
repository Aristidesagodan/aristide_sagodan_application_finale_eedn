<?php

namespace App\Repository;

use App\Entity\Poi;
use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les Points of Interest (POI)
 */
class PoiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poi::class);
    }

    /**
     * Récupère tous les POI d'une ville donnée
     *
     * @param City $city
     * @return Poi[]
     */
    public function findByCity(City $city): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.city = :city')
            ->setParameter('city', $city)
            ->orderBy('p.id', 'DESC') // derniers ajoutés en premier
            ->getQuery()
            ->getResult();
    }
}
