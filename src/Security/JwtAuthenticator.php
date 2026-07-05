<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\UserRepository;
use App\Service\ApiResponseFactory;
use App\Service\JwtTokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly JwtTokenService $jwtTokenService,
        private readonly UserRepository $userRepository,
        private readonly ApiResponseFactory $responseFactory,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        $header = $request->headers->get('Authorization', '');

        return str_starts_with($header, 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $header = $request->headers->get('Authorization', '');
        $token = trim(substr($header, 7));
        $payload = $this->jwtTokenService->decodeToken($token);

        if (!$payload || !isset($payload['sub'])) {
            throw new CustomUserMessageAuthenticationException('Token invalide.');
        }

        return new SelfValidatingPassport(new UserBadge((string) $payload['sub'], function (string $email) {
            $user = $this->userRepository->findOneBy(['email' => $email]);
            if (!$user) {
                throw new CustomUserMessageAuthenticationException('Utilisateur introuvable.');
            }

            return $user;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return $this->responseFactory->error('Authentification impossible.', [
            'token' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, ?AuthenticationException $authException = null): JsonResponse
    {
        return $this->responseFactory->error('Token manquant ou invalide.', [], Response::HTTP_UNAUTHORIZED);
    }
}
