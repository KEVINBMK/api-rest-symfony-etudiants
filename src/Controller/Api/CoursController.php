<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\CoursRepository;
use App\Service\ApiResponseFactory;
use App\Service\CoursService;
use App\Service\JsonRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cours')]
class CoursController extends AbstractController
{
    public function __construct(
        private readonly CoursRepository $coursRepository,
        private readonly CoursService $service,
        private readonly ApiResponseFactory $response,
        private readonly JsonRequest $jsonRequest,
    ) {
    }

    #[Route('', name: 'api_cours_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $items = array_map(
            static fn ($cours) => $cours->toArray(),
            $this->coursRepository->findBy([], ['id' => 'DESC'])
        );

        return $this->response->success('Liste des cours.', $items);
    }

    #[Route('', name: 'api_cours_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $cours = $this->service->create($this->jsonRequest->getData($request));

            return $this->response->success('Cours créé avec succès.', $cours->toArray(), Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $exception) {
            return $this->response->error($exception->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'api_cours_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $cours = $this->coursRepository->find($id);
        if (!$cours) {
            return $this->response->error('Cours introuvable.', [], Response::HTTP_NOT_FOUND);
        }

        return $this->response->success('Détails du cours.', $cours->toArray());
    }

    #[Route('/{id}', name: 'api_cours_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $cours = $this->coursRepository->find($id);
        if (!$cours) {
            return $this->response->error('Cours introuvable.', [], Response::HTTP_NOT_FOUND);
        }

        try {
            $cours = $this->service->update($cours, $this->jsonRequest->getData($request));

            return $this->response->success('Cours modifié avec succès.', $cours->toArray());
        } catch (\InvalidArgumentException $exception) {
            return $this->response->error($exception->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'api_cours_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $cours = $this->coursRepository->find($id);
        if (!$cours) {
            return $this->response->error('Cours introuvable.', [], Response::HTTP_NOT_FOUND);
        }

        $this->coursRepository->remove($cours);

        return $this->response->success('Cours supprimé avec succès.');
    }
}
