<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Security;

use App\IdentityAccess\Domain\Repository\UserRepositoryInterface;
use App\IdentityAccess\Domain\ValueObject\UserId;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException as SecurityUserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Recharge un User (Domain) à partir de l'identifiant présent dans le JWT
 * (claim "sub", configuré comme identity field dans lexik_jwt_authentication.yaml).
 */
final class UserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->users->ofId(UserId::fromString($identifier));

        if (null === $user) {
            throw new SecurityUserNotFoundException(sprintf('Utilisateur "%s" introuvable.', $identifier));
        }

        return new SecurityUser($user);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof SecurityUser) {
            throw new UnsupportedUserException(sprintf('Instances de "%s" non supportées.', get_debug_type($user)));
        }

        return $this->loadUserByIdentifier($user->id()->toString());
    }

    public function supportsClass(string $class): bool
    {
        return SecurityUser::class === $class;
    }
}
