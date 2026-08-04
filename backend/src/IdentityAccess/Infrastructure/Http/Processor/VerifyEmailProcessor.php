<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\IdentityAccess\Application\Command\VerifyEmail\VerifyEmailCommand;
use App\IdentityAccess\Application\Command\VerifyEmail\VerifyEmailHandler;
use App\IdentityAccess\Domain\Repository\UserRepositoryInterface;
use App\IdentityAccess\Domain\ValueObject\UserId;
use App\IdentityAccess\Infrastructure\Http\ApiResource\UserResource;
use App\IdentityAccess\Infrastructure\Http\ApiResource\VerifyEmailInput;

/**
 * @implements ProcessorInterface<VerifyEmailInput, UserResource>
 */
final class VerifyEmailProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly VerifyEmailHandler $handler,
        private readonly UserRepositoryInterface $users,
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): UserResource
    {
        /** @var VerifyEmailInput $data */
        $userId = (string) $uriVariables['id'];

        ($this->handler)(new VerifyEmailCommand(
            userId: $userId,
            token: $data->token,
        ));

        $user = $this->users->ofId(UserId::fromString($userId));

        $resource = new UserResource();
        $resource->id = $user->id()->toString();
        $resource->email = $user->email()->value();
        $resource->status = $user->status()->value;

        return $resource;
    }
}
