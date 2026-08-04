<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Doctrine\Type;

use App\IdentityAccess\Domain\ValueObject\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

/**
 * Fait le pont entre le Value Object UserId (Domain) et la colonne native
 * "uuid" de PostgreSQL. C'est la seule classe de tout le module qui connaît
 * à la fois UserId et Doctrine.
 */
final class UserIdType extends GuidType
{
    public const string NAME = 'user_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserId
    {
        if ($value === null || $value instanceof UserId) {
            return $value;
        }

        return UserId::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof UserId ? $value->toString() : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
