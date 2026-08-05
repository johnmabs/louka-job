<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Command\RefreshToken;

final readonly class RefreshTokenCommand
{
    public function __construct(
        public string $refreshToken,
        public string $deviceFingerprint,
    ) {}
}
