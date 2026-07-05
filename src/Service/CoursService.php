<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Cours;
use App\Repository\CoursRepository;

class CoursService
{
    public function __construct(private readonly CoursRepository $coursRepository)
    {
    }

    public function create(array $data): Cours
    {
        $this->validateRequired($data, ['code', 'intitule', 'credits']);
        $code = strtoupper(trim((string) $data['code']));

        if ($this->coursRepository->findOneBy(['code' => $code])) {
            throw new \InvalidArgumentException('Code du cours déjà utilisé.');
        }

        $credits = (int) $data['credits'];
        if ($credits <= 0) {
            throw new \InvalidArgumentException('Le nombre de crédits doit être supérieur à 0.');
        }

        $cours = (new Cours())
            ->setCode($code)
            ->setIntitule((string) $data['intitule'])
            ->setDescription($data['description'] ?? null)
            ->setCredits($credits);

        $this->coursRepository->save($cours);

        return $cours;
    }

    public function update(Cours $cours, array $data): Cours
    {
        if (isset($data['code'])) {
            $code = strtoupper(trim((string) $data['code']));
            $existing = $this->coursRepository->findOneBy(['code' => $code]);
            if ($existing && $existing->getId() !== $cours->getId()) {
                throw new \InvalidArgumentException('Code du cours déjà utilisé.');
            }
            $cours->setCode($code);
        }

        if (isset($data['intitule'])) {
            $cours->setIntitule((string) $data['intitule']);
        }
        if (array_key_exists('description', $data)) {
            $cours->setDescription($data['description']);
        }
        if (isset($data['credits'])) {
            $credits = (int) $data['credits'];
            if ($credits <= 0) {
                throw new \InvalidArgumentException('Le nombre de crédits doit être supérieur à 0.');
            }
            $cours->setCredits($credits);
        }

        $cours->touch();
        $this->coursRepository->save($cours);

        return $cours;
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
