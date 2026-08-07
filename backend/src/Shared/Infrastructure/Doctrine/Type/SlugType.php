<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Shared\Domain\ValueObject\Slug;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class SlugType extends StringType
{
    public const string NAME = 'slug';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Slug
    {
        if ($value === null || $value instanceof Slug) {
            return $value;
        }

        return Slug::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof Slug ? $value->value() : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
