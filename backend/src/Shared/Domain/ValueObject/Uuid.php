<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidUuidException;
use Symfony\Component\Uid\Uuid as SymfonyUuid;

/**
 * Value Object générique encapsulant Symfony\Component\Uid.
 *
 * Fait partie du Shared Kernel : c'est une mécanique technique commune
 * (génération UUID v7, validation, égalité), pas un concept métier propre
 * à un Bounded Context. Les identités fortes de chaque contexte (UserId,
 * CompanyId, JobPostingId...) composent ce type en interne au lieu de
 * dupliquer cette logique.
 */
final readonly class Uuid
{
    private SymfonyUuid $value;

    private function __construct(SymfonyUuid $value)
    {
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(SymfonyUuid::v7());
    }

    public static function fromString(string $value): self
    {
        if (!SymfonyUuid::isValid($value)) {
            throw new InvalidUuidException(sprintf('"%s" n\'est pas un UUID valide.', $value));
        }

        return new self(SymfonyUuid::fromString($value));
    }

    public function toString(): string
    {
        return $this->value->toRfc4122();
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
