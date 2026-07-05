<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Etudiant> */
class EtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }

    public function save(Etudiant $etudiant, bool $flush = true): void
    {
        $this->getEntityManager()->persist($etudiant);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Etudiant $etudiant, bool $flush = true): void
    {
        $this->getEntityManager()->remove($etudiant);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
