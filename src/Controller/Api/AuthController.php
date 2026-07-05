<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ApiResponseFactory;
use App\Service\JsonRequest;
use App\Service\JwtTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseFactory $response,
        private readonly JsonRequest $jsonRequest,
    ) {
    }

    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserRepository $users, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = $this->jsonRequest->getData($request);

        foreach (['nom', 'email', 'password'] as $field) {
            if (empty($data[$field])) {
                return $this->response->error('Données invalides.', [$field => 'Champ obligatoire.']);
            }
        }

        $email = strtolower(trim((string) $data['email']));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->error('Email invalide.', ['email' => 'Format incorrect.']);
        }

        if ($users->findOneBy(['email' => $email])) {
            return $this->response->error('Email déjà utilisé.', ['email' => 'Cet email existe déjà.'], Response::HTTP_CONFLICT);
        }

        if (strlen((string) $data['password']) < 6) {
            return $this->response->error('Mot de passe trop court.', ['password' => 'Minimum 6 caractères.']);
        }

        $user = (new User())
            ->setNom((string) $data['nom'])
            ->setEmail($email)
            ->setRoles(['ROLE_USER']);

        $user->setPassword($hasher->hashPassword($user, (string) $data['password']));
        $users->save($user);

        return $this->response->success('Utilisateur créé avec succès.', [
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UserRepository $users, UserPasswordHasherInterface $hasher, JwtTokenService $jwt): JsonResponse
    {
        $data = $this->jsonRequest->getData($request);

        if (empty($data['email']) || empty($data['password'])) {
            return $this->response->error('Email et mot de passe obligatoires.');
        }

        $user = $users->findOneBy(['email' => strtolower(trim((string) $data['email']))]);
        if (!$user || !$hasher->isPasswordValid($user, (string) $data['password'])) {
            return $this->response->error('Identifiants incorrects.', [], Response::HTTP_UNAUTHORIZED);
        }

        return $this->response->success('Connexion réussie.', [
            'token' => $jwt->createToken($user),
            'type' => 'Bearer',
            'expires_in' => 28800,
        ]);
    }

    #[Route('/me', name: 'api_me', methods: ['GET'])]
    public function me(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->response->error('Utilisateur non connecté.', [], Response::HTTP_UNAUTHORIZED);
        }

        return $this->response->success('Profil utilisateur.', [
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }
}
