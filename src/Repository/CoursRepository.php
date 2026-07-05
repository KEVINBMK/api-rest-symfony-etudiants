<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Cours> */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    public function save(Cours $cours, bool $flush = true): void
    {
        $this->getEntityManager()->persist($cours);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cours $cours, bool $flush = true): void
    {
        $this->getEntityManager()->remove($cours);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
