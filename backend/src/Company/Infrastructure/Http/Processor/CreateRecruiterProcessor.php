<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Http\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Company\Application\Command\CreateRecruiter\CreateRecruiterCommand;
use App\Company\Application\Command\CreateRecruiter\CreateRecruiterHandler;
use App\Company\Infrastructure\Http\ApiResource\CreateRecruiterInput;
use App\Company\Infrastructure\Http\ApiResource\RecruiterCreatedResource;
use App\IdentityAccess\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @implements ProcessorInterface<CreateRecruiterInput, RecruiterCreatedResource>
 */
final class CreateRecruiterProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CreateRecruiterHandler $handler,
        private readonly Security $security,
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): RecruiterCreatedResource
    {
        /** @var CreateRecruiterInput $data */
        $actor = $this->security->getUser();

        if (!$actor instanceof SecurityUser) {
            throw new AccessDeniedException('Authentification requise.');
        }

        $companyId = (string) $uriVariables['id'];

        $newUserId = ($this->handler)(new CreateRecruiterCommand(
            companyId: $companyId,
            email: $data->email,
            plainPassword: $data->plainPassword,
            role: $data->role,
            actorUserId: $actor->id()->toString(),
        ));

        $resource = new RecruiterCreatedResource();
        $resource->userId = $newUserId->toString();
        $resource->email = $data->email;
        $resource->role = $data->role;

        return $resource;
    }
}
