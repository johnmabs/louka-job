<?php

declare(strict_types=1);

namespace App\Company\Domain\Model;

use App\Company\Domain\Model\CompanyRole;
use App\Company\Domain\ValueObject\UserId;

/**
 * Entité enfant de l'agrégat Company — n'existe jamais indépendamment
 * d'une Company, pas de repository dédié.
 */
final class CompanyMember
{
    public function __construct(
        private readonly UserId $userId,
        private CompanyRole $role,
        private readonly \DateTimeImmutable $joinedAt,
    ) {}

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function role(): CompanyRole
    {
        return $this->role;
    }

    public function joinedAt(): \DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function changeRole(CompanyRole $role): void
    {
        $this->role = $role;
    }
}
