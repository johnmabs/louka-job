<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\IdentityAccess\Application\Command\RegisterUser\RegisterUserCommand;
use App\IdentityAccess\Application\Command\RegisterUser\RegisterUserHandler;
use App\IdentityAccess\Domain\Repository\UserRepositoryInterface;
use App\IdentityAccess\Infrastructure\Http\ApiResource\UserResource;

/**
 * @implements ProcessorInterface<UserResource, UserResource>
 */
final class RegisterUserProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly RegisterUserHandler $handler,
        private readonly UserRepositoryInterface $users,
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): UserResource
    {
        /** @var UserResource $data */
        $userId = ($this->handler)(new RegisterUserCommand(
            email: $data->email,
            plainPassword: $data->plainPassword,
        ));

        $user = $this->users->ofId($userId);

        $resource = new UserResource();
        $resource->id = $user->id()->toString();
        $resource->email = $user->email()->value();
        $resource->status = $user->status()->value;

        return $resource;
    }
}
