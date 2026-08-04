<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Security;

use App\IdentityAccess\Domain\Model\User;
use App\IdentityAccess\Domain\Security\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;


/**
 * Adapter s'appuyant sur le composant Symfony PasswordHasher.
 *
 * On passe la classe User (chaîne) plutôt qu'une instance : User n'a pas
 * besoin d'implémenter PasswordAuthenticatedUserInterface, l'algorithme est
 * résolu uniquement à partir du nom de classe déclaré dans security.yaml.
 */
final class SymfonyPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private readonly PasswordHasherFactoryInterface $factory,
    ) {}

    public function hash(string $plainPassword): string
    {
        return $this->factory->getPasswordHasher(User::class)->hash($plainPassword);
    }
}
