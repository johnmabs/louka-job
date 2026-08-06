<?php

declare(strict_types=1);

namespace App\Company\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Uuid;

final readonly class CompanyId
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
