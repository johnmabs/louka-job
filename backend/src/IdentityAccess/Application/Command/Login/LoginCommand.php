<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Command\Login;

final readonly class LoginCommand
{
    public function __construct(
        public string $email,
        public string $plainPassword,
        public string $deviceFingerprint,
    ) {}
}
