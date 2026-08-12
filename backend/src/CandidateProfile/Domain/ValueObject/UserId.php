<?php

declare(strict_types=1);

namespace App\CandidateProfile\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Uuid;

/**
 * Référence locale à "un utilisateur" du point de vue du Bounded Context
 * CandidateProfile — même principe que Company\Domain\ValueObject\UserId :
 * pas de dépendance au type d'identité d'IdentityAccess.
 */
final readonly class UserId
{
    private Uuid $value;

    private function __construct(Uuid $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value));
    }

    public function toString(): string
    {
        return $this->value->toString();
    }

    public function equals(self $other): bool
    {
        return $this->value->equals($other->value);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
