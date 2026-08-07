<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Http\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Company\Application\Command\CreateCompany\CreateCompanyCommand;
use App\Company\Application\Command\CreateCompany\CreateCompanyHandler;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Infrastructure\Http\ApiResource\CompanyResource;
use App\IdentityAccess\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @implements ProcessorInterface<CompanyResource, CompanyResource>
 */
final class CreateCompanyProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CreateCompanyHandler $handler,
        private readonly CompanyRepositoryInterface $companies,
        private readonly Security $security,
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): CompanyResource
    {
        /** @var CompanyResource $data */
        $user = $this->security->getUser();

        if (!$user instanceof SecurityUser) {
            // Ne devrait jamais arriver : la route est protégée par le firewall.
            throw new AccessDeniedException('Authentification requise.');
        }

        $companyId = ($this->handler)(new CreateCompanyCommand(
            name: $data->name,
            siret: $data->siret,
            ownerUserId: $user->id()->toString(),
        ));

        $company = $this->companies->ofId($companyId);

        $resource = new CompanyResource();
        $resource->id = $company->id()->toString();
        $resource->name = $company->name();
        $resource->slug = $company->slug()->value();
        $resource->siret = $company->siret();
        $resource->verificationStatus = $company->verificationStatus()->value;

        return $resource;
    }
}
