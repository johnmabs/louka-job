<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Doctrine;

use App\IdentityAccess\Domain\Model\User;
use App\IdentityAccess\Domain\Repository\UserRepositoryInterface;
use App\IdentityAccess\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Email;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Adapter Doctrine du Port UserRepositoryInterface.
 */
final class DoctrineUserRepository implements UserRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(User::class);
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function ofId(UserId $id): ?User
    {
        return $this->repository->find($id);
    }

    public function ofEmail(Email $email): ?User
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function existsWithEmail(Email $email): bool
    {
        return $this->ofEmail($email) instanceof User;
    }
}
