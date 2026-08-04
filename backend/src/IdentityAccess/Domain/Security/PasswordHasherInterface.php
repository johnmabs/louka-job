<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Security;

/**
 * Port du Domain : hachage de mot de passe, sans référence à Symfony Security.
 */
interface PasswordHasherInterface
{
    public function hash(string $plainPassword): string;
    public function verify(string $plainPassword, string $hash): bool;
}
