<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\CoursRepository;
use App\Repository\EtudiantRepository;
use App\Repository\InscriptionRepository;
use App\Service\ApiResponseFactory;
use App\Service\InscriptionService;
use App\Service\JsonRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InscriptionController extends AbstractController
{
    public function __construct(
        private readonly InscriptionRepository $inscriptions,
        private readonly EtudiantRepository $etudiants,
        private readonly CoursRepository $coursRepository,
        private readonly InscriptionService $service,
        private readonly ApiResponseFactory $response,
        private readonly JsonRequest $jsonRequest,
    ) {
    }

    #[Route('/inscriptions', name: 'api_inscriptions_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $items = array_map(
            static fn ($inscription) => $inscription->toArray(),
            $this->inscriptions->findBy([], ['id' => 'DESC'])
        );

        return $this->response->success('Liste des inscriptions.', $items);
    }

    #[Route('/inscriptions', name: 'api_inscriptions_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $inscription = $this->service->create($this->jsonRequest->getData($request));

            return $this->response->success('Inscription créée avec succès.', $inscription->toArray(), Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $exception) {
            return $this->response->error($exception->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/inscriptions/{id}', name: 'api_inscriptions_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $inscription = $this->inscriptions->find($id);
        if (!$inscription) {
            return $this->response->error('Inscription introuvable.', [], Response::HTTP_NOT_FOUND);
        }

        $this->inscriptions->remove($inscription);

        return $this->response->success('Inscription supprimée avec succès.');
    }

    #[Route('/etudiants/{id}/cours', name: 'api_etudiant_cours', methods: ['GET'])]
    public function coursByEtudiant(int $id): JsonResponse
    {
        $etudiant = $this->etudiants->find($id);
        if (!$etudiant) {
            return $this->response->error('Étudiant introuvable.', [], Response::HTTP_NOT_FOUND);
        }

        $cours = [];
        foreach ($etudiant->getInscriptions() as $inscription) {
            $cours[] = $inscription->getCours()->toArray();
        }

        return $this->response->success('Cours de l’étudiant.', $cours);
    }

    #[Route('/cours/{id}/etudiants', name: 'api_cours_etudiants', methods: ['GET'])]
    public function etudiantsByCours(int $id): JsonResponse
    {
        $cours = $this->coursRepository->find($id);
        if (!$cours) {
            return $this->response->error('Cours introuvable.', [], Response::HTTP_NOT_FOUND);
        }

        $etudiants = [];
        foreach ($cours->getInscriptions() as $inscription) {
            $etudiants[] = $inscription->getEtudiant()->toArray();
        }

        return $this->response->success('Étudiants du cours.', $etudiants);
    }
}
