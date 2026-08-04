<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Command\RegisterUser;

/**
 * Intention métier : "quelqu'un veut créer un compte".
 * DTO simple, immuable, sans logique.
 */
final readonly class RegisterUserCommand
{
    public function __construct(
        public string $email,
        public string $plainPassword,
    ) {}
}
