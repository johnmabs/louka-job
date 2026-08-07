<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository;

use App\Company\Domain\Model\Company;
use App\Company\Domain\ValueObject\CompanyId;
use App\Shared\Domain\ValueObject\Slug;

interface CompanyRepositoryInterface
{
    public function save(Company $company): void;

    public function ofId(CompanyId $id): ?Company;

    public function ofSlug(Slug $slug): ?Company;

    public function existsWithSlug(Slug $slug): bool;
}
