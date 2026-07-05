<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Etudiant;
use App\Repository\EtudiantRepository;

class EtudiantService
{
    public function __construct(private readonly EtudiantRepository $etudiantRepository)
    {
    }

    public function create(array $data): Etudiant
    {
        $this->validateRequired($data, ['matricule', 'nom', 'prenom']);

        if ($this->etudiantRepository->findOneBy(['matricule' => strtoupper(trim((string) $data['matricule']))])) {
            throw new \InvalidArgumentException('Matricule déjà utilisé.');
        }

        $etudiant = (new Etudiant())
            ->setMatricule((string) $data['matricule'])
            ->setNom((string) $data['nom'])
            ->setPostnom($data['postnom'] ?? null)
            ->setPrenom((string) $data['prenom'])
            ->setEmail($data['email'] ?? null)
            ->setTelephone($data['telephone'] ?? null);

        $this->etudiantRepository->save($etudiant);

        return $etudiant;
    }

    public function update(Etudiant $etudiant, array $data): Etudiant
    {
        if (isset($data['matricule'])) {
            $matricule = strtoupper(trim((string) $data['matricule']));
            $existing = $this->etudiantRepository->findOneBy(['matricule' => $matricule]);
            if ($existing && $existing->getId() !== $etudiant->getId()) {
                throw new \InvalidArgumentException('Matricule déjà utilisé.');
            }
            $etudiant->setMatricule($matricule);
        }

        if (isset($data['nom'])) {
            $etudiant->setNom((string) $data['nom']);
        }
        if (array_key_exists('postnom', $data)) {
            $etudiant->setPostnom($data['postnom']);
        }
        if (isset($data['prenom'])) {
            $etudiant->setPrenom((string) $data['prenom']);
        }
        if (array_key_exists('email', $data)) {
            $etudiant->setEmail($data['email']);
        }
        if (array_key_exists('telephone', $data)) {
            $etudiant->setTelephone($data['telephone']);
        }

        $etudiant->touch();
        $this->etudiantRepository->save($etudiant);

        return $etudiant;
    }

    private function validateRequired(array $data, array $fields): void
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                throw new \InvalidArgumentException(sprintf('Le champ %s est obligatoire.', $field));
            }
        }
    }
}
