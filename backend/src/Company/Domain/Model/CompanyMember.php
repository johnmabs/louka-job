<?php

declare(strict_types=1);

namespace App\Company\Domain\Model;

use App\Company\Domain\Model\Company;
use App\Company\Domain\Model\CompanyRole;
use App\Company\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Uuid;

/**
 * Entité enfant de l'agrégat Company — n'existe jamais indépendamment
 * d'une Company, pas de repository dédié.
 */
final class CompanyMember
{
    private Uuid $id;

    /**
     * Référence inverse vers Company, utilisée uniquement par Doctrine pour
     * résoudre la clé étrangère company_id. Jamais exposée publiquement ;
     * ne fait pas partie de l'API métier de CompanyMember.
     */
    private ?Company $company = null;

    public function __construct(
        private readonly UserId $userId,
        private CompanyRole $role,
        private readonly \DateTimeImmutable $joinedAt,
    ) {
        $this->id = Uuid::generate();
    }

    /**
     * @internal Appelé uniquement par Company juste après construction —
     * ne pas utiliser ailleurs.
     */
    public function assignCompany(Company $company): void
    {
        $this->company = $company;
    }

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
