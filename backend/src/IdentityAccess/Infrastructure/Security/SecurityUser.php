<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Security;

use App\IdentityAccess\Domain\Model\User;
use App\IdentityAccess\Domain\ValueObject\UserId;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Adapter qui satisfait UserInterface de Symfony Security sans que le
 * Domain User n'ait à en dépendre. Uniquement utilisé pour construire le
 * JWT (Lexik) — jamais exposé en dehors de l'Infrastructure.
 */
final class SecurityUser implements UserInterface
{
    public function __construct(
        private readonly User $user,
    ) {}

    public function id(): UserId
    {
        return $this->user->id();
    }

    public function getRoles(): array
    {
        return $this->user->roles();
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->user->email()->value();
    }
}
