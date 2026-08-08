<?php

declare(strict_types=1);

namespace App\Company\Application\Command\CreateRecruiter;

use App\Company\Domain\Exception\CompanyNotFoundException;
use App\Company\Domain\Model\CompanyRole;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\UserId as CompanyUserId;
use App\IdentityAccess\Application\Command\RegisterRecruiter\RegisterRecruiterCommand;
use App\IdentityAccess\Application\Command\RegisterRecruiter\RegisterRecruiterHandler;

/**
 * Orchestrateur cross-module : IdentityAccess (création du compte) puis
 * Company (ajout comme membre). Chaque module garde son Domain isolé — seule
 * l'Application layer traverse la frontière, ce qui est le point de couture
 * légitime entre deux Bounded Contexts d'un même monolithe modulaire.
 */
final readonly class CreateRecruiterHandler
{
    public function __construct(
        private CompanyRepositoryInterface $companies,
        private RegisterRecruiterHandler $registerRecruiter,
    ) {}

    public function __invoke(CreateRecruiterCommand $command): CompanyUserId
    {
        $company = $this->companies->ofId(CompanyId::fromString($command->companyId));

        if (null === $company) {
            throw new CompanyNotFoundException('Cette entreprise n\'existe pas.');
        }

        // Vérifié AVANT de créer le compte IdentityAccess : on ne veut pas
        // d'un compte "orphelin" si l'acteur n'a pas la permission.
        $company->ensureCanManageMembers(CompanyUserId::fromString($command->actorUserId));

        $newUserId = ($this->registerRecruiter)(new RegisterRecruiterCommand(
            email: $command->email,
            plainPassword: $command->plainPassword,
        ));

        $newCompanyUserId = CompanyUserId::fromString($newUserId->toString());

        $company->inviteMember(
            newMemberId: $newCompanyUserId,
            role: CompanyRole::from($command->role),
            invitedBy: CompanyUserId::fromString($command->actorUserId),
        );

        $this->companies->save($company);

        return $newCompanyUserId;
    }
}
