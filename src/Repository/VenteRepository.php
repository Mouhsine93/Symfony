<?php

namespace App\Repository;

use App\Entity\Vente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vente::class);
    }

    public function getTotalSales(): float
    {
        return (float) $this->createQueryBuilder('v')
            ->select('SUM(v.prix * v.quantite)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0.0;
    }

    public function getRecentSales(int $limit = 10): array
    {
        return $this->createQueryBuilder('v')
            ->select('v', 'c.nom as client', 'a.nom as article')
            ->leftJoin('App\Entity\Client', 'c', 'WITH', 'v.idClient = c.id')
            ->leftJoin('App\Entity\Article', 'a', 'WITH', 'v.idArticle = a.id')
            ->orderBy('v.dateVente', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
