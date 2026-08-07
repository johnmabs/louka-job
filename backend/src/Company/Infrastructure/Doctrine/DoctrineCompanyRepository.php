<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Doctrine;

use App\Company\Domain\Model\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyId;
use App\Shared\Domain\ValueObject\Slug;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DoctrineCompanyRepository implements CompanyRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Company::class);
    }

    public function save(Company $company): void
    {
        $this->entityManager->persist($company);
        $this->entityManager->flush();
    }

    public function ofId(CompanyId $id): ?Company
    {
        return $this->repository->find($id);
    }

    public function ofSlug(Slug $slug): ?Company
    {
        return $this->repository->findOneBy(['slug' => $slug]);
    }

    public function existsWithSlug(Slug $slug): bool
    {
        return $this->ofSlug($slug) instanceof Company;
    }
}
