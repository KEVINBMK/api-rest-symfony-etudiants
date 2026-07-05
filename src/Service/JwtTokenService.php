<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

class JwtTokenService
{
    public function createToken(User $user): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'sub' => $user->getEmail(),
            'user_id' => $user->getId(),
            'roles' => $user->getRoles(),
            'iat' => time(),
            'exp' => time() + 3600 * 8,
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR)),
            $this->base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR)),
        ];

        $signature = hash_hmac('sha256', implode('.', $segments), $this->getSecret(), true);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    public function decodeToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;
        $expected = $this->base64UrlEncode(hash_hmac('sha256', $header . '.' . $payload, $this->getSecret(), true));

        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $data = json_decode($this->base64UrlDecode($payload), true);
        if (!is_array($data)) {
            return null;
        }

        if (isset($data['exp']) && time() > (int) $data['exp']) {
            return null;
        }

        return $data;
    }

    private function getSecret(): string
    {
        return (string) ($_ENV['JWT_SECRET'] ?? $_SERVER['JWT_SECRET'] ?? 'change_this_secret');
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        return base64_decode(strtr($value, '-_', '+/')) ?: '';
    }
}
