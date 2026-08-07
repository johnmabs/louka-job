<?php

declare(strict_types=1);

namespace App\Company\Application\Command\CreateCompany;

use App\Company\Domain\Exception\SlugAlreadyUsedException;
use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Slug;
use Symfony\Component\Uid\Uuid as SymfonyUuid;

final readonly class CreateCompanyHandler
{
    public function __construct(
        private CompanyRepositoryInterface $companies,
    ) {}

    public function __invoke(CreateCompanyCommand $command): CompanyId
    {
        $slug = $this->generateUniqueSlug($command->name);

        $company = Company::register(
            name: $command->name,
            slug: $slug,
            siret: $command->siret,
            ownerUserId: UserId::fromString($command->ownerUserId),
        );

        $this->companies->save($company);

        return $company->id();
    }

    /**
     * Ajoute un court suffixe aléatoire en cas de collision plutôt que de
     * rejeter la création — l'utilisateur n'a pas à choisir un slug lui-même.
     */
    private function generateUniqueSlug(string $name): Slug
    {
        $slug = Slug::generate($name);

        if (!$this->companies->existsWithSlug($slug)) {
            return $slug;
        }

        for ($attempt = 0; $attempt < 5; ++$attempt) {
            $candidate = Slug::generate($name, substr(SymfonyUuid::v4()->toRfc4122(), 0, 6));

            if (!$this->companies->existsWithSlug($candidate)) {
                return $candidate;
            }
        }

        throw new SlugAlreadyUsedException('Impossible de générer un slug unique pour cette entreprise.');
    }
}
