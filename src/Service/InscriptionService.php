<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Inscription;
use App\Repository\CoursRepository;
use App\Repository\EtudiantRepository;
use App\Repository\InscriptionRepository;

class InscriptionService
{
    public function __construct(
        private readonly InscriptionRepository $inscriptionRepository,
        private readonly EtudiantRepository $etudiantRepository,
        private readonly CoursRepository $coursRepository,
    ) {
    }

    public function create(array $data): Inscription
    {
        if (!isset($data['etudiant_id'], $data['cours_id'])) {
            throw new \InvalidArgumentException('etudiant_id et cours_id sont obligatoires.');
        }

        $etudiant = $this->etudiantRepository->find((int) $data['etudiant_id']);
        if (!$etudiant) {
            throw new \InvalidArgumentException('Etudiant introuvable.');
        }

        $cours = $this->coursRepository->find((int) $data['cours_id']);
        if (!$cours) {
            throw new \InvalidArgumentException('Cours introuvable.');
        }

        if ($this->inscriptionRepository->findExisting($etudiant, $cours)) {
            throw new \InvalidArgumentException('Double inscription non autorisee.');
        }

        $inscription = (new Inscription())
            ->setEtudiant($etudiant)
            ->setCours($cours);

        $this->inscriptionRepository->save($inscription);

        return $inscription;
    }
}
