<?php

namespace App\Repository;

use App\Entity\Bed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bed>
 */
class BedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bed::class);
    }

    public function findOccupancyStats(): array
    {
        return $this->createQueryBuilder('b')
            ->select('w.name as wardName, count(b.id) as totalBeds, SUM(CASE WHEN b.status = \'occupied\' THEN 1 ELSE 0 END) as occupiedBeds')
            ->join('b.ward', 'w')
            ->groupBy('w.id')
            ->getQuery()
            ->getResult();
    }
}
