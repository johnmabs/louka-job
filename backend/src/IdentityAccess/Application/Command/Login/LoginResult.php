<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Command\Login;

final readonly class LoginResult
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
        public int $expiresIn,
    ) {}
}
