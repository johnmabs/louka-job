<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidEmailException;

/**
 * Value Object immuable représentant un email valide.
 *
 * Ne dépend d'aucune infrastructure : la validation est une règle métier
 * (un User ne peut pas exister avec un email malformé), pas une contrainte
 * technique de framework.
 */
final readonly class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = mb_strtolower(trim($value));

        if (!filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException(sprintf('"%s" n\'est pas un email valide.', $value));
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
