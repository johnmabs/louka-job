<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Command\RegisterUser;

use App\IdentityAccess\Domain\Exception\EmailAlreadyUsedException;
use App\IdentityAccess\Domain\Model\User;
use App\IdentityAccess\Domain\Repository\UserRepositoryInterface;
use App\IdentityAccess\Domain\Security\PasswordHasherInterface;
use App\IdentityAccess\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Email;

/**
 * Orchestration de l'inscription : ne dépend que d'interfaces (Ports),
 * jamais de Doctrine ou Symfony Security directement.
 */
final readonly class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $passwordHasher,
    ) {}

    public function __invoke(RegisterUserCommand $command): UserId
    {
        $email = new Email($command->email);

        if ($this->users->existsWithEmail($email)) {
            throw new EmailAlreadyUsedException(sprintf('Un compte existe déjà pour "%s".', $email));
        }

        $user = User::register(
            email: $email,
            passwordHash: $this->passwordHasher->hash($command->plainPassword),
        );

        $this->users->save($user);

        return $user->id();
    }
}
