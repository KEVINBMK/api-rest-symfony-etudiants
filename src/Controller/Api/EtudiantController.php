<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\EtudiantRepository;
use App\Service\ApiResponseFactory;
use App\Service\EtudiantService;
use App\Service\JsonRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/etudiants')]
class EtudiantController extends AbstractController
{
    public function __construct(
        private readonly EtudiantRepository $etudiants,
        private readonly EtudiantService $service,
        private readonly ApiResponseFactory $response,
        private readonly JsonRequest $jsonRequest,
    ) {
    }

    #[Route('', name: 'api_etudiants_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $items = array_map(
            static fn ($etudiant) => $etudiant->toArray(),
            $this->etudiants->findBy([], ['id' => 'DESC'])
        );

        return $this->response->success('Liste des étudiants.', $items);
    }

    #[Route('', name: 'api_etudiants_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $etudiant = $this->service->create($this->jsonRequest->getData($request));

            return $this->response->success('Étudiant créé avec succès.', $etudiant->toArray(), Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $exception) {
            return $this->response->error($exception->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'api_etudiants_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $etudiant = $this->etudiants->find($id);
        if (!$etudiant) {
            return $this->response->error('Étudiant introuvable.', [], Response::HTTP_NOT_FOUND);
        }

        return $this->response->success('Détails de l’étudiant.', $etudiant->toArray());
    }

    #[Route('/{id}', name: 'api_etudiants_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $etudiant = $this->etudiants->find($id);
        if (!$etudiant) {
            return $this->response->error('Étudiant introuvable.', [], Response::HTTP_NOT_FOUND);
        }

        try {
            $etudiant = $this->service->update($etudiant, $this->jsonRequest->getData($request));

            return $this->response->success('Étudiant modifié avec succès.', $etudiant->toArray());
        } catch (\InvalidArgumentException $exception) {
            return $this->response->error($exception->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'api_etudiants_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $etudiant = $this->etudiants->find($id);
        if (!$etudiant) {
            return $this->response->error('Étudiant introuvable.', [], Response::HTTP_NOT_FOUND);
        }

        $this->etudiants->remove($etudiant);

        return $this->response->success('Étudiant supprimé avec succès.');
    }
}
