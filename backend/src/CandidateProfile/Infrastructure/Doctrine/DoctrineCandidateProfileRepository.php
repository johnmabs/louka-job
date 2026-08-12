<?php

declare(strict_types=1);

namespace App\CandidateProfile\Infrastructure\Doctrine;

use App\CandidateProfile\Domain\Model\CandidateProfile;
use App\CandidateProfile\Domain\Repository\CandidateProfileRepositoryInterface;
use App\CandidateProfile\Domain\ValueObject\CandidateProfileId;
use App\CandidateProfile\Domain\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DoctrineCandidateProfileRepository implements CandidateProfileRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(CandidateProfile::class);
    }

    public function save(CandidateProfile $profile): void
    {
        $this->entityManager->persist($profile);
        $this->entityManager->flush();
    }

    public function ofId(CandidateProfileId $id): ?CandidateProfile
    {
        return $this->repository->find($id);
    }

    public function ofUserId(UserId $userId): ?CandidateProfile
    {
        return $this->repository->findOneBy(['userId' => $userId]);
    }
}
