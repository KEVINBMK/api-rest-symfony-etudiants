<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cours;
use App\Entity\Etudiant;
use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Inscription> */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    public function save(Inscription $inscription, bool $flush = true): void
    {
        $this->getEntityManager()->persist($inscription);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Inscription $inscription, bool $flush = true): void
    {
        $this->getEntityManager()->remove($inscription);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findExisting(Etudiant $etudiant, Cours $cours): ?Inscription
    {
        return $this->findOneBy(['etudiant' => $etudiant, 'cours' => $cours]);
    }
}
