<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Repository;

use App\IdentityAccess\Domain\Model\User;
use App\IdentityAccess\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Email;

/**
 * Port du Domain : contrat de persistance, sans aucune référence à Doctrine.
 */
interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function ofId(UserId $id): ?User;

    public function ofEmail(Email $email): ?User;

    public function existsWithEmail(Email $email): bool;
}
