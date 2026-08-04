<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Command\VerifyEmail;

use App\IdentityAccess\Domain\Exception\UserNotFoundException;
use App\IdentityAccess\Domain\Repository\UserRepositoryInterface;
use App\IdentityAccess\Domain\Security\EmailVerificationTokenizerInterface;
use App\IdentityAccess\Domain\ValueObject\UserId;

final readonly class VerifyEmailHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private EmailVerificationTokenizerInterface $tokenizer,
    ) {}

    public function __invoke(VerifyEmailCommand $command): void
    {
        $userId = UserId::fromString($command->userId);
        $user = $this->users->ofId($userId);

        if (null === $user) {
            throw new UserNotFoundException('Compte introuvable.');
        }

        // Valide la signature et l'expiration AVANT de toucher au Domain.
        $this->tokenizer->verify($userId, $command->token);

        $user->verifyEmail(new \DateTimeImmutable());

        $this->users->save($user);
    }
}
