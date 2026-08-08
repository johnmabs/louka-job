<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Command\RegisterRecruiter;

final readonly class RegisterRecruiterCommand
{
    public function __construct(
        public string $email,
        public string $plainPassword,
    ) {}
}
