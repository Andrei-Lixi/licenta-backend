<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtService
{
    public function __construct(private string $privateKey, private string $publicKey)
    {
    }

    public function generateJwtForUser(UserInterface $user): string
    {

        $privateKey = str_replace('/r/n', PHP_EOL, $this->privateKey);

        $payload = [
            'id' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'created_at' => time(),
            'expires_in' => 1800
        ];

        return JWT::encode($payload, $privateKey, 'RS256');
    }

    public function isExpired(string $jwt): bool
    {
        $decoded = (array) JWT::decode($jwt, new Key($this->publicKey, 'RS256'));

        return time() > $decoded['created_at'] + $decoded['expires_in'] ;
    }
}