<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Command\RegisterRecruiter;

use App\IdentityAccess\Domain\Exception\EmailAlreadyUsedException;
use App\IdentityAccess\Domain\Model\User;
use App\IdentityAccess\Domain\Notification\EmailVerificationNotifierInterface;
use App\IdentityAccess\Domain\Repository\UserRepositoryInterface;
use App\IdentityAccess\Domain\Security\EmailVerificationTokenizerInterface;
use App\IdentityAccess\Domain\Security\PasswordHasherInterface;
use App\IdentityAccess\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Email;

/**
 * Distinct de RegisterUserHandler (inscription publique candidat) : un
 * recruteur n'est jamais auto-inscrit (cahier des charges §22, ligne 1441),
 * ce Handler n'est appelé que depuis le module Company.
 */
final readonly class RegisterRecruiterHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $passwordHasher,
        private EmailVerificationTokenizerInterface $tokenizer,
        private EmailVerificationNotifierInterface $notifier,
    ) {}

    public function __invoke(RegisterRecruiterCommand $command): UserId
    {
        $email = new Email($command->email);

        if ($this->users->existsWithEmail($email)) {
            throw new EmailAlreadyUsedException(sprintf('Un compte existe déjà pour "%s".', $email));
        }

        $user = User::registerAsRecruiter(
            email: $email,
            passwordHash: $this->passwordHasher->hash($command->plainPassword),
        );

        $this->users->save($user);

        $token = $this->tokenizer->generateFor($user->id());
        $this->notifier->notify($user->id(), $user->email(), $token);

        return $user->id();
    }
}
