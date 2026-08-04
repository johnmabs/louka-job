<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Shared\Domain\ValueObject\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * Pont entre le Value Object partagé Email et une colonne varchar.
 */
final class EmailType extends StringType
{
    public const string NAME = 'email';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Email
    {
        if ($value === null || $value instanceof Email) {
            return $value;
        }

        return new Email($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof Email ? $value->value() : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
