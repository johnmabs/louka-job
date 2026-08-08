<?php

declare(strict_types=1);

namespace App\Company\Application\Command\CreateRecruiter;

final readonly class CreateRecruiterCommand
{
    public function __construct(
        public string $companyId,
        public string $email,
        public string $plainPassword,
        public string $role,
        public string $actorUserId,
    ) {}
}
