<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Command\VerifyEmail;

final readonly class VerifyEmailCommand
{
    public function __construct(
        public string $userId,
        public string $token,
    ) {}
}
