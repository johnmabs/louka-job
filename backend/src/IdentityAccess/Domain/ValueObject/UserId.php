<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Uuid;

/**
 * Value Object identité de l'agrégat User.
 *
 * Reste local au Bounded Context IdentityAccess (principe des Bounded
 * Contexts, cahier des charges §3.1.3) : les autres contextes ne doivent
 * pas dépendre de ce type directement. Seule la mécanique UUID sous-jacente
 * est mutualisée via App\Shared\Domain\ValueObject\Uuid.
 */
final readonly class UserId
{
    private Uuid $value;

    private function __construct(Uuid $value)
    {
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(Uuid::generate());
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
