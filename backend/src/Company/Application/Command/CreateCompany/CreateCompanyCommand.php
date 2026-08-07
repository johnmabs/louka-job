<?php

declare(strict_types=1);

namespace App\Company\Application\Command\CreateCompany;

final readonly class CreateCompanyCommand
{
    public function __construct(
        public string $name,
        public ?string $siret,
        public string $ownerUserId,
    ) {}
}
